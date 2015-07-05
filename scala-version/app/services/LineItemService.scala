package bgi.services

import bgi.models._
import bgi.models.dao.LineItemDAO

import org.mindrot.jbcrypt.BCrypt
import org.scala_tools.time.Imports._

import scala.concurrent.{Future, future}
import scala.concurrent.ExecutionContext.Implicits.global


/** Service abstraction over operations involving the LineItems
 *
 * Provides layer over basic DAO and ability to perform business logic
 * surrounding the lineItem object.
 */
class LineItemService(implicit val lineItemDAO : LineItemDAO) extends BaseService[LineItem]{
	type LineItems = List[LineItem]
	
	/** Wrapper around lineItemDAO method
	 * 
	 * @see [[bgi.models.dao.LineItemDAO]]
	 */
	def findAllByCategory(category: Long) : Future[LineItems] = lineItemDAO.findAllByCategory(category)

	/** Wrapper around lineItemDAO method
	 * 
	 * @see [[bgi.models.dao.LineItemDAO]]
	 */
	def findAllByCategories(categories: List[Long]) : Future[LineItems] = lineItemDAO.findAllByCategories(categories)

	/** Helper method for modifying dates and periods */
	private def clearDayMinuteSecond(d: DateTime) = {
		d.withZone(DateTimeZone.UTC)
			.dayOfMonth().withMinimumValue()
			.minuteOfDay().withMinimumValue()
			.secondOfMinute().withMinimumValue()
			.millisOfSecond().withMinimumValue()
	}

	/** Get line items that have occured in the current month for a User
	 * 
	 * @param user The User to whom the line items belong
	 * @return A Future containing the LineItems of this month belonging to the User
	 */
	def findInThisMonth(user: User) : Future[LineItems] = {
		val startTime = clearDayMinuteSecond(DateTime.now).millis/1000
		lineItemDAO.findAllInPeriodForUser(user, startTime)
	}

	/** Get line items that have occured in the given year for a user
	 * 
	 * @param user The user who owns the line items.
	 * @param year An option containing the year that line items must have occured in, or None for the current year
	 */
	def findAllInYear(user: User, year: Option[Int] = None) : Future[LineItems] = {
		val theYear = year.getOrElse(clearDayMinuteSecond(DateTime.now).year.getAsText.toInt)
		val startTime = clearDayMinuteSecond(DateTime.now)
			.monthOfYear().withMinimumValue().withYear(theYear)
		val endTime = (startTime + 1.years).millis/1000
		lineItemDAO.findAllInPeriodForUser(user, startTime.millis/1000, Some(endTime))
	}

	/** Returns all line items */
	def findAllForUser(user: User) : Future[LineItems] = lineItemDAO.findAllInPeriodForUser(user, 0)

	/** Returns line items in the given year that match the specified categories
	 *
	 * @param categories The id's of the categories line items must match
	 * @param year An option containing the year that line items must have occured in, or None for the current year
	 */
	def findAllInYearWithCategories(categories: List[Long], year: Option[Int] = None) : Future[LineItems] = {
		val theYear = year.getOrElse(clearDayMinuteSecond(DateTime.now).year.getAsText.toInt)
		val startTime = clearDayMinuteSecond(DateTime.now)
			.monthOfYear().withMinimumValue().withYear(theYear)
		val endTime = (startTime + 1.years).millis/1000
		lineItemDAO.findAllInPeriodMatchingCategories(categories, startTime.millis/1000, Some(endTime))
	}

	
}