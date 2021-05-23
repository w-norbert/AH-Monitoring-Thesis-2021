package GraphDB.Model

import java.util

/**
 * Model class for GraphDB.Relationship
 * @param id Long the identifier of the relationship
 * @param start Long the identifier of a staring node
 * @param end Long the identifier of an ending node
 * @param typeName String name of the relationship type
 * @param properties Map[String, String] A map of node properties where the key is the name of the property and
 *                   the value is the property
 */
case class Relationship(id: Long, start: Long, end: Long, typeName: String, properties: util.Map[String, String])
