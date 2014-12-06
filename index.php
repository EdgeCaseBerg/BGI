<?php
/* Include whatever view we need */
require_once dirname(__FILE__) . '/init.php';

$viewName = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'index';

logMessage('Attempting to load View: ' . $viewName, LOG_LVL_DEBUG);

$one = 1;
$viewName = str_replace('/', '', $viewName,$one); // remove first / in the url

if(empty($viewName)) $viewName = 'index';

$allowedViewScripts = array(
	'index',
	'register'
);
if (!in_array($viewName, $allowedViewScripts)) {
	$viewName = '404';
}

$controller->render($viewName);

