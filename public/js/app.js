jQuery( document ).ready(function( $ ) {
	/* Load accounts */
	if($('body').attr('id') == "welcome"){
		var accountsURI =  window.bgidomain + "accounts.cgi"
		$.get(accountsURI, function(response){
			var accounts = response
			for (var i = accounts.length - 1; i >= 0; i--) {
				var row = $('<tr></tr>')
				var first = $('<td>' + accounts[i].id + '</td>')
				var second = $('<td>' + accounts[i].name + '</td>')
				var third = $('<td>' + accounts[i].balance + '</td>')
				row.append(first)
				row.append(second)
				row.append(third)
				$('table[name="accounts-list"]').find('tbody').append(row)
			};
		})
	}
})