<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);

register_js('/resources/manage-categories.js');
?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>Category Management</h1>
		<p>
			You can create different categories using the action bar below. 
			Some suggestions for categories? Try things like: food, grocery,
			shopping, bills, and fun.
		</p>
	</div>
</div>
<div class="grid-1 gutter-40">
	<?php
		if (isset($_GET['e']) && intval($_GET['e']) == 1 ) {
			echo '<div class="message warning">There was a problem performing the requested action</div>';
		}
	?>
	<div class="span-1">
		<div class="flakes-actions-bar">
			<a href="#delete" class="action button-red smaller">Delete</a>
			<a href="#new" class="action button-green smaller">New</a>
		</div> 	
		<form id="new-account-form" class="grid-form hidden" action="/api/create-account.php" method="POST">
			<fieldset>
				<legend>Create New Account</legend>
				<div data-row-span="4">
					<div data-field-span="3">
		                <label>Name</label>
		                <input name="accountName" type="text">
		            </div>
		            <div data-field-span="1">
		                <label>Starting Balance</label>
		                <input name="balance" type="text">
		            </div>
				</div>
			</fieldset>
			<br/>
			<input type="submit" value="Create Account" class="button-green bigger right" />
		</form>
	</div>
</div>

<div class="grid-1 gutter-40">
	<div class="span-1">
		<table class="flakes-table">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Balance</th>
					<th>Last Updated</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($userAccounts as $account): ?>
					<tr>
						<td>
							<input type="checkbox" name="<?php echo htmlspecialchars($account->name); ?>" rel="<?php echo $account->id; ?>" />
						</td>
						<td><?php echo htmlspecialchars($account->name); ?></td>
						<td><?php echo money_format('%.2n',(intval($account->balance)/100)); ?></td>
						<td><?php echo date('m/d/Y', strtotime($account->last_updated));  ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>