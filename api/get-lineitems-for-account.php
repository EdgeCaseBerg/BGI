<?php

$response = new StdClass();
$response->code = 400;
$response->message = 'Invalid Request';
include '../init.php';

if ($_SERVER['REQUEST_METHOD'] != 'GET') { 	
	logMessage('Invalid method', LOG_LVL_DEBUG);
	goto send_response;
}

if( !isset($_GET['account_id']) || empty($_GET['account_id']) || !is_numeric($_GET['account_id'])) {
	logMessage('Invalid parameters',LOG_LVL_DEBUG);
	goto send_response;	
}

$account_id = intval($_GET['account_id']);
ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$accountService = AccountService::instance();
$userAccounts = $accountService->getUserAccounts($user);

$accountToQuery = null;
foreach ($userAccounts as $account) {
	if($account->id == $account_id) {
		$accountToQuery = $account;
		break;
	}
}

if( is_null($accountToQuery)) {
	logMessage("Null account to query", LOG_LVL_DEBUG);
	goto send_response;
}


$lineItemService = LineItemService::instance();
$lineItems = $lineItemService->getAccountLineItems($accountToQuery);

$response->code = 200;
if ($lineItems === false) {
	$response->data = array();
	$response->message = 'No line items on this account yet';
} else {
	$response->data = $lineItems;
	$response->message = 'Loaded';
}


send_response:
header('Content-Type: application/json');
echo json_encode($response);
exit();