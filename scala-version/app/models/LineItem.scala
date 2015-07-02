package bgi.models

import java.text.NumberFormat
import org.joda.time.DateTime

/** Class representing an individual expendeture  
 * 
 * @param userId Id of the user to which this [[LineItem]] is from
 * @param name Name of the line item, a brief description
 * @param amountInCents The amount this item cost in cents
 * @param createdTime unix epoch timestamp of when this item was made
 */
case class LineItem(id: Long, userId: Long, categoryId: Long, name: String, amountInCents: Long, createdTime: Long) {
	def amountInDollars : String = {
		val numberFormat = NumberFormat.getCurrencyInstance()
		numberFormat.format(amountInCents/100.0)
	}

	def stringDate : String = {
		new DateTime(createdTime*1000).toLocalDate.toString
	}

}
