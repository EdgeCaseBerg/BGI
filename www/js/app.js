jQuery( document ).ready(function( $ ) {
	/* Load accounts */
	var accountsURI =  window.bgidomain + "accounts.cgi"

	if($('body').attr('id') == "welcome"){
		$.get(accountsURI, function(response){
			var accounts = response
			for (var i = accounts.length - 1; i >= 0; i--) {
				var row = $('<tr></tr>')
				var button = $('<td><button name="view-account" rel="'+accounts[i].name+'">View</button></td>')
				var first = $('<td>' + accounts[i].id + '</td>')
				var second = $('<td>' + accounts[i].name + '</td>')
				var third = $('<td>' + accounts[i].balance + '</td>')
				row.append(button)
				row.append(first)
				row.append(second)
				row.append(third)
				$('table[name="accounts-list"]').find('tbody').append(row)
			};
		})

		$('table[name="accounts-list"]').on('click', 'button[name="view-account"]', function(evt){
			evt.preventDefault()
			/* Load the line items for this account */
			var accountName = $(this).attr('rel')
			$('section[name="account-display"]').find('h3').text(accountName.charAt(0).toUpperCase() + accountName.substr(1))

			/* Get em. */
			var lineItemsTable = $('table[name="lineitems"]')
			var lineItemsURI = window.bgidomain + "list-lineitems.cgi?accountname=" + accountName

			$.get(lineItemsURI, function(response){
				var lineitems = response
				console.log(lineitems)
			})
		})


	}

	if($('select[name="accountname"]').length > 0){
		$.get(accountsURI, function(response){
			var accounts = response
			for (var i = accounts.length - 1; i >= 0; i--) {
				$('select[name="accountname"]').append( '<option value="' + accounts[i].name + '">'+accounts[i].name+'</option>')
			};
		})
	}
})