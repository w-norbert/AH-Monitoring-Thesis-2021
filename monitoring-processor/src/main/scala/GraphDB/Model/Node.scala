package GraphDB.Model

import java.util

/**
 * Model class for a GraphDB.Node
 * @param id Long the identifier of the node
 * @param labels ArrayList[String] the list of node labels
 * @param properties Map[String, String] A map of node properties where the key is the name of the property and
 *                   the value is the property
 */
case class Node(id: Long, labels: util.ArrayList[String], properties: util.Map[String, String])
