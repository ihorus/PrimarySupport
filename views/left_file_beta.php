<script>
$(function() {
	$( "#notesTabs" ).tabs({
		cookie: {
			// store cookie for a day, without, it would be a session cookie
			expires: 1
		}
	});
});
</script>

<?php
$user = User::find_by_uid($_SESSION['cUser']['uid']);
$schoolUIDS = ps_sanitise_array($user->school_uid);
$i = 1;

foreach ($schoolUIDS AS $schoolUID) {
	$school = Group::find_by_uid($schoolUID);
	$notes = Notes::find_all_by_school_uid($schoolUID);
	
	// include the div in the notes tabs menu
	$tabs .= "<li><a href=\"#notesTabs-" . $i . "\">" . $school->name . "</a></li>";
	
	// build the div for each of the schools	
	$output .= "<div id=\"notesTabs-" . $i . "\">";
	
		// display all the notes for this school
		$output .= "<ul>";
		foreach ($notes AS $note) {
			$li  = "<li>";
			$li .= "<a href=\"node.php?m=notes/views/index.php&noteUID=" . $note->uid . "\">";
			$li .= $note->title;
			$li .= "</a>";
			$li .= "</li>";
			
			$output .= $li;
		}
		$output .= "</ul>";
	$output .= "</div>";
	
	$i = $i + 1;
}


echo "<div id=\"notesTabs\">";
	echo "<ul>";
	echo $tabs;
	echo "</ul>";
	echo "<div id=\"sidemenu_content\">";
	echo $output;
	echo "</div>";
echo "</div>";
?>