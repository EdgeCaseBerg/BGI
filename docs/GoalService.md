GoalService
========================================================================

Example:

	// Setup
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

	$g = new Goal();
	$g->name = 'goal';
	$g->amount = 100*150;
	$g->user_id = $user->id;

	$gs = GoalService::instance();

	// Create a weekly goal
	$gs->createWeeklyGoal($g);

	// an goals can be linked to multiple accounts and etc
	print $gs->linkAccountToGoal($a1, $g) . PHP_EOL;
	print $gs->removeAccountFromGoal($a1, $g) . PHP_EOL;

	// clean up
	$us->deleteUser($user);

Methods
------------------------------------------------------------------------

**createWeeklyGoal**  

Parameters: Goal  
Returns: false or the goal object with id filled in by the database

**createMonthlyGoal**  

Parameters: Goal  
Returns: false or the goal object with id filled in by the database

**createTimedGoal**  

Parameters: Goal  
Returns: false or the goal object with id filled in by the database

**getUserGoals**  

Parameters: User  
Returns: returns an array of goals, sorted by type

**deleteGoal**  

Parameters: Goal  
Returns: true or false

**linkAccountToGoal**  

Parameters: Account, Goal  
Returns: true or false

**removeAccountFromGoal**  

Parameters: Account, Goal  
Returns: true or false
