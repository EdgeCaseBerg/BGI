package bgi.controllers

import play.api._
import play.api.mvc._

object Dashboard extends Controller {

	def index = Action {
		Ok("Hello BGI")
	}

}
