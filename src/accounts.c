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

        if( 1 != _user_exists(username) ){
            qcgires_redirect(req, REGISTER);
            goto end;
        }

        /* Bump session time */
        qcgisess_settimeout(sess, SESSION_TIME);
        qcgires_setcontenttype(req, "application/JSON");

        /* Return all accounts for the user */
        struct accountChain * head = read_accounts(username);
        printf("%s", "[");    
        /* Loop and output each account, freeing the memory as we go */
        struct accountChain * chain;
        for(chain = head; chain != NULL; ){
            if(chain->data != NULL){
                printf("{\"id\" : %d, \"name\" : \"%s\", \"balance\" : %.2lf }", chain->data->id, chain->data->name, chain->data->balance);
                if(chain->next != NULL) printf(",");
                free(chain->data);  
            } 
            head = chain->next;
            free(chain);
            chain = head;
        }
        printf("%s", "]");
        
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