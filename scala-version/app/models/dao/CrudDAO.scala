package bgi.models.dao

import scala.concurrent._

/** Create. Read. Update. Remove trait for data access of models 
 * 
 * Each method takes an implicit ExecutionContext to run the future threads 
 * in. This is to support async access to datastores for a flexible data layer.
 */
trait CrudDAO[T] {
	/** Create a model of type T in the underlying datastore
	 * 
	 * @param model The model which will be created in the datastore
	 * @param ec Implicit execution context in which to run threads in
	 * @return A Future containing an option holding the created object or None
	 */
	def create(model: T)(implicit ec: ExecutionContext) : Future[Option[T]]

	/** Find a model by its ID.  
	 * 
	 * @param id The id of the model you would like to find in the datastore
	 * @param ec Implicit execution context containing in which to run threads in
	 * @return An option containing the found type T or None if unavailable or non-existent
	 */
	def findById(id: Long)(implicit ec: ExecutionContext) : Future[Option[T]]

	/** Update a model to have the same values as the given
	 *
	 * The model is found by the id in the passed object. 
	 * 
	 * @param model The updated model to place into the datastore
	 * @param ec Implicit execution context containing in which to run threads in
	 * @return A Future containing the result of whether the model was updated or not
	 */
	def update(model: T)(implicit ec: ExecutionContext) : Future[Boolean]

	/** Remove a model from the underlying datastore by ither s id
	 * 
	 * @param id The id of the model to remove
	 * @param ec Implicit execution context containing in which to run threads in
	 * @return A Future containing the result of whether the model was removed or not
	 */
	def remove(id: Long)(implicit ec: ExecutionContext) : Future[Boolean]
}
