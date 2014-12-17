<?php
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);

$goalService = GoalService::instance();
$userGoals = $goalService->getUserGoals($user);

$metricsService = MetricsService::instance();
$spentThisWeek = $metricsService->spentThisWeek($user);

register_js('/lib/d3.min.js');
?>
<div class="grid-1 gutter-40">
	<div class="span-1">
		<h1>Metrics by Week</h1>
		<p>
			Below you'll find your metrics for this week. Use this data 
			to help you curb bad habits and start new ones!
		</p>
	</div>
</div>
<h2>Highlights (Week of: <?php echo date('m/d', strtotime('this week')) ?>)</h2>
<div class="grid-2 gutter-40">
	<div class="span-1">
		You've spent <mark><?php echo money_format('$%.2n',(intval($spentThisWeek->amount)/100));
		?></mark> so far this week on <mark><?php echo $spentThisWeek->count; ?></mark> categories. 
	</div>
	<div class="span-1 chart-area-200">
		<!-- Pie Chart of amount spent per category -->
		<!-- https://gist.github.com/enjalot/1203641 -->
	</div>
</div>
<div class="grid-1 gutter-40">
	<div class="span-1 chart-area-300">
		<!-- Show current goals and whatnot, bar graph and lines of goal points -->
	</div>
</div>
<hr>
<div id="old-weeks">
	<a href="#old" class="button-blue bigger">Show Previous Weeks</a>
	<!-- Populated by d3 with data older than current week -->
</div>
