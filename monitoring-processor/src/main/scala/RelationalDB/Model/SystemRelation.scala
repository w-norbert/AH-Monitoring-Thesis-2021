package RelationalDB.Model

/**
 * This model which represents a relation (connection, Auth rule, store orchestration rule, etc) between a consumer a provider systems
 * @param consumer_system_name String  Name of the consumer system
 * @param consumer_system_address String Address of the consumer system
 * @param consumer_system_port String  Port of the consumer system
 * @param provider_system_name String Name of the provider system
 * @param provider_system_address String  Address of the provider system
 * @param provider_system_port String Port of the provider system
 * @param service_definition String Name of the service
 * @param interface_name String Name of the interface
 * @param service_uri String URI of the service
 * @param orchestration_priority String The orchestration priority (priority in the orchestration_store table)
 */
case class SystemRelation(consumer_system_name: String, consumer_system_address: String, consumer_system_port: String,
                          provider_system_name: String, provider_system_address: String, provider_system_port: String,
                          service_definition: String, interface_name: String, service_uri: String, orchestration_priority: String)
{
  override def toString():String=
  {
    s"consumer_system_name : $consumer_system_name, provider_system_name: $provider_system_name, service_definition: $service_definition"
  }
}