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
