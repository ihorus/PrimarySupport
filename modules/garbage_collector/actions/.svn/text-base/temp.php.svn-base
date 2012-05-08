<?php

function orphanedEntries() {
	$sql = "SELECT * FROM `jobs` WHERE spawn NOT IN (SELECT uid FROM `jobs` WHERE type = 'Job') AND info != 'Info'";
}
?>