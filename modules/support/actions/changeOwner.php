<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['ownerUID'])) {
	$newOwnerUID = $_POST['ownerUID'];
	$originalJobUID = $_POST['jobUID'];
	
	$info = new Response();
	$info->school_uid = "0";
	$info->spawn = $originalJobUID;
	
	if ($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'] == $newOwnerUID) {
		$entity1  = "{User:" . $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'] . "}";
		$description = " took ownership of job {Job:" . $originalJobUID . "}";
		$entity2 = "";
	} else {
		$entity1 = "{User:" . $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'] . "}";
		$description = " assigned job {Job:" . $originalJobUID . "} to ";
		$entity2 = "{User:" . $newOwnerUID . "}";
	}
	
	$info->description = ($entity1 . $description . $entity2);
	$info->user_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'];
	
	$jobChangeOwner = Support::assignJob($newOwnerUID, $originalJobUID);
	
	$displayInfo = Response::find_by_uid($info->create_info());
	
	echo $displayInfo->displayResponse();
}
?>