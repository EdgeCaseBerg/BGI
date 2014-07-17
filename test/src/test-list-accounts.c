#include "required.h"
#include "util/ctl.h"

int main(){
	char * username = "admin";
	
	struct accountChain * head = read_accounts(username);
	struct accountChain * chain; 
	if(head != NULL){
		for (chain = head; chain != NULL; ){

			if(chain->data != NULL){
				printf("%s\n", chain->data->name);
				int exists = account_exists(username, chain->data->name);
				printf("Exists: %d\n", exists);
				free(chain->data);
			} 
			head = chain->next;
			free(chain);
			chain = head;
		}
	}

	return 1;
}