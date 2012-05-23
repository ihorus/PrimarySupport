<link rel="stylesheet" type="text/css" media="all" href="modules/notes/css/notesCSS.css" />

<?php
$note = Notes::find_by_uid($_GET['noteUID']);
$schoolUIDS = ps_sanitise_array($_SESSION[SITE_UNIQUE_KEY]['cUser']['schoolUID']);

if (in_array($note->school_uid, $schoolUIDS)) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}
?>


<!-- main -->
<div class="row">
<div class="span12">
	<div id="note">
		<h1><?php echo ($note->title); ?></h1>
		<p><?php echo paragraphTidyUp($note->description); ?></p>
	</div>
</div>
</div>