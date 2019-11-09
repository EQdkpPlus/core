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

if ( !class_exists( "bridge_usersync_crontask" ) ) {
	class bridge_usersync_crontask extends crontask {
		public $options = array(
				'delete_eqdkp_user'	=> array(
					'lang'	=> 'Delete EQdkp Plus user, that are not in CMS',
					'type'	=> 'radio',
				),
		);

		public function __construct(){
			$this->defaults['repeat']		= true;
			$this->defaults['repeat_type']	= 'daily';
			$this->defaults['editable']		= true;
			$this->defaults['delay']		= true;
			$this->defaults['ajax']			= true;
			$this->defaults['description']	= 'Bridge User Sync';
		}

		public function run(){
			$crons		= $this->cronjobs->list_crons();
			$params		= $crons['bridge_usersync']['params'];

			//Bridge not active, exit cronjob
			if(intval($this->config->get('cmsbridge_active')) === 0) return false;

			$a = $this->bridge->get_users();
			$arrUser = array();
			$arrCMSUsernames = array();
			foreach($a as $val){
				$id = intval($val['id']);
				$arrUser[] = $val;
				$arrCMSUsernames[] = clean_username($val['name']);
			}

			foreach($arrUser as $arrUserdata){
				if ($this->pdh->get('user', 'check_username', array(sanitize($arrUserdata['name']))) != 'false'){
					if(!$this->bridge->check_user_group($arrUserdata['id'])) continue;

					//Neu anlegen
					$strPassword = random_string(32);
					$strPwdHash = $this->user->encrypt_password($strPassword);

					$user_id = $this->pdh->put('user', 'insert_user_bridge', array(sanitize($arrUserdata['name']), $strPwdHash, $arrUserdata['email'], false));
					$this->pdh->process_hook_queue();
					//Sync Usergroups
					$this->bridge->sync_usergroups((int)$arrUserdata['id'], $user_id);
					//Sync Userdata
					$this->bridge->sync_fields($user_id, $arrUserdata);

					//Notify Admins
					$this->ntfy->add('eqdkp_user_new_registered', $user_id, sanitize($arrUserdata['name']), $this->root_path.'admin/manage_users.php'.$this->SID.'&u='.$user_id, false, "", false, array("a_users_man"));

				} else {
					$user_id = $this->pdh->get('user', 'userid', array(sanitize($arrUserdata['name'])));
					//Sync Usergroups
					$this->bridge->sync_usergroups((int)$arrUserdata['id'], $user_id);
					//Sync Userdata
					$this->bridge->sync_fields($user_id, $arrUserdata);
				}
			}

			//Delete EQdkp Plus User, except Admins and Superadmins
			if ((int)$params['delete_eqdkp_user'] == 1){
				$arrEQdkpUser = $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list', array(true))));
				foreach($arrEQdkpUser as $userid => $username){
					$username = clean_username($username);
					if (!in_array($username, $arrCMSUsernames)){
						if ($this->user->check_group(2, false, $userid) || $this->user->check_group(3, false, $userid)) continue;
						$this->pdh->put('user', 'delete_user', array($userid));
					}
				}
			}
		}
	}
}
