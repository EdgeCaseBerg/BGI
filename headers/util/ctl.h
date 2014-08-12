/* What's CTL you ask? CTL is the BGI Admin Control program.
 * It's responsible for making user data directories and handling
 * the input and output of the flat files used for BGI's data.
*/

#ifndef __CTL_H__
#define __CTL_H__
#include <sys/stat.h>
#include <stdlib.h>
#include <dirent.h>
#include <errno.h>
#include <string.h>

#include "config.h"
#include "errors.h"
#include "required.h"
#include <stdint.h>
#include <inttypes.h> /* Need inttypes for PRIu32 */
#include <time.h>
#include <unistd.h>


/* All returns 1 on Truth, 0 on False, -1 on Error */
int _directory_exists(const char * directoryToCheck);
int _user_exists(const char * username);

/* All returns 1 for truth, 0 for false */
int _file_exists(const char * filename);
int _password_matches(const char * username, const uint32_t hashpass);
int create_user(const char * username, const uint32_t hashpass);
int account_exists(const char * username, const char * account);

/* Remember to free the resultant pointer */
char * _get_user_path(const char * username);
char * _get_users_accounts_path(const char * accountPath);
char * _get_user_account_path(const char * accountPath, const char * accountName);

/* Creates neccesary structures for data storage and ctl usage */
int bgi_data_init();

/* Returns 1 on success, 0 on false, -1 on error */
int create_account(const char * username, const char * account);
int create_item(const char * username, const char * account, const char * name, double amount, double latitude, double longitude, struct tm * itemTime);
int update_account_balance(const char * username, const char * accountName , double additionToAccount);

/* Will return a linked list of accounts stored in the accountChain,
 * the calling party is responsible for free-ing the resultant nodes.
 * - in the line items remember to free the string for name.
 */
struct accountChain * read_accounts(const char * username);
struct lineItemChain * read_lineitems(const char * username, const char * account);





#endif