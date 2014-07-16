#include "required.h"
#include "util/ctl.h"

int main(){
	char * username = "admin";
	char * account = "checkings";

	fprintf(stderr, "%s %s\n", username, account );

	update_account_balance(username, account , 1.0);
	return 1;
}