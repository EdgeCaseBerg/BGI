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

print_r($user);

/* Check that it doesn't already exist */

//header('Location: /register?e=1');