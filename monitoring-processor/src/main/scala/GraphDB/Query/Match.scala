package GraphDB.Query

/**
 * A collection of queries related to graph query
 */
object Match {
    // Returns all nodes and edges with a relationship in the graph
    val all = "Match (n)-[r]->(m) Return n,r,m"
    // Returns all nodes with a given relationship name, parameter: relation
    val withRelation = "MATCH (n) OPTIONAL MATCH (n)-[r:$relation]-() RETURN n, r"
    // Returns all relationships with a given name, parameter: relation
    val relation = "Match (n)-[r:$relation]->(m) Return r"
    // Returns all nodes
    val nodes = "MATCH (n) return n"
}
