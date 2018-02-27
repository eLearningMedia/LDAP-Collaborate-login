<?php
function authenticate($user, $password) {
	if(empty($user) || empty($password)) return false;

	include("config.php");

	// connect to active directory
	$ldapconn = ldap_connect($ldap_host) or die("Could not connect to LDAP Server");

	// set connection is using protocol version 3, if not will occur warning error.
	ldap_set_option($ldapconn, LDAP_OPT_PROTOCOL_VERSION, 3);
	ldap_set_option($ldapconn,LDAP_OPT_REFERRALS,0);
	
	$bind = ldap_bind($ldapconn, "uid=$user,ou=people,$ldap_dn", $password);
	if($bind) {
		// valid

		$filter="(uid=$user)";
		$justthese = array("givenName", "sn", "cn", "mail");

		$results=ldap_search($ldapconn, $ldap_dn, $filter, $justthese);

		$countResult = ldap_count_entries($ldapconn,$results); 
		if($countResult === 1) {
			$data = ldap_get_entries($ldapconn, $results);
			// establish session variables
			$_SESSION['user'] = $user;
			$_SESSION['cn'] = $data[0]['cn'][0];
			$_SESSION['givenName'] = $data[0]['givenName'][0];
			$_SESSION['sn'] = $data[0]['sn'][0];
			$_SESSION['mail'] = $data[0]['mail'][0];

			return true;
		}

		ldap_unbind($ldapconn);

		return false;

	} else {
		// invalid name or password
		return false;
	}
}
?>
