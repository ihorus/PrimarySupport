<?php
class ldapSession {	
	protected $ad;
	protected $ldapDomain;
	protected $ldapSearchUser;
	protected $ldapSearchPassword;
	public $username;
	public $password;
	
	function __construct() {
		session_start();
				
		// check if we're logged in automatically
		$this->is_logged_in();
	}
	
	function serverUsername() {
		return $_SESSION['cUser']['username'];
	}
	
	function ldapGroups() {
		foreach ($_SESSION['cUser']['ldapGroups'] AS $group) {
			//clean up the full name given by LDAP
			$firstCN = strpos($group, "CN=") + 3;
			$firstComma = strpos($group, ",") - 3;
			
			$groupName = substr($group, $firstCN, $firstComma);
			
			// build an array of friendly names
			$array[] = $groupName;
		}
		
		return $array;
	}
	
	function is_logged_in(){
		// check to see if we're already logged in
		if (!isset($_SESSION['cUser']['uid'])) {
			// we're not logged in yet, so authenticate against the AD
			$this->ldapAuthenticate()
			//$this->fakeLogon();
		}
		
		return $_SESSION['cUser']['logonStatus'];
		return "1";
	}
	
	function is_in_group($groupName = NULL) {
		// cycle through every group LDAP reports this user being a member of
		// and get just the group name (not all the CN, DN stuff
		// then, check to see if that group matches $groupName variable
		// if so, return true.
		
		// force original return value to false, just incase!
		$returnValue = FALSE;
		
		if ($groupName == NULL) {
			echo "NO GROUP SPECIFIED IN 'is_in_group()'<br />";
		} else {
			foreach ($this->ldapGroups() AS $group) {
				if ($group == $groupName) {
					// $groupName matches the LDAP group, hurrah!
					$returnValue = TRUE;
				}
			}
		}
		
		return $returnValue;
	}
	
	function ldapBind() {
		$this->ad = ldap_connect(LDAP_LOCATION);		// Active Directory location (e.g. '10.111.240.114')
		$this->ldapDomain = LDAP_DOMAIN;				// Domain name suffix (e.g. '@domain.local')
		
		
		// These need to be set, not sure why though
		ldap_set_option($this->ad, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ad, LDAP_OPT_REFERRALS, 0);
		
		// Locate security options (username/password)
		$usernameFull = $this->username . $this->ldapDomain;
		
		// Bind to the AD as the username/password combo specified in the form
		$bd = ldap_bind($this->ad, $this->username . $this->ldapDomain, $this->password);
	}
	
	function ldapAdminBind() {
		$this->ad = ldap_connect(LDAP_LOCATION);		// Active Directory location (e.g. '10.111.240.114')
		$this->ldapDomain = LDAP_DOMAIN;				// Domain name suffix (e.g. '@domain.local')
		$this->ldapSearchUser = LDAP_USERNAME;			// User able to search the AD (e.g. 'username')
		$this->ldapSearchPassword = LDAP_PASSWORD;		// Password for ldapSearchUser
		
		// These need to be set, not sure why though
		ldap_set_option($this->ad, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->ad, LDAP_OPT_REFERRALS, 0);
		
		// Locate security options (username/password)
		$usernameFull = $this->username . $this->ldapDomain;
		
		// Bind to the AD as the username/password combo specified in the form
		
		$bd = ldap_bind($this->ad, $this->ldapSearchUser . $this->ldapDomain, $this->ldapSearchPassword);
	}
	
	function ldapUnbind() {
		ldap_unbind($this->ad);
	}
	
	function ldapAuthenticate() {
		$_SESSION['cUser']['logonStatus'] = FALSE;
		
		
		if (!isset($this->username)) {
			$this->username = $_COOKIE['username'];
			$this->password = ps_decrypt($_COOKIE['rPassword']);
		}
		
		$this->ldapBind();
		$entries = NULL;
		
		// Create filter (we only want to search the username)
		// As we only have security credentials for one person anyway, this will return the user
		
		$baseDN = ("DC=wsnet,DC=local");
		$filter = "(SAMAccountName=" . $this->username . ")";
		
		if ($this->password != "" && $this->username != "") {
						
			$entries = ldap_search($this->ad, $baseDN, $filter);
			$entries = ldap_get_entries($this->ad, $entries);
		}
		//printArray($entries);
		// check to see if either search resulted in a hit
		if ($entries["count"] == 1) {
			$output  = "<div class=\"alert alert-info\">";
			$output .= "<button class=\"close\" data-dismiss=\"alert\">x</button>";
			$output .= "<strong>LDAP Search Complete</strong> Found LDAP user: " . $_SESSION['cUser']['username'];
			$output .= "</div>";
			
			echo $output;
			
			$_SESSION['cUser']['logonStatus'] = TRUE;
			$_SESSION['cUser']['firstname'] = $entries[0]["givenname"][0];
			$_SESSION['cUser']['lastname'] = $entries[0]["sn"][0];
			$_SESSION['cUser']['username'] = $entries[0]["samaccountname"][0];
			$_SESSION['cUser']['email'] = $entries[0]["mail"][0];
			$_SESSION['cUser']['ldapGroups'] = $entries[0]['memberof'];
			
			$localUser = User::find_by_username($_SESSION['cUser']['username']);
						
			$_SESSION['cUser']['uid'] = $localUser->uid;
			$_SESSION['cUser']['schoolUID'] = $localUser->school_uid;
			
			$user->username = $_SESSION['cUser']['username'];
			$user->firstName = $_SESSION['cUser']['firstname'];
			$user->lastName = $_SESSION['cUser']['lastname'];
			$user->email = $_SESSION['cUser']['email'];

			if (self::is_in_group("Domain Admins")) {
				$user->security = "Admin";
			} elseif(self::is_in_group("Staff")) {
				$user->security = "Staff";
			} else {
				$user->security = "Student";
			}
			

			
			$returnValue = TRUE;
		}
		
		return $returnValue;
	}
	
	function gatekeeper($groupRequired = "Admin") {
		if ($this->is_in_group($groupRequired)) {
			// all is fine - user can access this page
		} else {
			// user shouldn't be viewing this page!
			$message  = "You do not have permission to view this page. <br />";
			$message .= "This page require your account to be a member of '" . $groupRequired . "'";
			
			// kill the process
			exit($message);
		}
	}
	
	function findUser($username = NULL) {
		// check if we've already cached the result of this LDAP search
		if (cacheCheck($username)) {
			// results already exist in cache
			
			$returnValue = cacheReturn($username);
			
		} else {
			$this->ldapAdminBind();
			
			// Create filter (we only want to search the username)
			// As we only have security credentials for one person anyway, this will return the user
			$filter = "(samAccountName=" . $username . ")";
			$ldapFilterAttributes = array ('samAccountName', 'facsimileTelephoneNumber', 'sn', 'givenname', 'mail'); 
			$baseDN = ("DC=wsnet,DC=local");
			
			//Search the directory
			$result = ldap_search($this->ad, $baseDN, $filter, $ldapFilterAttributes);
			
			//Create result set
			$entries = ldap_get_entries($this->ad, $result);
			array_shift($entries);
			
			// log this search
			$log = new Observations();
			$log->username = $this->serverUsername();
			$log->logType = "logInfo";
			$log->observationType = "system";
			$log->description = $this->serverUsername() . " logged in from IP: " . $_SERVER['REMOTE_ADDR'];
			$log->create();
			
			//never forget to unbind!
			$this->ldapUnbind();
					
			$returnValue = !empty($entries) ? array_shift($entries) : false;
			
			// cache this result, so we don't need to bother the LDAP in the future with the same request
			cacheResult($username, $returnValue);
		}
		
		return $returnValue;
	}
	
	function findUsers($baseDN = NULL) {
		// check if we've already cached the result of this LDAP search
		if (cacheCheck($baseDN)) {
			// results already exist in cache
			
			$returnValue = cacheReturn($baseDN);
			
		} else {
			$this->ldapAdminBind();
			
			// Create filter (we only want to search the username)
			// As we only have security credentials for one person anyway, this will return the user
			$filter = "(cn=*)";
			$ldapFilterAttributes = array ('samAccountName', 'facsimileTelephoneNumber', 'sn', 'givenname', 'mail'); 
			$ldapSortAttributes = array('givenname', 'sn'); // ie. sort by givenname, then by sn 
			
			//Search the directory
			$result = ldap_search($this->ad, $baseDN, $filter, $ldapFilterAttributes);
			
			foreach ($ldapSortAttributes as $eachSortAttribute) {
				if (in_array($eachSortAttribute, $ldapFilterAttributes)) {
					// making sure we don't accidentally try to sort against an inexisting field
					ldap_sort($this->ad, $result, $eachSortAttribute);
				}
			}
			
			//Create result set
			$returnValue = ldap_get_entries($this->ad, $result);
			cacheResult($baseDN, $returnValue);
			
			// log this search
			$log = new Observations();
			$log->username = $this->serverUsername();
			$log->logType = "logInfo";
			$log->observationType = "system";
			$log->description = $this->serverUsername() . " searched the LDAP and returned " . count($returnValue) . autoPluralise(" result", " results", count($returnValue));
			$log->create();
			
			//never forget to unbind!
			$this->ldapUnbind();
		}
		return $returnValue;
	}
}

$ldapSession = new ldapSession();

?>