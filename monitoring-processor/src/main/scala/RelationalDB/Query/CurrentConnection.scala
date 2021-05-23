package RelationalDB.Query

/**
 * A collection of queries related to Consumer-Provider connections
 */
object CurrentConnection {
    // Returns all current connections which are news then a datetime (?) joined by system, service, interface properties
    val queryAll: String = "SELECT consumer_system.system_name as consumer_system_name," +
        "consumer_system.address as consumer_system_address," +
        "consumer_system.port as consumer_system_port," +
        "provider_system.system_name as provider_system_name," +
        "provider_system.address as provider_system_address," +
        "provider_system.port as provider_system_port," +
        "sd.service_definition as service_definition," +
        "si.interface_name as interface_name," +
        "sr.service_uri as service_uri," +
        "connection.updated_at as updated_at," +
        "connection.terminated_at as terminated_at " +
        "FROM orchestration_connection connection " +
        "INNER JOIN system_ as consumer_system ON connection.requester_id = consumer_system.id " +
        "INNER JOIN system_ as provider_system ON connection.provider_id = provider_system.id " +
        "INNER JOIN service_definition as sd ON connection.service_id = sd.id " +
        "INNER JOIN service_interface as si ON connection.interface_id = si.id " +
        "INNER JOIN service_registry as sr ON connection.service_id = sr.service_id and connection.provider_id  = sr.system_id " +
        "WHERE terminated_at IS NULL AND connection.updated_at >= ?"
}
