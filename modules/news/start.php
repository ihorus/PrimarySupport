<?php

// register this module in the navigation menu
// would like to use something like this... register_module("Test","test",$navigation = TRUE;)
$moduleName = "news";
$moduleFriendlyName = "News";

//$navItems[$moduleFriendlyName] = "node.php?m=" . $moduleName . "/views/index.php";
require_once("actions/newsClass.php");

?>