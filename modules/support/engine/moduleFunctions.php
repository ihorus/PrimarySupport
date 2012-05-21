<?php
function priorities() {
	$priorities = array(
		0 => "Unknown",
		1 => "High",
		2 => "Medium",
		3 => "Low",
		4 => "Known Issue");
		
		return $priorities;
}

function priorityName($priority=0) {
	$name = priorities();
	return $name[$priority];
}

function linkJobUID($string, $schoolUID = 0) {
	// fantasticly clever function to locate all numbers in a string, then check if these numbers are
	// jobUID's.  If they are, it wraps the UID in an '<a href' tag
	// Usage:	$string		= any string, with numbers in it or not
	// Return:	$string		= the same string only with any jobUID's now wrapped in an <a href

	if (!$schoolUID == 0) {
		$jobs = Support::find_by_sql("SELECT uid, type, school_uid FROM jobs WHERE type = 'Job' AND school_uid = {$schoolUID}");
	} else {
		// go initialise the Support class to locate all current jobs
		$jobs = Support::find_all();
	}
	
	// declare the $jobUIDS array that will hold all the uids of every job in the database
	$jobUIDS = array();
	
	// itterate through each job, and make a new array entry for every uid
	foreach ($jobs as $job) {
		$jobUIDS[$job->uid] = $job->uid;
	}
	
	// don't bother linking to a job if it's the current job
	if (isset($_GET['supportUID'])) {
		unset($jobUIDS[$_GET['supportUID']]);
	}
	
	rsort($jobUIDS);
	
	// build a pattern to locate any number with a space, curly bracket or hash before,
	// and a space or curly bracket after them
	$pattern  = "/(";
	// $pattern .= "(?!=\[^0-9A-Za-z])[0-9][0-9]*"; <- doesn't work that well
	$pattern .= "?!\s|\#|\()([0-9]){1,}(?=\s|\)|\.|\,|\?|\!|\$|\r";
	$pattern .= ")/";
	
	// search $string for any of the $patterns, if they're found, assign these to the $found array
	$found = preg_match_all($pattern, $string, $matches);
	
	// if there are multiple instances of the same jobUID in the array - group them
	$matches = array_unique($matches[0]);
	
	// itterate through every value in the $numbersFound array
	foreach ($matches as $var) {
		// if any of the values in $numbersFound are also in the $jobUIDS then it's obviously a reference to a job!
		if (in_array($var, $jobUIDS)) {
			// search for this job UID ($var) in $string, then replace it with an <a href=...
			$string = str_replace($var, "<a href=\"node.php?m=support/views/support_unique.php&supportUID=" . $var . "\">" . $var . "</a>", $string);
		}
	}
	
	return $string;
}


function expandInfoBar($string="Error in String", $checkUID = NULL) {
		// build a pattern to locate '{User:' then a number, then '}'
		$userPattern  = "/({User:[0-9]{1,}})/";
		$jobPattern =  "/({Job:([0-9]){1,}})/";
		$schoolPattern =  "/({School:([0-9]){1,}})/";
		$visitPattern =  "/({Visit:([0-9]){1,}})/";
		
		preg_match_all($userPattern, $string, $userMatches);
		preg_match_all($jobPattern, $string, $jobMatches);
		preg_match_all($schoolPattern, $string, $schoolMatches);
		preg_match_all($visitPattern, $string, $visitMatches);
		
		$userMatches = array_unique($userMatches[0]);
		$jobMatches = array_unique($jobMatches[0]);
		$schoolMatches = array_unique($schoolMatches[0]);
		$visitMatches = array_unique($visitMatches[0]);
	
		foreach ($userMatches as $var) {
			$userUID = str_replace("{User:", "", $var);
			$userUID = str_replace("}", "", $userUID);
						
			$string = str_replace($var, tagUser($userUID), $string);
		}
		
		foreach ($jobMatches as $var) {
			$jobUID = str_replace("{Job:", "", $var);
			$jobUID = str_replace("}", "", $jobUID);
			
			// check to see if the job being expanded is the job being viewed
			if ($jobUID == $checkUID) {
				// this job is the job being viewed, reference it by saying 'this job'
				$string = str_replace("job " . $var, "this job", $string);
			} else {
				// this job isn't being viewed specifically, reference it by UID
				$string = str_replace($var, tagJob($jobUID), $string);
			}
		}
		
		foreach ($visitMatches as $var) {
			$visitUID = str_replace("{Visit:", "", $var);
			$visitUID = str_replace("}", "", $visitUID);
						
			$string = str_replace($var, "<a href=\"node.php?m=visits/views/visit_unique.php&visitUID=" . $visitUID . "\">" . $visitUID . "</a>", $string);
		}
		
		foreach ($schoolMatches as $var) {
			$schoolUID = str_replace("{School:", "", $var);
			$schoolUID = str_replace("}", "", $schoolUID);
									
			$string = str_replace($var, tagGroup($schoolUID), $string);
		}
		
		return $string;
	}
	
	function displayAttachment($attachmentsArray = NULL) {
		if (!is_array($attachmentsArray)) {
			$attachmentsArray = explode(",", $attachmentsArray);
		}
		
		$output  = ("<ul class=\"thumbnails\">");
		
		foreach ($attachmentsArray AS $attachment) {
			$output .= displayImage($attachment);
		}
		
		$output .= ("</ul>");
		
		return $output;
	}
	
	function displayImage($attachmentURL) {
		
		// if the file doesn't exist, use a default 'not found' image
		if (!file_exists($attachmentURL)) {
			$attachmentURL = "images/image-not-found.png";
		}
		
		// list of extensions that will be treated as images
		$imageExtensions = array(
			'jpg',
			'bmp',
			'gif',
			'png'
		);
		
		// this finds the extension of the file being retrieved (without the '.')
		$imageExtension = substr($attachmentURL, strrpos($attachmentURL, '.') + 1);
		
		// check if the extension of the file is in the imageExtensions array
		// if it is, display a thumb of the image
		if (in_array($imageExtension, $imageExtensions)) {

			list($width, $height, $type, $attr) = getimagesize($attachmentURL);
			
			// check if the image has large dimensions, if so - resize it
			if ($width > 100 || $height > 100) {
				$imgHeight = $height / 4;
				$imgWidth = $width / 4;
			} else {
				$imgHeight = $height;
				$imgWidth = $width;
			}
			
			
			$displayCode = ("<li class=\"span3\">");
				$displayCode .= ("<a href=\"" . $attachmentURL . "\" class=\"thumbnail\">");
				$displayCode .= ("<img ");
					$displayCode .= ("src=\"" . $attachmentURL . "\">");
				$displayCode .= ("</a>");
			$displayCode .= ("</li>");
		} else {
			// otherwise, the attachment must be a file of some other type
			// hyperlink to it instead
			
			$displayCode  = ("<a href=\"" . $attachmentURL . "\">");
			$displayCode .= ("This response contains an attachment - Click Here To View");
			$displayCode .= ("</a>");
		}
		
		return $displayCode;
	}
	
	function uploadAttachment($fileArray){
		$target_path = "uploads/";

		$target_path = $target_path . basename($fileArray['name']); 

		if (move_uploaded_file($fileArray['tmp_name'], $target_path)) {
    		//echo "The file ".  basename($fileArray['name']). " has been uploaded";
		} else{
   			echo "There was an error uploading the file, please try again!";
		}
	}
	
	function display_onbehalf_form_element() {
		$schoolUIDS = ps_sanitise_array($_SESSION['currentUser']['school_uid']);
		
		$output  = "<div class=\"control-group\">";
		
		
		if (isTechnician()){
			$users = User::find_all_active();
			//$users = User::find_all();
			
			$output .= "<label class=\"control-label\" for=\"description\">Log On Behalf Of</label>";
			$output .= "<div class=\"controls\">";
			$output .= ("<select id=\"onBehalfOfUser\">");
			
			foreach ($users AS $user) {
				$schoolUIDS = $user->schoolUIDS();
				
				foreach ($schoolUIDS AS $schoolUID) {
					$userSchool = Group::find_by_uid($schoolUID);
					$output .= optionDropdown($user->uid, $user->full_name(), $_SESSION['currentUser']['uid']);
				}
			}
			$output .= ("</select>");
			$output .= "</div>";
		} else {
			$output .= "<label class=\"control-label\" for=\"description\">Log On Behalf Of</label>";
			$output .= "<div class=\"controls\">";
			
			$output .= ("Log As: <select id=\"onBehalfOfUser\">");
			
			foreach ($schoolUIDS AS $schoolUID) {
				$userSchool = Group::find_by_uid($schoolUID);
				$output .= optionDropdown($_SESSION['currentUser']['uid'], ($_SESSION['currentUser']['firstname'] . " at " . $userSchool->name), $schoolUID);
			}
			
			$output .= ("</select>");
			$output .= "</div>";
		}
		$output .= "</div>";
		return $output;
	}
?>