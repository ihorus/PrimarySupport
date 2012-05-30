<?php
require_once("../../../engine/initialise.php");

if (isset($_POST['itemUID'])) {
	$item = new Inventory();
	
	if (is_array($_POST['itemUID'])) {
		foreach ($_POST['itemUID'] AS $itemUID) {
			$item->uid = $itemUID;
			$item->touchItem();
		}
	} else {
		$item->uid = $_POST['itemUID'];
		$item->touchItem();
	}
}
?>