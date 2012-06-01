<script type="text/javascript">
$(function() {
	$(".updateDetailsButton").click(function() {
		// validate and process form here
		// userUID
		var userUID = $("input#userUID").val();
		if (userUID == "") {
			alert("There is no user UID specified.  Please contact an Administrator.");
			return false;
		}
		
		// username
		var username = $("input#username").val();
		if (username == "") {
			$("input#username").css({"background-color": "#FFBFBF"});
			$("input#username").focus();
			return false;
		}
		
		// givenName
		var givenName = $("input#firstname").val();
		if (givenName == "") {
			$("input#firstname").css({"background-color": "#FFBFBF"});
			$("input#firstname").focus();
			return false;
		}
		
		// familyName
		var familyName = $("input#lastname").val();
		if (familyName == "") {
			$("input#lastname").css({"background-color": "#FFBFBF"});
			$("input#lastname").focus();
			return false;
		}
		
		// schoolUID
		var schoolUID = "";
		$('select#school_uid option:selected').each(function(index) {
			schoolUID += $(this).val() + ",";
		});
		
		if (schoolUID == "") {
			$("select#school_uid").css({"background-color": "#FFBFBF"});
			$("select#school_uid").focus();
			return false;
		}
		
		// email
		var email = $("input#email").val();
		if (email == "") {
			$("input#email").css({"background-color": "#FFBFBF"});
			$("input#email").focus();
			return false;
		}
		
		// access
		var access = $("select#access").val();
		if (access == "") {
			$("select#access").css({"background-color": "#FFBFBF"});
			$("select#access").focus();
			return false;
		}
		
		// type
		var type = $("select#type").val();
		if (type == "") {
			$("select#type").css({"background-color": "#FFBFBF"});
			$("select#type").focus();
			return false;
		}
		
		// active
		var active = $('input[type=checkbox]:checked').val() != undefined;
				
		// url we're going to send the data to
		var url = "modules/user_profile/actions/updateProfile.php";
				
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			userUID: userUID,
			username: username,
			givenName: givenName,
			familyName: familyName,
			schoolUID: schoolUID,
			email: email,
			access: access,
			type: type,
			active: active
		}, function(data){
			$("#profileEditForm").slideUp("slow");
		},'html');
	// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;
	});
	
});
</script>

<script type="text/javascript">
$(function() {
	$(".updatePasswordButton").click(function() {
		// validate and process form here
		// userUID
		var userUID = $("input#userUID").val();
		if (userUID == "") {
			alert("There is no user UID specified.  Please contact an Administrator.");
			return false;
		}
		
		// password
		var password = $("input#password").val();
		var password2 = $("input#password2").val();
		if (password == "") {
			$("input#password").css({"background-color": "#FFBFBF"});
			$("input#password").focus();
			return false;
		}
		if (password2 == "") {
			$("input#password2").css({"background-color": "#FFBFBF"});
			$("input#password2").focus();
			return false;
		}
		if (password !== password2) {
			alert("Your passwords don't match, please try again");
			$("input#password").css({"background-color": "#FFBFBF"});
			$("input#password").focus();
			return false;
		}
				
		// url we're going to send the data to
		var url = "modules/user_profile/actions/resetPassword.php";
				
		// perform the post to the action (take the info and submit to database)
		$.post(url,{
			userUID: userUID,
			password: password
		}, function(data){
			$("#showHide").html("Password reset");
		},'html');
	// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;
	});
	
});
</script>

<script type="text/javascript">
$(function() {
	$(".updateSettingButton").click(function() {
	
	var test = "";
	var test2 = "";
	
	$("#settingUpdateForm :input").each(function(){
		var currentID = $(this).attr('id');
		var currentValue = $("input#" + currentID).val();
		//alert("ID:" + currentID + " Value:" + currentValue);
		
		if(!isNaN(currentID)) {
			if ($('#' + currentID).attr('type') == "checkbox") {
				if ($('#' + currentID).is(':checked')) {
					currentValue = 'TRUE';
				} else {
					currentValue = 'FALSE';
				}
			}
			test = test + currentID + ",";
			test2 = test2 + currentValue + ",";
		}
		
		
	});
	
	// validate and process form here
	// userUID
	var userUID = $("input#userUID").val();
	if (userUID == "") {
		alert("There is no user UID specified.  Please contact an Administrator.");
		return false;
	}
		
	var url = "modules/user_profile/actions/updateSetting.php";
	
	// perform the post to the action (take the info and submit to database)
	$.post(url,{
		userUID: userUID,
		settingUID: test,
		settingValue: test2
	}, function(data){
		$("#profileEditForm").append(data);
	},'html');
	
	// stop the page refreshing, this is all handled in jQuery so we don't need a proper submit
	return false;
	});
});
</script>

<?php
if (isset($_GET['userUID'])) {
	if ($_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'] == $_GET['userUID']) {
		gatekeeper(3);
	} else {
		gatekeeper(2);
	}
} else {
	gatekeeper(3);
	$_GET['userUID'] = $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid'];
}

$user = User::find_by_uid($_GET['userUID']);
$usersSchool = Group::find_by_uid($user->school_uid);
$schools = Group::find_all();
$allSettings = AvailableSettings::allAvailableSettings();

?>
<script>
$(document).ready(function() {
	$('#showHide').hide(); // hides the div as soon as the DOM is ready
	$('#showHideClick').click(function() {; // show/hide the div when clicked (using toggle for state-change)
		$('#showHide').toggle(400);
		return false; // stop the link going anywhere
	});
});
</script>
		
<div class="row-fluid">
	<div class="span12">
	<?php
		if (isset($_POST['user_update'])) {
			echo ("<h2>Update Complete</h2>");
		}
		
		echo "<div class=\"page-header\">";
		
		if ($user->uid == $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']) {
			echo "<h1>" . (ucfirst(strtolower($user->firstname)) . " " . ucfirst(strtolower(($user->lastname)))) . " <small> my profile information</small></h1>";
		} else {
			echo "<h1>" . (ucfirst(strtolower($user->firstname)) . " " . ucfirst(strtolower(($user->lastname)))) . " <small> profile information</small></h1>";
		}
		echo "</div>";
	?>
	</div>
</div>

<div class="row-fluid">
	<div class="span6">
	<form class="form-horizontal" target="_self" method="POST" name="add_visit" id="add_visit">
	<fieldset>
		<legend>Profile Details</legend>
		<div class="control-group">
			<label class="control-label" for="username">Username</label>
			<div class="controls">
				<input class="input-xlarge disabled" id="username" type="text" " value="<?php echo $user->username; ?>" disabled>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="firstname">Given Name</label>
			<div class="controls">
				<input class="input-xlarge" id="firstname" type="text" " value="<?php echo $user->firstname; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="lastname">Family Name</label>
			<div class="controls">
				<input class="input-xlarge" id="firstname" type="text" " value="<?php echo $user->lastname; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="school_uid">School(s)</label>
			<div class="controls">
				<?php
				if (!isAdmin()) {
					$disabled = "DISABLED";
				}
				echo ("<select " . $disabled ." id=\"school_uid\" multiple size=5>");
				
				$schoolUIDS = explode(",", $user->school_uid);
				foreach($schools AS $school) {
					echo optionDropdown($school->uid, $school->name, $schoolUIDS);
				}
				echo "</select>";
				?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="email">E-Mail Address</label>
			<div class="controls">
				<input class="input-xlarge" id="email" type="text" " value="<?php echo $user->email; ?>">
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="access">Access Level</label>
			<div class="controls">
				<?php
				if (!isAdmin()) {
					$disabled = "DISABLED READONLY";
				}
				$accessArray = array(1 => "Administrator", 2 => "Technician", 3 => "User");
				//printArray($accessArray);
				
				echo ("<select " . $disabled ." id=\"access\">");
				foreach($accessArray AS $access => $value) {
					echo optionDropdown($access, $value, $user->access);
				}
				echo "</select>";
				?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="type">Type</label>
			<div class="controls">
				<?php
				if (!isAdmin()) {
					$disabled = "DISABLED READONLY";
				}
				$typeArray = array("Administrator", "Technician", "School");
				//printArray($accessArray);
				
				echo ("<select " . $disabled ." id=\"type\">");
				foreach($typeArray AS $type) {
					echo optionDropdown($type, $type, $user->type);
				}
				echo "</select>";
				?>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="active">Active</label>
			<div class="controls">
				<?php
				if (!isAdmin()) {
					$disabled = "DISABLED";
				}
				if ($user->active == 1) {
					$activeState = "1";
					$checkedState = "checked=\"checked\"";
				}
				echo ("<input class=\"input-xlarge\" type=\"checkbox\" id=\"active\" value=\"" . $activeState . "\" " . $disabled . " " . $checkedState . " />");
				?>
			</div>
		</div>
		<div class="form-actions">
			<button type="submit" class="btn btn-primary"><i class="icon-ok icon-white"></i> Save changes</button>
			<input type="submit" class="updateDetailsButton" id="updateDetailsButton" value="Update Details"/>
		</div>
	</fieldset>
	</form>
	</div>
	<div class="span6">
		<form class="form-horizontal" target="_self" method="POST" name="add_visit" id="add_visit">
		<fieldset>
			<legend>Password Reset</legend>
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input class="input-xlarge" id="password" type="password">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label" for="password2">Confirm</label>
				<div class="controls">
					<input class="input-xlarge" id="password2" type="password">
				</div>
			</div>
			<div class="form-actions">
				<button type="submit" class="btn btn-danger" id="updateDetailsButton"><i class="icon-lock icon-white"></i> Reset Password</button>
				<input type="hidden" id="userUID" value="<?php echo $user->uid; ?>" />
			</div>
		</fieldset>
		</form>
	</div>
	
	<?php
		if ($user->uid == $_SESSION[SITE_UNIQUE_KEY]['cUser']['uid']) {
			echo ("<h2>My Settings</h2>");
		} else {
			echo ("<h2>Settings for " . $user->firstname . " <i>(" . $user->username . ")</i></h2>");
		}
	?>
		
	<?php
	echo "<form target=\"_self\" method=\"POST\" name=\"update\" id=\"settingUpdateForm\">";
	foreach ($allSettings AS $setting) {
		$settingValue = UserSettings::get_setting($user->uid, $setting->uid);
		
		if ($setting->settingType == "textbox") {
			$formElement = "<input type=\"text\" id=\"" . $setting->uid . "\" value=\"" . $settingValue . "\" />";
		} elseif ($setting->settingType == "checkbox") {
			if ($settingValue == "TRUE") {
				$checkValue = "checked=\"TRUE\"";
			} else {
				$checkValue = "";
			}
			
			$formElement = "<input type=\"checkbox\" id=\"" . $setting->uid . "\" value=\"" . $settingValue . "\" " . $checkValue . " /> <br />";
		} elseif($setting->settingType == "array") {
			$allArrayItems = AvailableSettings::get_setting($setting->uid);
			$allArrayItems = explode(",", $allArrayItems[0]->settingDefaultValue);
			
			// get the specified user settings (if any)
			$arrayItems = explode(",",$settingValue);
			//$formElement  = "<input type=\"hidden\" name=\"" . $setting->uid . "\" value=\"FALSE\" />";
			foreach ($allArrayItems AS $specificSetting) {
				// check if the user has already specified a setting, if so, put a tick in the checkbox
				if (in_array($specificSetting, $arrayItems)) {
					$checkedValue = " checked ";
				} else {
					$checkedValue = "";
				}
				
				$formElement = "<input type=\"checkbox\" id=\"" . $setting->uid . "[]\" value = \"" . $specificSetting . "\" " . $checkedValue . "/>" . $specificSetting;

				$formElement .= "<br />";
			}
		} elseif($setting->settingType == "password") {
			$formElement = "<input type=\"password\" id=\"" . $setting->uid . "\" value = \"" . $settingValue . "\" />";
		} else {
			$formElement = "Unknown setting type";
		}
		
		//$formElement .= "<input type=\"hidden\" id=\"" . $setting->uid . "\" value=\"" . $setting->uid . "\" />";
		
		echo "<p>" . $setting->settingFriendlyName . ": <br />";
		echo $formElement;
		echo "</p>";
		
	}
			echo "<input type=\"submit\" class=\"updateSettingButton\" id=\"updateSettingButton\" value=\"Update Details\"/>";

		echo "</form>";
	
	?>
</div>