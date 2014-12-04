AccountServic
========================================================================

Example:
	
	//Initial setup of a user
	$us = UserService::instance();

	$user = new User();
	$user->nickname = 'Test';
	$user->ident = 'password';

	$us->createUser($user);

	$a1 = new Account();
	$a1->user_id = $user->id;
	$a1->name = 'Test 1';
	$a1->balance = 10;

	$a2 = new Account();
	$a2->user_id = $user->id;
	$a2->name = 'Test 2';
	$a2->balance = 20;

	//Create accounts 
	$acs = AccountService::instance();
	$acs->createAccount($a1);
	$acs->createAccount($a2);

	//Retrieve the accounts 
	$accounts = $acs->getUserAccounts($user);

	print_r($accounts);

	//Cascade will delete all accounts
	$us->deleteUser($user);

Methods
------------------------------------------------------------------------

**createAccount**  
Parameters: Account Entity
Returns: False or the entity with the id field set.

**deleteAccount**  
Parameters: Account Entity
Returns: true or false


**getUserAccounts**  
Parameters: User Entity
Returns: false or an array of user accounts

