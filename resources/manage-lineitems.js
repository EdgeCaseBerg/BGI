$(document).ready(function($) {
	var accountsLoaded = {}
	var acctTbody = $('#account-body')
	var loadingArea = $('#loading-area')

	function tdWrap(item) {
		return '<td>' + item + '</td>'
	}

	function createRow(row_data) {
		var tr = $(document.createElement('tr'))
		tr.append( $(tdWrap('<input type="checkbox" name="'+row_data.name+'" rel="'+row_data.id+'" />')))
		tr.append( tdWrap(row_data.name))
		tr.append( tdWrap(row_data.amount))
		var dateInfo = new Date(row_data.created_time)
		tr.append( tdWrap(dateInfo.toLocaleDateString()))
		acctTbody.append(tr)
	}



	function showAccount(account_data) {
		//Animate things a bit
		console.info('Showing line item data', account_data)
		loadingArea.slideDown()
		acctTbody.find('tr').remove()
		for (var i = account_data.length - 1; i >= 0; i--) {
			createRow(account_data[i])
		};
		if (account_data.length == 0) {
			createRow({
				id : -1,
				name : 'No items',
				created_time : new Date(),
				amount: 0
			})
		}

		loadingArea.slideUp()
	}

	$('a[href="#view"]').click( function() {
		/* Load from the server or from the cache on page */
		var account_id = $(this).attr('rel')
		if (accountsLoaded[account_id]) {
			console.info('Using saved data', account_id)
			showAccount(accountsLoaded[account_id])
		} else {
			console.info('Retrieving lineitem data', account_id)
			$.ajax({
				url: "/api/get-lineitems-for-account.php?account_id=" + parseInt(account_id),
				context: this,
				method: "GET",
				dataType: 'json',
				error: function() {
					alert('There was a problem with your request')
				},
				success: function(data) {
					if (data.code == 200) {
						accountsLoaded[account_id] = data.data
						showAccount(data.data)
					} else {
						alert(data.message)
					}
				}
			})
		}
	})	

	$('a[href=#delete]').click( function() {
		var checkboxes = $('input[type=checkbox]:checked')
		if (checkboxes.length > 0) {
			for (var i = checkboxes.length - 1; i >= 0; i--) {
				var box = $(checkboxes[i])
				var lineItemId = box.attr('rel')
				console.info('Deleting LineItem with id: ' + lineItemId)
				if (window.confirm('Are you sure you want to delete ' + box.attr('name') + '?')) {
					$.ajax({
						url: "/api/delete-lineitem.php", /* todo: make this */
						context: box,
						method: "POST",
						data: {
							lineItemId : lineItemId
						},
						dataType: 'json',
						error: function() {
							alert('There was a problem with your request')
						},
						success: function(data) {
							if (data.code == 200) {
								console.info(data.message)
								box.closest('tr').fadeOut()
							} else {
								alert(data.message)
							}
						}
					})
				}
			};
		} else {
			window.alert('You must select an item first')
		}
	})
})