<div class="page-header hidden-phone">
	<h1>Active Users <small> sub-tag</small></h1>
</div>

<?php
if ($session->is_logged_in()) {
	if (isTechnician()) {
		// if user is technician or admin, display all active users
		include_once ("active_users.php");
	}

} ?>