<?php 

$isAjax = isset($_GET['json']) && (intval($_GET['json']) == 1 || $_GET['json'] == 'true');
$response = new StdClass();
$response->code = 400;
$response->message = 'Problem executing request';

if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	logMessage("Invalid method", LOG_LVL_DEBUG);
	if ($isAjax) goto ajax_response;
	else header('Location: /add-lineitem?e=1');
    exit();
}

$invalid = !isset($_POST['account_id']) || empty($_POST['account_id']);
$invalid = $invalid || !isset($_POST['name']) || empty($_POST['name']);
$invalid = $invalid || !isset($_POST['amount']) || empty($_POST['amount']);
if( $invalid ) {
	if ($isAjax) goto ajax_response;
	else header('Location: /add-lineitem?e=1');
    exit();
}

include '../init.php';
ensureUserLoggedin(); 

$lineItemService = LineItemService::instance();
$lineItem = new LineItem();
$lineItem->account_id = intval($_POST['account_id']);
$lineItem->name = trim($_POST['name']);
$lineItem->amount = intval(floatval($_POST['amount'])*100);
$lineItem->created_time = date('c');

if( $lineItemService->addLineItem($lineItem) === false ) {
	if ($isAjax) goto ajax_response;
	else header('Location: /add-lineitem?e=1');
    exit();	
}

/* update the account as well */
$accountService = AccountService::instance();
$account = new Account();
$account->id = $lineItem->account_id;
$account = $accountService->getAccount($account);
if ($account === false) {
	$lineItemService->deleteLineItem($lineItem);
	if ($isAjax) goto ajax_response;
	else header('Location: /add-lineitem?e=1');
    exit();	
}

$account->balance += $lineItem->amount;
$updateSuccess = $accountService->updateAccount($account);

if (!$updateSuccess) {
	$lineItemService->deleteLineItem($lineItem);
	if ($isAjax) goto ajax_response;
	else header('Location: /add-lineitem?e=1');
    exit();	
}

if ($isAjax) goto ajax_response;
else header('Location: /add-lineitem?s=1');
exit();


ajax_response:
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>