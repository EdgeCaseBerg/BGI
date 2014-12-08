$(document).ready(function($) {
	$('a[href="#new"]').click( function() {
		$('#new-account-form').slideToggle();
	})	

	$('a[href=#delete]').click( function() {
		var checkboxes = $('input[type=checkbox]:checked')
		if (checkboxes.length > 0) {
			for (var i = checkboxes.length - 1; i >= 0; i--) {
				var box = $(checkboxes[i])
				var account_id = box.attr('rel')
				console.info('Deleting Account with id: ' + account_id)
				if (window.confirm('Are you sure you want to delete ' + box.attr('name') + '?')) {
					$.ajax({
						url: "/api/delete-account.php",
						context: box,
						method: "POST",
						data: {
							account_id : account_id
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
			window.alert('You must select an account first')
		}
	})
})