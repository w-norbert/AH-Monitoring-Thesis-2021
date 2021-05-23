package GraphDB.Query

/**
 * A collection of queries related to Graph deletion
 */
object Drop {
    // Deletes every relationships in the graph
    val relationships = "MATCH (a) -[r] -> (b) DELETE a, r"
    // Deletes every nodes in the graph
    val nodes =  "MATCH (a) DELETE a"
}
