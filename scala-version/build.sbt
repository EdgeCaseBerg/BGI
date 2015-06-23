name := "bgi"
 
version := "0.1"
 
scalaVersion := "2.10.4"
 
scalacOptions := Seq("-unchecked", "-deprecation", "-encoding", "utf8", "-feature")

libraryDependencies ++= Seq(
  	"ch.qos.logback" % "logback-classic" % "1.0.12",
  	"org.scalatest" %% "scalatest" % "2.2.1" % "test",
 	"org.scalatestplus" %% "play" % "1.2.0" % "test",
	"jp.t2v" %% "play2-auth"      % "0.13.2",
	"jp.t2v" %% "play2-auth-test" % "0.13.2" % "test",
	"org.mindrot"  % "jbcrypt"   % "0.3m",
	"mysql" % "mysql-connector-java" % "5.1.18"
  )

lazy val root = (project in file("."))
	.enablePlugins(PlayScala,SbtWeb)

libraryDependencies += javaJdbc

includeFilter in (Assets, LessKeys.less) := "*.less"

excludeFilter in (Assets, LessKeys.less) := "_*.less"

LessKeys.compress := true