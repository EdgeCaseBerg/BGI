<?php
/* Include whatever view we need */

$viewName = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'index';
$one = 1;
$viewName = str_replace('/', '', $viewName,$one); // remove first / in the url

if(empty($viewName)) $viewName = 'index';

$allowedViewScripts = array('index');
if (!in_array($viewName, $allowedViewScripts)) {
	$viewName = '404';
}
require_once dirname(__FILE__) . '/init.php';
$controller->render($viewName);

