package bgi.models.dao

import bgi.models._

import scala.concurrent.{Future, ExecutionContext}

/** User specific DAO operations 
 * 
 * All functions take an implicit ExecutionContext which is used to 
 * run the future threads in. 
 */
trait UserDAO extends CrudDAO[User] {
	/** Increase the count of how many login attempts there have been for a User
	 *
	 * @param user The user who will have their login attempts variable incremented
	 * @param ec Implicit ExecutionContext to run future threads in
	 * @return A Future containing a boolean of whether the user was incremented or not
	 */
	def incrementLoginAttempt(user: User)(implicit ec: ExecutionContext) : Future[Boolean]

	/** Reset the loginAttempts for a user to 0 
	 * 
	 * This function is to be used when a user successfully logs in
	 *
	 * @param user The user who will have their login attempts reset
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a boolean of whether the user was reset or not
	 */
	def resetLoginAttempts(user: User)(implicit ec: ExecutionContext) : Future[Boolean]
}
