#include "util/ctl.h"

int _directory_exists(const char * directoryToCheck){
	DIR * dir;
	int success;

	dir = opendir(directoryToCheck);
	if ( dir ) {
		closedir(dir);
		success = 1;
	} else if ( ENOENT == errno ) success = 0; /* The directory doesn't exist */
	else success = -1;/* Something went wrong with opening it */

	return success;
}

int _directory_create(const char * directoryToCheck){
	return mkdir(directoryToCheck, DIR_PERM);
}

int _file_exists(const char * filename){
	/* Security Concern: If you check for a file's existence and then open the 
	 * file, between the time of access checking and creation of a file someone
	 * can create a symlink or something and cause your open to fail or open 
	 * something that shouldn't be opened. That being said... I'm not concerned.
	*/
	struct stat buffer;
	return(stat (filename, &buffer) == 0);
}

int bgi_data_init(){
	int success = _directory_exists(DATA_DIR);
	if(success < 0){
		fprintf(stderr, "%s\n", FAILED_INIT FAILED_DIR_EXISTS DATA_DIR);
		return 0; /* Unknown Failure. Panic. */
	}
	if(success != 1){
		/* DATA_DIR does not exist. Create it */
		success = _directory_create(DATA_DIR);
		if (success == -1){
			fprintf(stderr,"%s\n", FAILED_INIT FAILED_DIR_CREATION DATA_DIR);
			return 0;
		}
	}

	success = _file_exists( DATA_DIR USERS_INDEX);
	if(success == 0){
		/* USERS_INDEX does not exist. create it */
		FILE *fp = fopen(DATA_DIR USERS_INDEX, "wb");
		if (!fp) {
    		success = 0;
			fprintf(stderr,"%s\n", FAILED_INIT FAILED_FILE_CREATION);
    	} else {
    		fclose(fp);
    		success = 1;
    	}
	}
	return success;
}

int create_user(const char * username, const char * hashpass){
	//silence compiler for now
	(void)username;
	(void)hashpass;
	return 0;
}