<div class="page-header">
	<h1><a href="node.php?m=recent_activity/views/index.php">Recent Activity</a> <small> sub-tag</small></h1>
</div>

<?php if ($session->is_logged_in()) { ?>
<p>All jobs, responses and info. updates are shown here.  Visits, and other admin. tasks are not yet included in the recent activity.</p>

<?php
if (isTechnician()) {
	$activity = Activity::display_recent_activity(
		$all = FALSE,
		$relevant=FALSE
	);
} else {
	$activity = Activity::display_recent_activity(
		$all = FALSE,
		$relevant=TRUE
	);
}
?>
<?php } ?>