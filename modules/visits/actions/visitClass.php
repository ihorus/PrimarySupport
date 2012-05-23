<?php

require_once(SITE_LOCATION . "/engine/database.php");

class Visit extends DatabaseObject {

	protected static $table_name = "visits";
	public $uid;
	public $school_uid;
	public $category;
	public $arrival;
	public $departure;
	public $description;
	public $mileage_claim;
	public $user_uid;
	public $tech_hourly;
	
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
	/* ---------------------*/
	
	public function find_by_uid($uid) {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = {$uid} LIMIT 1");

		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public function find_users_vists($userUID = NULL, $dateFrom = NULL, $dateTo = NULL) {
		global $database;
		
		if ($userUID == NULL) {
			$userUID = $_SESSION['cUser']['uid'];
		}
		
		$dateFrom = strtotime($dateFrom);
		$dateTo = strtotime($dateTo);
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE UNIX_TIMESTAMP(arrival) >= '" . $dateFrom . "' ";
		$sql .= "AND UNIX_TIMESTAMP(arrival) <= '" . $dateTo . "' ";
		$sql .= "AND user_uid = '" . $userUID . "' ";
		$sql .= "AND category = 'ICT Support' ";
		$sql .= "AND mileage_claim = '1'";
		
		$result_array = self::find_by_sql($sql);
		return $result_array;
	}
	
	
	public function create() {
		global $database;
				
		$sql  = "INSERT INTO visits (";
		$sql .= "school_uid, category, arrival, departure, description, mileage_claim, user_uid, tech_hourly";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->school_uid) . "', '";
		$sql .= $database->escape_value($this->category) . "', '";
		$sql .= $database->escape_value($this->arrival) . "', '";
		$sql .= $database->escape_value($this->departure) . "', '";
		$sql .= $database->escape_value($this->description) . "', '";
		$sql .= $database->escape_value($this->mileage_claim) . "', '";
		$sql .= $database->escape_value($this->user_uid) . "', '";
		$sql .= $database->escape_value($this->tech_hourly) . "')";
		
		// insert the record to the database
		if (!$database->query($sql)) {
			echo ("Error entering visit into the database.");
		} else {
			$info = new Response();
			$info->school_uid = $this->school_uid;
			$info->spawn = 0;
	
			$entity1  = "{User:" . $_SESSION['cUser']['uid'] . "}";
			$description  = " logged visit {Visit:" . $database->insert_id() . "}";
			$description .= " to {School:" . $this->school_uid . "}";
			$entity2 = "";

			$info->description = ($entity1 . $description . $entity2);
			$info->user_uid = $_SESSION['cUser']['uid'];
			$info->create_info();
		}
		
		$insertID = $database->insert_id();
	}
	
	public function allVisitsByTechnician($technicianUID = NULL) {
		if ($technicianUID == NULL) {
			$technicianUID = $_SESSION['cUser']['uid'];
		}
	
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE user_uid = '" . $technicianUID . "' ";
		$sql .= "ORDER BY arrival ASC";
		
		$result_array = self::find_by_sql($sql);
		return $result_array;
	}
	
	public function allVisitsBySchool($dateFrom, $dateTo, $schoolUID = 0){
		global $database;
		
		$dateFrom = strtotime($dateFrom);
		$dateTo = strtotime($dateTo);
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE UNIX_TIMESTAMP(arrival) >= '" . $dateFrom . "' ";
		$sql .= "AND UNIX_TIMESTAMP(arrival) <= '" . $dateTo . "' ";
		$sql .= "AND school_uid = '" . $schoolUID . "' ";
		$sql .= "AND (category = 'ICT Support' ";
		$sql .= "OR category = 'ICT Support (Phone)') ";
				
		$result_array = self::find_by_sql($sql);
		return $result_array;
	}
}

?>