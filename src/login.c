#include "required.h"

int main(void){
    #ifdef ENABLE_FASTCGI
        while(FCGI_Accept() >= 0) {
    #endif
        int success = false;

        qentry_t *req = qcgireq_parse(NULL, 0);
   
        //char *name  = req->getstr(req, "username", false);
        //char *pass  = req->getstr(req, "password", false);;

        /* Check if the user is already logged in a session: */
        qentry_t *sess = qcgisess_init(req, NULL);
        qcgisess_settimeout(sess, SESSION_TIME);

        //char *sessUserName = sess->getstr(sess, "username", false);
        
        if(success == 0) qcgires_redirect(req, BAD_LOGIN);
        else qcgires_redirect(req, APPLICATION);

        qcgires_setcontenttype(req, "text/html");

        // De-allocate memories
        qcgisess_save(sess);
        sess->free(sess);        

        req->free(req);
#ifdef ENABLE_FASTCGI
    }
#endif
    return 0;
}