CREATE TABLE lineitems (
	id INT(20) NOT NULL auto_increment PRIMARY KEY, -- association id for foreign relationships to other tables
	account_id INT(20) NOT NULL,
	name VARCHAR(128), 
	amount INT(20), -- amount stored in cents
	created_time TIMESTAMP,
	INDEX (`account_id`),
	CONSTRAINT FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;

