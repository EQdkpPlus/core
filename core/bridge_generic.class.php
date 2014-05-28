<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class bridge_generic extends gen_class {
	public static $shortcuts = array('config', 'pdh', 'user', 'pdl',
		'crypt'	=> 'encrypt',
	);

	protected $code					= '';
	protected $data					= array();
	protected $functions			= array();
	protected $settings				= array();
	protected $callbacks			= array();
	public $db						= false;
	public $prefix			= '';

	public function __construct() {
		$this->code = get_class($this);
		$this->prefix = $this->config->get('cmsbridge_prefix');
		
		//Initialisierung der DB-Connection
		if ($this->config->get('cmsbridge_notsamedb') == '1'){	
			$this->db = dbal::factory(array('dbtype' => 'mysql', 'die_gracefully' => true, 'debug_prefix' => 'bridge_', 'table_prefix' => $this->prefix));
			if(!$this->db->open($this->crypt->decrypt($this->config->get('cmsbridge_host')),$this->crypt->decrypt($this->config->get('cmsbridge_database')),$this->crypt->decrypt($this->config->get('cmsbridge_user')),$this->crypt->decrypt($this->config->get('cmsbridge_password'))))
				$this->db = false;
				//$this->deactivate_bridge();
		} else {
			$this->db = dbal::factory(array('dbtype' => $this->dbtype, 'die_gracefully' => true, 'debug_prefix' => 'bridge_', 'table_prefix' => $this->prefix));
			$this->db->open($this->dbhost, $this->dbname, $this->dbuser, $this->dbpass);
		}
	}
			
	public function login($strUsername, $strPassword, $boolSetAutoLogin = false, $boolUseHash = false, $blnCreateUser = true, $boolUsePassword = true){
		if (!$this->db) return false;
		//Check if username is given
		if (strlen($strUsername) == 0) return false;
		
		//Callbefore Login
		if ($this->functions['login']['callbefore'] != '' && method_exists($this, $this->functions['login']['callbefore'])){
			$method = $this->functions['login']['callbefore'];
			$this->$method(unsanitize($strUsername), unsanitize($strPassword), $boolSetAutoLogin, $boolUseHash);
		}
		$boolLoginResult = false;
		$strPwdHash = '';
		
		//Login
		if ($this->functions['login']['function'] != '' && method_exists($this, $this->functions['login']['function'])){
			$method = $this->functions['login']['function'];
			$arrResult = $this->$method(unsanitize($strUsername), unsanitize($strPassword), $boolSetAutoLogin, $boolUseHash);
			$boolLoginResult = $arrResult['status'];
			$arrUserdata 	 = $arrResult;
			$this->pdl->log('login', 'Call Bridge Login method, Result: '.(($boolLoginResult) ? 'true' : 'false'));
		} else {
			//Hole User aus der Datenbank		
			$arrUserdata = $this->get_userdata(unsanitize($strUsername));
			if ($arrUserdata){
				if ($boolUsePassword){
					$boolLoginResult = $this->check_password(unsanitize($strPassword), $arrUserdata['password'], $arrUserdata['salt'], $boolUseHash, unsanitize($strUsername));
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
		
		//Callafter Login
		if ($boolLoginResult && $this->functions['login']['callafter'] != '' && method_exists($this, $this->functions['login']['callafter'])){
			$method = $this->functions['login']['callafter'];
			$boolLoginResult = $this->$method(unsanitize($strUsername), unsanitize($strPassword), $boolSetAutoLogin, $arrUserdata, $boolLoginResult, $boolUseHash);
			$this->pdl->log('login', 'Bridge callafter, Result: '.(($boolLoginResult) ? 'true' : 'false'));
		}
		
		//Existiert der User im EQdkp? Wenn nicht, lege ihn an
		if ($boolLoginResult){
		
			if ($this->pdh->get('user', 'check_username', array(sanitize($arrUserdata['name']))) == 'false'){
				$user_id = $this->pdh->get('user', 'userid', array(sanitize($arrUserdata['name'])));
				$arrEQdkpUserdata = $this->pdh->get('user', 'data', array($user_id));
				
				list($strEQdkpUserPassword, $strEQdkpUserSalt) = explode(':', $arrEQdkpUserdata['user_password']);
				//If it's an old password without salt or there is a better algorythm
				$blnHashNeedsUpdate = $this->user->checkIfHashNeedsUpdate($strEQdkpUserPassword) || !$strEQdkpUserSalt;
				
				//Update Email und Password - den Rest soll die Sync-Funktion machen	
				if ($boolUsePassword && (!$this->user->checkPassword($strPassword, $arrEQdkpUserdata['user_password'])) || ($arrUserdata['email'] != $arrEQdkpUserdata['user_email']) || $blnHashNeedsUpdate){
					$strSalt = $this->user->generate_salt();
					$strApiKey = $this->user->generate_apikey($strPassword, $strSalt);
					$strPwdHash = $this->user->encrypt_password($strPassword, $strSalt);
					$this->pdh->put('user', 'update_user', array($user_id, array('user_email' => $this->crypt->encrypt($arrUserdata['email']), 'user_password' => $strPwdHash.':'.$strSalt, 'api_key'=>$strApiKey), false, false));
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
				$strApiKey = $this->user->generate_apikey($strPassword, $salt);
				
				$user_id = $this->pdh->put('user', 'insert_user_bridge', array(sanitize($arrUserdata['name']), $strPwdHash.':'.$salt, $arrUserdata['email'], false, $strApiKey));
				$this->pdh->process_hook_queue();
			}
		}

		//Geb jetzt das Ergebnis zurück
		if ($boolLoginResult){
			//User-Sync
			if ($this->functions['sync'] != '' && method_exists($this, $this->functions['sync']) && $this->config->get('cmsbridge_disable_sync') != 1){
				$this->sync($user_id, $arrUserdata);
			}
			
			$arrUserData = $this->pdh->get('user', 'data', array($user_id));

			return array('status'		=> 1,
						'user_id'		=> $user_id,
						'password_hash'	=> $arrUserData['password'],
						'user_login_key' => $arrUserData['user_login_key'],
						);
		}
		
		return false;		
	}
	
	public function logout(){
		if ($this->functions['logout'] != '' && method_exists($this, $this->functions['logout'])){
			$method = $this->functions['logout'];
			$this->$method();
		}
		return true;
	}
	
	public function autologin($arrCookieData){
		$result = false;
		if ($this->functions['autologin']!= '' && method_exists($this, $this->functions['autologin']) && $this->db){
			$method = $this->functions['autologin'];
			$result = $this->$method($arrCookieData);
		}
		return $result;
	}
	
	public function deactivate_bridge() {
		$this->config->set('cmsbridge_active', 0);
	}
	
	public function get_settings(){
		return $this->settings;	
	}
	
	public function sync($user_id, $arrUserdata){
		$eqdkp_user_data = $this->pdh->get('user', 'data', array($user_id));
		$eqdkp_custom_fields = $this->pdh->get('user', 'custom_fields', array($user_id));
		
		$method = $this->functions['sync'];
		$sync_array = $this->$method($arrUserdata);
		$save = false;
		if (is_array($sync_array) && count($sync_array) > 0){
			foreach ($sync_array as $key=>$value){
				if (array_key_exists($key, $eqdkp_user_data)){
					if ($eqdkp_user_data[$key] != $value){
						$save_array[$key] = $value;
						$save = true;
					}
					
				} else {
					if ($eqdkp_custom_fields[$key] != $value){
						$eqdkp_custom_fields[$key] = $value;
						$save = true;
					}
				}
			}
		}
		if ($save){
			$save_array['custom_fields'] = serialize($eqdkp_custom_fields);
			$this->pdh->put('user', 'update_user', array($user_id, $save_array, false, false));
		}
		return;
	}
	
	public function get_sync_fields(){
		if (isset($this->sync_fields) && is_array($this->sync_fields)){
			return $this->sync_fields;
		}
		return false;
	}
	
	//Returns the usergroups of the CMS/Forum
	public function get_user_groups($blnWithID = false){
		if (!$this->db) return false;
		
		if ($this->check_function('groups')){
			$method_name = $this->data['groups']['FUNCTION'];
			return $this->$method_name($blnWithID);
		} else {
			if ($this->check_query('groups')){
				$strQuery = $this->check_query('groups');			
			} else {
				$strQuery = "SELECT ".$this->data['groups']['id'].' as id, '.$this->data['groups']['name'].' as name FROM '.$this->prefix.$this->data['groups']['table'];
			}
			
			$result = $this->db->fetch_array($strQuery);
			$groups = false;

			if (is_array($result) && count($result) >0) {
				foreach ($result as $row){
					$groups[$row['id']] = $row['name'].(($blnWithID) ? ' (#'.$row['id'].')': '');
				}
			}

			return $groups;
		}
	}
	
	//Userdata of an User of the CMS/Forum
	public function get_userdata($name){
		if (!$this->db) return false;
		
		$name = unsanitize($name);
		
		//Clean Username if neccessary
		if (method_exists($this, 'convert_username')){
			$strCleanUsername = $this->convert_username($name);
		} else {
			$strCleanUsername = utf8_strtolower($name);
		}
		
		if ($this->check_query('user')){
			$strQuery = str_replace("_USERNAME_", $this->db->escape($strCleanUsername), $this->check_query('user'));
		} else {
			//Check if there's a user table
			if ( !$this->db->num_rows($this->db->query("SHOW TABLES LIKE '".$this->prefix.$this->data['user']['table']."'"))){
				$this->deactivate_bridge();
				return false;
			}
		
			$salt = ($this->data['user']['salt'] != '') ? ', '.$this->data['user']['salt'].' as salt ': '';
			$strQuery = "SELECT *, ".$this->data['user']['id'].' as id, '.$this->data['user']['name'].' as name, '.$this->data['user']['password'].' as password, '.$this->data['user']['email'].' as email'.$salt.'
						FROM '.$this->prefix.$this->data['user']['table'].' 
						WHERE LOWER('.$this->data['user']['where'].") = '".$this->db->escape($strCleanUsername)."'";
		}

		$query = $this->db->query($strQuery);
		$arrResult = $this->db->fetch_record($query);
		if ($salt == '')  $arrResult['salt'] = ''; 
		return $arrResult;
	}
	
	//Checks if the User is in the groups
	public function check_user_group($intUserID){
		if (!$this->db) return false;
		
		$groups = explode(',', $this->config->get('cmsbridge_groups'));
		
		if ($this->check_function('user_group')){
			$method_name = $this->data['user_group']['FUNCTION'];
			return $this->$method_name($intUserID, $groups);
		} else {

			if ($this->check_query('user_group')){
				$strQuery = str_replace("_USERID_", $this->db->escape($intUserID), $this->check_query('user_group'));
			} else {
				$strQuery = "SELECT ".$this->data['user_group']['group'].' as group_id 
							FROM '.$this->prefix.$this->data['user_group']['table'].' 
							WHERE '.$this->data['user_group']['user']." = '".$this->db->escape($intUserID)."'";
			}
			$result = $this->db->fetch_array($strQuery);
			if ($result && is_array($result)){
				foreach ($result as $row){
					if (is_array($groups) && in_array($row['group_id'], $groups)){
						return true;
					}
				}
			}
		}
		
		return false;
	}
		
	public function check_user_group_table(){
		if ($this->get_user_groups() && count($this->get_user_groups() > 0)){
			return true;
		} else {
			return false;
		}
	}
	
	public function get_prefix(){
		$alltables = $this->db->get_tables();
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
	
	public function get_available_bridges(){
		$bridges = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/bridges/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/bridges/' . $file)) && valid_folder($file)){
					include_once($this->root_path . 'core/bridges/' . $file);
					$name = substr($file, 0, strpos($file, '.'));
					$classname = $name.'_bridge';
					$class = registry::register($classname);
					$bridges[$name] = (isset($class->name)) ? $class->name : $name;
					unset($class);
				}
			}
		}
		return $bridges;
	}
	
	//Checks if we have to use an special query instead of the predefined querys
	private function check_query($key){
		if ($this->data[$key]['QUERY'] != "") {	
			return str_replace('___', $this->prefix, $this->data[$key]['QUERY']);
		} else {
			return false;
		}
	}
	
	private function check_function($key){
		if (isset($this->data[$key]['FUNCTION']) && $this->data[$key]['FUNCTION'] != "" && method_exists($this, $this->data[$key]['FUNCTION'])){
			return true;
		}
		return false;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_bridge_generic', bridge_generic::$shortcuts);
?>