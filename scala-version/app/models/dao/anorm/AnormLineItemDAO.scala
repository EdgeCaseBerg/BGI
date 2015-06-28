package bgi.models.dao.anorm

import bgi.models.dao.LineItemDAO
import bgi.models._

import play.api.db._
import play.api.Play.current
 
import anorm._
import anorm.SqlParser._

import scala.concurrent.{ExecutionContext, Future, future}
import scala.language.postfixOps

/** Class representing DAO layer for LineItems using Anorm
 * 
 * Implementation of LineItemDAO, uses the anorm library to speak to 
 * RDBMS style databases
 */
class AnormLineItemDAO extends LineItemDAO{
	/** Simple Anorm Mapper for a LineItem row */
	val fullLineItemParser = {
		get[Long]("id") ~
		get[Long]("userId") ~
		get[String]("name") ~ 
		get[Long]("amountInCents") ~ 
		get[Long]("createdTime") map {
			case id ~ userId ~ name ~ amountInCents ~ createdTime => 
				LineItem(id, userId, name, amountInCents, createdTime)
		}
	}

	def create(lineItem: LineItem)(implicit ec: ExecutionContext) : Future[Option[LineItem]] = {
		val newId : Option[Long] = DB.withConnection { implicit connection =>
      		SQL("""
      			INSERT INTO lineitems (name, userId, amountInCents, createdTime) 
      			VALUES ({name}, {userId}, {amountInCents}, {createdTime})
      			"""
      		).on(
        		"name" -> lineItem.name,
        		"userId" -> lineItem.userId,
        		"amountInCents" -> lineItem.amountInCents,
        		"createdTime" -> lineItem.createdTime
      		).executeInsert()
    	}
    	newId.fold( 
    		/* Silly, but we need to specify the type or erasure will stop compilation */
    		future { val o : Option[LineItem] = None; o } 
    	) { id =>
	    	findById(id)
	    }
	}	

	def findById(id: Long)(implicit ec: ExecutionContext): Future[Option[LineItem]] = future {
		DB.withConnection { implicit connection =>
			val optionLineItem : Option[LineItem] = SQL("""
	      		SELECT id, userId, name, amountInCents, createdTime FROM lineitems 
	      		WHERE id = {id}
	      		"""
	      		).on("id" -> id).as(fullLineItemParser *).headOption
      		optionLineItem
		}
	}

	def remove(id: Long)(implicit ec : ExecutionContext): Future[Boolean] = future {
		DB.withConnection { implicit connection => 
			val numberEffected = SQL("""
				DELETE FROM lineitems WHERE id = {id}
				"""
				).on("id" -> id).executeUpdate()
			numberEffected match {
				case 0 => false
				case _ => true
			}
		}
	}

	def update(lineItem: LineItem)(implicit ec: ExecutionContext): Future[Boolean] = future {
		DB.withConnection { implicit connection =>
			val numberEffected = SQL("""
				UPDATE lineitems SET 
					name = {name}, 
					userId = {userId}, 
					amountInCents = {amountInCents},
					WHERE id = {id}
				""").on(
					"name" -> lineItem.name,
		        	"userId" -> lineItem.userId,
		        	"amountInCents" -> lineItem.amountInCents,
		        	"id" -> lineItem.id
				).executeUpdate()
			numberEffected match {
				case 0 => false
				case _ => true
			}
		}
	}

	def findAllByCategories(categories: List[Long])(implicit ec: ExecutionContext): Future[List[LineItem]] = future {
		DB.withConnection { implicit connection => 
			SQL("""
					SELECT id, userId, name, amountInCents, createdTime FROM lineitems 
					JOIN lineitem_categories 
					ON lineitemId = id AND categoryId in ({categories})
	      	"""
	     ).on("categories" -> categories.mkString(",")).as(fullLineItemParser *)
		}
	}

	def findAllInPeriod(startEpoch: Long,endEpoch: Option[Long])(implicit ec: ExecutionContext): Future[List[LineItem]] = future {
		val endTime = endEpoch.getOrElse("UNIX_TIMESTAMP(UTC_TIMESTAMP())").toString
		DB.withConnection { implicit connection =>
			SQL("""
				SELECT id, userId, name, amountInCents, createdTime FROM lineitems 
				WHERE createdTime BETWEEN {startEpoch} AND {endTime}
				"""
			).on(
				"startEpoch" -> startEpoch,
				"endTime" -> endTime
			).as(fullLineItemParser *)
		}
	}

	def findAllInPeriodMatchingCategories(categories: List[Long],startEpoch: Long,endEpoch: Option[Long])(implicit ec: ExecutionContext): Future[List[LineItem]] = future {
		val endTime = endEpoch.getOrElse("UNIX_TIMESTAMP(UTC_TIMESTAMP())").toString
		DB.withConnection { implicit connection =>
			SQL("""
				SELECT id, userId, name, amountInCents, createdTime FROM lineitems 
				JOIN lineitem_categories 
				ON lineitemId = id AND categoryId in ({categories})
				WHERE createdTime BETWEEN {startEpoch} AND {endTime}
				"""
			).on(
				"categories" -> categories.mkString(","),
				"startEpoch" -> startEpoch,
				"endTime" -> endTime
			).as(fullLineItemParser *)
		}	
	}

}