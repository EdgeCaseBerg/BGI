package bgi.controllers

import play.api._
import play.api.mvc._

import bgi.services._
import bgi.globals.Context
import bgi.globals.ProtoContext

abstract class DashboardController extends Controller with Context {
	def index = Action {
		Ok("Hello BGI")
	}
}

object Dashboard extends DashboardController with ProtoContext{ 

}
