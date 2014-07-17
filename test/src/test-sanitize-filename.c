#include "required.h"
#include "util/ctl.h"

static void _sanitize_filename(char * filename){
	if(filename == NULL) return;
	for (unsigned int i = 0; i < strlen(filename); ++i){
		if(*(filename + i) == ' '){
			*(filename + i) = '-';
		}
	}
}

int main(){
	char * c = malloc(sizeof(char)*255);
	bzero(c, 255);
	char * tmp = "hello there I have spaces in me";

	for (unsigned int i = 0; i < strlen(tmp); ++i){
		*(c+i) = tmp[i];
	}

	fprintf(stdout, "%s\n", c);

	_sanitize_filename(c);

	fprintf(stdout, "%s\n", c);
	free(c);

	return 1;
}