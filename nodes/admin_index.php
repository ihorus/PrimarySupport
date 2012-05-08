<?php
gatekeeper(2);

$currentUser = User::find_by_uid($_SESSION['currentUser']['uid']);
$myActiveJobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND owner_uid = '" . $currentUser->uid . "' AND active = TRUE ORDER BY entry");
$myCompletedJobs = Support::find_by_sql("SELECT * FROM jobs WHERE type = 'Job' AND owner_uid = '" . $currentUser->uid . "' AND active = 0 ORDER BY job_closed DESC LIMIT 5");

if (isset($_POST['updateModules'])) {
	foreach($_POST as $key => $value) {
		if($value == "") {
			unset($_POST[$key]); 
		}
	}
	$enabledModules = array_values($_POST); 
	
	$enabledModules = AvailableSettings::updateSetting("site_enabled_modules", $enabledModules);
}

?>
<!-- main -->
<div class="row">
	<div class="span4">
		<div class="well">
			<a href="node.php?m=visits/views/index.php">Visits/Claim Mileage</a>
		</div>
	</div>
	<div class="span4">
		<div class="well">
			<a href="node.php?n=user_add">Create New User</a>
		</div>
	</div>
	<div class="span4">
		<div class="well">
			<a href="node.php?m=visits/views/invoice_overview.php">Invoices</a>
		</div>
	</div>
</div>
<div class="row">
	<div class="span12">
		<div class="page-header">
			<h1>Initialised Modules <small>site configuration</small></h1>
		</div>
		
		<form enctype="multipart/form-data" target="_self" method="POST" name = "enabledModules" id = "enabledModules">
		
		<?php
		// get a list of the available/enabled modules
		$availableModules = availableModules();
		$enabledModules = enabledModules();
			
		foreach ($availableModules AS $value) {
			if (in_array($value, $enabledModules)) {
				$checkValue = "checked=\"yes\"";
			} else {
				$checkValue = "";
			}
			
			echo ("<input type=\"checkbox\" name=\"" . $value . "\" value=\"" . $value . "\" " . $checkValue . " > ");
			echo $value;
			echo ("<br />");
		}
		?>
		<input type="hidden" name="updateModules" />
		<input type="submit" value="Update" />
		</form>
	</div>
</div>