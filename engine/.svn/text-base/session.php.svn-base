<?php
class Session {
	
	var $logged_in = false;
	public $user_id;
	
	function __construct() {
		session_start();
		$this->is_logged_in();
	}
	
	public function is_logged_in() {
		if(isset($_SESSION['currentUser']['uid'])) {
			$this->user_id = $_SESSION['currentUser']['uid'];
			$this->logged_in = true;
		} else {
			unset($this->user_id);
			$this->logged_in = false;
		}
		
		return $this->logged_in;
	}
	
	public function login($user) {
		// database should find user based on username/password
		
		if ($user) {
			$this->user_id = $_SESSION['user_id'] = $user->uid;
			$this->logged_in = true;
		}
	}
	
	public function logout() {
		unset ($_SESSION['user_id']);
		unset ($_SESSION['currentUser']);
	}
	
	public function forgot_password($email) {
		$resetUser = User::find_by_email($email);

		$subject = SITE_NAME . " - Password Reset";
		
		// generate new string for password
		$newPassword = randomString();
		
		$resetUser->resetPassword(md5($newPassword));		

		$message  = ("<p>This e-mail has been sent to you because you have requested a new password for the " . SITE_NAME . " site.  If you did not request this - please dis-regard this e-mail.</p>");
		$message .= ("<p>Your new password is: " . $newPassword . " (passwords are case-sensitive)</p><br />");
		
		sendMail($resetUser->email, $subject, $message);
	}

}

function redirect_to($page) {
	$siteURL = $_SERVER['SERVER_NAME'] . SITE_PATH . $page;
	header( 'Location: ' . 'http://' . $siteURL);
}


$session = new Session();

?>