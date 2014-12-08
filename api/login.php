<?php

if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	logMessage("Invalid method");
	header('Location: /login?e=1');
    exit();
}

$invalid = !isset($_POST['nickname']) || !isset($_POST['ident']);
$invalid = $invalid || empty($_POST['nickname']) || empty($_POST['ident']);

if ($invalid) {
	logMessage("Invalid nickname or idents", LOG_LVL_DEBUG);
	header('Location: /login?e=1');
	exit();
}


$nickname = trim($_POST['nickname']);
$password = trim($_POST['ident']);


include '../init.php';
$user = new User();
$user->nickname = $nickname;
$user->ident = $password;

$password = 'xxxx';//overwrite
unset($password);

$authService = AuthenticationService::instance();

$loggedIn = $authService->login($user);
if ($loggedIn) {
	header('Location: /login.php?s=1');
} else {
	header('Location: /login.php?e=1');
}
exit();