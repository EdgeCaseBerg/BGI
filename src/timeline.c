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

        if( 1 != _user_exists(username) ){
            qcgires_redirect(req, REGISTER);
            goto end;
        }

        qcgires_setcontenttype(req, "application/JSON");

        /* Retrieve the line items for the account name */
        struct accountChain * head = read_accounts(username);
        printf("[");    
        /* Loop and output each account, freeing the memory as we go */
        struct accountChain * chain;
        for(chain = head; chain != NULL; ){
            if(chain->data != NULL){
                printf("{\"name\" : \"%s\", \"accountBalance\" : %lf, \"items\" : [",  chain->data->name, chain->data->balance);

                struct lineItemChain * itemChain = read_lineitems(username, chain->data->name);
                struct lineItemChain * tmp = NULL;
                int i = 0;
                while(itemChain != NULL){
                    if(i != 0){
                        printf(",");
                    }
                    i++;
                    printf("{\"date\" : %zu, \"name\" : \"%s\", \"amount\" : %lf, \"latitude\" : %lf, \"longitude\" : %lf}", 
                        itemChain->data->date, itemChain->data->name, itemChain->data->amount, itemChain->data->latitude, itemChain->data->longitude
                        );
                    tmp = itemChain;
                    itemChain = itemChain->next;
                    free(tmp->data->name);
                    free(tmp->data);
                    free(tmp);
                }

                printf("]}");
                if(chain->next != NULL) printf(",");
                free(chain->data);  
            } 
            head = chain->next;
            free(chain);
            chain = head;
        }                
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