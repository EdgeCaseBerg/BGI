package bgi.models.pagedata

import bgi.models._

/** Data for the Dashboard page, used to construct charts and ledger 
 * 
 * @note At a future date, might refactor preferredCategories to a List of Lists
 *
 * @param recentItems A list of recent line items
 * @param preferredCategories a subset of all categories that the user wants to see a chart of
 */
case class DashboardPageData(recentItems: List[LineItem], preferredCategories: List[Category])