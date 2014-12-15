<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);

$goalService = GoalService::instance();
$userGoals = $goalService->getUserGoals($user);
$goalAccounts = $goalService->getUserGoalAccounts($user);
/* */

if (empty($userAccounts)) {
	echo '<h1>You need to have <a href="/manage-categories">categories</a> before you can manage goals</h1>';
	exit();
}

register_js('/resources/manage-goals.js');

?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>Goal Management</h1>
		<p>
			Below you can delete and edit goals. When selecting which goals will 
			be linked to which categories please do not exit the page quickly, as 
			there may be background tasks occuring via ajax.
		</p>
		<p>
			To delete a goal, check the box of its row and click the delete button.
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
			<?php foreach ($userGoals as $goal): ?>
				<tr>
					<td>
						<input type="checkbox" class="delete" name="<?php echo htmlspecialchars($goal->name); ?>" rel="<?php echo $goal->id; ?>" />
					</td>
					<td><?php echo htmlspecialchars($goal->name); ?></td>
					<td><?php echo money_format('%.2n',(intval($goal->amount)/100)); ?></td>
					<td>
						<ul class="dotless">
						<?php foreach ($userAccounts as $account): ?>
							<li>
								<?php 
								$checked = isset($goalAccounts['id:'.$goal->id]) && in_array($account->id, $goalAccounts['id:'.$goal->id]);
								?>
								<label><?php echo $account->name ?></label>
								<input type="checkbox" <?php echo $checked ? 'checked' : ''; ?> class="category" rel="<?php echo $account->id ?>" />
							</li>
						<?php endforeach; ?>
						</ul>
					</td>
					<td><?php echo goal_type_text($goal->goal_type); ?></td>
					<?php if ($goal->goal_type == GOAL_TYPE_TIMED): ?>
						<td><?php echo date('m/d/Y', strtotime($goal->start_time));  ?></td>
						<td><?php echo date('m/d/Y', strtotime($goal->end_time));  ?></td>
					<?php else: ?>
						<td>N/A</td>
						<td>N/A</td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>