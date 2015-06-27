package bgi.globals

import bgi.models.dao._
import bgi.models.dao.anorm._
import bgi.models.dao.prototyping._
import bgi.services._

/** Global level dependency injection object
 *
 * Subclasses should be used for providing actual Context's to 
 * controllers or services.
 */
abstract trait Context {
	implicit lazy val userDAO : UserDAO = ???
	implicit lazy val lineItemDAO : LineItemDAO = ???
	implicit lazy val userService : UserService = ???
}

trait AnormContext extends Context {
	override implicit lazy val userDAO : AnormUserDAO = new AnormUserDAO()
	override implicit lazy val lineItemDAO : AnormLineItemDAO = new AnormLineItemDAO()
	override implicit lazy val userService : UserService = new UserService
}
