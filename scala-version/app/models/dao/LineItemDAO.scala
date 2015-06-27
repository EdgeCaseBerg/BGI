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
	 * @param tagId The Id of the tag to find by
	 * @param ec Implicit ExecutionContext to run future threads in
	 * @return A Future containing a list of LineItems matching the tag
	 */
	def findAllByTag(tagId: Long)(implicit ec: ExecutionContext) : Future[LineItems] = findAllByTags(List(tagId))

	/** Retrieves all LineItems belonging all tags given
	 * 
	 * @param tags A list of tag ID's
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of LineItems matching the tags provided
	 */
	def findAllByTags(tags: List[Long])(implicit ec: ExecutionContext) : Future[LineItems]

	/** Returns all LineItems in a given date range
	 *
	 * Useful for constructing a list of most recent items
	 *
	 * @param startEpoch The bottom of the date range in UTC Epoch
	 * @param endEpoch The end of the date range in UTC Epoch, or None for now
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of LineItems within the date range provided
	 */
	def findAllInPeriod(startEpoch: Long, endEpoch: Option[Long])(implicit ec: ExecutionContext) : Future[LineItems]

	/** Returns all LineItems within a period of time matching the given tags
	 * 
	 * @param tags A list of tag ID's 
	 * @param startEpoch The bottom of the date range in UTC Epoch
	 * @param endEpoch The end of the date range in UTC Epoch, or None for now
	 * @param ec Implicit ExecutionContext to run function threads in
	 * @return A Future containing a list of LineItems within the date range provided
	 */
	 def findAllInPeriodMatchingTags(tags: List[Long], startEpoch: Long, endEpoch: Option[Long])(implicit ec :ExecutionContext) : Future[LineItems]

}
