<?php
/* Include whatever view we need */
require_once dirname(__FILE__) . '/init.php';

$viewName = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'index';

logMessage('Attempting to load View: ' . $viewName, LOG_LVL_DEBUG);

$one = 1;
$viewName = str_replace('/', '', $viewName,$one); // remove first / in the url
if (strpos($viewName, '?') !== false) {
	$viewName = explode('?', $viewName);
	$viewName = $viewName[0];
}

if (strpos($viewName, '.php') !== false) {
	$viewName = str_replace('.php', '', $viewName);
}

if(empty($viewName)) $viewName = 'index';

$allowedViewScripts = array(
	'index',
	'register',
	'login'
);
if (!in_array($viewName, $allowedViewScripts)) {
	$viewName = '404';
}

$controller->render($viewName);