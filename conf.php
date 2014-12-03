<?php
/* Configure application settings here. 
 * - Logging level and function to call to log
 * - redefine CONFIG_FILE_PATH as neccesary in a file prior to this one
*/
define('LOG_LVL_DEBUG', 4);
define('LOG_LVL_VERBOSE', 3);
define('LOG_LVL_INFO',  2);
define('LOG_LVL_WARN',  1);
define('LOG_LVL_ERROR', 0);

define('CURRENT_LOG_LEVEL', LOG_LVL_DEBUG);

function logMessage ($message, $level) {
	if(CURRENT_LOG_LEVEL <= $level ) {
		if(is_array($message) || is_object($message)) $message = print_r($message, 1);
		error_log($message);
	}
}

logMessage("Configuration Loaded", LOG_LVL_DEBUG);
logMessage("Current log level is: " . CURRENT_LOG_LEVEL, LOG_LVL_DEBUG);

//TODO: Later on, if apc cache used, cache conf so to avoid I.O.

if (!defined('CONFIG_FILE_PATH')) {
	define('CONFIG_FILE_PATH', dirname(__FILE__) . '/conf.json');
}

$confFileContents = @file_get_contents(CONFIG_FILE_PATH);

if ($confFileContents === FALSE) {
	error_log("Could not open configuration file. Please check CONFIG_FILE_PATH");
	header('HTTP/1.1 503 Service Unavailable');
	die('Internal Error' . PHP_EOL);
}

$configuration = json_decode($confFileContents);

define('DB_HOST', $configuration->database->host);
define('DB_NAME', $configuration->database->name);
define('DB_USER', $configuration->database->user);
define('DB_PASS', $configuration->database->pass);

/* Types are created during sql install script, please view them for the correct
 * id to set. (open mysql and run SELECT * FROM GOALS TYPES and set accordingly)
 * if you have done this fresh, the id's are simply 1,2,3
*/
define('GOAL_TYPE_TIMED', $configuration->goals->timedId);
define('GOAL_TYPE_WEEKLY', $configuration->goals->weeklyId);
define('GOAL_TYPE_MONTHLY', $configuration->goals->monthlyId);

//TODO: Could connect to database and verify defines here, but that can come later

/* Destroy sensitive variables, only defines should be used from here out */
unset($confFileContents);
unset($configuration);