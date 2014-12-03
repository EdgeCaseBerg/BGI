CREATE TABLE users (
	id INT(20) NOT NULL auto_increment PRIMARY KEY, -- association id for foreign relationships to other tables
	ident CHAR(64), -- sha256 hashed identifier,
	hash CHAR(255), -- salt to apply during creation of ident
	nickname VARCHAR(32), 
	last_seen TIMESTAMP 
) ENGINE InnoDB;