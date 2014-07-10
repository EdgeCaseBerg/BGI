#include "required.h"
#include "util/ctl.h"

int main(){
	char * username = "admin";
	char * account = "checkings";

	fprintf(stderr, "%s %s\n", username, account );

	char * accountPath = NULL;
	accountPath = _get_user_path(username);
	if(accountPath == NULL){
		fprintf(stderr, "%s\n", "Bad account path");
		return 0;	
	} 
	if( _directory_exists(accountPath) != 1 ){
		fprintf(stderr, "%s %s\n", "Directory does not exist", accountPath);
		free(accountPath);
		return 0;
	}

	char * accountFile = _get_user_account_path(accountPath, account);
	if(accountFile == NULL){
		fprintf(stderr, "%s\n", "account file bad");
		free(accountPath);
		return 0;	
	} 

	if(_file_exists(accountFile) != 1){
		fprintf(stderr, "%s\n", "account file does not exist");
		free(accountPath);
		return 0;
	}

	free(accountPath);

	FILE *fp = fopen(accountFile, "r");
	if(!fp){
		fprintf(stderr, "%s %s\n", FAILED_FILE_OPEN, accountFile);
		return 0;
	}

	struct lineItemChain * chain = NULL;
	struct lineItemChain * head = NULL;
	struct lineItemChain * backPtr = NULL;
	chain = malloc(sizeof(struct lineItemChain));
	if(chain == NULL){
		fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
		return 0; /* OUT OF MEMORY */	
	}
	chain->next = NULL;
	head = chain;
	backPtr = head;	
	
	char * name = NULL;
	time_t date;
	long tmpdate;
	double amount;
	double latitude;
	double longitude;
	while(fscanf(fp, "%ld %s %lf %lf %lf\n", &tmpdate, name, &amount, &latitude, &longitude) == 5){
		date = tmpdate;
		chain->data = malloc(sizeof(struct lineitem));
		if(chain->data == NULL){
			fprintf(stderr, "%s %s, line: %d\n", OUT_OF_MEMORY, __FILE__, __LINE__);
			fclose(fp);
			goto destroy_list;
		}
		

		fprintf(stderr, "%zu %s %lf %lf %lf\n", date, name, amount, latitude, longitude);

		chain->data->date = date;
		chain->data->name = name;
		chain->data->amount = amount;
		chain->data->latitude = latitude;
		chain->data->longitude = longitude;

		chain->next = malloc(sizeof(struct lineitem));
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
	 if(backPtr != NULL){
		free(backPtr->next);
		backPtr->next = NULL;
	}
	fclose(fp);

	return 1;

	destroy_list:
	fprintf(stderr, "%s\n", "destroying list");
	if(head != NULL){
		for (chain = head; chain != NULL; ){
			if(chain->data != NULL) free(chain->data);
			head = chain->next;
			free(chain);
			chain = head;
		}
	}
	return 0;
}