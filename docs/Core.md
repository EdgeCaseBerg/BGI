Core Classes
========================================================================

The core classes are simple containers for the information stored in the 
database. At the base of the core classes is the notion of an `Entity`. 
Entities are simple, they are objects which have an `id` field. Because 
of this id field and contract to support the simple methods of `getId` 
and `setId`, any class inheriting from the `Entity` class can be used 
for the various [Database] functions. 

The core classes are as follow:

**Entity**
------------------------------------------------------------------------

The base class for all core classes, it provides the id field as well as 
the functions setId and getId

**Account**
------------------------------------------------------------------------

Mirror of the SQL table rows for accounts. Additional fields provided by 
this class are:  user_id,name, and balance.

**Goal**
------------------------------------------------------------------------

Mirror of the SQL table rows for goals. Additional fields provided by 
this class are: name,amount,end_time,start_time,goal_type,user_id.

**GoalType**
------------------------------------------------------------------------

Mirror of the SQL table rows for goal types. Additional fields provided 
by this class are: name.

**LineItem**
------------------------------------------------------------------------

Mirror of the SQL table rows for line items. Additional fields provided 
by this class are: account_id,name,amount, and created_time.

**User**
------------------------------------------------------------------------

Mirror of the SQL table rows for users. Additional fields provided by 
this class are: account_id,name,amount, and created_time.


[Database]:Database.md