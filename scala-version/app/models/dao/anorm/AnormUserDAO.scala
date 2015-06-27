package bgi.models.dao.anorm

import bgi.models.dao.UserDAO
import bgi.models.{User, UserPassword, UserPasswordComplexity}, UserPasswordComplexity._

import play.api.db._
import play.api.Play.current
 
import anorm._
import anorm.SqlParser._

import scala.concurrent.{ExecutionContext, Future, future}
import scala.language.postfixOps

/** Class representing DAO layer for Users using Anorm
 * 
 * Implementation of UserDAO, uses the anorm library to speak to 
 * RDBMS style databases
 */
class AnormUserDAO extends UserDAO{
	/** Simple Anorm Mapper for a User row */
	val fullUserParser = {
		get[String]("name") ~
		get[String]("hash") ~ 
		get[Int]("complexity") ~ 
		get[Option[String]]("email") ~ 
		get[Int]("loginAttempts") ~ 
		get[Long]("id") map {
			case name ~ hash ~ complexity ~ 
				email ~ loginAttempts ~ id => 
				User(name, UserPassword(hash, complexity.asComplexity), email, loginAttempts, id)
		}
	}

	def create(user: User)(implicit ec: ExecutionContext) : Future[Option[User]] = {
		val newId : Option[Long] = DB.withConnection { implicit connection =>
      SQL("""
      	INSERT INTO users (name, hash, complexity, email, loginAttempts) 
      	VALUES ({name}, {hash}, {complexity}, {email}, {loginAttempts})
      	""").on(
        	"name" -> user.name,
        	"hash" -> user.hash.hash,
        	"complexity" -> user.hash.complexity.toInt,
        	"email" -> user.email,
        	"loginAttempts" -> user.loginAttempts
      	).executeInsert()
    }
    newId.fold( 
    	/* Silly, but we need to specify the type or erasure will stop compilation */
    	future { val o : Option[User] = None; o } 
    ) { id =>
    	findById(id)
    }
	}

	def findById(id: Long)(implicit ec: ExecutionContext): Future[Option[User]] = future {
		DB.withConnection { implicit connection =>
			val optionUser : Option[User] = SQL("""
      		SELECT name, hash, complexity, email, loginAttempts, id FROM users 
      		WHERE id = {id}
      		"""
      ).on("id" -> id).as(fullUserParser *).headOption
      optionUser
		}
	}

	def findByUsername(username: String)(implicit ec: ExecutionContext): Future[Option[User]] = future {
		DB.withConnection { implicit connection =>
			val optionUser : Option[User] = SQL("""
				SELECT name, hash, complexity, email, loginAttempts, id FROM users 
      	WHERE name = {name}
				"""
			).on("name" -> username).as(fullUserParser *).headOption
			optionUser
		}
	}

	def remove(id: Long)(implicit ec : ExecutionContext): Future[Boolean] = future {
		DB.withConnection { implicit connection => 
			val numberEffected = SQL("""
				DELETE FROM users WHERE id = {id}
				"""
				).on("id" -> id).executeUpdate()
			numberEffected match {
				case 0 => false
				case _ => true
			}
		}
	}

	def update(user: User)(implicit ec: ExecutionContext): Future[Boolean] = future {
		DB.withConnection { implicit connection =>
			val numberEffected = SQL("""
				UPDATE users SET 
					name = {name}, 
					hash = {hash}, 
					complexity = {complexity},
					email = {email},
					loginAttempts = {loginAttempts}
					WHERE id = {id}
				""").on(
					"name" -> user.name,
        	"hash" -> user.hash.hash,
        	"complexity" -> user.hash.complexity.toInt,
        	"email" -> user.email,
        	"loginAttempts" -> user.loginAttempts,
        	"id" -> user.id
				).executeUpdate()
			numberEffected match {
				case 0 => false
				case _ => true
			}
		}
	}

	def incrementLoginAttempt(user: User)(implicit ec: ExecutionContext) : Future[Boolean] = 
		update(user.copy(loginAttempts = user.loginAttempts + 1))

	def resetLoginAttempts(user: User)(implicit ec: ExecutionContext) : Future[Boolean] = 
		update(user.copy(loginAttempts = 0))

}