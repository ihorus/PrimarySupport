<?php

require_once("database.php");

class Group extends DatabaseObject {
	
	protected static $table_name = "groups";
	public $uid;
	public $name;
	public $type;
	public $headteacher;
	
	public $address1;
	public $address2;
	public $address3;
	public $address4;
	public $address5;
	
	public $phone1;
	public $fax1;
	public $email1;
	public $distance;
	
	public $active;
	
	public static function find_all($activeOnly = "TRUE") {
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		
		if ($activeOnly == "TRUE") {
			$sql .= "WHERE active = '1' ";
		}
		$sql .= "ORDER BY name ASC";
		
		return self::find_by_sql($sql);
	}
	

	public static function find_by_uid($schoolUID = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE uid = '" . $schoolUID . "' ";
		$sql .= "LIMIT 1"; 
				
		$result_array = self::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_name($schoolName = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE name = '" . $schoolName . "' ";
		$sql .= "LIMIT 1"; 
				
		$result_array = self::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
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
	
	
	public function schoolname() {
		if (isset($this->uid)) {
			return $this->name;
		} else {
			return "Unknown School Name";
		}
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
		$object_vars = get_object_vars($this) ;
		
		// we don't care about the value, we just want to know if the key exists
		// will return true or false
		return array_key_exists($attribute, $object_vars);
	}
	
	public function totalActiveJobs() {
		$jobs = Support::find_subset($active = TRUE, $schoolUID = $this->uid);
		
		$totalJobs = count($jobs);
		
		return $totalJobs;
	}
}
?>