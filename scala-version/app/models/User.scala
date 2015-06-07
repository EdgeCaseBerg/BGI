package bgi.models

import scala.language.implicitConversions

/** Object representing the complexity of a user's password
 * 
 * Really nothing more than a type alias/enum in order to state things
 * with clarity like so:
 *
 * {{{
 * BCrypt.hashpw("p", BCrypt.gensalt(UserPasswordComplexity.Normal))
 * }}}
 *
 */
object UserPasswordComplexity extends Enumeration {
	type Complexity = Value
	val Normal, Difficult, Hard = Value

	implicit def complexityToInteger(c: Complexity) : Int = { 
		c match {
			case Normal => 10
			case Difficult => 12
			case Hard => 15
		}
	}

}

/** Class representing a password hash of a user, saved with the complexity to compute it 
 * 
 * @note When a user is created a number of these will be created so that the complexity can be checked against different work factors
 * @param hash The hash of the password
 * @param complexity The work factor to use when checking the hash with BCrypt
 */
case class UserPassword(hash: String, complexity: UserPasswordComplexity.Complexity)

/** Class to represent a simple user of the system
 *
 * @note The number of login attempts is used during Bcrypt to assign how slow to be
 * 
 * @param id Identifying number used to associate user with data
 * @param name login name 
 * @param hash See [[UserPassword]]
 * @param email optional email address, this is used for emailing weekly reports
 * @param loginAttempts number of times an unsuccesful login attempt has been made. 
 */
case class User(id: Long, name: String, hash: UserPassword, email: Option[String] = None, loginAttempts: UserPasswordComplexity.Complexity = UserPasswordComplexity.Normal)