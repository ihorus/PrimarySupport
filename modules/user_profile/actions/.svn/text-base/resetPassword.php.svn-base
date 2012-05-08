<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['userUID'])) {
	$updatedUser = new User();
	
	$updatedUser->uid = $_POST['userUID'];
	$updatedUser->password = $_POST['password'];
	
	$updatedUser->update();
}
?>