<?php
gatekeeper(2);
$schools = Group::find_all();
$users = User::find_all();

if (isset($_POST['visit_submit'])) {
	$arrival = $_POST['date'] . " " . $_POST['arrival_time'];
	$departure = $_POST['date'] . " " . $_POST['departure_time'];
		
	$visit = new Visit();
	$visit->school_uid = $_POST['school_uid'];
	$visit->category = $_POST['category'];
	$visit->arrival = $arrival;
	$visit->departure = $departure;
	$visit->description = $_POST['description'];
	$visit->mileage_claim = $_POST['mileage_claim'];
	$visit->user_uid = $_SESSION['cUser']['uid'];
	$visit->tech_hourly = UserSettings::get_setting($_SESSION['cUser']['uid'],"user_per_hour_cost");
	
	// insert visit to the database
	echo ("Form submitted - Entry UID: " . $visit->create());
}
?>

<script>
$(function() {
	$( "#date" ).datepicker({ dateFormat: 'yy/mm/dd' });
});
</script>

<div class="grid_9 alpha omega">
	<form target="_self" method="POST" name="add_visit" id="add_visit">
	<h2>Log Visit</h2>
	<p>School: <br />
	<select name="school_uid">
		<?php foreach($schools AS $school) {
		echo optionDropdown($school->uid, $school->name);
		} ?>
	</select>
	</p>
	<p>Date: <br />
	<input type="text" id="date" name="date" value="<?php echo date('Y/m/d'); ?>" />
<!--	<input type="text"  /> -->
	</p>
	<p>Arrival Time: <br />
	<input type="text" name="arrival_time" value = "<?php echo date('H:m'); ?>" />
	</p>
	<p>Departure Time: <br />
	<input type="text" name="departure_time" value = "<?php echo date('H:m'); ?>" />
	</p>
	<p>Category: <br />
	<select name="category">
		<option value = "ICT Support">ICT Support</option>
		<option value = "ICT Support">ICT Support (Phone)</option>
		<option value = "ICT Audit">ICT Audit</option>
	</select>
	</p>
	<p>Description: <br />
	<textarea name="description"></textarea>
	</p>
	<p>Claim Mileage: <br />
	<input type="checkbox" name="mileage_claim" value = "1" />
	</p>
	<input type="submit" />
	<input type="hidden" name="visit_submit" />	
	</form>
</div>



<div class="grid_9 alpha omega">
	<form action="modules/visits/views/mileage_claim.php" method="POST" name="mileage_claim" id="mileage_claim">
	<h2>Mileage Claim</h2>
	<p>User: <br />
	<select name="user_uid">
		<?php foreach($users AS $user) {
		echo optionDropdown($user->uid, $user->firstname, $_SESSION['cUser']['uid']);
		} ?>
	</select>
	</p>
	<p>Month: <br />
	<select name="monthClaim" id="monthClaim">
	<?php
		$monthCounter = date('m')-1;
		for ($counter = 1; $counter <= 6; $counter += 1) {	
			
			$tempDate =  date('F-Y', mktime(0,0,0,($monthCounter),28,date('Y')));
			echo optionDropdown(strtotime($tempDate), $tempDate);
			$monthCounter = $monthCounter -1;
		}
	?>
	</select>
	<br />
	<input type="submit" />
	</form>
</div>