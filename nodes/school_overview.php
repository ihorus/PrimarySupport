<link rel="stylesheet" type="text/css" media="all" href="modules/notes/css/notesCSS.css" />

<?php
//$currentUser = User::find_by_uid($_SESSION[currentUserUID]);

$schoolUIDS = ps_sanitise_array($_SESSION['cUser']['schoolUID']);

$school = Group::find_by_uid($_GET['schoolUID']);

if (in_array($school->uid, $schoolUIDS)) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}


?>
<!-- main -->
<div class="row-fluid">
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
</div>

<div class="row-fluid">
	<div class="span12">
	<?php
	$rootAddress = "http://" . $_SERVER['HTTP_HOST'] . SITE_PATH;
	$schoolActiveJobs = $rootAddress . "modules/support/views/jobs_subset.php?schoolUID=" . $school->uid . "&filter=active";
	$schoolClosedJobs = $rootAddress . "modules/support/views/jobs_subset.php?schoolUID=" . $school->uid . "&filter=closed";
	$schoolAllJobs = $rootAddress . "modules/support/views/jobs_subset.php?schoolUID=" . $school->uid . "&filter=all";;
	?>
	<ul class="nav nav-tabs" id="myTab">
		<li><a href="#activeJobs" data-toggle="tab" data-parameter="<?php echo $schoolActiveJobs;?>">Active Jobs</a></li>
		<li><a href="#closedJobs" data-toggle="tab" data-parameter="<?php echo $schoolClosedJobs;?>">Closed Jobs</a></li>
		<li><a href="#allJobs" data-toggle="tab" data-parameter="<?php echo $schoolAllJobs;?>">All Jobs</a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane" id="activeJobs">Loading...</div>
		<div class="tab-pane" id="closedJobs">Loading...</div>
		<div class="tab-pane" id="allJobs">Loading...</div>
	</div>
</div><!-- main ends -->
</div>

<script type="text/javascript">
$(function () {
	$('.tabs #activeJobs').tab('show')
	//$('.tabs a:last').tab('show')
	
	$('#myTab').on('shown', function (e) {
		var nowtab = e.target // activated tab
		var extAddress = $(nowtab).attr('data-parameter');
		var divid = $(nowtab).attr('href').substr(1);
		
		$("#"+divid).load(extAddress);
		
	})
})
</script>