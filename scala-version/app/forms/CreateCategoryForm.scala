package bgi.forms

import play.api.data._
import play.api.data.Forms._

/** Container for the category creation form 
 * 
 * @param name The submitted category name, a string length 3-64
 * @param userId The id of the User to create the Category for
 */
case class CreateCategoryForm(name: String) 

/** Companion object of the category creation form 
 *
 * Provides a form object to bind requests and validate form input
 */
object CreateCategoryForm {
	val form = Form(
		mapping(
			"name" -> nonEmptyText(minLength = 3, maxLength = 64)
		)(CreateCategoryForm.apply)(CreateCategoryForm.unapply)
	)
}
