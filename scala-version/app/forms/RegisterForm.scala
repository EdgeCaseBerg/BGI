package bgi.forms

import play.api.data._
import play.api.data.Forms._
import play.api.data.validation.{Valid, Invalid, Constraint, ValidationError}

import com.typesafe.config.ConfigFactory

/** Container for the registration form 
 * 
 * @param username The submitted username, a string length 3-128
 * @param password The submitted password, a string length 4-256
 * @param signupcode The signup code, this should match the application's configured signup code
 */
case class RegisterForm(username: String, password: String, signupcode: String)

/** Companion object of the registration form 
 *
 * Provides a constraint to match against the application's signupcode, which 
 * should be configured in application.conf by app.signupcode.
 *
 * {{{
 * RegisterForm.form.bindFromRequest.fold(
 *   formWithErrors => { BadRequest(...)} , 
 *   formNoErrors => { Redirect(...) }
 * )	
 * }}}
 */
object RegisterForm {
	private val envConfig = ConfigFactory.load()
	private val configuredCode = envConfig getString "app.signupcode"

	private val signUpCodeMatches : Constraint[String] = Constraint("signupcode.valid")({ signupcode : String =>	
		signupcode match {
			case `configuredCode` => Valid
			case _ => Invalid(Seq(ValidationError("Sign Up Code invalid!")))
		}
	})

	val form = Form(
		mapping(
			"username" -> nonEmptyText(minLength = 3, maxLength = 128),
			"password" -> nonEmptyText(minLength = 4, maxLength = 256),
			"signupcode" -> nonEmptyText.verifying(signUpCodeMatches)
		)(RegisterForm.apply)(RegisterForm.unapply)
	)
}
