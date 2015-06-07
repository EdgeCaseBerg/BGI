package bgi.models

/** Class representing a category 
 * 
 * Categories have a Many to Many relationship with [[LineItem]]s. While the previous 
 * implementation of bgi only allowed for a single category for each [[LineItem]], 
 * this one will allow for multiple. This is to allow for more interesting data
 * and charts. 
 * 
 * @param userId The id identifying which user has created this Category
 * @param name A string describing this category
 * @param balanceInCents The total balance of line items within this category
 * @param lastUpdated The time which the balance was last updated for this category
 */
case class Category(userId : Long, name: String, balanceInCents: Long, lastUpdated: Long)