<?php

if ($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'] == $_GET['userUID']) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}

$user = User::find_by_uid($_GET['userUID']);
$usersSchool = Group::find_by_uid($user->school_uid);


$visits = Visit::allVisitsByTechnician($user->uid);
	
	foreach ($visits AS $visit) {
	
		$visitTotalTime =  date('U', strtotime($visit->departure)) - date('U', strtotime($visit->arrival));
		
		if (isset($_POST['monthSelected']) && $_POST['monthSelected'] <> "All"){
			if (date('Y, M', strtotime($visit->arrival)) == date('Y, M', strtotime($_POST['monthSelected']))) {
				$visitsTotalTime = $visitsTotalTime + $visitTotalTime;
				$visitTotal = $visitTotal +1;
			}
			
		} else {
			$visitsTotalTime = $visitsTotalTime + $visitTotalTime;
			$visitTotal = $visitTotal +1;
		}
	}
	

?>


<!-- main -->
<div id="main">
	<h2><?php echo (ucfirst(strtolower($user->firstname)) . " " . ucfirst(strtolower(($user->lastname)))); ?></h2>
	<div class="grid_9 alpha omega">
		<h3>Statistics:</h3>
		<form target="_self" method="POST" name="changeDateRange" id="changeDateRange">
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
		<p>Total Responses: <?php echo count($usersResponses); ?> <i>(avg. <?php echo $responsesPerJob; ?> per job)</i></p>
		
		<br />
		<?php
			// build an array on months
			$mon = array(NULL, 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
			$current = date('n');
			
			$i = 100;
			$monthNum = date('n');
			$yearNum = date('Y');
			do {
				$months[] = date('Y, M', strtotime($yearNum . "-" . $monthNum));
				
				//if ($current)
				//$months[] = date('Y, M', mktime(1, 0, 0, date('m'), date('d'), date('Y')));		// this month
				
				if ($monthNum == 1) {
					$monthNum = 12;
					$yearNum = $yearNum-1;
				} else {
					$monthNum = $monthNum - 1;
				}
				
				$i = $i - 1;
			} while ($i > 0);
			
			/*
			$months[] = date('Y, M', mktime(1, 0, 0, date('m'), date('d'), date('Y')));		// this month
			$months[] = date('Y, M', mktime(1, 0, 0, date('m')-1, date('d'), date('Y')));	// last month
			$months[] = date('Y, M', mktime(1, 0, 0, date('m')-2, date('d'), date('Y')));	// 2 months ago
			$months[] = date('Y, M', mktime(1, 0, 0, date('m')-3, date('d'), date('Y')));	// 3 months ago
			$months[] = date('Y, M', mktime(1, 0, 0, date('m')-4, date('d'), date('Y')));	// 4 months ago
			*/
			
			$form .= "<select name=\"monthSelected\" id=\"monthSelected\" onchange=\"changeDateRange.submit()\">";
			$form .= optionDropdown("All", "All", $_POST['monthSelected']);
			foreach ($months AS $month) {
				$form .= optionDropdown($month, $month, $_POST['monthSelected']);
			}
			$form .= "</select>";
			
			echo $form;
		?>
		<?php if (isTechnician($user->uid) || isAdmin($user->uid)) {
			foreach ($months AS $month) {
				echo $month . ",";
				
				$visitTotalTime = 0;
				foreach ($visits AS $visit) {
					
					
					$visitTotalTime =  date('U', strtotime($visit->departure)) - date('U', strtotime($visit->arrival));
		
					
					if (date('Y, M', strtotime($visit->arrival)) == date('Y, M', strtotime($month))) {
						$visitsTotalTime = $visitsTotalTime + $visitTotalTime;
					}
				}
				echo ($visitsTotalTime/60);
				echo "<br />";
			}
			
			$stat  = "<p>";
			$stat .= "Time Spent on Primary Support: " . round($visitsTotalTime/60/60,$decimalAccuracy) . " hours ";
			$stat .= "<i>(" . $visitTotal . autoPluralise(" visit", " visits", $visitTotal) . ")</i>";
			$stat .= "</p>";
			
			echo $stat;
		}
		?>
		</form>
	</div>
	
	<div class="clear"></div>
	<?php
	echo ("<h1>" . count($Jobs) . " " . autoPluralise("Job", "Jobs", count($activeJobs)) . "</h1>");		
	?>
	
	
	</div>
</div>