AuthenticationService
========================================================================

Example:

	$user = new User();
	$user->nickname = 'TestUser';
	$user->ident = 'TestPassword';

	$as = AuthenticationService::instance();
	$as->login($user);

	print_r($_SESSION);

	$as->logout();

The Authentication service is in charge of authenticating the user and a 
small amount of session management. 

Methods
------------------------------------------------------------------------

**login**  

Parameters: User Entity with `ident` field set to plaintext password  
Returns: true or false, also updates User with ident field removed and 
data populated from the database if succesful.


**logout**  
Parameters: None  
Returns: nothing. Destroys current session and unsets all session fields


(static) **isUserLoggedIn**  
Parameters: None  
Returns: true or false if the user is logged in or not