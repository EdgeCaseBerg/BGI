<?php
/* Init script. (The magic)
 * 
 * Responsible for very simple bootstrapping process outlined below:
 *
 * -----------------------------------------------------------------
 * 1. overrides.php
 *    If a user creates an overrides file they can redefine the 
 *    configuration and `defines` as neccesary.
 * 
 * 2. bootstrap/conf.php1
 *    sets up configuration settings and connects to database
 *    
 * 3. load all core classes
 *    loads core entitys representing the data in the program
 *
 * 4. load security classes
 *	  load up password compatability classes and etc
 *
 * 5. load all services 
 *    load up services that operate on the data that can be called from controllers
 
*/
$dirname = dirname(__FILE__);

/* Configuration for easily loading neccesary files that do not require
 * any dependencys besides Entity etc. (aka, core classes)
*/
$axiomClasses = array('Entity', 'Goal', 'User', 'GoalType', 'LineItem', 'Account');

/* Configure Services to load as well. Note this is before overrides so 
 * we can shut off services or replace them with different ones if desired
*/
$servicesToLoad = array('Authentication', 'User', 'Account', 'LineItem', 'Goal');

@include $dirname . '/overrides.php';
include $dirname . '/bootstrap/conf.php';
foreach ($axiomClasses as $axiom) {
	include $dirname . '/core/'.$axiom.'.class.php';	
}
include $dirname . '/bootstrap/database.php';
include $dirname . '/lib/password_compat/password.php';

/* Set password cryptography options for auth and user services (global var) */
$cryptOptions = array("cost" => 11);


foreach ($servicesToLoad as $service) {
	include $dirname . '/service/'.$service.'Service.php';
}

/* Attempt to Connect to the database to ensure we can */
Database::instance();


/* Clear out the session and get ready */
$_SESSION = array();