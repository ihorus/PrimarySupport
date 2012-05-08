<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['uid'])) {
	$note = new Notes();
	$note->uid = $_POST['uid'];
	$note->title = $_POST['title'];
	$note->description = $_POST['description'];
	
	// instruct database to open/close job
	$note->update();
	
	echo "Note updated";
}
?>