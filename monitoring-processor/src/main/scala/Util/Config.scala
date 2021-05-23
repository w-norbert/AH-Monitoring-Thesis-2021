package Util

import java.util.Properties
import scala.io.Source

class Config {
    var arrowheadDbConnectionString:String = _
    var monitoringDbConnectionString:String = _
    var arrowheadDbUsername:String = _
    var monitoringDbUsername:String = _
    var arrowheadDbPassword:String = _
    var monitoringDbPassword:String = _
    var graphDbUri:String = _
    var graphDbUser:String = _
    var graphDbPassword:String = _
    var runInterval: Long = 5*1000
    var connectionLimitSeconds = 30

    val storeRelationshipName = "COULD_CONSUME"
    val connectionRelationshipName = "CONSUMES"
    val authorizationRelationshipName = "AUTHORIZED"

    /**
     * Reading configuration file
     * @param configFilePath Path of the config file
     */
    def this (configFilePath: String) = {
        this
        val properties: Properties = new Properties()
        try {
            val source = Source.fromFile(configFilePath)
            properties.load(source.bufferedReader())
            this.arrowheadDbConnectionString = properties.getProperty("arrowheadDbConnectionString")
            this.monitoringDbConnectionString = properties.getProperty("monitoringDbConnectionString")
            this.arrowheadDbUsername = properties.getProperty("arrowheadDbUsername")
            this.monitoringDbUsername = properties.getProperty("monitoringDbUsername")
            this.arrowheadDbPassword = properties.getProperty("arrowheadDbPassword")
            this.monitoringDbPassword = properties.getProperty("monitoringDbPassword")
            this.graphDbUri = properties.getProperty("graphDbUri")
            this.graphDbUser = properties.getProperty("graphDbUser")
            this.graphDbPassword = properties.getProperty("graphDbPassword")
            this.runInterval = Integer.parseInt(properties.getProperty("runInterval", this.runInterval.toString))
            this.connectionLimitSeconds = Integer.parseInt(properties.getProperty("connectionLimitSeconds",  this.runInterval.toString))
        }
        catch {
            case ex: Exception => {
                System.err.println("application.properties file cannot be loaded or has an invalid property")
                throw ex
            }
        }
    }
}
