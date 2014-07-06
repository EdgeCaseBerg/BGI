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

/*1 = truth, 0 = false, -1 = error */
int _user_exists(const char * username){
	FILE *fp = fopen(DATA_DIR USERS_INDEX, "r");
	if(!fp){
		fprintf(stderr, "%s\n", FAILED_FILE_OPEN USERS_INDEX);
		return -1;
	}
	/* Read and find the user */
	char user[64];
	while(fscanf(fp,"%s %*"PRIu32"\r\n",user) == 1){
		if(strncmp(user, username, 64) == 0){
			fclose(fp);
			return 1;
		}
	}
	fclose(fp);
	return 0;
}

int _password_matches(const char * username, const uint32_t hashpass){
	if( username == NULL){
		return 0;
	}

	FILE *fp = fopen(DATA_DIR USERS_INDEX, "r");
	if(!fp){
		fprintf(stderr, "%s\n", FAILED_FILE_OPEN USERS_INDEX);
		return 0;
	}

	char user[64];
	uint32_t pass;
	while(fscanf(fp,"%s %"PRIu32"\r\n",user,&pass) == 2){
		if(strncmp(user, username, 64) == 0){
			if(hashpass == pass){
				fclose(fp);
				return 1;
			}
			fclose(fp);
			return 0;
		}
	}
	return 0;
}

int create_user(const char * username, const uint32_t hashpass){
	/* silence compiler for now */
	if( username == NULL  )
		return 0;

	/* Make sure user/pass within constraints */
	if(strlen(username) >= 64){
		return 0;
	}

	/* Scan the Users file to determine if this user exists */
	if( _user_exists(username) != 0 ){ /* returns on err too! */
		return 0;
	}

	/* Create the user since they don't exist */
	FILE *fp = fopen(DATA_DIR USERS_INDEX, "a");
	if(!fp){
		fprintf(stderr, "%s\n", FAILED_FILE_OPEN DATA_DIR USERS_INDEX);
		return 0;
	}

	fprintf(fp, "%s %"PRIu32"\r\n", username, hashpass);
	fclose(fp);
	return 1;
}

int create_account(const char * username, const char * account){
	if( username == NULL || account == NULL) return 0;
	if(_user_exists(username) != 1) return 0;

	/* Be warry of buffer overflow and try to protect against it */
	char buffer[BUFFER_LENGTH]; 
	char * accountPath;
	if(strlen(DATA_DIR) >= BUFFER_LENGTH) return -1;
	accountPath = strcpy(buffer, DATA_DIR);

	
	if(strlen(accountPath) + strlen(username) >= BUFFER_LENGTH) return -1;
	accountPath = strcat(accountPath,username);

	if( _directory_exists(accountPath) != 1 ){
		/* Account directory does not exist. Make it. */
		if( _directory_create(accountPath) != 1 ){
			return 0;
		}
	}

	/* Open the account file and check if account already exists */
	/* +1 for the / we'll use in accessing */
	if(strlen(accountPath) + strlen(ACCOUNT_INDEX) +1 >= BUFFER_LENGTH) return -1;
	char * accountsFile;
	char buffer2[BUFFER_LENGTH];
	strcpy(buffer2, accountPath);
	accountsFile = strcat(buffer2, "/");
	accountsFile = strcat(buffer2, ACCOUNT_INDEX);

	/* Open in a+ so we'll read from beginning and write to end */
	FILE *fp = fopen(accountsFile, "a+"); 
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountsFile );
		return -1;
	}

	char accountName[64]; /* Account names are not allowed to be more than this*/
	int numAccount = 0;
	double balance = 0.00;
	int exists = 0;
	while(fscanf(fp, "%d %s %lf\n", &numAccount, accountName, &balance) == 3){
		if(strncmp(accountName, account, 64) == 0){
			/* Account already exists! */
			exists = 1;
			break;
		}
	}

	strncpy(accountName, account, sizeof(accountName));
	accountName[sizeof(accountName) -1] = '\0';
	if(exists == 0){
		fprintf(fp, "%d %s %.2lf\n", numAccount+1,  accountName, 0.00 );
	}
	fclose(fp);

	/* Account has been written to the account index file, 
	 * now create the storage place for the acount line items */
	char * accountFile;
	char buffer3[BUFFER_LENGTH];
	strcpy(buffer3, accountPath);
	accountFile = strcat(buffer3, "/");
	if(strlen(accountFile) + strlen(accountName) >= BUFFER_LENGTH) return -1;
	accountFile = strcat(buffer3, accountName);

	if(_file_exists(accountFile) == 0){
		FILE *fp = fopen(accountFile, "w");
		if(!fp){
			fprintf(stderr, "%s %s\n", FAILED_FILE_CREATION, accountFile);
			return 0;
		}
		fclose(fp);
	}

	return 1;
}


/*
int create_item(const char * username, const char * account, const char * name, long amount, category cat, long latitude, long longitude){
	if(_user_exists(username != 1))	return 0;
*/
	/* Create the path to the line items file for the account */
/*

	return 0;	
}
*/