package bgi.models.dao

import bgi.models._

import scala.concurrent.{Future, ExecutionContext}

/** Category specific DAO operations 
 * 
 * All functions take an implicit ExecutionContext which is used to 
 * run the future threads in. 
 */
trait CategoryDAO extends CrudDAO[Category] {
	type Categories = List[Category]

	/** Retrieves all Categories
	 *
	 * @param ec Implicit ExecutionContext to run future threads in
	 * @return A Future containing a list of Categories matching the tag
	 */
	def getAllForUser(user: User)(implicit ec: ExecutionContext) : Future[Categories]

	/** Retrieves all Categories that are preffered by the user
	 * 
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of Categories matching the categories provided
	 */
	def getPrefferedByUser(user: User)(implicit ec: ExecutionContext) : Future[Categories]
	
}
