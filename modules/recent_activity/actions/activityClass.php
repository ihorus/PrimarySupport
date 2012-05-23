<?php

require_once(SITE_LOCATION . "/engine/database.php");

class Activity extends DatabaseObject {

	protected static $table_name = "jobs";
	public $uid;
	public $active;
	public $school_uid;
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
	
	
		
	public static function find_recent_activity($all = FALSE, $school = "ALL") {
	
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ";
		
		if (!$school == "ALL") {
			$sql .= "WHERE school_uid = {$school} ";
		}
		
		$sql .= "ORDER BY entry DESC";
		
		if (!$all == TRUE) {
			$sql .= " LIMIT 15";
		}
		
		return self::find_by_sql($sql);
	}
	
	public static function find_relevant_activity($all = FALSE, $schoolUIDS = 0) {
	
		if (is_array($schoolUIDS)) {
			$schoolUIDS = implode(",", $schoolUIDS);
		}
		
		//locate all jobs/responses/visits that reference $schoolUID
		$schoolJobs = Support::find_by_sql("SELECT uid FROM " . self::$table_name . " WHERE school_uid IN (" . $schoolUIDS . ") ");
		
		// build the uids found earlier into an array
		foreach ($schoolJobs as $job) {
			$stringJobsArray[] = $job->uid;
		}
		
		// implode the array (separated by comas), ready to use in the sql select
		$stringJobs = implode(", ", $stringJobsArray);
		
		// select everything from the database that either directly references the $schoolUID, or by use
		// of the spawn field, references the $schoolUID
		$sql  = "SELECT * FROM " . self::$table_name;
		$sql .= " ";
		$sql .= "WHERE school_uid IN (" . $schoolUIDS . ") ";
		$sql .= "OR spawn IN ({$stringJobs}) ";
		$sql .= "ORDER BY entry DESC";
				
		// limit the results if asked
		if (!$all == TRUE) {
			$sql .= " LIMIT 15";
		}
		
		return $activity = self::find_by_sql($sql);
	}
	

	
	public static function display_recent_activity($all = FALSE, $relevant = TRUE) {
		
		if ($relevant == TRUE) {
			// limit activity to that of the users school only
			
			$schoolUIDS = ps_sanitise_array($_SESSION['cUser']['schoolUID']);
			
			//$activity = self::find_relevant_activity($all = FALSE, $schoolUID = $currentSchool);
			$activity = self::find_relevant_activity($all, $schoolUIDS);
		} else {
			// show all activity
			$activity = self::find_recent_activity($all);
		}
		
		foreach ($activity AS $event) {
			$user = User::find_by_uid($event->user_uid);
			$school = Group::find_by_uid($event->school_uid);

			if ($event->type == 'Job') {
				//do this if it's a new job
				$itemInsert  = tagUser($event->user_uid);
				$itemInsert .= " logged job ";
				$itemInsert .= tagJob($event->uid);
				$itemInsert .= " for ";
				$itemInsert .= tagGroup($event->school_uid);
			} elseif ($event->type == 'Response') {
				//do this if it's a new response
				
				$originalJob = Support::find_by_uid($event->spawn);
				
				
				$itemInsert  = tagUser($event->user_uid);
				$itemInsert .= " responded to ";
				$itemInsert .= tagGroup($originalJob->school_uid);
				$itemInsert .= " job ";
				$itemInsert .= tagJob($event->spawn);

			} elseif ($event->type == 'Info') {
				//do this if it's an info update
				$itemInsert = expandInfoBar($event->description);
			} elseif($event->type == 'Visit') {
				// do this if it's a visit
			}
			
			echo "<p>" . $itemInsert . "</p>";
		}
	}


	public function find_total_activity_by_daterange($dateFrom = NULL, $dateTo = NULL, $schoolUIDS = NULL) {
		if ($dateFrom == NULL) {
			$dateFrom = date(U) - 115200;
		}
		
		if ($dateTo == NULL) {
			$dateTo = date(U);
		}
		
		if (is_array($schoolUIDS)) {
			$schoolUIDS = implode(",", $schoolUIDS);
		}
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE UNIX_TIMESTAMP(entry) > '{$dateFrom}' AND ";
		$sql .= "UNIX_TIMESTAMP(entry) < '{$dateTo}' ";
		if (!$schoolUIDS == NULL) {
			$sql .= "AND school_uid IN (" . $schoolUIDS . ") "; 
		}
		$sql .= "ORDER BY entry DESC";
		
		return self::find_by_sql($sql);
	}
	
	public function relevantActivity($date = NULL, $schoolUID = NULL) {
		global $database;
		
		$timeSkew = 172800; // how many seconds either way of the date to search for activity
		
		$date = strtotime($date);
		$dateFrom = $date - $timeSkew;
		$dateTo = $date + $timeSkew;
		
		
		// locate all jobs that reference $schoolUID
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE school_uid = {$schoolUID} AND ";	
		$sql .= "UNIX_TIMESTAMP(entry) > '{$dateFrom}' AND ";
		$sql .= "UNIX_TIMESTAMP(entry) < '{$dateTo}' ";
		
		
		$schoolJobs = Support::find_by_sql($sql);
		// build the uids found earlier into an array
		foreach ($schoolJobs as $job) {
			if ($job->type == "Job") {
				$stringJobsArray[] = $job->uid;
			} else {
				$stringJobsArray[] = $job->spawn;
			}
		}
				
		// implode the array (separated by comas), ready to use in the sql select
		if (count($stringJobsArray) > 0){
			$stringJobs = implode(", ", $stringJobsArray);
		} else {
			$stringJobs = "";
		}
		
		// select everything from the database that either directly references the $schoolUID, or by use
		// of the spawn field, references the $schoolUID
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE uid IN ({$stringJobs}) AND ";
		$sql .= "UNIX_TIMESTAMP(entry) > '{$dateFrom}' AND ";
		$sql .= "UNIX_TIMESTAMP(entry) < '{$dateTo}' ";
		$sql .= "ORDER BY entry DESC";
				
		return self::find_by_sql($sql);
	}
	
}
?>