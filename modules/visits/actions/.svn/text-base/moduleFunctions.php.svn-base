<?php
function timeTotalDisplay($seconds = 0) {	
	$secondsInMinute = 60;
	$secondsInHour = $secondsInMinute * 60;
	$secondsInDay = $secondsInHour * 24;
	$secondsInWeek = $secondsInDay * 7;
	
	if ($seconds < $secondsInMinute) {
		// VISIT IN SECONDS
		$strTime = $seconds . " seconds";
	} elseif ($seconds < $secondsInHour) {
		// VISTIN IN MINUTES
		$strTime = $seconds/$secondsInMinute . " minutes";
	} elseif ($seconds < $secondsInDay) {
		// VISIT IN HOURS
		$hours = floor($seconds / $secondsInHour);
		$minutes = ($seconds - ($hours * $secondsInHour)) / $secondsInMinute;
		
		// pad with 0's if required to make minutes a 2 digit number
		//$minutes = sprintf("%02d", $minutes);
		
		// FRIENDLY TIME
		//$strTime = $hours . autoPluralise(" hour", " hours", $hours) . " " . $minutes . autoPluralise(" minute", " minutes", $minutes);
		
		// pad with 0's if required to make minutes a 2 digit number
		$minutes = sprintf("%02d", $minutes);
		$strTime = $hours . ":" . $minutes . autoPluralise(" hour", " hours", $hours);
	} elseif ($seconds < $secondsInWeek) {
		// VISIT IN DAYS
		$days = floor($seconds / $secondsInDay);
		$hours = floor($seconds / $secondsInHour);
		
		$strTime = "Over ". $days . autoPluralise(" day", " days", $days);
	}
	return $strTime;
} // END function dateDisplay()

?>