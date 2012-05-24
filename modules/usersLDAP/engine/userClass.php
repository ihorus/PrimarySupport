<?php

require_once(SITE_LOCATION . "/engine/database.php");

class User extends DatabaseObject {
	
	protected static $table_name = "users";
	public $uid;
	public $username;
	public $password;
	public $firstname;
	public $lastname;
	public $school_uid;
	public $email;
	public $access;
	public $type;
	public $active;
	public $salutation;
	
	
	public static function find_all() {
		global $ldapSession;
		
		$baseDN = "OU=Staff,OU=Wallingford Users,DC=wsnet,DC=local";
		$users = $ldapSession->findUsers($baseDN);
		
		return $users;
	}
	
	public static function find_all_active() {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE active = TRUE ORDER BY access ASC, firstname ASC");
	}
	
	public static function find_all_recent() {
		global $database;
		
		$recentUsersSQL  = "SELECT DISTINCT user_uid FROM jobs ";
		$recentUsersSQL .= "WHERE entry BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()+1 ";

		$usersSQL  = "SELECT * FROM " . self::$table_name . " ";
		$usersSQL .= "WHERE uid IN (" . $recentUsersSQL . ") ";
		$usersSQL .= "AND active = TRUE ";
		$usersSQL .= "ORDER BY access ASC, firstname ASC ";
		$usersSQL .= "LIMIT 10";
		
		return self::find_by_sql($usersSQL);
	}
	
	
	public static function find_all_techs() {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE (type = 'Administrator' OR type = 'Technician') ORDER BY type ASC, firstname ASC");
	}
	

	public static function find_by_uid($uid=0) {
		global $database;
		
		if (isset($uid) & ($uid <> 0)){
		} else {
			$uid = 1;
		}
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = '{$uid}' LIMIT 1");

		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_username($username = NULL) {
		global $database;
		
		$sql  = "SELECT * FROM " . self::$table_name . " ";
		$sql .= "WHERE username = '" . $username . "' ";
		$sql .= "LIMIT 1";
		
		$result_array = self::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	public static function find_by_MD5email($uid = NULL, $MD5email = NULL) {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = {$uid} LIMIT 1");
		
		if (md5($result_array[0]->email) == $MD5email) {
			return !empty($result_array) ? array_shift($result_array) : false;
		} else {
			return FALSE;
		}
	}
	
	public static function find_by_email($email) {
		global $database;
		
		// escape the email var, incase it contains any nasties!
		$email = $database->escape_value($email);
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE email = '{$email}' LIMIT 1");

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
	
	public static function authenticate($username="", $password="") {
		global $database;
		global $ldapSession;
		
		// connect to the AD as this username/password
		$ldapSession->username = $username;
		$ldapSession->password = $password;
		$ldapSession->ldapBind();
		
		// check if connection is established - if true, then the ldap username/password for this user is correct
		if ($ldapSession->ldapAuthenticate()) {
		$username = $database->escape_value($username);
				
		$sql  = "SELECT * FROM users ";
		$sql .= "WHERE username = '{$username}' ";
		$sql .= "LIMIT 1";
		
		$result_array = self::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
		}
	}
	
	public function full_name() {
		if (isset($this->firstname) && isset($this->lastname)) {
			return $this->firstname . " " . $this->lastname;
		} else {
			return "";
		}
	}
	
	public function resetPassword($newPasswordHash) {
		global $database;
		
		$sqlUpdate  = "UPDATE users set ";
		$sqlUpdate .= "password = '" . $newPasswordHash . "' ";
		$sqlUpdate .= "WHERE uid = '" . $this->uid . "' ";
		$sqlUpdate .= "LIMIT 1";	
		
		
		if (!$database->query($sqlUpdate)) {
			echo "password reset error";
		}
		
		return TRUE;
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
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO " . self::$table_name . " (";
		$sql .= "username, password, firstname, lastname, school_uid, email, access, type, active, salutation";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->username) . "', '";
		$sql .= $database->escape_value($this->password) . "', '";
		$sql .= $database->escape_value($this->firstname) . "', '";
		$sql .= $database->escape_value($this->lastname) . "', '";
		$sql .= $database->escape_value($this->school_uid) . "', '";
		$sql .= $database->escape_value($this->email) . "', '";
		$sql .= $database->escape_value($this->access) . "', '";
		$sql .= $database->escape_value($this->type) . "', '";
		$sql .= $database->escape_value($this->active) . "', '";
		$sql .= $database->escape_value($this->salutation) . "')";
		
		if ($database->query($sql)) {
			$this->id = $database->insert_id();
			return true;
		} else {
			return false;
		}
		
		
		// Could I itterate through the values like this?? ...
		//
		// $sql  = "INSERT INTO " . self::$table_name . " (";
		// $sql .= join(", ", array_keys($atributes));
		// $sql .= ") VALUES ('";
		// $sql .= join("', '", array_values($attributes));
		// $sql .= "')";
		
	}
	
	function update() {
		global $database;
		
		$sqlUpdate  = "UPDATE users set ";
		
		// CHECK IF EACH VALUE HAS BEEN SPECIFIED, WE DON'T WANT TO UPDATE TO A NULL VALUE!
		if (isset($this->username)) {
			$updateArray[] = "username = '" . $database->escape_value($this->username) . "'";
		}
		if (isset($this->password) && $this->password <> "") {
			$updateArray[] = "password = '" . md5($database->escape_value($this->password)) . "'";
		}
		if (isset($this->firstname)) {
			$updateArray[] = "firstname = '" . $database->escape_value($this->firstname) . "'";
		}
		if (isset($this->lastname)) {
			$updateArray[] = "lastname = '" . $database->escape_value($this->lastname) . "'";
		}
		if (isset($this->school_uid)) {
			$updateArray[] = "school_uid = '" . $database->escape_value($this->school_uid) . "'";
		}
		if (isset($this->email)) {
			$updateArray[] = "email = '" . $database->escape_value($this->email) . "'";
		}
		if (isset($this->access)) {
			$updateArray[] = "access = '" . $database->escape_value($this->access) . "'";
		}
		if (isset($this->type)) {
			$updateArray[] = "type = '" . $database->escape_value($this->type) . "'";
		}
		if (isset($this->active)) {
			$updateArray[] = "active = '" . $database->escape_value($this->active) . "'";
		}
		if (isset($this->salutation)) {
			$updateArray[] = "salutation = '" . $database->escape_value($this->salutation) . "'";
		}
		
		// BASED ON WHICH VALUES HAVE BEEN SPECIFIED (HELD IN THE updateArray) BUILD THE SQL STATEMENT
		$sqlUpdate .= implode(", ", $updateArray);
		
		$sqlUpdate .= " WHERE uid = '" . $this->uid . "' ";
		$sqlUpdate .= "LIMIT 1";	
				
		if (!$database->query($sqlUpdate)) {
			echo "user update error";
		}
		
		return TRUE;
	}

	
	public function gravatarURL($img = false, $defaultSize = 55) {
		$defaultAvatar = "http://" . $_SERVER["SERVER_NAME"] . SITE_PATH . "/images/gravatar.jpg";
		$size = $defaultSize;
		
		$gravatarURL  = ("http://www.gravatar.com/avatar.php?gravatar_id=" . md5(strtolower($this->email)));
		$gravatarURL .= ("&amp;default=" . urlencode($defaultAvatar));
		$gravatarURL .= ("&amp;s=" . $defaultSize);
		
		// if $link is specified, and is true, build the entire <a href, and <img tag
		if ($img == true) {
			$imageURL .= ("<img class=\"hidden-phone\" src=\"");
			$imageURL .= ($gravatarURL);
			$imageURL .= ("\" width=\"" . $defaultSize . "\" height=\"" . $defaultSize . "\" title=\"" . $this->displayFirstname(false) . "\" alt=\"" . $this->displayFirstname(false) ."\"/>");
			
			// check if we're viewing the users profile, if so - don't bother linking
			$currentNode = nodeName();
			if ($currentNode == "user_unique" && $_GET['userUID'] == $this->uid) {
				// the current node is the profile of the user in question, don't link the image
			} else {
				// it's a normal page - link the image
				$imageURL = ("<a href=\"node.php?n=user_unique&amp;userUID=" . $this->uid ."\">" . $imageURL . "</a>");
			}
			
			$gravatarURL = $imageURL;
		}
		
		return $gravatarURL;
	}
	
	public function displayFirstname($link = TRUE) {
		
		if ($this->uid != 0){
			if ($link == TRUE) {
				$output  = "<a href=\"node.php?n=user_unique&amp;userUID=" . $this->uid . "\" class=\"readmore\">";
				$output .= $this->firstname;
				$output .= "</a>";
			} else {
				$output =  $this->firstname;
			}
		} else {
			$output = "None";
		}
		return $output;
	}
	
	public function schoolUIDS() {
		$schoolUIDS = explode(",", $this->school_uid);
		
		return $schoolUIDS;
	}
}
?>