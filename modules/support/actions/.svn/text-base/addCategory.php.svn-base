<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['jobUID'])) {
	$jobUID = $_POST['jobUID'];
	$category = $_POST['category'];
	
	$job = new Support();
	$job->uid = $jobUID;
	$job->addCategory($category);
	
	echo makeCategory($category);
}
?>