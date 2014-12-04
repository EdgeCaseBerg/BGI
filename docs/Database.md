Database
========================================================================

Example Usage:

	
	//Retrieve database instance
	$db = Database::instance();
	
	//Using any child type of Entity (core/Entity.class.php)
	$g = new Goal();
	$g->user_id = 1;
	$g->name = "Test";
	$g->goal_type = GOAL_TYPE_WEEKLY;
	$g->amount = 1000;

	//Call insert to create, false or your object back
	$db->insert($g);

	$g->name = "Test Updated";
	//Call update to update Entity, false or your object back
	$db->update($g);

	//Call delete with an Entity, T or F for deleted successfully or not
	$db->delete($g);

	//To select, set id of empty entity and observe
	$g->id = 13;
	$g2 = $db->get($g);

	//Get all goals
	$goals = $db->all($g);

	//Select goal types using custom query
	$s = $db->custom("SELECT * FROM goaltypes");

	//Select custom query using parameters, returns array of objects
	$s = $db->custom("SELECT * FROM accounts where id = :id", array(':id' => 1));

	//Insert,update,delete using custom, returns true or false
	$db->custom("INSERT INTO goaltypes (id, name) VALUES (:id,:name)", array(':id' => -1, ':name' => 'test'));


The database class within the bootstrap folder is a simple PDO wrapper 
that allows for basic CRUD operations on any Entity. It also provides a 
few simple operations to retrieve Entities, see below for a full list:


### Database Functions

**all**

Parameters: `Entity`  
Returns: Array of the given Entity's type. 

------------------------------------------------------------------------

**get**

Parameters: `Entity` with `id` field set to desire item  
Returns: Desired Entity or false if it does not exist

------------------------------------------------------------------------

static::**instance**

Parameters:None 
Returns: instance of database to use

------------------------------------------------------------------------

**insert**

Parameters: `Entity`  
Returns: Desired Entity or false if failure to insert occurs

------------------------------------------------------------------------

**update**

Parameters: `Entity` with `id` field set
Returns: Desired Entity or false if it does not update

------------------------------------------------------------------------

**delete**

Parameters: `Entity` with `id` field set  
Returns: True if deletion is successful, false otherwise

------------------------------------------------------------------------

**where**

Parameters: `Entity`, `fieldName`, `fieldValue`  
Returns: Array of Entities matching where Entity table has fieldName = fieldValue

------------------------------------------------------------------------

**custom** 

Parameters: query with `:key`-ed parameters, array with key => values 
Returns: array of objects if query is a select statement, true or false otherwise.


### More examples:

Creating a user, account, and 2 line items then cleaning up everything:

	$db = Database::instance();
	$u = new User();
	$u->ident = 'Test';

	$db->insert($u);

	$a = new Account();
	$a->user_id = $u->id;
	$a->balance = 0;
	$a->name = 'Test';

	$db->insert($a);

	$l = new LineItem();
	$l->account_id = $a->id;
	$l->name = 'Test Item 1';
	$l->amount = 100;
	$l->created_time = date('c');

	$db->insert($l);

	$l->name = 'Test Item 2';
	$l->amount = 200;

	$db->insert($l);

	$items = $db->where(new LineItem(), 'account_id', $a->id);

	$db->delete($u); //remove all