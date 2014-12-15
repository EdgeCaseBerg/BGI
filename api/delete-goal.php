<?php
$response = new StdClass();
$response->code = 400;
$response->message = 'There was an error processing your request';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	$response->message = 'Invalid Method';
	goto sendResponse;
}

if( !isset($_POST['goal_id']) || empty($_POST['goal_id'])) {
	goto sendResponse;
}

include '../init.php';
ensureUserLoggedin(); 

$goalService = GoalService::instance();
$goalToDelete = new Goal();
$goalToDelete->id = intval($_POST['goal_id']);

$goalExists = false;
$user = new User();
$user->id = $_SESSION['userId'];
$userGoals = $goalService->getUserGoals($user);
foreach ($userGoals as $goal) {
	if ($goalToDelete->id == $goal->id) {
		$goalExists = true;
	}
}

if (!$goalExists) {
	$response->code = 409;
	$response->message = 'Goal Forbidden';
	goto sendResponse;
}

if( !$goalService->deleteGoal($goalToDelete) ) {
	$response->message = 'Could not delete goal';
	goto sendResponse;
}

logMessage('Deleted Goal Successfully [id:'.$goalToDelete->id.']',LOG_LVL_DEBUG);
$response->code = 200;
$response->message = 'Successful Deletion';

sendResponse:
echo json_encode($response);
exit();