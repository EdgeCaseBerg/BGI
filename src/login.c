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
        qentry_t *sess = NULL;

        qentry_t *req = qcgireq_parse(NULL, 0);
   
        //char *name  = req->getstr(req, "username", false);
        char *pass  = req->getstr(req, "password", false);;
        if(pass == NULL){
            goto noparams;
        }

        //uint32_t hashed = SuperFastHash(pass, strlen(pass));


        /* Check if the user is already logged in a session: */
        sess = qcgisess_init(req, NULL);
        qcgisess_settimeout(sess, SESSION_TIME);

        //char *sessUserName = sess->getstr(sess, "username", false);
        noparams:
        if(success == 0) qcgires_redirect(req, BAD_LOGIN);
        else qcgires_redirect(req, APPLICATION);

        qcgires_setcontenttype(req, "text/html");

        // De-allocate memories
        if(sess){
            qcgisess_save(sess);
            sess->free(sess);            
        } 

        req->free(req);
#ifdef ENABLE_FASTCGI
    }
#endif
    return 0;
}