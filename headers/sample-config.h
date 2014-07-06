#ifndef __CONFIG_H__
#define __CONFIG_H__

/*  Data Layer Constants */
#define DATA_DIR "/home/guest/bgi/data/"
#define DATA_DIR_PERM 0755 
#define USERS_INDEX ".users"
#define ACCOUNT_INDEX "accounts"
#define ACCOUNT_EXT ".account"	
#define BUFFER_LENGTH 256

/*  CGI Layer Constants */
#define SESSION_TIME 1800 /* Half Hour */
#define BASE_URL "http://www.bgi.test"
#define ADMIN_SECRET "admin"

#endif
