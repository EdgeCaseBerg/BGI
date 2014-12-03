CREATE DATABASE budget;

CREATE USER 'buser'@'localhost' IDENTIFIED BY 'money';
GRANT ALL ON budget.* TO 'buser'@'localhost';
FLUSH PRIVILEGES;

USE budget;