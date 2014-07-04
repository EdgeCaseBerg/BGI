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

int _file_exists(const char * filename){
	/* Security Concern: If you check for a file's existence and then open the 
	 * file, between the time of access checking and creation of a file someone
	 * can create a symlink or something and cause your open to fail or open 
	 * something that shouldn't be opened. That being said... I'm not concerned.
	*/
	struct stat buffer;
	return(stat (filename, &buffer) == 0);
}

int init(){
	int success = _directory_exists(DATA_DIR);
	if(success < 0){
		fprintf(stderr, "%s\n", FAILED_INIT);
		return 0; /* Unknown Failure. Panic. */
	}
	if(success != 1){
		/* DATA_DIR does not exist. Create it */
	}
	
}

int create_user(const char * username, const char * hashpass){

}