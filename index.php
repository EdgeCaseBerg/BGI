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

/* You might ask yourself, oh god why is there this list its so annoying 
 * to update, wah, and yeah, yes it is. because like hell you want someone
 * including a file you didn't mean for them to do so.
*/
$allowedViewScripts = array(
	'index',
	'register',
	'login',
	'logout',
	'home',
	'manage-categories',
	'manage-lineitems',
	'add-lineitem',
	'create-goals',
	'manage-goals'
);
if (!in_array($viewName, $allowedViewScripts)) {
	$viewName = '404';
}

$controller->render($viewName);