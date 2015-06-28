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

	/** Get line items that have occured in the current month */
	def findInThisMonth : Future[LineItems] = {
		val startTime = clearDayMinuteSecond(DateTime.now).millis/1000
		lineItemDAO.findAllInPeriod(startTime)
	}

	/** Get line items that have occured in the given year
	 * 
	 * @param year An option containing the year that line items must have occured in, or None for the current year
	 */
	def findAllInYear(year: Option[Int] = None) : Future[LineItems] = {
		val theYear = year.getOrElse(clearDayMinuteSecond(DateTime.now).year.getAsText.toInt)
		val startTime = clearDayMinuteSecond(DateTime.now)
			.monthOfYear().withMinimumValue().withYear(theYear)
		val endTime = (startTime + 1.years).millis/1000
		lineItemDAO.findAllInPeriod(startTime.millis/1000, Some(endTime))
	}

	/** Returns all line items */
	def findAll : Future[LineItems] = lineItemDAO.findAllInPeriod(0)

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