package GraphDB.Model

import scala.collection.mutable

/**
 * A model class for a GraphDB.Graph
 * @param nodes HashMap[Long,Node] the map of nodes in the graph, there the key is the node id and the value is the node
 * @param relationships HashMap[Long, Relationship] the map of relationship in the graph, there the key is the
 *                      relationship id and the value is the relationship
 */
case class Graph(nodes: mutable.HashMap[Long, Node], relationships: mutable.HashMap[Long, Relationship])
