package bgi.controllers

import play.api._
import play.api.mvc._

import bgi.forms._
import bgi.services._
import bgi.globals.Context
import bgi.globals.ProtoContext


/** Controller for handling generic non-specific pages */
abstract class DashboardController extends Controller with Context {
	def index = Action { implicit request =>
		Ok(views.html.index())
	}

	def register = Action { implicit request =>
		RegisterForm.form.bindFromRequest.fold(
			formWithErrors => {
				/* We don't actually want to tell them what went wrong :^) */
				Redirect("/").flashing("error" -> "Error! Please ensure your username and password are at least 4 characters long and you've entered the right admin code")
			},
			boundForm => {
				Ok(views.html.index())
			}
		)
	}
}

object Dashboard extends DashboardController with ProtoContext{ 

}
