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
				userService.createUser(newUser).map { optionUser =>
					optionUser match {
						case None =>	
							Redirect("/").flashing("error" -> "Error! Please ensure your username and password are at least 4 characters long and you've entered the right admin code")
						case Some(user) => 
							Redirect("/")
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
					Redirect("/").flashing("error" -> "Sorry, that doesn't seem right. Try Again")
				)
			},
			boundForm => {
				userService.findUserByUsername(boundForm.username).flatMap { optionUser =>
					optionUser match {
						case None => 
							Future.successful(Redirect("/").flashing("error" -> "Sorry, that doesn't seem right. Try Again"))
						case Some(user) if user.loginAttempts < UserPasswordComplexity.MaxAttempts =>
							val authFuture : Future[Boolean] = userService.authenticateUser(user, boundForm.password)
							authFuture.map { isUser => 
								if(isUser) {
										Redirect("/")
											.withSession("userId" -> user.id.toString)
											.flashing("success" -> "You have been logged in")
								} else {									
										Redirect("/")
											.flashing("error" -> "Sorry, that doesn't seem right. Try Again")
											.withNewSession			
								}
							}
						case Some(user) =>
							Future.successful(Redirect("/").flashing("error" -> "Your account has been locked due to too many attempts to login"))
					}		
				}
			}
		)
		
	}

	def test = Authenticated { implicit request =>
		val pie = new bgi.models.charts.Pie(80)
		pie.addPortion(bgi.models.charts.Portion(33.5, "Loans" ))
		pie.addPortion(bgi.models.charts.Portion(375, "Fun" ))
		pie.addPortion(bgi.models.charts.Portion(69.12, "Bills" ))
		pie.addPortion(bgi.models.charts.Portion(23.4, "Groceries" ))
		Ok(views.html.svg.pie(pie))
	}
}

object Dashboard extends DashboardController with AnormContext{ 

}
