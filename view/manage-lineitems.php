<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);
if (empty($userAccounts)) {
	echo '<h1 class="flakes-message warning">You need to <a href="/manage-categories">create some categories</a> before you create line items</div>';
}
register_js('/resources/manage-lineitems.js');
?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>LineItem Management</h1>
		<p>
			Click the View button below to pull up a categorie's items. You can 
			delete the items by selecting the checkbox next to their name. Clicking
			new line item will bring you aware from this page.
		</p>
		<small>Note that account balances will not update unless you refresh the page</small>
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
							<a href="#view" class="button-blue smaller" rel="<?php echo $account->id; ?>">View</a> 
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
<hr>
<br/>
<div id="lineitem-actions" class="grid-1 gutter-40 ">
	<div class="span-1">
		<div class="flakes-actions-bar">
			<a href="#delete" class="action button-red smaller">Delete</a>
			<a href="/add-lineitem" class="action button-green smaller">New LineItem</a>
		</div> 	
		<div id="loading-area" class="grid-1 gutter-40 hidden">
			<div class="span-1" style="text-align: center">
				<img src="/resources/images/loading-bar.gif" />
			</div>
		</div>
		<table class="flakes-table">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Amount</th>
					<th>Created Time</th>
				</tr>
			</thead>
			<tbody id="account-body">
				<!-- Populate with JS -->
			</tbody>
		</table>
	</div>
</div>