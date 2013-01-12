<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
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

if(!class_exists('user_core')) include_once(registry::get_const('root_path').'/core/user_core.class.php');

class auth extends user_core {

	public static function __shortcuts() {
		$shortcuts = array('config', 'time', 'in', 'db', 'pdh', 'bridge', 'logs', 'env');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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

	private $settings = array(
		'session_length'	=> array(
			'fieldtype'		=> 'text',
			'name'			=> 'session_length',
			'size'			=> 7,
			'not4hmode'		=> true,
			'default'		=> 3600,
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
		if($this->current_time - $this->session_length > $this->config->get('session_last_cleanup')){
			$this->cleanup($this->current_time);
		}
		//Cookie-Data
		$arrCookieData = array();
		$arrCookieData['sid']	= get_cookie('sid');
		$arrCookieData['data']	= get_cookie('data');
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
			$query = $this->db->query("SELECT *
								FROM __sessions s
								LEFT JOIN __users u
								ON u.user_id = s.session_user_id
								WHERE s.session_id = '".$this->db->escape($this->sid)."'
								AND session_type = '".$this->db->escape((defined('SESSION_TYPE')) ? SESSION_TYPE : '')."'");
			$arrResult = $this->db->fetch_record($query);
			$this->db->free_result($query);

			$this->data = $arrResult;
			if (!isset($this->data['user_id'])){
				$this->data['user_id'] = ANONYMOUS;
			}

			//If the Session is in our Table && is the session_length ok && the IP&Browser fits
			//prevent too short session_length
			if ($arrResult && (($arrResult['session_start'] + $this->session_length) > $this->current_time) ){
				//If the IP&Browser fits
				if (($arrResult['session_ip'] === $this->env->ip) && ($arrResult['session_browser'] === $this->env->useragent)){
					//We have a valid session
					$this->data['user_id'] = ($this->data['user_id'] == (int)$arrResult['session_user_id']) ? intval($arrResult['session_user_id']) : $this->data['user_id'];
					$this->id = $this->data['user_id'];
					// Only update session DB a minute or so after last update or if page changes
					if ( ($this->current_time - $arrResult['session_current'] > 60) || ($arrResult['session_page'] != $this->env->current_page) ){
						$this->db->query("UPDATE __sessions SET :params WHERE session_id = ?", array(
							'session_current'	=> $this->current_time,
							'session_page'		=> strlen($this->env->current_page) ? $this->env->current_page : '',
						), $this->sid);
					}
					//The Session is valid, copy the user-data to the data-array and finish the init. You you can work with this data.

					registry::add_const('SID', "?s=".((!empty($arrCookieData['sid'])) ? '' : $this->sid));
					return true;
				}
			}
		}
		
		//START Autologin
		$boolSetAutoLogin = false;

		//Loginmethod Autologin
		$arrAuthObjects = $this->get_login_objects();
		foreach($arrAuthObjects as $strMethods => $objMethod){
			if (method_exists($objMethod, 'autologin')){
				$arrAutologin = $objMethod->autologin($arrCookieData);
				if ($arrAutologin){
					$this->data = $arrAutologin;
					$boolSetAutoLogin = true;
					break;
				}
			}
		}

		//EQdkp Autologin
		if (!$boolSetAutoLogin){
			$arrAutologin = $this->autologin($arrCookieData);
			if ($arrAutologin){
				$this->data = $arrAutologin;
				$boolSetAutoLogin = true;
			}
		}

		//Bridge Autologin
		if (!$boolSetAutoLogin && $this->config->get('cmsbridge_active') == 1 && $this->config->get('pk_maintenance_mode') != 1){
			$arrAutologin = $this->bridge->autologin($arrCookieData);
			if ($arrAutologin){
				$this->data = $arrAutologin;
				$boolSetAutoLogin = true;
			}
		}
		//END Autologin

		//Let's create a session
		$this->create($this->data['user_id'], (isset($this->data['user_login_key']) ? $this->data['user_login_key'] : ''), $boolSetAutoLogin);
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
	public function create ($user_id, $strAutologinKey, $boolSetAutoLogin = false){
		if (!$user_id) $user_id = ANONYMOUS;
		$this->sid = substr(md5(rand().uniqid('', true).rand()).md5(rand()), 0, 40);
		$strSessionKey = $this->generate_session_key();
		$this->current_time = $this->time->time;
		$arrData = array(
				'session_id'			=> $this->sid,
				'session_user_id'		=> $user_id,
				'session_last_visit'	=> (isset($this->data['session_last_visit'])) ? $this->data['session_last_visit'] : $this->current_time,
				'session_start'			=> $this->current_time,
				'session_current'		=> $this->current_time,
				'session_ip'			=> $this->env->ip,
				'session_browser'		=> $this->env->useragent,
				'session_page'			=> ($this->env->current_page) ? $this->env->current_page : '',
				'session_key'			=> $strSessionKey,
				'session_type'			=> (defined('SESSION_TYPE')) ? SESSION_TYPE : '',
		);
		$this->db->query('INSERT INTO __sessions :params', $arrData);

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
		$strCookieSID = get_cookie('sid');
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
		$sql = "UPDATE __users
				SET user_lastvisit='" . $this->db->escape(intval($this->data['session_current'])) . "'
				WHERE user_id='" . $this->db->escape($this->data['user_id']) . "'";
		$this->db->query($sql);

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
		$sql = "DELETE FROM __sessions
						WHERE session_id='" . $this->db->escape($strSID) . "'";
		if ($intUserID) {
				$sql .= "AND session_user_id='" . $this->db->escape($intUserID) . "'";
		}

		$this->db->query($sql);

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
		$sql =	"SELECT session_page, session_user_id, MAX(session_current) AS recent_time
						FROM __sessions
						WHERE session_start < '" . $this->db->escape($this->time->time - $this->session_length) . "'
						GROUP BY session_user_id";
		$result = $this->db->query($sql);

		while($row = $this->db->fetch_record($result)){
			if ( intval($row['session_user_id']) != ANONYMOUS ){
				$this->db->query("UPDATE __users SET :params WHERE user_id = '" . $this->db->escape($row['session_user_id']) . "'", array(
					'user_lastvisit'	=> $row['recent_time'],
					'user_lastpage'		=> $row['session_page'],
				));
			}
			$this->db->query("DELETE FROM __sessions
									WHERE session_user_id = '". $this->db->escape($row['session_user_id']) . "'
									AND session_start < '". $this->db->escape($this->time->time - $this->session_length) ."'");
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
		$sql =	"SELECT u.*, s.*
				FROM __sessions s, __users u
				WHERE s.session_id = '".$this->db->escape($sid)."'
				AND u.user_id = s.session_user_id";
		$result	= $this->db->query($sql);
		$data	= $this->db->fetch_record($result);

		$this->db->free_result($result);

		// Did the session exist in the DB?
		if(isset($data['user_id'])){
			// Validate IP
			if($data['session_ip'] == $this->env->ip){
				return $data['user_id'];
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
	* Session Key
	*
	* @return string
	*/
	public function generate_session_key(){
		return substr(md5(rand().uniqid('', true).rand()), 0, 12);
	}

	/**
	* CSRF GET Token
	*
	* @param $strAction, e.g. "delete_user"
	* @return string
	*/
	public function csrfGetToken($strAction){
		$strUserPassword = (isset($this->data['user_password_clean'])) ? $this->data['user_password_clean'] : $this->data['session_start'];
		$strSessionKey = $this->data['session_key'];
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
		$strSessionKey = $this->data['session_key'];
		$strExpectedToken = substr(sha1($strUserPassword.$strAction.$strSessionKey), 0, 12);
		if ($strToken === $strExpectedToken) return true;
		return false;
	}

	/**
	* CSRF POST Token
	*
	* @return string
	*/
	public function csrfPostToken(){
		return $this->data['session_key'];
	}

	/**
	* Check CSRF POST Token
	*
	* @param strToken
	* @return string
	*/
	public function checkCsrfPostToken($strToken){
		$strExpectedToken = $this->data['session_key'];
		if ($strToken === $strExpectedToken) return true;
		return false;
	}

	/**
	* Overtake the permissions of another User
	*
	* @param $intUserID						User-ID you want to overtake the permissions from
	*/
	public function overtake_permissions($intUserID){
		$this->db->query("UPDATE __sessions SET :params WHERE session_id = '".$this->sid."'", array(
				'session_perm_id'					=> $intUserID,
		));
	}

	/**
	* Restore your own permissions
	*/
	public function restore_permissions(){
		$this->db->query("UPDATE __sessions SET :params WHERE session_id = '".$this->sid."'", array(
				'session_perm_id'					=> ANONYMOUS,
		));
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
		$arrLoginMethods = unserialize($this->config->get('login_method'));
		if(!$arrLoginMethods) return array();
		return $arrLoginMethods;
	}

	public function get_loginmethod_settings(){
		$arrLoginMethods = $this->get_active_loginmethods();
		$settings = false;
		foreach($arrLoginMethods as $strMethod){
			include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
			$objClass = register('login_'.$strMethod);
			if (method_exists($objClass, 'settings')){
				$settings['system']['login'] = $objClass->settings();
			}
		}
		return $settings;
	}

	public function get_loginmethod_options(){
		$arrLoginMethods = $this->get_active_loginmethods();
		$options = array();
		foreach($arrLoginMethods as $strMethod){
			include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
			$objClass = register('login_'.$strMethod);
			if (isset($objClass->options)){
				$options[$strMethod] = $objClass->options;
			}
		}

		return $options;
	}

	public function handle_login_functions($method, $loginMethod=false){
		$arrLoginMethods = $this->get_active_loginmethods();
		if ($loginMethod) $arrLoginMethods = array($loginMethod);
		$arrReturn = array();
		foreach($arrLoginMethods as $strMethod){
			include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
			$objClass = register('login_'.$strMethod);
			$functions = isset($objClass->functions) ? $objClass->functions : array();

			if (isset($functions[$method]) && method_exists($objClass, $functions[$method])){
				$arrReturn[$strMethod] = $objClass->$functions[$method]();
			}
			if ($loginMethod) return $arrReturn[$strMethod];
		}
		return $arrReturn;
	}

	public function get_login_objects($loginMethod=false){
		$arrLoginMethods = $this->get_active_loginmethods();
		if ($loginMethod) $arrLoginMethods = array($loginMethod);
		$arrReturn = array();
		foreach($arrLoginMethods as $strMethod){
			include_once($this->root_path . 'core/auth/login/login_'.$strMethod.'.class.php');
			$objClass = register('login_'.$strMethod);
			$arrReturn[$strMethod] = $objClass;
			if ($loginMethod) return $arrReturn[$strMethod];
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
					$auth[$name] = ($this->lang('auth_'.$name)) ? 'auth_'.$name : ucfirst($name);
				}
			}
		}
		return $auth;
	}

	public function get_authmethod_settings(){
		if (count($this->settings) > 0){
			$settings['system']['auth'] = $this->settings;
			return $settings;
		}
		return false;
	}

}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_auth', auth::__shortcuts());
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('dep_auth',auth::__dependencies());
?>