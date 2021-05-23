package RelationalDB.Query

/**
 * A collection of queries related to graph validation
 */
object Validation {
    // Returns all active validation rules
    val getRules = "SELECT * FROM validation_rule WHERE active = 1";
    // Updates a validation rule by ID
    val updateResults = "UPDATE validation_rule SET `fulfilled`=?, `last_validation`=? WHERE id=?"
}
