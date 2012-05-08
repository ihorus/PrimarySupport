<?php
require_once("database.php");

class AvailableSettings extends DatabaseObject {

	protected static $table_name = "availableSettings";
//	protected static $available_settings_table = "availableSettings";
	public $uid;
	public $settingName;
	public $settingType;
	public $settingDefaultValue;
	public $settingFriendlyName;
	public $settingSecurity;
	
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
	
	public function get_setting($setting = NULL) {
		$sql  = "SELECT * FROM availableSettings ";
		$sql .= "WHERE uid = '{$setting}' ";			// check for the setting UID
		$sql .= " OR settingName = '{$setting}' ";	// check for the setting name too
		$sql .= "LIMIT 1";
		
		$result_array = self::find_by_sql($sql);
		
		return $result_array; 
	}

	public function updateSetting($settingName = NULL, $settingValue = NULL) {
		global $database;
		
		if ($settingValue == "" || $settingValue == NULL) {
			$settingValue = "FALSE";
		}
		
		if (is_array($settingValue)) {
			$settingValue = implode(",",$settingValue);
		}
		
		$sqlUpdate  = "UPDATE availableSettings set ";
		$sqlUpdate .= "settingDefaultValue = '" . $settingValue . "' ";
		$sqlUpdate .= "WHERE settingName = '" . $settingName . "' ";
		$sqlUpdate .= "LIMIT 1";
		
		if ($database->query($sqlUpdate)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function allAvailableSettings() {
		// only show settings that are available to this security level
		$securityLevel = $_SESSION['currentUser']['access'];

		$sql  = "SELECT * FROM availableSettings ";		
		$sql .= "WHERE settingSecurity >= "	. $securityLevel;
		
		$result_array = self::find_by_sql($sql);
		
		return $result_array;
	}
	
	public function settingByName($settingName) {
		$sql  = "SELECT * FROM availableSettings ";		
		$sql .= "WHERE settingName = '{$settingName}' ";
		$sql .= "LIMIT 1";
		
		$result_array = self::find_by_sql($sql);
		
		return $result_array;
	}

}

?>