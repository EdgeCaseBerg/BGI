CREATE DATABASE bgi;
CREATE USER 'test'@'localhost' IDENTIFIED BY 'test';
GRANT ALL ON bgi.* TO 'test'@'localhost';
FLUSH PRIVILEGES;
USE bgi;

CREATE TABLE users (
	id INT(12) NOT NULL auto_increment PRIMARY KEY,
	name VARCHAR(64) NOT NULL,
	complexity INT(2) NOT NULL DEFAULT 10, 
	email VARCHAR(128),
	loginAttempts INT(2) NOT NULL DEFAULT 0
) ENGINE InnoDB;

