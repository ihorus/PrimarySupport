<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['openclose'])) {
	$openCloseState = $_POST['openclose'];
	$originalJobUID = $_POST['jobUID'];
	
	// work out if the job is being opened, or closed...
	if ($openCloseState == "open") {
		$open_close = 1;
	}
	if ($openCloseState == "close") {
		$open_close = 0;
	}
	
	// instruct database to open/close job
	$openCloseStateChange = Support::openClose($open_close, $originalJobUID);
	
	$response = Response::find_by_uid($openCloseStateChange);
	
	echo $response->displayResponse();
}
?>