<?php

require_once(SITE_LOCATION . "/engine/database.php");

class News extends DatabaseObject {

	protected static $table_name = "news";
	public $uid;
	public $date;
	public $description;
	public $active;
	public $title;
	public $user_uid;

	
	public static function find_all() {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " ORDER BY date DESC");
	}
	
	public static function find_subset($limit = 5, $active = TRUE) {
		return self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE active = {$active} ORDER BY date DESC LIMIT {$limit}");
	}
	
	public static function find_by_uid($newsUID = '0') {
		global $database;
		
		$result_array = self::find_by_sql("SELECT * FROM " . self::$table_name . " WHERE uid = {$newsUID} LIMIT 1");

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
	
	public function displayNewsItem() {
		global $database;
		
		//$school = School::find_by_uid($this->school_uid);
		$poster = User::find_by_uid($this->user_uid);
		
		$jobNode  = ("<div class=\"alert alert-info\">");
			if ((date(U) - strtotime($this->date)) < 86400) {
				//$jobNode .= ("<div id=\"ribbon-new\"></div>");
			}

			
			$jobNode .= ("<h2>" . $this->title . "</h2>");		
		
			if (isTechnician()) {
				// display 'add news item' for technicians and admins
				$addURL = "node.php?m=news/views/add.php";
				$editURL = "node.php?m=news/views/edit.php&amp;newsUID=" . $this->uid;
				$delteURL = "#";
				displayToolbar($addURL,$editURL,$deleteURL);
			}
			
			$jobNode .= ("<div class=\"post-info\">");
				$jobNode .= ("<p>");
				$jobNode .= "Posted by " . tagUser($this->user_uid) . " ";
				$jobNode .= ("on <span class=\"date\">" . dateDisplay(strtotime($this->date), true) . "</span>.");
				$jobNode .= ("</p>");
			$jobNode .= ("</div>");
		//$jobNode .= ("</div>");
		//$jobNode .= ("<div class=\"clear\"></div>");	
		
		//$jobNode .= ("<div class=\"grid_9\" id=\"support-incident\">");
			$jobNode .= ("<p>");
			
			$jobNode .= (paragraphTidyup($this->description));
			$jobNode .= ("</p>");
		$jobNode .= ("</div>");
		
		$jobNode .= ("<div class=\"clear\"></div>");
		
		return $jobNode;
	}
	
	public function create() {
		global $database;
		
		$sql  = "INSERT INTO news (";
		$sql .= "date, title, description, active, user_uid";
		$sql .= ") VALUES ('";
		$sql .= $database->escape_value($this->date) . "', '";
		$sql .= $database->escape_value($this->title) . "', '";
		$sql .= $database->escape_value($this->description) . "', '";
		$sql .= $this->active . "', '";
		$sql .= $database->escape_value($this->user_uid) . "')";

		
		// insert the record to the database
		$database->query($sql);
	}
	
	public function update() {
		global $database;
		
		$sql  = "UPDATE news SET ";
		$sql .= "date = '" . $this->date . "', ";
		$sql .= "description = '" . $database->escape_value($this->description) . "', ";
		$sql .= "active = '" . $this->active . "', ";
		$sql .= "title = '" . $database->escape_value($this->title) . "' ";
		$sql .= "WHERE uid = " . $this->uid;
		
		// insert the record to the database
		$database->query($sql);
	}
} // end class Response

?>