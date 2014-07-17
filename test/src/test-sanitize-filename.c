#include "required.h"
#include "util/ctl.h"
#include <ctype.h>

static void rtrim(char *string) { 
   	int pos;
	for (pos=strlen(string); pos >= 1 && isspace(string[pos-1]); --pos)
        ;
    string[pos] = '\0';
}

static void _sanitize_filename(char * filename){
	if(filename == NULL) return;
	rtrim(filename);
	for (unsigned int i = 0; i < strlen(filename); ++i){
		if(*(filename + i) == ' '){
			*(filename + i) = '-';
		}
	}
}

int main(){
	char * c = malloc(sizeof(char)*255);
	bzero(c, 255);
	char * tmp = "hello there I have spaces in me   ";

	for (unsigned int i = 0; i < strlen(tmp); ++i){
		*(c+i) = tmp[i];
	}

	fprintf(stdout, "%s\n", c);

	_sanitize_filename(c);

	fprintf(stdout, "%s\n", c);
	free(c);

	/* If you try to pass DATA_DIR directly, you're going to have a segfault*/
	char dir[] = DATA_DIR;
	_sanitize_filename(dir);

	return 1;
}