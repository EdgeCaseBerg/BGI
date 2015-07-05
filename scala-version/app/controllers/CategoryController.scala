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
		val futureCategories = categoryService.getAllForUser(request.user)
		val futureResult = for {
			categories <- futureCategories
		} yield CategoryPageData(categories)
		Ok(views.html.categories(Await.result(futureResult, 10.seconds)))
	}

	def create = Authenticated.async { implicit request =>
		CreateCategoryForm.form.bindFromRequest.fold(
			formWithErrors => {
				Future.successful(Redirect(bgi.controllers.routes.Category.categories).flashing("error" -> "Error! Category name must be between 3-64 characters!"))
			},
			boundForm => {
				categoryService.create(new Category(-1, request.user.id, boundForm.name, 0, 0)).map { optionCategory =>
					optionCategory match {
						case None => Redirect(bgi.controllers.routes.Category.categories).flashing("error" -> "Could not create Category. Please Contact Us")
						case _ => Redirect(bgi.controllers.routes.Category.categories).flashing("success" -> "Successfully Created Category!")
					}
				}
			}
		)
	}
}

object Category extends CategoryController with AnormContext{ 

}
