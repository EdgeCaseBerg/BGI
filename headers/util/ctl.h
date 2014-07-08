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


/* All returns 1 on Truth, 0 on False, -1 on Error */
int _directory_exists(const char * directoryToCheck);
int _user_exists(const char * username);

/* All returns 1 for truth, 0 for false */
int _file_exists(const char * filename);
int _password_matches(const char * username, const uint32_t hashpass);
int create_user(const char * username, const uint32_t hashpass);

/* Creates neccesary structures for data storage and ctl usage */
int bgi_data_init();

/* Returns 1 on success, 0 on false, -1 on error */
int create_account(const char * username, const char * account);
int create_item(const char * username, const char * account, const char * name, long amount, long latitude, long longitude);

/* Will return a linked list of accounts stored in the accountChain,
 * the calling party is responsible for free-ing the resultant nodes.
 */
struct accountChain * read_accounts(const char * username);





#endif