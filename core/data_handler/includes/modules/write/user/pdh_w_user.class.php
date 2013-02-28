<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2007
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_user')) {
	class pdh_w_user extends pdh_w_generic {
		public static function __shortcuts() {
			$shortcuts = array('pdh', 'db', 'in', 'user', 'config', 'pfh', 'html', 'pm', 'jquery', 'time', 'core', 'crypt' => 'encrypt');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public function __construct() {
			parent::__construct();
		}

		public function insert_user($arrData, $logging = true, $toDefaultGroup = true){
			$arrData = $this->set_defaults($arrData);
			$arrData['user_registered'] = $this->time->time;
			
			$result = $this->db->query("INSERT INTO __users :params", $arrData);

			if ( !($result) ) {
				return false;
			}

			$user_id = $this->db->insert_id();

			if ($logging) {
				// Logging
				$log_action = array(
					'{L_USER}'	=> $arrData['username'],
				);
				$this->log_insert('action_user_added', $log_action);
			}

			//Put him to the default group
			if ($this->pdh->get('user_groups', 'standard_group') && $toDefaultGroup){
				$this->pdh->put('user_groups_users', 'add_user_to_group', array($user_id, $this->pdh->get('user_groups', 'standard_group'), 1));
			}
			$this->pdh->enqueue_hook('user');

			return $user_id;
		}

		public function register_user($arrData, $user_active = 1, $user_key = '', $rules = false, $strLoginMethod = false) {
			$new_salt = $this->user->generate_salt();
			$new_password = $this->user->encrypt_password($arrData['user_password1'], $new_salt).':'.$new_salt;
			$strApiKey = $this->user->generate_apikey($arrData['user_password1'], $new_salt);

			$arrSave = array(
				'username' 				=> $arrData['username'],
				'user_password'			=> $new_password,
				'user_email'			=> $this->crypt->encrypt($arrData['user_email']),
				'first_name'			=> (isset($arrData['first_name'])) ? $arrData['first_name'] : '',
				'country'				=> (isset($arrData['country'])) ? $arrData['country'] : '',
				'gender'				=> (isset($arrData['gender'])) ? $arrData['gender'] : '',
				'user_style'			=> $arrData['user_style'],
				'user_lang'				=> $arrData['user_lang'],
				'user_timezone'			=> $arrData['user_timezone'],
				'user_key'				=> $user_key,
				'user_active'			=> $user_active,
				'rules'					=> ($rules) ? 1 : 0,
				'api_key'				=> $strApiKey,
			);
			if ($strLoginMethod && $this->user->handle_login_functions('after_register', $strLoginMethod )){
				$arrSave = array_merge($arrSave, $this->user->handle_login_functions('after_register', $strLoginMethod ));
			}

			$user_id = $this->insert_user($arrSave);
			$this->pdh->enqueue_hook('user');
			return $user_id;
		}

		public function insert_user_bridge($username, $password, $email, $rules = false, $apikey=''){
			$arrData = array(
				'username'				=> $username,
				'user_password'			=> $password,
				'user_email'			=> $this->crypt->encrypt($email),
				'api_key'				=> $apikey,
				'user_active'			=> 1,
				'rules'					=> ($rules) ? 1 : 0,
			);
			$user_id = $this->insert_user($arrData, false);
			$this->pdh->enqueue_hook('user');
			return $user_id;
		}

		public function set_defaults($arrData){
			$arrDefaults = array(
				'user_alimit'		=> $this->config->get('default_alimit'),
				'user_elimit'		=> $this->config->get('default_elimit'),
				'user_ilimit'		=> $this->config->get('default_ilimit'),
				'user_nlimit'		=> $this->config->get('default_nlimit'),
				'user_rlimit'		=> $this->config->get('default_rlimit'),
				'user_style'		=> $this->config->get('default_style'),
				'user_lang'			=> $this->config->get('default_lang'),
				'user_timezone'		=> $this->config->get('timezone'),
				'user_date_long'	=> ($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->user->lang('style_date_long'),
				'user_date_short'	=> ($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->user->lang('style_date_short'),
				'user_date_time'	=> ($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->user->lang('style_date_time'),
				'exchange_key'			=> md5(rand().rand()),
			);
			$arrReturn = $arrData;
			foreach ($arrDefaults as $key => $value){
					if (!isset($arrData[$key])){
						$arrData[$key] = $arrDefaults[$key];
					}
			}
			return $arrData;
		}

		public function update_user ($user_id, $query_ary, $logging = true, $defaults = true){
			if ($defaults){
				$query_ary = $this->set_defaults($query_ary);
			}
			$sql = $this->db->query("UPDATE __users SET :params WHERE user_id = ?", $query_ary, $user_id);

			if ($logging){
				$log_action = array(
					'{L_USER}'	=> $this->in->get('username'),
				);
				$this->log_insert('action_user_updated', $log_action);
			}


			$this->pdh->enqueue_hook('user');
			return ($sql) ? true : false;
		}

		public function update_user_settings ($user_id, $settingsdata) {
			$query_ary = array();
			if ( $this->in->get('username') != $this->in->get('old_username') ){
				$query_ary['username'] = $this->in->get('username');
			}
			if ( $this->in->get('new_password') ){
				$new_salt = $this->user->generate_salt();
				$query_ary['user_password'] = $this->user->encrypt_password($this->in->get('new_password'), $new_salt).':'.$new_salt;
				$strApiKey = $this->user->generate_apikey($this->in->get('new_password'), $new_salt);
				$query_ary['api_key'] = $strApiKey;
				$query_ary['user_login_key'] = '';
			}

			$query_ary['user_email']			= $this->crypt->encrypt($this->in->get('email_address'));
			
			$privArray = array();
			$customArray = array();
			$custom_fields = array('user_avatar', 'work', 'interests', 'hardware', 'facebook', 'twitter');
			foreach($settingsdata as $group => $fieldsets) {
				if($group == 'registration_information') continue;
				foreach($fieldsets as $tab => $fields) {
					foreach($fields as $name => $field) {
						if($tab == 'user_priv') 
							$privArray[$name] = $this->html->widget_return($field);
						elseif(in_array($name, $custom_fields)) 
							$customArray[$name] = $this->html->widget_return($field);
						else 
							$query_ary[$name] = $this->html->widget_return($field);
					}
				}
			}
			
			$query_ary['privacy_settings']		= serialize($privArray);
			$query_ary['custom_fields']			= serialize($customArray);

			$plugin_settings = array();
			if (is_array($this->pm->get_menus('settings'))){
				foreach ($this->pm->get_menus('settings') as $plugin => $values){
					foreach ($values as $key=>$setting){
						$name = $setting['name'];
						$setting['name'] = $plugin.':'.$setting['name'];
						$setting['plugin'] = $plugin;
						$plugin_settings[$plugin][$name] = $this->html->widget_return($setting);
					}
				}
			}
			$query_ary['plugin_settings']	= serialize($plugin_settings);
			return $this->update_user($user_id, $query_ary);
		}

		public function delete_avatar($user_id) {
			$result = $this->db->query_first("SELECT custom_fields FROM __users WHERE user_id = '".$this->db->escape($user_id)."'");
			$custom = unserialize($result);
			$this->pfh->Delete($this->pfh->FilePath('user_avatars/'.$custom['user_avatar']));
			unset($custom['user_avatar']);
			$this->db->query("UPDATE __users SET custom_fields = '".$this->db->escape(serialize($custom))."' WHERE user_id='".$this->db->escape($user_id)."'");
			$this->pdh->enqueue_hook('user');
		}

		public function delete_authaccount($user_id, $strMethod){
			$arrAccounts = $this->pdh->get('user', 'auth_account', array($user_id));
			unset($arrAccounts[$strMethod]);
			$this->db->query("UPDATE __users SET auth_account = '".$this->db->escape($this->crypt->encrypt(serialize($arrAccounts)))."' WHERE user_id='".$this->db->escape($user_id)."'");
			$this->pdh->enqueue_hook('user');
			return true;
		}

		public function add_authaccount($user_id, $strAccount, $strMethod){
			$arrAccounts = $this->pdh->get('user', 'auth_account', array($user_id));
			$arrAccounts[$strMethod] = $strAccount;
			$this->db->query("UPDATE __users SET auth_account = '".$this->db->escape($this->crypt->encrypt(serialize($arrAccounts)))."' WHERE user_id='".$this->db->escape($user_id)."'");
			$this->pdh->enqueue_hook('user');
			return true;
		}

		public function update_userstyle($style){
			$this->db->query("UPDATE __users SET user_style='".$this->db->escape($style)."'");
			$this->pdh->enqueue_hook('user');
		}

		public function activate($user_id, $active=1) {
			if(!is_array($user_id)) $user_id = array($user_id);
			$this->db->query("UPDATE __users SET user_active = '".$this->db->escape($active)."' WHERE user_id IN ('".implode("', '", $user_id)."');");
			$this->pdh->enqueue_hook('user');
		}

		public function update_failed_logins($user_id, $intFailedLogins) {
			$this->db->query("UPDATE __users SET failed_login_attempts = '".$this->db->escape($intFailedLogins)."' WHERE user_id = '".$this->db->escape($user_id)."';");
			$this->pdh->enqueue_hook('user');
		}

		public function hide_nochar_info($user_id) {
			$this->db->query("UPDATE __users SET hide_nochar_info = '1' WHERE user_id = '".$this->db->escape($user_id)."';");
			$this->pdh->enqueue_hook('user');
		}

		public function create_new_activationkey($user_id){
			// Create a new activation key
			$user_key = random_string(true, 32);
			$sql = "UPDATE __users
						SET user_key='" . $this->db->escape($user_key) . "'
						WHERE user_id='" . $this->db->escape($user_id) . "'";
			if(!$this->db->query($sql)) return false;
			$this->pdh->enqueue_hook('user');
			return $user_key;
		}

		public function create_new_exchangekey($user_id){
			$app_key = random_string(true, 32);
			$sql = "UPDATE __users
						SET exchange_key='" . $this->db->escape($app_key) . "'
						WHERE user_id='" . $this->db->escape($user_id) . "'";
			if(!$this->db->query($sql)) return false;
			$this->pdh->enqueue_hook('user');
			return $app_key;
		}

		public function delete_user($user_id, $delete_member = false) {
			$this->db->query("DELETE FROM __users WHERE user_id=".$this->db->escape($user_id));
			$this->db->query("DELETE FROM __auth_users WHERE user_id=".$this->db->escape($user_id));
			$this->db->query("DELETE FROM __groups_users WHERE user_id=".$this->db->escape($user_id));
			$this->pdh->put('member', 'update_connection', array(array(), $user_id));
			if ($delete_member){
				$members = $this->pdh->get('member', 'connection_id', array($user_id));
				foreach ($members as $member){
					$this->pdh->put('member', 'delete_member', array($member));
				}
			}
			$log_action = array(
				'{L_USER}'		=> $this->pdh->get('user', 'name', array($user_id))
			);
			$this->log_insert('action_user_deleted', $log_action);
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('member_update');
			$this->pdh->enqueue_hook('update_connection');
		}

		public function reset() {
			$this->db->query("DELETE FROM __users WHERE user_id != '".$this->db->escape($this->user->data['user_id'])."';");
			$this->db->query("DELETE FROM __member_user WHERE user_id != '".$this->db->escape($this->user->data['user_id'])."';");
			$this->pdh->enqueue_hook('user');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_user', pdh_w_user::__shortcuts());
?>