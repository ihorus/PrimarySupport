<?php

// register this module in the navigation menu
$moduleName = "support";
$moduleFriendlyName = "Support";

$navItems[$moduleFriendlyName] = "node.php?m=" . $moduleName . "/views/index.php";

require_once("engine/moduleFunctions.php");
require_once("engine/supportClass.php");
require_once("engine/responseClass.php");

?>