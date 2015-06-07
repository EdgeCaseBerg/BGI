package bgi.models

/** Class representing an individual expendeture  
 * 
 * @param userId Id of the user to which this [[Lineitem]] is from
 * @param name Name of the line item, a brief description
 * @param amountInCents The amount this item cost in cents
 * @param createdTime unix epoch timestamp of when this item was made
 */
case class LineItem(userId: Long, name: String, amountInCents: Long, createdTime: Long)
