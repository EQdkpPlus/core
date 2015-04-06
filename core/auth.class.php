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

if(!class_exists('user')) include_once(registry::get_const('root_path').'/core/user.class.php');

class auth extends user {

	public static function __dependencies() {
		$dependencies = array('timekeeper');
		return array_merge(parent::$dependencies, $dependencies);
	}

	public $sid 					= 0;
	public $data 					= array();
	public $id						= ANONYMOUS;
	private $current_time 			= 0;
	//minimum session_length
	private $session_length			= 3600;
	public $blnFirstVisit		= false;

	private $settings = array(
		'session_length'	=> array(
			'type'		=> 'spinner',
			'size'		=> 7,
			'default'	=> 3600,
			'step'		=> 900,
			'min'			=> 0,
		)
	);

	public function __construct(){
		parent::__construct();
		if($this->config->get('session_length') > $this->session_length) $this->session_length = $this->config->get('session_length');
	}

	/**
	* Initiates the whole Session-Thing
	* @return true
	*/
	public function start(){
		$this->current_time = $this->time->time;
		$this->data['user_id'] = ANONYMOUS;
		$boolValid = false;

		//Return, if we don't want a session
		if (defined('NO_SESSION')) {
			return true;
		}

		// Remove old sessions and update user information if necessary.
		if(($this->current_time - $this->session_length) > $this->config->get('session_last_cleanup')){
			$this->cleanup($this->current_time);
		}
		//Cookie-Data
		$arrCookieData = array();
		$arrCookieData['sid']	= $this->in->getEQdkpCookie('sid');
		$arrCookieData['data']	= $this->in->getEQdkpCookie('data');
		$arrCookieData['data']	= ( !empty($arrCookieData['data']) ) ? unserialize(base64_decode(stripslashes($arrCookieData['data']))) : '';

		//Let's get a Session
		if ($this->in->exists('s') && $this->in->get('s', '') != ""){
			//s-param
			$this->sid = $this->in->get('s', '');
		} else {
			$this->sid = $arrCookieData['sid'];
		}

		//Do we have an session? If yes, try to look if it's a valid session and get all information about it
		if ($this->sid != ''){
			$arrResult = false;
			
			$objQuery = $this->db->prepare("SELECT *
								FROM __sessions s
								LEFT JOIN __users u
								ON u.user_id = s.session_user_id
								WHERE s.session_id = ?
								AND session_type = ?")->execute($this->sid, ((defined('SESSION_TYPE')) ? SESSION_TYPE : ''));
			
			if ($objQuery && $objQuery->numRows){
				$arrResult = $objQuery->fetchAssoc();
			}

			$this->data = $arrResult;
			if (!isset($this->data['user_id'])){
				$this->data['user_id'] = ANONYMOUS;
			}

			//If the Session is in our Table && is the session_length ok && the IP&Browser fits
			//prevent too short session_length
			if ($arrResult){
				//If the IP&Browser fits
				if (($arrResult['session_ip'] === $this->env->ip) && ($arrResult['session_browser'] === $this->env->useragent)){
					//Check Session length
					if ((($arrResult['session_start'] + $this->session_length) > $this->current_time)){				
						//We have a valid session
						$this->data['user_id'] = ($this->data['user_id'] == (int)$arrResult['session_user_id']) ? intval($arrResult['session_user_id']) : $this->data['user_id'];
						$this->id = $this->data['user_id'];					
						
						// Only update session DB a minute or so after last update or if page changes
						if ( !register('environment')->is_ajax && (($this->current_time - $arrResult['session_current'] > 60) || ($arrResult['session_page'] != $this->env->current_page) )){
							$this->db->prepare("UPDATE __sessions :p WHERE session_id = ?")->set(array(
								'session_current'	=> $this->current_time,
								'session_page'		=> strlen($this->env->current_page) ? substr(utf8_strtolower($this->env->current_page), 0, 254) : '',
							))->execute($this->sid);
						}
						//The Session is valid, copy the user-data to the data-array and finish the init. You you can work with this data.
	
						registry::add_const('SID', "?s=".((!empty($arrCookieData['sid'])) ? '' : $this->sid));
						return true;
					} else {
						$arrSessionKeys = explode(";", $arrResult['session_key']);
						$arrSessionKeys = array_reverse($arrSessionKeys);
						$this->data['old_sessionkey'] = $arrSessionKeys[0];
					}
				}
			}
		} else {			
			$this->blnFirstVisit = true;
		}
		
		$this->data['user_id'] = ANONYMOUS;
		
		
		//START Autologin
		$boolSetAutoLogin = false;

		//Loginmethod Autologin
		$arrAuthObjects = $this->get_login_objects();
		foreach($arrAuthObjects as $strMethods => $objMethod){
			if (method_exists($objMethod, 'autologin')){
				$arrAutologin = $objMethod->autologin($arrCookieData);
				if ($arrAutologin){
					$this->data = array_merge($this->data, $arrAutologin);
					$boolSetAutoLogin = true;
					break;
				}
			}
		}

		//EQdkp Autologin
		if (!$boolSetAutoLogin){
			$arrAutologin = $this->autologin($arrCookieData);
			if ($arrAutologin){
				$this->data = array_merge($this->data, $arrAutologin);
				$boolSetAutoLogin = true;
			}
		}

		//Bridge Autologin
		if (!$boolSetAutoLogin && $this->config->get('cmsbridge_active') == 1 && $this->config->get('pk_maintenance_mode') != 1){
			$arrAutologin = $this->bridge->autologin($arrCookieData);
			if ($arrAutologin){
				$this->data = array_merge($this->data, $arrAutologin);
				$boolSetAutoLogin = true;
			}
		}
		//END Autologin

		//Let's create a session
		$this->create($this->data['user_id'], (isset($this->data['user_login_key']) ? $this->data['user_login_key'] : ''), $boolSetAutoLogin, ((isset($this->data['old_sessionkey'])) ? $this->data['old_sessionkey'] : false)  );
		$this->id = $this->data['user_id'];
		return true;
	}

	/**
	* Creates a new Session, sets the cookies,
	*
	* @var int $user_id User-ID
	* @var string $strPwdHash Hash of the Userpassword, needed for autologin-cookie
	* @var bool $boolSetAutoLogin If the Autologin-Cookie should be set
	* @return true
	*/
	public function create ($user_id, $strAutologinKey, $boolSetAutoLogin = false, $strOldSessionKey=false){
		if (!$user_id) $user_id = ANONYMOUS;
		$this->sid = substr(md5(generateRandomBytes(55)).md5(generateRandomBytes()), 0, 40);
		$strSessionKey = $this->generate_session_key();
		if ($strOldSessionKey) $strSessionKey = $strOldSessionKey.';'.$strSessionKey;
		$this->current_time = $this->time->time;
		$arrData = array(
				'session_id'			=> $this->sid,
				'session_user_id'		=> $user_id,
				'session_last_visit'	=> (isset($this->data['session_last_visit'])) ? $this->data['session_last_visit'] : $this->current_time,
				'session_start'			=> $this->current_time,
				'session_current'		=> $this->current_time,
				'session_ip'			=> $this->env->ip,
				'session_browser'		=> $this->env->useragent,
				'session_page'			=> ($this->env->current_page) ? substr(utf8_strtolower($this->env->current_page),0,254) : '',
				'session_key'			=> $strSessionKey,
				'session_type'			=> (defined('SESSION_TYPE')) ? SESSION_TYPE : '',
		);
		$this->db->prepare('INSERT INTO __sessions :p')->set($arrData)->execute();

		//generate cookie-Data
		$arrCookieData = array();
		$arrCookieData['user_id'] = $user_id;
		if ($boolSetAutoLogin && ($user_id != ANONYMOUS)){
		
			if ($strAutologinKey == ''){
				$strAutologinKey = hash('sha512', $this->generate_salt());
				$this->updateAutologinKey($user_id, $strAutologinKey);
			}
			$arrCookieData['auto_login_id'] = $strAutologinKey;

		}
		
		
		// set the cookies
		set_cookie('data', base64_encode(serialize($arrCookieData)), $this->current_time + 2592000); //30 days
		set_cookie('sid', $this->sid, 0);
		$strCookieSID = $this->in->getEQdkpCookie('sid');
		//Check if cookie was set

		registry::add_const('SID', '?s=' . (( empty($strCookieSID) ) ? $this->sid : ''));
		$this->data['user_id'] = $user_id;
		$this->data = array_merge($this->data, $arrData);

		return true;
	}

	/**
	* Destroy the session of the current user
	*
	* @return true
	*/
	public function destroy(){
		//Update last visit of the user
		$this->db->prepare("UPDATE __users
				SET user_lastvisit=?
				WHERE user_id=?")->execute(intval($this->data['session_current']), $this->data['user_id']);

		// Delete existing session
		$this->destroy_session($this->sid);

		set_cookie('data', '0', -1);
		set_cookie('sid',  '0', -1);
		registry::add_const('SID', '?s=');
		$this->sid = '';
		return true;
	}

	/**
	* Destroys a specific Session
	*
	* @var string $strSID Session-ID
	* @var int $intUserID User-ID
	* @return true
	*/
	public function destroy_session($strSID, $intUserID = false){
		if ($intUserID){
			$this->db->prepare("DELETE FROM __sessions WHERE session_id=? AND session_user_id=?")->execute($strSID, $intUserID);
		} else {
			$this->db->prepare("DELETE FROM __sessions WHERE session_id=?")->execute($strSID);
		}

		return true;
	}


	/**
	* Deletes old Sessions and updating of last-visit-date of the uers
	*
	* @var int $intTime Current Time
	* @return true
	*/
	public function cleanup($intTime){
		
		// Get expired sessions
		$objQuery = $this->db->prepare("SELECT session_page, session_user_id, MAX(session_current) AS recent_time
						FROM __sessions
						WHERE session_start < ?
						GROUP BY session_user_id")->execute($this->time->time - ($this->session_length*2));

		if ($objQuery){
			while($row = $objQuery->fetchAssoc()){
				if ( intval($row['session_user_id']) != ANONYMOUS ){
					$this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
						'user_lastvisit'	=> $row['recent_time'],
						'user_lastpage'		=> $row['session_page'],
					))->execute($row['session_user_id']);
				}
				
				$this->db->prepare("DELETE FROM __sessions
									WHERE session_user_id = ?
									AND session_start < ?")->execute($row['session_user_id'], ($this->time->time - $this->session_length));
			}
		}
		$this->config->set('session_last_cleanup', $this->time->time);
		return true;
	}

	/**
	* Checks if a session is valid and returns the user_id
	*
	* @param $sid						Session-ID
	* @return $user_id			Returns the User-ID
	*/
	public function check_session($sid){
		$objQuery = $this->db->prepare("SELECT u.*, s.*
				FROM __sessions s, __users u
				WHERE s.session_id = ?
				AND u.user_id = s.session_user_id")->execute($sid);	

		if ($objQuery){
			$data = $objQuery->fetchAssoc();
			
			// Did the session exist in the DB?
			if(isset($data['user_id'])){
				// Validate IP
				if($data['session_ip'] == $this->env->ip){
					return $data['user_id'];
				}
			}
		}

		return ANONYMOUS;
	}

	public function removeSIDfromString($string){
		$strSearch = (strpos($string, '&') !== false) ? $this->SID.'&' : $this->SID;
		$strSearch = (strpos($string, '&amp;') !== false) ? $this->SID.'&amp;' : $this->SID;
		$strReplace = (strpos($string, '&') !== false) ? '?' : '';
		$string = preg_replace("#(\&|\&amp;)link\_hash\=([a-zA-Z0-9]{12})#", "", $string);
		return str_replace(array($strSearch, $this->sid), array($strReplace, ''), $string);
	}

	/**
	* Generates new Session Key for insertion
	*
	* @return string
	*/
	public function generate_session_key(){
		return substr(md5(generateRandomBytes(55)), 0, 12);
	}
	
	/**
	* CSRF GET Token
	*
	* @param $strAction, e.g. "delete_user"
	* @return string
	*/
	public function csrfGetToken($strAction){
		$strUserPassword = (isset($this->data['user_password_clean'])) ? $this->data['user_password_clean'] : $this->data['session_start'];
		$strSessionKeys = $this->data['session_key'];
		$arrSessionKeys = explode(";", $strSessionKeys);
		$arrSessionKeys = array_reverse($arrSessionKeys);
		$strSessionKey = $arrSessionKeys[0];
		
		return substr(sha1($strUserPassword.$strAction.$strSessionKey), 0, 12);
	}

	/**
	* Check CSRF GET Token
	*
	* @param strToken
	* @param strAction, e.g. "delete_user"
	* @return string
	*/
	public function checkCsrfGetToken($strToken, $strAction){
		$strUserPassword = (isset($this->data['user_password_clean'])) ? $this->data['user_password_clean'] : $this->data['session_start'];
		$strSessionKeys = $this->data['session_key'];
		$arrSessionKeys = explode(";", $strSessionKeys);
		$arrSessionKeys = array_reverse($arrSessionKeys);
		$strSessionKeyNew = $arrSessionKeys[0];
		//Check new Token
		$strExpectedToken = substr(sha1($strUserPassword.$strAction.$strSessionKeyNew), 0, 12);
		if ($strToken === $strExpectedToken) return true;
		
		//Check old Token
		if (isset($arrSessionKeys[1])){
			$strSessionKeyOld = $arrSessionKeys[1];
			$strExpectedToken = substr(sha1($strUserPassword.$strAction.$strSessionKeyOld), 0, 12);
			if ($strToken === $strExpectedToken) return true;
		}
		
		return false;
	}

	/**
	* CSRF POST Token
	*
	* @return string
	*/
	public function csrfPostToken($blnReturnOld=false){
		$strSessionKeys = $this->data['session_key'];
		$arrSessionKeys = explode(";", $strSessionKeys);
		$arrSessionKeys = array_reverse($arrSessionKeys);
		$strSessionKeyNew = $arrSessionKeys[0];
		if ($blnReturnOld && isset($arrSessionKeys[1])) return $arrSessionKeys[1];
		return $strSessionKeyNew;
	}

	/**
	* Check CSRF POST Token
	*
	* @param strToken
	* @return string
	*/
	public function checkCsrfPostToken($strToken){
		$strSessionKeys = $this->data['session_key'];
		$arrSessionKeys = explode(";", $strSessionKeys);
		$arrSessionKeys = array_reverse($arrSessionKeys);
		$strSessionKeyNew = $arrSessionKeys[0];
		
		$strExpectedToken = $strSessionKeyNew;
		if ($strToken === $strExpectedToken) return true;
		
		//Check old Token
		if (isset($arrSessionKeys[1])){
			$strSessionKeyOld = $arrSessionKeys[1];
			$strExpectedToken = $strSessionKeyOld;
			if ($strToken === $strExpectedToken) return true;
		}
		
		return false;
	}

	/**
	* Overtake the permissions of another User
	*
	* @param $intUserID						User-ID you want to overtake the permissions from
	*/
	public function overtake_permissions($intUserID){
		$objQuery = $this->db->prepare("UPDATE __sessions :p WHERE session_id=?")->set(array(
			'session_perm_id' => $intUserID,
		))->execute($this->sid);
	}

	/**
	* Restore your own permissions
	*/
	public function restore_permissions(){
		$objQuery = $this->db->prepare("UPDATE __sessions :p WHERE session_id=?")->set(array(
				'session_perm_id'					=> ANONYMOUS,
		))->execute($this->sid);
	}


	public function setSessionVar($strVarname, $strValue){
		$this->data['session_vars'][$strVarname] = $strValue;
	
		$objQuery = $this->db->prepare("UPDATE __sessions :p WHERE session_id=?")->set(array(
				'session_vars' => serialize($this->data['session_vars']),
		))->execute($this->sid);
	}

	/**
	* Attempt to log out a user
	*/
	public function logout() {
		//Bridge Logout
		if ($this->config->get('cmsbridge_active') == 1 && !$this->lite_mode){
			$this->bridge->logout();
		}

		//Loginmethod logout
		$arrAuthObjects = $this->get_login_objects();
		foreach($arrAuthObjects as $strMethods => $objMethod){
			if (method_exists($objMethod, 'logout')){
				$objMethod->logout();
			}
		}

		//Destroy this session
		$this->destroy();
	}

	public function get_available_loginmethods(){
		$auth = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/auth/login/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/auth/login/' . $file)) && valid_folder($file)){
					$name = substr(substr($file, 0, strpos($file, '.')), 6);
					$auth[$name] = ($this->lang('login_'.$name)) ? 'login_'.$name : ucfirst($name);
				}
			}
		}
		return $auth;;
	}

	public function get_active_loginmethods(){
		$arrLoginMethods = $this->config->get('login_method');
		if(!$arrLoginMethods) return array();
		return $arrLoginMethods;
	}

	public function get_loginmethod_settings(){
		$arrLoginMethods = $this->get_active_loginmethods();
		$settings = array();
		foreach($arrLoginMethods as $strMethod){
			include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
			$objClass = register('login_'.$strMethod);
			if (method_exists($objClass, 'settings')){
				$settings = array_merge($settings, $objClass->settings());
			}
		}
		return $settings;
	}

	public function get_loginmethod_options(){
		$arrLoginMethods = $this->get_active_loginmethods();
		$options = array();

		foreach($arrLoginMethods as $strMethod){
			include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
			$strClassname = 'login_'.$strMethod;
			if (class_exists($strClassname) && isset($strClassname::$options)){
				$options[$strMethod] = $strClassname::$options;
			}
		}

		return $options;
	}

	public function handle_login_functions($method, $loginMethod=false, $arrOptions=false){
		$arrLoginMethods = $this->get_active_loginmethods();
		if ($loginMethod) $arrLoginMethods = array($loginMethod);
		$arrReturn = array();
		if (is_array($arrLoginMethods)){
			foreach($arrLoginMethods as $strMethod){
				include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
				$classname = 'login_'.$strMethod;
				$functions = (class_exists($classname) && isset($classname::$functions)) ? $classname::$functions : array();
				
				if (isset($functions[$method])){
					$objClass = register('login_'.$strMethod);
					if (method_exists($objClass, $functions[$method])) $arrReturn[$strMethod] = $objClass->$functions[$method]($arrOptions);
				}

				if ($loginMethod) return $arrReturn[$strMethod];
			}
		}
		return $arrReturn;
	}

	public function get_login_objects($loginMethod=false){
		$arrLoginMethods = $this->get_active_loginmethods();
		if ($loginMethod) $arrLoginMethods = array($loginMethod);
		$arrReturn = array();
		if (is_array($arrLoginMethods)){
			foreach($arrLoginMethods as $strMethod){
				include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
				$objClass = register('login_'.$strMethod);
				$arrReturn[$strMethod] = $objClass;
				if ($loginMethod) return $arrReturn[$strMethod];
			}
		}
		return $arrReturn;
	}



	public function get_available_authmethods(){
		$auth = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/auth/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/auth/' . $file)) && valid_folder($file)){
					$name = substr(substr($file, 0, strpos($file, '.')), 5);
					$auth[$name] = ($this->lang('auth_'.$name, false, false)) ? $this->lang('auth_'.$name) : ucfirst($name);
				}
			}
		}
		return $auth;
	}

	public function get_authmethod_settings(){
		if (count($this->settings) > 0) return $this->settings;
		return false;
	}
}
?>