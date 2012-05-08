<?php
// this needs to be dynamic
require_once("engine/config.php");
require_once(SITE_LOCATION . "/engine/" . "initialise.php");

if (isset($_GET['logout']) & $_GET['logout'] == TRUE) {
	$logout = Session::logout();
}

if ($session->is_logged_in()) {
	redirect_to("index.php");
}

		
if (isset($_POST['username'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);

	$found_user = User::authenticate($username, $password);

	if ($found_user && $found_user->active == TRUE) {
		$session->login($found_user);
				
		$_SESSION['currentUser']['uid'] = $found_user->uid;
		$_SESSION['currentUser']['firstname'] = $found_user->firstname;
		$_SESSION['currentUser']['lastname'] = $found_user->lastname;
		$_SESSION['currentUser']['username'] = $found_user->username;
		$_SESSION['currentUser']['school_uid'] = $found_user->school_uid;
		$_SESSION['currentUser']['access'] = $found_user->access;
		$_SESSION['currentUser']['lastname'] = $found_user->lastname;
		
		if (isset($_POST['remember']) && $_POST['remember'] == true) {
			// set the logged in user's username/password hash in a cookie - for the next time they log in
			setcookie("username", strtolower($found_user->username), time()+604800);
			setcookie("password", strtolower($found_user->password), time()+604800);
		} else {
			setcookie("username", "", time()+604800);
			setcookie("password", "", time()+604800);
		}

/*	public $email;
	public $hourly_rate;
	public $type;
	public $active;
	public $salutation;	
	
*/	
	
		redirect_to("index.php");
	} else {
		$message = "Username/password combination incorrect";
	}
} else { // Form has been submitted
	$username = "";
	$password = "";
}
?>

<?php include_once ("views/html_head.php"); ?>

<body onload="setFocus()">
<div class="container">
<?php include_once ("views/navigation.php"); ?>
<?php include_once ("views/header.php"); ?>


<section id="login">
  <div class="page-header">
    <h1>Access Denied <small>You are not logged in</small></h1>
  </div>
  
  <div class="row-fluid">
  	<div class="span12">
  		<p>You do not have permission to view this site using the credentials you have supplied.  Using the form below, attempt to logon using an account with valid credentials.  If you find you cannot login and believe you should be able to, please contact the site <a href="mailto:ab2394@wallingford.oxon.sch.uk">Administrator</a>.</p>
  		<form class="well form-inline" id="loginForm" action="login.php" method="post">
  			<?php
  				if (isset($_POST['username']) && $_POST['username'] <> "") {
  					$usernameSuggest = $_POST['username'];
  				} elseif (isset($_COOKIE['username']) && $_COOKIE['username'] <> "") {
  					$usernameSuggest = $_COOKIE['username'];
  				} else {
  					$usernameSuggest = "";
  				}
  				?>
  			<input type="text" class="input-large" placeholder="Username" name="username" value="<?php echo $usernameSuggest; ?>">
  			
  			<input type="password" class="input-large" placeholder="Password" name="password">
  			
  			
  			<button type="submit" class="btn">Sign in</button>
  			<div class="clear"></div>
  			<label class="checkbox"><input type="checkbox" id="remember" value="true"> Remember me</label>
  			<div class="clear"></div>
  			<span class="help-inline"><a href="node.php?n=password_reset">Forgot Password</a></span>
  			
  			</form>						
	</div>
</div>
</section>





		<p>Technical Information (for support personnel):
		Background - The Web application uses a session filter to verify users connecting to the site. The authentication used to connect to the server was denied access by this filter.</p>
		
				<?php
				if (isset($_POST['username'])) {
					// user has tried to log in - but credentials incorrect
					// echo the submitted username into the username form field
					
					$submittedUsername = $_POST['username'];
					
					$autoFocus  = ("<script type=\"text/javascript\">");
					$autoFocus .= ("var formInUse = false;");
					$autoFocus .= ("function setFocus() {");
					$autoFocus .= ("if(!formInUse) {");
					$autoFocus .= ("document.forms['loginForm'].password.focus();");
					$autoFocus .= ("}");
					$autoFocus .= ("}");
					$autoFocus .= ("</script>");
					echo $autoFocus;
				} elseif (isset($_COOKIE['username'])) {
					// user has yet to try to log in, but there is a cookie with a username
					// echo the cookie username into the username form field
					$submittedUsername = $_COOKIE['username'];
					
					$autoFocus  = ("<script type=\"text/javascript\">");
					$autoFocus .= ("var formInUse = false;");
					$autoFocus .= ("function setFocus() {");
					$autoFocus .= ("if(!formInUse) {");
					$autoFocus .= ("document.forms['loginForm'].password.focus();");
					$autoFocus .= ("}");
					$autoFocus .= ("}");
					$autoFocus .= ("</script>");
					echo $autoFocus;
				} else {
					$submittedUsername = "";
					$autoFocus  = ("<script type=\"text/javascript\">");
					$autoFocus .= ("var formInUse = false;");
					$autoFocus .= ("function setFocus() {");
					$autoFocus .= ("if(!formInUse) {");
					$autoFocus .= ("document.forms['loginForm'].username.focus();");
					$autoFocus .= ("}");
					$autoFocus .= ("}");
					$autoFocus .= ("</script>");
					echo $autoFocus;
				}
				
				echo ($message); ?>
	</form>		
	</div>
</div>

</div>

</body>
</html>