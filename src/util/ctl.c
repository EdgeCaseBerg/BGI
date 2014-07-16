#include "util/ctl.h"

int _directory_exists(const char * directoryToCheck){
	DIR * dir = NULL;
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
	while(fscanf(fp,"%64s %*"PRIu32"\r\n",user) == 1){
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
	while(fscanf(fp,"%64s %"PRIu32"\r\n",user,&pass) == 2){
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
char * _get_user_path(const char * username){
	if( username == NULL ) return NULL;

	/* Be warry of buffer overflow and try to protect against it */
	
	if(strlen(DATA_DIR) >= BUFFER_LENGTH) return NULL;
	char * accountPath = malloc(sizeof(char) *BUFFER_LENGTH);
	accountPath = memcpy(accountPath, DATA_DIR, BUFFER_LENGTH);
	if(strlen(accountPath) + strlen(username) >= BUFFER_LENGTH){
		free(accountPath);
		return NULL;	
	} 
	accountPath = strcat(accountPath,username);
	return accountPath;
}

char * _get_users_accounts_path(const char * accountPath){
	if(accountPath == NULL) return NULL;
	/* +1 for the / we'll use in accessing */
	if(strlen(accountPath) + strlen(ACCOUNT_INDEX) +1 >= BUFFER_LENGTH) return NULL;
	
	char * accountsFile = malloc(sizeof(char) *BUFFER_LENGTH);
	if(accountsFile == NULL) return NULL;
	accountsFile = memcpy(accountsFile,accountPath,BUFFER_LENGTH);
	
	accountsFile = strcat(accountsFile, "/");
	accountsFile = strcat(accountsFile, ACCOUNT_INDEX);
	return accountsFile;
}

char * _get_user_account_path(const char * accountPath, const char * accountName){
	if(strlen(accountPath) + strlen(accountName) >= BUFFER_LENGTH) return NULL;

	char * accountFile = malloc(sizeof(char) * BUFFER_LENGTH);
	if(accountFile == NULL) return NULL;

	accountFile = memcpy(accountFile, accountPath, BUFFER_LENGTH);
	accountFile = strcat(accountFile, "/");
	accountFile = strcat(accountFile, accountName);

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
	free(accountPath);
	if(accountsFile == NULL){
		return NULL;	
	} 

	/* Open the file and construct the chain */
	FILE *fp = fopen(accountsFile, "r");
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountsFile);
		free(accountsFile);
		return NULL;
	}
	free(accountsFile);

	
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
	bzero(accountName, 64);
	int numAccount = 0;
	double balance = 0.00;
	while(fscanf(fp, "%d %64s %lf\n", &numAccount, accountName, &balance) == 3){
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
			free(accountPath);
			return 0;
		}
	}

	/* Open the account file and check if account already exists */
	if(strlen(accountPath) + strlen(ACCOUNT_INDEX) +1 >= BUFFER_LENGTH){
		free(accountPath);
		return -1;
	}
		
	char * accountsFile = _get_users_accounts_path(accountPath);
	
	/* Open in a+ so we'll read from beginning and write to end */
	FILE *fp = fopen(accountsFile, "a+"); 
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountsFile );
		free(accountPath);
		free(accountsFile);
		return -1;
	}
	free(accountsFile);

	char accountName[64]; /* Account names are not allowed to be more than this*/
	int numAccount = 0;
	double balance = 0.00;
	int exists = 0;
	while(fscanf(fp, "%d %64s %lf\n", &numAccount, accountName, &balance) == 3){
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
	free(accountPath);
	if(accountFile == NULL){
		return  -1;	
	} 

	if(_file_exists(accountFile) == 0){
		FILE *fp = fopen(accountFile, "w");
		if(!fp){
			fprintf(stderr, "%s %s\n", FAILED_FILE_CREATION, accountFile);
			free(accountFile);
			return 0;
		}
		fclose(fp);
	}
	free(accountFile);

	return 1;
}

int account_exists(const char * username, const char * account){
	char * accountPath = _get_user_path(username);
	if(accountPath == NULL) return 0;

	char * accountFile = _get_user_account_path(accountPath, account);
	free(accountPath);
	if(accountFile == NULL) return 0;

	int exists =  _file_exists(accountFile);
	free(accountFile);
	return exists;
}

/* 	Generate a random string name for our tmp file 
 *	http://stackoverflow.com/a/440240/1808164
*/
static void gen_random(char *s, const int len) {
    static const char alphanum[] =
        "0123456789"
        "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
        "abcdefghijklmnopqrstuvwxyz";

    for (int i = 0; i < len; ++i) {
        s[i] = alphanum[rand() % (sizeof(alphanum) - 1)];
    }

    s[len] = 0;
}

int update_account_balance(const char * username, const char * accountName , double additionToAccount){
	if(_user_exists(username) != 1) return 0;
	if(accountName == NULL) return 0;

	char * accountPath = _get_user_path(username);

	if(accountPath == NULL) return -1;
	if( _directory_exists(accountPath) != 1 ){
		free(accountPath);
		return 0;
	}

	char * accountsFile = _get_users_accounts_path(accountPath);
	free(accountPath);
	if(accountsFile == NULL){
		return 0;
	}	


	char * tmpName = malloc(sizeof(char) * (BUFFER_LENGTH/8)+1);
	if(tmpName == NULL){
		free(accountsFile);
		fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
		return 0;
	}
	gen_random(tmpName, (sizeof(char) * (BUFFER_LENGTH/8)));

	char tmpFile[BUFFER_LENGTH];
	strncpy(tmpFile, TMP_DIR, BUFFER_LENGTH-1);
	strncat(tmpFile, tmpName, BUFFER_LENGTH-1);

	/* Open a tmp file for writing our temporary file to. */
	FILE *tmp = fopen(tmpFile, "w");
	if(!tmp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, tmpFile );
		free(tmpName);
		free(accountsFile);
		return 0;
	}

	FILE *fp = fopen(accountsFile, "r"); 
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountsFile );
		free(accountsFile);
		fclose(tmp);
		unlink(tmpName);
		free(tmpName);
		return -1;
	}


	char readAccountName[64]; /* Account names are not allowed to be more than this*/
	int numAccount = 0;
	double balance = 0.00;
	int exists = 0;
	while(fscanf(fp, "%d %64s %lf\n", &numAccount, readAccountName, &balance) == 3){
		if(strncmp(readAccountName, accountName, 64) == 0){
			/* found the account */
			fprintf(tmp, "%d %s %lf\n", numAccount, readAccountName, balance + additionToAccount);
			exists = 1;
		}else{
			fprintf(tmp, "%d %s %lf\n", numAccount, readAccountName, balance);
		}
	}
	fflush(NULL);
	/* We have now written out the new file into the tmp folder, so we should overwrite the old file with it*/
	fseek(fp,0,SEEK_END);
	fclose(fp);

	if(exists == 0){
		/* the account doesn't exist apparently, so don't bother doing anything */
		fprintf(stderr, "%s\n", "Update Err: Account Does Not Exist");
		fclose(tmp);
		unlink(tmpName);
		free(tmpName);
		free(accountsFile);
		return 0;
	}

	FILE *overwriteFP = fopen(accountsFile, "w");
	if(overwriteFP == NULL){
		/* Well damn. If we fail here after we just closed it that means the permissions are wrong...*/
		fprintf(stderr, "%s %s (Likely Permissions problem)\n", FAILED_FILE_OPEN, accountsFile );
		if(exists == 1){
			fclose(tmp);
			unlink(tmpName);
			free(tmpName);
			free(accountsFile);
		}
		return 0;	
	}

	while(fscanf(tmp, "%d %64s %lf\n", &numAccount, readAccountName, &balance) == 3){
		fprintf(overwriteFP, "%d %s %lf\n", numAccount, readAccountName, balance);
	}

	fflush(NULL);
	fclose(tmp);
	fclose(overwriteFP);
	
	unlink(tmpName);
	if(exists == 1){
		free(tmpName);
		free(accountsFile);
	}
	return 1;
}

int create_item(const char * username, const char * account, const char * name, double amount, double latitude, double longitude){
	if(_user_exists(username) != 1)	return 0;
	/* Create the path to the line items file for the account */

	char * accountPath = _get_user_path(username);

	fprintf(stderr, "%s\n", accountPath);
	if(accountPath == NULL) return 0;
	if( _directory_exists(accountPath) != 1 ){
		free(accountPath);
		return 0;
	}

	char * accountFile = _get_user_account_path(accountPath, account);
	free(accountPath);
	if(accountFile == NULL) return  0;


	if(_file_exists(accountFile) != 1){
		free(accountFile);
		return 0;
	}

	FILE *fp = fopen(accountFile, "a");
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountFile);
		free(accountFile);
		return 0;
	}
	free(accountFile);
	fprintf(fp, "%ld %s %.2lf %lf %lf\n", time(0), name, amount, latitude, longitude);
	fclose(fp);

	/* Finally update the balance listed for the account*/
	if( update_account_balance(username, account, amount) == 0 ){
		/* Log the Err but consider it non critical */
		fprintf(stderr, "Could not update account balance %s\n", account);
	}
	return 1;	
}

struct lineItemChain * read_lineitems(const char * username, const char * account){
	if(_user_exists(username) != 1) return NULL;
	
	char * accountPath = _get_user_path(username);
	if(accountPath == NULL) return NULL;
	if( _directory_exists(accountPath) != 1 ){
		free(accountPath);
		return NULL;
	}

	char * accountFile = _get_user_account_path(accountPath, account);
	free(accountPath);
	if(accountFile == NULL) return NULL;

	if(_file_exists(accountFile) != 1){
		free(accountFile);
		return NULL;
	}

	FILE *fp = fopen(accountFile, "r");
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountFile);
		free(accountFile);
		return NULL;
	}
	free(accountFile);

	struct lineItemChain * chain = NULL;
	struct lineItemChain * head = NULL;
	struct lineItemChain * backPtr = NULL;
	chain = malloc(sizeof(struct lineItemChain));
	if(chain == NULL){
		fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
		fclose(fp);
		return NULL; /* OUT OF MEMORY */	
	}
	chain->next = NULL;
	head = chain;
	backPtr = head;	
	
	char * name = malloc(sizeof(char)*BUFFER_LENGTH);
	if(name == NULL){
		fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
		fclose(fp);
		return NULL;
	}
	time_t date;
	long tmpdate;
	double amount;
	double latitude;
	double longitude;
	while(fscanf(fp, "%ld %" TOSTR(BUFFER_LENGTH) "[^0-9] %lf %lf %lf\n", &tmpdate, name, &amount, &latitude, &longitude) == 5){
		date = tmpdate;
		chain->data = malloc(sizeof(struct lineitem));
		if(chain->data == NULL){
			fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
			fclose(fp);
			free(name);
			goto destroy_list;
		}
		
		chain->data->name = malloc(sizeof(char) * BUFFER_LENGTH);
		if(chain->data->name == NULL){
			fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
			fclose(fp);
			free(name);
			goto destroy_list;	
		}

		chain->data->date = date;
		memcpy(chain->data->name, name, BUFFER_LENGTH);
		chain->data->amount = amount;
		chain->data->latitude = latitude;
		chain->data->longitude = longitude;

		chain->next = malloc(sizeof(struct lineitem));
		if(chain->next == NULL){
			fprintf(stderr, "%s %s, line %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
			fclose(fp);
			free(name);
			goto destroy_list;
		}

		backPtr = chain;
		chain = chain->next;
	}
	free(name);

	/* We end up freeing one more item on the list than neccesary, so free that 
	 * up and NULL the ->next from the back pointer
	 */
	 if(backPtr != NULL){
		free(backPtr->next);
		backPtr->next = NULL;
	}
	fclose(fp);

	return head;

	destroy_list:
	if(head != NULL){
		for (chain = head; chain != NULL; ){
			if(chain->data->name != NULL) free(chain->data->name);	
			if(chain->data != NULL) free(chain->data);
			head = chain->next;
			free(chain);
			chain = head;
		}
	}
	return NULL;
}

	