package RelationalDB.Query

/**
 * A collection of queries related to Store Orchestration
 */
object OrchestrationStore {
    // Returns all store orchestration rules joined by system, service, interface properties
    val queryAll: String = "SELECT " +
        "consumer_system.system_name as consumer_system_name," +
        "consumer_system.address as consumer_system_address," +
        "consumer_system.port as consumer_system_port," +
        "provider_system.system_name as provider_system_name," +
        "provider_system.address as provider_system_address," +
        "provider_system.port as provider_system_port," +
        "sd.service_definition as service_definition," +
        "si.interface_name as interface_name," +
        "sr.service_uri as service_uri," +
        "store.priority as store_priority " +
        "FROM orchestrator_store store " +
        "INNER JOIN system_ as consumer_system " +
        "ON store.consumer_system_id = consumer_system.id " +
        "INNER JOIN system_ as provider_system " +
        "ON store.provider_system_id = provider_system.id " +
        "INNER JOIN service_definition as sd " +
        "ON store.service_id = sd.id  " +
        "INNER JOIN service_interface as si " +
        "ON store.service_interface_id = si.id " +
        "INNER JOIN service_registry as sr " +
        "ON store.service_id = sr.service_id and store.provider_system_id  = sr.system_id ;";
}
