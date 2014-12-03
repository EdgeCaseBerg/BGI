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

@include $dirname . '/overrides.php';
include $dirname . '/bootstrap/conf.php';
//TODO: include entity model here that all models will use.
include $dirname . '/bootstrap/database.php';

$db = Database::instance();


?>