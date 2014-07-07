jQuery( document ).ready(function( $ ) {
	/* Load accounts */
	if($('body').attr('id') == "welcome"){
		var accountsURI =  window.bgidomain + "accounts.cgi"
		console.log( $('ul[name="accounts-list"]').get(accountsURI) )
	}
})