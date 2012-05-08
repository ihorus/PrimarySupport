<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['responseUID'])) {
	// response needs to be promotted to new job	
	$responseToPromote = new Response;
	$responseToPromote->uid = $_POST['responseUID'];
	
	$displayInfo = Response::find_by_uid($responseToPromote->promote_to_job());

	echo $displayInfo->displayResponse();
}
?>