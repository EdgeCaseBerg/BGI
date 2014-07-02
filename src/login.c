#include "required.h"

int main(void){
    #ifdef ENABLE_FASTCGI
        while(FCGI_Accept() >= 0) {
    #endif

    qentry_t *req = qcgireq_parse(NULL, 0);
    qcgires_setcontenttype(req, "text/plain");

    // De-allocate memories
    req->free(req);
#ifdef ENABLE_FASTCGI
    }
#endif
    return 0;
}