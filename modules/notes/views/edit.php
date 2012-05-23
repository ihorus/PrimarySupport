<?php
$note = Notes::find_by_uid($_GET['noteUID']);
$school = Group::find_by_uid($note->school_uid);
$schoolUIDS = ps_sanitise_array($_SESSION['cUser']['schoolUID']);

if (in_array($note->school_uid, $schoolUIDS)) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}

?>
<script type="text/javascript">
$(function() {
	$("#updateNoteSubmit").click(function() {
		// validate and process form here
		
		// noteUID
		var uid = $("input#noteUID").val();
		
		// title
		var title = $("input#title").val();
		if (title == "") {
			$("input#title").css({"background-color": "#FFBFBF"});
			$("input#title").focus();
			return false;
		}
		
		// description
		var description = $("textarea#description").val();
		if (description == "") {
			$("textarea#description").css({"background-color": "#FFBFBF"});
			$("textarea#description").focus();
			return false;
		}
		
		// url we're going to send the data to
		var url = "modules/notes/actions/updateNote.php";
		
		// perform the post to the action (take the info and submit to database)
		$("#loading_spinner_placeholder").append('<img src="images/loading_spin.gif" alt="Loading..." id="loading" />');
		
		$.post(url,{
			uid: uid,
			title: title,
			description: description
		}, function(data){
			$("#loading_spinner_placeholder").slideUp();
			$("#noteUpdated").append(data);
			var cssObj = {
				'background-color' : '#FFFFFF',
				'textarea:focus' : '#EFFAE6' // this doesn't seem to be working (ABr)
				}
			$("textarea#description").css(cssObj);
		},'html');
		
		
		// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;

	});
	
});
</script>

<!-- main -->
<div id="main">
	<?php if ($addNote == TRUE) {
		echo ("<h2>News Item Added Successfully</h2>");
	}
	?>
	<h2>Edit Note for <?php echo ($school->name); ?></h2>
	<form target="_self" method="POST" name="add_news" id="edit_news">
	<p>Title: <br />
	<input type="text" id="title" value = "<?php echo ($note->title); ?>" />
	</p>
	<p>Description: <br />
	<textarea id="description" cols="80" rows="7"><?php echo ($note->description); ?></textarea>
	</p>
	<input type="submit" id="updateNoteSubmit" value = "Update" />
	<div id="loading_spinner_placeholder"></div>
	<input type="hidden" id="noteUID" value= "<?php echo ($note->uid); ?>" />	
	</form>
	<div id="noteUpdated"></div>
</div>