package bgi.controllers

import play.api._
import play.api.mvc._

import bgi.forms._
import bgi.models._
import bgi.models.pagedata._
import bgi.services._
import bgi.globals.{Context, AnormContext, Authenticated}

import scala.concurrent.ExecutionContext.Implicits.global
import scala.concurrent.{Future,Await}
import scala.concurrent.duration._

abstract class CategoryController extends Controller with Context {
	def categories = Authenticated { implicit request =>
		val futureCategories = categoryService.getAll
		val futureResult = for {
			categories <- futureCategories
		} yield CategoryPageData(categories)
		Ok(views.html.categories(Await.result(futureResult, 10.seconds)))
	}

	def create = Authenticated { implicit request =>
		BadRequest("Not yet implemented")
	}
}

object Category extends CategoryController with AnormContext{ 

}
