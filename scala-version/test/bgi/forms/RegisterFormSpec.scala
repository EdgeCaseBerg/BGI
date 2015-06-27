package bgi.forms

import org.scalatest._
import org.mindrot.jbcrypt.BCrypt

class RegisterFormSpec extends FlatSpec {

	val valid = RegisterForm("username","password", "TEST")
	val usernameTooShort = RegisterForm("u","password", "TEST")
	val usernameTooLong = RegisterForm("u" * 129, "password", "TEST")
	val usernameEmpty = RegisterForm("", "password", "TEST")
	val passwordTooShort = RegisterForm("username", "p", "TEST")
	val passwordTooLong = RegisterForm("username", "p" * 257, "TEST")
	val passwordEmpty = RegisterForm("username", "", "TEST")
	val signupcodeEmpty = RegisterForm("username", "password", "")
	val signupcodeIncorrect = RegisterForm("username", "password", "invalid")

	
	"The RegisterForm" should "validate when given proper input" in {		
		assert(!RegisterForm.form.fillAndValidate(valid).hasErrors)
	}

	it should "not allow short usernames" in {
		assert(RegisterForm.form.fillAndValidate(usernameTooShort).hasErrors)	
	}

	it should "not allow long usernames" in {
		assert(RegisterForm.form.fillAndValidate(usernameTooLong).hasErrors)	
	}

	it should "not allow empty usernames" in {
		assert(RegisterForm.form.fillAndValidate(usernameEmpty).hasErrors)	
	}

	it should "not allow short passwords" in {
		assert(RegisterForm.form.fillAndValidate(passwordTooShort).hasErrors)	
	}

	it should "not allow long passwords" in {
		assert(RegisterForm.form.fillAndValidate(passwordTooLong).hasErrors)	
	}

	it should "not allow empty passwords" in {
		assert(RegisterForm.form.fillAndValidate(passwordEmpty).hasErrors)	
	}

	it should "not allow empty signupcodes" in {
		assert(RegisterForm.form.fillAndValidate(signupcodeEmpty).hasErrors)	
	}

	it should "not allow registration if the signupcode is wrong" in {
		assert(RegisterForm.form.fillAndValidate(signupcodeIncorrect).hasErrors)	
	}
		
}