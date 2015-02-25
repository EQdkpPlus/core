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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_user')) {
	class pdh_w_user extends pdh_w_generic {
		public static $shortcuts = array('crypt' => 'encrypt', 'email' => 'MyMailer');

		public function insert_user($arrData, $logging = true, $toDefaultGroup = true){
			$arrData = $this->set_defaults($arrData);
			$arrData['user_registered'] = $this->time->time;
			
			$objQuery = $this->db->prepare("INSERT INTO __users :p")->set($arrData)->execute();

			if ( !($objQuery) ) {
				return false;
			}

			$user_id = $objQuery->insertId;

			if ($logging) {
				// Logging
				$log_action = array(
					'{L_USER}'	=> $arrData['username'],
				);
				$this->log_insert('action_user_added', $log_action, $user_id, sanitize($arrData['username']));
			}
			
			$this->hooks->process('user_inserted', array('user_id' => $user_id, 'username' => $arrData['username'], 'data' => $arrData));

			//Put him to the default group
			if ($this->pdh->get('user_groups', 'standard_group') && $toDefaultGroup){
				$this->pdh->put('user_groups_users', 'add_user_to_group', array($user_id, $this->pdh->get('user_groups', 'standard_group'), false));
			}
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('styles_update');

			return $user_id;
		}

		public function register_user($arrData, $user_active = 1, $user_key = '', $rules = false, $strLoginMethod = false, $arrProfileData=false) {
			$new_salt = $this->user->generate_salt();
			$new_password = $this->user->encrypt_password($arrData['user_password1'], $new_salt).':'.$new_salt;

			//User Profilefields
			$arrUserProfileFields = array();
			if (is_array($arrProfileData)){
				foreach($arrProfileData as $key => $value){
					$newKey = intval(str_replace('userprofile_', '', $key));
					$arrUserProfileFields[$newKey] = $value;
				}
			}
			
			$arrSave = array(
				'username' 				=> $arrData['username'],
				'user_password'			=> $new_password,
				'user_email'			=> $this->crypt->encrypt($arrData['user_email']),
				'country'				=> (isset($arrData['country'])) ? $arrData['country'] : '',
				'gender'				=> (isset($arrData['gender'])) ? $arrData['gender'] : '',
				'user_style'			=> $arrData['user_style'],
				'user_lang'				=> $arrData['user_lang'],
				'user_timezone'			=> $arrData['user_timezone'],
				'user_key'				=> $user_key,
				'user_active'			=> $user_active,
				'rules'					=> ($rules) ? 1 : 0,
				'custom_fields'			=> serialize($arrUserProfileFields),
			);
			if ($strLoginMethod && $this->user->handle_login_functions('after_register', $strLoginMethod )){
				$arrSave = array_merge($arrSave, $this->user->handle_login_functions('after_register', $strLoginMethod ));
			}

			$user_id = $this->insert_user($arrSave);
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('styles_update');
			return $user_id;
		}

		public function insert_user_bridge($username, $password, $email, $rules = false){
			$arrData = array(
				'username'				=> $username,
				'user_password'			=> $password,
				'user_email'			=> $this->crypt->encrypt($email),
				'user_active'			=> 1,
				'rules'					=> ($rules) ? 1 : 0,
			);
			$user_id = $this->insert_user($arrData, false);
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('styles_update');
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
				'exchange_key'		=> md5(generateRandomBytes()),
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
			
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id = ?")->set($query_ary)->execute($user_id);

			if ($logging){
				$log_action = array(
					'{L_USER}'	=> $this->in->get('username'),
				);
				$this->log_insert('action_user_updated', $log_action, $user_id, $this->in->get('username'));
			}
			
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('styles_update');
			return ($objQuery) ? true : false;
		}

		public function delete_avatar($user_id) {
			$objQuery = $this->db->prepare("SELECT custom_fields FROM __users WHERE user_id =?")->execute($user_id);
			if ($objQuery && $objQuery->numRows){
				$arrResult = $objQuery->fetchAssoc();
				$custom = unserialize($arrResult['custom_fields']);
				$this->pfh->Delete($this->pfh->FilePath('user_avatars/'.$custom['user_avatar']));
				unset($custom['user_avatar']);
				
				$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'custom_fields' => serialize($custom)
				))->execute($user_id);
				
				$this->pdh->enqueue_hook('user');
				return true;
			}
			return false;		
		}
		
		public function disable_gravatar($user_id){
			$objQuery = $this->db->prepare("SELECT custom_fields FROM __users WHERE user_id =?")->execute($user_id);
			if ($objQuery && $objQuery->numRows){
				$arrResult = $objQuery->fetchAssoc();
				$custom = unserialize($arrResult['custom_fields']);
				$custom['user_avatar_type'] = '0';
				$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'custom_fields' => serialize($custom)
				))->execute($user_id);
				
				$this->pdh->enqueue_hook('user');
				return true;
			}	
			return false;		
		}

		public function delete_authaccount($user_id, $strMethod){
			$arrAccounts = $this->pdh->get('user', 'auth_account', array($user_id));
			unset($arrAccounts[$strMethod]);			
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'auth_account'	=> $this->crypt->encrypt(serialize($arrAccounts))
			))->execute($user_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			return true;
		}

		public function add_authaccount($user_id, $strAccount, $strMethod){
			$arrAccounts = $this->pdh->get('user', 'auth_account', array($user_id));
			$arrAccounts[$strMethod] = $strAccount;
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'auth_account'	=> $this->crypt->encrypt(serialize($arrAccounts))
			))->execute($user_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			return true;
		}

		public function update_userstyle($style){
			$objQuery = $this->db->prepare("UPDATE __users :p")->set(array(
					'user_style'	=> $style
			))->execute();
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('styles_update');
			return true;
		}

		public function activate($user_id, $active=1) {
			$oldState = intval($this->pdh->get('user', 'active', array($user_id)));
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'user_active'	=> $active
			))->execute($user_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			
			//Send out notification email
			$bodyvars = array(
				'USERNAME'		=> $this->pdh->get('user', 'name', array($user_id)),
				'GUILDTAG'		=> $this->config->get('guildtag'),
			);
			$this->email->Set_Language($this->pdh->get('user', 'lang', array($user_id)));
			if ($active && $oldState === 0){
				$result = $this->email->SendMailFromAdmin($this->pdh->get('user', 'email', array($user_id)), $this->user->lang('email_subject_activation_none'), 'register_account_activated.html', $bodyvars);		
				if (!$result) return false;
			}
			
			return true;
		}

		public function update_failed_logins($user_id, $intFailedLogins) {
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'failed_login_attempts'	=> $intFailedLogins
			))->execute($user_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			return true;
		}

		public function hide_nochar_info($user_id) {
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'hide_nochar_info'	=> 1
			))->execute($user_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			return true;
		}

		public function create_new_activationkey($user_id){
			// Create a new activation key
			$user_key = random_string(true, 32);

			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'user_key'	=> $user_key
			))->execute($user_id);
			
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			return $user_key;
		}

		public function create_new_exchangekey($user_id){
			$app_key = random_string(true, 32);
			
			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_id=?")->set(array(
					'exchange_key'	=> $app_key
			))->execute($user_id);
			
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('user');
			return $app_key;
		}
		
		public function add_special_user($user_id){
			$special_users = $this->config->get('special_user');
			if (!is_array($special_users)) $special_users = array();
			$special_users[$user_id] = $user_id;
			$this->config->set('special_user', serialize($special_users));
		}
		
		public function delete_special_user($user_id){
			$special_users = $this->config->get('special_user');
			if (!is_array($special_users)) return;
			
			if(isset($special_users[$user_id])) unset($special_users[$user_id]);
			$this->config->set('special_user', serialize($special_users));
		}

		public function delete_user($user_id, $delete_member = false) {
			
			//Delete Avatars
			$this->pfh->Delete('users/'.$user_id, 'files');
			$strAvatar = $this->pdh->get('user', 'avatarimglink', array($user_id));
			if (strlen($strAvatar)) $this->pfh->Delete($strAvatar);
			
			$log_action = array(
				'{L_USER}'		=> $this->pdh->get('user', 'name', array($user_id)),
				'{L_EMAIL}'		=> $this->pdh->get('user', 'email', array($user_id)),
			);	
			
			if ($delete_member){
				$members = $this->pdh->get('member', 'connection_id', array($user_id));
				foreach ($members as $member){
					$this->pdh->put('member', 'delete_member', array($member));
				}
			}
			
			$this->log_insert('action_user_deleted', $log_action, $user_id, $this->pdh->get('user', 'name', array($user_id)));
			
			$this->db->prepare("DELETE FROM __users WHERE user_id=?")->execute($user_id);
			$this->db->prepare("DELETE FROM __auth_users WHERE user_id=?")->execute($user_id);
			$this->db->prepare("DELETE FROM __groups_users WHERE user_id=?")->execute($user_id);
			$this->db->prepare("DELETE FROM __comments WHERE userid=?")->execute($user_id);
			
			$this->pdh->put('member', 'update_connection', array(array(), $user_id));
			$this->pdh->put('notifications', 'delete_by_user', array($user_id));
			
			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('user_groups_update');
			$this->pdh->enqueue_hook('comment_update');
			$this->pdh->enqueue_hook('member_update');
			$this->pdh->enqueue_hook('update_connection');
			$this->pdh->enqueue_hook('styles_update');
		}

		public function reset() {
			$this->db->prepare("DELETE FROM __users WHERE user_id !=?")->execute($this->user->id);
			$this->db->prepare("DELETE FROM __member_user WHERE user_id !=?")->execute($this->user->id);

			$this->pdh->enqueue_hook('user');
			$this->pdh->enqueue_hook('user_groups_update');
			$this->pdh->enqueue_hook('comment_update');
			$this->pdh->enqueue_hook('member_update');
			$this->pdh->enqueue_hook('update_connection');
			$this->pdh->enqueue_hook('styles_update');
		}
	}
}
?>