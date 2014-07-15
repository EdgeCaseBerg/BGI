#include "required.h"

#include "util/fasthash.h"
#include "util/ctl.h"
#include <string.h>
#include <ctype.h>


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
   
        char *accountname  = req->getstr(req, "accountname", false);
        char *name = req->getstr(req, "name", false);
        char *tmpamount = req->getstr(req, "amount", false);
        char *tmplatitude = req->getstr(req, "latitude", false);
        char *tmplongitude = req->getstr(req, "longitude", false);
        if(accountname == NULL ||  name == NULL || tmpamount == NULL || tmplatitude == NULL || tmplongitude == NULL){
            qcgires_redirect(req, BAD_LINEITEM);
            goto end;
        }

        /*Make sure that name matches the pattern [^0-9]*/
        for (int i = 0; i < (int)strlen(name); ++i){
            if( isdigit( name[i] ) ){
                qcgires_redirect(req, BAD_LINEITEM);
                goto end;       
            }
        }

        /* All exist, convert to appropriate types */
        double amount, latitude, longitude;

        sscanf(tmpamount, "%lf", &amount);
        sscanf(tmplongitude, "%lf", &longitude);
        sscanf(tmplatitude, "%lf", &latitude);


        if( 1 != _user_exists(username) ){
            qcgires_redirect(req, REGISTER);
            goto end;
        }

        /* Bump session time */
        qcgisess_settimeout(sess, SESSION_TIME);

        /* Does the account exist? */
        if( account_exists(username, accountname) == 0 ){
        	qcgires_redirect(req, CREATE_ACCOUNT);
        	goto end;
        }

        int success = create_item(username, accountname,name, amount, latitude, longitude);
		
		if(success == 0) qcgires_redirect(req, BAD_LINEITEM);
		else qcgires_redirect(req, APPLICATION);
        
        // De-allocate memories
        end:
        qcgires_setcontenttype(req, "text/html");
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