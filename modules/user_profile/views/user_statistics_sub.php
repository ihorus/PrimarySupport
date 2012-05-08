<?php
require_once("../../../engine/initialise.php");

$user = User::find_by_uid($_POST['uUID']);
$schoolUIDS = ps_sanitise_array($user->school_uid);

foreach ($schoolUIDS AS $schoolUID) {
	$usersSchool[] = Group::find_by_uid($schoolUID);
}

if (isTechnician($user->uid) || isAdmin($user->uid)) {
	// user is tech/admin - show jobs they're working on, as opposed to logged
	$usersActiveJobs = Support::find_by_sql("SELECT * FROM jobs WHERE owner_uid = '" . $user->uid . "' AND active = 1 AND type = 'Job' ORDER BY priority ASC, entry ASC");
	$usersCompletedJobs = Support::find_by_sql("SELECT * FROM jobs WHERE owner_uid = '" . $user->uid . "' AND active = 0 AND type = 'Job'");
	$visits = Visit::allVisitsByTechnician($user->uid);
	
	foreach ($visits AS $visit) {
	
		$visitTotalTime =  date('U', strtotime($visit->departure)) - date('U', strtotime($visit->arrival));
		
		if (isset($_POST['month']) && $_POST['month'] <> "All"){
			if (date('Y, M', strtotime($visit->arrival)) == date('Y, M', strtotime($_POST['month']))) {
				$visitsTotalTime = $visitsTotalTime + $visitTotalTime;
				$visitTotal = $visitTotal +1;
			}
			
		} else {
			$visitsTotalTime = $visitsTotalTime + $visitTotalTime;
			$visitTotal = $visitTotal +1;
		}
	}
	
} else {
	// user is normal user - show jobs they've logged
	$usersActiveJobs = Support::find_by_sql("SELECT * FROM jobs WHERE user_uid = '" . $user->uid . "' AND active = 1 AND type = 'Job'");
	$usersCompletedJobs = Support::find_by_sql("SELECT * FROM jobs WHERE user_uid = '" . $user->uid . "' AND active = 0 AND type = 'Job'");
}
$usersResponses = Support::find_by_sql("SELECT * FROM jobs WHERE user_uid = '" . $user->uid . "' AND type = 'Response'");

?>
<div class="row-fluid">
	<div class="span6">
	<h2><?php echo $_POST['month']; ?></h2>
	<p>Current Active Jobs: <?php echo count($usersActiveJobs); ?></p>
	<p>Completed Jobs: <?php echo count($usersCompletedJobs); ?></p>
	<?php
	$decimalAccuracy = 2;
	$totalJobs = round(count($usersActiveJobs) + count($usersCompletedJobs),$decimalAccuracy);
	
	if ($totalJobs > 0) {
		$responsesPerJob = round(count($usersResponses)/$totalJobs,$decimalAccuracy);
	} else {
		$responsesPerJob = 0;
	}
	?>
	
	<p>Total Responses: <?php echo count($usersResponses); ?> <i>(avg. <?php echo $responsesPerJob; ?> per job)</i></p><br />
	
	<?php
	if (isTechnician($user->uid) || isAdmin($user->uid)) {
		$stat  = "<p>";
		$stat .= "Time Spent on Primary Support: " . round($visitsTotalTime/60/60,$decimalAccuracy) . " hours ";
		$stat .= "<i>(" . $visitTotal . autoPluralise(" visit", " visits", $visitTotal) . ")</i>";
		$stat .= "</p>";
			
			echo $stat;
	}
	?>
	</div>
	<div class="span6">
	<div class="progress progress-info progress-striped">
		<div class="bar" style="width: 20%;"></div>
	</div>
	<div class="progress progress-alert progress-striped">
		<div class="bar" style="width: 40%;"></div>
	</div>
	<div class="progress progress-warning progress-striped">
		<div class="bar" style="width: 60%;"></div>
	</div>
	<div class="progress progress-danger progress-striped">
		<div class="bar" style="width: 80%;"></div>
	</div>
	<div class="progress progress-success progress-striped">
		<div class="bar" style="width: 100%;"></div>
	</div>
</div>