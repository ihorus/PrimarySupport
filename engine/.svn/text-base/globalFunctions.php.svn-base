<?php
function autoPluralise ($singular, $plural, $count = 1) {
	// fantasticly clever function to return the correct plural of a word/count combo
	// Usage:	$singular	= single version of the word (e.g. 'Bus')
	//       	$plural 	= plural version of the word (e.g. 'Busses')
	//			$count		= the number you wish to work out the plural from (e.g. 2)
	// Return:	the singular or plural word, based on the count (e.g. 'Jobs')
	// Example:	autoPluralise("Bus", "Busses", 3)  -  would return "Busses"
	//			autoPluralise("Bus", "Busses", 1)  -  would return "Bus"

	return ($count == 1)? $singular : $plural;
} // END function autoPluralise

function fileInclude($file) {
	if (file_exists($file)) {
		include_once($file);
	} else {
		echo ("<img src=\"..\\images\\404.png\">");
		echo ("<br />");
		echo ("The file '" . $file . "' doesn't exist.");
	}
}

function paragraphTidyup($string = "") {
	// clean up the text to replace hard returns "\r" with proper HTML markup "<br /"
	// Usage:	$string	=	paragraph or string of text you wish to replace hardreturns with
	//						proper html markup. (e.g. 'Hello,\r World')
	// Return:	the same string, with <br /> tags inplace of hard returns AND URLs fully hyperlinked
	
	// terrible preg_replace pattern, I know - why don't you do better!
	$string = preg_replace('@((https|http|www)://([-\w\.]+)+(:\d+)?(/([\w/_\.\-\%\+]*(\?\S+)?)?)?)@', '<a href="$1">$1</a>', $string);
	
	//replace any hard returns with proper HTML <br /> tags
	$string = str_replace("\r", "<br />", $string);
	
	return $string;
} // END function paragraphTidyUp


function howLongAgo($strPastDate) {
	$diff = time() - ((int) $strPastDate);
	
	if ($diff < 0) {
		return FALSE;
	} else if ($diff < 60) {
		return ("just now");
	} else if ($diff < 3600) {
		// minutes ago
		$diff = round($diff / 60);
		if ($diff == 0) {
			$diff = 1;
		}
		$diff = $diff . (autoPluralise (" minute", " minutes", $diff)) . " ago";

		return ($diff);
	} else if ($diff < 86400) {
		// hours ago
		$diff = round($diff / 3600);
		if ($diff == 0) {
			$diff = 1;
		}
		$diff = $diff . (autoPluralise (" hour", " hours", $diff)) . " ago";

		return ($diff);
	} else if ($diff < 2592000) {
		// days ago
		$diff = round($diff / 86400);
		if ($diff == 0 | $diff == 1) {
			$diff = ("yesterday");
			return $diff;
		}
		$diff = $diff . (autoPluralise (" day", " days", $diff)) . " ago";
		return ($diff);
	} else if ($diff < 31536000) {
		//months ago
		$diff = round($diff / 2592000);
		$diff = $diff . (autoPluralise (" month", " months", $diff)) . " ago";
		return ($diff);
	} else {
		// years ago
		$diff = round($diff / 31536000);
		$diff = $diff . (autoPluralise (" year", " years", $diff)) . " ago";
		return ($diff);
	}

}




function dateDisplay($strUnixTime, $age=false) {
	// check if the time element is '00:00' - meaning time isn't a consideration
	if (date('H:i', $strUnixTime) == "00:00") {
		// time element is '00:00' - so change the mask to not display it
		$strDateTime = date('l jS \of F Y', $strUnixTime);
	} else {
		// time element is specified - so use the date mask to display it
		$strDateTime = date('l jS \of F Y H:i', $strUnixTime);
	}
	
	// should we even show how old the date/time is?
	if ($age == true) {
		// check that we get a value back from howLongAgo, otherwise, the age isn't valid and shouldn't be displayed
		if (howLongAgo($strUnixTime)) {
			$strDateTime = $strDateTime . " <i>(" . howLongAgo($strUnixTime) . ")</i>";
		}
	}
	return $strDateTime;
} // END function dateDisplay()

function moneyDisplay($value = FALSE, $showSymbol = TRUE) {
	$currencySign = "&#163;";
	$value = round($value,2);
	
	$value = number_format($value, 2, '.', ',');
	
	if ($showSymbol == TRUE) {
		$value = $currencySign . $value;
	}
	
	return $value;
}




function sendMail($recipient, $subject, $message) {

	if (!isset($subject)) {
		$subject = (SITE_NAME . " - " . SITE_SLOGAN);
	}
	// Function to send a simple e-mail
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	$headers .= "To: " . $recipient . "\r\n";
	$headers .= "From: " . SITE_NAME . " <no-reply@wallingford.oxon.sch.uk>" . "\r\n";
	$headers .= "Reply-To: no-reply@wallingford.oxon.sch.uk" . "\r\n";
	
	// Additional headers
	// $headers .= 'Cc: birthdayarchive@example.com' . "\r\n";
	// $headers .= 'Bcc: birthdaycheck@example.com' . "\r\n";
	
	$message .= "<br /><br />";
	$message .= "Don't want to receive these e-mails?  Log on to the " . SITE_NAME . " site and change the settings under your profile 'preferences'.";

	$message  = paragraphTidyup($message);
		
	if (isset($recipient)) {
		mail($recipient, $subject, $message, $headers);
	} else {
		echo("<p>Message delivery failed...</p>");
		echo $recipient . $subject . $message;
		return FALSE;
	}
}

function gatekeeper($pageRequirement = 99) {
	global $pageAccess;
	
	$pageAccess = $pageRequirement;
}

function gatekeeperCheck($pageAccess = 99) {
	// security check!
	global $pageAccess;
	
	$currentUser = User::find_by_uid($_SESSION['currentUser']['uid']);

	if (is_null($_SESSION['currentUser']['uid'])) {
		$currentUser->access = 999;
	}
	
		if ($pageAccess == 0) {
			return TRUE;
		} elseif ($pageAccess >= $currentUser->access) {
			return TRUE;
		} else {
			return FALSE;
		}
}

function optionDropdown($value, $display, $selected) {
 	$option  = ("<option ");
 	
 	// check if the selected option is an array
	if (is_array($selected)){
		// if so, we need to check EACH element against the $value
		foreach ($selected AS $select) {
			if ($select == $value) {
				$option .= (" selected ");
			}
		}
	} else {
		if ($selected == $value) {
			$option .= (" selected ");
		}
	}
	
	$option .= ("value=\"");
	$option .= ($value);
	$option .= ("\">");
	$option .= ($display);
	$option .= ("</option>");
	
	return $option;
}

function printArray($array) {
	echo ("<pre>");
	print_r ($array);
	echo ("</pre>");
}

function displayToolbar($addURL = "#", $editURL = "#", $deleteURL = "#") {
	$addIcon = "<i class=\"icon-plus-sign\"></i>";
	$editIcon = "<i class=\"icon-edit\"></i>";
	$deleteIcon = "<i class=\"icon-minus-sign\"></i>";
	
	$bar  = "<div class=\"pull-right\">";
	$bar .= "<a href=\"" . $deleteURL . "\" title=\"Add New\">" . $addIcon . "</a>";
	$bar .= "<a href=\"" . $deleteURL . "\" title=\"Edit\">" . $editIcon . "</a>";
	$bar .= "<a href=\"" . $deleteURL . "\" title=\"Delete\">" . $deleteIcon . "</a>";
	$bar .= "</div>";
	
	echo $bar;
}

function isAdmin($userUID = NULL) {
	if ($userUID == NULL) {
		// set the user in question to be the current logged in user
		$userUID = $_SESSION['currentUser']['uid'];
	}
	
	$userInQuestion = User::find_by_uid($userUID);

	if ($userInQuestion->access == 1) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function isTechnician($userUID = NULL) {
	if ($userUID == NULL) {
		// set the user in question to be the current logged in user
		$userUID = $_SESSION['currentUser']['uid'];
	}
	
	$userInQuestion = User::find_by_uid($userUID);

	if ($userInQuestion->access <= 2) {
		return TRUE;
	} else {
		return FALSE;
	}
}

function availableModules() {
	$searchDir = SITE_LOCATION . "/modules/";
	$resultsOfDir = scandir($searchDir);

	foreach ($resultsOfDir AS $result) {
		if (is_dir($searchDir . $result)) {
			if (file_exists($searchDir . $result . "/start.php")) {
				$availableModules[] = $result;
			}
		}
	}
	
	return $availableModules;
}

function enabledModules() {
	global $database;
	
	$settings = AvailableSettings::get_setting("site_enabled_modules");
	$setting = $settings[0]->settingDefaultValue;
	
	$modules = explode(",", $setting);
	
	return $modules;
}

function randomString() {
	// an array of random words - case sensitive!
	$randomWordsArray = array('edit', 'happy', 'green', 'red', 'yellow', 'blue', 'sheet', 'replace', 'machine', 'invoice', 'support', 'office', 'london', 'oxford', 'number');
	
	// count how many items in the $randomWordsArray, minus 1 obviously!
	$randomWordsArrayCount = count($randomWordsArray) - 1;
	
	// generate 2 random numbers between 0, and the $randomWordsArrayCount total
	// the do/while loop ensures that the 2 random numbers are not the same
	do {
		$randomNumber1 = rand(0, $randomWordsArrayCount);
		$randomNumber2 = rand(0, $randomWordsArrayCount);
	} while (!$randomNumber2 == $randomNumber1);
	
	// generate 1 additional random number between 0 and 999
	$randomNumber3 = rand(0, 999);
	
	// stick it all together, using a random word, the random number(3), then a second random word
	$string = $randomWordsArray[$randomNumber1] . $randomNumber3 . $randomWordsArray[$randomNumber2];
	
	//return the complete random string
	return $string;
}

function nodeName() {
	if (isset($_GET['m'])) {
		$fullNode = $_GET['m'];
		
		//remove the slashes
		$firstSlash = strpos($fullNode, "/");
		$fullNode = substr($fullNode, 0, $firstSlash);
		
	} elseif (isset($_GET['n'])) {
		$fullNode = $_GET['n'];
	} else {
		$fullNode = "home";
	}
	
	return $fullNode;
	}

function ps_sanitise_array($array = NULL) {
	// function to take an array (or comma-seperated string) and make sure that the return is a clean
	// well formed array.
	
	// if the passed string isn't an array - build it into one
	if (!is_array($array)) {
		$output = explode(",", $array);
	} else {
		$output = $array;
	}
	
	$output = array_filter($output);
	
	return $output;
}

function makeCategory($category = NULL) {
	$output  = "<span class=\"label label-inverse\">";
	$output .= $category;
	$output .= "</span>";
	
	return $output;
}

function tagUser($userUID = NULL) {
	$user = $user = User::find_by_uid($userUID);
	
	$output  = "<a href=\"node.php?n=user_unique&userUID=" . $user->uid . "\">";
	$output .= "<span class=\"label label-info\">";
	$output .= $user->firstname . " " . $user->lastname;
	$output .= "</span>";
		$output .= "</a>";
	
	return $output;
}

function tagJobInfo($jobUID = NULL) {
	$job = Support::find_by_uid($jobUID);
	$poster = $user = User::find_by_uid($job->user_uid);
	$owner = $user = User::find_by_uid($job->owner_uid);
	
	$title = "Ticket: " . $job->uid;
	
	$content  = "<small>" . dateDisplay(strtotime($job->entry), true) . "</small><hr />";
	$content .= "Originally Logged By: " . $poster->full_name() . "<br />";
	$content .= "Current Technician: " . $owner->full_name() . "<br />";
	$content .= "Priority: " . priorityName($job->priority) . "<br />";
	$content .= "Total Comments: " . $job->totalResponses() . "<br />";
	$content .= "";
	$content .= "";
	$content .= "";
	
	//$jobNode .= "<a href=\"node.php?n=user_unique&amp;userUID=\" >" . tagJob($this->uid) . "</a>";
	

	$output  = "<a href=\"node.php?m=support/views/support_unique.php&supportUID=" . $jobUID . "\" class=\"btn btn-mini btn-info test123\" rel=\"popover\" title=\"" . $title . "\" data-content=\"" . $content . "\">";
	$output .= "<i class=\"icon-info-sign icon-white\"></i>";
	$output .= "</a>";
	
	return $output;
}

function tagJob($jobUID = NULL) {
	$output  = "<a href=\"node.php?m=support/views/support_unique.php&supportUID=" . $jobUID . "\">";
	$output .= "<span class=\"label label-success\">";
	$output .= $jobUID;
	$output .= "</span>";
	$output .= "</a>";
	
	return $output;
}


function tagGroup($groupUID = NULL) {
	$group = Group::find_by_uid($groupUID);
	
	$output  = "<a href=\"node.php?n=school_overview&schoolUID=" . $group->uid . "\">";
	$output .= "<span class=\"label\">";
	$output .= $group->name;
	$output .= "</span>";
	$output .= "</a>";
	
	return $output;
}

function cacheResult($cacheTitle = NULL, $cacheValue = NULL) {
	$_SESSION['cachedValues'][$cacheTitle] = $cacheValue;
}

function cacheCheck($cacheTitle = NULL) {
	if (isset($_SESSION['cachedValues'][$cacheTitle])) {
		$returnValue = TRUE;
	} else {
		$returnValue = FALSE;
	}
	
	return $returnValue;
}

function cacheReturn($cacheTitle = NULL) {
	return $_SESSION['cachedValues'][$cacheTitle];
}
?>
