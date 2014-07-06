#include "required.h"

#include "util/fasthash.h"
#include "util/ctl.h"
#include <string.h>

int main(void){
    /* Before accepting make sure BGI is setup */
    bgi_data_init();
    #ifdef ENABLE_FASTCGI
        while(FCGI_Accept() >= 0) {
    #endif
        int success = false;

        qentry_t *req = qcgireq_parse(NULL, 0);
   
        char *name  = req->getstr(req, "username", false);
        char *pass  = req->getstr(req, "password", false);
        char *admin = req->getstr(req, "ADMIN_SECRET", false);
        if(pass == NULL || name == NULL || admin == NULL){
            goto badrequest;
        }

        if( strncmp(admin, ADMIN_SECRET, 64) != 0 ){
            goto badrequest;
        }

        if( 0 != _user_exists(name) ){
            goto badrequest;
        }
        uint32_t hashed = SuperFastHash(pass, strlen(pass));

        success = create_user(name, hashed);

        badrequest:
        if(success == 0) qcgires_redirect(req, REGISTER);
        else qcgires_redirect(req, HOME);

        qcgires_setcontenttype(req, "text/html");

        req->free(req);
#ifdef ENABLE_FASTCGI
    }
#endif
    return 0;
}