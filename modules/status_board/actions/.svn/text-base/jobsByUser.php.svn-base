<?php
require_once("../../../engine/initialise.php");

// Find all jobs, and group them by user.  This returns the technician UID, and their current total active jobs
$activeJobsByUser = Support::find_by_sql("SELECT jobs.owner_uid, COUNT(jobs.uid) AS totalJobs FROM jobs WHERE jobs.type = 'Job' AND jobs.active = 1 AND jobs.owner_uid <> '6' GROUP BY jobs.owner_uid DESC");

// Find all active jobs
$allActiveJobs = $allJobs = Support::find_all_active();

// If the total jobs is less than 30, I don't want to use the 'count()' feature.
// instead, I'll hard code the totalJobs value at 30.  This way, when there are only
// a few jobs left in the database, it won't look like we're really busy!
if ($allActiveJobs = Support::find_all_active() > 20) {
	$totalActiveJobs = count($allJobs);
} else {
	//$totalActiveJobs = 30;
	$totalActiveJobs = count($allJobs);
}

// for each row found by SQL, itterate through...
foreach ($activeJobsByUser AS $uniqueActiveJobsByUser) {
	// find the user's firstname
	$user = User::find_by_uid($uniqueActiveJobsByUser->owner_uid);
	
	// do some math to find out how many jobs each technician has
	// don't forget, 100% is NO jobs, 0% is ALL of the jobs
	$totalUserJobs = 100 * ($uniqueActiveJobsByUser->totalJobs / $totalActiveJobs);
	$totalUserJobs = 100 - $totalUserJobs;
	
	// now build an array with the technicians name, with the value of their jobs (as a percentage)
	//$JobsByUserArray[$user->firstname] = 100-($uniqueJobsByUser->totalJobs/25*100);
	$JobsByUserArray[$user->firstname] = $totalUserJobs;
}

$totalJobsByUser = array_values($JobsByUserArray);
$users = array_keys($JobsByUserArray);



$baseURL = ("http://chart.apis.google.com/chart");
$chartType = ("?cht=gom&chtt=Busyometer");
$chartSize = ("&chs=295x295");
$chartLables = ("&chxl=0:|" . implode("|",$users) . "|1:|Busy|Normal|Quiet");
$chartInfo = ("&chf=bg,s,65432100&chxt=x,y");
$chartData = ("&chd=t:" . implode(",",$totalJobsByUser));



$url = ($baseURL . $chartType . $chartSize . $chartInfo . $chartData. $chartLables);


echo ("<img src=\"" . $url . "\" />");

?>