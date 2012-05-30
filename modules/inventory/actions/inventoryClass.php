<?php
require_once(SITE_LOCATION . "/engine/database.php");

class Inventory extends DatabaseObject {
	protected static $table_name = "inventory";
	public $uid;
	public $school_uid;
	public $classroom_uid;
	public $type;
	public $manufacturer;
	public $model;
	public $serial;
	public $notes;
	public $purchase_date;
	public $last_modified;
	public $value;
	
	
	
	public static function find_all_by_room($schoolUID = 0, $roomUID = 0, $type = 0) {
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE school_uid = {$schoolUID} ";
		$sql .= "AND classroom_uid = {$roomUID}";
		
		if (!$type == 0) {
			$sql .= " AND type = '{$type}'";	
		}
		
		return self::find_by_sql($sql);
	}
	
	public function contents_by_roomUID() {
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE classroom_uid = " . $this->classroom_uid . " ";
		
		return self::find_by_sql($sql);
	}
	
	public static function find_by_uid($schoolUID = 0, $itemUID = 0) {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE school_uid = {$schoolUID} AND uid = {$itemUID} LIMIT 1");

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
	
	public static function find_types() {
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "GROUP BY type ";
		$sql .= "ORDER BY type ASC";
		
		return self::find_by_sql($sql);
	}
	
	public function value_by_room() {
		$sql  = "SELECT SUM(value) AS value FROM " . self::$table_name . " ";
		$sql .= "WHERE classroom_uid = " . $this->classroom_uid . " ";
				
		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function itemsPurchasedBeforeByType($date = NULL) {
		$sql  = "SELECT type, COUNT(*) AS uid, SUM(value) AS notes FROM " . self::$table_name . " ";
		$sql .= "WHERE purchase_date <= " . strtotime($date) . " ";
		$sql .= "AND classroom_uid <> '130' ";
		$sql .= "AND classroom_uid <> '141' ";
		$sql .= "GROUP BY type ";
		$sql .= "ORDER BY COUNT(*) ASC";
		
		return self::find_by_sql($sql);
	}
	
	public function create() {
		global $database;
	
		$sql  = "INSERT INTO inventory (";
		$sql .= "school_uid, classroom_uid, type, manufacturer, model, serial, notes, value, last_modified, purchase_date";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->school_uid) . "', '";
		$sql .= $database->escape_value($this->classroom_uid) . "', '";
		$sql .= $database->escape_value($this->type) . "', '";
		$sql .= $database->escape_value($this->manufacturer) . "', '";
		$sql .= $database->escape_value($this->model) . "', '";
		$sql .= $database->escape_value($this->serial) . "', '";
		$sql .= $database->escape_value($this->notes) . "', '";
		$sql .= $database->escape_value($this->value) . "', '";
		$sql .= date('Y-m-d H:i:s') . "', '";
		$sql .= $database->escape_value($this->purchase_date) . "')";
		
		// insert the record to the database
		if ($database->query($sql)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	public function update() {
		global $database;
		
		$sql  = "UPDATE inventory SET ";
		$sql .= "classroom_uid = '" . $database->escape_value($this->classroom_uid) . "', ";
		$sql .= "type = '" . $database->escape_value($this->type) . "', ";
		$sql .= "manufacturer = '" . $this->manufacturer . "', ";
		$sql .= "model = '" . $database->escape_value($this->model) . "', ";
		$sql .= "value = '" . $database->escape_value($this->value) . "', ";
		$sql .= "last_modified = '" . date('Y-m-d H:i:s') . "', ";
		$sql .= "notes = '" . $database->escape_value($this->notes) . "' ";
		$sql .= "WHERE uid = " . $this->uid;
		
		
		// update the record in the database
		$database->query($sql);

	}
	
	public function touchItem() {
		global $database;
		
		$sql  = "UPDATE inventory SET ";
		$sql .= "last_modified = '" . date('Y-m-d H:i:s') . "' ";
		$sql .= "WHERE uid = " . $this->uid;
		
		// update the record in the database
		$database->query($sql);
	}
	
	public function findByString($searchString = NULL) {
		global $database;
		
		// find jobs with the right search term
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE uid LIKE '%" . $database->escape_value($searchString) . "%' ";
		$sql .= "OR type LIKE '%" . $database->escape_value($searchString) . "%' ";
		$sql .= "OR manufacturer LIKE '%" . $database->escape_value($searchString) . "%' ";
		$sql .= "OR model LIKE '%" . $database->escape_value($searchString) . "%' ";
		$sql .= "OR serial LIKE '%" . $database->escape_value($searchString) . "%' ";
		$sql .= "OR notes LIKE '%" . $database->escape_value($searchString) . "%' ";
				
		return self::find_by_sql($sql);
	}
} // end class Inventory

?>