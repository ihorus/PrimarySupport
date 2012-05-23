<?php

require_once(SITE_LOCATION . "/engine/database.php");

class Support extends DatabaseObject {

	protected static $table_name = "jobs";
	public $uid;
	public $active;
	public $school_uid;
	public $entry;
	public $description;
	public $type;
	public $owner_uid;
	public $category;
	public $priority;
	public $user_uid;
	public $last_update;
	public $job_closed;
	public $totalJobs;
	public $spawn;
	
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE type = 'Job'");
	}
	
	public static function find_unclaimed() {
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE active = '1' ";
		$sql .= "AND type = 'Job' ";
		$sql .= "AND owner_uid IS NULL ";
				
		return self::find_by_sql($sql);
	}
	
	public static function find_recently_assigned($ownerUID = NULL) {
		$searchString = "to {User:" . $ownerUID . "}";
		
		$sql  = "SELECT spawn FROM " . self::$table_name . " ";
		$sql .= "WHERE type = 'Info' ";
		$sql .= "AND DATE(entry) = DATE(NOW()) ";
		$sql .= "AND description LIKE '%" . $searchString . "%' ";
		$sql .= "ORDER BY entry DESC";
		
		$sql2  = "SELECT * FROM " . self::$table_name . " ";
		$sql2 .= "WHERE active = '1' ";
		$sql2 .= "AND type = 'Job' ";
		$sql2 .= "AND uid IN (" . $sql . ")";
				
		return self::find_by_sql($sql2);
	}
	
	public static function find_all_active() {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE type = 'Job' AND active = '1'");
	}
	
	public static function find_subset($active = TRUE, $schoolUIDS = '0', $orderBY = 'entry', $limit = 999) {
		if (is_array($schoolUIDS)) {
			$schoolUIDS = implode(",",$schoolUIDS);
		}
		
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE type = 'Job' AND active = {$active} AND school_uid IN (" . $schoolUIDS . ") ORDER BY {$orderBY} DESC LIMIT {$limit}");
	}
	

	public static function find_by_uid($jobUID = '0') {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = {$jobUID} AND type = 'Job' LIMIT 1");
		
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
		
		//echo $sql;
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
	
	public function displayJob() {
		global $database;
		
		$school = Group::find_by_uid($this->school_uid);
		$owner = User::find_by_uid($this->owner_uid);
		$poster = User::find_by_uid($this->user_uid);
		
		$jobNode  = "<div class=\"row-fluid\">";		
			$jobNode .= "<div class=\"span1\">";
				$jobNode .= $poster->gravatarURL(true);
			$jobNode .= "</div>";
			
			if ($this->priority == 1) {
				$class = "alert alert-error";
			} elseif ($this->priority == 2) {
				$class = "alert alert-warning";
			} else {
				$class = "alert alert-info";
			}
			
			$jobNode .= "<div class=\"span11\">";
				$jobNode .= "<div class=\"" . $class . "\">";
					$jobNode .= "<div class=\"jobMeta\">";
					$jobNode .= ("<strong>" . tagJobInfo($this->uid) . " Logged by " . tagUser($this->user_uid) . " ");
					$jobNode .= ("on " . dateDisplay(strtotime($this->entry), true) . "</strong>");
					
					/*
					if (isTechnician() && $originalJob->active == 1) {
						$jobNode .= $this->displayBranch();
					}
					*/
					$jobNode .= "</div>";
					
					
					$jobNode .= paragraphTidyup($this->description);
				$jobNode .= "</div>";
			$jobNode .= "</div>";
		$jobNode .= "</div>";
		$jobNode .= "<hr />";
	
		return $jobNode;
	}
	
	public function assignJob($newOwner, $jobUID) {
		global $database;
				
		$sql  = "UPDATE " . self::$table_name . " SET ";
		$sql .= "owner_uid = '" . $newOwner . "' ";
		$sql .= "WHERE uid = '" . $jobUID . "'";
		
		$database->query($sql);
	}
	
	public function changePriority($priority, $jobUID) {
		global $database;
		
		$originalJob = Support::find_by_uid($jobUID);
		
		$sql  = "UPDATE " . self::$table_name . " SET ";
		$sql .= "priority = '" . $priority . "', ";
		$sql .= "entry = '" . $originalJob->entry . "', ";
		$sql .= "last_update = '" . date('Y-m-d H:m:s') . "' ";
		$sql .= "WHERE uid = " . $jobUID;
		
		$database->query($sql);
	}
	
	public function openClose($state = 1, $jobUID) {
		global $database;
		
		$originalJob = Support::find_by_uid($jobUID);
		
		$sql  = "UPDATE " . self::$table_name . " SET ";
		$sql .= "active = '" . $state . "', ";
		$sql .= "job_closed = '" . date('Y-m-d H:m:s') . "', ";
		$sql .= "entry = '" . $originalJob->entry . "' ";
		$sql .= "WHERE uid = " . $jobUID;
		
		$database->query($sql);
				
		//info box!
		
		$info = new Response();
		$info->school_uid = 0;
		$info->spawn = $originalJob->uid;
		
		if ($state == 1) {
			$statement = "re-opened";
		}
		if ($state == 0) {
			$statement = "closed";
		}
		
		$entity1 = "{User:" . $_SESSION['cUser']['uid'] . "}";
		$description = " " . $statement . " job {Job:" . $originalJob->uid . "}";
		
		$info->description = ($entity1 . $description);
		$info->user_uid = $_SESSION['cUser']['uid'];
		
		return $info->create_info();
	}
	
	public function create() {
		global $database;
		
		// check if the description was empty.  We don't want empty records in the database!
		if (!$this->description == "") {
			$sql  = "INSERT INTO " . self::$table_name . " (";
			$sql .= "type, school_uid, description, user_uid";
			$sql .= ") VALUES ('";
			$sql .= $database->escape_value("Job") . "', '";
			$sql .= $database->escape_value($this->school_uid) . "', '";
			$sql .= $database->escape_value($this->description) . "', '";
			$sql .= $database->escape_value($this->user_uid) . "')";
			
			// check if the database entry was successful (by attempting it)
			if ($database->query($sql)) {
				$this->uid = $database->insert_id();
				
				$technicians = User::find_all_techs();
				
				foreach ($technicians AS $technician) {
					// get the user preferece for e-mail delivery
					$sendEmailOnNewJob = UserSettings::get_setting($technician->uid, "email_send_to_tech_on_new_job");
										
					// if the user wants an e-mail, send one
					if ($sendEmailOnNewJob == "TRUE") {
						
						// find out who the technician is
						$userLogging = User::find_by_uid($this->user_uid);
						$school = Group::find_by_uid($this->school_uid);
						
						// build the message to send to the user
						$message  = "<p>Dear " . $technician->firstname . ",";
						$message .= "<br />";
						$message .= $userLogging->firstname . " at " . $school->name . " has logged a new job (#" . $this->uid . ") on " . SITE_NAME . "</p>";
						$message .= "<p>The update was:</p>";
						$message .= "<p>" . paragraphTidyup($this->description) . "</p>";
						
						// include link to job!
						$message .= "<p>Please do not respond directly to this e-mail.  Instead, logon to the " . SITE_NAME . " site and respond to the original job thread.  Thank you.</p>";
						
						// send the e-mail
						sendMail ($technician->email, $subject, $message);
					}
				} // end check to send e-mail
				
				
				return true;
			} else {
				// database entry didn't work
				return false;
			}
		} // end check to see if the description field was black
	}
	
	public function stagnantJobs($dateFrom = NULL, $dateTo = NULL, $ownerUID = 0) {
		$sql  = "SELECT * FROM " . self::$table_name . " WHERE ";
		$sql .= "type = 'Job' AND ";
		$sql .= "active = 1 ";
		
		if (!$dateFrom == NULL) {
			$sql .= "AND UNIX_TIMESTAMP(last_update) > '{$dateFrom}' AND ";
			$sql .= "UNIX_TIMESTAMP(last_update) < '{$dateTo}' ";
		}
		
		if ($ownerUID == 1) {
			$sql .= "AND (owner_uid = '{$ownerUID}' OR owner_uid IS NULL) ";
		} else {
			$sql .= "AND owner_uid = '{$ownerUID}'";
		}
		
		return self::find_by_sql($sql);
	}
	
	function displayAddResponse() {
		$users = User::find_all_active();
		
		$output  = "<div class=\"row\" id=\"responseOuter\">";
		
		// heading
		$output .= "<div class=\"span12\">";
			$output .= "<div class=\"page-header\">";
				$output .= "<h1>Add Response</h1>";
			$output .= "</div>";
		$output .= "</div>";
		
		// response content
		$output .= "<div class=\"span12\">";
			$output .= "<form class=\"form-horizontal\" target=\"_self\" method=\"POST\" name=\"response_form\" id=\"response_form\">";

			$output .= "<fieldset>";
				if($this->owner_uid == 0) {
					$disabledState = "disabled=\"" . $disabledState . "\"";
					$value = "Please take ownership of this job first";
				}
				
				$output .= "<div class=\"control-group\">";
					$output .= "<label class=\"control-label\" for=\"description\">Description</label>";
					$output .= "<div class=\"controls\">";
						$output .= "<textarea class=\"input-xxlarge\" id=\"description\" rows=\"5\" " .$disabledState . ">" . $value . "</textarea>";
					$output .= "</div>";
				$output .= "</div>";
			
			$output .= display_onbehalf_form_element();
			
			if (isTechnician()) {
				/*
				$output .= "<a href=\"#\" id=\"showHideClick\">More Options...</a>";
				$output .= ("<div id=\"showHide\">");
					$output .= ("<input type=\"file\" name=\"attachment\" id=\"attachment\" disabled/>"); // disabled for now
				$output .= ("</div>");
				*/
			}
				
				
				$output .= "<div class=\"control-group\">";
					$output .= "<div class=\"controls\">";
						$output .= "<div class=\"progress progress-alert progress-striped active\" style=\"width: 400px;\">";
							$output .= "<div class=\"bar\" style=\"width: 0%;\"></div>";
						$output .= "</div>";
				
						$output .= "<a class=\"btn btn-primary btn-large\" href=\"#\" id=\"submit_response\"><i class=\"icon-comment icon-white\"></i> Add Response</a>";
						$output .= " ";
						
						$output .= "<a class=\"btn btn-danger btn-large\" href=\"#\" id=\"closeJobButton\"><i class=\"icon-remove icon-white\"></i> Close Job</a>";
						if ($this->active == 1) {
							$output .= "<input type=\"hidden\" id=\"opencloseState\" value=\"close\" />";
						} else {
							$output .= "<input type=\"hidden\" id=\"opencloseState\" value=\"open\" />";
						}
						
						$output .= "<input type=\"hidden\" id=\"spawnUID\" name=\"spawnUID\" value=\"" . $this->uid . "\" />";
					$output .= "</div>";
				$output .= "</div>";
		
			//$output .= "<input type=\"submit\" name=\"submit\" class=\"button\" id=\"submit_btn\" value=\"Submit Response\" />";
			
			$output .= "<input type=\"hidden\" name=\"submit_response\" value=\"true\">";
			$output .= "</fieldset>";
			$output .= "</form>";
		$output .= "</div>";
		
		$output .= "<div class=\"span4\">";
			$output .= "<div class=\"page-header\">";
				$output .= "<h1>Owner</h1>";
			$output .= "</div>";
		
			$output .= "<form target=\"_self\" method=\"POST\" name=\"change_owner\" id=\"change_owner\">";
			
			$technicians = User::find_by_sql("SELECT * FROM users WHERE type IN ('Technician', 'Administrator')");
			
			if (!isTechnician()) {
				$dropDownState = "disabled";
			}
			
			$output .= "<select " . $dropDownState . " class=\"owner\" name=\"owner\" id=\"owner\" >";
			$output .= optionDropdown(0, "", $this->owner_uid);
			
			foreach($technicians AS $technician) {
				$output .= optionDropdown($technician->uid, $technician->firstname, $this->owner_uid);
			}
			
			$output .= "</select>";
			$output .= "<br />";
			$output .= "<noscript><input type=\"submit\" name=\"submit\" value=\"Assign\"></noscript>";
			$output .= "<input type=\"hidden\" id=\"spawnUID\" value=\"" . $this->uid . "\">";
			$output .= "</form>";
		$output .= "</div>";
		
		$output .= "<div class=\"span4\">";
			$output .= "<div class=\"page-header\">";
				$output .= "<h1>Priority</h1>";
			$output .= "</div>";
			
			$output .= "<form target=\"_self\" method=\"POST\" name=\"change_priority\" id=\"change_priority\">";
			
			if (!isTechnician()) {
				$dropDownState = "disabled";
			}
			
			$output .= "<select " . $dropDownState . " class=\"priority\" name=\"priority\" id=\"priority\">";
			
			foreach (priorities() AS $priority => $value) {
				$output .= optionDropdown($priority, $value, $this->priority);
			}
		
			$output .= "</select>";
			$output .= "<br />";
			$output .= "<noscript><input type=\"submit\" name=\"submit\" value=\"Change\"></noscript>";
			$output .= "<input type=\"hidden\" id=\"spawnUID\" value=\"" . $this->uid . "\">";
			$output .= "<input type=\"hidden\" name=\"submit_priority\">";
			$output .= "</form>";
		$output .= "</div>";
		
		$output .= "<div class=\"span4\">";
			$output .= "<div class=\"page-header\">";
				$output .= "<h1>Categories</h1>";
			$output .= "</div>";
			
			$categories = $this->allCategories();
			foreach ($categories AS $cat) {
				$catOutput[] = "\"" . $cat . "\"";
			}
			$catOutput = array_unique($catOutput);
			
			
			$output .= "<form id=\"categoryFormContainer\">";			 
			$output .= "<input type=\"text\" class=\"input-medium search-query\" data-provide=\"typeahead\" data-items=\"4\" id=\"categoryAdd\" data-source='[" . implode(",", $catOutput) . "]'/>";
			$output .= " <button type=\"submit\" class=\"btn\" id=\"categoryAddSubmit\">Add</button>";
			
			$categories = ps_sanitise_array($this->category);
			
			$output .= "<div class=\"clear\"></div>";
			$output .= "</form>";
		$output .= "</div>";
		$output .= "</div>";
		
		$output .= "<div class=\"row\" id=\"responseOuterReopen\">";
			$output .= "<div class=\"span11 offset1\">";
				$output .= "<a class=\"btn btn-success btn-large\" href=\"#\" id=\"openJobButton\"><i class=\"icon-refresh icon-white\"></i> Re-Open Job</a>";
			$output .= "</div>";
		$output .= "</div>";
		
		return $output;
	}
	
	public function displayMerge() {
		$bar = ("<div id=\"tabs\">");
			$bar .= ("<ul>");
				$bar .= ("<li id=\"merge\">" . "<a href=\"#\" id=\"" . $this->uid . "\" class=\"mergeWithJob\"><span>merge</span></a></li>");
			$bar .= ("</ul>");
		$bar .= ("</div>");
		
		echo $bar;
	}
	
	public function addCategory($category = NULL) {
		global $database;
		
		$job = self::find_by_uid($this->uid);
		
		$currentCats = ps_sanitise_array($job->category);
		$currentCats[] = $database->escape_value($category);
		
		$sql  = "UPDATE " . self::$table_name . " SET ";
		$sql .= "category = '" . implode(",",$currentCats) . "' ";
		$sql .= "WHERE uid = '" . $this->uid . "'";
		
		if ($database->query($sql)) {
			return true;
		}
	}
	
	public function totalResponses() {
		global $database;
		
		$job = self::find_by_uid($this->uid);
		
		$currentCats = ps_sanitise_array($job->category);
		$currentCats[] = $database->escape_value($category);
		
		$sql  = "SELECT COUNT(*) AS totalJobs FROM " . self::$table_name . " ";
		$sql .= "WHERE spawn = '" . $this->uid . "' ";
		$sql .= "AND type = 'Response'";
		
		$result_array = self::find_by_sql($sql);
		
		$totalResponses = !empty($result_array) ? array_shift($result_array) : false;
		$totalResponses = get_object_vars($totalResponses);
		
		return $totalResponses['totalJobs'];
	}
	
	public function allCategories() {
		global $database;
		
		$sql  = "SELECT category FROM " . self::$table_name . " ";
		$sql .= "GROUP BY category";
				
		$result_array = self::find_by_sql($sql);
		
		foreach ($result_array AS $category) {
			$categoryArray = explode(",",$category->category);
			foreach ($categoryArray AS $categoryItem) {
				$returnArray[] = $categoryItem;
			}
		}
		
		$returnArray = ps_sanitise_array($returnArray);
				
		return $returnArray;
	}

} // end class Support

?>