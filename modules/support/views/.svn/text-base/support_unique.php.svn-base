<script src="modules/support/js/support.js"></script>

<script type="text/javascript">
$(function() {
	$(".progress").hide();
});



$(function() {
	$("#submit_response").click(function() {
		// clearInterval(progress);
		// $('.progress').removeClass('active');

		// validate and process form here
		// jobUID
		var jobUID = $("input#spawnUID").val();
		if (jobUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}
		
		// description
		var description = $("textarea#description").val();
		description = description.replace(/\r?\n/g, "<br>\n");
		// reset the description box css, incase there was an error before hand
		$("textarea#description").css({"background-color": "#FFFFFF"});
		
		if (description == "") {
			$("textarea#description").css({"background-color": "#FFBFBF"});
			$("textarea#description").focus();
			return false;
		}
		
		$(".progress").show();
		var $bar = $(".bar");
		$bar.width($bar.width()+100);
		
		// on behalf of checkbox/user selector
		var onBehalfOfUser = $("select#onBehalfOfUser").val();
		
		// attachment
		var attachment = $("input#attachment").val();
		
		// url we're going to send the data to
		var url = "modules/support/actions/submitResponse.php";
		
		$bar.width($bar.width()+200);
		
		// perform the post to the action (take the info and submit to database)
		$("textarea#description").val("");
		$("textarea#description").attr('disabled', 'disabled');
		
		$bar.width($bar.width()+200);
		
		$.post(url,{
			jobUID: jobUID,
			description: description,
			onBehalfOfUser: onBehalfOfUser,
			attachment: attachment
		}, function(data){
			$bar.width($bar.width()+600);
			$("#response_added").append(data);
			$bar.width($bar.width()+300);
			
			$("textarea#description").val("");
			var cssObj = {
				'background-color' : '#FFFFFF',
				'textarea:focus' : '#EFFAE6' // this doesn't seem to be working (ABr)
				}
			$("textarea#description").css(cssObj);
			$("textarea#description").removeAttr('disabled');
			$bar.width($bar.width()+300);
		},'html');
		
		$bar.width($bar.width()+400);
		
		// remove the loading image
		setTimeout(function() {
			$(".progress").slideUp();
		},1250);
		
		

		// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;
	});
	
});
</script>



<script>
$(function() {
	if ($("#opencloseState").val() == "open") {
		$('#responseOuter').hide();
		$('#responseOuterReopen').show();
	} else {
		$('#responseOuterReopen').hide();
	}
	
	$("#closeJobButton").click(function() {
		// validate and process form here
		// jobUID
		var jobUID = $("input#spawnUID").val();
		if (jobUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}
		
		var url = 'modules/support/actions/openCloseTicket.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			jobUID: jobUID,
			openclose: 'close'
		}, function(data){
			$("#response_added").append(data);
			
			$('#responseOuter').slideUp('slow');
			$('#responseOuterReopen').show();
		},'html');
		
		return false;
	});
	
	$("#openJobButton").click(function() {
		// validate and process form here
		// jobUID
		var jobUID = $("input#spawnUID").val();
		if (jobUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}
		
		var url = 'modules/support/actions/openCloseTicket.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			jobUID: jobUID,
			openclose: 'open'
		}, function(data){
			$("#response_added").append(data);
			
			$('#responseOuterReopen').hide();
			$('#responseOuter').slideDown('slow');
		},'html');
		
		return false;
	});
});
</script>

<script>
$(function() {
	$(".owner").change(function() {
		// validate and process form here
		// jobUID
		var jobUID = $("input#spawnUID").val();
		if (jobUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}
		
		var ownerUID = $("select#owner").val();
		var ownerName = $("select#owner option:selected").text();
		if (ownerUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}

		var url = 'modules/support/actions/changeOwner.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			jobUID: jobUID,
			ownerUID: ownerUID
		}, function(data){
			$("#response_added").append(data);
			$("textarea#description").val("");
			$("textarea#description").removeAttr('disabled');
			$("#jobOwner").html("Owner: <a href='node.php?n=user_unique&amp;userUID=" + ownerUID + "' class='readmore'>" + ownerName + "</a>| ");
		},'html');
		
		return false;
	});
});
</script>

<script>
$(function() {
	$(".priority").change(function() {
		// validate and process form here
		// jobUID
		var jobUID = $("input#spawnUID").val();
		if (jobUID == "") {
			alert("There is no job UID specified.  Please contact an Administrator.");
			return false;
		}
		
		var priority = $("select#priority").val();
		if (priority == "") {
			alert("There is no priority specified.  Please contact an Administrator.");
			return false;
		}

		var url = 'modules/support/actions/changePriority.php';
		
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			jobUID: jobUID,
			priority: priority
		}, function(data){
			$("#response_added").append(data);
		},'html');
		
		return false;
	});
});
</script>

<script>
$(function() {
	$("#dialog-confirm").hide();

	$(".promoteResponseToJob").click(function() {
		var responseUID =  $(this).attr('id');
		if (responseUID == "") {
			alert("There is no response UID specified.  Please contact an Administrator.");
			return false;
		}

		$( "#dialog-confirm" ).dialog({
			resizable: false,
			height:140,
			modal: true,
			buttons: {
				"Promote To New Job": function() {					
					var url = 'modules/support/actions/promoteResponse.php';
					
					$.post(url,{
						responseUID: responseUID
					}, function(data){
						$("#response_added").append(data);
					},'html');
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		return false;
	});
});
</script>
<script>
$(function() {
	$("#mergeDialog-confirm").hide();

	$(".mergeWithJob").click(function() {
		var jobUID =  $("#mergeJobUIDSelect").val();
		if (jobUID == "") {
			alert("There is no response UID specified.  Please contact an Administrator.");
			return false;
		}

		$( "#mergeDialog-confirm" ).dialog({
			resizable: false,
			height:150,
			modal: true,
			buttons: {
				"Merge With Job": function() {		
					var url = 'modules/support/actions/mergeWithJob.php';
					
					$.post(url,{
						jobUID: jobUID
					}, function(data){
						$("#main").html(data);
					},'html');
					$( this ).dialog( "close" );
				},
				Cancel: function() {
					$( this ).dialog( "close" );
				}
			}
		});
		return false;
	});
});
</script>


<div id="dialog-confirm" title="Promote To New Job?">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to promote this response to a new job?</p>
</div>



<?php
$job = Support::find_by_uid($_GET['supportUID']);
$schoolUIDS = ps_sanitise_array($_SESSION['currentUser']['school_uid']);

if (in_array($job->school_uid, $schoolUIDS)) {
	gatekeeper(3);
} else {
	gatekeeper(2);
}

$responses = Response::find_all($job->uid);

?>

<div id="mergeDialog-confirm" title="Merge To Existing Job?">
	<?php $activeJobs = Support::find_subset($active = TRUE, $schoolUIDS = $job->school_uid); ?>
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Are you sure you want to merge this job, and all responses to the following job?</p>
	<select id="mergeJobUIDSelect">
		<?php
		foreach ($activeJobs AS $activeJob) {
			//echo optionDropdown($activeJob->uid, $activeJob->uid);
		}
		?>
	</select>
</div>



<div class="row">
<div class="span12">
	<!-- support incident -->
	<?php echo $job->displayJob(); ?>
	<!-- eof support incident -->
	
	<div class="clear"></div>
	<!-- response incident -->
	<?php
	foreach ($responses as $response) {
		echo $response->displayResponse();
	}
	?>
	
	<div id="response_added"></div>
	
	<!-- eof response incident -->
	
	<div class="clear"></div>
</div>
<div class="span12">
	<?php
	echo $job->displayAddResponse();
?>
</div>
</div>