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
		<h1>Create a LineItem</h1>
		<p>
			Track your spending by inputting your expenses here! Make sure 
			to make a category to classify line items under first though!
		</p>
	</div>
</div>
<div class="grid-1 gutter-40">
	<?php
		if (isset($_GET['e']) && intval($_GET['e']) == 1 ) {
			echo '<div class="message warning">There was a problem performing the requested action</div>';
		}
		if (isset($_GET['s']) && intval($_GET['s']) == 1 ) {
			echo '<div class="message information">Successfully created item</div>';
		}
	?>
	<hr></br>
	<div class="span-1">
		<form class="grid-form" action="/api/create-lineitem.php" method="POST">
			<fieldset>
				<legend>Create New Account</legend>
				<div data-row-span="4">
					<div data-field-span="1">
						<select name="account_id">
							<?php foreach ($userAccounts as $account) {
								echo '<option value="' . $account->id . '">' . $account->name . '</option>';
							} ?>
						</select>
					</div>
					<div data-field-span="2">
		                <label>Name</label>
		                <input name="name" type="text">
		            </div>
		            <div data-field-span="1">
		                <label>Amount</label>
		                <input name="amount" type="text" pattern="[-]?[0-9]+\.[0-9]{2}?" title="Please enter a valid dollar amount">
		            </div>
				</div>
			</fieldset>
			<br/>
			<input type="submit" value="Create" class="button-green bigger right" />
		</form>
	</div>
</div>