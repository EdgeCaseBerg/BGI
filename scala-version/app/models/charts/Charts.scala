package bgi.models.charts

import scala.math._

case class Pie(val radius : Int) {
	val portions = scala.collection.mutable.ListBuffer[Double]()

	def addPortion(portion: Double) {
		portions += portion
	}

	private def determineLargeArcFlag(portion: Double, total: Double) : Int = {
		if((portion / total) * 360 > 180) {
			1
		} else {
			0
		}
	}

	def getSvgArcs : List[String] = {
		val allPortions = portions.toList
		val total = allPortions.reduce { (l,r) => l + r }
		var prevX = radius.toDouble
		var prevY = 0.0
		var arcLength = 0.0
		val paths = scala.collection.mutable.ListBuffer[String]()
		for(portion <- allPortions) {
			var largeArcFlag = determineLargeArcFlag(portion, total)
			arcLength = (portion/total) * 360 + arcLength
			val nextX = cos(arcLength.toRadians) * radius
			val nextY = sin(arcLength.toRadians) * radius
			paths += s"l ${prevX} ${-prevY} a ${radius} ${radius} 0, ${largeArcFlag} 0 ${nextX - prevX} ${-(nextY - prevY)} z"
			prevX = nextX
			prevY = nextY
		}
		paths.toList
	}
}