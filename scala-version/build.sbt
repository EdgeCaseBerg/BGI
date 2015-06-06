name := "bgi"
 
version := "0.1"
 
scalaVersion := "2.10.4"
 
scalacOptions := Seq("-unchecked", "-deprecation", "-encoding", "utf8", "-feature")

libraryDependencies ++= Seq(
  	"ch.qos.logback" % "logback-classic" % "1.0.12",
  	"org.scalatest" %% "scalatest" % "2.2.1" % "test",
 	"org.scalatestplus" %% "play" % "1.2.0" % "test"
  )

lazy val root = (project in file("."))
	.enablePlugins(PlayScala,SbtWeb)
