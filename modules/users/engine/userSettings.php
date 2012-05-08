<?php
require_once(SITE_LOCATION . "/engine/database.php");

class UserSettings extends DatabaseObject {

	protected static $table_name = "settings";
	public $uid;
	public $user_uid;
	public $settingUID;
	public $setting_value;
	
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

	public function get_setting($userUID = 0, $setting = NULL) {
		global $database;
		
		if (!is_numeric($setting)) {
			// setting isn't a UID, so find out what the UID of the setting name is
			$allSettings = AvailableSettings::settingByName($setting);
			$setting = $allSettings[0]->uid;
		}
		
		$sql  = "SELECT * FROM settings WHERE ";
		$sql .= "user_uid = '{$userUID}' AND ";
		$sql .= "settingUID = '{$setting}' ";
		$sql .= "LIMIT 1";
		
		$result_array = self::find_by_sql($sql);
		
		if (count($result_array) == 0) {
			// user setting not found - search for site-wide
			$result_array = AvailableSettings::get_setting($setting);			
			
			return $result_array[0]->settingDefaultValue;
		} else {
			return $result_array[0]->setting_value;
		}
		
		
	}
	
	public function exists($userUID = 0, $settingUID = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM settings WHERE ";
		$sql .= "user_uid = '{$userUID}' AND ";
		$sql .= "settingUID = '{$settingUID}' ";
		$sql .= "LIMIT 1";
		
		$result_array = self::find_by_sql($sql);
		
		if (count($result_array) == 0) {
			return false;
		} else {
			return true;
		}
		
		
	}
	
	public function updateSetting($userUID = 0, $settingUID = NULL, $settingValue = NULL) {
		global $database;
		
		if ($settingValue == "" || $settingValue == NULL) {
			$settingValue = "";
		}
		
		if (is_array($settingValue)) {
			$settingValue = implode(",",$settingValue);
		}
		
		$sqlUpdate  = "UPDATE settings set ";
		$sqlUpdate .= "setting_value = '" . $settingValue . "' ";
		$sqlUpdate .= "WHERE settingUID = '" . $settingUID . "' AND ";
		$sqlUpdate .= "user_uid = '" . $userUID . "'";
				
		if ($database->query($sqlUpdate)) {
			if ($database->affected_rows() == 0) {
			//	echo "creating new setting for user:" . $userUID . " settingUID:" . $settingUID . " settingValue:" . $settingValue;
			//	echo "<br />";
			self::createSetting($userUID, $settingUID, $settingValue);
			} else {
			//	echo "setting existed, so it was updated";
			}
		} else {
			echo "Error";
		}
	}
	
	public function createSetting($userUID = 0, $settingUID = NULL, $settingValue = NULL) {
		global $database;
		
		// check to see if the setting already exists, as we don't want multiple copies!
		if (self::exists($userUID, $settingUID)) {
			// setting exists, don't create another one
		} else {
			if (is_array($settingValue)) {
				$settingValue = implode(",",$settingValue);
			}
			
			// setting doesn't exist, create it
			$sql  = "INSERT INTO " . self::$table_name . " (";
			$sql .= "setting_value, settingUID, user_uid";
			$sql .= ") VALUES ('";
			$sql .= $database->escape_value($settingValue) . "', '";
			$sql .= $database->escape_value($settingUID) . "', '";
			$sql .= $database->escape_value($userUID) . "')";
						
			if ($database->query($sql)) {
				return true;
			} else {
				return false;
			}

		}
	}
	
	public function findAll($userUID = 0) {
		global $database;
				
		$sql  = "SELECT * FROM settings ";
		$sql .= "WHERE user_uid = '" . $userUID . "'";
				
		$result_array = self::find_by_sql($sql);
		
		//return !empty($result_array) ? array_shift($result_array) : false;
		return $result_array;
	}
	


}

?>