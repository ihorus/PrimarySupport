<?php

$visit = Visit::find_by_uid($_GET['visitUID']);
$activity = Activity::relevantActivity($visit->arrival, $visit->school_uid);

if (isTechnician() || $visit->school_uid == $_SESSION['currentUser']['school_uid']) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}


$school = Group::find_by_uid($visit->school_uid);
$technician = User::find_by_uid($visit->user_uid);

$techCostPerHour = $visit->tech_hourly;
$totalMinutes = (date('U', strtotime($visit->departure)) - date('U', strtotime($visit->arrival)))/60;
$totalCost = $techCostPerHour * ($totalMinutes/60);

if ($visit->mileage_claim ==1) {
	$totalMileage = $school->distance;
} else {
	$totalMileage = 0;
}
$totalMileageCost = $totalMileage * SITE_PERMILE;
$finalCost = $totalMileageCost + $totalCost;
?>

<div class="grid_9 alpha omega">
	<h2>Visit to <?php echo ($school->name . " (" . $visit->uid . ")"); ?></h2>
	<table>
		<th>Description</th>
		<th>Cost</th>
		<tr>
			<td>
			<div class="grid_1 alpha">
			<?php echo $technician->gravatarURL(true); ?>
			</div>
			Date: <?php echo date('l jS \of F Y', strtotime($visit->arrival)); ?><br />
			Arrival: <?php echo (date('H:i', strtotime($visit->arrival))); ?>
			- Departure: <?php echo (date('H:i', strtotime($visit->departure))); ?><br />
			Total: <?php echo timeTotalDisplay($totalMinutes*60); ?>
			<div class="clear"></div>
			<?php echo ($visit->description); ?>
			</td>
			<td><?php echo moneyDisplay($totalCost); ?></td>
		</tr>
		<?php if ($visit->mileage_claim == 1) {
			echo ("<tr>");
			echo ("<td>Mileage: " . $school->distance . " miles</td>");
			echo ("<td>" . moneyDisplay($school->distance * SITE_PERMILE) . "</td>");
			echo ("</tr>");
		}
		?>
		<tr class="altrow">
			<td>Total</td>
			<td><?php echo moneyDisplay($finalCost); ?></td>
		</tr>
	</table>
	
	<?php
	if (count($activity) > 0) {
		echo "<h2>" . "Activity near the time of this visit" . "</h2>";
	}
	foreach ($activity AS $item) {
		if ($item->type == "Job") {
			$job = Support::find_by_uid($item->uid);
			echo $job->displayJob();
		
		} elseif ($item->type == "Response") {
			$job = Support::find_by_uid($item->spawn);
			echo $job->displayJob();
		} elseif ($item->type == "Info") {
			// this works, but I don't think it really needs to be displayed
			/*
			$response = new Response();
			$response->uid = $item->spawn;
			$response->school_uid = $item->school_uid;
			$response->user_uid = 2;
			$response->spawn = $item->uid;
			$response->type = "Info";
			$response->description = $item->description;
			$response->entry = $item->entry;

			echo $response->displayResponse();
			*/
		}
	}
	?>
</div>