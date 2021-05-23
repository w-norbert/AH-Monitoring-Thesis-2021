package Arrowhead

import GraphDB.Model
import GraphDB.Model.{Graph => GraphModel}

/**
 * This class represents a graph in the Arrowhead FW
 */
class Graph {
    var sourceGraph: GraphModel = null

    /**
     * The constructor of the class
     * @param sourceGraph GraphModel The source graph of this class
     */
    def this(sourceGraph: GraphModel) = {
        this()
        this.sourceGraph = sourceGraph
    }

    /**
     * Parses the graph to JSON value
     * @return String the resulting JSON value
     */
    def toJson(): String = {
        val graph = this.sourceGraph
        var nodes = ""
        for (item <- graph.nodes) {
            val node = item._2;
            val name = node.properties.get("name")
            val address = node.properties.get("address")
            val port = node.properties.get("port")
            val label = node.labels.get(0)
            nodes += s"{'label':'${label}'," +
                s"'name':'${name}'," +
                s"'id':'${node.id}'," +
                s"'address':'${address}',"+
                s"'port':'${port}'},"
        }
        var relationships = ""
        for (item <- graph.relationships) {
            val relationship = item._2;
            val service_definition = relationship.properties.get("service_definition")
            val interface = relationship.properties.get("interface")
            val service_uri = relationship.properties.get("service_uri")
            val orchestration_priority = relationship.properties.getOrDefault("orchestration_priority", "1")
            relationships += s"{" +
                s"'priority':'${getPriority(relationship)}'," +
                s"'orchestration_priority':'${orchestration_priority}'," +
                s"'source':'${relationship.start}'," +
                s"'target':'${relationship.end}'," +
                s"'type':'${relationship.typeName}'," +
                s"'service_definition':'${service_definition}'," +
                s"'interface':'${interface}'," +
                s"'service_uri':'${service_uri}'},"
            //relationships.concat(relationshipString)
        }
        var result = s"{'nodes':[${nodes.dropRight(1)}],'links':[${relationships.dropRight(1)}]}"
        return result.replace('\'','"')
    }

    /**
     * Returns a priority value based on relationship
     * @param relationship Relationship input relationship
     * @return Int resulting priority value
     */
    private def getPriority(relationship: Model.Relationship): Int = {
        if(relationship.typeName.equals("COULD_CONSUME")) {
            return 2
        }
        else if(relationship.typeName.equals("CONSUMES")) {
            return 1
        }
        else if(relationship.typeName.equals("AUTHORIZED")) {
            return 3
        }
        return 10
    }
}
