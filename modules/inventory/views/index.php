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

<!-- main -->
<div id="main">
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
	
	<form target="_self" method="POST" name="add_job" id="add_job">
	<h2>Add New Location</h2>
	<p>Room Name: <br />
	<input type="text" name = "name" />
	</p
	<p>Staff Name: <br />
	<input type="text" name = "teacher" />
	</p>
	<p>Notes: <br />
	<textarea name = "notes" cols="80" rows="7"></textarea>
	</p>
	<input type="submit" />
	<input type="hidden" name = "add_room" />
	</form>
</div>