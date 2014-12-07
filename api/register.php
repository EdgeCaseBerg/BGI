<?php

/* Check that we've got the data sent to us */
if ($_SERVER['REQUEST_METHOD'] != 'POST') { 	
	logMessage("Invalid method");
	header('Location: /register?e=1');
    exit();
}

$invalid = !isset($_POST['nickname']) || !isset($_POST['ident']);
$invalid = $invalid || empty($_POST['nickname']) || empty($_POST['ident']);

if ($invalid) {
	logMessage("Invalid nickname or idents", LOG_LVL_DEBUG);
	header('Location: /register?e=1');
	exit();
}


$nickname = trim($_POST['nickname']);
$password = trim($_POST['ident']);

include '../init.php';
$userService =  UserService::instance();

$user = new User();
$user->ident = $password;
$user->nickname = $nickname;

$result = $userService->createUser($user);
if ($result) {
	header('Location: /register?s=1');
} else {
	header('Location: /register?e=1');
}
