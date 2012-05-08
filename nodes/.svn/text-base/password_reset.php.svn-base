<?php
// no gatekeeper check - this is a public/anon. page
gatekeeper(0);

// check if the form has been submitted
if (isset($_GET['reset']) && $_GET['reset'] == TRUE) {

	// search for the '@' symbol in the e-mail address, destroy everything to the right
	// of the '@' symbol (if there isn't an '@', the string will be blank!
	$domain = strstr($_POST['email'],"@");
	
	// check if the email field contains an '@' symbol
	if ($domain[0] == "@") {
		// email field contains an '@', send the e-mail message
		$email = Session::forgot_password($_POST['email']);

		// set the status to complete
		$resetStatus = "complete";
		
	} else {
	
		// the e-mail field doesn't contain an '@' symbol
		$message = "Please enter a valid e-mail address";

		$resetStatus = "awaiting";
		
	}
}
?>


<!-- main -->
<div id="main">

	<?php
		// check if the form has already been submitted, if it has, don't show the user
		// the form submission page again!
	
		if ($resetStatus == "complete") {
			// form submission sent - show completion message
			echo ("<h2>A new password has been sent to " . $_POST['email'] . "</h2>");
		} else {
	?>
	<form id="resetPassword" action="node.php?n=password_reset&reset=TRUE" method="post">
		<h2>Forgot your password?</h2>
		<h3><?php echo ($message); ?></h3>
		<p>Please enter your e-mail address</p>
		E-Mail: <input type="text" name="email" id="email" value="<?php echo $_POST['email']; ?>"/>
		<br />
		<input type="submit" value="Submit" />
		<input type="hidden" name="resetPassword" value=TRUE />
	</form>
	<?php }
	?>

</div>