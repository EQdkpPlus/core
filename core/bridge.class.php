<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class bridge extends gen_class {
	public static $shortcuts = array(
		'crypt'	=> 'encrypt',
	);

	protected $type			= '';
	public $bridgedb		= false;
	public $prefix			= '';
	public $objBridge		= false;
	public $status			= false;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->type		= $this->config->get('cmsbridge_type');
		$this->prefix	= $this->config->get('cmsbridge_prefix');
		//First, try connection
		$this->connect();
		//Then include the Bridge and pass DBAL Object
		$this->init();
	}
	
	/**
	 * Include the Bridge file
	 * 
	 * @return boolean
	 */
	private function init(){
		//Dont include if there is no connection
		if(!$this->status) return false;
		
		include_once($this->root_path."core/bridges/bridge_generic.class.php");
		if (is_file($this->root_path."core/bridges/".$this->type.".bridge.class.php")){
			include_once($this->root_path."core/bridges/".$this->type.".bridge.class.php");
			$this->objBridge = registry::register($this->type.'_bridge', array($this->bridgedb, $this->prefix));
			return true;
		}
		return false;
	}
	
	
	/**
	 * Initialize the Bridge Database Connection
	 */
	private function connect(){
		if ((int)$this->config->get('cmsbridge_notsamedb') == 1){
			try {
				$this->bridgedb = dbal::factory(array('dbtype' => registry::get_const('dbtype'), 'debug_prefix' => 'bridge_', 'table_prefix' => $this->prefix));
				$this->bridgedb->connect($this->crypt->decrypt($this->config->get('cmsbridge_host')),$this->crypt->decrypt($this->config->get('cmsbridge_database')),$this->crypt->decrypt($this->config->get('cmsbridge_user')),$this->crypt->decrypt($this->config->get('cmsbridge_password')));
				$this->status = true;
			} catch(DBALException $e){
				$this->bridgedb = $this->status = false;
				$this->pdl->log('bridge', 'Connection error: '.$e->getMessage());
			}
		} else {
			try {
				$this->bridgedb = dbal::factory(array('dbtype' => registry::get_const('dbtype'), 'open' => true, 'debug_prefix' => 'bridge_', 'table_prefix' => $this->prefix));
				$this->status = true;
			} catch(DBALException $e){
				$this->bridgedb = $this->status = false;
				$this->pdl->log('bridge', 'Connection error: '.$e->getMessage());
			}
		}	
	}
			
	/**
	 * Login using the Bridge
	 * Requires working Bridge Connection.
	 * 
	 * @param string $strUsername
	 * @param string $strPassword
	 * @param boolean $boolSetAutoLogin
	 * @param boolean $boolUseHash
	 * @param boolean $blnCreateUser
	 * @param boolean $boolUsePassword
	 * @return boolean|array
	 */
	public function login($strUsername, $strPassword, $boolSetAutoLogin = false, $boolUseHash = false, $blnCreateUser = true, $boolUsePassword = true){
		if (!$this->status || !$this->objBridge) return false;
		
		//Check if username is given
		if (strlen($strUsername) == 0) return false;
		
		//Callbefore Login - without any influence
		$this->objBridge->before_login(unsanitize($strUsername), unsanitize($strPassword), $boolSetAutoLogin, $boolUseHash);
		
		$boolLoginResult = false;
		$strPwdHash = '';
		
		//Login
		$arrLoginmethodResult = $this->objBridge->login(unsanitize($strUsername), unsanitize($strPassword), $boolSetAutoLogin, $boolUseHash);
		if($arrLoginmethodResult !== 0){
			$boolLoginResult = $arrLoginmethodResult['status'];
			$arrUserdata 	 = $arrLoginmethodResult;
			$this->pdl->log('login', 'Call Bridge Login method, Result: '.(($boolLoginResult) ? 'true' : 'false'));
		} else {
			//Hole User aus der Datenbank		
			$arrUserdata = $this->get_userdata(unsanitize($strUsername));
			if ($arrUserdata){
				if ($boolUsePassword){
					$boolLoginResult = $this->objBridge->check_password(unsanitize($strPassword), $arrUserdata['password'], $arrUserdata['salt'], $boolUseHash, unsanitize($strUsername), $arrUserdata);
					$this->pdl->log('login', 'Check Bridge Password, Result: '.(($boolLoginResult) ? 'true' : 'false'));
					//Passwort stimmt, jetzt müssen wir schaun, ob er auch in der richtigen Gruppe ist
					if ($boolLoginResult){
						$boolLoginResult = $this->check_user_group((int)$arrUserdata['id']);
						$this->pdl->log('login', 'Check Bridge Groups, Result: '.(($boolLoginResult) ? 'true' : 'false'));
					}
				} else {
					$boolLoginResult = $this->check_user_group((int)$arrUserdata['id']);
					$this->pdl->log('login', 'Check Bridge Groups, without password: '.(($boolLoginResult) ? 'true' : 'false'));
				}			
			}
		}
		
		//Callafter Login - has influence to loginstatus
		$boolLoginResult = $this->objBridge->after_login(unsanitize($strUsername), unsanitize($strPassword), $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash);
		$this->pdl->log('login', 'Bridge callafter, Result: '.(($boolLoginResult) ? 'true' : 'false'));

		
		//Existiert der User im EQdkp? Wenn nicht, lege ihn an
		if ($boolLoginResult){
		
			if ($this->pdh->get('user', 'check_username', array(sanitize($arrUserdata['name']))) == 'false'){
				$user_id = $this->pdh->get('user', 'userid', array(sanitize($arrUserdata['name'])));
				$arrEQdkpUserdata = $this->pdh->get('user', 'data', array($user_id));
				
				list($strEQdkpUserPassword, $strEQdkpUserSalt) = explode(':', $arrEQdkpUserdata['user_password']);
				//If it's an old password without salt or there is a better algorythm
				$blnHashNeedsUpdate = $this->user->checkIfHashNeedsUpdate($strEQdkpUserPassword) || !$strEQdkpUserSalt;
				
				//Update Email und Password - den Rest soll die Sync-Funktion machen	
				if ($boolUsePassword && $strPassword && ((!$this->user->checkPassword($strPassword, $arrEQdkpUserdata['user_password'])) || ($this->objBridge->blnSyncEmail && ( $arrUserdata['email'] != $arrEQdkpUserdata['user_email'])) || $blnHashNeedsUpdate)){
					$strSalt = $this->user->generate_salt();
					$strPwdHash = $this->user->encrypt_password($strPassword, $strSalt);
					$arrToSync = array('user_password' => $strPwdHash.':'.$strSalt);
					if ($this->objBridge->blnSyncEmail) $arrToSync['user_email'] = $this->crypt->encrypt($arrUserdata['email']);
					$this->pdh->put('user', 'update_user', array($user_id, $arrToSync, false, false));
					$this->pdh->process_hook_queue();
				}
				
				//Ist EQdkp-User active?
				if ($this->pdh->get('user', 'active', array($user_id)) == '0'){
					return false;
				}
			} elseif ($blnCreateUser) {

				//Neu anlegen
				$salt = $this->user->generate_salt();
				$strPwdHash = $this->user->encrypt_password($strPassword, $salt);
				
				$user_id = $this->pdh->put('user', 'insert_user_bridge', array(sanitize($arrUserdata['name']), $strPwdHash.':'.$salt, $arrUserdata['email'], false));
				$this->pdh->process_hook_queue();
			}
		}

		//Geb jetzt das Ergebnis zurück
		if ($boolLoginResult){
			//Userdata-Sync
			if ($this->config->get('cmsbridge_disable_sync') != 1){
				$this->sync_fields($user_id, $arrUserdata);
			}
			//Usergroup-Sync
			$this->sync_usergroups((int)$arrUserdata['id'], $user_id);
			
			$arrUserData = $this->pdh->get('user', 'data', array($user_id));

			return array('status'		=> 1,
						'user_id'		=> $user_id,
						'password_hash'	=> $arrUserData['password'],
						'user_login_key' => $arrUserData['user_login_key'],
						);
		}
		
		return false;		
	}
	
	/**
	 * Logout
	 * Requires working Bridge Connection.
	 * 
	 * @return boolean always true
	 */
	public function logout(){
		if (!$this->status || !$this->objBridge) return true;
		
		$this->objBridge->logout();
		return true;
	}
	
	/**
	 * Autologin for Bridge
	 * Requires working Bridge Connection.
	 * 
	 * @param array $arrCookieData
	 * @return boolean
	 */
	public function autologin($arrCookieData){
		if (!$this->status || !$this->objBridge) return false;
		
		$result = $this->objBridge->autologin($arrCookieData);
		return $result;
	}
	
	/**
	 * Deactivate the Bridge
	 */
	public function deactivate_bridge() {
		$this->config->set('cmsbridge_active', 0);
	}
	
	/**
	 * Returns the Bridge specific settings
	 * 
	 * @return array
	 */
	public function get_settings(){
		return $this->objBridge->settings;	
	}

	/**
	 * Syncs the Fields of a User from CMS to EQdkp
	 * Requires working Bridge Connection.
	 * 
	 * @param integer $user_id
	 * @param array $arrUserdata
	 */
	public function sync_fields($user_id, $arrUserdata){
		if (!$this->status || !$this->objBridge) return false;
		
		//Key: Bridge ID, Value: EQdkp Profilefield ID
		$arrMapping = $this->pdh->get('user_profilefields', 'bridge_mapping');
		
		$eqdkp_user_data = $this->pdh->get('user', 'data', array($user_id));
		$eqdkp_custom_fields = $this->pdh->get('user', 'custom_fields', array($user_id));
		
		//Key: Bridge ID, Value: Bridge Profilefield Value
		$sync_array = $this->objBridge->sync($arrUserdata);
		
		foreach($arrMapping as $intBridgeID => $intEQdkpFieldID){
			if (isset($sync_array[$intBridgeID])){
				$currentVal = $eqdkp_custom_fields['userprofile_'.$intEQdkpFieldID];
				$newVal = $sync_array[$intBridgeID];
				
				if ($currentVal != $newVal){
					$save = true;
					$eqdkp_custom_fields['userprofile_'.$intEQdkpFieldID] = $newVal;
				}
			}
		}
		
		//Birthday is a user field
		if (isset($sync_array['birthday'])){
			if ($save_array['birthday'] != $sync_array['birthday']){
				$save_array['birthday'] = $sync_array['birthday'];
				$save = true;
			}
		}
		
		//Country is a user field
		if (isset($sync_array['country'])){
			if ($save_array['country'] != $sync_array['country']){
				$save_array['country'] = $sync_array['country'];
				$save = true;
			}
		}
		
		if ($save){
			$save_array['custom_fields'] = serialize($eqdkp_custom_fields);
			$this->pdh->put('user', 'update_user', array($user_id, $save_array, false, false));
		}
		return;
	}
	
	/**
	 * Returns array with all available Sync Fields from CMS
	 * Requires working Bridge Connection.
	 * 
	 * @return array
	 */
	public function get_available_sync_fields(){
		if (!$this->status || !$this->objBridge) return array();
		
		$arrAvailableFields = $this->objBridge->sync_fields();
		return $arrAvailableFields;
	}
	
	/**
	 * Get all Usergroups from CMS.
	 * Requires working Bridge Connection.
	 * 
	 * @param boolean $blnWithID Append GroupID to Groupname
	 * @return mixed
	 */
	public function get_user_groups($blnWithID = false){
		if (!$this->status || !$this->objBridge) return false;
		
		if ($this->check_function('groups')){
			$method_name = $this->objBridge->data['groups']['FUNCTION'];
			return $this->objBridge->$method_name($blnWithID);
		} else {
			if ($this->check_query('groups')){
				$strQuery = $this->check_query('groups');	
				$objQuery = $this->bridgedb->query($strQuery);		
			} else {
				$objQuery = $this->bridgedb->query("SELECT ".$this->objBridge->data['groups']['id']." as id, ".$this->objBridge->data['groups']['name']." as name FROM ".$this->prefix.$this->objBridge->data['groups']['table']);
			}
			
			if ($objQuery){
				$arrResult = $objQuery->fetchAllAssoc();
				$groups = false;
				
				if (is_array($arrResult) && count($arrResult) > 0) {
					foreach ($arrResult as $row){
						$groups[$row['id']] = $row['name'].(($blnWithID) ? ' (#'.$row['id'].')': '');
					}
				}
				
				return $groups;
			}
		}
		return false;
	}
	

	/**
	 * Get CMS Userdata of a CMS User.
	 * Requires working Bridge Connection.
	 * 
	 * @param string $name
	 * @return mixed
	 */
	public function get_userdata($name){
		if (!$this->status || !$this->objBridge) return false;
		
		$name = unsanitize($name);
		
		//Clean Username if neccessary
		if (method_exists($this->objBridge, 'convert_username')){
			$strCleanUsername = $this->objBridge->convert_username($name);
		} else {
			$strCleanUsername = utf8_strtolower($name);
		}
		
		if ($this->check_query('user')){
			$strQuery = str_replace("_USERNAME_", "?", $this->check_query('user'));
		} else {
			//Check if there's a user table
			$arrTables = $this->bridgedb->listTables();
			if (!in_array($this->prefix.$this->objBridge->data['user']['table'], $arrTables)){
				$this->deactivate_bridge();
				return false;
			}
		
			$salt = ($this->objBridge->data['user']['salt'] != '') ? ', '.$this->objBridge->data['user']['salt'].' as salt ': '';
			$strQuery = "SELECT *, ".$this->objBridge->data['user']['id'].' as id, '.$this->objBridge->data['user']['name'].' as name, '.$this->objBridge->data['user']['password'].' as password, '.$this->objBridge->data['user']['email'].' as email'.$salt.'
						FROM '.$this->prefix.$this->objBridge->data['user']['table'].' 
						WHERE LOWER('.$this->objBridge->data['user']['where'].") = ?";
		}

		$objQuery = $this->bridgedb->prepare($strQuery)->execute($strCleanUsername);
		if ($objQuery){
			$arrResult = $objQuery->fetchAssoc();
			if ($salt == '')  $arrResult['salt'] = '';
			return $arrResult;
		}
		return false;
	}
	
	/**
	 * Get all Users from the CMS.
	 * Requires working Bridge Connection.
	 * 
	 * @return mixed 
	 */
	public function get_users(){
		if (!$this->status || !$this->objBridge) return false;
		
		if ($this->check_query('user')) return false;
		
		//Check if there's a user table
		$arrTables = $this->bridgedb->listTables();
		if (!in_array($this->prefix.$this->objBridge->data['user']['table'], $arrTables)){
			//Disabled Bridge if there is no access to the User Table
			$this->deactivate_bridge();
			return false;
		}
		
		$salt = ($this->objBridge->data['user']['salt'] != '') ? ', '.$this->objBridge->data['user']['salt'].' as salt ': '';
		
		$strQuery = "SELECT ".$this->objBridge->data['user']['id'].' as id, '.$this->objBridge->data['user']['name'].' as name, '.$this->objBridge->data['user']['password'].' as password, '.$this->objBridge->data['user']['email'].' as email'.$salt.', LOWER('.$this->objBridge->data['user']['where'].') AS username_clean
						FROM '.$this->prefix.$this->objBridge->data['user']['table'];
		$objQuery = $this->bridgedb->query($strQuery);
		$arrResult = false;
		if ($objQuery){
			$arrResult = $objQuery->fetchAllAssoc();
		}
		
		return $arrResult;
	}
	
	/**
	 * Sync the Usergroups of the User with CMS
	 * 
	 * @param integer $intCMSUserID
	 * @param integer $intUserID
	 * @return boolean
	 */
	public function sync_usergroups($intCMSUserID, $intUserID){
		$arrGroupsToSync = explode(',', $this->config->get('cmsbridge_sync_groups'));
		if (!array($arrGroupsToSync) || count($arrGroupsToSync) < 1) return false;
		
		$arrCMSToEQdkpID = array();
		
		//Get GroupIDs of CMS User
		$arrUserGroups = $this->get_usergroups_for_user($intCMSUserID);
		
		//Get all EQdkp Groups
		$arrEQdkpGroups = array();
		foreach($this->pdh->get('user_groups', 'id_list') as $key){
			$arrEQdkpGroups[$key] = utf8_strtolower($this->pdh->get('user_groups', 'name', array($key)));
		}

		//Get all CMS Groups
		$arrCMSGroups = $this->get_user_groups();
		foreach($arrCMSGroups as $groupID => $groupName){
			$groupName = utf8_strtolower($groupName);
			//If group should be synced, and it does not exist, create it
			if (in_array($groupID, $arrGroupsToSync)){
				if (!in_array($groupName, $arrEQdkpGroups)){
					//Create the Group
					$intEQdkpGroupID = max($this->pdh->get('user_groups', 'id_list'))+1;
					$this->pdh->put('user_groups', 'add_grp', array($intEQdkpGroupID, $groupName, 'Synced by CMS-Bridge', 0, 1));
					$arrCMSToEQdkpID[$groupID] = $intEQdkpGroupID;
					$this->pdh->process_hook_queue();
				} else {
					//Search for the name
					$key = array_search($groupName, $arrEQdkpGroups);
					if ($key !== false) $arrCMSToEQdkpID[$groupID] = $key;
				}
			}
		}	
		
		//Get EQdkp Group Memberships
		$arrEQdkpMemberships = $this->pdh->get('user_groups_users', 'memberships', array($intUserID));
		
		foreach($arrGroupsToSync as $groupID){
			$intEQdkpGroupID = $arrCMSToEQdkpID[$groupID];
			if (in_array($groupID, $arrUserGroups) && !in_array($intEQdkpGroupID, $arrEQdkpMemberships)){
				//add to group
				$this->pdh->put('user_groups_users', 'add_user_to_group', array($intUserID, $intEQdkpGroupID));
			} elseif (!in_array($groupID, $arrUserGroups) && in_array($intEQdkpGroupID, $arrEQdkpMemberships)){
				//remove from group
				$this->pdh->put('user_groups_users', 'delete_user_from_group', array($intUserID, $intEQdkpGroupID));
			}
		}
		
	}
	
	/**
	 * Returns array with the Usergroup-IDs the CMS User is member of.
	 * Requires working Bridge Connection.
	 * 
	 * @param integer $intUserID
	 * @return boolean|multitype:unknown 
	 */
	public function get_usergroups_for_user($intUserID){
		if (!$this->status || !$this->objBridge) return false;
		
		$arrReturn = array();
		
		if ($this->check_function('user_group')){
			$method_name = $this->objBridge->data['user_group']['FUNCTION'];
			return $this->objBridge->$method_name($intUserID);
		} else {

			if ($this->check_query('user_group')){
				$strQuery = str_replace("_USERID_", "?", $this->check_query('user_group'));
			} else {
				$strQuery = "SELECT ".$this->objBridge->data['user_group']['group'].' as group_id 
							FROM '.$this->prefix.$this->objBridge->data['user_group']['table'].' 
							WHERE '.$this->objBridge->data['user_group']['user']." = ?";
			}
			$objQuery = $this->bridgedb->prepare($strQuery)->execute($intUserID);
			if ($objQuery){
				$arrResult = $objQuery->fetchAllAssoc();
				if ($arrResult && is_array($arrResult)){
					foreach ($arrResult as $row){
						$arrReturn[] = $row['group_id'];
					}
				}
			}
		}
		
		return $arrReturn;
	}
	
	
	
	/**
	 * Checks if User is in defined CMS Groups
	 * Requires working Bridge Connection.
	 * 
	 * @param integer $intUserID
	 * @return boolean
	 */
	public function check_user_group($intUserID){
		if (!$this->status || !$this->objBridge) return false;
		
		$arrAllowedGroups = explode(',', $this->config->get('cmsbridge_groups'));
		
		if ($this->check_function('check_user_group')){
			$method_name = $this->objBridge->data['check_user_group']['FUNCTION'];
			return $this->objBridge->$method_name($intUserID, $arrAllowedGroups);
		} else {

			$arrGroups = $this->get_usergroups_for_user($intUserID);
			if (is_array($arrGroups)){
				foreach($arrGroups as $groupID){
					if (is_array($arrAllowedGroups) && in_array($groupID, $arrAllowedGroups)){
						return true;
					}
				}
			}
		
		}
		
		return false;
	}
		
	/**
	 * Checks if Usergroups Table of CMS is accessable.
	 * Requires working Bridge Connection.
	 * 
	 * @return boolean True if accessable, otherwise false
	 */
	public function check_user_group_table(){
		if (!$this->status || !$this->objBridge) return false;
		
		if ($this->get_user_groups() && count($this->get_user_groups() > 0)){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Returns all Prefixes for Bridge Database.
	 * Requires working Bridge Connection.
	 * 
	 * @return array 
	 */
	public function get_prefix(){
		if (!$this->status) return array();
		
		$alltables = $this->bridgedb->listTables();
		$tables		= array();
		foreach ($alltables as $name){
			if (strpos($name, '_') !== false){
				$prefix = substr($name, 0, strpos($name, '_')+1);
				$tables[$prefix] = $prefix;
			} elseif  (strpos($name, '.') !== false){
				$prefix = substr($name, 0, strpos($name, '.')+1);
				$tables[$prefix] = $prefix;
			}
		}
		return $tables;
	}
	
	/**
	 * Returns array will all available Bridges
	 * 
	 * @return array
	 */
	public function get_available_bridges(){
		include_once $this->root_path.'core/bridges/bridge_generic.class.php';
		$bridges = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/bridges/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/bridges/' . $file)) && valid_folder($file)){
					if ($file == 'bridge_generic.class.php') continue;
					
					include_once($this->root_path . 'core/bridges/' . $file);
					$name = substr($file, 0, strpos($file, '.'));
					$classname = $name.'_bridge';
					$static_name = $classname::$name;
					$bridges[$name] = (strlen($static_name)) ? $static_name : $name;
				}
			}
		}
		return $bridges;
	}
	
	/**
	 * Checks if there is an special Query instead of the predefined.
	 * Returns false if there is none, otherwise returns the Querystring.
	 * 
	 * @param string $key
	 * @return mixed|boolean
	 */
	private function check_query($key){
		if ($this->objBridge && $this->objBridge->data[$key]['QUERY'] != "") {	
			return str_replace('___', $this->prefix, $this->objBridge->data[$key]['QUERY']);
		} else {
			return false;
		}
	}
	
	/**
	 * Checks if there is an special Function instead of the predefined.
	 * 
	 * @param string $key
	 * @return boolean
	 */
	private function check_function($key){
		if ($this->objBridge && isset($this->objBridge->data[$key]['FUNCTION']) && $this->objBridge->data[$key]['FUNCTION'] != "" && method_exists($this->objBridge, $this->objBridge->data[$key]['FUNCTION'])){
			return true;
		}
		return false;
	}
}
?>