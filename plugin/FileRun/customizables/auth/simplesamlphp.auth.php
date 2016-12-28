<?php
/*
 * Plugin for authenticating FileRun users using simpleSAMLphp
 *
 * */
class customAuth_simplesamlphp {
	var $error, $errorCode, $ssaml, $attributes;
	function pluginDetails() {
		return array(
			'name' => 'SimpleSAMLphp v1.14',
			'description' => 'Authenticate users with SimpleSAMLphp',
			'fields' => array(
				array(
					'name' => 'path',
					'label' => 'Path to simpleSAMLphp',
					'required' => true
				),
				array(
					'name' => 'auth_source',
					'label' => 'Authentication source',
					'default' => 'default-sp'
				),
				array(
					'name' => 'mapping_username',
					'label' => 'Username attribute mapping',
					'default' => 'urn:oid:0.9.2342.19200300.100.1.1',
					'required' => true
				),
				array(
					'name' => 'mapping_name',
					'label' => 'First name attribute mapping',
					'default' => 'urn:oid:2.5.4.42',
					'required' => true
				),
				array(
					'name' => 'mapping_name2',
					'label' => 'Last name attribute mapping',
					'default' => 'urn:oid:2.5.4.4'
				),
				array(
					'name' => 'mapping_email',
					'label' => 'E-mail attribute mapping',
					'default' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.9'
				),
				array(
					'name' => 'mapping_groups',
					'label' => 'Group names attribute mapping',
					'default' => 'urn:oid:1.3.6.1.4.1.5923.1.1.1.1'
				)
			)
		);
	}
	function pluginTest($opts) {
		$pluginInfo = self::pluginDetails();
		//check required fields
		foreach($pluginInfo['fields'] as $field) {
			if ($field['required'] && !$opts['auth_plugin_simplesamlphp_' . $field['name']]) {
				return 'The field "' . $field['label'] . '" needs to have a value.';
			}
		}
		//check folder existance
		if (!is_dir($opts['auth_plugin_simplesamlphp_path'])) {
			return 'The path you specified does not point to an existing folder.';
		}
		//check that folder has index.php
		if (!is_file(gluePath($opts['auth_plugin_simplesamlphp_path'], '/lib/SimpleSAML/Auth/Simple.php'))) {
			return 'simpleSAMLphp was not found at the specified path.';
		}
		return 'Things seem to be in order.';
	}
	static function getSetting($fieldName) {
		global $settings;
		$keyName = 'auth_plugin_simplesamlphp_'.$fieldName;
		return $settings->$keyName;
	}

	function getSSAML() {
		if (!$this->ssaml) {
			require_once(gluePath(self::getSetting('path'), '/lib/_autoload.php'));
			$this->ssaml = new SimpleSAML_Auth_Simple(self::getSetting('auth_source'));
		}
		return $this->ssaml;
	}

	function singleSignOn() {
		global $config;
		$this->getSSAML();
		$this->ssaml->requireAuth(array('ReturnTo' => $config['url']['root'].'/sso'));
		$this->attributes = $this->ssaml->getAttributes();
		$username = $this->attributes[$this->getSetting('mapping_username')][0];
		if (!$username) {return false;}
		return $username;
	}

	function getUserInfo($username) {
		//Here you can either look up the user info in a remote database:

		//1. connect to LDAP
		//2. $remoteRecord = ldap_get_attributes($connection_id, ldap_first_entry($connection_id, ldap_search($connection_id, 'dc=example,dc=com', '(&(uid='.$username.')(objectClass=person))')));

		//or use the attributes returned by SimpleSAMLphp:
		$remoteRecord = $this->attributes;

		$userData = array(
			'name' => $remoteRecord[$this->getSetting('mapping_name')][0],
			'name2' => $remoteRecord[$this->getSetting('mapping_name2')][0],
			'email' => $remoteRecord[$this->getSetting('mapping_email')][0]
		);
		$groups = array();
		if ($this->getSetting('mapping_groups')) {
			$groups = $remoteRecord[$this->getSetting('mapping_groups')];
		}
		$groups[] = 'SimpleSAMLPHP';

		if (!$userData['name']) {
			$this->error = 'Missing name for the user record';
			return false;
		}
		$userPerms = array();
		return array(
			'userData' => $userData,
			'userPerms' => $userPerms,
			'userGroups' => $groups
		);
	}
	
	function logout() {
		global $settings, $config;
		$this->getSSAML();
		if ($this->ssaml->isAuthenticated()) {
			if ($settings->logout_redirect) {
				$redirect = $settings->logout_redirect;
			} else {
				$redirect = $config['url']['root'];
			}
			$this->ssaml->logout($redirect);
		}
	}
}