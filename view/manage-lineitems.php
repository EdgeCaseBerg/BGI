<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);
if (empty($userAccounts)) {
	echo '<h1 class="message warning">You need to create categories before you create line items</div>';
}
?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>LineItem Management</h1>
		<p>
			
		</p>
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
		<table class="flakes-table">
			<thead>
				<tr>
					<th>&nbsp;</th>
					<th>Name</th>
					<th>Amount</th>
					<th>Created Time</th>
				</tr>
			</thead>
			<tbody>
				<!-- Populate with JS -->
			</tbody>
		</table>
	</div>
</div>