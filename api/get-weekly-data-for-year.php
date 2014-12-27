<?php

$response = new StdClass();
$response->code = 400;
$response->message = 'Invalid Request';
include '../init.php';

if ($_SERVER['REQUEST_METHOD'] != 'GET') { 	
	logMessage('Invalid method', LOG_LVL_DEBUG);
	goto send_response;
}

//Todo: add get param to go back further than 1 year

ensureUserLoggedin(); 

$user = new User();
$user->id = $_SESSION['userId'];

$metricsService = MetricsService::instance();

$results = $metricsService->getLastYearOfData($user);

$response->code = 200;
if ($results === false) {
	$response->data = array();
	$response->message = 'No data to load';
} else {
	$response->data = $results;
	$response->message = 'Loaded';
}


send_response:
header('Content-Type: application/json');
echo json_encode($response);
exit();