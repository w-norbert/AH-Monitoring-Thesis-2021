package RelationalDB

import GraphDB.Model.ValidationRule
import RelationalDB.Model.SystemRelation
import Util.Date

import java.sql.{DriverManager, ResultSet, SQLException}
import java.util
import scala.collection.mutable

/**
 * Class for handling Relational Database connection
 */
class Connection {
    var connection: java.sql.Connection = null
    val driver = "com.mysql.jdbc.Driver"

    /**
     * Constructor for the RelationalDB.Connection class
     * @param uri String URI of the database
     * @param username String username of the database
     * @param password String password of the database
     */
    def this(uri: String, username: String, password:String) = {
        this()
        Class.forName(driver);
        this.connection = DriverManager.getConnection(uri, username, password)
    }

    /**
     * Runs a simple SQL query on the relational db
     * @param sql String Sql query to run
     * @return ResultSet the result of the query
     */
    def runQuery(sql: String): ResultSet = {
        val statement = this.connection.createStatement()
        statement.executeQuery(sql)
    }

    /**
     * Runs a query with prepared query
     * @param sql String the prepared query, placeholder character: ?
     * @param parameters ArrayList[String] Values for the placeholders in order
     * @return ResultSet the result
     */
    def runPreparedQuery(sql: String, parameters: util.ArrayList[String]): ResultSet = {
        val statement = this.connection.prepareStatement(sql)
        var i = 0
        for(i <- 1 to parameters.size()){
            statement.setString(i, parameters.get(i - 1))
        }
        statement.executeQuery()
    }

    /**
     * Executes a prepared query
     * @param sql String the prepared query, placeholder character: ?
     * @param parameters ArrayList[String] Values for the placeholders in order
     * @return ResultSet the result
     */
    def runPreparedStatement(sql: String, parameters: util.ArrayList[String]): Boolean = {
        val statement = this.connection.prepareStatement(sql)
        var i = 0
        for(i <- 1 to parameters.size()){
            val param = parameters.get(i - 1)
            statement.setString(i, param)
        }
        statement.execute()
    }

    /**
     * Queries the Arrowhead orchestration_store table
     * @return
     */
    def queryOrchestrationStore(): util.ArrayList[SystemRelation] = {
        val queryResult: ResultSet = this.runQuery(Query.OrchestrationStore.queryAll)
        resultSetToSystemRelation(queryResult)
    }

    /**
     * Queries the Arrowhead orchestration_connection table
     * @param connectionLimitSeconds Int Limit connection with now minus this number of seconds
     * @return ArrayList[SystemRelation] the result
     */
    def queryCurrentConnections(connectionLimitSeconds: Int = 30): util.ArrayList[SystemRelation] = {
        val parameterList = new util.ArrayList[String]
        parameterList.add(Date.getDateTimeNow(connectionLimitSeconds))
        val queryResult: ResultSet = this.runPreparedQuery(Query.CurrentConnection.queryAll, parameterList)
        resultSetToSystemRelation(queryResult)
    }

    /**
     * Queries the authorization rules for the local cloud
     * @return ArrayList[SystemRelation] the result
     */
    def queryAuthorizationRules(): util.ArrayList[SystemRelation] = {
        val queryResult: ResultSet = this.runQuery(Query.AuthorizationRules.queryAll)
        resultSetToSystemRelation(queryResult)
    }

    /**
     * Queries the database for validation rules
     * @return ArrayList[ValidationRule] the result
     */
    def queryValidationRules(): util.ArrayList[ValidationRule] = {
        val resultSet = runQuery(Query.Validation.getRules)
        val resultList = new util.ArrayList[ValidationRule]()
        while (resultSet.next()) {
            val rule: ValidationRule = ValidationRule(
                resultSet.getLong("id"),
                resultSet.getString("name"),
                resultSet.getString("rule"),
                resultSet.getBoolean("positive_evaluation"),
                resultSet.getBoolean("active"),
                resultSet.getString("created_at"),
                resultSet.getString("updated_at"))
            resultList.add(rule)
        }
        resultList
    }

    /**
     * Creates or updates a Graph View
     * @param jsonData String the json representation of the graph
     * @param id Long the ID of the view
     * @param name String the name of the view
     * @return
     */
    def saveGraphState(jsonData: String, id: Long, name: String): Boolean = {
        val date = Util.Date.getDateTimeNow()
        val parameters = new util.ArrayList[String]()
        parameters.add(0, id.toString)
        parameters.add(1, name)
        parameters.add(2, jsonData)
        parameters.add(3, date)
        parameters.add(4, jsonData)
        parameters.add(5, date)
        runPreparedStatement(Query.GraphState.save, parameters)
    }

    /**
     * This function takes a raw SQL ResultSet and returns an array of SystemRelations from it
     * @param resultSet ResultSet input
     * @return ArrayList[SystemRelation] result
     */
    private def resultSetToSystemRelation(resultSet: ResultSet):util.ArrayList[SystemRelation]  = {
        val result: util.ArrayList[SystemRelation] = new util.ArrayList[SystemRelation]
        while (resultSet.next()) {
            var store_priority: String = null

            try store_priority = resultSet.getString("store_priority")
            catch {
                case e: SQLException =>
                    store_priority = "1"
            }

            val sqlResult: SystemRelation = SystemRelation(resultSet.getString("consumer_system_name"),
                resultSet.getString("consumer_system_address"),
                resultSet.getString("consumer_system_port"),
                resultSet.getString("provider_system_name"),
                resultSet.getString("provider_system_address"),
                resultSet.getString("provider_system_port"),
                resultSet.getString("service_definition"),
                resultSet.getString("interface_name"),
                resultSet.getString("service_uri"),
                store_priority)
            result.add(sqlResult)
        }
        result
    }

    /**
     * Updates the status of an executed validation rule
     * @param executedRules HasMap[Long, Boolean] the key is the ID of the validation rule and the value
     *                      is true if the executed rule is ture false otherwise
     * @return Boolean true if the update is saved false otherwise
     */
    def saveExecutedRules(executedRules: mutable.HashMap[Long,Boolean]): Boolean = {
        val date = Util.Date.getDateTimeNow()
        var success = true
        for(result <- executedRules) {
            val statement = this.connection.prepareStatement(Query.Validation.updateResults)
            statement.setBoolean(1, result._2)
            statement.setString(2, date)
            statement.setLong(3, result._1)
            if(!statement.execute()) {
                success = false
            }
        }
        success
    }
}
