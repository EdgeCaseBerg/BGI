<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);

register_js('/resources/manage-goals.js');

?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>Goal Management</h1>
		<p>
			Below you can delete and edit goals. When selecting which goals will 
			be linked to which accounts please do not exit the page quickly, as 
			there be background tasks occuring via ajax.
		</p>
	</div>
</div>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<div class="flakes-actions-bar">
			<a href="#delete" class="action button-red smaller">Delete</a>
			<a href="/create-goals" class="action button-green smaller">New</a>
		</div> 	
	</div>
</div>

<div class="grid-1 gutter-40">
	<div class="span-1">
		<table class="flakes-table">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Amount</th>
					<th>Accounts</th>
					<th>Type</th>
					<th>Start Time</th>
					<th>End Time</th>
				</tr>
			</thead>
			<tbody>
				
			</tbody>
		</table>
	</div>
</div>