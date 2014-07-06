#ifndef __URLS_H_
	#define __URLS_H_
	/* Relys on config.h for the base urls */
	#include "config.h"

	#define HOME BASE_URL "/"
	#define BAD_LOGIN BASE_URL "/badlogin.html"
	#define APPLICATION BASE_URL "/welcome.html"
	#define REGISTER BASE_URL "/register.html"
	#define CREATE_ACCOUNT BASE_URL "/create-account.html"
	#define BAD_ACCOUNT BASE_URL "/failed-create-account.html"
#endif
