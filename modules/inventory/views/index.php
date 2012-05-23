<?php
gatekeeper(3);

$currentUser = User::find_by_uid($_SESSION['currentUser']['uid']);
$schoolUIDS = ps_sanitise_array($currentUser->school_uid);

if (count($schoolUIDS) !== 1) {
	echo "Inventory not available to users associated with more than 1 school.";
	exit;
}


if (isset($_POST['add_room'])) {
	// add room
	$room = new Classroom();
	$room->school_uid = $_SESSION['currentUser']['school_uid'];
	$room->name = $_POST['name'];
	$room->teacher = $_POST['teacher'];
	$room->notes = $_POST['notes'];
	
	// insert visit to the database
	$room->create();
}

$classrooms = Classroom::find_all_by_school($currentUser->school_uid);

?>

<div class="row">
<div class="span12">
	<div class="page-header">
		<h1>Inventory</h1>
	</div>
	
	<h2>Locations</h2>
	<?php
	foreach ($classrooms AS $room) {
		$uniqueRoom  = ("<a href=\"node.php?m=inventory/views/room.php&amp;roomUID=");
		$uniqueRoom .= ($room->uid . "\">");
		$uniqueRoom .= $room->name;
		if ($room->teacher) {
			$uniqueRoom .= (" <i>(" . $room->teacher . ")</i>");
		}
		$uniqueRoom .= ("</a>");
		
		echo $uniqueRoom;
		echo "<br />";
	}
	?>
</div>
<div class="span12">
	<form class="form-horizontal" target="_self" method="POST" name="add_job" id="add_job">
	<fieldset>
		<div class="control-group">
			<label class="control-label" for="name">Room Name</label>
			<div class="controls">
				<input type="text" class="span3" placeholder="e.g. T8" name="name">
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