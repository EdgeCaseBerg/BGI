package bgi.services

import bgi.models._
import bgi.models.dao.prototyping._

import scala.concurrent._
import scala.concurrent.ExecutionContext.Implicits.global 

import org.scalatest._
import org.scalatest.concurrent.ScalaFutures

class UserServiceSpec extends FlatSpec with ScalaFutures{

	trait TestContext {
		implicit val userDAO = new ProtoUserDAO()
		val password = "test"
		val hashes = Map(
			UserPasswordComplexity.Normal -> """$2a$10$.IgPB.iFU.lrvRQEeWMlReO.PidsyqP4QbnDOLGWdhBu3TvjPjrFe""",
			UserPasswordComplexity.Difficult -> """$2a$12$hIAcrb/9H25HM0hmlVc/du2sfmJ89n93rKp6jSqoTDkvrq28B1Wsu""",
			UserPasswordComplexity.Hard -> """$2a$15$wC8xswg9AqmDUxREqFnmEecFaYj8qSyEWvZ3xbQV5/4aknUslAhCW"""
		)
		val awaitTime = scala.concurrent.duration.Duration("1 seconds")
		val hash = UserPassword(hashes(UserPasswordComplexity.Normal), UserPasswordComplexity.Normal)
		val user = Await.result(userDAO.create( new User("testUser", hash=hash)), awaitTime).get
		val userService = new UserService
		
	}
	
	"The UserService" should "authenticate a user if given the correct password" in new TestContext {
		whenReady(userService.authenticateUser(user, password)) { success =>
			assert(success)
		}
	}

	it should "rehash a password to a higher complexity" in new TestContext {
		whenReady(userService.rehashWithComplexity(user, "test", UserPasswordComplexity.Difficult)) { success =>
			assert(success)
			val updatedUser = Await.result(userService.findById(user.id), awaitTime).get
			assert(updatedUser.hash.hash != user.hash.hash)
			assert(updatedUser.hash.complexity == UserPasswordComplexity.Difficult)
		}
	}

}
