<?php
require_once("engine/initialise.php");

// locate all active users in the database
$users = User::find_all_active();

$veryStagnantAge = 5 * 86400;
$semiStagnantAge = 2 * 86400;

// itterate through each user
foreach ($users AS $user) {
	if (isTechnician($user->uid)){
		// findout if this user has any stagnant jobs
		$anyStagnantJobs = Support::stagnantJobs($dateFrom = NULL, $dateTo =  NULL, $userUID = $user->uid);
	
		$veryStagnant = Support::stagnantJobs($dateFrom = date('U')-864000000, date('U')-($veryStagnantAge - 1), $userUID = $user->uid);
		$semiStagnant = Support::stagnantJobs($dateFrom = date('U')-$veryStagnantAge, $dateTo = date('U')-$semiStagnantAge, $userUID = $user->uid);
		
		$userArray[] = $user->firstname;
		$veryStagArray[] = count($veryStagnant);
		$semiStagArray[] = count($semiStagnant);
		$allArray[] = count($anyStagnantJobs) - (count($veryStagnant) + count($semiStagnant));
	}
}


krsort($userArray);

// stop the chart from auto-adusting the axis scale, once jobs reduce to 20 or less
// this way, it is clear when there aren't many jobs
if (!count($anyStagnantJobs) < 20) {
	$totalMaxChart = 20;
} else {
	// more than 20 jobs.  Have the axis auto-scale to accomodate the data.
	$totalMaxChart = count($anyStagnantJobs);
}

$url = "http://chart.apis.google.com/chart";
$chartType = "?cht=bhs";
$chartColours = "&chco=cd312b,e1e42d,639956";
$chartSize = "&chs=500x140&chf=bg,s,111111";
$chartSeries = "&chd=t:" . implode($veryStagArray,",") . "|" . implode($semiStagArray,",") . "|" . implode($allArray,",");
$chartAxis = "&chxt=x,y";
$chartMinMax = "&chds=0," . $totalMaxChart;
$chartAxisLabels = "&chxl=1:|" . implode($userArray,"|") . "|";
$chartAxisLabels .= "&chxr=0,0," . $totalMaxChart . ",5";
$chartImage = $url . $chartType . $chartColours . $chartSize . $chartSeries . $chartAxis . $chartMinMax . $chartAxisLabels;
?>

<img src="<?php echo $chartImage; ?>">