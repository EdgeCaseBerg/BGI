package bgi.services

import bgi.models._
import bgi.models.dao.CategoryDAO

import scala.concurrent.{Future, future}
import scala.concurrent.ExecutionContext.Implicits.global


/** Service abstraction over operations involving the Categories
 *
 * Provides layer over basic DAO and ability to perform business logic
 * surrounding the category object.
 */
class CategoryService(implicit val categoryDAO : CategoryDAO) extends BaseService[Category]{
	type Categories = List[Category]
	
	/** Returns all line items 
	 *
	 * @return A Future of a list of Categories in the system
	 */
	def getAllForUser(user: User) : Future[Categories] = categoryDAO.getAllForUser(user)

	/** Retrieve all the preffered categories from the database 
	 *
	 * @return A Future of a list of Categories that are preffered in the system
	 */
	def getPrefferedByUser(user: User) : Future[Categories] = categoryDAO.getPrefferedByUser(user)
}