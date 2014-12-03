CREATE TABLE goal_types (
	id INT(20) NOT NULL auto_increment PRIMARY KEY,
	name VARCHAR(16) NOT NULL
) ENGINE InnoDB;

-- Goal types are important, should be defined in code as well
INSERT INTO goal_types (name) VALUES ("timed");
INSERT INTO goal_types (name) VALUES ("weekly");
INSERT INTO goal_types (name) VALUES ("monthly");

CREATE TABLE goals (
	id INT(20) NOT NULL auto_increment PRIMARY KEY, -- association id for foreign relationships to other tables
	name VARCHAR(128), 
	amount INT(20), -- amount stored in cents
	end_time TIMESTAMP NULL, -- when goal type is timed this is valid
	start_time TIMESTAMP NULL, -- when goal type is timed this is valid
	goal_type INT (20) NOT NULL,
	INDEX (`goal_type`),
	CONSTRAINT FOREIGN KEY (`goal_type`) REFERENCES `goal_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;

CREATE TABLE account_goals (
	goal_id INT(20) NOT NULL,
	account_id INT(20) NOT NULL,
	CONSTRAINT FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FOREIGN KEY (`goal_id`) REFERENCES `goals` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE InnoDB;

