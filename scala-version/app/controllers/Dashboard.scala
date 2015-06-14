package bgi.controllers

import play.api._
import play.api.mvc._

import bgi.forms._
import bgi.models._
import bgi.services._
import bgi.globals.Context
import bgi.globals.ProtoContext

import org.mindrot.jbcrypt.BCrypt

import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.Future

/** Controller for handling generic non-specific pages */
abstract class DashboardController extends Controller with Context {
	def index = Action { implicit request =>
		Ok(views.html.index())
	}

	def register = Action.async { implicit request =>
		RegisterForm.form.bindFromRequest.fold(
			formWithErrors => {
				/* We don't actually want to tell them what went wrong :^) */
				Future.successful(
					Redirect("/").flashing("error" -> "Error! Please ensure your username and password are at least 4 characters long and you've entered the right admin code")
				)
			},
			boundForm => {
				
				val newUser = new User(name = boundForm.username, hash = UserPassword(BCrypt.hashpw(boundForm.password, BCrypt.gensalt(UserPasswordComplexity.Normal)), UserPasswordComplexity.Normal))
				val createdUserFuture = userService.createUser(newUser)
				for {
					createdUser <- createdUserFuture
				} yield createdUser
				createdUserFuture.map { user =>
					Redirect("/").flashing("success" -> "Successfully created user, please sign in!")
				}
			}
		)
	}
}

object Dashboard extends DashboardController with ProtoContext{ 

}
