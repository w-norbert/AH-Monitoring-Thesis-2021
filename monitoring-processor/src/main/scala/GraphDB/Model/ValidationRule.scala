package GraphDB.Model

/**
 * Model class for a GraphDB.ValidationRule
 * @param id Long the identifier of the rule
 * @param name String name of the rule
 * @param rule String the rule itself
 * @param positiveEvaluation Boolean if positive then the rule is valid when it is has a result after execution
 *                           false otherwise
 * @param active Boolean true if the rule is active false otherwise
 * @param createdAt String creation datetime
 * @param updatedAt String datetime when it was updated
 */
case class ValidationRule(id: Long, name: String, rule: String, positiveEvaluation: Boolean, active: Boolean, createdAt: String, updatedAt: String)