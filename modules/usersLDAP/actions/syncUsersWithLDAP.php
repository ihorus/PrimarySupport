<?php
require_once(SITE_LOCATION . "/engine/initialise.php");

global $ldapSession;

$allLDAPUsers = $ldapSession->findUsers("OU=Staff,OU=Wallingford Users,DC=wsnet,DC=local");

foreach ($allLDAPUsers AS $user) {
	$localUserDB = new User();
	
	$username = $user['samaccountname'][0];
	$firstname = $user['givenname'][0];
	$lastname = $user['sn'][0];
	$mail = $user['mail'][0];
	
	if (isset($firstname)) {
		$localUserTest = $localUserDB->find_by_username($username);
		
		if (isset($localUserTest->uid)) {
			$usersExisting[] = $localUserTest->username;
		} else {
			$localUserDB->password = "password";
			$localUserDB->firstname = $user['givenname'][0];
			$localUserDB->lastname = $user['sn'][0];
			$localUserDB->username = $user['samaccountname'][0];
			$localUserDB->school_uid = "1";
			$localUserDB->email = $user['mail'][0];
			$localUserDB->access = "3";
			$localUserDB->type = "School";
			$localUserDB->active = "1";
			$localUserDB->salutation = "";
			$localUserDB->auth_type = "ldap";
			
			if ($localUserDB->create()) {
				$usersCreated[] = $username;
			} else {
				$usersCreatedWithError[] = $username;
			}
		}
		
	}
	
}

echo "Skipped " . count($usersExisting) . " users, because they already existed in the local DB<hr />";
echo "Created " . count($usersCreated) . " users from LDAP source '" . LDAP_LOCATION . "'<hr />";

if (count($usersCreatedWithError) > 0) {
	echo "Error creating " . count($usersCreatedWithError) . " users - " . implode(", ", $usersCreatedWithError) . "<hr />";
}
?>