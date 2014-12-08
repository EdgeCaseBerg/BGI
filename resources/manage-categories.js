$(document).ready(function($) {
	$('a[href="#new"]').click( function() {
		$('#new-account-form').slideToggle();
	})	

	$('a[href=#delete]').click( function() {
		var checkboxes = $('input[type=checkbox]:checked')
		if (checkboxes.length > 0) {
			for (var i = checkboxes.length - 1; i >= 0; i--) {
				var account_id = $(checkboxes[i]).attr('rel')
				console.info('Deleting Account with id: ' + account_id)
				//todo: confirm each and do ajax to api
			};
		} else {
			window.alert('You must select an account first')
		}
	})
})