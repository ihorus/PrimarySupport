<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['jobUID'])) {	
	$info = new Response();
	$info->school_uid = "0";
	$info->spawn = $originalJobUID;
	
	$info->school_uid = "0";
	$info->spawn = $_POST['jobUID'];
	
	$entity1 = "{User:" . $_SESSION['currentUser']['uid'] . "}";
	$description = " changed the priority of job {Job:" . $_POST['jobUID'] . "} to ";
	$entity2 = priorityName($_POST['priority']);
	
	$info->description = ($entity1 . $description . $entity2);
	$info->user_uid = $_SESSION['currentUser']['uid'];
		
	$jobChangePriority = Support::changePriority($_POST['priority'], $_POST['jobUID']);
	
	$displayInfo = Response::find_by_uid($info->create_info());
	
	echo $displayInfo->displayResponse();
}
?>