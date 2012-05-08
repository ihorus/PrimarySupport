<?php
gatekeeper(2);

$school = Group::find_by_uid($_GET['schoolUID']);

if (isset($_POST['add_note_submit'])) {
	$addNote = new Notes();
	$addNote->description = $_POST['description'];
	$addNote->school_uid = $school->uid;
	$addNote->title = $_POST['title'];
	
	if ($addNote->create()) {
		$addNoteComplete = TRUE;
	}
}

?>


<!-- main -->
<div id="main">
	<?php if ($addNote == TRUE) {
		echo ("<h2>News Item Added Successfully</h2>");
	}
	?>
	<h2>Add New Note for <?php echo ($school->name); ?></h2>
	<form target="_self" method="POST" name="add_news" id="edit_news">
	<p>Title: <br />
	<input type="text" name="title" value = "(e.g. I.P. Address for Office Printer)" />
	</p>
	<p>Description: <br />
	<textarea name="description" cols="80" rows="7"></textarea>
	</p>
	<input type="submit" value="add" />
	<input type="hidden" name="add_note_submit" />	
	</form>
</div>