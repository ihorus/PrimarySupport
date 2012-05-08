<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['userUID'])) {
	$userUID = $_POST['userUID'];
	
	$settingUIDS = explode(",", $_POST['settingUID']);
	$settingValues = explode(",", $_POST['settingValue']);
	
	//printArray($settingUIDS);
	//printArray($settingValues);
	
	foreach($settingUIDS AS $settingPosition => $value) {
		$updateSetting = UserSettings::updateSetting($userUID, $value, $settingValues[$settingPosition]);
	}
}
?>