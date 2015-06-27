package bgi.forms

import play.api.data._
import play.api.data.Forms._

import com.typesafe.config.ConfigFactory

/** Container for the login form 
 * 
 * @param username The submitted username, a string length 3-128
 * @param password The submitted password, a string length 4-256
 */
case class LoginForm(username: String, password: String)

/** Companion object of the login form 
 *
 * {{{
 * LoginForm.form.bindFromRequest.fold(
 *   formWithErrors => { BadRequest(...)} , 
 *   formNoErrors => { Redirect(...) }
 * )	
 * }}}
 */
object LoginForm {
	val form = Form(
		mapping(
			"username" -> nonEmptyText(minLength = 3, maxLength = 128),
			"password" -> nonEmptyText(minLength = 4, maxLength = 256)
		)(LoginForm.apply)(LoginForm.unapply)
	)
}
