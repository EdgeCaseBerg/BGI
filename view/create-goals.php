<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);

if (empty($userAccounts)) {
	echo '<h1>You need to <a href="/manage-categories">make a category first</a></h1>';
}

?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>Create Goals</h1>
		<p>
			You can use the forms below to create goals for the week, for the 
			month, or for any time period you'd like. Creating goals can be 
			useful in maintaining your own budget and sticking to a plan.
		</p>
		<p>
			Once you've created a goal, you'll be able to see and edit it on the <a href="/manage-goals">goals page</a>.
		</p>
	</div>
</div>
<div class="grid-3 gutter-40">
	<?php
		if (isset($_GET['e']) && intval($_GET['e']) == 1 ) {
			echo '<div class="message warning">There was a problem performing the requested action</div>';
		}
		if (isset($_GET['s']) && intval($_GET['s']) == 1 ) {
			echo '<div class="message information">Successfully created item</div>';
		}
	?>
	<hr>
	<div class="span-1">
		<h2>Weekly</h2>
		<p>Set a goal for a single or multiple accounts in aggregate to meet each week</p>
	</div>
	<div class="span-1">
		<h2>Monthly</h2>
		<p>Set a goal for single or multiple accounts in aggregate to meet each month</p>
	</div>
	<div class="span-1">
		<h2>Timed</h2>
		<p>If you want to set a goal for a specific time period across accounts, use the form below</p>
	</div>
</div>