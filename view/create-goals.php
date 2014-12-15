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
			echo '<div class="flakes-message warning">There was a problem performing the requested action</div>';
		}
		if (isset($_GET['s']) && intval($_GET['s']) == 1 ) {
			echo '<div class="flakes-message information">Successfully created item</div>';
		}
	?>
	<hr>
	<div class="span-1">
		<h2>Weekly</h2>
		<p>Set a goal for a single or multiple accounts in aggregate to meet each week</p>
		<form class="grid-form" action="/api/create-goal.php" method="POST">
			<fieldset>
				<legend>Create Weekly Goal</legend>
				<div data-row-span="3">
					<input type="hidden" name="goal_type" value="<?php echo GOAL_TYPE_WEEKLY ?>" />
					<div data-field-span="2">
		                <label>Name</label>
		                <input name="name" type="text">
		            </div>
		            <div data-field-span="1">
		                <label>Amount</label>
		                <input name="amount" type="text" pattern="[-]?[0-9]+\.[0-9]{2}?" title="Please enter a valid dollar amount">
		            </div>
				</div>
				<div data-row-span="1">
					<div data-field-span="1">
		            	<label>Accounts (select multiple)</label>
						<select name="accounts[]" multiple >
							<?php foreach ($userAccounts as $account) {
								echo '<option value="' . $account->id . '">' . $account->name . '</option>';
							} ?>
						</select>
					</div>
				</div>
			</fieldset>
			<br/>
			<input type="submit" value="Create" class="button-green bigger right" />
		</form>
	</div>
	<div class="span-1">
		<h2>Monthly</h2>
		<p>Set a goal for single or multiple accounts in aggregate to meet each month</p>
		<form class="grid-form" action="/api/create-goal.php" method="POST">
			<fieldset>
				<legend>Create Monthly Goal</legend>
				<div data-row-span="3">
					<input type="hidden" name="goal_type" value="<?php echo GOAL_TYPE_MONTHLY ?>" />
					<div data-field-span="2">
		                <label>Name</label>
		                <input name="name" type="text">
		            </div>
		            <div data-field-span="1">
		                <label>Amount</label>
		                <input name="amount" type="text" pattern="[-]?[0-9]+\.[0-9]{2}?" title="Please enter a valid dollar amount">
		            </div>
				</div>
				<div data-row-span="1">
					<div data-field-span="1">
		            	<label>Accounts (select multiple)</label>
						<select name="accounts[]" multiple >
							<?php foreach ($userAccounts as $account) {
								echo '<option value="' . $account->id . '">' . $account->name . '</option>';
							} ?>
						</select>
					</div>
				</div>
			</fieldset>
			<br/>
			<input type="submit" value="Create" class="button-green bigger right" />
		</form>
	</div>
	<div class="span-1">
		<h2>Timed</h2>
		<p>If you want to set a goal for a specific time period across accounts, use the form below</p>
		<form class="grid-form" action="/api/create-goal.php" method="POST">
			<fieldset>
				<legend>Create Timed Goal</legend>
				<div data-row-span="3">
					<input type="hidden" name="goal_type" value="<?php echo GOAL_TYPE_TIMED ?>" />
					<div data-field-span="2">
		                <label>Name</label>
		                <input name="name" type="text">
		            </div>
		            <div data-field-span="1">
		                <label>Amount</label>
		                <input name="amount" type="text" pattern="[-]?[0-9]+\.[0-9]{2}?" title="Please enter a valid dollar amount">
		            </div>
				</div>
				<div data-row-span="1">
					<div data-field-span="1">
		                <label>Start Date</label>
		                <input name="start_time" type="date">
		            </div>
		        </div>
		        <div data-row-span="1">
		            <div data-field-span="1">
		                <label>End Date</label>
		                <input name="end_time" type="date">
		            </div>
				</div>
				<div data-row-span="1">
					<div data-field-span="1">
		            	<label>Accounts (select multiple)</label>
						<select name="accounts[]" multiple >
							<?php foreach ($userAccounts as $account) {
								echo '<option value="' . $account->id . '">' . $account->name . '</option>';
							} ?>
						</select>
					</div>
				</div>
			</fieldset>
			<br/>
			<input type="submit" value="Create" class="button-green bigger right" />
		</form>
	</div>
</div>