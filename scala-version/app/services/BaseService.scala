package bgi.services

import bgi.models._
import bgi.models.dao.CrudDAO

import org.mindrot.jbcrypt.BCrypt

import scala.concurrent.{Future, future}
import scala.concurrent.ExecutionContext.Implicits.global

/** Service abstraction over operations involving a model with CRUD operations
 *
 * Provides layer over basic DAO and ability to perform business logic
 * surrounding the model object.
 */
class BaseService[Model](implicit val crudDAO : CrudDAO[Model]) {

	/** Wrapper around crudDAO method
	 * 
	 * @see [[bgi.models.dao.CrudDAO]]
	 */
	def findById(id: Long) : Future[Option[Model]] = crudDAO.findById(id)

	/** Wrapper around crudDAO method
	 * 
	 * @see [[bgi.models.dao.CrudDAO]]
	 */
	def create(model : Model) : Future[Option[Model]] = crudDAO.create(model)

	/** Wrapper around crudDAO method
	 * 
	 * @see [[bgi.models.dao.CrudDAO]]
	 */
	def update(model: Model) : Future[Boolean] = crudDAO.update(model)

	/** Wrapper around crudDAO method
	 * 
	 * @see [[bgi.models.dao.CrudDAO]]
	 */
	def removeById(id: Long) : Future[Boolean] = crudDAO.remove(id)

}