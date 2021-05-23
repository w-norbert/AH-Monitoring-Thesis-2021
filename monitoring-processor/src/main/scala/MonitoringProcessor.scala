import Util.Config

/**
 * Main controller of the MonitoringProcessor
 */
object MonitoringProcessor {
    def main(args: Array[String]): Unit = {
        // Reading config file
        val config = new Config("application.properties")
        // Creating Arrowhead database connection
        val arrowheadDbConnection = new RelationalDB.Connection(
            config.arrowheadDbConnectionString,
            config.arrowheadDbUsername,
            config.arrowheadDbPassword)
        // Creating monitoring database connection
        val monitoringDbConnection =new RelationalDB.Connection(
            config.monitoringDbConnectionString,
            config.monitoringDbUsername,
            config.monitoringDbPassword)
        // Creating graph database connection
        val graphDbConnection = new GraphDB.Connection(
            config.graphDbUri,
            config.graphDbUser,
            config.graphDbPassword)
        while(true) {
            // Dropping previous graph
            graphDbConnection.drop()
            // Querying orchestration store
            val storeEntries = arrowheadDbConnection.queryOrchestrationStore()
            // Saving orchestration store graph into graph db
            graphDbConnection.save(storeEntries, config.storeRelationshipName)

            // Querying current connections
            val currentConnection = arrowheadDbConnection.queryCurrentConnections(config.connectionLimitSeconds)
            // Saving current connections into graph db
            graphDbConnection.save(currentConnection, config.connectionRelationshipName)

            // Querying authorization rules
            val authEntries = arrowheadDbConnection.queryAuthorizationRules()
            // Saving authorization rules into graph db
            graphDbConnection.save(authEntries, config.authorizationRelationshipName)

            // Querying the resulting graph from graph db
            val graph: Arrowhead.Graph =  new Arrowhead.Graph(graphDbConnection.queryAll())
            // Parsing the graph to JSON
            val rawJsonGraph: String = graph.toJson()
            // Saving graph JSON as default view
            monitoringDbConnection.saveGraphState(rawJsonGraph, 1, "Default")

            // Querying graph with store relation from graph db
            val storeGraph: Arrowhead.Graph = new Arrowhead.Graph(graphDbConnection
                .queryWithRelation(config.storeRelationshipName))
            val rawJsonStoreGraph: String = storeGraph.toJson()
            // Saving graph JSON as store view
            monitoringDbConnection.saveGraphState(rawJsonStoreGraph, 2,  "Orchestration Store")

            // Querying graph with authorized relation from graph db
            val authGraph: Arrowhead.Graph =  new Arrowhead.Graph(graphDbConnection
                .queryWithRelation(config.authorizationRelationshipName))
            val rawJsonAuthGraph: String = authGraph.toJson()
            // Saving graph JSON as authorized view
            monitoringDbConnection.saveGraphState(rawJsonAuthGraph, 3, "Authorized")

            // Querying validation rules
            val validationRules = monitoringDbConnection.queryValidationRules()
            // Executing validation rules on the graph db
            val executedRules = graphDbConnection.executeValidationRules(validationRules)
            // Saving the result of execution
            monitoringDbConnection.saveExecutedRules(executedRules)
            println("Export is done")
            // Waiting x seconds before restarting the process
            Thread.sleep(config.runInterval)
        }
    }
}