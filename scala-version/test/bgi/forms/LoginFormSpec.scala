package bgi.forms

import org.scalatest._
import org.mindrot.jbcrypt.BCrypt

class LoginFormSpec extends FlatSpec {

	val valid = LoginForm("username","password")
	val usernameTooShort = LoginForm("u","password")
	val usernameTooLong = LoginForm("u" * 129, "password")
	val usernameEmpty = LoginForm("", "password")
	val passwordTooShort = LoginForm("username", "p")
	val passwordTooLong = LoginForm("username", "p" * 257)
	val passwordEmpty = LoginForm("username", "")

	
	"The LoginForm" should "validate when given proper input" in {		
		assert(!LoginForm.form.fillAndValidate(valid).hasErrors)
	}

	it should "not allow short usernames" in {
		assert(LoginForm.form.fillAndValidate(usernameTooShort).hasErrors)	
	}

	it should "not allow long usernames" in {
		assert(LoginForm.form.fillAndValidate(usernameTooLong).hasErrors)	
	}

	it should "not allow empty usernames" in {
		assert(LoginForm.form.fillAndValidate(usernameEmpty).hasErrors)	
	}

	it should "not allow short passwords" in {
		assert(LoginForm.form.fillAndValidate(passwordTooShort).hasErrors)	
	}

	it should "not allow long passwords" in {
		assert(LoginForm.form.fillAndValidate(passwordTooLong).hasErrors)	
	}

	it should "not allow empty passwords" in {
		assert(LoginForm.form.fillAndValidate(passwordEmpty).hasErrors)	
	}
		
}