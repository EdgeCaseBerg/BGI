<?php
/* It is expected that conf is called before this, if it is not, expect
 * the unexpected (as well as the spanish inquisition) 
*/

// In debug mode this class will throw exceptions, non debug it will simply log them
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
		return self::$instance;
	}

	final private function keyMaker($key) {
		return ':'.$key;
	}

	final private function determinePDOType($value) {
		switch (gettype($value)) {
			case 'boolean':
				return PDO::PARAM_BOOL;
			case 'NULL':
				return PDO::PARAM_NULL;
			case 'integer':
				return PDO::PARAM_INT;
			case 'string':
			default:
				/* No such thing as doubles, so pass as str */
				return PDO::PARAM_STR;
		}
	}

	private function getEntityTableName(Entity $genericObj) {
		return strtolower(get_class($genericObj)) . 's'; //plurals aren't trying to appease the pedantic
	}

	public function insert(Entity $genericObj) {
		$tableName = $this->getEntityTableName($genericObj);
		$objInfo   = get_object_vars($genericObj);
		
		unset($objInfo['id']); //remove id so we don't try to assign the autogen

		$keys = array_keys($objInfo);
		$sql = 	'INSERT INTO ' . $tableName . ' ( ' . implode(',', $keys) . ' ) ' .
				'VALUES (' . implode(',', array_map(array($this,'keyMaker'), $keys)) . ' ) ';
		$statement = $this->pdo->prepare($sql );

		foreach ($objInfo as $key => $value) {
			$statement->bindValue(':'.$key, $value, $this->determinePDOType($value) );
		}

		if ($statement->execute() == FALSE ) {
			logMessage('Failed to execute database query ', LOG_LVL_WARN);
			logMessage($statement->errorInfo(), LOG_LVL_DEBUG);
			return false;
		} 

		$newId = $this->pdo->lastInsertId();
		$genericObj->setId($newId);

		logMessage("Created new database row. "  . $tableName .'[id:' . $genericObj->getId() .']', LOG_LVL_VERBOSE);
		logMessage($genericObj, LOG_LVL_DEBUG);
		
		return $genericObj;
	}

	/* update based on entity's id */
	public function update(Entity $genericObj) {
		$tableName = $this->getEntityTableName($genericObj);
		$objInfo   = get_object_vars($genericObj);

		unset($objInfo['id']); //remove id for other field purposes
		
		$sql = 'UPDATE ' . $tableName . ' SET '; 
		$first = true;
		foreach ($objInfo as $key => $value) {
			if(!$first) $sql .= ',';
			$sql .= $key . ' = :' . $key;	
			$first = false;
		};

		$sql .= ' WHERE id = :id';

		$statement = $this->pdo->prepare($sql);

		$objInfo['id'] = $genericObj->getId();

		foreach ($objInfo as $key => $value) {
			$statement->bindValue(':'.$key, $value, $this->determinePDOType($value));
		}

		if ($statement->execute() == FALSE ) {
			logMessage('Failed to execute database query ', LOG_LVL_WARN);
			logMessage($statement->errorInfo(), LOG_LVL_DEBUG);
			return false;
		} 

		if ($statement->rowCount() != 1 ){
			logMessage('Failed to update database row', LOG_LVL_WARN);
			return false;
		}

		logMessage('Updated database row. '  . $tableName .'[id:' . $genericObj->getId() .']', LOG_LVL_VERBOSE);
		logMessage($genericObj, LOG_LVL_DEBUG);

		return $genericObj;

	}

	public function delete(Entity $genericObj) {
		$tableName = $this->getEntityTableName($genericObj);

		$sql = 'DELETE FROM ' . $tableName . ' WHERE id = :id';
		$statement = $this->pdo->prepare($sql);
		$statement->bindValue(':id', $genericObj->getId());

		if ($statement->execute() == FALSE ) {
			logMessage('Failed to execute database query ', LOG_LVL_WARN);
			logMessage($statement->errorInfo(), LOG_LVL_DEBUG);
			return false;
		} 

		if ($statement->rowCount() != 1 ){
			logMessage('Failed to delete database row', LOG_LVL_WARN);
			return false;
		}

		logMessage("Deleted database row. " . $tableName .'[id:' . $genericObj->getId() .']', LOG_LVL_VERBOSE);
		logMessage($genericObj, LOG_LVL_DEBUG);

		return true;
	}

	/* Pass a blank entity with the id you want of the class you want */
	public function get(Entity $genericObj) {
		$tableName = $this->getEntityTableName($genericObj);
		$sql = 'SELECT * FROM ' . $tableName . ' WHERE id = :id';
		$statement = $this->pdo->prepare($sql);
		$statement->bindValue(':id', $genericObj->getId());

		if ($statement->execute() == FALSE ) {
			logMessage('Failed to execute database query ', LOG_LVL_WARN);
			logMessage($statement->errorInfo(), LOG_LVL_DEBUG);
			return false;
		} 

		if ($statement->rowCount() != 1 ){
			//no logging since it's just a 404
			logMessage('No entity found for query ' . $tableName .'[id:'.$genericObj->getId().']', LOG_LVL_DEBUG);
			return false;
		}		
		logMessage('Retrieved entity from database. ' . $tableName .'[id:'.$genericObj->getId().']', LOG_LVL_VERBOSE);
		$genericObj = $statement->fetchObject(get_class($genericObj));
		logMessage($genericObj, LOG_LVL_DEBUG);
		return $genericObj;
	}

	public function all(Entity $genericObj) { 
		$tableName = $this->getEntityTableName($genericObj);
		$sql = 'SELECT * FROM ' . $tableName;
		$statement = $this->pdo->prepare($sql);

		if ($statement->execute() == FALSE ) {
			logMessage('Failed to execute database query ', LOG_LVL_WARN);
			logMessage($statement->errorInfo(), LOG_LVL_DEBUG);
			return array();
		} 

		$entities = $statement->fetchAll(PDO::FETCH_CLASS, get_class($genericObj));
		logMessage('Retrieved ' . count($entities) . ' rows from database ' . $tableName, LOG_LVL_VERBOSE);
		return $entities;
	}

}

?>