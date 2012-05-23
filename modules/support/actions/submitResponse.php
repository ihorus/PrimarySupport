<?php
require_once("../../../engine/initialise.php");
//printArray($_POST);

if (isset($_POST['description'])) {
	$response = new Response();
	$response->type = "Response";
	$response->school_uid = "0";
	$response->spawn = $_POST['jobUID'];
	
	// check to see if this response should be logged on behalf of someone else
	if ($_POST['onBehalfOfUser'] !== $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']) {
		if (isTechnician()){
			// change the user logging the job to the selected user
			$response->user_uid = $_POST['onBehalfOfUser'];
			$behalfOfUser = User::find_by_uid($_POST['onBehalfOfUser']);
		
			// add a message to the description to say that it was logged on behalf of someone else
			$name1 = $_SESSION[SITE_UNIQUE_KEY]['cUser']['firstname'] . " " . $_SESSION[SITE_UNIQUE_KEY]['cUser']['lastname'];
			$name2 = $behalfOfUser->firstname . " " . $behalfOfUser->lastname;
			$response->description  = "-- response logged by " . $name1 . " on behalf of " . $name2 . " --<br />";
			$response->description .= $_POST['description'];
		} else {
			// change the user logging the job to the selected user
			$response->user_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'];		
			$response->description = $_POST['description'];
		}
	} else {
		$response->user_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'];
		$response->description = $_POST['description'];
	}
	
	$response->attachment = NULL;
	
	$response->create();
	
	echo $response->displayResponse();
}

/*	
// upload file if use selected one...
if ($_FILES['attachment']['name'] <> "") {
	uploadAttachment($_FILES['attachment']);
	$response->attachment = "uploads/" . $_FILES['attachment']['name'];
} else {
	$response->attachment = NULL;
}
*/
?>