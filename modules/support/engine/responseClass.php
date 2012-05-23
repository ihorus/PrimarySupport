<?php

require_once(SITE_LOCATION . "/engine/database.php");

class Response extends DatabaseObject {

	protected static $table_name = "jobs";
	public $uid;
	public $spawn;
	public $active;
	public $school_uid;
	public $entry;
	public $type;
	public $description;
	public $owner_uid;
	public $user_uid;
	public $attachment;
	
	public static function find_all($spawnUID) {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE type IN ('Response', 'Info') AND spawn = {$spawnUID} ORDER BY entry ASC");
	}
	
	public static function find_by_uid($responseUID = '0') {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = {$responseUID} AND type IN ('Response', 'Info') LIMIT 1");

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
	
	public function displayResponse() {
		global $database;
		
		$school = Group::find_by_uid($this->school_uid);
		//$owner = User::find_by_uid($this->owner_uid);
		$poster = User::find_by_uid($this->user_uid);
		$originalJob = Support::find_by_uid($this->spawn);
		
		$jobNode  = "<div class=\"row\">";
		
		if ($this->type == "Response") {
			$jobNode .= "<div class=\"span1 offset1\">";
				$jobNode .= $poster->gravatarURL(true);
			$jobNode .= "</div>";
		
			$jobNode .= ("<div class=\"span10\">");
				$jobNode .= ("<div class=\"well\">");
					$jobNode .= "<div class=\"meta\">";
					$jobNode .= ("<strong>Posted by " . tagUser($this->user_uid) . " ");
					$jobNode .= ("on " . dateDisplay(strtotime($this->entry), true) . "</strong>");
					
					/*
					if (isTechnician() && $originalJob->active == 1) {
						$jobNode .= $this->displayBranch();
					}
					*/
					$jobNode .= "</div>";
					
					$text = paragraphTidyup($this->description);
					$text = linkJobUID($text, $originalJob->school_uid);
					
					$jobNode .= ("<p>" . $text . "</p>");
					
					if (!$this->attachment == NULL) {
						$jobNode .= displayAttachment($this->attachment);
					}
				$jobNode .= "</div>";
			$jobNode .= "</div>";			
		} elseif ($this->type == "Info") {
			$jobNode .= ("<div class=\"span11 offset1\">");
				$jobNode .= ("<div class=\"alert alert-info\">");
					$jobNode .= expandInfoBar($this->description, $this->spawn);
					$jobNode .= " on " . dateDisplay(strtotime($this->entry), true);
				$jobNode .= "</div>";
			$jobNode .= "</div>";
		}
		
		$jobNode .= "</div>";
		$jobNode .= "<div class=\"clearfix\"></div>";
		
		return $jobNode;
	}
	
	public function create() {
		global $database;
		
		if (!$this->description == "") {
			$this->entry = date('c');
			
			$sql  = "INSERT INTO jobs (";
			$sql .= "type, school_uid, spawn, description, attachment, user_uid, entry";
			$sql .= ") VALUES ('";
			$sql .= $database->escape_value("Response") . "', '";
			$sql .= $database->escape_value(0) . "', '";
			$sql .= $database->escape_value($this->spawn) . "', '";
			$sql .= $database->escape_value($this->description) . "', '";
			$sql .= $database->escape_value($this->attachment) . "', '";
			$sql .= $database->escape_value($this->user_uid) . "', '";
			$sql .= $database->escape_value($this->entry) . "')";
			
			
			if ($database->query($sql)) {
				$this->id = $database->insert_id();
				
				// update the original job's 'last_update' field to indicate that there has been activity on it
				// yes, I know this is messey - feel free to come up with a better sollution!
				$originalJob = Support::find_by_uid($this->spawn);
		
				$sqlUpdate  = "UPDATE jobs set ";
				$sqlUpdate .= "entry = '" . $originalJob->entry . "', ";
				$sqlUpdate .= "last_update = '" . date('Y-m-d H:i:s') . "' ";
				$sqlUpdate .= "WHERE uid = " . $database->escape_value($this->spawn);
				
				// insert the record to the database
				$database->query($sqlUpdate);
				
				// get the user preferece for e-mail delivery
				$sendEmailOnResponse = UserSettings::get_setting($_SESSION['cUser']['uid'], "email_send_on_support_response");
				
				// if the user wants an e-mail, send one
				if ($sendEmailOnResponse == "TRUE") {
					$sendMailToUser = TRUE;
				} else {
					$sendMailToUser = FALSE;
				}
								
				// only send e-mail if the user wants it, and the poster is not the person who logged the original job!
				if ($sendMailToUser == TRUE && $originalJob->user_uid <> $this->user_uid) {
					
					// find out who it is that's just updated the job
					$technician = User::find_by_uid($this->user_uid);
					
					// find out the e-mail address of the person who originally updated the job
					$originalJobPoster = User::find_by_uid($originalJob->user_uid);
					
					// build the message to send to the user
					$message  = "<p>Dear " . $originalJobPoster->firstname . ",";
					$message .= "<br />";
					$url = "http://intranet2/witches/node.php?m=support/views/support_unique.php&supportUID=" . $this->spawn . " ";
					$message .= "<p>View This Job: " . ($url) . "</p>";
					
										
					$message .= $technician->firstname . " has updated your job (#" . $this->spawn . ") on the ICT Primary Support site.</p>";
					$message .= "<p>Original Job: " . $originalJob->description . "</p><br />";
					$message .= "<p>The update was:</p>";
					$message .= "<p>" . paragraphTidyup($this->description) . "</p>";
					
					// include link to job!
					$message .= "<p>Please do not respond directly to this e-mail.  Instead, logon to the Primary Support site and respond to the original job thread.  Thank you.</p>";
					
					// send the e-mail
					sendMail ($originalJobPoster->email, $subject, $message);
				} // end check to send e-mail
				
				return true; // response created!
			} else {
				return false; // something when wrong with creating a response
			}
		}
	}
	
	public function create_info() {
		global $database;
		
		$description = (" assigned this job to ");
		
		$sql  = "INSERT INTO jobs (";
		$sql .= "type, school_uid, spawn, description, user_uid";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value("Info") . "', '";
		$sql .= $database->escape_value($this->school_uid) . "', '";
		$sql .= $database->escape_value($this->spawn) . "', '";
		$sql .= $database->escape_value($this->description) . "', '";
		$sql .= $database->escape_value($_SESSION['cUser']['uid']) . "')";
		
		$database->query($sql);
		
		return $database->insert_id();
	}
	
	public function sendResponseMail() {
		// only send e-mail if the poster is not the person who logged the original job!
		if ($originalJob->user_uid <> $this->user_uid) {
			
			// find out who it is that's just updated the job
			$technician = User::find_by_uid($this->user_uid);
			
			// find out the e-mail address of the person who originally updated the job
			$originalJobPoster = User::find_by_uid($originalJob->user_uid);
		
			$message  = "";
			$message .= "";
			
			// send the e-mail
			sendMail ($originalJobPoster->email, $subject, $message);
		}
	}
	
	public function  promote_to_job() {
		global $database;
		
		$response = Response::find_by_uid($this->uid);		
		$originalJob = Support::find_by_uid($response->spawn);
		
		$description = "-- created from a response to job  " . $originalJob->uid . " by " . $_SESSION['cUser']['firstname'] . " --<br />";
		$description .= $response->description;
		
		// some sql to do stuff later
		$sqlUpdate  = "UPDATE jobs set ";
		$sqlUpdate .= "type = 'Job', ";
		$sqlUpdate .= "description = '" . $database->escape_value($description) . "', ";
		$sqlUpdate .= "school_uid = " . $originalJob->school_uid . " ";
		$sqlUpdate .= "WHERE uid = " . $response->uid;
		
		$database->query($sqlUpdate);
		
		
		//info box!
		$info = new Response();
		$info->school_uid = $originalJob->school_uid;
		$info->spawn = $originalJob->uid;
		
		$statement = "promoted this response to";
				
		$entity1 = "{User:" . $_SESSION['cUser']['uid'] . "}";
		$description = " " . $statement . " job {Job:" . $response->uid . "}";
		
		$info->description = ($entity1 . $description);
		$info->user_uid = $_SESSION['cUser']['uid'];
		$info->create_info();
		
		return $database->insert_id();
	}
	
	public function displayBranch() {
		$imageRoot = "/images/";
		$branchImage = $imageRoot . "branch.png";
		
		$bar = ("<div id=\"tabs\">");
			$bar .= ("<ul>");
				$bar .= ("<li id=\"branch\">" . "<a href=\"#\" id=\"" . $this->uid . "\" class=\"promoteResponseToJob\"><span>branch</span></a></li>");
			$bar .= ("</ul>");
		$bar .= ("</div>");
		
		echo $bar;
	}
	
} // end class Response

?>