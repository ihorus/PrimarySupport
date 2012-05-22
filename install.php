<?php
function makeBar($type = "info", $content = NULL) {
	if ($type == "success") {
		$class = "alert alert-success";
	} elseif ($type == "error") {
		$class = "alert alert-error";
	} else {
		$class = "alert alert";
	}
	
	$output  = "<div class=\"" . $class . "\">";
	$output .= $content;
	$output .= "</div>";
	
	return $output;
}
?>
<?php include_once ("views/html_head.php"); ?>
<div class="container">

<?php
// check for config
$file1 = getcwd() . "/engine/config.php";

if (file_exists($file1)) {
	echo makeBar("success", "config.php located");
	include_once($file1);
} else {
	echo makeBar("error", "Can't find config.php (I tried, honestly!) at " . $_SERVER['DOCUMENT_ROOT'] . "/engine");
}

// check for files
$file1 = SITE_LOCATION . "/nodes/admin_index.php";

if (file_exists($file1)) {
	echo makeBar("success", "It seems the files/folders are all located in the root of " . SITE_LOCATION);
} else {
	echo makeBar("error", "Files aren't located in " . SITE_LOCATION . " - or at least, I couldn't find: " . $file1 . ". Are you sure you've configured config.php?");
}

// check for uploads permissions
$permissions = substr(sprintf('%o', fileperms('uploads')), -4);
$permissions = substr($permissions, 1, 3);

if ($permissions == "755") {
	echo makeBar("success", "Permissions on uploads set to " . $permissions);
} elseif ($permissions == "777") {
	echo makeBar("alert", "Permissions on uploads set to " . $permissions . " which will work, but isn't advisable");
} else {
	echo makeBar("error", "Permissions on uploads set to " . $permissions . " which just isn't what I wanted! (755)");
}

// check for database connection
require_once(SITE_LOCATION . "/engine/database.php");
$connection = $database->query("SELECT * FROM users LIMIT 10");

if (!$connection) {
	echo makeBar("error", "Database connection failed to " . DB_NAME . ". Are you sure the details are right in config.php?");
} else {
	echo makeBar("success", "Database connection to " . DB_NAME . " successful");
	
	if (isset($_GET['installTables']) && $_GET['installTables'] == TRUE) {
		echo "Installing tables";
		//$sql = file_get_contents('engine/tableInstaller.sql');
		$sql = implode("\n", file('engine/tableInstaller.sql')); 
		//$sql = "USE `ict_witches`; CREATE TABLE `test` (`uid` int(10), PRIMARY KEY (`uid`));";
		
		$connection2 = mysql_connect(DB_SERVER, $_POST['db_username'], $_POST['db_password']);			
		
		//echo $sql;
		
		if (mysql_query($sql)) {
			echo "success"; 
		} else {
			$formOutput  = "<form method=\"post\" class=\"well form-inline\">";
			$formOutput .= "<input type=\"\" name=\"db_username\" class=\"input-small\" placeholder=\"DB Username\" value=\"breakspeara\">";
			$formOutput .= "<input type=\"password\" name=\"db_password\" class=\"input-small\" placeholder=\"Password\">";
			$formOutput .= "<button type=\"submit\" class=\"btn\">Create Tables</button>";
			$formOutput .= "</form>";
			echo $formOutput;
			echo "error" .  mysql_error();
		}
	}
}

// check for tables in database
$table = "test";

if (mysql_num_rows(mysql_query("SHOW TABLES LIKE '" . $table . "'"))) {
	echo makeBar("success", "I've taken a peak inside the database, and it all looks ok.  Great job!");
} else {
	echo makeBar("error", "Tables not setup in database yet.  Do you want to run the <a href=\"install.php?installTables=TRUE\">table installer</a>?");
}





?>
</div>
</body>
</html>