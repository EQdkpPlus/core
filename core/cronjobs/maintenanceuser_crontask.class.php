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

if ( !class_exists( "maintenanceuser_crontask" ) ) {
	class maintenanceuser_crontask extends crontask {

		public function __construct(){
			$this->defaults['description']	= 'Deleting Maintenance-user';
			$this->defaults['delay']		= false;
			$this->defaults['ajax']			= false;
		}

		public function run(){
			$muser = unserialize(stripslashes($this->encrypt->decrypt($this->config->get('maintenance_user'))));
			if ($muser['user_id']){
				$this->db->prepare("DELETE FROM __users WHERE user_id =?")->execute($muser['user_id']);
				
				$this->pdh->put('user_groups_users', 'delete_user_from_group', array($muser['user_id'], 2));
				
				$this->pdh->put('user', 'delete_special_user', array($muser['user_id']));
				$this->logs->add('action_maintenanceuser_deleted', array(), $muser['user_id'], $this->user->lang('maintenanceuser_user'));
			}
			$this->config->set('maintenance_user', '');
		}
	}
}
?>