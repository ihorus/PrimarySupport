<?php
// CHECK FOR config.php FILE IN ENGINE FOLDER
$configFile = "engine/config.php";
$configFile2 = "../../../" . $configFile;

if (file_exists($configFile)) {
	require_once($configFile);
} elseif (file_exists($configFile2)) {
	require_once($configFile2);
} else {
	echo ("WARNING - NO CONFIG.PHP FILE FOUND!");
	echo ("<br />");
	echo ("Search Location: '" . $configFile . "' and '" . $configFile2 . "'");
	exit;
}
	
$includes = array();

$includes[] = "/engine/database.php";
$includes[] = "/engine/database_object.php";
$includes[] = "/engine/globalFunctions.php";
$includes[] = "/engine/pagination.php";
$includes[] = "/engine/availableSettings.php";
$includes[] = "/engine/groupClass.php";
$includes[] = "/engine/session.php";

foreach ($includes as $include) {
	if (file_exists(SITE_LOCATION . $include)) {
		require_once(SITE_LOCATION . $include);
	} else {
		echo ("Error in initialisation. Could not load the file '" . $include . "' in " . SITE_LOCATION);
		echo ("<br />");
	}
}

// go get all the enabled modules from the database
$enabledModules = enabledModules();

// include all the enabled modules' 'start.php' files
foreach ($enabledModules AS $result) {
	$searchDir = SITE_LOCATION . "modules/";
	$startFile = $searchDir . $result . "/start.php";
	
	if (file_exists($startFile)) {
		require_once($startFile);
	}
}

?>