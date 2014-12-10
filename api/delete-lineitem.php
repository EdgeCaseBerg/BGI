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

if( !isset($_POST['lineItemId']) || empty($_POST['lineItemId'])) {
	goto sendResponse;
}

if( !isset($_POST['account_id']) || empty($_POST['account_id'])) {
	goto sendResponse;
}

include '../init.php';
ensureUserLoggedin(); 

$accountService = AccountService::instance();
$itemToDelete = new LineItem();
$itemToDelete->id = intval($_POST['lineItemId']);
$itemToDelete->account_id = intval($_POST['account_id']);

$accountExists = false;
$user = new User();
$user->id = $_SESSION['userId'];

/* Make sure item belongs to the user  */
$userAccounts = $accountService->getUserAccounts($user);
$accountToUpdate = null;
foreach ($userAccounts as $account) {
	if ($itemToDelete->account_id == $account->id) {
		$accountExists = true;
		$accountToUpdate = $account;
	}
}

if (!$accountExists) {
	$response->code = 409;
	$response->message = 'Account Forbidden';
	goto sendResponse;
}

/* Check this account for that line item (we're being careful!) */
$lineItemService = LineItemService::instance();
$accountToQuery = new Account();
$accountToQuery->id = $itemToDelete->account_id;
$lineItems = $lineItemService->getAccountLineItems($accountToQuery);

if ($lineItems === false) {
	$response->code = 404;
	$response->message = 'Line items not found';
	goto sendResponse;
}

$lineItemAmount = null;
foreach ($lineItems as $lineItem) {
	if ($lineItem->id == $itemToDelete->id) {
		$lineItemAmount = $lineItem->amount;
	}
}

if (is_null($lineItemAmount)) {
	$response->code = 404;
	$response->message = 'Line item not found';
	goto sendResponse;
}

if( !$lineItemService->deleteLineItem($itemToDelete) ) {
	$response->message = 'Could not delete item';
	$response->code = 500;
	goto sendResponse;
}


$accountToUpdate->balance = $accountToUpdate->balance - $lineItemAmount;
$result =  $accountService->updateAccount($accountToUpdate);

if( $result === false ) {
	logMessage('Failed to update account [id: ' . $account_id . '] and remove ' . $lineItemAmount . ' from balance');
}

logMessage('Deleted LineItem Successfully [id:'.$itemToDelete->id.']',LOG_LVL_DEBUG);
$response->code = 200;
$response->message = 'Successful Deletion';

sendResponse:
echo json_encode($response);
exit();