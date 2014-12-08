<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);
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
	<div class="span-1">
		<div class="flakes-actions-bar">
			<a href="#delete" class="action button-gray smaller">Delete</a>
			<a href="#delete" class="action button-gray smaller">New</a>
			
		</div> 

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
							<input type="checkbox" rel="<?php echo $account->id; ?>" />
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

