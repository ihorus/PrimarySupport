<?php

require_once(SITE_LOCATION . "/engine/database.php");

class Search extends DatabaseObject {

	protected static $jobs_table_name = "jobs";
	protected static $notes_table_name = "notes";
	public $searchString;
	
	public $uid;
	public $active;
	public $school_uid;
	public $title;
	public $entry;
	public $type;
	public $description;
	public $owner_uid;
	public $priority;
	public $user_uid;
	public $last_update;
	public $job_closed;
	public $spawn;
	public $monthYear; // temp
	
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
	
	public function find_all_jobs($status = "active") {	
		$jobs_sql  = "SELECT * FROM " . self::$jobs_table_name . " ";
		
		if ($status == "active") {
			$jobs_sql .= "WHERE (type = 'Job' OR type = 'Response') AND (type = 'Job' AND active = 1) ";
		} elseif ($status == "closed") {
			$jobs_sql .= "WHERE (type = 'Job' OR type = 'Response') AND (type = 'Job' AND active = 0) ";
		} else {
			$jobs_sql .= "WHERE (type = 'Job' OR type = 'Response') ";
		}
		
		$jobs_sql .= "AND description LIKE '%" . $this->searchString . "%' ";
		$jobs_sql .= "OR uid LIKE '%" . $this->searchString . "%' ";
		$jobs_sql .= "ORDER BY entry DESC";
		
		$results = self::find_by_sql($jobs_sql);
		
		// itterate through the search_results
		foreach ($results as $search_result) {
			if ($search_result->type == "Job") {
				// the search result is an original job, so just display it
				$jobUIDArray[] =  $search_result->uid;
			} elseif ($search_result->type == "Response") {
				// the search result is a response, so display the original job instead
				$jobUIDArray[] =  $search_result->spawn;
			}
		}
		
		// if the search term found a response/job or multiple instances, only return the original job uid
		if (count($jobUIDArray) !== 0) {
			$uniqueArray = array_unique($jobUIDArray);
		}
		
		return $uniqueArray;
	}
	
	public function find_school_jobs($schoolUID = NULL) {	
		$jobs_sql  = "SELECT * FROM " . self::$jobs_table_name . " ";
		$jobs_sql .= "WHERE (type = 'Job' OR type = 'Response') ";
		$jobs_sql .= "AND school_uid = '" . $schoolUID . "' ";
		$jobs_sql .= "AND description LIKE '%" . $this->searchString . "%' ";
		$jobs_sql .= "OR uid LIKE '%" . $this->searchString . "%' ";
		$jobs_sql .= "ORDER BY entry DESC";
		
		$results = self::find_by_sql($jobs_sql);
		
		// itterate through the search_results
		foreach ($results as $search_result) {
			if ($search_result->type == "Job") {
				// the search result is an original job, so just display it
				$jobUIDArray[] =  $search_result->uid;
			} elseif ($search_result->type == "Response") {
				// the search result is a response, so display the original job instead
				$jobUIDArray[] =  $search_result->spawn;
			}
		}
		
		// if the search term found a response/job or multiple instances, only return the original job uid
		if (count($jobUIDArray) !== 0) {
			$uniqueArray = array_unique($jobUIDArray);
		}
		
		return $uniqueArray;
	}
	
	public function find_all_notes($searchString = NULL) {	
		$jobs_sql  = "SELECT * FROM " . self::$notes_table_name . " ";
		$jobs_sql .= "WHERE description LIKE '%" . $searchString . "%' ";
		$jobs_sql .= "OR title LIKE '%" . $searchString . "%' ";
		$jobs_sql .= "ORDER BY uid DESC";
		
		$results = self::find_by_sql($jobs_sql);
				
		return $results;
	}
	
	public function findJobsBySearch($searchString = NULL) {
		global $database;
		
		// find jobs with the right search term
		$sql  = "SELECT * FROM " . self::$jobs_table_name . " ";
		$sql .= "WHERE description LIKE '%" . $database->escape_value($searchString) . "%' ";
		$sql .= "AND (type = 'Info' OR type = 'Job') ";
		$sql .= "LIMIT 500";
		
		return self::find_by_sql($sql);
	}
}
?>