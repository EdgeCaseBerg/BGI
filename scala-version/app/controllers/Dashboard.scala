package bgi.controllers

import play.api._
import play.api.mvc._

import bgi.forms._
import bgi.models._
import bgi.models.pagedata._
import bgi.services._
import bgi.globals.{Context, AnormContext, Authenticated}

import org.mindrot.jbcrypt.BCrypt

import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.duration._
import scala.concurrent.{Future, Await}

/** Controller for handling generic non-specific pages */
abstract class DashboardController extends Controller with Context {
	def index = Action { implicit request =>
		if (request.session.get("userId").isDefined) {
			Redirect(bgi.controllers.routes.Dashboard.dashboard)
		} else {
			Ok(views.html.index())
		}
	}

	def dashboard = Authenticated { implicit request =>
		val futureLineItems = lineItemService.findInThisMonth
		val futurePrefferedCategories = Future.successful(List[Category]())

		val futureResult = for {
			lineItems <- futureLineItems
			prefferedCategories <- futurePrefferedCategories
		} yield DashboardPageData(lineItems, prefferedCategories)

		Ok(views.html.dashboard(Await.result(futureResult, 5.seconds)))
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
