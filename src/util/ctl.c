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

/* Returns the user path (DATA_DIR/username) or NULL
*/
static char * _get_user_path(const char * username){
	if( username == NULL ) return NULL;

	/* Be warry of buffer overflow and try to protect against it */
	char buffer[BUFFER_LENGTH]; 
	char * accountPath;
	if(strlen(DATA_DIR) >= BUFFER_LENGTH) return NULL;
	accountPath = strcpy(buffer, DATA_DIR);

	if(strlen(accountPath) + strlen(username) >= BUFFER_LENGTH) return NULL;
	accountPath = strcat(accountPath,username);

	return accountPath;
}

static char * _get_users_accounts_path(const char * accountPath){
	if(accountPath == NULL) return NULL;
	/* +1 for the / we'll use in accessing */
	if(strlen(accountPath) + strlen(ACCOUNT_INDEX) +1 >= BUFFER_LENGTH) return NULL;
	char * accountsFile;
	char buffer[BUFFER_LENGTH];
	strcpy(buffer, accountPath);
	accountsFile = strcat(buffer, "/");
	accountsFile = strcat(buffer, ACCOUNT_INDEX);
	return accountsFile;
}

static char * _get_user_account_path(const char * accountPath, const char * accountName){
	char * accountFile;
	char buffer[BUFFER_LENGTH];
	strcpy(buffer, accountPath);
	accountFile = strcat(buffer, "/");
	if(strlen(accountFile) + strlen(accountName) >= BUFFER_LENGTH) return NULL;
	accountFile = strcat(buffer, accountName);

	return accountFile;
}

/* Will return a linked list of accounts stored in the accountChain,
 * the calling party is responsible for free-ing the resultant nodes.
 */
struct accountChain * read_accounts(const char * username){
	if(username == NULL) return NULL;
	if(_user_exists(username) != 1) return NULL;

	char * accountPath = _get_user_path(username);
	if(accountPath == NULL) return NULL;	

	char * accountsFile = _get_users_accounts_path(accountPath);
	if(accountsFile == NULL) return NULL;

	/* Open the file and construct the chain */
	FILE *fp = fopen(accountsFile, "r");
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountsFile);
		return NULL;
	}


	struct accountChain * chain = NULL;
	struct accountChain * backPtr = NULL;
	struct accountChain * head = NULL;
	chain = malloc(sizeof(struct accountChain));
	if(chain == NULL){
		fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
		return NULL; /* OUT OF MEMORY */	
	} 
	chain->next = NULL;

	/* Set the head so we can return them all linked together easily */
	head = chain;
	backPtr = head;	

	char accountName[64]; /* Account names are not allowed to be more than this*/
	int numAccount = 0;
	double balance = 0.00;
	while(fscanf(fp, "%d %s %lf\n", &numAccount, accountName, &balance) == 3){
		chain->data = malloc(sizeof(struct account));
		if(chain->data == NULL){
			fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
			fclose(fp);
			goto destroy_list;
		}
		chain->data->id = numAccount;
		strcpy(chain->data->name, accountName);
		chain->data->balance = balance;

		chain->next = malloc(sizeof(struct accountChain));
		if(chain->next == NULL){
			fprintf(stderr, "%s %s, line %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
			fclose(fp);
			goto destroy_list;
		}
		backPtr = chain;
		chain = chain->next;
	}
	/* We end up freeing one more item on the list than neccesary, so free that 
	 * up and NULL the ->next from the back pointer
	 */
	free(backPtr->next);
	backPtr->next = NULL;
	fclose(fp);

	return head;


	destroy_list:
	if(head != NULL){
		for (chain = head; chain != NULL; ){
			if(chain->data != NULL) free(chain->data);
			head = chain->next;
			free(chain);
			chain = head;
		}
	}
	return NULL;
}

int create_account(const char * username, const char * account){
	if( username == NULL || account == NULL) return 0;
	if(_user_exists(username) != 1) return 0;

	
	char * accountPath = _get_user_path(username);

	if(accountPath == NULL) return -1;
	if( _directory_exists(accountPath) != 1 ){
		/* Account directory does not exist. Make it. */
		if( _directory_create(accountPath) != 1 ){
			return 0;
		}
	}

	/* Open the account file and check if account already exists */
	if(strlen(accountPath) + strlen(ACCOUNT_INDEX) +1 >= BUFFER_LENGTH) return -1;
	char * accountsFile = _get_users_accounts_path(accountPath);
	
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
	char * accountFile = _get_user_account_path(accountPath, accountName);
	if(accountFile == NULL) return  -1;

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

int account_exists(const char * username, const char * account){
	char * accountPath = _get_user_path(username);
	if(accountPath == NULL) return 0;

	char * accountFile = _get_user_account_path(accountPath, account);
	if(accountFile == NULL) return 0;

	return _file_exists(accountFile);
}

int create_item(const char * username, const char * account, const char * name, double amount, double latitude, double longitude){
	if(_user_exists(username) != 1)	return 0;
	/* Create the path to the line items file for the account */

	char * accountPath = _get_user_path(username);

	if(accountPath == NULL) return 0;
	if( _directory_exists(accountPath) != 1 ){
		return 0;
	}

	char * accountFile = _get_user_account_path(accountPath, account);
	if(accountFile == NULL) return  0;

	if(_file_exists(accountFile) != 1){
		return 0;
	}

	FILE *fp = fopen(accountFile, "a");
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountFile);
		return 0;
	}
	fprintf(fp, "%zu %s %lf %lf %lf\n", time(0), name, amount, latitude, longitude);
	fclose(fp);



	return 1;	
}
