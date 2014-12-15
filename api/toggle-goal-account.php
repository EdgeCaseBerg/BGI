<?php
$response = new StdClass();
$response->code = 400;
$response->message = 'There was an error processing your request';

header('Content-Type: application/json');
error_log($_SERVER['REQUEST_METHOD']);
if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	$response->message = 'Invalid Method';
	goto sendResponse;
}

if( !isset($_POST['account_id']) || empty($_POST['account_id']) ||
	!isset($_POST['goal_id']) 	 || empty($_POST['goal_id']) 	|| 
	!isset($_POST['state']) || ($_POST['state'] != 'T' && $_POST['state'] != 'F') ) {
	goto sendResponse;
}

include '../init.php';
ensureUserLoggedin(); 

$accountService = AccountService::instance();
$account = new Account();
$account->id = intval($_POST['account_id']);

$account = $accountService->getAccount($account);

if ($account === false) {
	$response->code = 404;
	$response->message = 'Account not found';
	goto sendResponse;
}

$user = new User();
$user->id = $_SESSION['userId'];

if ($user->id != $account->user_id) {
	$response->code = 409;
	$response->message = 'Account Forbidden';
	goto sendResponse;
}

$goal = new Goal();
$goal->id = intval($_POST['goal_id']);

$goalService = GoalService::instance();
$goal = $goalService->getGoal($goal);

if( $goal === false) {
	$response->code = 404;
	$response->message = 'Goal not found';
	goto sendResponse;
}

if ($user->id != $goal->user_id) {
	$response->code = 409;
	$response->message = 'Goal Forbidden';
	goto sendResponse;
}

if ($_POST['state'] == 'T') {
	$linked = $goalService->linkAccountToGoal($account, $goal);
} else {
	$linked = $goalService->removeAccountFromGoal($account, $goal);
}

if ($linked === false) {
	$response->message = 'Could not complete action';
} else {
	$response->code = 200;
	$response->message = 'Successful Action';	
}

sendResponse:
echo json_encode($response);
exit();