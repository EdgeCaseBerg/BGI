<?php

include '../init.php';

$authService = AuthenticationService::instance();
$authService->logout();
header('Location: /logout');

exit();