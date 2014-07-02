#ifdef ENABLE_FASTCGI
	#include "fcgi_stdio.h"
#else
	#include <stdio.h>
#endif
#include <stdlib.h>
#include <stdbool.h>
#include "qdecoder.h"

#include <time.h>
#include "config.h"

int main(void)
{
#ifdef ENABLE_FASTCGI
    while(FCGI_Accept() >= 0) {
#endif
    qentry_t *req = qcgireq_parse(NULL, 0);
    qcgires_setcontenttype(req, "text/plain");

    printf("%ld", time(0));

    // De-allocate memories
    req->free(req);
#ifdef ENABLE_FASTCGI
    }
#endif
    return 0;
}