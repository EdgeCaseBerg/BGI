<?php
$response = new StdClass();
$response->code = 400;
$response->message = 'There was an error processing your request';

header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	$response->message = 'Invalid Method';
	goto sendResponse;
}

if( !isset($_POST['account_id']) || empty($_POST['account_id'])) {
	goto sendResponse;
}

include '../init.php';
ensureUserLoggedin(); 

$accountService = AccountService::instance();
$accountToDelete = new Account();
$accountToDelete->id = intval($_POST['account_id']);

$accountExists = false;
$user = new User();
$user->id = $_SESSION['userId'];
$userAccounts = $accountService->getUserAccounts($user);
foreach ($userAccounts as $account) {
	if ($accountToDelete->id == $account->id) {
		$accountExists = true;
	}
}

if (!$accountExists) {
	$response->code = 409;
	$response->message = 'Account Forbidden';
	goto sendResponse;
}

if( !$accountService->deleteAccount($accountToDelete) ) {
	$response->message = 'Could not delete account';
	goto sendResponse;
}

logMessage('Deleted Account Successfully [id:'.$accountToDelete->id.']',LOG_LVL_DEBUG);
$response->code = 200;
$response->message = 'Successful Deletion';

sendResponse:
echo json_encode($response);
exit();