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
        qentry_t *sess = NULL;
        qentry_t *req = qcgireq_parse(NULL, 0);

        sess = qcgisess_init(req, NULL);
        qcgisess_settimeout(sess, SESSION_TIME);

        if(sess == NULL){
            qcgires_redirect(req, HOME);
            goto end;
        }
        
        /* Make sure the user is logged in with the session */
        char * username = sess->getstr(sess, "username", false);
        if(username == NULL){
            qcgires_redirect(req, HOME);
            goto end;
        }
   
        char *name  = req->getstr(req, "accountname", false);
        if(name == NULL){
            qcgires_redirect(req, APPLICATION);
            goto end;
        }

        if( 1 != _user_exists(username) ){
            qcgires_redirect(req, REGISTER);
            goto end;
        }

        /* Bump session time */
        qcgisess_settimeout(sess, SESSION_TIME);
        qcgires_setcontenttype(req, "application/JSON");

        /* Retrieve the line items for the account name */
        printf("[");
        /* TODO */
        printf("]");
        
        // De-allocate memories
        end:
        qcgires_setcontenttype(req, "application/JSON");
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