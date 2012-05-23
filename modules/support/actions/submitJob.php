<?php
require_once("../../../engine/initialise.php");
//printArray($_POST);

if (isset($_POST['description'])) {
	$job = new Support();
	$job->type = "Job";
	$job->entry = date('r');
	$job->active = 1;
	$job->priority = 3;
	
	// check to see if this response should be logged on behalf of someone else
	if ($_POST['onBehalfOfUserUID'] !== $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']) {
		if (isTechnician()){
			// change the user logging the job to the selected user
			$job->user_uid = $_POST['onBehalfOfUserUID'];
			$behalfOfUser = User::find_by_uid($_POST['onBehalfOfUserUID']);
			
			// work out if this user has more than one school associated with them
			$behalfOfUserSchoolUIDS = explode(",",$behalfOfUser->school_uid);
						
			if (count($behalfOfUserSchoolUIDS) > 1) {
				// the user is associated with more than 1 school - work out which school this job should be logged under
				$dropdownText = $_POST['onBehalfOfUserName'];
				$positionStart = strpos($dropdownText, "at ");
				$schoolName = substr($dropdownText, $positionStart + 3);
				$school = Group::find_by_name($schoolName);
				
				$job->school_uid = $school->uid;
			} else {
				$job->school_uid = $behalfOfUser->school_uid;
			}
			

			$behalfOfUserName = User::find_by_uid($_POST['onBehalfOfUserUID']);
			
		
		
			// add a message to the description to say that it was logged on behalf of someone else
			$name1 = $_SESSION[SITE_UNIQUE_KEY]['cUser']['firstname'] . " " . $_SESSION[SITE_UNIQUE_KEY]['cUser']['lastname'];
			$name2 = $behalfOfUser->firstname . " " . $behalfOfUser->lastname;
			
			$job->description  = "-- job logged by " . $name1 . " on behalf of " . $name2 . " --<br />";
			$job->description .= $_POST['description'];
		} else {
			// change the user logging the job to the selected user
			$job->user_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'];		
			$job->description = $_POST['description'];
			$job->school_uid = $_POST['onBehalfOfUser'];
		}
	} else {
		$job->user_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'];
		$job->description = $_POST['description'];
		$job->school_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['schoolUID'];
	}
	
	$job->attachment = NULL;
	
	$job->create();
	
	echo $job->displayJob();
}
?>
