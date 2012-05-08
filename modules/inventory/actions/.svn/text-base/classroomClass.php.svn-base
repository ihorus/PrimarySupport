<?php
require_once(SITE_LOCATION . "/engine/database.php");

class Classroom extends DatabaseObject {
	protected static $table_name = "classrooms";
	public $uid;
	public $school_uid;
	public $name;
	public $teacher;
	public $notes;
	
	public static function find_by_sql($sql="") {
		global $database;
		
		$result_set = $database->query($sql);
		$object_array = array();
		while ($row = $database->fetch_array($result_set)) {
			global $database;
			$object_array[] = self::instantiate($row);
		}
		
		return $object_array;
	}
	
	private static function instantiate($record) {
		
	$object = new self;
		foreach ($record as $attribute=>$value) {
			if ($object->has_attribute($attribute)) {
				$object->$attribute = $value;
			}
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
		// get_object_vars returns as associative array with all attributes
		// (incl. private ones!) as the keys and their current values as the value
		$object_vars = $this->attributes($this) ;
		
		// we don't care about the value, we just want to know if the key exists
		// will return true or false
		return array_key_exists($attribute, $object_vars);
	}
	
	private function attributes($attribute) {
		return get_object_vars($this);
	}
	
	public static function find_by_uid($roomUID = '0') {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = {$roomUID} LIMIT 1");

		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_all_by_school($schoolUID) {
		global $database;
		
		if (isset($schoolUID)) {
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE school_uid = {$schoolUID} ";
		$sql .= "ORDER by name ASC";

		return self::find_by_sql($sql);
		}
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO classrooms (";
		$sql .= "school_uid, name, teacher, notes";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->school_uid) . "', '";
		$sql .= $database->escape_value($this->name) . "', '";
		$sql .= $database->escape_value($this->teacher) . "', '";
		$sql .= $database->escape_value($this->notes) . "')";
		
		// insert the record to the database
		$database->query($sql);
	}
} // end class Classroom

?>