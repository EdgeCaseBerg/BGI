package bgi.controllers

import play.api._
import play.api.mvc._

import bgi.forms._
import bgi.models._
import bgi.services._
import bgi.globals.{Context, AnormContext, Authenticated}

import org.mindrot.jbcrypt.BCrypt

import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.Future

/** Controller for handling generic non-specific pages */
abstract class AuthController extends Controller with Context {
	def register = Action.async { implicit request =>
		RegisterForm.form.bindFromRequest.fold(
			formWithErrors => {
				/* We don't actually want to tell them what went wrong :^) */
				Future.successful(
					Redirect(bgi.controllers.routes.Dashboard.index).flashing("error" -> "Error! Please ensure your username and password are at least 4 characters long and you've entered the right admin code")
				)
			},
			boundForm => {
				val newUser = new User(name = boundForm.username, hash = UserPassword(BCrypt.hashpw(boundForm.password, BCrypt.gensalt(UserPasswordComplexity.Normal)), UserPasswordComplexity.Normal))
				userService.create(newUser).map { optionUser =>
					optionUser match {
						case None =>	
							Redirect(bgi.controllers.routes.Dashboard.index).flashing("error" -> "Error! Please ensure your username and password are at least 4 characters long and you've entered the right admin code")
						case Some(user) => 
							Redirect(bgi.controllers.routes.Dashboard.dashboard)
								.withSession("userId" -> user.id.toString)
								.flashing("success" -> "Successfully created user, please sign in!")
					}
				}
			}
		)
	}

	def login = Action.async { implicit request =>
		LoginForm.form.bindFromRequest.fold(
			formWithErrors => {
				Future.successful(
					Redirect(bgi.controllers.routes.Dashboard.index).flashing("error" -> "Sorry, that doesn't seem right. Try Again")
				)
			},
			boundForm => {
				userService.findUserByUsername(boundForm.username).flatMap { optionUser =>
					optionUser match {
						case None => 
							Future.successful(Redirect(bgi.controllers.routes.Dashboard.index).flashing("error" -> "Sorry, that doesn't seem right. Try Again"))
						case Some(user) if user.loginAttempts < UserPasswordComplexity.MaxAttempts =>
							val authFuture : Future[Boolean] = userService.authenticateUser(user, boundForm.password)
							authFuture.map { isUser => 
								if(isUser) {
										Redirect(bgi.controllers.routes.Dashboard.dashboard)
											.withSession("userId" -> user.id.toString)
											.flashing("success" -> "You have been logged in")
								} else {									
										Redirect(bgi.controllers.routes.Dashboard.index)
											.flashing("error" -> "Sorry, that doesn't seem right. Try Again")
											.withNewSession			
								}
							}
						case Some(user) =>
							Future.successful(Redirect(bgi.controllers.routes.Dashboard.index).flashing("error" -> "Your account has been locked due to too many attempts to login"))
					}		
				}
			}
		)
		
	}
}

object Auth extends AuthController with AnormContext{ 

}
