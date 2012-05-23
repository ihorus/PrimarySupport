<?php
require_once("engine/initialise.php");

// check to see if the current user is logged in
if (!$ldapSession->is_logged_in()) {
	// the user isn't logged in, but are they are accessing one of the public pages?
	if ($_GET['n'] == "password_reset" || $_GET['n'] == "login") {
	} else {
		// user not logged in, and their page request if not one of the public pages
		// redirect to the login.php page
		redirect_to("login.php");
	}

}
?>

<?php include_once ("views/html_head.php"); ?>

<body>
<?php include_once ("views/navigation.php"); ?>

<div class="container">
<?php include_once ("views/header.php"); ?>

<?php
// based on the url, fetch the node view being requested
if ($_GET['n']) {
	$nodeContent = "nodes/" . $_GET['n'] . ".php";
} elseif ($_GET['m']) {
	$nodeContent = "modules/" . $_GET['m'];
	} elseif (!isset($_GET['n']) & !isset($_GET['m'])) {
$nodeContent = "nodes/index.php";
}

ob_start();
// include the requested node
fileInclude ($nodeContent);


if (isset($pageAccess)) {
	if (gatekeeperCheck($pageAccess) == TRUE) {
	} else {
		ob_end_clean();
		echo "You do not have sufficient access to view this page.";
		echo ("<br />");
		echo ("You have access " . $_SESSION[SITE_UNIQUE_KEY]['cUser']['access'] . " and you have tried to access a page that requires access " . $pageAccess . " (the lower the number, the higher the clearance)");
	}
	
} else {
	ob_end_clean();
	echo ("Page security not set by Admin.");
	
}

ob_end_flush();
?>
<?php include_once ("views/footer_wrapper.php"); ?>
</div>
</body>
</html>