$(document).ready(function($) {

	/* Deletion of goals that are checked */
	$('a[href=#delete]').click( function() {
		var checkboxes = $('input.delete[type=checkbox]:checked')
		if (checkboxes.length > 0) {
			for (var i = checkboxes.length - 1; i >= 0; i--) {
				var box = $(checkboxes[i])
				var goal_id = box.attr('rel')
				console.info('Deleting Goal with id: ' + goal_id)
				if (window.confirm('Are you sure you want to delete ' + box.attr('name') + '?')) {
					$.ajax({
						url: "/api/delete-goal.php",
						context: box,
						method: "POST",
						data: {
							goal_id : goal_id
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
			window.alert('You must select a Goal first')
		}
	})

	$('input.category[type=checkbox]').change(function(){
		var box = $(this)
		var goal_id = box.closest('tr').find('input.delete').attr('rel')
		var account_id = box.attr('rel')
		var state = this.checked

		console.info('Toggling linkage of goal and account',goal_id,account_id,state)
		if (account_id && goal_id) {
			$.ajax({
				url: "/api/toggle-goal-account.php",
				context: box,
				method: "POST",
				data: {
					goal_id: goal_id,
					account_id: account_id,
					state: (state ? 'T' : 'F')
				},
				dataType: 'json',
				error: function() {
					alert('There was a problem toggling the accounts linked to the goal')
				},
				success: function(data) {
					if (data.code == 200) {
						console.info(data.message)
						box.closest('li').fadeIn(100).fadeOut(100).fadeIn(100)
						window.box = box
					} else {
						alert(data.message)
					}
				}
			})	
		}
	})
})