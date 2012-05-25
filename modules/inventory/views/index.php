<?php
gatekeeper(3);

$currentUser = User::find_by_uid($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']);
$schoolUIDS = ps_sanitise_array($currentUser->school_uid);

if (count($schoolUIDS) !== 1) {
	echo "Inventory not available to users associated with more than 1 school.";
	exit;
}


if (isset($_POST['add_room'])) {
	// add room
	$room = new Classroom();
	$room->school_uid = $_SESSION[SITE_UNIQUE_KEY]['cUser']['schoolUID'];
	$room->official_name = $_POST['official_name'];
	$room->friendly_name = $_POST['friendly_name'];
	$room->teacher = $_POST['teacher'];
	$room->notes = $_POST['notes'];
	
	// insert visit to the database
	$room->create();
}

$classrooms = Classroom::find_all_by_school($currentUser->school_uid);

?>

<div class="row">
<div class="span8">
	<div class="page-header">
		<h1>Inventory</h1>
	</div>
	
	<h2>Locations</h2>
	<?php
	
	echo ("<table class=\"table table-bordered table-striped\">");
	echo ("<thead><tr>");
	echo "<th style=\"width: 30%\">" . "Room Name" . "</th>";
	echo "<th style=\"width: 30%\">" . "Primary User" . "</th>";
	echo "<th style=\"width: 20%\">" . "Notes" . "</th>";
	echo "<th style=\"width: 20%\">" . "Status" . "</th>";
	echo ("</tr></thead>");
	
	foreach ($classrooms AS $classroom) {
		$uniqueItem  = "<tr>";
		$uniqueItem .= "<td>" . "<a href=\"node.php?m=inventory/views/room.php&amp;roomUID=" . $classroom->uid . "\">" . $classroom->roomName() . "</a></td>";
		$uniqueItem .= "<td>" . $classroom->teacher . "</td>";
		$uniqueItem .= "<td>" . $classroom->notes . "</td>";
		if (count($classroom->contents()) > 0) {
			if (strtotime($classroom->averageContentsModifiedDate()) >= strtotime("-1 year")) {
				$uniqueItem .= "<td>" . "<span class=\"label label-success\">Up-to-date</span>" . "</td>";
			} elseif (strtotime($classroom->averageContentsModifiedDate()) >= strtotime("-3 years")) {
				$uniqueItem .= "<td>" . "<span class=\"label label-warning\">Needs Updating</span>" . "</td>";
			} else {
				$uniqueItem .= "<td>" . "<span class=\"label label-important\">Missing</span>" . "</td>";
			}
		} else {
			$uniqueItem .= "<td>" . "<span class=\"label\">Empty</span>" . "</td>";
		}
			$uniqueItem .= "</tr>";
		echo $uniqueItem;
	}
	
	echo ("</table>");
	?>
</div>
<div class="span4">
	<?php
		$dateFrom = date('Y')-5 . "-09-01";
		$items = Inventory::itemsPurchasedBeforeByType($dateFrom);
		foreach ($items AS $item) {
			$itemArray[$item->type][] = $item->uid;
		}
	?>
	<div class="well">
		<h1 style="text-align:center;">Technology Refresh</h1>
		<h6 style="text-align:center;">Equipment Purchased On/Before <?php echo $dateFrom; ?></h6>
		<?php
		foreach ($items AS $item) {
			echo "<span class=\"badge badge-info\">" . $item->uid . "</span> " . $item->type . " (" . moneyDisplay($item->notes) . ")<br />";
			$totalValue = $totalValue + $item->notes;
		}
		
		echo "<h2 style=\"text-align:center;\">Total: " . moneyDisplay($totalValue) . "</h2>";
		?>
	</div>
</div>
<div class="span12">
	<form class="form-horizontal" target="_self" method="POST" name="add_job" id="add_job">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="name">Room Official Name</label>
			<div class="controls">
				<input type="text" class="span3" placeholder="e.g. T8" name="official_name">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="name">Room Friendly Name</label>
			<div class="controls">
				<input type="text" class="span3" placeholder="e.g. T8" name="friendly_name">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="teacher">Staff Name</label>
			<div class="controls">
				<input type="text" class="span3" placeholder="e.g. John Smith" name="teacher">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="notes">Notes</label>
			<div class="controls">
				<textarea class="input-xxlarge" name="notes" rows="5"></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<input class="btn-primary" type="submit" value="Add Room" id="submit">
			</div>
		</div>
		<input type="hidden" name = "add_room" />
	</fieldset>
	</form>
</div>
</div>