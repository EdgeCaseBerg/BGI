import org.scalatest._

import org.mindrot.jbcrypt.BCrypt
import bgi.models._

class UserSpec extends FlatSpec {

	def identity(arg: Int) : Int = arg
	
	"An implicit complexity conversion in scope" should "convert Complexities to Integers" in {		
		val s = BCrypt.hashpw("password", BCrypt.gensalt(UserPasswordComplexity.Normal));
		assert(!s.isEmpty)
	}

	it should "use the correct work factor for each value" in {
		val values = List(10,12,15).zip(UserPasswordComplexity.values)
		for((integerValue, complexity) <- values) {
			assertResult(integerValue) {
				identity(complexity)
			}
		}
	}

	"The same password" should "hash differently for different Complexities" in {
		val set = scala.collection.immutable.Set(
			BCrypt.hashpw("password", BCrypt.gensalt(UserPasswordComplexity.Normal)),
			BCrypt.hashpw("password", BCrypt.gensalt(UserPasswordComplexity.Difficult)),
			BCrypt.hashpw("password", BCrypt.gensalt(UserPasswordComplexity.Hard))
		)
		assert(set.size == 3)
	}
		
}