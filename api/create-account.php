<?php 


if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	logMessage("Invalid method", LOG_LVL_DEBUG);
	header('Location: /manage-categories?e=1');
    exit();
}

if( !isset($_POST['accountName']) || empty($_POST['accountName'])) {
	header('Location: /manage-categories?e=1');
    exit();
}

include '../init.php';
ensureUserLoggedin(); 

$accountService = AccountService::instance();
$account = new Account();
$account->name = trim($_POST['accountName']);
$account->balance = intval(floatval($_POST['balance'])*100);
$account->last_updated = date('c');
$account->user_id = $_SESSION['userId'];

if( $accountService->createAccount($account) === false ) {
	header('Location: /manage-categories?e=1');
    exit();	
}
header('Location: /manage-categories');
exit();
?>