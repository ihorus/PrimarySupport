<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['userUID'])) {
	$updatedUser = new User();
	
	$updatedUser->uid = $_POST['userUID'];
	$updatedUser->username = $_POST['username'];
	$updatedUser->firstname = $_POST['givenName'];
	$updatedUser->lastname = $_POST['familyName'];
	
	$schoolUIDS = ps_sanitise_array($_POST['schoolUID']);
	$updatedUser->school_uid = implode(",", $schoolUIDS);
	
	$updatedUser->email= $_POST['email'];
	$updatedUser->access = $_POST['access'];
	$updatedUser->type = $_POST['type'];
	if ($_POST['active'] == "true") {
		$updatedUser->active = 1;
	} else {
		$updatedUser->active = 0;
	}
	//$updatedUser->salutation = $_POST['salutation'];
	
	$updatedUser->update();
}
?>