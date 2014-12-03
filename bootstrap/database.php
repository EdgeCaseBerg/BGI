<?php
/* It is expected that conf is called before this, if it is not, expect
 * the unexpected (as well as the spanish inquisition) 
 * TODO: create entity class for basic CRUD
*/

class Database extends StdClass {
	private $pdo = NULL;
	static $instance = NULL;
	
	private function __construct() {
		try{
			$this->pdo = new PDO('mysql:host='.DB_HOST.';dbname=' . DB_NAME, DB_USER, DB_PASS);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, DEBUG_MODE ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT);
		} catch (PDOException $e) {
			logMessage("Could not connect to database. {$e->getMessage()}" ,LOG_LVL_ERROR);
			internal_error();
		}
		logMessage("Database Connection Made", LOG_LVL_DEBUG);
	}

	/* Automatically close connection */
	public function __destruct() {
		$this->pdo = NULL;
		self::$instance = NULL;
		logMessage("Database Connection Closed", LOG_LVL_DEBUG);
	}

	final public static function instance(){
		if (is_null(self::$instance)) {
			self::$instance = new Database();
		}
	}
}

?>