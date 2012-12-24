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

class user_core extends gen_class {
	public static $shortcuts = array('pdl', 'config', 'pfh', 'db', 'tpl', 'time', 'in', 'acl', 'config', 'core', 'bridge', 'env', 'pw', 'pdh');
	public static $dependencies = array('pfh');

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

		$sql =	"SELECT user_id FROM __users
				WHERE exchange_key = '".$this->db->escape($strKey)."'";
		$result	= $this->db->query($sql);
		$data	= $this->db->fetch_record($result);

		$this->db->free_result($result);

		if(isset($data['user_id'])){
			return (int)$data['user_id'];
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
		//START Language
		//-----------------------------
		if ($this->data['user_id'] == ANONYMOUS) {
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
		if (intval($intStyleID) > 0){
			$intUserStyleID = (int)$intStyleID;
		} elseif ($this->data['user_id'] == ANONYMOUS){
			$intUserStyleID = $this->config->get('default_style');
		} else {
			$intUserStyleID = (isset($this->data['user_style']) && !$this->config->get('default_style_overwrite')) ? $this->data['user_style'] : $this->config->get('default_style');
		}
		$intStyleID = (is_numeric($intUserStyleID) && $intUserStyleID > 0) ? $intUserStyleID : $this->db->query_first("SELECT style_id FROM __styles;");

		//Get Style-Information
		$result = $this->db->query("SELECT * FROM __styles WHERE style_id='".$this->db->escape($intStyleID)."'");
		$this->style = $this->db->fetch_record($result);
		//No Style-Information -> Fallback to the default style
		if ( !$this->style && ($intStyleID != $this->config->get('default_style'))) {
			$result = $this->db->query("SELECT * FROM __styles WHERE style_id='".$this->db->escape($this->config->get('default_style'))."'");
			$this->style = $this->db->fetch_record($result);
		}
		//Now set the Style-Settings
		if(empty($this->data['user_timezone']) || $this->data['user_timezone'] == '') $this->data['user_timezone'] = $this->config->get('timezone');

		if ($this->data['user_id'] == ANONYMOUS){
			$this->data['user_alimit']				= $this->config->get('default_alimit');
			$this->data['user_elimit']				= $this->config->get('default_elimit');
			$this->data['user_ilimit']				= $this->config->get('default_ilimit');
			$this->data['user_nlimit']				= $this->config->get('default_nlimit');
			$this->data['user_rlimit']				= $this->config->get('default_rlimit');
			$this->style['date_notime_long']	= ($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->lang('style_date_long');
			$this->style['date_notime_short']	= ($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->lang('style_date_short');
			$this->style['time']							= ($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->lang('style_time');
			$this->style['date_time']					= $this->style['date_notime_short'].' '.$this->style['time'];
			$this->style['date']							= 'l, '.$this->style['date_notime_long'];
			$this->style['date_short']				= 'D '.$this->style['date_notime_short'].' '.$this->style['time'];
		} else {
			$this->style['date_notime_long']	= ($this->data['user_date_long']) ? $this->data['user_date_long'] : (($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->lang('style_date_long'));
			$this->style['date_notime_short']	= ($this->data['user_date_short']) ? $this->data['user_date_short'] : (($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->lang('style_date_short'));
			$this->style['time']							= ($this->data['user_date_time']) ? $this->data['user_date_time'] : (($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->lang('style_time'));
			$this->style['date_time']					= $this->style['date_notime_short'].' '.$this->style['time'];
			$this->style['date']							= 'l, '.$this->style['date_notime_long'];
			$this->style['date_short']				= 'D '.$this->style['date_notime_short'].' '.$this->style['time'];

			$this->data['privacy_settings'] 	= ($this->data['privacy_settings'] && unserialize($this->data['privacy_settings'])) ? unserialize($this->data['privacy_settings']) : array();
			$this->data['custom_fields'] 			= ($this->data['custom_fields'] && unserialize($this->data['custom_fields'])) ? unserialize($this->data['custom_fields']) : array();
			$this->data['plugin_settings'] 		= unserialize($this->data['plugin_settings']);
			list($this->data['user_password_clean'], $this->data['user_salt']) = explode(':', $this->data['user_password']);
			$this->data['user_email'] = register('encrypt')->decrypt($this->data['user_email']);
			$this->data['auth_account'] = @unserialize(register('encrypt')->decrypt($this->data['auth_account']));
		}

		$this->style['column_left_width'] = ($this->style['column_left_width'] != '0px' && $this->style['column_left_width'] != '0%') ? $this->style['column_left_width'] : 0;
		$this->style['column_right_width'] = ($this->style['column_right_width'] != '0px' && $this->style['column_right_width'] != '0%') ? $this->style['column_right_width'] : 0;
		$this->style['portal_width'] = ($this->style['portal_width'] != '0px' && $this->style['portal_width'] != '0%') ? $this->style['portal_width'] : 0;
		$this->style['logo_position'] = ($this->style['logo_position'] != '') ? $this->style['logo_position'] : 'center';

		if (!$this->lite_mode) {
			#if(empty($this->style['template_path'])) $this->style['template_path'] = $this->db->query_first("SELECT template_path FROM __styles;");
			$this->tpl->set_template($this->style['template_path']);
		}
		//-----------------------------
		//END Style

		//Global Warning if somebody has overtaken user permissions
		if (!$this->lite_mode && isset($this->data['session_perm_id']) && $this->data['session_perm_id'] > 0){
			$query = $this->db->query("SELECT username FROM __users WHERE user_id = '".$this->db->escape($this->data['session_perm_id'])."'");
			$arrResult = $this->db->fetch_record($query);
			$message = sprintf($this->lang('info_overtaken_permissions'), $arrResult['username']);
			$message .= '<br /><b><a href="'.$this->root_path.'index.php'.$this->SID.'&mode=rstperms">'.$this->lang('link_overtaken_permissions')."</a></b>";
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
		include($file_path . 'lang_main.php');
		if (defined('IN_ADMIN') || $this->config->get('pk_debug') >= 4) {
			include($file_path . 'lang_admin.php');
		}
		if (defined('MAINTENANCE_MODE')) {
			include($file_path . 'lang_mmode.php');
		}
		$this->lang[$lang_name] = &$lang;
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
		$this->lang[$lang_name] = (is_array($this->lang[$lang_name])) ? array_merge($this->lang[$lang_name], $langtoadd) : $langtoadd;
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

	/**
	* Checks if the eqdkp runs in the easy mode...
	*
	* @param $die					If they don't have permission, exit with message_die or just return false?
	* @return bool
	*/
	public function check_hostmode($boolDie = true){
		if(!$this->HMODE){
			return true;
		}else{
			return ($boolDie) ? message_die($this->lang('noauth_hostmode'), $this->lang('noauth_default_title'), 'access_denied', true) : false;
		}
	}


	public function updateAutologinKey($intUserID, $strAutologinKey){
		$query = $this->db->query('UPDATE __users SET :params WHERE user_id=?', array(
				'user_login_key' => $strAutologinKey,
			), $intUserID);
		return $query;
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
		return substr(md5(rand().uniqid('', true).rand()), 0, 23);
	}

	public function generate_apikey($strPassword, $strSalt){
		$strRandom = md5(rand().uniqid('', true).rand());
		$objCrypt = register('encrypt', array($this->pw->prehash($strPassword, $strSalt)));
		$strEncrypted = $objCrypt->encrypt($strRandom);
		return $strRandom.':'.$strEncrypted;
	}


	public function __destruct() {
		if(is_array($this->unused) && count($this->unused) > 0) $this->pfh->putContent($this->pfh->FilePath('unused.lang', 'eqdkp'), serialize($this->unused));
		parent::__destruct();
	}
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_user_core', user_core::$shortcuts);
	registry::add_const('dep_user_core', user_core::$dependencies);
}
?>