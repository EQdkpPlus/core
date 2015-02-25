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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_user_chars')){
	class exchange_user_chars extends gen_class{
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function get_user_chars($params, $body){
			if ($this->user->check_auth('u_calendar_view', false)){
				$userid = (intval($params['get']['userid']) > 0) ? intval($params['get']['userid']) : $this->user->data['user_id'];
				//UserChars
				$user_chars = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'connection_id', array($userid))));
				$mainchar = $this->pdh->get('user', 'mainchar', array($userid));
				$arrRoles = array();
				if (is_array($user_chars)){
					foreach ($user_chars as $key=>$charname){
						$roles = $this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($key))));
						if (is_array($roles)){
							$arrRoles = array();
							foreach ($roles as $roleid => $rolename){
								$arrRoles['role:'.$roleid] = array(
									'id'	=> $roleid,
									'name'	=> $rolename,
									'default'	=> ((int)$this->pdh->get('member', 'defaultrole', array($key)) == $roleid) ? 1 : 0,
								);
							}
						}
						
						//Raidgroups
						$arrRaidgroups = array();
						$arrTotalRaidgroups = $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list')));
						if(count($arrTotalRaidgroups)){
							foreach($arrTotalRaidgroups as $raidgroupid => $raidgroupname) {
								$status = $this->pdh->get('raid_groups_members', 'membership_status', array($key, $raidgroupid));
								if($status !== false){
									$status = $status+1;
								} else {
									$status = (count($arrTotalRaidgroups) === 1) ? 1 : 0;
								}								
								
								$arrRaidgroups['raidgroup:'.$raidgroupid] = array(
										'id'		=> $raidgroupid,
										'name'		=> $raidgroupname,
										'default'	=> ($this->pdh->get('raid_groups', 'standard', array($raidgroupid))) ? 1 : 0,
										'color'		=> $this->pdh->get('raid_groups', 'color', array($raidgroupid)),
										'status'	=> $status,
								);
							}
						}

						$arrUserChars['char:'.$key] = array(
							'id'			=> $key,
							'name'			=> unsanitize($charname),
							'main'			=> ($key == $mainchar) ? 1 : 0,
							'class'			=> $this->pdh->get('member', 'classid', array($key)),
							'classname'		=> $this->pdh->get('member', 'classname', array($key)),
							'roles'			=> $arrRoles,
							'raidgroups'	=> $arrRaidgroups,
						);
					}
				}
				$out['chars'] = $arrUserChars;
				return $out;
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>