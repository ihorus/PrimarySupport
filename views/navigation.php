<!-- navigation -->
<?php
if (isset($_SESSION['currentNavPage'])) {
} else {
	$_SESSION['currentNavPage'] = "Home";
}

//$navItems["Contact"] = "node.php?n=contact";

function makeLI($url, $name, $var1=0, $var2=0) {
	$row  = "<li";
	if ($var1 == $var2) {
		$row .= " class=\"active\"";
	}
	$row .= ">";
	$row .= "<a href=\"";
	$row .= $url;
	$row .= "\">";
	$row .= $name;
	$row .= "</a>";
	$row .= "</li>";
	
	return $row;
}
?>
<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container-fluid">
			<!-- .btn-navbar is used as the toggle for collapsed navbar content -->
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>
			
			<a class="brand" href="index.php"><?php echo SITE_NAME; ?></a>
			
			<!-- Everything you want hidden at 940px or less, place within here -->
			<div class="nav-collapse">
			
				<ul class="nav">
					<?php
					echo (makeLI("index.php", "Home", strtolower(nodeName()), "home"));
					
					foreach ($navItems AS $item => $value) {
						echo (makeLI($value, $item, strtolower(nodeName()), strtolower($item)));
					}
			
					?>
				</ul>
				<form class="navbar-search pull-left" method="post" action="node.php?m=search/views/search_results.php">
					<input type="text" class="search-query span2" placeholder="Search" name="quickSearchTerm">
				</form>
				
				<?php
				if ($session->is_logged_in()) {
				?>

				<ul class="nav pull-right">
					
					
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown">My Profile <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php
							echo gravatarURL($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']);
							?>
						<li><a href="node.php?n=user_unique&userUID=<?php echo $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']; ?>">View Profile</a></li>
						<li><a href="node.php?m=user_profile/views/editUser.php&userUID=<?php echo $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']; ?>">Change Settings</a></li>
						<li><a href="node.php?m=user_profile/views/editUser.php&userUID=<?php echo $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']; ?>">Reset Password</a></li>
						
						<li class="divider"></li>
						<li><a href="node.php?n=admin_index">Admin</a></li>
						<li><a href="login.php?logout=TRUE">Logout</a></li>
						</ul>
				</ul>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>



<?php
function gravatarURL($uid = false) {
	$user = User::find_by_uid($uid);
	
	$defaultAvatar = "http://" . $_SERVER["SERVER_NAME"] . SITE_PATH . "/images/gravatar.jpg";
	$size = "55";
	
	$gravatarURL  = ("http://www.gravatar.com/avatar.php?gravatar_id=" . md5(strtolower($user->email)));
	//$gravatarURL .= ("&amp;default=" . urlencode($defaultAvatar));
	//$gravatarURL .= ("&amp;s=" . $defaultSize);
	
	$imageURL .= ("<img class=\"pull-left hidden-phone\" src=\"");
	$imageURL .= ($gravatarURL);
	$imageURL .= ("\" width=\"" . $defaultSize . "\" height=\"" . $defaultSize . "\" title=\"" . $user->displayFirstname(false) . "\" alt=\"" . $user->displayFirstname(false) ."\"/>");
	
	return $imageURL;
}
?>