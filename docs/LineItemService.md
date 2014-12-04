LineItemService
========================================================================

Example:

	$us = UserService::instance();

	$user = new User();
	$user->nickname = 'Test';
	$user->ident = 'password';

	$us->createUser($user);

	$a1 = new Account();
	$a1->user_id = $user->id;
	$a1->name = 'Test 1';
	$a1->balance = 10;

	$acs = AccountService::instance();
	$acs->createAccount($a1);

	$lis = LineItemService::instance();
	$l = new LineItem();

	$l->account_id = $a1->id;
	$l->name = 'Lunch';
	$l->amount = 10*100; //cents
	$l->created_time = date('c');

	$lis->addLineItem($l);

	$lineItems = $lis->getUserLineItems($user);

	print_r($lineItems);

	$us->deleteUser($user);

Methods
------------------------------------------------------------------------

**getUserLineItems**  

Parameters: User Entity  
returns: false or an array of line items

**getAccountLineItems**  

Parameters: Account Entity
returns: false or an array of line items

**addLineItem**  

Parameters: LineItem Entity  
returns: false or the updated LineItem object

**deleteLineItem**  

Parameters: LineItem to be deleted (id set)  
returns: true or false based on LineItem

