package bgi.models.pagedata

import bgi.models._
import bgi.models.charts._

/** Data for the Dashboard page, used to construct charts and ledger 
 * 
 * @note At a future date, might refactor preferredCategories to a List of Lists
 *
 * @param recentItems A list of recent line items
 * @param categories all categories, used for the create line item form
 * @param charts charts to display on the view. 
 */
case class DashboardPageData(recentItems: List[LineItem], categories: List[Category], charts: List[Pie])