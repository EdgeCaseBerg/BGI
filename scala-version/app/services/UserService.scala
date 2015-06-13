package bgi.services

import bgi.models._
import bgi.models.dao.UserDAO

import org.mindrot.jbcrypt.BCrypt

import scala.concurrent.{Future, future}
import scala.concurrent.ExecutionContext.Implicits.global

/** Service abstraction over operations involving the user
 *
 * Provides layer over basic DAO and ability to perform business logic
 * surrounding the user object.
 */
class UserService(implicit val userDAO : UserDAO) {

	/** Wrapper around userDAO method
	 * 
	 * @see [[bgi.models.dao.UserDAO]]
	 */
	def findUserById(id: Long) : Future[Option[User]] = userDAO.findById(id)

	/** Wrapper around userDAO method
	 * 
	 * @see [[bgi.models.dao.UserDAO]]
	 */
	def createUser(user : User) : Future[Option[User]] = userDAO.create(user)

	/** Wrapper around userDAO method
	 * 
	 * @see [[bgi.models.dao.UserDAO]]
	 */
	def updateUser(user: User) : Future[Boolean] = userDAO.update(user)

	/** Wrapper around userDAO method
	 * 
	 * @see [[bgi.models.dao.UserDAO]]
	 */
	def removeById(id: Long) : Future[Boolean] = userDAO.remove(id)

	/** Wrapper around userDAO method
	 * 
	 * @see [[bgi.models.dao.UserDAO]]
	 */
	def incrementLoginAttemptForUser(user: User) : Future[Boolean] = userDAO.incrementLoginAttempt(user)

	/** Wrapper around userDAO method
	 * 
	 * @see [[bgi.models.dao.UserDAO]]
	 */
	def resetLoginAttemptsForUser(user: User) : Future[Boolean] = userDAO.resetLoginAttempts(user)


	/** Method to update a user's password. Can be used to rehash the old password to a new as well
	 * 
	 * @param user The user which should be updated with the new password information
	 * @param newPassword The cleartext new password to be hashed
	 * @param newComplexity The complexity which the hash should be updated to
	 * @return A Future boolean on whether the user was successfully updated or not
	 */
	def rehashWithComplexity(user : User, newPassword: String, newComplexity: UserPasswordComplexity.Complexity) : Future[Boolean] = {
		val newHash = BCrypt.hashpw(newPassword, BCrypt.gensalt(newComplexity))
		updateUser(user.copy(hash = UserPassword(newHash, newComplexity)))
	}

	/** Method to determine is a given password matches the stored hashed value
	 * 
	 * @note This method will reset the login attempts made be a user if the passwords match
	 * 
	 * @param user The user to authenticate against the possiblePassword
	 * @param possiblePassword The password to check as a match for the stored hash
	 * @return A Future boolean on whether the passwords matched or not
	 */
	def authenticateUser(user: User, possiblePassword: String) : Future[Boolean] = {
		BCrypt.hashpw(possiblePassword, BCrypt.gensalt(user.hash.complexity)) match {
			case user.hash =>
				resetLoginAttemptsForUser(user)
			case _ => 
				future {
					false	
				}
		}
	}
}