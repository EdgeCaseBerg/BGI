package bgi.models.charts

import scala.math._
import bgi.models._

trait Chartable {
	def name : String = ???
	def nameToFill : String = {
		Charts.nameToFill(name)
	}
}

object Charts {
	def nameToFill(name: String) : String = {
		val nameVal = name.map(_.toInt).reduceLeft((l,r) => l + r)
		"#" + "%06x".format((nameVal*0xf24) % 0xffffff).replace("0","f")
	}
}

case class Portion(val amount : Double, override val name: String) extends Chartable

case class Pie(val radius : Int = 80) {
	val portions = scala.collection.mutable.ListBuffer[Portion]()

	def addPortion(portion: Portion) {
		portions += portion
	}

	private def determineLargeArcFlag(portion: Portion, total: Double) : Int = {
		val v = (portion.amount/total) * 360
		v match {
			case _ if v > 180 => 1
			case _ => 0
		}
	}

	def getColors : List[(String, String)] = {
		portions.toList.map(p => (p.name, p.nameToFill))
	}

	def getSvgArcs : List[(String,String)] = {
		val allPortions = portions.toList
		val total = allPortions.foldLeft(0.0) { (l,r) => l + r.amount }
		var prevX = radius.toDouble
		var prevY = 0.0
		var arcLength = 0.0
		val paths = scala.collection.mutable.ListBuffer[(String, String)]()
		for(portion <- allPortions) {
			var largeArcFlag = determineLargeArcFlag(portion, total)
			arcLength = (portion.amount/total) * 360 + arcLength
			val nextX = cos(arcLength.toRadians) * radius
			val nextY = sin(arcLength.toRadians) * radius
			val color = portion.nameToFill
			paths += Tuple2(s"l ${prevX} ${-prevY} a ${radius} ${radius} 0, ${largeArcFlag} 0 ${nextX - prevX} ${-(nextY - prevY)} z", color)
			prevX = nextX
			prevY = nextY
		}
		paths.toList
	}
}