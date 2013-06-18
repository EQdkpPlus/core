<?php
 /*
 * Project:		EQdkp TwinkIt (v0.7 eqdkp plus sandbox test)
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		twinkit
 * @version		$Rev$
 *
 * $Id$
 */
if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if (!class_exists("pdh_r_user")){
	class pdh_r_user extends pdh_r_generic {
		public static function __shortcuts() {
		$shortcuts = array('db', 'user', 'pfh', 'pdh', 'crypt' => 'encrypt');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $users;

		public $hooks = array(
			'user',
		);

		public function reset(){
			$this->users = NULL;
		}

		public function init(){
			$this->users = array();
			$sql = "SELECT * FROM __users u ORDER BY username ASC;";
			$r_result = $this->db->query($sql);

			while( $row = $this->db->fetch_record($r_result) ){
				//decrypt email address
				$row['user_email'] = $this->crypt->decrypt($row['user_email']);
				$row['auth_account'] = unserialize($this->crypt->decrypt($row['auth_account']));
				$this->users[$row['user_id']] = $row;
				$this->users[$row['user_id']]['username_clean'] = clean_username($row['username']);
				$this->users[$row['user_id']]['user_email_clean'] = utf8_strtolower($row['user_email']);
			}
			$this->db->free_result($r_result);
		}

		public function get_id_list(){
			return array_keys($this->users);
		}

		public function get_name($user_id){
			if (isset($this->users[$user_id]) AND isset($this->users[$user_id]['username'])){
				return $this->users[$user_id]['username'];
			} elseif ($user_id == ANONYMOUS) {
				return $this->user->lang('anonymous');
			} else {
				return $this->user->lang('unknown');
			}
		}

		public function get_check_username($name){
			$name = clean_username($name);
			return (is_array(search_in_array($name, $this->users, true, 'username_clean'))) ? 'false' : 'true';
		}

		public function get_check_email($email){
			$email = utf8_strtolower($email);
			return (is_array(search_in_array($email, $this->users, true, 'user_email_clean'))) ? 'false' : 'true';
		}

		public function get_check_auth_account($name){
			return (is_array(search_in_array($name, $this->users, true, 'auth_account'))) ? false : true;
		}

		public function get_userid_for_authaccount($strAuthAccount){
			if ($strAuthAccount != ""){
				$arrResult = search_in_array($strAuthAccount, $this->users, true, 'auth_account');
				if (is_array($arrResult)){
					$arrResultKeys = array_keys($arrResult);
					return $arrResultKeys[0];
				}
			}
			return false;
		}

		public function get_userid_for_email($strEmail){
			if ($strEmail != ""){
				$strEmail = utf8_strtolower($strEmail);
				$arrResult = search_in_array($strEmail, $this->users, true, 'user_email_clean');
				if (is_array($arrResult)){
					$arrResultKeys = array_keys($arrResult);
					return $arrResultKeys[0];
				}
			}
			return false;
		}

		public function get_userid($name){
			$name = clean_username($name);
			if (is_array(search_in_array($name, $this->users, true, 'username_clean'))){
				$array = array_keys(search_in_array($name, $this->users, true, 'username_clean'));
				return $array[0];
			} else {
				return ANONYMOUS;
			}
		}

		public function get_check_password($password, $user_id){
			$hashAndSalt = $this->users[$user_id]['user_password'];
			return $this->user->checkPassword($password, $hashAndSalt);
		}

		public function get_email($user_id, $checkForIgnoreMailsFlag = false){
			if ($checkForIgnoreMailsFlag){
				$arrPriv = $this->get_privacy_settings($user_id);
				if (!$arrPriv['priv_no_boardemails']) return $this->users[$user_id]['user_email'];
				return '';
			} else {
				return $this->users[$user_id]['user_email'];
			}
			return '';
		}

		public function get_last_visit($user_id) {
			return $this->users[$user_id]['user_lastvisit'];
		}

		public function get_active($user_id) {
			return $this->users[$user_id]['user_active'];
		}

		public function get_failed_logins($user_id) {
			return $this->users[$user_id]['failed_login_attempts'];
		}

		public function get_cellphone($user_id){
			return $this->users[$user_id]['cellphone'];
		}
		
		public function get_exchange_key($user_id){
			return $this->users[$user_id]['exchange_key'];
		}

		public function get_data($user_id=''){
			if ($user_id == ''){
				return $this->users;
			} else {
				if (isset($this->users[$user_id])){
					$data = $this->users[$user_id];
					if (strpos($data['user_password'], ':') === false){
						$data['password'] = $data['user_password'];
					} else {
						list($data['password'], $data['salt']) = explode(':', $data['user_password']);
					}

					return $data;
				} else {
					return false;
				}
			}
		}

		public function get_stylecount($styleid){
			return countWhere($this->users, '==', $styleid, 'user_style');
		}

		public function get_inactive(){
			$users = array();
			foreach ($this->users as $user_id => $value){
				if ($value['user_active'] == '0'){
					$users[] = $user_id;
				}
			}
			return $users;
		}

		public function get_active_users(){
			$users = array();
			foreach ($this->users as $user_id => $value){
				if ($value['user_active'] == '1'){
					$users[] = $user_id;
				}
			}
			return $users;
		}

		public function get_regdate($user_id){
			return $this->users[$user_id]['user_registered'];
		}

		public function get_custom_fields($user_id, $field = false){
			$fields = unserialize($this->users[$user_id]['custom_fields']);
			if ($fields){
				if ($field){
					return $fields[$field];
				}
				return $fields;
			} else {
				return array();
			}
		}

		public function get_avatarimglink($user_id){
			if($avatarimg = $this->get_custom_fields($user_id, 'user_avatar')){
				return $this->pfh->FolderPath('user_avatars','eqdkp').$avatarimg;
			}
			return '';
		}

		public function get_privacy_settings($user_id) {
			$fields = unserialize($this->users[$user_id]['privacy_settings']);
			if ($fields){
				$fields['priv_set'] = ((isset($fields['priv_set'])) ? (int)$fields['priv_set'] : 1);
				$fields['priv_phone'] = ((isset($fields['priv_phone'])) ? (int)$fields['priv_phone'] : 1);
				$fields['priv_no_boardemails'] = ((isset($fields['priv_no_boardemails'])) ? (int)$fields['priv_no_boardemails'] : 0);
				return $fields;
			} else {
				return array('priv_set' => 1, 'priv_phone' => 1, 'priv_no_boardemails' => 0);
			}
		}

		public function get_mainchar($user_id){
			$members = $this->pdh->get('member', 'connection_id', array($user_id));
			return $this->pdh->get('member', 'mainid', array($members[0]));
		}

		public function get_plugin_settings($user_id, $plugin = false){
			$fields = unserialize($this->users[$user_id]['plugin_settings']);
			if ($fields){
				if ($plugin){
					return $fields[$plugin];
				}
				return $fields;
			} else {
				return array();
			}
		}

		public function get_auth_account($user_id){
			return $this->users[$user_id]['auth_account'];
		}

		public function get_search($search_value) {
			$arrSearchResults = array();
			if (is_array($this->users)){
				foreach($this->users as $id => $value) {
					if(stripos($value['username'], $search_value) !== false OR stripos($value['username_clean'], $search_value) !== false OR stripos($value['first_name'], $search_value) !== false OR stripos($value['last_name'], $search_value) !== false) {

						$arrSearchResults[] = array(
							'id'	=> '',
							'name'	=> $this->get_name($id),
							'link'	=> $this->root_path.'listusers.php'.$this->SID.'&amp;u='.$id,
						);
					}
				}
			}
			return $arrSearchResults;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_user', pdh_r_user::__shortcuts());
?>