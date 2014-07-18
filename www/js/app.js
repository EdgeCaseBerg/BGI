jQuery( document ).ready(function( $ ) {
	/* Load accounts */
	var accountsURI =  window.bgidomain + "accounts.cgi"

	if($('body').attr('id') == "welcome"){
		$.get(accountsURI, function(response,e,x){
			if(x.getAllResponseHeaders().indexOf("text/html") != -1){
				alert( "Could not load accounts");
				location.replace(window.bgidomain + "accounts.cgi") //redo the call to redirect them
				return
			}
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
			lineItemsTable.find('tbody').children().remove()
			var lineItemsURI = window.bgidomain + "list-lineitems.cgi?accountname=" + accountName

			$.get(lineItemsURI, function(response){
				var lineitems = response
				for (var i = lineitems.length - 1; i >= 0; i--) {
					var item = lineitems[i]
					var row = $('<tr></tr>')	
					var headers = $('table[name="lineitems"]').find('thead tr th').each(function(idx,elem){
						var key = $(elem).text().toLowerCase()
						if( item[key] ){
							if(key == "date"){
								row.append($('<td>'+new Date(parseInt(item[key])*1000).toLocaleDateString()+'</td>'))
							}else{
								row.append($('<td>'+item[key]+'</td>'))	
							}
							
						}
					})

					$('table[name="lineitems"]').find('tbody').append(row)
				};	
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

	if($('body').attr('id') == 'create-lineitem'){
		/* Prepopulate the location for the inputs */
		if ("geolocation" in navigator) {
			var success = function(position){
				var lat = position.coords.latitude
				var lng = position.coords.longitude
				$('input[name=latitude]').val(lat)
				$('input[name=longitude]').val(lng)
			}
			var error = function(positionError){
				if(window.console)
					window.console.warn('ERROR(' + err.code + '): ' + err.message);
			}
			var options = {
			  enableHighAccuracy: true,
			  timeout: 5000,
			  maximumAge: 0
			};
			navigator.geolocation.getCurrentPosition(success, error, options)
		}
	}
})