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

class user extends gen_class {
	public static $dependencies = array('pfh');
	
	/**
	 *	Field Definitions
	 */
	public static $customFields = array('user_avatar', 'user_gravatar_mail', 'user_avatar_type');

	private $lang			= array();		// Loaded language pack
	private $loaded_plugs	= array();		// Not loaded plug-langs
	private $plugs_to_load	= array();		// Plugins, which have language to load
	private $unused			= array();		// Unused language keys
	public $style			= array();		// Style data
	public $lang_name		= '';			// Pack name (ie 'english')

	/**
	* Constructor
	*/
	public function __construct(){
        if(!$this->pdl->type_known("language"))
			$this->pdl->register_type("language", false, array($this, 'pdl_html_format_language'), array(4));
        if(!$this->pdl->type_known("unused_language"))
			$this->pdl->register_type("unused_language", array($this, 'pdl_pt_format_unused_language'), array($this, 'pdl_html_format_unused_language'), array(4));
	}

	public function getUserIDfromExchangeKey($strKey){
		if (!strlen($strKey)) return ANONYMOUS;

		$objQuery = $this->db->prepare("SELECT user_id FROM __users
				WHERE exchange_key =?")->execute($strKey);
		if ($objQuery){
			$data = $objQuery->fetchAssoc();
			if(isset($data['user_id'])){
				return (int)$data['user_id'];
			}
		}
		
		return ANONYMOUS;
	}

	//Returns true, if user to recent session is logged in
	public function is_signedin(){
		return ($this->id != ANONYMOUS);
	}

	/**
	* Sets up user-data like language and style
	*
	* @param $strLanguage Language to set
	* @param $intStyleID Style ID to set
	*/
	public function setup($strLanguage = '', $intStyleID = 0){
		$this->data['session_vars'] = (strlen($this->data['session_vars']) && is_serialized($this->data['session_vars'])) ? unserialize($this->data['session_vars']) : array();
		
		//Set Session Vars
		if($strLanguage != ""){
			$this->setSessionVar("lang", substr($strLanguage, 0, 20));
		}
		if($intStyleID > 0 && $this->data['user_id'] == ANONYMOUS){
			$this->setSessionVar("style", $intStyleID);
		}
		
		//START Language
		//-----------------------------
		if ($this->data['user_id'] == ANONYMOUS) {
			$strLanguage = (isset($this->data['session_vars']['lang']) && strlen($this->data['session_vars']['lang'])) ? $this->data['session_vars']['lang'] : ''; 
			$this->lang_name = ( $strLanguage != '' && file_exists($this->root_path . 'language/' . $strLanguage) ) ? $strLanguage : $this->config->get('default_lang');
			$this->data['user_lang'] = $this->lang_name;
		} else {
			$this->lang_name = (!empty($this->data['user_lang'])) ? $this->data['user_lang'] : $this->config->get('default_lang');
			$this->lang_name = ( $strLanguage != '' && file_exists($this->root_path . 'language/' . $strLanguage) ) ? $strLanguage : $this->lang_name;
			$this->data['user_lang'] = $this->lang_name;
		}
		$this->init_lang($this->lang_name);
		if ($this->lang_name == "german") {
			setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');
		}
		//-----------------------------
		//END Language


		//START Style
		//-----------------------------
		//What Style-ID should be used?
		$intStyleID = (isset($this->data['session_vars']['style']) && strlen($this->data['session_vars']['style'])) ? $this->data['session_vars']['style'] : $intStyleID;
		if (intval($intStyleID) > 0){
			$intUserStyleID = (int)$intStyleID;
		} elseif ($this->data['user_id'] == ANONYMOUS){
			$intUserStyleID = $this->config->get('default_style');
		} else {
			$intUserStyleID = (isset($this->data['user_style']) && !$this->config->get('default_style_overwrite')) ? $this->data['user_style'] : $this->config->get('default_style');
		}

		$intStyleID = intval($intUserStyleID);

		//Mobile Device?
		$intStyleID = ($this->config->get('mobile_template') && strlen($this->config->get('mobile_template')) && $this->env->agent->mobile && registry::get_const('mobile_view')) ? intval($this->config->get('mobile_template')) : $intStyleID;

		//Get Style-Information
		$objQuery = $this->db->prepare("SELECT * FROM __styles WHERE style_id=?")->execute($intStyleID);
		if ($objQuery && $objQuery->numRows){
			$this->style = $objQuery->fetchAssoc();	
		}

		//No Style-Information -> Fallback to the default style
		if ( !$this->style && ($intStyleID != $this->config->get('default_style'))) {
			$objQuery = $this->db->prepare("SELECT * FROM __styles WHERE style_id=?")->execute((int)$this->config->get('default_style'));
			if ($objQuery && $objQuery->numRows){
				$this->style = $objQuery->fetchAssoc();
			}
		}
		
		//No infos about default style? Take the first one
		//Fallback to first available style
		if ( !$this->style) {
			$objQuery = $this->db->prepare("SELECT * FROM __styles ORDER BY style_id ASC")->limit(1)->execute();
			if ($objQuery && $objQuery->numRows) {
				$this->style = $objQuery->fetchAssoc();
				$intStyleID = $this->style['style_id'];
			}
		}
		
		//Now set the Style-Settings
		if(empty($this->data['user_timezone']) || $this->data['user_timezone'] == '') $this->data['user_timezone'] = $this->config->get('timezone');

		if ($this->data['user_id'] == ANONYMOUS){
			$this->data['user_alimit']			= $this->config->get('default_alimit');
			$this->data['user_elimit']			= $this->config->get('default_elimit');
			$this->data['user_ilimit']			= $this->config->get('default_ilimit');
			$this->data['user_nlimit']			= $this->config->get('default_nlimit');
			$this->data['user_rlimit']			= $this->config->get('default_rlimit');

			$this->style['date_notime_long']	= ($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->lang('style_date_long');
			$this->style['date_notime_short']	= ($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->lang('style_date_short');
			$this->style['time']				= ($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->lang('style_time');
			$this->style['date_time']			= $this->style['date_notime_short'].' '.$this->style['time'];
			$this->style['date']				= 'l, '.$this->style['date_notime_long'];
			$this->style['date_short']			= 'D '.$this->style['date_notime_short'].' '.$this->style['time'];
			$this->data['user_style']			= $intStyleID;
		} else {
			$this->style['date_notime_long']	= ($this->data['user_date_long']) ? $this->data['user_date_long'] : (($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->lang('style_date_long'));
			$this->style['date_notime_short']	= ($this->data['user_date_short']) ? $this->data['user_date_short'] : (($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->lang('style_date_short'));
			$this->style['time']				= ($this->data['user_date_time']) ? $this->data['user_date_time'] : (($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->lang('style_time'));
			$this->style['date_time']			= $this->style['date_notime_short'].' '.$this->style['time'];
			$this->style['date']				= 'l, '.$this->style['date_notime_long'];
			$this->style['date_short']			= 'D '.$this->style['date_notime_short'].' '.$this->style['time'];

			$this->data['privacy_settings'] 	= ($this->data['privacy_settings'] && unserialize($this->data['privacy_settings'])) ? unserialize($this->data['privacy_settings']) : array();
			$this->data['custom_fields'] 		= ($this->data['custom_fields'] && unserialize($this->data['custom_fields'])) ? unserialize($this->data['custom_fields']) : array();
			$this->data['plugin_settings'] 		= ($this->data['plugin_settings'] && unserialize($this->data['plugin_settings'])) ? unserialize($this->data['plugin_settings']) : array();
			$this->data['notification_settings'] = ($this->data['notifications'] && unserialize($this->data['notifications'])) ? unserialize($this->data['notifications']) : array();
				
			list($this->data['user_password_clean'], $this->data['user_salt']) = explode(':', $this->data['user_password']);
			$this->data['user_email']			= register('encrypt')->decrypt($this->data['user_email']);
			$this->data['auth_account'] 		= @unserialize(register('encrypt')->decrypt($this->data['auth_account']));
			$this->data['birthday']				= ($this->data['birthday'] === 0) ? '' : $this->data['birthday'];
		}

		$this->style['column_left_width']		= ($this->style['column_left_width'] != '0px' && $this->style['column_left_width'] != '0%') ? $this->style['column_left_width'] : 0;
		$this->style['column_right_width']		= ($this->style['column_right_width'] != '0px' && $this->style['column_right_width'] != '0%') ? $this->style['column_right_width'] : 0;
		$this->style['portal_width']			= ($this->style['portal_width'] != '0px' && $this->style['portal_width'] != '0%') ? $this->style['portal_width'] : 0;
		$this->style['logo_position']			= ($this->style['logo_position'] != '') ? $this->style['logo_position'] : 'center';

		if (!$this->lite_mode) {
			$this->tpl->set_template($this->style['template_path']);
		}
		//-----------------------------
		//END Style

		//Global Warning if somebody has overtaken user permissions
		if (!$this->lite_mode && isset($this->data['session_perm_id']) && $this->data['session_perm_id'] > 0){
			$username	= $this->pdh->get('user', 'username', array((int)$this->data['session_perm_id']));
			$message	= sprintf($this->lang('info_overtaken_permissions'), $username);
			$message	.= '<br /><b><a href="'.$this->server_path.'index.php'.$this->SID.'&mode=rstperms">'.$this->lang('link_overtaken_permissions')."</a></b>";
			$this->core->global_warning($message);
		}
	}

	public function pdl_html_format_language($log_entry) {
		return "Variable ".$log_entry['args'][0]." not found".((isset($log_entry['args'][3])) ? " in language ".$log_entry['args'][3] : "").".<br />File: ".$log_entry['args'][1]."<br />Line: ".$log_entry['args'][2];
	}
/* Unused language addition start */
	public function pdl_pt_format_unused_language($log_entry) {
		return serialize($log_entry['args']);
	}

	public function pdl_html_format_unused_language($log_entry) {
		$text = 'array('.count($log_entry['args'][0]).') {<br />';
		foreach($log_entry['args'][0] as $key => $val) {
			if(is_array($val)) continue;
			$text .= '&nbsp;&nbsp;&nbsp;&nbsp;["'.$key.'"] => "'.htmlspecialchars($val).'",<br />';
		}
		return $text.'}';
	}

	private function init_unused_lang() {
		if(count($this->unused) > 0) return true;
		$file = $this->pfh->FilePath('unused.lang', 'eqdkp');
		$data = unserialize(file_get_contents($file));
		if(is_array($data) && count($data) > 0) {
			$this->unused = $data;
			//check for deleted keys
			foreach($this->unused as $key => $val) {
				if(!isset($this->lang[$this->lang_name][$key])) unset($this->unused[$key]);
			}
		} elseif(DEBUG >= 4) {
			$this->unused = array();
			$this->read_folder($this->root_path);
		}
	}

	private function read_folder($folder){
		$folder = preg_replace('/\/$/', '', $folder);
		$ignore = array('.', '..', '.svn', '.htaccess', 'index.html', 'language');
		if ( $dir = opendir($folder) ){
			while ( $path = readdir($dir) ){
				if ( !in_array(basename($path), $ignore) && is_dir($folder . '/' . $path) ){
					$this->read_folder($folder . '/' . $path);
				}elseif ( !in_array(basename($path), $ignore) && is_file($folder . '/' . $path) ){
					$this->read_file_html($folder . '/' . $path);
					$this->read_file_php($folder . '/' . $path);
				}
			}
		}
	}

	private $used = array();
	private function read_file_html($path){
		// Check if its a php, tpl or html file
		if (!preg_match('/\.html$/', $path) || !preg_match('/\.tpl$/', $path)){ return; }
		$search = (count($this->unused) < 1) ? $this->lang[$this->lang_name] : $this->unused;
		$file = file_get_contents($path);
		preg_match_all('#\{L_([a-zA-Z0-9_]+)\}#', $file, $matches, PREG_SET_ORDER);
		if (count($matches) > 0 ){
			foreach ( $matches as $match ){
				if(isset($search[$match[1]])) $this->used[$match[1]] = $search[$match[1]];
			}
			$this->unused = array_diff_key($search, $this->used);
		}
	}

	private function read_file_php($path){
		// Check if its a php, tpl or html file
		if (!preg_match('/\.php$/', $path)){ return; }
		$search = (count($this->unused) < 1) ? $this->lang[$this->lang_name] : $this->unused;
		$file = file_get_contents($path);
		preg_match_all('/lang\(\'([\w]+)\'\)/', $file, $matches, PREG_SET_ORDER);
		if (count($matches) > 0 ){
			foreach ( $matches as $match ){
				if(isset($search[$match[1]])) $this->used[$match[1]] = $search[$match[1]];
			}
			$this->unused = array_diff_key($search, $this->used);
		}
	}

	private function lang_used($key) {
		if(isset($this->unused[$key])) unset($this->unused[$key]);
	}

	public function output_unused() {
		$this->pdl->log('unused_language', $this->unused);
	}
/* Unused language addition end */

	private function init_lang($lang_name) {
		if(!$lang_name) return false;
		if(isset($this->lang[$lang_name])) return true;
		$file_path = $this->root_path . 'language/' . $lang_name . '/';
		$tmp_lang = array();
		include($file_path . 'lang_main.php');
		if (is_array($lang)) {
			$tmp_lang = array_merge($tmp_lang, $lang);
			unset($lang);
		}
		if (defined('IN_ADMIN') || $this->config->get('debug') >= 4) {
			include($file_path . 'lang_admin.php');
			if (is_array($lang)) {
				$tmp_lang = array_merge($tmp_lang, $lang);
				unset($lang);
			}
		}
		if (defined('MAINTENANCE_MODE')) {
			include($file_path . 'lang_mmode.php');
			if (is_array($lang)) {
				$tmp_lang = array_merge($tmp_lang, $lang);
				unset($lang);
			}
		}
		
		$this->lang[$lang_name] = &$tmp_lang;
		if($this->lang_name == $lang_name && !$this->lite_mode) $this->init_unused_lang($lang_name);
	}

	private function init_plug_lang($lang_name) {
		$lang = array();
		foreach($this->plugs_to_load as $plug) {
			$file_path = $this->root_path.'plugins/'.$plug.'/language/'.$lang_name.'/lang_main.php';
			if(file_exists($file_path)) {
				include($file_path);
			}
			$this->loaded_plugs[$plug][$lang_name] = true;
		}
		if(count($this->plugs_to_load) > 0) $this->add_lang($lang_name, $lang);
	}

	private function missing_plug_lang($lang_name) {
		foreach($this->plugs_to_load as $code) {
			if(!isset($this->loaded_plugs[$code][$lang_name])) return true;
		}
		return false;
	}

	public function register_plug_language($plugin) {
		$this->plugs_to_load[] = $plugin;
	}

	public function add_lang($lang_name, $langtoadd) {
		if(!is_array($langtoadd)) return false;
		$this->lang[$lang_name] = (isset($this->lang[$lang_name]) && is_array($this->lang[$lang_name])) ? array_merge($this->lang[$lang_name], $langtoadd) : $langtoadd;
	}

	private function lang_error($key, $return_key, $warning=false, $error=true) {
		if($error) {
			$debug = debug_backtrace();
			if(!$warning) $this->pdl->log('language', $key, $debug[1]['file'], $debug[1]['line']);
			else $this->pdl->log('language', $key, $debug[1]['file'], $debug[1]['line'], $this->lang_name);
			unset($debug);
		}
		return ($return_key) ? $key : false;
	}

	public function lang($key, $return_key=false, $error=true, $lang=false, $error_key='') {
		if(is_array($key)) {
			$keys = $key;
			$key = array_shift($keys);
		}
		if(!$lang) {
			$cur_lang_name = $this->lang_name;
			if(!isset($this->lang[$cur_lang_name][$key])) {
				//check if plugin_lang initialized first
				if($this->missing_plug_lang($cur_lang_name)) $this->init_plug_lang($cur_lang_name);
				if(!isset($this->lang[$cur_lang_name][$key]) && $cur_lang_name != $this->config->get('default_lang')) {
					$this->init_lang($this->config->get('default_lang'));
					$cur_lang_name = $this->config->get('default_lang');
					$default_chosen = true;
				}
				if(!isset($this->lang[$cur_lang_name][$key]) && $this->missing_plug_lang($cur_lang_name)) $this->init_plug_lang($cur_lang_name);
				if(!isset($this->lang[$cur_lang_name][$key]) && $this->lang_name != 'english' &&  $this->config->get('default_lang') != 'english') {
					$this->init_lang('english');
					$cur_lang_name = 'english';
					$default_chosen = true;
				}
				if(!isset($this->lang[$cur_lang_name][$key]) && $this->missing_plug_lang($cur_lang_name)) $this->init_plug_lang($cur_lang_name);
			}
			$lang = $this->lang[$cur_lang_name];
		}
		if(!isset($lang[$key])) {
			return $this->lang_error($error_key.$key, $return_key, false, $error);
		}
		if(isset($default_chosen) && $error) {
			$this->lang_error($key, false, true);
		}
		if(isset($keys) && count($keys) > 0) return $this->lang($keys, $return_key, $error, $lang[$key], $error_key.$key.' -> ');
		$this->lang_used($key);
		return $lang[$key];
	}


	/**
	* Checks if a user has permission to do ($auth_value)
	*
	* @param $auth_value		Permission we want to check
	* @param $die						If they don't have permission, exit with message_die or just return false?
	* @param $user_id				If set, checks $user_id's permission instead of $this->data['user_id']
	* @param $groups				If Group-Permissions should be checked, too
	* @return bool
	*/
	public function check_auth($strAuthValue, $boolDie = true, $intUserID = 0, $boolGroups = true){
		if($intUserID == 0){
			$intUserID = $this->data['user_id'];
		}
		//Overtake user permissions
		if (isset($this->data['session_perm_id']) AND $this->data['session_perm_id'] > 0){
			$intUserID = $this->data['session_perm_id'];
		}

		$boolAuthResult = $this->acl->check_auth($strAuthValue, $intUserID, $boolGroups);

		if($boolAuthResult) {
			return true;
		} elseif ($boolDie) {
			$index = ( $this->lang('noauth_'.$strAuthValue) ) ? 'noauth_'.$strAuthValue : 'noauth';
			return message_die($this->lang($index), $this->lang('noauth_default_title'), 'access_denied', true);
		}
		return false;
	}

	/**
	* Checks if a user has all (AND) or one (OR) of the required permissions
	*
	* @param $arrAuths			Array of Permission we want to check
	* @param $strMode			AND or OR
	* @param $die				If they don't have permission, exit with message_die or just return false?
	* @param $user_id			If set, checks $user_id's permission instead of $this->data['user_id']
	* @param $groups			If Group-Permissions should be checked, too
	* @return bool
	*/
	public function check_auths($arrAuths, $mode = 'AND', $boolDie = true, $intUserID = 0, $boolGroups = true){
		if (is_array($arrAuths) && count($arrAuths) > 0){
			if (strtolower($mode) == 'and'){
				$intPerms = 0;
				foreach ($arrAuths as $auth){
					if ($this->check_auth($auth, $boolDie, $intUserID, $boolGroups)){
						$intPerms++;
					}
				}
				if ($intPerms === count($arrAuths)){
					return true;
				}
			} else {
				$blnPerm = false;
				foreach ($arrAuths as $auth){
					if ($this->check_auth($auth, false, $intUserID, $boolGroups)){
						return true;
					}
				}

				if ($boolDie){
					return message_die($this->lang('noauth'), $this->lang('noauth_default_title'), 'access_denied', true);
				}
			}

		}

		return false;
	}

	/**
	* Checks if a user is a member of the group
	*
	* @param $group_id		Group we want to check
	* @param $die					If the user is not member of the group, exit with message_die or just return false?
	* @param $user_id			If set, checks $user_id's permission instead of $this->data['user_id']
	* @return bool
	*/
	public function check_group($intGroupID, $boolDie = true, $intUserID = 0){
		if($intUserID == 0) $intUserID = $this->data['user_id'];
		if(!is_array($intGroupID)) $intGroupID = array($intGroupID);

		//Overtake user permissions
		if (isset($this->data['session_perm_id']) && $this->data['session_perm_id'] > 0){
			$intUserID = $this->data['session_perm_id'];
		}

		$boolAuthResult = array();
		foreach($intGroupID as $group_id) {
			$boolAuthResult[] = $this->acl->check_group($group_id, $intUserID);
		}

		if(in_array(true, $boolAuthResult, true)){
			return true;
		}else{
			return ($boolDie) ? message_die($this->lang('noauth'), $this->lang('noauth_default_title'), 'access_denied', true) : false;
		}
	}
	
	public function check_pageobject($strPageObject, $boolDie = true, $intUserID = 0){
		if($intUserID == 0) $intUserID = $this->data['user_id'];
		
		$blnResult = $this->pdh->get('articles', 'check_pageobject_permission', array($strPageObject, $intUserID));
		if($blnResult) return true;
		
		return ($boolDie) ? message_die($this->lang('noauth'), $this->lang('noauth_default_title'), 'access_denied', true) : false;
	}
	
	public function check_pageobjects($arrPageObjects, $mode = 'AND', $boolDie = true, $intUserID = 0){
		if (is_array($arrPageObjects) && count($arrPageObjects) > 0){
			if (strtolower($mode) == 'and'){
				$intPerms = 0;
				foreach ($arrPageObjects as $strPageObject){
					if ($this->check_pageobject($strPageObject, $boolDie, $intUserID)){
						$intPerms++;
					}
				}
				if ($intPerms === count($arrPageObjects)){
					return true;
				}
			} else {
				$blnPerm = false;
				foreach ($arrPageObjects as $strPageObject){
					if ($this->check_pageobject($strPageObject, false, $intUserID)){
						return true;
					}
				}
	
				if ($boolDie){
					return message_die($this->lang('noauth'), $this->lang('noauth_default_title'), 'access_denied', true);
				}
			}
	
		}
	
		return false;
	}


	public function updateAutologinKey($intUserID, $strAutologinKey){
		$objQuery = $this->db->prepare('UPDATE __users :p WHERE user_id=?')->set(array(
				'user_login_key' => $strAutologinKey,
			))->execute((int)$intUserID);
		
		return $objQuery;
	}

	/**
	* Function to abstract password encryption
	*
	* @param string $string String to encrypt
	* @param string $salt Salt value; not yet in use
	* @return string
	*/
	public function encrypt_password($strPassword, $strSalt = '', $strMethod=''){
		return $this->pw->hash($strPassword, $strSalt, $strMethod);
	}

	public function checkPassword($strPassword, $strStoredHash, $blnUseHash = false, $blnReturnHash = false){
		return $this->pw->checkPassword($strPassword, $strStoredHash, $blnUseHash, $blnReturnHash);
	}

	public function checkIfHashNeedsUpdate($strHash){
		return $this->pw->checkIfHashNeedsUpdate($strHash);
	}

	/**
	* Generate Salt
	*
	* @return string
	*/
	public function generate_salt(){
		return substr(md5(generateRandomBytes(55)), 0, 23);
	}
	
	
	/**
	 *	Generate User-Settings
	 */	
	public static function get_settingsdata($user_id=-1) {
		$settingsdata = array();
		
		$priv_wall_posts_read_array = array(
			'0'=>'user_priv_all',
			'1'=>'user_priv_user',
			'2'=>'user_priv_onlyme'
		);
		
		$priv_wall_posts_write_array = array(
			'1'=>'user_priv_user',
			'2'=>'user_priv_onlyme'
		);

		$priv_set_array = array(
			'0'=>'user_priv_all',
			'1'=>'user_priv_user',
			'2'=>'user_priv_admin'
		);

		$gender_array = array(
			'1'=> 'gender_m',
			'2'=> 'gender_f',
		);

		$root_path = registry::get_const('root_path');
		$cfile = $root_path.'core/country_states.php';
		if (file_exists($cfile)){
			include($cfile);
		}

		// Build language array
		if($dir = @opendir($root_path . 'language/')){
			while ( $file = @readdir($dir) ){
				if ((!is_file($root_path . 'language/' . $file)) && (!is_link($root_path . 'language/' . $file)) && valid_folder($file)){
					include($root_path.'language/'.$file.'/lang_main.php');
					$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
					$language_array[$file]					= $lang_name_tp;
					$locale_array[$lang['ISO_LANG_SHORT']]	= $lang_name_tp;
				}
			}
		}

		$style_array = array();
		foreach(register('pdh')->get('styles', 'styles', array(0, false)) as $styleid=>$row){
			$style_array[$styleid] = $row['style_name'];
		}
		
		// hack the birthday format, to be sure there is a 4 digit year in it
		$birthday_format = register('user')->style['date_notime_short'];
		if(stripos($birthday_format, 'y') === false) $birthday_format .= 'Y';
		$birthday_format = str_replace('y', 'Y', $birthday_format);
		
		$settingsdata = array(
			'registration_info'	=> array(
				'registration_info'	=> array(
					'username'	=> array(
						'type'		=> 'text',
						'lang'		=> 'username',
						'after_txt'	=> '<i class="fa fa-check fa-lg icon-color-green" id="tick_username" style="display: none;"></i><span id="error_username" class="error-message-red" style="display:none;"><i class="fa fa-exclamation-triangle fa-lg"></i> '.register('user')->lang('fv_username_alreadyuse').'</span>',
						'size'		=> 40,
						'required'	=> true,
					),
					'user_email' => array(
						'type'		=> 'text',
						'lang'		=> 'email_address',
						'size'		=> 40,
						'id'		=> 'useremail',
						'required'	=> true,
						'after_txt'	=> '<i class="fa fa-check fa-lg icon-color-green" id="tick_mail" style="display: none;"></i><span id="error_email" class="error-message-red" style="display:none;"><i class="fa fa-exclamation-triangle fa-lg"></i> '.register('user')->lang('fv_email_alreadyuse').'</span>',
						'pattern'	=> 'email',
					),
					'current_password'	=> array(
						'type'		=> 'password',
						'size'		=> 40,
						'id'		=> 'oldpassword',
						'required'	=> false,
						'pattern'	=> 'password',
					),
					'new_password' => array(
						'type'		=> 'password',
						'size'		=> 40,
						'id'		=> 'password1',
						'required'	=> false,
					),
					'confirm_password' => array(
						'type'		=> 'password',
						'size'		=> 40,
						'id'		=> 'password2',
						'required'	=> false,
						'equalto'	=> 'password1',
					),
				),
			),
			'profile'	=> array(
				'profile'	=> array(
					'gender' => array(
						'type'		=> 'radio',
						'tolang'	=> true,
						'options'	=> $gender_array,
					),
					'country' => array(
						'type'		=> 'dropdown',
						'options'	=> $country_array,
					),
					'birthday'	=> array(
						'type'			=> 'datepicker',
						'allow_empty'	=> true,
						'year_range'	=> '-80:+0',
						'change_fields' => true,
						'format'		=> $birthday_format
					),
				),
				'user_avatar' => array(
					'user_avatar_type' => array(
						'type'		=> 'radio',
						'tolang'	=> true,
						'options'	=> array(
							'0'	=> 'user_avatar_type_own',
							'1'	=> 'user_avatar_type_gravatar',
						),
						'default'	=> '0',
						'dependency'=> array(1 => array('user_gravatar_mail')),
					),
					'user_avatar'	=> array(
						'type'			=> 'imageuploader',
						'imgpath'		=> register('pfh')->FolderPath('users/'.$user_id,'files'),
						'returnFormat'	=> 'filename',
					),
					'user_gravatar_mail' => array(
						'type'	=> 'text',
						'size'	=> 40,
					),
				),
			),
			'privacy_options' => array(
				'user_priv' => array(
					'priv_bday'	=> array(
						'type'		=> 'radio',
						'default'	=> 0,
					),
					'priv_userprofile_age' => array(
							'type'		=> 'dropdown',
							'options'	=> $priv_set_array,
							'tolang'	=> true,
							'default'	=> 1,
					),
					'priv_userprofile_country' => array(
							'type'		=> 'dropdown',
							'options'	=> $priv_set_array,
							'tolang'	=> true,
							'default'	=> 1,
					),
				),
				'user_priv_contact' => array(
						'priv_no_boardemails'	=> array(
								'type'		=> 'radio',
								'default'	=> 0,
						),
						'priv_userprofile_email' => array(
							'type'		=> 'dropdown',
							'options'	=> $priv_set_array,
							'tolang'	=> true,
							'default'	=> 1,
						),
						
				),
				'user_wall' => array(
					'priv_wall_posts_read'	=> array(
						'type'		=> 'dropdown',
						'tolang'	=> true,
						'options'	=> $priv_wall_posts_read_array,
					),
					'priv_wall_posts_write'	=> array(
						'type'		=> 'dropdown',
						'tolang'	=> true,
						'options'	=> $priv_wall_posts_write_array,
					),
				),
			),
			
			'view_options'		=> array(
				'view_options'	=> array(
					'user_lang'	=> array(
						'type'	=> 'dropdown',
						'lang'	=> 'language',
						'options'	=> $language_array,
						'default'	=> register('config')->get('default_lang'),
					),
					'user_timezone'	=> array(
						'type'	=> 'dropdown',
						'lang'		=> 'user_timezones',
						'options'	=> register('time')->timezones,
						'default'	=> register('config')->get('timezone'),
					),
					'user_style'	=> array(
						'type'	=> 'dropdown',
						'lang'		=> 'style',
						'options'	=> $style_array,
						'text2'		=> ' (<a href="javascript:template_preview()">'.register('user')->lang('preview').'</a>)',
						'default'	=> register('config')->get('default_style'),
					),
					'user_alimit'	=> array(
						'type'		=> 'spinner',
						'lang'		=> 'adjustments_per_page',
						'size'		=> 5,
						'step'		=> 10,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_alimit')) ? register('config')->get('default_alimit') : 100,
					),
					'user_climit'	=> array(
						'type'	=> 'spinner',
						'lang'	=> 'characters_per_page',
						'size'	=> 5,
						'step' => 10,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_climit')) ? register('config')->get('default_climit') : 100,
					),
					'user_elimit'	=> array(
						'type'	=> 'spinner',
						'lang'	=> 'events_per_page',
						'size'	=> 5,
						'step' => 10,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_elimit')) ? register('config')->get('default_elimit') : 100,
					),
					'user_ilimit'	=> array(
						'type'	=> 'spinner',
						'lang'	=> 'items_per_page',
						'size'	=> 5,
						'step' => 10,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_ilimit')) ? register('config')->get('default_ilimit') : 100,
					),
					'user_rlimit'	=> array(
						'type'	=> 'spinner',
						'lang'	=> 'raids_per_page',
						'size'	=> 5,
						'step' => 10,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_rlimit')) ? register('config')->get('default_rlimit') : 100,
					),
					/*
					'user_nlimit'	=> array(
						'type'	=> 'spinner',
						'lang'	=> 'news_per_page',
						'size'	=> 5,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_nlimit')) ? register('config')->get('default_nlimit') : 10,
					),
					*/
					'user_date_time'	=> array(
						'type'	=> 'text',
						'size'	=> 40,
						'help'	=> 'user_sett_date_note',
						'default'	=> (register('config')->get('default_date_time')) ? register('config')->get('default_date_time') : $this->user->lang('style_date_time'),
					),
					'user_date_short'	=> array(
						'type'	=> 'text',
						'size'	=> 40,
						'help'	=> 'user_sett_date_note',
						'default'	=> (register('config')->get('default_date_short')) ? register('config')->get('default_date_short') : $this->user->lang('style_date_short'),
					),
					'user_date_long'	=> array(
						'type'	=> 'text',
						'size'	=> 40,
						'help'	=> 'user_sett_date_note',
						'default'	=> (register('config')->get('default_date_long')) ? register('config')->get('default_date_long') : $this->user->lang('style_date_long'),
					),
				),
			),
			'notifications' => array(
				'notifications' => array(
					'info' => true,
				),
			),
		);
		
		//Contact Fields
		$arrContactFields = register('pdh')->get('user_profilefields', 'contact_fields');
		if (count($arrContactFields)) $settingsdata['profile']['user_contact'] = $arrContactFields;
		
		//Normal Profile Fields
		$arrProfileFields = register('pdh')->get('user_profilefields', 'usersettings_fields');
		foreach($arrProfileFields as $key => $val){
			$settingsdata['profile']['profile'][$key] = $val;
		}
		
		//Privacy Options
		foreach($arrContactFields as $key => $val){
			$settingsdata['privacy_options']['user_priv_contact']['priv_'.$key] = array(
				'type'		=> 'dropdown',
				'options'	=> $priv_set_array,
				'tolang'	=> true,
				'lang'		=> $val['lang'],
				'default'	=> 1,
			);
		}
		foreach($arrProfileFields as $key => $val){
			$settingsdata['privacy_options']['user_priv']['priv_'.$key] = array(
					'type'		=> 'dropdown',
					'options'	=> $priv_set_array,
					'tolang'	=> true,
					'lang'		=> $val['lang'],
					'default'	=> 1,
			);
		}
		
		//Notifications
		$arrNotificationTypes = register('pdh')->get('notification_types', 'id_list');
		foreach($arrNotificationTypes as $strNotificationType){
			if ($strNotificationType === 'comment_new_article'){
				$arrCategoryIDs = register('pdh')->sort(register('pdh')->get('article_categories', 'id_list', array()), 'articles', 'sort_id', 'asc');
				$arrCategories = array();
				foreach($arrCategoryIDs as $caid){
					$arrCategories[$caid] = register('pdh')->get('article_categories', 'name_prefix', array($caid)).register('pdh')->get('article_categories', 'name', array($caid));
				}
				
				$settingsdata['notifications']['notifications']['ntfy_'.$strNotificationType] = array(
						'type'		=> 'multiselect',
						'options'	=> $arrCategories,
						'default'	=> array_keys($arrCategories),
				);
			} else {
				$settingsdata['notifications']['notifications']['ntfy_'.$strNotificationType] = array(
						'type'		=> 'radio',
						'default'	=> register('pdh')->get('notification_types', 'default', array($strNotificationType)),
				);
			}
		}

		return $settingsdata;
	}

	public function getAvailableLanguages($blnWithIsoShort=true, $blnWithIcon=false){
		$root_path = registry::get_const('root_path');
		$language_array = array();
		$icon = "";
		// Build language array
		if($dir = @opendir($root_path . 'language/')){
			while ( $file = @readdir($dir) ){
				if ((!is_file($root_path . 'language/' . $file)) && (!is_link($root_path . 'language/' . $file)) && valid_folder($file)){
					include($root_path.'language/'.$file.'/lang_main.php');
					if(isset($lang['ISO_LANG_SHORT'])){
						list($pre, $post) = explode('_', $lang['ISO_LANG_SHORT']);
						if($pre != "" && is_file($root_path.'images/flags/'.$pre.'.svg')){
							$icon = '<img src="'.registry::get_const('server_path').'images/flags/'.$pre.'.svg" class="icon icon-language absmiddle" title="'.(($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'] : ucfirst($file)).'"/> <span>';
						}
					}
					
					$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].(($blnWithIsoShort) ? ' ('.$lang['ISO_LANG_SHORT'].')' : '') : ucfirst($file));
					$language_array[$file] = (($blnWithIcon) ? $icon : '').$lang_name_tp.(($blnWithIcon) ? '</span>' : '');
				}
			}
		}
		return $language_array;
	}
	
	//Should be used for resolve multilang serialized array to display the value for the user in the right language
	public function multilangValue($strRawContent){
		$arrValues = @unserialize($strRawContent);
		if(!$arrValues) return $strRawContent;
		$strDefLang = $this->config->get('default_lang');
		if(isset($arrValues[$this->lang_name]) && strlen($arrValues[$this->lang_name])){
			return $arrValues[$this->lang_name];
		} elseif(isset($arrValues[$strDefLang])){
			return $arrValues[$strDefLang];
		}
		return "";
	}

	public function __destruct() {
		if(is_array($this->unused) && count($this->unused) > 0) $this->pfh->putContent($this->pfh->FilePath('unused.lang', 'eqdkp'), serialize($this->unused));
		parent::__destruct();
	}
}
?>