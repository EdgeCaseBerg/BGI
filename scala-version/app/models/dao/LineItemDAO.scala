package bgi.models.dao

import bgi.models._

import scala.concurrent.{Future, ExecutionContext}

/** LineItem specific DAO operations 
 * 
 * All functions take an implicit ExecutionContext which is used to 
 * run the future threads in. 
 */
trait LineItemDAO extends CrudDAO[LineItem] {
	type LineItems = List[LineItem]

	/** Retrieves all LineItems belonging in a given tag
	 *
	 * @param categoryId The Id of the tag to find by
	 * @param ec Implicit ExecutionContext to run future threads in
	 * @return A Future containing a list of LineItems matching the tag
	 */
	def findAllByCategory(categoryId: Long)(implicit ec: ExecutionContext) : Future[LineItems] = findAllByCategories(List(categoryId))

	/** Retrieves all LineItems belonging all categories given
	 * 
	 * @param categories A list of tag ID's
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of LineItems matching the categories provided
	 */
	def findAllByCategories(categories: List[Long])(implicit ec: ExecutionContext) : Future[LineItems]

	/** Returns all LineItems in a given date range
	 *
	 * Useful for constructing a list of most recent items
	 *
	 * @param startEpoch The bottom of the date range in UTC Epoch
	 * @param endEpoch The end of the date range in UTC Epoch, or None for now
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of LineItems within the date range provided
	 */
	def findAllInPeriod(startEpoch: Long, endEpoch: Option[Long] = None)(implicit ec: ExecutionContext) : Future[LineItems]

	/** Returns all LineItems within a period of time matching the given categories
	 * 
	 * @param categories A list of tag ID's 
	 * @param startEpoch The bottom of the date range in UTC Epoch
	 * @param endEpoch The end of the date range in UTC Epoch, or None for now
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of LineItems within the date range provided
	 */
	 def findAllInPeriodMatchingCategories(categories: List[Long], startEpoch: Long, endEpoch: Option[Long] = None)(implicit ec :ExecutionContext) : Future[LineItems]

}
