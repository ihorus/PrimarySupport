<?php
require_once("../../../engine/initialise.php");
//printArray($_GET);
$user = User::find_by_uid($_GET['uUID']);
$schoolUIDS = ps_sanitise_array($user->school_uid);

foreach ($schoolUIDS AS $schoolUID) {
	$usersSchool[] = Group::find_by_uid($schoolUID);
}

$sql  = "SELECT * FROM jobs ";
$sql .= "WHERE type = 'Job' ";

// if we're searching for a user, restrict to users jobs, or technicians jobs
if ($user->type == "School") {
	$sql .= "AND user_uid = '" . $user->uid . "' ";
} else {
	$sql .= "AND owner_uid = '" . $user->uid . "' ";
}

// filter active/complete/all jobs
if ($_GET['filter'] == "active") {
	$sql .= "AND active = '1' ";
	
	// order to something sensible
	$sql .= "ORDER BY priority ASC, entry ASC";
} elseif ($_GET['filter'] == "completed") {
	$sql .= "AND active = '0' ";
	// order to something sensible
	$sql .= "ORDER BY job_closed DESC";
} else {
	$sql .= "ORDER BY priority ASC, entry ASC";
}

$Jobs = Support::find_by_sql($sql);

?>

<?php
// UNCLAIMED JOBS
$unclaimedJobs = Support::find_unclaimed();
if (isTechnician() && count($unclaimedJobs) > 0) {
?>
<div class="row-fluid">
	<div class="span12">
		<?php
		echo ("<h1>" . count($unclaimedJobs) . " Unclaimed " . autoPluralise("Job", "Jobs", count($unclaimedJobs)) . "</h1>");
		
		foreach ($unclaimedJobs as $job) {
			$output1 .= $job->displayJob();
		}
		echo $output1;
		?>
	</div>
</div>
<?php
}
?>

<?php
// RECENTLY ASSIGNED JOBS
$assignedJobs = Support::find_recently_assigned($user->uid);
if (isTechnician() && count($assignedJobs) > 0) {
?>
<div class="row-fluid">
	<div class="span12">
		<?php
		echo ("<h1>" . count($assignedJobs) . " Recently Assigned " . autoPluralise("Job", "Jobs", count($assignedJobs)) . "</h1>");
		
		foreach ($assignedJobs as $job) {
			$output2 .= $job->displayJob();
		}
		echo $output2;
		?>
	</div>
</div>
<?php
}
?>


<div class="row-fluid">
	<div class="span12">
		<div id="jobs_paginated">
			<?php
			echo ("<h1>" . count($Jobs) . " " . autoPluralise("Job", "Jobs", count($Jobs)) . "</h1>");
			
			foreach ($Jobs as $job) {
				$output .= $job->displayJob();
			}
			echo $output;
			?>
		</div>
	</div>
</div>
<script>
$(".test123").mouseover(function() {
	//alert('test');
	$(this).popover('show')
});
</script>