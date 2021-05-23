package GraphDB.Query

/**
 * A collection of queries related to saving a graph
 */
object  Save {
    // Saves a node  into the graph with the following properties: name, address, port
    val node: String = "MERGE (s:System {name: $name, address: $address, port: $port})"
    // Creates a relationship between two nodes with the following properties: consumer_name, provider_name,
    // relationship_name, service_definition, service_uri, interface_name, orchestration_priority
    val relationship: String ="MATCH (a:System {name:$consumer_name}),(b:System {name: $provider_name}) MERGE " +
        "(a)-[r: $relationship_name {service_definition: $service_definition, service_uri: $service_uri ,interface: $interface, orchestration_priority: $orchestration_priority}]->(b)"
}
