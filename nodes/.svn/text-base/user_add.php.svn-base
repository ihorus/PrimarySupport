<?php
gatekeeper(1);

$schools = Group::find_all();

if (isset($_POST['user_create'])) {
	$newUser = new User();
	
	$newUser->username = ucwords($_POST['username']);
	$newUser->firstname = ucfirst($_POST['firstname']);
	$newUser->lastname = ucfirst($_POST['lastname']);
	$newUser->school_uid = $_POST['school_uid'];
	$newUser->email = $_POST['email'];
	$newUser->access = $_POST['access'];
	$newUser->type = $_POST['type'];
	$newUser->active = $_POST['active'];
	$newUser->salutation = $_POST['salutation'];
	$newUser->password = md5($_POST['password']);
	
	// insert visit to the database
	if ($newUser->create()) {
		echo ("<h2>User created!</h2>");
	}

}
?>



<div class="grid_9 alpha omega">
	<form target="_self" method="POST" name="add_visit" id="add_visit">
	<h2>Create New User</h2>
	<p>Username: <br />
	<input type="text" name = "username" />
	</p>
	<p>Given Name: <br />
	<input type="text" name = "firstname" />
	</p>
	<p>Family Name: <br />
	<input type="text" name="lastname" />
	</p>
	<p>School: <br />
	<select name="school_uid">
		<?php foreach($schools AS $school) {
		echo optionDropdown($school->uid, $school->name);
		} ?>
	</select>
	</p>
	<p>E-Mail Address: <br />
	<input type="text" name="email" />
	</p>
	<p>Access Level: <br />
	<select name="access">
		<option value = "1">Administrator</option>
		<option value = "2">Technician</option>
		<option value = "3">User</option>
	</select>
	</p>
	<p>Type: <br />
	<select name="type">
		<option value = "Administrator">Administrator</option>
		<option value = "Technician">Technician</option>
		<option value = "School">School</option>
	</select>
	</p>
	<p>Active: <br />
	<input type="checkbox" name="active" value="1" checked="checked" />
	</p>
	<p>Password: <br />
	<input type="password" name="password" />
	</p>
	<input type="submit" />
	<input type="hidden" name="user_create" />	
	</form>
</div>