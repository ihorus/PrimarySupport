

<?php

if ($_SESSION['cUser']['uid'] == $_GET['userUID']) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}

$user = User::find_by_uid($_GET['userUID']);
$schoolUIDS = ps_sanitise_array($user->school_uid);

foreach ($schoolUIDS AS $schoolUID) {
	$usersSchool[] = Group::find_by_uid($schoolUID);
}


?>


<!-- main -->
<div class="row-fluid">
	<div class="span12">
		<?php
		if (isTechnician() || isAdmin()) {
	
			$addURL = "#";
			$editURL = "node.php?n=user_edit&userUID=" . $user->uid;
			$deleteURL = "#";
			
			displayToolbar($addURL,$editURL,$deleteURL);
		}
		?>
		<div class="page-header">
			<h1><?php echo (ucfirst(strtolower($user->firstname)) . " " . ucfirst(strtolower(($user->lastname)))); ?> <small>user information</small></h1>
		</div>
		
		<?php
		$rootAddress = "http://" . $_SERVER['HTTP_HOST'] . SITE_PATH;
		$userStatsAdd = $rootAddress . "modules/user_profile/views/user_statistics.php?uUID=" . $user->uid . "&filter=active";
		$userActiveAdd = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=active";
		$userClosedAdd = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=closed";
		$userAllAdd = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=all";;
		?>
		<ul class="nav nav-tabs" id="myTab">
			<li class="active"><a href="#info" data-toggle="tab">Info</a></li>
			<li><a href="#userStats" data-toggle="tab" data-parameter="<?php echo $userStatsAdd;?>">User Stats</a></li>
			<li><a href="#activeJobs" data-toggle="tab" data-parameter="<?php echo $userActiveAdd;?>">Active Jobs</a></li>
			<li><a href="#closedJobs" data-toggle="tab" data-parameter="<?php echo $userClosedAdd;?>">Closed Jobs</a></li>
			<li><a href="#allJobs" data-toggle="tab" data-parameter="<?php echo $userAllAdd;?>">All Jobs</a></li>
		</ul>
		
		<div class="tab-content">
			<div class="tab-pane active" id="info">
				<p><?php echo ($user->gravatarURL(true, "128")); ?><br />
				<?php echo ("Username: " . $user->username . " <i>(uid: " . $user->uid . ")</i>"); ?><br />
				<?php echo ("Password Hash: " . $user->password); ?><br />
				<?php echo ("First Name: " . $user->firstname); ?><br />
				<?php echo ("Last Name: " . $user->lastname); ?><br />
				<?php echo autoPluralise("School: ", "Schools: ", count($usersSchool));
				
				foreach ($usersSchool AS $school) {
					$schoolArrayOutput[] = "<a href=\"node.php?n=school_overview&amp;schoolUID=" . $school->uid . "\">" . $school->name . "</a>";
				}
				
				echo implode(", ", $schoolArrayOutput);
				?>
				<br />
				<?php echo ("E-Mail: <a href=\"mailto:" . $user->email ."\">" . $user->email . "</a>"); ?><br />
				<?php echo ("Access Level: " . $user->access); ?><br />
				<?php echo ("User Type: " . $user->type); ?><br />
				<?php echo ("Active: " . $user->active); ?><br />
				<?php echo ($user->salutation); ?>
				</p>
			</div>
			
			<div class="tab-pane" id="userStats">Loading...</div>
			<div class="tab-pane" id="activeJobs">Loading...</div>
			<div class="tab-pane" id="closedJobs">Loading...</div>
			<div class="tab-pane" id="allJobs">Loading...</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function () {
	$('.tabs #userStats').tab('show')
	//$('.tabs a:last').tab('show')
	
	$('#myTab').on('shown', function (e) {
		var nowtab = e.target // activated tab
		var extAddress = $(nowtab).attr('data-parameter');
		var divid = $(nowtab).attr('href').substr(1);
		
		$("#"+divid).load(extAddress);
		
	})
})
</script>