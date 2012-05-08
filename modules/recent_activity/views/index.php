<?php
gatekeeper(3);
$schoolUIDS = ps_sanitise_array($_SESSION['currentUser']['school_uid']);

?>


<?php
/*
$days = Activity::find_days_of_activity();
foreach ($days AS $event) {
	echo $event->monthYear;
	echo ("<br>");
	
	$jobs = Activity::find_activity_by_yearMonth($event->monthYear);
		echo (date('Y', strtotime($event->monthYear)) . " had " . count($jobs) . " jobs");
		echo ("<br>");
}

*/
?>

<?php
// $daterange is an array of friendly names, followed by their inclusive dates (in unixtime)
// the structure is "{{friendly name}} => "UNIXTIME FROM, UNIXTIME TO"
// for example, "Today" => "1284030348, 1283943948"
$todayStart = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
$todayEnd = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('d'), date('Y')));

$yesterdayStart = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
$yerterdayEnd = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('d')-1, date('Y')));

$weekStart = date('Y-m-d', mktime(1, 0, 0, date('m'), date('d')-date('w')+1, date('Y')));
$weekEnd = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('d')-2, date('Y')));

$monthStart = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
$monthEnd = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('d')-3, date('Y')));

$todayInclusive = strtotime($todayStart) . "," . strtotime($todayEnd);
$yesterdayInclusive = strtotime($yesterdayStart) . "," . strtotime($yerterdayEnd);
$thisWeekInclusive = strtotime($weekStart) . "," . strtotime($weekEnd);
$thisMonthInclusive = strtotime($monthStart) . "," . strtotime($monthEnd);

// build a $dateRange array with all the inclusive dates
$dateRange = array	(	"Today" => $todayInclusive,
						"Yesterday" => $yesterdayInclusive,
						"This Week" => $thisWeekInclusive,
						"This Month" => $thisMonthInclusive
					);

//print_r($dateRange);

// itterate through each date in $dateRange
foreach ($dateRange AS $dateUnique => $value) {
	// get the 2 unix timestamps held in $value, and sperate them into an array
	$dates = explode(",",$value);
	
	//the first value is the dateFrom, the second is the dateTo value
	$dateFrom = $dates[0];
	$dateTo = $dates[1];
	
	if (isTechnician()) {
		//$activity = Activity::find_recent_activity($all = TRUE);
		$activity = Activity::find_total_activity_by_daterange($dateFrom, $dateTo);
	} else {
		$activity = Activity::find_total_activity_by_daterange($dateFrom, $dateTo, $schoolUIDS);
	}
	
	if (count($activity) > 0) {
	echo ("<h1>Activity " .  $dateUnique . "</h1>");
	
	foreach ($activity AS $event) {
			$user = User::find_by_uid($event->user_uid);
			$school = Group::find_by_uid($event->school_uid);

			if ($event->type == 'Job') {
				//do this if it's a new job
				$itemInsert  = tagUser($event->user_uid);
				$itemInsert .= " logged job ";
				$itemInsert .= tagJob($event->uid);
				$itemInsert .= " for ";
				$itemInsert .= tagGroup($event->school_uid);
			} elseif ($event->type == 'Response') {
				//do this if it's a new response
				
				$originalJob = Support::find_by_uid($event->spawn);
				
				
				$itemInsert  = tagUser($event->user_uid);
				$itemInsert .= " responded to ";
				$itemInsert .= tagGroup($originalJob->school_uid);
				$itemInsert .= " job ";
				$itemInsert .= tagJob($event->spawn);

			} elseif ($event->type == 'Info') {
				//do this if it's an info update
				$itemInsert = expandInfoBar($event->description);
			} elseif($event->type == 'Visit') {
				// do this if it's a visit
			}
			
			echo "<p>" . $itemInsert . "</p>";
		}	
	}
}
?>