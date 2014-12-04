UserService
========================================================================

Example:

	$us = UserService::instance();
	
	$user = new User();
	$user->nickname = 'TestUser';
	$user->ident = 'TestPassword';
	
	print $us->createUser($user) . PHP_EOL;
	print $us->deleteUser($user) . PHP_EOL; 

The UserService is a simple service meant to handle the in and outs of 
interacting with the user at the database level. It provides automatic
password creation and is also available to delete user's as neccesary.

Methods
------------------------------------------------------------------------


**createUser**  
Parameters: User Entity  
Returns True or False, also updates User state, erases `ident` field, 
creates `hash` field. The hash field is the hashed password and is the 
hash of `ident`. Also updates the `last_seen` field.


**deleteUser**  
Parameters: User Entity  
Returns true or false on whether the user was deleted or not.

