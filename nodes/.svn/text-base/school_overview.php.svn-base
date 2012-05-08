<link rel="stylesheet" type="text/css" media="all" href="modules/notes/css/notesCSS.css" />

<?php
//$currentUser = User::find_by_uid($_SESSION[currentUserUID]);

$schoolUIDS = ps_sanitise_array($_SESSION['currentUser']['school_uid']);

$school = Group::find_by_uid($_GET['schoolUID']);

if (in_array($school->uid, $schoolUIDS)) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}


?>
<!-- main -->
<div class="row">
<div class="span12">
	<div class=\"page-header\">
		<h1><?php echo ($school->name); ?> <small> overview</small></h1>
	</div>
</div>
<div class="span4">
	<h3>Address:</h3>
	<p>
	<?php echo ($school->address1); ?><br />
	<?php echo ($school->address2); ?><br />
	<?php echo ($school->address3); ?><br />
	<?php echo ($school->address4); ?><br />
	<?php echo ($school->address5); ?>
	</p>
	
	<p>
	Tel: <?php echo ($school->phone1); ?><br />
	Fax: <?php echo ($school->fax1); ?><br />
	</p>
	
	<p>
	E-Mail: <?php echo ($school->email1); ?>
	</p>
</div>

<div class="span4">
	<div id="note">
		<?php
		if (isTechnician()) {
			// display 'add news item' for technicians and admins
			$addURL = "node.php?m=notes/views/add.php&schoolUID=" . $school->uid;
			$editURL = "#";
			$delteURL = "#";
			displayToolbar($addURL,$editURL,$deleteURL);
		}
		
		$notes = Notes::find_all_by_school_uid($school->uid); ?>
		<h1>Notes</h1>
		
		<p>
		<?php foreach ($notes AS $note) {
			$output  = "<a href=\"node.php?m=notes/views/index.php&noteUID=" . $note->uid . "\">";
			$output .= $note->title;
			$output .= "</a>";
			$output .= "<br />";
			
			echo $output;
		}
		?>
		</p>
	</div>
</div>

<div class="span4">
	<h3>Other</h3>
	<p>test</p>
</div>

<div class="clearfix"></div>

<div class="span12">
	<div id="filter" >
		<ul>
			<li><a href="<?php echo ("node.php?n=school_overview&schoolUID=" . $school->uid . "&filter=active"); ?>">Active Jobs</a></li>
			<li><a href="<?php echo ("node.php?n=school_overview&schoolUID=" . $school->uid . "&filter=completed"); ?>">Recently Completed Jobs</a></li>
			<li><a href="<?php echo ("node.php?n=school_overview&schoolUID=" . $school->uid); ?>">All Recent Jobs</a></li>
		</ul>
	</div>
		
<?php

if (isset($_GET['filter']) && $_GET['filter'] == "active") {
	// return only active jobs
	$jobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND school_uid = {$school->uid} AND active = 1 ORDER BY priority ASC, entry ASC");
} elseif (isset($_GET['filter']) && $_GET['filter'] == "completed") {
	// return only completed jobs
	$jobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND school_uid = {$school->uid} AND active = 0 ORDER BY last_update DESC");
} else {
	// return only completed jobs
	$jobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND school_uid = {$school->uid} ORDER BY entry DESC");
}

	
	$limitedJobs = paginateResults($jobs);
	
	//display pagination navigation
	echo paginationNavBar(count($jobs));
	
	foreach ($limitedJobs as $job) {
		echo $job->displayJob();
	}
	
	//display pagination navigation
	echo paginationNavBar(count($jobs));
	?>
</div><!-- main ends -->
</div>