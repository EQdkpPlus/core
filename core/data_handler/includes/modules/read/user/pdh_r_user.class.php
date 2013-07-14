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
		$shortcuts = array('db', 'user', 'pfh', 'pdh', 'crypt' => 'encrypt', 'config', 'html', 'time', 'routing');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $users;
		private $countries = false;
		private $online_user = false;

		public $hooks = array(
			'user', 'user_groups_update'
		);
		
		public $presets = array(
			'usersmscheckbox'  => array('sms_checkbox', array('%user_id%'), array()),
			'username' => array('name', array('%user_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'), array()),
			'userfullname' => array('fullname', array('%user_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'), array()),
			'useravatar'  => array('avatarimglink', array('%user_id%'), array()),
			'useremail'  => array('email', array('%user_id%', true), array()),
			'usercountry'  => array('country', array('%user_id%'), array()),
			'userregdate'  => array('regdate', array('%user_id%'), array()),
			'usergroups'  => array('groups', array('%user_id%', '%use_controller%'), array()),
			'usercharnumber'  => array('charnumber', array('%user_id%'), array()),
			'useronlinestatus' => array('is_online', array('%user_id%'), array()),
			"usericq" => array('icq', array('%user_id%'), array()),
			"userskype" => array('skype', array('%user_id%'), array()),
			"usercellphone" => array('cellphone', array('%user_id%'), array()),
			"userphone" => array('phone', array('%user_id%'), array()),
			"usertwitter" => array('twitter', array('%user_id%'), array()),
			"userfacebook" => array('facebook', array('%user_id%'), array()),
			"usertown" => array('town', array('%user_id%'), array()),
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

		public function get_id_list($skip_special_users = true){
			if ($skip_special_users){
				$special_user	= unserialize(stripslashes($this->config->get('special_user')));
				$special_user = (!$special_user) ? array() : $special_user;
				if (count($special_user)){
					$arrOut = array();
					foreach($this->users as $userid => $data){
						if (!in_array($userid, $special_user)) $arrOut[] = $userid;
					}
					return $arrOut;
				} else {
					return array_keys($this->users);
				}
			}
			return array_keys($this->users);
		}

		public function get_name($user_id, $link_url = '', $link_url_suffix = '', $blnUseController=false){
			if (isset($this->users[$user_id]) AND isset($this->users[$user_id]['username'])){
				return $this->users[$user_id]['username'];
			} elseif ($user_id == ANONYMOUS) {
				return $this->user->lang('anonymous');
			} else {
				return $this->user->lang('unknown');
			}
		}
		
		public function get_html_name($user_id, $link_url = '', $link_url_suffix = '', $blnUseController=false){
			if ($blnUseController) return '<a href="'.$this->routing->build('User', $this->get_name($user_id), 'u'.$user_id).'">'.$this->get_name($user_id).'</a>';
			return '<a href="'.$link_url.$this->SID.'&u='.$user_id.'">'.$this->get_name($user_id).'</a>';
		}
		
		public function comp_name($params1, $params2) {
			return strcasecmp($this->pdh->get('user', 'name', array($params1[0])), $this->pdh->get('user', 'name', array($params2[0])));
		}
		
		public function get_fullname($user_id){
			return (($this->users[$user_id]['first_name'] != '') ? sanitize($this->users[$user_id]['first_name']).' ' : '').(($this->users[$user_id]['last_name'] != '') ? sanitize($this->users[$user_id]['last_name']) : '');
		}
		
		public function comp_fullname($params1, $params2) {
			return strcasecmp($this->pdh->get('user', 'fullname', array($params1[0])), $this->pdh->get('user', 'fullname', array($params2[0])));
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
		
		public function get_is_online($user_id){
			$this->init_online_user();
			return (in_array($user_id, $this->online_user)) ? true : false;
		}
		
		public function get_html_is_online($user_id){
			return ($this->get_is_online($user_id)) ? '<img src="'.$this->server_path.'/images/glyphs/status_green.gif" alt="" />' : '<img src="'.$this->server_path.'/images/glyphs/status_red.gif" alt="" />';
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
		
		public function get_html_email($user_id, $checkForIgnoreMailsFlag = false){
			if ($this->check_privacy($user_id) && $this->user->is_signedin() && strlen($this->get_email($user_id, $checkForIgnoreMailsFlag))) {
				return '<a href="javascript:usermailer('.$user_id.');"><i class="icon-envelope"></i>'.$this->user->lang('adduser_send_mail').'</a>';
			}
		
			return '';
		}

		public function get_last_visit($user_id) {
			return $this->users[$user_id]['user_lastvisit'];
		}
		
		public function get_country($user_id) {
			return $this->users[$user_id]['country'];
		}
		
		public function get_html_country($user_id){
			$country = $this->get_country($user_id);
			if (strlen($country)){
				$this->init_countries();
				$html = $this->html->Tooltip(ucfirst(strtolower($this->countries[$country])), '<img src="'.$this->server_path.'images/flags/'.strtolower($country).'.png" alt="'.$country.'" />');
				return $html;
			}
			return '';
		}

		public function get_active($user_id) {
			return $this->users[$user_id]['user_active'];
		}

		public function get_failed_logins($user_id) {
			return $this->users[$user_id]['failed_login_attempts'];	
		}
		
		public function get_town($user_id){
			return $this->users[$user_id]['town'];
		}

		public function get_cellphone($user_id){
			return $this->users[$user_id]['cellphone'];
		}

		public function get_exchange_key($user_id){
			return $this->users[$user_id]['exchange_key'];
		}
		
		public function get_html_cellphone($user_id){
			if ($this->check_phone_privacy($user_id) && strlen($this->get_cellphone($user_id))){
				return $this->get_cellphone($user_id);
			}
			return '';
		}
		
		public function get_phone($user_id){
			return $this->users[$user_id]['phone'];
		}
		
		public function get_html_phone($user_id){
			if ($this->check_phone_privacy($user_id) && strlen($this->get_phone($user_id))){
				return $this->get_phone($user_id);
			}
			return '';
		}
		
		public function get_icq($user_id){
			return $this->users[$user_id]['icq'];
		}
		
		public function get_html_icq($user_id){
			if ($this->check_privacy($user_id) && strlen($this->get_icq($user_id))){
				return '<a href="http://www.icq.com/people/'.$this->get_icq($user_id).'" target="_blank"><img src="http://status.icq.com/online.gif?icq='.$this->get_icq($user_id).'&amp;img=5" alt="icq" /></a>';
			}
			return '';
		}
		
		public function get_skype($user_id){
			return $this->users[$user_id]['skype'];
		}
		
		public function get_html_skype($user_id){
			if ($this->check_privacy($user_id) && strlen($this->get_skype($user_id))){
				return '<a href="skype:'.$this->get_skype($user_id).'?add"><i class="icon-skype icon-large"></i>'.sanitize($this->get_skype($user_id)).'</a>';
			}
			return '';
		}
		
		public function get_twitter($user_id){
			$twitter = $this->get_custom_fields($user_id, 'twitter');
			if (is_array($twitter)) return '';
			return $twitter;
		}
		
		public function get_html_twitter($user_id){
			if ($this->check_privacy($user_id) && strlen($this->get_twitter($user_id))){
				return '<a href="http://twitter.com/'.$this->get_twitter($user_id).'" target="_blank"><i class="icon-twitter icon-large"></i>'.$this->get_twitter($user_id).'</a>';
			}
			return '';	
		}
		
		public function get_facebook($user_id){
			$fb = $this->get_custom_fields($user_id, 'facebook');
			if (is_array($fb)) return '';
			return $fb;
		}
		
		public function get_html_facebook($user_id){
			if ($this->check_privacy($user_id) && strlen($this->get_facebook($user_id))){
				return '<a href="http://facebook.com/'.((is_numeric($this->get_facebook($user_id))) ? 'profile.php?id='.$this->get_facebook($user_id) : $this->get_facebook($user_id)).'" target="_blank"><i class="icon-facebook icon-large"></i>'.sanitize($this->get_facebook($user_id)).'</a>';
			}
			return '';	
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
		
		public function get_charnumber($user_id){
			$arrConnections = $this->pdh->get('member', 'connection_id', array($user_id));
			if ($arrConnections && is_array($arrConnections)){
				return count($arrConnections);
			}
			return 0;
		}
		
		public function get_groups($user_id){
			$arrMemberships = $this->pdh->get('user_groups_users', 'memberships', array($user_id));
			$arrMemberships = $this->pdh->sort($arrMemberships, 'user_groups', 'sortid');
			return $arrMemberships;
		}
		
		public function get_html_groups($user_id, $blnUseController=false){
			$arrMemberships = $this->get_groups($user_id);
			
			$arrOut = array();
			foreach($arrMemberships as $groupid){
				if ($blnUseController) {
					$arrOut[] = '<a href="'.$this->routing->build('Usergroup', $this->pdh->get('user_groups', 'name', array($groupid)), $groupid).'">'.$this->pdh->get('user_groups', 'name', array($groupid)).'</a>';
				} else {
					$arrOut[] = '<a href="listusers.php'.$this->SID.'&g='.$groupid.'">'.$this->pdh->get('user_groups', 'name', array($groupid)).'</a>';
				}			
			}
			return implode(', ', $arrOut);
		}
		
		public function comp_groups($params1, $params2) {
			$arrMemberships1 = $this->get_groups($params1[0]);
			$arrMemberships2 = $this->get_groups($params2[0]);
			$intGroup1 = $arrMemberships1[0];
			$intGroup2 = $arrMemberships2[0];
			
			return ($intGroup1 > $intGroup2);
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
		
		public function get_html_regdate($user_id){
			return $this->time->user_date($this->get_regdate($user_id), true);
		}

		public function get_birthday($user_id){
			return ($this->users[$user_id]['birthday'] > 0) ? $this->users[$user_id]['birthday'] : 0;
		}

		public function get_birthday_list(){
			$useroutput	= array();
			foreach($this->users as $user_id=>$uderdata){
				if($uderdata['birthday'] > 0){
					$useroutput[$user_id]	= $uderdata['birthday'];
				}
			}
			return $useroutput;
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
		
		public function get_avatar($user_id){
			$avatarimg = $this->get_custom_fields($user_id, 'user_avatar');
			return $avatarimg;
		}

		public function get_avatarimglink($user_id){
			if($avatarimg = $this->get_custom_fields($user_id, 'user_avatar')){
				return $this->pfh->FolderPath('users/'.$user_id,'files').$avatarimg;
			}
			return '';
		}
		
		public function get_html_avatarimglink($user_id){
			$strImg = $this->get_avatarimglink($user_id);
			if (!strlen($strImg)){
				$strImg = $this->server_path.'images/no_pic.png';
			} else {
				$strImg = $this->pfh->FileLink($strImg, false, 'absolute');
			}
			
			return '<img src="'.$strImg.'" class="user-avatar" alt="" />';
		}

		public function get_privacy_settings($user_id) {
			$fields = unserialize($this->users[$user_id]['privacy_settings']);
			if ($fields){
				$fields['priv_set'] = ((isset($fields['priv_set'])) ? (int)$fields['priv_set'] : 1);
				$fields['priv_phone'] = ((isset($fields['priv_phone'])) ? (int)$fields['priv_phone'] : 1);
				$fields['priv_no_boardemails'] = ((isset($fields['priv_no_boardemails'])) ? (int)$fields['priv_no_boardemails'] : 0);
				$fields['priv_nosms'] = ((isset($fields['priv_nosms'])) ? (int)$fields['priv_nosms'] : 0);
				$fields['priv_bday'] = ((isset($fields['priv_bday'])) ? (int)$fields['priv_bday'] : 0);
				return $fields;
			} else {
				return array('priv_set' => 1, 'priv_phone' => 1, 'priv_no_boardemails' => 0, 'priv_nosms' => 0, 'priv_bday' => 0);
			}
		}
		
		public function get_sms_checkbox($user_id){
			$privacy = $this->get_privacy_settings($user_id);
			if((int)$this->config->get('pk_sms_enable') == 1 && strlen($this->get_cellphone($user_id)) && $privacy['priv_nosms'] != 1 && $this->user->check_auth('a_sms_send', false)){
				return '<input type="checkbox" name="sendto['.$user_id.']" value="'.$this->crypt->encrypt($this->get_cellphone($user_id).';'.$this->get_name($user_id)).'" class="cellphonebox" />';
			}
			return '';
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
		
		private function check_privacy($user_id){
			$arrPrivacy = $this->get_privacy_settings($user_id);
			$is_user		= ($this->user->is_signedin()) ? true : false;
			$is_admin		= ($this->user->check_group(2, false) || $this->user->check_group(3, false));
			
			$perm = false;
			
			switch ($arrPrivacy['priv_set']){
				case 0: // all
					$perm = true;
					break;
				case 1: // only user
					if($is_user){
						$perm = true;
					}
					break;
				case 2: // only admins
					if($is_admin){
						$perm = true;
					}
				break;
				default:
					if($is_user || $is_admin){
						$perm = true;
					};
			}
			
			return $perm;
		}
		
		private function check_phone_privacy($user_id){
			$arrPrivacy = $this->get_privacy_settings($user_id);
			$is_user		= ($this->user->is_signedin()) ? true : false;
			$is_admin		= ($this->user->check_group(2, false) || $this->user->check_group(3, false));
			
			$phone_perm = false;
			
			switch ($arrPrivacy['priv_phone']){
				case 0: // all
					// do nothing... everything fine
					$phone_perm = true;
					break;
				case 1: // only user
					if ($is_user) { $phone_perm = true;}
					break;
				case 2: // only admins
					if ($is_admin) { $phone_perm = true;}
					break;
				case 3: // nobody
					$phone_perm = false;
					break;
				default:
					if ($is_admin || $is_user){
						$phone_perm = true;
					}
			}
			return $phone_perm;
		}
		
		private function init_countries(){
			if (!$this->countries){
				include($this->root_path.'core/country_states.php');
				$this->countries = $country_array;
			}
		}
		
		private function init_online_user(){
			if (!$this->online_user){
				$result = $this->db->query("SELECT session_user_id FROM __sessions;");
				while ( $row = $this->db->fetch_record($result) ) {
					$this->online_user[] = $row['session_user_id'];
				}
				$this->db->free_result($result);
			}
		}
	}//end class
}//end if
?>