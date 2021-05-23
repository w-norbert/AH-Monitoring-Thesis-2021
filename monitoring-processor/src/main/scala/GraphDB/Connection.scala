package GraphDB

import GraphDB.Model.{Graph, Node, ValidationRule}
import RelationalDB.Model.SystemRelation
import org.neo4j.driver.internal.value.{NodeValue, RelationshipValue}
import org.neo4j.driver.types.Relationship
import org.neo4j.driver.{AuthTokens, Config, GraphDatabase, Session}

import java.util
import java.util.ArrayList
import scala.collection.mutable
import scala.jdk.CollectionConverters._

/**
 * Class for handling Graph Database connection
 */
class Connection {
    var session: Session = null;

    /**
     * Constructor for the GraphDB.Connection class
     * @param uri String URI of the database
     * @param username String username of the database
     * @param password String password of the database
     */
    def this(uri: String, username: String, password: String) = {
        this()
        val driver = GraphDatabase.driver(uri, AuthTokens.basic(username, password), Config.builder().withoutEncryption().build())
        this.session = driver.session
    }

    /**
     * This method drops all nodes and relationships in the database
     */
    def drop(): Unit = {
        val resultDropRelationship = this.session.run(Query.Drop.relationships)
        val resultDropNodes = this.session.run(Query.Drop.nodes)
    }

    /**
     * This method returns all nodes and relationships from the database
     * @return Graph the resulting graph
     */
    def queryAll(): Graph = {
        queryGraph(Query.Match.all);
    }

    /**
     * Queries a graph with a specified relationship type
     * @param relation String name of relationship
     * @return Graph the resulting graph
     */
    def queryWithRelation(relation: String): Graph = {
        //Bug: Cannot use prepared statement for relationship name, simple string replacement is used instead
        //queryGraph(Query.Match.withRelation, Map("relation"-> relation.asInstanceOf[AnyRef]).asJava);
        val query: String = Query.Match.withRelation.replace("$relation", relation)
        queryGraph(query)
    }

    /**
     * Queries the graph db with parameters
     * @param query String the prepared query placeholder: ":<name_of_param>"
     * @param parameters Map[String, AnyRef] parameters, key= name of placeholder, value is the value
     * @return
     */
    def queryGraph(query: String, parameters: util.Map[String, AnyRef] = null): Graph = {
        val nodes: mutable.HashMap[Long, Node] = mutable.HashMap[Long, Node]()
        val relationships: mutable.HashMap[Long, Model.Relationship] =  mutable.HashMap[Long, Model.Relationship]()
        val relationShipsResultProvider = if(parameters != null) this.session.run(query, parameters) else this.session.run(query)

        while (relationShipsResultProvider.hasNext) {
            val next = relationShipsResultProvider.next()
            if(next.containsKey("n")) {
                val nodeN = nodeValueToNode(next.get("n").asInstanceOf[NodeValue])
                nodes.put(nodeN.id, nodeN)
            }
            if(next.containsKey("r")) {
                val nextR = next.get("r")
                if(nextR.isInstanceOf[RelationshipValue]) {
                    val relationshipR = relationshipValueToRelationship(nextR.asInstanceOf[RelationshipValue])
                    relationships.put(relationshipR.id, relationshipR)
                }
            }
            if(next.containsKey("m")) {
                val nodeM = nodeValueToNode(next.get("m").asInstanceOf[NodeValue])
                nodes.put(nodeM.id, nodeM)
            }
        }
        Graph(nodes, relationships)
    }

    /**
     * Converts a RelationshipValue into Model.Relationship
     * @param relationshipValue RelationshipValue input relationship
     * @return Model.Relationship output Relationship
     */
    def relationshipValueToRelationship(relationshipValue: RelationshipValue): Model.Relationship = {
        val rawRelationship = relationshipValue.asRelationship()
        Model.Relationship(rawRelationship.id(), rawRelationship.startNodeId(), rawRelationship.endNodeId(),
            rawRelationship.`type`(), rawRelationship.asMap().asInstanceOf[util.Map[String, String]])
    }

    /**
     * Converts a NodeValue into Model.Node
     * @param nodeValue NodeValue input node
     * @return Model.Node  output node
     */
    def nodeValueToNode(nodeValue: NodeValue): Node = {
        val rawNode = nodeValue.asNode()
        Node(rawNode.id(), rawNode.labels().asInstanceOf[util.ArrayList[String]],
            rawNode.asMap().asInstanceOf[util.Map[String, String]])
    }

    /**
     * Executes a list of graph queries on the graph db
     * @param rules ArrayList[ValidationRule] input validation rules
     * @return HashMap[Long,Boolean] The result HashMap where the key is the id of the executed rule and
     *         the value is true if that rule is executed and has no error
     */
    def executeValidationRules(rules: util.ArrayList[ValidationRule]): mutable.HashMap[Long, Boolean] = {
        val resultMap = new mutable.HashMap[Long, Boolean]
        rules.forEach(rule => {
            var isValid: Boolean = false
            try {
                val queryResult = this.session.run(rule.rule)
                if(rule.positiveEvaluation) {
                    isValid = queryResult.hasNext
                }
                else {
                    isValid = !queryResult.hasNext
                }
            }
            catch {
                case ex: Exception => {
                    println("Validation rule exception:")
                    println(ex.getMessage)
                }
            }
            resultMap.put(rule.id, isValid)
        })
        resultMap
    }

    /**
     * Saves relations of a provider and consumer system under a relationship type
     * @param itemsToSave ArrayList[SystemRelation] the list of relations to save
     * @param relationship String name of the relationship between the systems
     */
    def save(itemsToSave: util.ArrayList[SystemRelation] , relationship: String): Unit = {
        itemsToSave.forEach(item => {
            val providerNodeParams = new util.HashMap[String, AnyRef] ()
            providerNodeParams.put("name", item.provider_system_name)
            providerNodeParams.put("address", item.provider_system_address)
            providerNodeParams.put("port", item.provider_system_port)

            val resultProvider = this.session.run(Query.Save.node, providerNodeParams)

            val consumerNodeParams = new util.HashMap[String, AnyRef] ()
            consumerNodeParams.put("name", item.consumer_system_name)
            consumerNodeParams.put("address", item.consumer_system_address)
            consumerNodeParams.put("port", item.consumer_system_port)
            val resultConsumer = this.session.run(Query.Save.node, consumerNodeParams)

            val relationshipParams = new util.HashMap[String, AnyRef] ()
            relationshipParams.put("consumer_name", item.consumer_system_name)
            relationshipParams.put("provider_name", item.provider_system_name)
            //Cannot set relationship name as prepared parameter
            //relationshipParams.put("relationship_name", relationship)
            relationshipParams.put("service_definition", item.service_definition)
            relationshipParams.put("service_uri", item.service_uri)
            relationshipParams.put("interface", item.interface_name)
            relationshipParams.put("orchestration_priority", item.orchestration_priority)
            val query: String = Query.Save.relationship.replace("$relationship_name", relationship)
            val resultCreateRelationship = this.session.run(query, relationshipParams)
        })
    }
}
