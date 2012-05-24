<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['itemUID'])) {
	$item = new Inventory();
	$item->uid = $_POST['itemUID'];
	
	$item->touchItem();
}
?>