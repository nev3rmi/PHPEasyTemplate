<?php
/*
 * Plugin for authenticating FileRun users against a LDAP directory
 *
 * */
class customAuth_ldap {
	var $error, $errorCode, $cid, $userRecord;
	function pluginDetails() {
		return array(
			'name' => 'LDAP',
			'description' => 'Authenticate users against LDAP.',
			'fields' => array(
				array(
					'name' => 'host',
					'label' => 'Server hostname',
					'default' => 'ldap.forumsys.com',
					'required' => true
				),
				array(
					'name' => 'port',
					'label' => 'Server port number',
					'default' => 389
				),
				array(
					'name' => 'bind_dn',
					'label' => 'Bind DN',
					'default' => 'cn=read-only-admin,dc=example,dc=com'
				),
				array(
					'name' => 'bind_password',
					'label' => 'Bind password',
					'default' => 'password'
				),
				array(
					'name' => 'user_dn',
					'label' => 'User DN template',
					'default' => 'uid={USERNAME},dc=example,dc=com',
					'required' => true
				),
				array(
					'name' => 'search_dn',
					'label' => 'Search DN',
					'default' => 'dc=example,dc=com',
					'required' => true
				),
				array(
					'name' => 'search_filter',
					'label' => 'Search filter template',
					'default' => '(&(uid={USERNAME})(objectClass=person))',
					'required' => true
				),
				array(
					'name' => 'allow_iwa_sso',
					'label' => 'Enable IWA SSO',
					'default' => 'yes',
					'required' => false
				),
				array(
					'name' => 'mapping_name',
					'label' => 'First name field',
					'default' => 'cn',
					'required' => true
				),
				array(
					'name' => 'mapping_name2',
					'label' => 'Last name field',
					'default' => 'cn'
				),
				array(
					'name' => 'mapping_email',
					'label' => 'E-mail field',
					'default' => 'mail'
				),
				array(
					'name' => 'mapping_company',
					'label' => 'Company name field',
					'default' => ''
				),
				array(
					'name' => 'test_username',
					'label' => 'Test username',
					'default' => 'einstein'
				),
				array(
					'name' => 'test_password',
					'label' => 'Test password',
					'default' => 'password'
				)
			)
		);
	}
	function pluginTest($opts) {

		$pluginInfo = self::pluginDetails();
		//check required fields
		foreach($pluginInfo['fields'] as $field) {
			if ($field['required'] && !$opts['auth_plugin_ldap_'.$field['name']]) {
				return 'The field "'.$field['label'].'" needs to have a value.';
			}
		}
		if (!function_exists('ldap_connect')) {
			return 'PHP is missing LDAP support! Make sure the LDAP PHP extension is enabled.';
		}
		$cid = ldap_connect($opts['auth_plugin_ldap_host'], $opts['auth_plugin_ldap_port']);
		if (!$cid) {
			return 'Connection to the LDAP server failed! Make sure the hostname and the port number are correct.';
		}
		ldap_set_option($cid, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($cid, LDAP_OPT_REFERRALS, 0);
		if ($opts['auth_plugin_ldap_bind_dn']) {
			$rs = @ldap_bind($cid, $opts['auth_plugin_ldap_bind_dn'], $opts['auth_plugin_ldap_bind_password']);
			if (!$rs) {
				return "Bind with bind DN failed: ".ldap_error($cid);
			} else {
				echo 'Bind with bind DN successful!';
				echo '<br>';
			}
		} else {
			$user_dn = str_replace('{USERNAME}', $opts['auth_plugin_ldap_test_username'], $opts['auth_plugin_ldap_user_dn']);
			$rs = @ldap_bind($cid, $user_dn, $opts['auth_plugin_ldap_test_password']);
			if (!$rs) {
				return "Bind with test account failed: ".ldap_error($cid);
			} else {
				echo 'Bind with test account successful!';
				echo '<br>';
			}
		}

		$filter = str_replace("{USERNAME}", $opts['auth_plugin_ldap_test_username'], $opts['auth_plugin_ldap_search_filter']);
		echo 'Searching with filter: '.$filter;
		echo '<br>';
		$rs = @ldap_search($cid, $opts['auth_plugin_ldap_search_dn'], $filter);
		if (!$rs) {
			return "Failed to search for the LDAP record: ".ldap_error($cid);
		}
		$entry = ldap_first_entry($cid, $rs);
		if (!$entry) {
			return 'LDAP record not found. Verify the search filter.';
		}
		if ($opts['auth_plugin_ldap_bind_dn']) {
			$user_dn = ldap_get_dn($cid, $entry);
			echo 'User DN retrieved: '.$user_dn;
			echo '<br>';
			$rs = @ldap_bind($cid, $user_dn, $opts['auth_plugin_ldap_test_password']);
			if (!$rs) {
				return "Bind with test account failed: " . ldap_error($cid);
			} else {
				echo 'Bind with test account successful!';
				echo '<br>';
			}
		}
		echo 'Record found:';
		$attr = ldap_get_attributes($cid, $entry);
		$values = array();
		if (array($attr)) {
			echo '<div style="background-color:whitesmoke;margin:5px;border:1px solid silver">';
			foreach ($attr as $k => $a) {
				if (!is_numeric($k) && $k != 'count') {
					$values[$k] = $a[0];
					echo '<div style="margin-left:10px;">'.S::safeHTML($k).': '.S::safeHTML($a[0]).'</div>';
				}
			}
			echo '</div>';
		}
		echo 'Fields mapping:';
		echo '<div style="background-color:whitesmoke;margin:5px;border:1px solid silver">';
		echo '<div style="margin-left:10px;">Name ('.$opts['auth_plugin_ldap_mapping_name'].'): '.S::safeHTML($values[$opts['auth_plugin_ldap_mapping_name']]).'</div>';
		echo '<div style="margin-left:10px;">Last name ('.$opts['auth_plugin_ldap_mapping_name2'].'): '.S::safeHTML($values[$opts['auth_plugin_ldap_mapping_name2']]).'</div>';
		echo '<div style="margin-left:10px;">E-mail ('.$opts['auth_plugin_ldap_mapping_email'].'): '.S::safeHTML($values[$opts['auth_plugin_ldap_mapping_email']]).'</div>';
		if ($opts['auth_plugin_ldap_mapping_company']) {
			echo '<div style="margin-left:10px;">Company (' . $opts['auth_plugin_ldap_mapping_company'] . '): ' . S::safeHTML($values[$opts['auth_plugin_ldap_mapping_company']]) . '</div>';
		}
		echo '</div>';
		return 'The test was successful';

	}
	function getSetting($fieldName) {
		global $settings;
		$keyName = 'auth_plugin_ldap_'.$fieldName;
		return $settings->$keyName;
	}

	function ssoEnabled() {
		if ($this->getSetting('allow_iwa_sso') != 'yes') {
			$this->error = 'IWA SSO needs to be set to "yes" in the authentication plugin\'s settings';
			return false;
		}
		if (!$this->getSetting('bind_dn')) {
			$this->error = 'Plugins requires a bind_dn for SSO to work';
			return false;
		}
		return true;
	}
	
	function singleSignOn() {
		if (!$this->ssoEnabled()) {return false;}
		$rs = $this->ldapConnect();
		if (!$rs) {return false;}
		$username = S::fromHTML($_SERVER['AUTH_USER']);
		$backSlashPos = strpos($username, '\\');
		if ($backSlashPos !== false) {
			$username = substr($username, $backSlashPos+1);
		}
		if (!$username) {return false;}
		return $username;
	}

	function ldapConnect($username = false, $password = false) {
		if ($this->cid) {return true;}
		$this->cid = ldap_connect($this->getSetting('host'), $this->getSetting('port'));
		if (!$this->cid) {
			$this->errorCode = 'PLUGIN_CONFIG';
			$this->error = 'Connection to the LDAP server failed!';
			return false;
		}
		ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
		ldap_set_option($this->cid, LDAP_OPT_REFERRALS, 0);
		if ($this->getSetting('bind_dn')) {
			$rs = @ldap_bind($this->cid, $this->getSetting('bind_dn'), $this->getSetting('bind_password'));
			if (!$rs) {
				//"Bind failed: ".ldap_error($cid);
				$this->errorCode = 'PLUGIN_CONFIG';
				$this->error = 'Authentication with the bind DN failed';
				return false;
			}
		} else {
			$user_bind_dn = str_replace('{USERNAME}', $username, $this->getSetting('user_dn'));
			$rs = @ldap_bind($this->cid, $user_bind_dn, $password);
			if (!$rs) {
				$this->errorCode = 'WRONG_PASS';
				$this->error = "Invalid password.";
				return false;
			}
		}
		return true;
	}

	function getUserInfo($username, $password = false) {
		$this->userRecord = $this->getRemoteUserRecord($username);
		if (!$this->userRecord) {
			$this->errorCode = 'USERNAME_NOT_FOUND';//allows fall back to local authentication
			$this->error = 'The username was not found in the remote database';
			return false;
		}
		$rs = $this->formatUserDetails($this->userRecord);
		if (!$rs) {return false;}
		$userData = $rs['userData'];
		$userPerms = $rs['userPerms'];
		$userGroups = $rs['userGroups'];
		if ($password) {//not present for SSO
			$userData['password'] = $password;
		}
		return array(
			'userData' => $userData,
			'userPerms' => $userPerms,
			'userGroups' => $userGroups
		);
	}

	function formatUserDetails($remoteRecord) {
		$remoteRecord = ldap_get_attributes($this->cid, $remoteRecord);
		$values = array();
		if (array($remoteRecord)) {
			foreach ($remoteRecord as $k => $a) {
				if (!is_numeric($k) && $k != 'count') {
					$values[$k] = $a[0];
				}
			}
		}
		$mapName = $this->getSetting('mapping_name');
		$mapName2 = $this->getSetting('mapping_name2');
		if ($mapName == $mapName2) {
			$name = $values[$mapName];
			$parts = explode(" ", $name);
			$name = array_pop($parts);
			$name2 = implode(" ", $parts);
		} else {
			$name = $values[$mapName];
			$name2 = $values[$mapName2];
		}
		$userData = array(
			'name' => $name,
			'name2' => $name2,
			'email' => $values[$this->getSetting('mapping_email')]
		);
		if ($values['uid']) {
			$userData['username'] = $values['uid'];
		}
		if ($values[$this->getSetting('mapping_company')]) {
			$userData['company'] = $values[$this->getSetting('mapping_company')];
		}
		$userPerms = array();
		if ($values['homeDirectory']) {
			$userPerms['homefolder'] = str_replace('\\', '/', $values['homeDirectory']);
		}
		if (!$userData['name']) {
			$this->error = 'Missing name for the user record';
			return false;
		}
		return array(
			'userData' => $userData,
			'userPerms' => $userPerms,
			'userGroups' => array('LDAP')
		);
	}

	function getRemoteUserRecord($username) {
		$filter = str_replace("{USERNAME}", $username, $this->getSetting('search_filter'));
		$rs = @ldap_search($this->cid, $this->getSetting('search_dn'), $filter);
		if (!$rs) {
			$this->errorCode = 'PLUGIN_CONFIG';
			$this->error = "Failed to search for the LDAP record: ".ldap_error($this->cid);
			return false;
		}
		return ldap_first_entry($this->cid, $rs);
	}

	function authenticate($username, $password) {
		$rs = $this->ldapConnect($username, $password);
		if (!$rs) {return false;}
		$this->userRecord = $this->getRemoteUserRecord($username);
		if (!$this->userRecord) {
			$this->errorCode = 'USERNAME_NOT_FOUND';//allows fall back to local authentication
			$this->error = 'The provided username is not valid';
			return false;
		}
		if ($this->getSetting('bind_dn')) {
			//binding was done with predefined credentials
			//check user provided credentials
			$user_dn = ldap_get_dn($this->cid, $this->userRecord);
			if (!$user_dn) {
				$this->errorCode = 'PLUGIN_CONFIG';
				$this->error = 'Failed to retrieve user DN for the found record!';
				return false;
			}
			$rs = @ldap_bind($this->cid, $user_dn, $password);
			if (!$rs) {
				$this->errorCode = 'WRONG_PASS';
				$this->error = "Invalid password.";
				return false;
			}
		}
		$rs = $this->getUserInfo($username, $password);
		return $rs;
	}

	function listAllUsers() {
		$rs = $this->ldapConnect();
		if (!$rs) {return false;}
		$filter = '(objectClass=person)';
		$dn = $this->getSetting('search_dn');
		$rs = @ldap_search($this->cid, $dn, $filter);
		if (!$rs) {
			$this->error = "Failed to retrieve LDAP records: ".ldap_error($this->cid);
			return false;
		}
		$rs = ldap_get_entries($this->cid, $rs);
		if (!is_array($rs)) {return false;}
		array_shift($rs);
		$list = array();
		foreach ($rs as $record) {
			$list[] = $this->formatUserDetails($record);
		}
		return $list;
	}

	function listRemovedUsers() {
		$rs = $this->ldapConnect();
		if (!$rs) {return false;}
		$filter = '(objectClass=person)';
		$dn = "ou=removed,dc=example,dc=com";//you need to configure this
		$rs = @ldap_search($this->cid, $dn, $filter);
		if (!$rs) {
			$this->error = "Failed to retrieve LDAP records: ".ldap_error($this->cid);
			return false;
		}
		$rs = ldap_get_entries($this->cid, $rs);
		if (!is_array($rs)) {return false;}
		array_shift($rs);
		$list = array();
		foreach ($rs as $record) {
			$list[] = $this->formatUserDetails($record);
		}
		return $list;
	}
}
