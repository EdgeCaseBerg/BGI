package bgi.globals

import bgi.models.dao.prototyping._
import bgi.models.dao._
import bgi.services._

/** Global level dependency injection object
 *
 * Subclasses should be used for providing actual Context's to 
 * controllers or services.
 */
abstract trait Context {
	implicit lazy val userDAO : UserDAO = ???
	implicit lazy val userService : UserService = ???
}

/** A Context for using services wired with prototyping (non-db) DAO's 
 * 
 * Should not be used in production! This is for testing and trying out 
 * ideas.
 */
trait ProtoContext extends Context{
	override implicit lazy val userDAO : UserDAO = new ProtoUserDAO()
	override implicit lazy val userService : UserService = new UserService
}

trait AnormContext extends Context {
	override implicit lazy val userDAO : UserDAO = new AnormUserDAO()
	override implicit lazy val userService : UserService = new UserService
}