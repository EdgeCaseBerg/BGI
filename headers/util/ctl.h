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

#include "config.h"
#include "errors.h"

/* Returns 1 on Truth, 0 on False, -1 on Error */
int _directory_exists(const char * directoryToCheck);

/* Returns 1 for truth, 0 for false */
int _file_exists(const char * filename);

/* Returns 1 for Success, 0 for failure */
int create_user(const char * username, const char * hashpass);

/* Creates neccesary structures for data storage and ctl usage */
int init();

#endif