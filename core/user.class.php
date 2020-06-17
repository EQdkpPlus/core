<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
	//Without: auth_account, custom_fields, plugin_settings, privacy_settings, notifications
	public static $normalUserTableFields = array('user_id', 'username', 'user_password', 'user_email', 'user_login_key', 'user_rlimit', 'user_date_time',
			'user_date_short', 'user_date_long', 'user_style', 'user_lang', 'user_timezone', 'user_email_confirmkey', 'user_lastvisit', 'user_lastpage',
			'user_registered', 'user_active', 'user_email_confirmed', 'rules', 'failed_login_attempts', 'exchange_key',
			'hide_nochar_info', 'awaymode_enabled', 'awaymode_startdate', 'awaymode_enddate', 'awaymode_note', 'user_temp_email',
	);

	private $lang			= array();		// Loaded language pack
	public $style			= array();		// Style data
	public $lang_name		= '';			// Pack name (ie 'english')
	public $objLanguage		= null;

	/**
	* Constructor
	*/
	public function __construct(){
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

	public function deriveKeyFromExchangekey($userID, $strDerivedType){
		$strExchangeKey = $this->pdh->get('user', 'exchange_key', array($userID));
		$strDerivedKey = hash("sha256", md5($strExchangeKey).md5($strDerivedType));
		return $strDerivedKey;
	}

	public function getUserIDfromDerivedExchangekey($strDerivedKey, $strDerivedType){
		$arrUserList = $this->pdh->get('user', 'id_list', array(false));
		foreach($arrUserList as $intUserID){
			$strExchangeKey = $this->pdh->get('user', 'exchange_key', array($intUserID));
			$strUserDerivedKey = hash("sha256", md5($strExchangeKey).md5($strDerivedType));
			if($strUserDerivedKey === $strDerivedKey) return $intUserID;
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
		if(!isset($this->data['session_vars'])) { $this->data['session_vars'] = '';}
		$this->data['session_vars'] = (strlen($this->data['session_vars']) && is_serialized($this->data['session_vars'])) ? unserialize_noclasses($this->data['session_vars']) : array();

		//Set Session Vars
		if($strLanguage != ""){
			$this->setSessionVar("lang", substr($strLanguage, 0, 20));
		}
		if($intStyleID > 0){
			$this->setSessionVar("style", $intStyleID);
		}

		//START Language
		//-----------------------------
		if ($this->data['user_id'] == ANONYMOUS) {
			$strLanguage = (isset($this->data['session_vars']['lang']) && strlen($this->data['session_vars']['lang'])) ? $this->data['session_vars']['lang'] : $this->env->get_browser_language();
			$this->lang_name = ( $strLanguage != '' && file_exists($this->root_path . 'language/' . $strLanguage) ) ? $strLanguage : $this->config->get('default_lang');
			$this->data['user_lang'] = $this->lang_name;
		} else {
			$this->lang_name = (!empty($this->data['user_lang'])) ? $this->data['user_lang'] : $this->config->get('default_lang');
			$this->lang_name = ( $strLanguage != '' && file_exists($this->root_path . 'language/' . $strLanguage) ) ? $strLanguage : $this->lang_name;
			$this->data['user_lang'] = $this->lang_name;
		}

		$this->objLanguage = register('language', array($this->lang_name));

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

			$this->data['privacy_settings'] 	= ($this->data['privacy_settings'] && unserialize_noclasses($this->data['privacy_settings'])) ? unserialize_noclasses($this->data['privacy_settings']) : array();
			$this->data['custom_fields'] 		= ($this->data['custom_fields'] && unserialize_noclasses($this->data['custom_fields'])) ? unserialize_noclasses($this->data['custom_fields']) : array();
			$this->data['plugin_settings'] 		= ($this->data['plugin_settings'] && unserialize_noclasses($this->data['plugin_settings'])) ? unserialize_noclasses($this->data['plugin_settings']) : array();
			$this->data['notification_settings'] = ($this->data['notifications'] && unserialize_noclasses($this->data['notifications'])) ? unserialize_noclasses($this->data['notifications']) : array();

			if(strpos($this->data['user_password'], ':') !== false){
				list($this->data['user_password_clean'], $this->data['user_salt']) = explode(':', $this->data['user_password']);
			} else {
				$this->data['user_password_clean'] = $this->data['user_password'];
			}
			
			$this->data['user_email']			= register('encrypt')->decrypt($this->data['user_email']);
			$this->data['auth_account'] 		= @unserialize_noclasses(register('encrypt')->decrypt($this->data['auth_account']));
			$this->data['birthday']				= (!isset($this->data['birthday']) || $this->data['birthday'] === 0) ? '' : $this->data['birthday'];
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
			$username	= $this->pdh->get('user', 'name', array((int)$this->data['session_perm_id']));
			$message	= sprintf($this->lang('info_overtaken_permissions'), $username);
			$message	.= '<br /><b><a href="'.$this->server_path.'index.php'.$this->SID.'&mode=rstperms">'.$this->lang('link_overtaken_permissions')."</a></b>";
			$this->core->global_warning($message);
		}
	}

	public function lang($key, $return_key=false, $error=true, $lang=false, $error_key=''){
		if(!is_object($this->objLanguage)){
			$this->objLanguage = register('language');
		}

		return $this->objLanguage->get($this->lang_name, $key, $return_key, $error, $lang, $error_key);
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
		if((int)$intUserID === 0){
			$intUserID = $this->data['user_id'];
		}
		//Overtake user permissions
		if (isset($this->data['session_perm_id']) AND $this->data['session_perm_id'] > 0){
			$intUserID = $this->data['session_perm_id'];
		}

		if(strpos($strAuthValue, 'po_') === 0){
			$boolAuthResult = $this->check_pageobject(substr($strAuthValue, 3), false, $intUserID);
		} else {
			$boolAuthResult = $this->acl->check_auth($strAuthValue, $intUserID, $boolGroups);
		}

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
			if (strtolower($mode) === 'and'){
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
		if((int)$intUserID === 0) $intUserID = $this->data['user_id'];
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
		if((int)$intUserID === 0) $intUserID = $this->data['user_id'];

		$blnResult = $this->pdh->get('articles', 'check_pageobject_permission', array($strPageObject, $intUserID));
		if($blnResult) return true;

		return ($boolDie) ? message_die($this->lang('noauth'), $this->lang('noauth_default_title'), 'access_denied', true) : false;
	}

	public function check_pageobjects($arrPageObjects, $mode = 'AND', $boolDie = true, $intUserID = 0){
		if (is_array($arrPageObjects) && count($arrPageObjects) > 0){
			if (strtolower($mode) === 'and'){
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

	public function fireHooks(){
		if(isset($this->user->data['hooks'])){
			foreach($this->user->data['hooks'] as $key => $arrHook){
				$this->hooks->process($arrHook['id'], array($arrHook['data']));
				unset($this->user->data['hooks'][$key]);
			}
			unset($this->user->data['hooks']);
		}
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
	public function encrypt_password($strPassword, $strMethod=''){
		return $this->pw->hash($strPassword, $strMethod);
	}

	public function checkPassword($strPassword, $strStoredHash){
		return $this->pw->checkPassword($strPassword, $strStoredHash);
	}

	public function checkIfHashNeedsUpdate($strHash){
		return $this->pw->hashNeedsUpdate($strHash);
	}

	/**
	* Generate Salt
	*
	* @return string
	*/
	public function generate_salt(){
		$this->pdl->deprecated('User::generate_salt()');
		return substr(md5(generateRandomBytes(55)), 0, 23);
	}


	/**
	 *	Generate User-Settings
	 */
	public static function get_settingsdata($user_id=-1) {
		$settingsdata	= array();
		$root_path		= registry::get_const('root_path');

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
						'pattern'	=> '',
					),
					'new_password' => array(
						'type'		=> 'password',
						'size'		=> 40,
						'id'		=> 'password1',
						'required'	=> false,
						'after_txt' => '<div id="complexity" class="default"></div>',
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
				),
				'user_avatar' => array(
					'user_avatar_type' => array(
						'type'		=> 'radio',
						'tolang'	=> true,
						'options'	=> array(
						),
						'default'	=> 'eqdkp',
						//'dependency'=> array(1 => array('user_gravatar_mail')),
					),
					'user_avatar'	=> array(
						'type'			=> 'imageuploader',
						'imgpath'		=> register('pfh')->FolderPath('users/'.$user_id,'files'),
						'returnFormat'	=> 'filename',
					),
				),
			),
			'privacy_options' => array(
				'user_priv' => array(
					'priv_userprofile_age' => array(
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
					'user_rlimit'	=> array(
						'type'	=> 'spinner',
						'lang'	=> 'tableentries_per_page',
						'size'	=> 5,
						'step' => 10,
						'min'		=> 10,
						'required'	=> true,
						'default'	=> (register('config')->get('default_rlimit')) ? register('config')->get('default_rlimit') : 100,
					),
					'user_date_time'	=> array(
						'type'	=> 'text',
						'size'	=> 40,
						'help'	=> 'user_sett_date_note',
						'default'	=> (register('config')->get('default_date_time')) ? register('config')->get('default_date_time') : register('user')->lang('style_date_time'),
					),
					'user_date_short'	=> array(
						'type'	=> 'text',
						'size'	=> 40,
						'help'	=> 'user_sett_date_note',
						'default'	=> (register('config')->get('default_date_short')) ? register('config')->get('default_date_short') : register('user')->lang('style_date_short'),
					),
					'user_date_long'	=> array(
						'type'	=> 'text',
						'size'	=> 40,
						'help'	=> 'user_sett_date_note',
						'default'	=> (register('config')->get('default_date_long')) ? register('config')->get('default_date_long') : register('user')->lang('style_date_long'),
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
		$arrNotificationMethods = register('ntfy')->getAvailableNotificationMethods();
		array_unshift($arrNotificationMethods, register('user')->lang('notification_type_none'), register('user')->lang('notification_type_eqdkp'));
		$arrNotificationSettings = register('pdh')->get('user', 'notification_settings', array($user_id));

		foreach($arrNotificationTypes as $strNotificationType){
			$strCategory = register('pdh')->get('notification_types', 'category', array($strNotificationType));
			if ($strNotificationType === 'comment_new_article'){
				$arrCategoryIDs = register('pdh')->sort(register('pdh')->get('article_categories', 'id_list', array()), 'articles', 'sort_id', 'asc');
				$arrCategories = array();
				foreach($arrCategoryIDs as $caid){
					$arrCategories[$caid] = register('pdh')->get('article_categories', 'name_prefix', array($caid)).register('pdh')->get('article_categories', 'name', array($caid));
				}

				$settingsdata['notifications']['notifications_'.$strCategory]['ntfy_'.$strNotificationType] = array(
						'type'		=> 'dropdown',
						'options'	=> $arrNotificationMethods,
						'text_after'=> (new hmultiselect('ntfy_comment_new_article_categories', array('options' => $arrCategories, 'default'	=> array_keys($arrCategories), 'value' => $arrNotificationSettings['ntfy_comment_new_article_categories'])))->output().'<br />',
				);
			} else {
				$settingsdata['notifications']['notifications_'.$strCategory]['ntfy_'.$strNotificationType] = array(
						'type'		=> 'dropdown',
						'options'	=> $arrNotificationMethods,
						'default'	=> (string)register('pdh')->get('notification_types', 'default', array($strNotificationType)),
				);
			}
		}

		$arrNotificationUsersettings = register('ntfy')->getNotificationMethodsUserSettings();

		if(count($arrNotificationUsersettings)){
			$settingsdata['notifications']['notification_settings'] = $arrNotificationUsersettings;
		}

		// calendar settings
		$settingsdata['calendar'] = array(
			'calendar_awaymode_settings' => array(
				'awaymode_enabled'	=> array(
					'type'		=> 'radio',
					'default'	=> 0,
				),
				'awaymode_startdate'	=> array(
					'type'			=> 'datepicker',
					'allow_empty'	=> false,
					'default'		=> register('time')->time,
					'onclose' 		=> ' $( "#awaymode_enddate" ).datepicker( "option", "minDate", selectedDate );'
				),
				'awaymode_enddate'	=> array(
					'type'			=> 'datepicker',
					'allow_empty'	=> false,
					'year_range'	=> '-0:+2',
					'default'		=> register('time')->time,
				),
				'awaymode_note'	=> array(
					'type'		=> 'textarea',
					'rows'		=> 4,
					'cols'		=> 50
				),
			),
		);

		//Avatar Providers
		$arrAllowed = register('config')->get('avatar_allowed');
		$arrAvatarProviders = register('user')->getAvatarProviders(true);
		$arrAvatarDependencies = $arrAvatarOptions = array();
		foreach ($arrAvatarProviders as $key => $val){
			if(!in_array($key, $arrAllowed)) continue;
			$arrAvatarOptions[$key] = $val['name'];
			if(isset($val['settings'])){
				foreach($val['settings'] as $key2 => $val2){
					$settingsdata['profile']['user_avatar'][$key2] = $val2;
					$arrAvatarDependencies[$key][] = $key2;
				}
			}
		}

		$settingsdata['profile']['user_avatar']['user_avatar_type']['options'] = $arrAvatarOptions;
		$settingsdata['profile']['user_avatar']['user_avatar_type']['dependency'] = $arrAvatarDependencies;

		if(register('config')->get('disable_guild_features')) unset($settingsdata['calendar']);

		return $settingsdata;
	}

	public function getAvailableLanguages($blnWithIsoShort=true, $blnWithIcon=false, $blnIsoAsKey=false){
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
							$name = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'] : ucfirst($file));
							$icon = '<img src="'.registry::get_const('server_path').'images/flags/'.$pre.'.svg" class="icon icon-language absmiddle" alt="'.$name.'" title="'.$name.'"/> <span>';
						}
					}

					$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].(($blnWithIsoShort) ? ' ('.$lang['ISO_LANG_SHORT'].')' : '') : ucfirst($file));
					$key = ($blnIsoAsKey) ? $lang['ISO_LANG_SHORT'] : $file;

					$language_array[$key] = (($blnWithIcon) ? $icon : '').$lang_name_tp.(($blnWithIcon) ? '</span>' : '');
				}
			}
		}
		return $language_array;
	}

	//Should be used for resolve multilang serialized array to display the value for the user in the right language
	public function multilangValue($strRawContent){
		if(is_serialized($strRawContent)){
			$arrValues = @unserialize_noclasses($strRawContent);
		} else $arrValues = false;
		if(!$arrValues) return $strRawContent;
		$strDefLang = $this->config->get('default_lang');
		if(isset($arrValues[$this->lang_name]) && strlen($arrValues[$this->lang_name])){
			return $arrValues[$this->lang_name];
		} elseif(isset($arrValues[$strDefLang])){
			return $arrValues[$strDefLang];
		}
		return "";
	}

	/**
	 * Returns all available Avatar Providers, including their Settings
	 */
	public function getAvatarProviders($blnForUsersettings=false){
		$arrAvatarProviders = array(
			'eqdkp' => array(
				'name' => ($blnForUsersettings) ? $this->user->lang('user_avatar_type_own') : 'EQdkp Plus',
			),
			'gravatar' => array(
				'name' 		=> $this->user->lang('user_avatar_type_gravatar'),
				'settings'	=> array(
						'user_gravatar_mail' => array(
								'type'	=> 'text',
								'size'	=> 40,
						),
				),
			),
		);

		if($this->hooks->isRegistered('avatar_provider')){
			$arrHookProviders = $this->hooks->process('avatar_provider', array('for_usersettings' => $blnForUsersettings), false);
			foreach($arrHookProviders as $val){
				if(is_array($val)) $arrAvatarProviders = array_merge($arrAvatarProviders, $val);
			}
		}

		return $arrAvatarProviders;
	}

	public function registerUserFromAuthProvider($arrAccountDetails, $strAuthMethod){

		$auth_account = $arrAccountDetails['auth_account'];

		//Admin activation
		if ($this->config->get('account_activation') == 2){
			return $arrAccountDetails;
		}

		//Check Auth Account
		if ($arrAccountDetails['auth_account'] == "" || !$this->pdh->get('user', 'check_auth_account', array($arrAccountDetails['auth_account'], $strAuthMethod))){
			return $arrAccountDetails;
		}

		//Check Email address
		if($arrAccountDetails['user_email'] == "" || $this->pdh->get('user', 'check_email', array($arrAccountDetails['user_email'])) == 'false'){
			return $arrAccountDetails;
		}

		//Create Username
		$strUsername = ($arrAccountDetails['username'] != "") ? $arrAccountDetails['username'] : ucfirst($strAuthMethod).'User'.random_integer(100, 999);

		//Check Username and create a new one
		if ($this->pdh->get('user', 'check_username', array($strUsername)) == 'false'){
			$strUsername = $strUsername.random_integer(100, 999);
		}
		if ($this->pdh->get('user', 'check_username', array($strUsername)) == 'false'){
			return $arrAccountDetails;
		}

		//Register User (random credentials)
		$strPwdHash = $this->user->encrypt_password(random_string(32));

		$intUserID = $this->pdh->put('user', 'insert_user_bridge', array(
				$strUsername, $strPwdHash, $arrAccountDetails['user_email']
		));

		if(!$intUserID) return $arrAccountDetails;

		//Add the auth account
		$this->pdh->put('user', 'add_authaccount', array($intUserID, $auth_account, $strAuthMethod));

		//Add an avatar
		if(isset($arrAccountDetails['avatar']) && $arrAccountDetails['avatar'] != ""){
			include_once $this->root_path.'core/avatar.class.php';
			$strAvatar = registry::register('avatar')->downloadExternalAvatar($intUserID, $arrAccountDetails['avatar']);
			if($strAvatar != ""){
				$this->pdh->put('user', 'add_custom_avatar', array($intUserID, $strAvatar));
			}
		}


		//Send Email with username
		$email_template		= 'register_activation_none';
		$email_subject		= $this->user->lang('email_subject_activation_none');

		$objMailer = register('MyMailer');

		$objMailer->Set_Language($this->user->lang_name);

		$bodyvars = array(
				'USERNAME'		=> stripslashes($strUsername),
				'GUILDTAG'		=> $this->config->get('guildtag'),
		);

		if(!$objMailer->SendMailFromAdmin($arrAccountDetails['user_email'], $email_subject, $email_template.'.html', $bodyvars)){

		}

		$arrAccountDetails['user_id'] = $intUserID;
		return $arrAccountDetails;
	}



	public function __destruct() {
		if(is_array($this->unused) && count($this->unused) > 0) $this->pfh->putContent($this->pfh->FilePath('unused.lang', 'eqdkp'), serialize($this->unused));
		parent::__destruct();
	}
}
