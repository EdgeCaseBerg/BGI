<?php

/* Migration Script For BGI V1 -> V2
 * 
 * This is a migration script to take the flat file database structure
 * of the C + CGI version of BGI and translate it into a form that is
 * manageable from the PHP + mySQL version. In other words:
 * 
 *  _____________________________________
 * / Take the data, and PUSH it somewhere \
 * \ else                                 /
 *  --------------------------------------
 *       \    /\
 *        \  (oo)
 *           (__)
 *
 * This script should be ran from the directory of the data itself, and 
 * given some proper defines (modify the script below), the data will be
 * linked to the correct user.
 * While I could code this to run from the /data/ directory itself and
 * have it pull in ALL the users and all their info and go from there,
 * I'm not going to because I'm literally the only one who uses this
 * software, and therefore am not concerned with migrating more than a
 * single account which I will have setup before migrating.
 * 
 * Once ran, this file will produce a single SQL file which can be used
 * to send all the data from the old system, into the new. 
*/

define('USER_ID', 1);
define('ACCOUNT_FILE', dirname(__FILE__) . '/accounts');
define('BUFFER_LENGTH', 256);

$fp = fopen(ACCOUNT_FILE, 'r');

if (!$fp) {
	error_log('Could not open account directory file');
	die();
}

$accounts = array();
while ($accountInfo = fscanf($fp, "%d %64[^0-9] %lf\n")) {
	list($id, $name, $balance) = $accountInfo;
	print 'INSERT INTO accounts (user_id, name, balance,last_updated) VALUES ';
	print '(' . USER_ID . ',"' . trim($name) . '",' . intval($balance*100) . ',"' . date('c') .'");' . PHP_EOL;
	print 'SET @accountId = LAST_INSERT_ID();' . PHP_EOL;

	/* Insert items into the account while we have a reference to its ID  */	
	$afp = fopen(dirname(__FILE__) . '/' .str_replace(' ', '-',trim($name)), 'r');
	if (!$afp) {
		error_log('Could not open account file: ' .dirname(__FILE__) . '/' . $name);
	}

	while ($itemInfo = fscanf($afp, "%ld %" . BUFFER_LENGTH . "[^0-9] %lf %lf %lf\n")) { 
		list($date, $name, $amount, $lat, $lng) = $itemInfo;
		print 'INSERT INTO lineitems(account_id, name,amount,created_time) VALUES ';
		print '(@accountId, "'. trim($name) . '",' . intval($amount*100) .',"'.date('c',$date).'" );' . PHP_EOL ;
	}
	if ($afp) {
		fclose($afp);
	}
}

fclose($fp);
