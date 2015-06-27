package bgi.globals

import play.api._
import play.api.http._
import play.api.mvc._

import bgi.models.User

import scala.util.{Try, Success, Failure}
import scala.concurrent._
import scala.concurrent.ExecutionContext.Implicits.global


class AuthenticatedRequest[A](val user: User, request: Request[A]) extends WrappedRequest[A](request)

object Authenticated extends ActionBuilder[AuthenticatedRequest] with AnormContext{
	val failRedir = Future.successful(Results.Redirect("/").flashing("error" -> "Please Login and have cookies enabled"))

	def invokeBlock[A](request: Request[A], block: (AuthenticatedRequest[A]) => Future[Result]) = {
		request.session.get("userId").map { stringUserId =>
			Try(stringUserId.toLong) match {
				case Failure(e) => failRedir
				case Success(userId) => 
					val possibleUser : Future[Option[User]] = userService.findUserById(userId)
					possibleUser.flatMap { optionUser =>
						optionUser match { 
							case Some(user) => block(new AuthenticatedRequest(user, request))
							case _ => failRedir
						}	
					}
			}
		}.getOrElse {
		  failRedir
		}
	}
}