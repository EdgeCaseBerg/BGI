package bgi.models.dao.anorm

import bgi.models.dao.CategoryDAO
import bgi.models._

import play.api.db._
import play.api.Play.current
 
import anorm._
import anorm.SqlParser._

import scala.concurrent.{ExecutionContext, Future, future}
import scala.language.postfixOps

/** Class representing DAO layer for Categories using Anorm
 * 
 * Implementation of CategoryDAO, uses the anorm library to speak to 
 * RDBMS style databases
 */
class AnormCategoryDAO extends CategoryDAO{
	/** Simple Anorm Mapper for a Category row */
	val fullCategoryParser = {
		get[Long]("id") ~
		get[Long]("userId") ~
		get[String]("name") ~ 
		get[Long]("balanceInCents") ~ 
		get[Long]("lastUpdated") map {
			case id ~ userId ~ name ~ balanceInCents ~ lastUpdated => 
				Category(id, userId, name, balanceInCents, lastUpdated)
		}
	}

	def create(category: Category)(implicit ec: ExecutionContext) : Future[Option[Category]] = {
		val newId : Option[Long] = DB.withConnection { implicit connection =>
      		SQL("""
      			INSERT INTO categories (name, userId, balanceInCents, lastUpdated) 
      			VALUES ({name}, {userId}, {balanceInCents}, UNIX_TIMESTAMP(UTC_TIMESTAMP()))
      			"""
      		).on(
        		"name" -> category.name,
        		"userId" -> category.userId,
        		"balanceInCents" -> category.balanceInCents
      		).executeInsert()
    	}
    	newId.fold( 
    		/* Silly, but we need to specify the type or erasure will stop compilation */
    		future { val o : Option[Category] = None; o } 
    	) { id =>
	    	findById(id)
	    }
	}	

	def findById(id: Long)(implicit ec: ExecutionContext): Future[Option[Category]] = future {
		DB.withConnection { implicit connection =>
			val optionCategory : Option[Category] = SQL("""
	      		SELECT id, userId, name, balanceInCents, lastUpdated FROM categories 
	      		WHERE id = {id}
	      		"""
	      		).on("id" -> id).as(fullCategoryParser *).headOption
      		optionCategory
		}
	}

	def remove(id: Long)(implicit ec : ExecutionContext): Future[Boolean] = future {
		DB.withConnection { implicit connection => 
			val numberEffected = SQL("""
				DELETE FROM categories WHERE id = {id}
				"""
				).on("id" -> id).executeUpdate()
			numberEffected match {
				case 0 => false
				case _ => true
			}
		}
	}

	def update(category: Category)(implicit ec: ExecutionContext): Future[Boolean] = future {
		DB.withConnection { implicit connection =>
			val numberEffected = SQL("""
				UPDATE categories SET 
					name = {name}, 
					balanceInCents = {balanceInCents},
					lastUpdated = UNIX_TIMESTAMP(UTC_TIMESTAMP()),
					WHERE id = {id}
				""").on(
					"name" -> category.name,
					"balanceInCents" -> category.balanceInCents,
					"id" -> category.id
				).executeUpdate()
			numberEffected match {
				case 0 => false
				case _ => true
			}
		}
	}

	def getAll()(implicit ec: ExecutionContext) : Future[List[Category]] = future {
		DB.withConnection { implicit connection => 
			val categories = SQL("""
	      	SELECT id, userId, name, balanceInCents, lastUpdated FROM categories
	      	"""
	     ).as(fullCategoryParser *)
			categories
		}
	}

	def getPreffered()(implicit ec: ExecutionContext) : Future[List[Category]] = future {
		DB.withConnection { implicit connection => 
			//TODO: Change this to correct implementation when ready to implement category groups/preffereds
			val categories = SQL("""
	      	SELECT id, userId, name, balanceInCents, lastUpdated FROM categories
	      	"""
	     ).as(fullCategoryParser *)
			categories
		}	
	}

}