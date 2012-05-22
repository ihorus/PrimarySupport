<?php
gatekeeper(3);
?>
<script type="text/javascript">
$(function() {
	$("#submit_job_button").click(function() {
		// validate and process form here
				
		// description
		var description = $("textarea#description").val();
		description = description.replace(/\r?\n/g, "<br>\n");
		
		if (description == "") {
			$("#addJobControlGroup").addClass("error");
			$("textarea#description").focus();
			return false;
		}
		
		// on behalf of checkbox/user selector
		var onBehalfOfUserUID = $("select#onBehalfOfUser").val();
		var onBehalfOfUserName = $("#onBehalfOfUser :selected").text();
		
		// attachment
		var attachment = $("input#attachment").val();
		
		// url we're going to send the data to
		var url = "modules/support/actions/submitJob.php";
				
		// perform the post to the action (take the info and submit to database)
		$("#loading_spinner_placeholder").append('<img src="images/loading_spin.gif" alt="Loading..." id="loading" />');
		
		$.post(url,{
			description: description,
			onBehalfOfUserUID: onBehalfOfUserUID,
			onBehalfOfUserName: onBehalfOfUserName,			
			attachment: attachment
		}, function(data){
			$("#loading_spinner_placeholder").slideUp();
			$("#job_added").append(data);
			$("textarea#description").val("");
			var cssObj = {
				'background-color' : '#FFFFFF',
				'textarea:focus' : '#EFFAE6' // this doesn't seem to be working (ABr)
				}
			$("textarea#description").css(cssObj);
		},'html');
		
		
		// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;

	});
	
});
</script>
<?
$school = Group::find_by_uid($_SESSION['currentUser']['school_uid']);
$schools = Group::find_all();
$user = User::find_by_uid($_SESSION['currentUser']['uid']);



if (isset($_GET['filter']) && $_GET['filter'] == "active") {
	// return only active jobs
	$Jobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND owner_uid = " . $user->uid . " AND active = 1 ORDER BY priority ASC, entry ASC");
} elseif (isset($_GET['filter']) && $_GET['filter'] == "completed") {
	// return only completed jobs
	$Jobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND owner_uid = " . $user->uid . " AND active = 0 ORDER BY entry ASC LIMIT 50");
} else {
	$Jobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND owner_uid = " . $user->uid . " ORDER BY entry DESC LIMIT 50");
}

?>

<div class="row">
<div class="span12">
	<div class="page-header">
		<h1>Log a new job</h1>
	</div>
	
	<form class="form-horizontal" target="_self" method="POST" name="add_job" id="add_job">
		<fieldset>          
		<div class="control-group" id="addJobControlGroup">
			<label class="control-label" for="description">Description</label>
			<div class="controls">
				<textarea class="input-xxlarge" id="description" rows="5"></textarea>
			</div>
		</div>
		<?php
			echo display_onbehalf_form_element();
		?>
		<div class="control-group">
			<div class="controls">
				<input class="btn-primary" type="submit" value="Submit Job" id="submit_job_button">
			</div>
		</div>
		<div id="loading_spinner_placeholder" style="float: right;"></div><br />
		
		<input name="school_uid" type="hidden" id="school_uid" value="<?php echo ($_SESSION['currentUser']['school_uid']); ?>">
		<input type="hidden" id="submit_job" value="TRUE">
		</fieldset>
	</form>
</div>

<div class="span12">
	<div class="page-header">
		<h1><?php echo ucfirst($_GET['filter']); ?> Jobs <small> <?php echo count($Jobs); ?> total</small></h1>
	</div>
	
	<?php
	$rootAddress = "http://" . $_SERVER['HTTP_HOST'] . SITE_PATH;
	$schoolActiveJobs = $rootAddress . "modules/support/views/jobs_subset.php?filter=active";
	$schoolClosedJobs = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $user->uid . "&filter=closed";
	$schoolAllJobs = $rootAddress . "modules/support/views/jobs_subset.php?uUID=" . $school->uid . "&filter=all";;
	?>
	
	<ul class="nav nav-tabs" id="myTab">
		<li><a href="#activeJobs" data-toggle="tab" data-parameter="<?php echo $schoolActiveJobs;?>">Active Jobs</a></li>
		<li><a href="#closedJobs" data-toggle="tab" data-parameter="<?php echo $schoolClosedJobs;?>">Recently Closed Jobs</a></li>
		<li><a href="#allJobs" data-toggle="tab" data-parameter="<?php echo $schoolAllJobs;?>">All Jobs</a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane" id="activeJobs">Loading...</div>
		<div class="tab-pane" id="closedJobs">Loading...</div>
		<div class="tab-pane" id="allJobs">Loading...</div>
	</div>
</div>
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