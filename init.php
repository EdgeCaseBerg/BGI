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
 * 2. bootstrap/conf.php
 *    
*/
$dirname = dirname(__FILE__);

/* Configuration for easily loading neccesary files that do not require
 * any dependencys besides Entity etc. (aka, core classes)
*/
$axiomClasses = array('Entity', 'Goal');

@include $dirname . '/overrides.php';
include $dirname . '/bootstrap/conf.php';
foreach ($axiomClasses as $axiom) {
	include $dirname . '/core/'.$axiom.'.class.php';	
}
include $dirname . '/bootstrap/database.php';

/* Attempt to Connect to the database to ensure we can */
Database::instance();

