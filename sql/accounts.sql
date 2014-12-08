CREATE TABLE accounts (
	id INT(20) NOT NULL auto_increment PRIMARY KEY, -- association id for foreign relationships to other tables
	user_id INT(20) NOT NULL,
	name VARCHAR(64), 
	balance INT(20), -- amount stored in cents
	last_updated TIMESTAMP, 
	INDEX (`user_id`),
	CONSTRAINT FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;