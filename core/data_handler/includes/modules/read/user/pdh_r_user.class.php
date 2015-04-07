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
	die('Do not access this file directly.');
}

if (!class_exists("pdh_r_user")){
	class pdh_r_user extends pdh_r_generic {
	
		public $default_lang = 'english';
		public $users;
		private $countries = false;
		private $online_user = false;
		private $userProfileFields = false;

		public $hooks = array(
			'user', 'user_groups_update'
		);
		
		public $presets = array(
			'username' => array('name', array('%user_id%', '%link_url%', '%link_url_suffix%', '%use_controller%'), array()),
			'useravatar'  => array('avatarimglink', array('%user_id%'), array()),
			'useremail'  => array('email', array('%user_id%', true), array()),
			'usercountry'  => array('country', array('%user_id%'), array()),
			'userregdate'  => array('regdate', array('%user_id%'), array()),
			'usergroups'  => array('groups', array('%user_id%', '%use_controller%'), array()),
			'usercharnumber'  => array('charnumber', array('%user_id%'), array()),
			'useronlinestatus' => array('is_online', array('%user_id%'), array()),
		);
		
		public function init_presets(){
			//generate presets
			$this->userProfileFields = $this->pdh->get('user_profilefields', 'id_list');
			if(is_array($this->userProfileFields)) {
				foreach($this->userProfileFields as $intFieldID){
					$this->presets['userprofile_'.$intFieldID] = array('profilefield', array('%user_id%', $intFieldID), array($intFieldID));
					$this->preset_lang['userprofile_'.$intFieldID] = 'Benutzerprofil-'.$this->pdh->geth('user_profilefields', 'name', array($intFieldID));
				}
			}
		}

		public function reset(){
			$this->users = NULL;
		}

		public function init(){
			$this->users = array();
			
			$objQuery = $this->db->query("SELECT * FROM __users u ORDER BY username ASC;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					//decrypt email address
					$row['user_email'] = $this->encrypt->decrypt($row['user_email']);
					$row['auth_account'] = unserialize($this->encrypt->decrypt($row['auth_account']));
					$this->users[$row['user_id']] = $row;
					$this->users[$row['user_id']]['username_clean'] = clean_username($row['username']);
					$this->users[$row['user_id']]['user_email_clean'] = utf8_strtolower($row['user_email']);
				}
			}

		}

		public function get_id_list($skip_special_users = true){
			if ($skip_special_users){
				$special_user = $this->config->get('special_user');
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
			} elseif ($user_id == CRONJOB) {
				return "Cronjob";
			} else {
				return $this->user->lang('unknown');
			}
		}
		
		public function get_html_name($user_id, $link_url = '', $link_url_suffix = '', $blnUseController=false){
			if ($blnUseController) return '<a href="'.$this->routing->build('User', $this->get_name($user_id), 'u'.$user_id).'" data-user-id="'.$user_id.'">'.$this->get_name($user_id).'</a>';
			return '<a href="'.$link_url.$this->SID.'&u='.$user_id.'" data-user-id="'.$user_id.'">'.$this->get_name($user_id).'</a>';
		}
		
		public function comp_name($params1, $params2) {
			return strcasecmp($this->pdh->get('user', 'name', array($params1[0])), $this->pdh->get('user', 'name', array($params2[0])));
		}
		
		public function get_check_username($name){
			$name = clean_username($name);
			return (is_array(search_in_array($name, $this->users, true, 'username_clean'))) ? 'false' : 'true';
		}

		public function get_check_email($email){
			$email = utf8_strtolower($email);
			return (is_array(search_in_array($email, $this->users, true, 'user_email_clean'))) ? 'false' : 'true';
		}

		public function get_check_auth_account($name, $strMethod){
			return (is_array(search_in_array($name, $this->users, true, 'auth_account'))) ? false : true;
		}
		
		public function get_is_online($user_id){
			$this->init_online_user();
			return (in_array($user_id, $this->online_user)) ? true : false;
		}
		
		public function get_html_is_online($user_id){
			return ($this->get_is_online($user_id)) ? '<i class="eqdkp-icon-online"></i>' : '<i class="eqdkp-icon-offline"></i>';
		}

		public function get_userid_for_authaccount($strAuthAccount, $strMethod){
			if ($strAuthAccount != ""){
				$arrResult = search_in_array($strAuthAccount, $this->users, true, $strMethod);
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
		
		public function get_lang($user_id){
			return $this->users[$user_id]['user_lang'];
		}
		
		public function get_html_email($user_id, $checkForIgnoreMailsFlag = false){
			if ($this->get_check_privacy($user_id, 'userprofile_email') && strlen($this->get_email($user_id, $checkForIgnoreMailsFlag))) {
				if ($this->user->is_signedin()) {
					return '<a href="javascript:usermailer('.$user_id.');"><i class="fa fa-envelope fa-lg"></i>'.$this->user->lang('adduser_send_mail').'</a>';
				} else {
					return '<i class="fa fa-envelope fa-lg"></i> '.$this->get_email($user_id);
				}
			}
		
			return '';
		}

		public function get_last_visit($user_id) {
			return $this->users[$user_id]['user_lastvisit'];
		}
		
		public function get_html_last_visit($user_id) {
			return $this->time->user_date($this->get_last_visit($user_id), true);
		}
		
		public function get_country($user_id) {
			return $this->users[$user_id]['country'];
		}
		
		public function get_html_country($user_id){
			if (!$this->get_check_privacy($user_id, 'userprofile_country')) return '';
			
			$country = $this->get_country($user_id);
			if (strlen($country)){
				$this->init_countries();
				return '<img src="'.$this->server_path.'images/flags/'.strtolower($country).'.svg" alt="'.$country.'" class="coretip" data-coretip="'.ucfirst(strtolower($this->countries[$country])).'" />';
			}
			return '';
		}

		public function get_active($user_id) {
			return $this->users[$user_id]['user_active'];
		}

		public function get_failed_logins($user_id) {
			return $this->users[$user_id]['failed_login_attempts'];	
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
			$arrOut[] = '<div class="user-groups">';
			foreach($arrMemberships as $groupid){
				if ($this->pdh->get('user_groups', 'hide', array($groupid))) continue;
				if ($blnUseController) {
					$arrOut[] = '<a href="'.$this->routing->build('Usergroup', $this->pdh->get('user_groups', 'name', array($groupid)), $groupid).'" data-usergroup-id="'.$groupid.'"'.(($this->pdh->get('user_groups_users', 'is_grpleader', array($user_id, $groupid))) ? ' data-isgroupleader="1"' : ' data-isgroupleader="0"').'>'.$this->pdh->get('user_groups', 'name', array($groupid)).'</a>';
				} else {
					$arrOut[] = '<a href="listusers.php'.$this->SID.'&g='.$groupid.'" data-usergroup-id="'.$groupid.'"'.(($this->pdh->get('user_groups_users', 'is_grpleader', array($user_id, $groupid))) ? ' data-isgroupleader="1"' : ' data-isgroupleader="0"').'>'.$this->pdh->get('user_groups', 'name', array($groupid)).'</a>';
				}			
			}
			$arrOut[] = '</div>';
			return implode('', $arrOut);
		}
		
		public function comp_groups($params1, $params2) {
			$arrMemberships1 = $this->get_groups($params1[0]);
			$arrMemberships2 = $this->get_groups($params2[0]);
			$intGroup1 = array_shift($arrMemberships1);
			$intGroup2 = array_shift($arrMemberships2);
			$myArrayToSort = array($intGroup1, $intGroup2);
			$arrSorted = $this->pdh->sort($myArrayToSort, 'user_groups', 'sortid');
			if (array_shift($arrSorted) == array_shift($myArrayToSort)){
				return 1;
			}
			
			return -1;
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
			if(!isset($this->users[$user_id])) return array();
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

		public function get_avatarimglink($user_id, $fullSize=false){
			$intAvatarType = intval($this->get_custom_fields($user_id, 'user_avatar_type'));

			if ($intAvatarType == 0){
				$avatarimg = $this->get_custom_fields($user_id, 'user_avatar');
				
				if($avatarimg && strlen($avatarimg)){
					$fullSizeImage = $this->pfh->FolderPath('users/'.$user_id,'files').$avatarimg;
					$thumbnail = $this->pfh->FolderPath('users/thumbs','files').'useravatar_'.$user_id.'_68.'.pathinfo($avatarimg, PATHINFO_EXTENSION);		
					if (!$fullSize && is_file($thumbnail)) return $thumbnail;
					return $fullSizeImage;
				} elseif($this->config->get('gravatar_defaultavatar')){
					//Gravatar Default Avatars
					include_once $this->root_path.'core/gravatar.class.php';
					$gravatar = registry::register('gravatar');
					$result = $gravatar->getAvatar($this->get_name($user_id), (($fullSize) ? 400 : 68), true);
					if ($result) return $result;
				}
			} elseif($intAvatarType == 1){
				//Gravatar
				include_once $this->root_path.'core/gravatar.class.php';
				$gravatar = registry::register('gravatar');
				$strEmail = $this->get_email($user_id);
				$strGravatarMail = $this->get_custom_fields($user_id, 'user_gravatar_mail');
				if (strlen($strGravatarMail)) $strEmail = $strGravatarMail;
				$result = $gravatar->getAvatar($strEmail, (($fullSize) ? 400 : 68));
				if ($result) return $result;
				//Disable
				$this->pdh->put('user', 'disable_gravatar', array($user_id));
			}

			return '';
		}
		
		public function get_html_avatarimglink($user_id, $fullSize=false){
			$strImg = $this->get_avatarimglink($user_id, $fullSize);
			if (!strlen($strImg)){
				$strImg = $this->server_path.'images/global/avatar-default.svg';
			} else {
				$strImg = $this->pfh->FileLink($strImg, false, 'absolute');
			}
			
			return '<div class="user-tooltip-avatar"><img src="'.$strImg.'" class="user-avatar" alt="" /></div>';
		}

		public function get_privacy_settings($user_id) {
			$fields = unserialize($this->users[$user_id]['privacy_settings']);
			return ($fields) ? $fields : array();
		}
		
		public function get_notification_settings($user_id){
			$fields = unserialize($this->users[$user_id]['notifications']);
			return ($fields) ? $fields : array();
		}
		
		public function get_notification_abos($strNotificationID){
			$arrUser = $this->get_id_list();
			$arrOut = array();
			foreach($arrUser as $intUserID){
				if ($this->get_notification_abo($strNotificationID, $intUserID)) $arrOut[] = $intUserID;
			}
			return $arrOut;
		}
		
		public function get_notification_abo($strNotificationID, $intUserID=false){
			if ($intUserID === false) $intUserID = $this->user->id;
			
			$arrNotificationSettings = $this->get_notification_settings($intUserID);
			if ($arrNotificationSettings && isset($arrNotificationSettings['ntfy_'.$strNotificationID])){
				if ((int)$arrNotificationSettings['ntfy_'.$strNotificationID]) return true;
			} else {
				if ($this->pdh->get('notification_types', 'check_existing_type', array($strNotificationID))){
					$intDefault = $this->pdh->get('notification_types', 'default', array($strNotificationID));
					return ($intDefault) ? true : false;
					
				} else {
					return true;
				}
			}
			
			return false;
		}
		
		public function get_notification_articlecategory_abo($intCategoryID, $intUserID=false){
			if ($intUserID === false) $intUserID = $this->user->id;
			
			$arrNotificationSettings = $this->get_notification_settings($intUserID);
			if ($arrNotificationSettings && isset($arrNotificationSettings['ntfy_comment_new_article'])){
				$arrCategories = $arrNotificationSettings['ntfy_comment_new_article'];
				if (in_array($intCategoryID, $arrCategories)) {
					return true;
				} else {
					return false;
				}
				
			} else {
				//Default Value
				$intDefault = $this->pdh->get('notification_types', 'default', array('comment_new_article'));
				if ($intDefault) {
					return true;
				} else {
					return false;
				}
			}
				
			return true;
		}
		
		public function get_notification_articlecategory_abos($intCategoryID){
			$arrUser = $this->get_id_list();
			$arrOut = array();
			foreach($arrUser as $intUserID){
				if ($this->get_notification_articlecategory_abo($intCategoryID, $intUserID)) $arrOut[] = $intUserID;
			}
			return $arrOut;
		}
		
		public function get_users_with_permission($strPermission){
			$arrOut = array();
			$arrUser = $this->get_id_list();
			foreach($arrUser as $intUserID){
				if ($this->user->check_auth($strPermission, false, $intUserID)) $arrOut[] = $intUserID;
			}
			return $arrOut;
		}

		public function get_mainchar($user_id){
			$members = $this->pdh->get('member', 'connection_id', array($user_id));
			return $this->pdh->get('member', 'mainid', array($members[0]));
		}

		public function get_plugin_settings($user_id, $plugin = false){
			$fields = unserialize($this->users[$user_id]['plugin_settings']);
			if ($fields){
				if ($plugin){
					return isset($fields[$plugin]) ? $fields[$plugin] : array();
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
							'name'	=> $this->get_html_avatarimglink($id).' '.$this->get_name($id),
							'link'	=> $this->routing->build('user', $value['username'], 'u'.$id),
						);
					}
				}
			}
			return $arrSearchResults;
		}
		
		public function get_profilefield($user_id, $intFieldID, $blnIgnorePrivacyCheck=false){
			if (!$blnIgnorePrivacyCheck && !$this->get_check_privacy($user_id, 'userprofile_'.$intFieldID)) return '';
			
			return $this->pdh->get('user_profilefields', 'display_field', array($intFieldID, $user_id));
		}
		
		public function get_html_profilefield($user_id, $intFieldID, $blnIgnorePrivacyCheck=false){
			if (!$blnIgnorePrivacyCheck && !$this->get_check_privacy($user_id, 'userprofile_'.$intFieldID)) return '';
			
			return $this->pdh->geth('user_profilefields', 'display_field', array($intFieldID, $user_id));
		}
		
		public function get_profilefield_by_name($intUserID, $strName, $blnIgnorePrivacyCheck=false){
			$intFieldID = $this->pdh->get('user_profilefields', 'field_by_name', array($strName));
			if ($intFieldID){
				return $this->get_profilefield($intUserID, $intFieldID, $blnIgnorePrivacyCheck);
			}
			return false;
		}
		
		public function get_html_profilefield_by_name($intUserID, $strName, $blnIgnorePrivacyCheck=false){
			$intFieldID = $this->pdh->get('user_profilefields', 'field_by_name', array($strName));
			if ($intFieldID){
				return $this->get_html_profilefield($intUserID, $intFieldID, $blnIgnorePrivacyCheck);
			}
			return false;
		}
		
		public function get_html_caption_profilefield($param){
			return $this->pdh->geth('user_profilefields', 'name', array($param));
		}
		
		public function get_check_privacy($user_id, $strField){
			$arrPrivacySettings = $this->get_privacy_settings($user_id);
			
			if (strpos($strField, 'priv_') !== 0) $strField = 'priv_'.$strField;
			$intUserValue = isset($arrPrivacySettings[$strField]) ? $arrPrivacySettings[$strField] : $this->get_privacy_defaults($strField);

			//Radio Fields
			$arrRadioFields = array('priv_bday', 'priv_no_boardemails');
			if (in_array($strField, $arrRadioFields)){
				return ($intUserValue) ? true : false;
			}
			
			//Now Check
			$is_user		= ($this->user->is_signedin()) ? true : false;
			$is_admin		= ($this->user->check_group(2, false) || $this->user->check_group(3, false));
			
			$perm = false;
			
			if ($strField == 'priv_wall_posts_read' || $strField ==  'priv_wall_posts_write'){

				switch ($intUserValue){
					case 0: // all
						$perm = true;
						break;
					case 1: // only user
						if($is_user){
							$perm = true;
						}
						break;
					case 2: // only me
						if($user_id === $this->user->id){
							$perm = true;
						}
						break;
				}
				
			} else {

				switch ($intUserValue){
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
			}	
				
			return $perm;
		}
		
		public function get_country_list(){
			$this->init_countries();
			return $this->countries;
		}
		
		private function get_privacy_defaults($strField){
			switch($strField){
				case 'priv_wall_posts_read': return 0;
				case 'priv_wall_posts_write': return 1;
				case 'priv_bday': return 0;
				case 'priv_no_boardemails': return 0;
				default: return 1;
			}
		}
				
		private function init_countries(){
			if (!$this->countries){
				include($this->root_path.'core/country_states.php');
				$this->countries = $country_array;
			}
		}
		
		private function init_online_user(){
			if (!$this->online_user){
				$objQuery = $this->db->prepare("SELECT session_user_id FROM __sessions WHERE session_current > ? AND session_user_id > 0;")->execute($this->time->time-600);
				if($objQuery){
					while($row = $objQuery->fetchAssoc()){
						$this->online_user[] = $row['session_user_id'];
					}
				}
			}
		}
	}//end class
}//end if
?>