<?php
include '../init.php';
ensureUserLoggedin(); 

if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	logMessage("Invalid method", LOG_LVL_DEBUG);
	goto err;
}

/* Check Neccesary parameters */
if (empty($_POST['goal_type']) || empty($_POST['name']) || empty($_POST['amount']) || empty($_POST['accounts'])) {
	goto err;
}

$goalType = intval($_POST['goal_type']);
$name = trim($_POST['name']);
$amount = intval(floatval($_POST['amount'])*100);


$goal = new Goal();
$goal->name = $name;
$goal->amount = $amount;
$goal->user_id = $_SESSION['userId'];
$goalService = GoalService::instance();

$created = null;
switch ($goalType) {
	case GOAL_TYPE_MONTHLY:
	case GOAL_TYPE_WEEKLY:
		$created =  $goalService->createWeeklyGoal($goal);
		if ($created === false) { 
			goto err;
		}
		break;
	case GOAL_TYPE_TIMED:
		if (!isset($_POST['start_time']) || !isset($_POST['end_time'])) {
			goto err;
		}

		$goal->start_time =  strtotime($_POST['start_time']);
		$goal->end_time =  strtotime($_POST['end_time'] .' 23:59');

		if (empty($goal->start_time) || empty($goal->end_time) || $goal->start_time > $goal->end_time) {
			goto err;
		}

		$goal->start_time = date('c', $goal->start_time);
		$goal->end_time = date('c', $goal->end_time);

		$created = $goalService->createTimedGoal($goal);
		if ($created === false) { 
			goto err;
		}

		break;
	default:
		goto err;
}

/* link newly minted goal to each account */
foreach ($_POST['accounts'] as $aid) {
	/* todo: check that the user account belongs to said account */
	$account = new Account();
	$account->id = $aid;
	$linked = $goalService->linkAccountToGoal($account, $created);
	if (!$linked) {
		logMessage('Could not link account [id:'.$aid.'] to Goal [id:'.$created->id.']',LOG_LVL_INFO);
	}
}


header('Location: /create-goals?s=1');
exit();		

err:
header('Location: /create-goals?e=1');
exit();		

/*
goal_type
name
amount
start_time
end_time
*/