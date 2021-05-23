package RelationalDB.Query

/**
 * A collection of queries related to GraphView
 */
object GraphState {
    // Inserts or updates a graph view with data: view_id, name, data, updated_at
    val save = "INSERT INTO visualization_graph_state (view_id, name, data, updated_at) VALUES(?, ?, ?, ?) ON DUPLICATE KEY UPDATE data=?,updated_at=?"
}
