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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_search')){
	class exchange_search extends gen_class{
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function get_search($params, $arrBody){
			$isAPITokenRequest = $this->pex->getIsApiTokenRequest();
			$out = array();

			if($isAPITokenRequest){
				$strSearchFor = (isset($params['get']['for'])) ? $params['get']['for'] : false;
				$strSearchIn = (isset($params['get']['in'])) ? $params['get']['in'] : 'charname';
				if($strSearchIn == 'username'){
					$arrUsers = $this->pdh->get('user', 'id_list', array(false));
					$strSearchValue = utf8_strtolower($strSearchFor);
					foreach($arrUsers as $intUserID){
						$strUsername = $this->pdh->get('user', 'name', array($intUserID));
						$strUsername = utf8_strtolower($strUsername);

						if($strUsername == $strSearchValue){
							$out['direct']['user:'.$intUserID] = array(
								'user_id' => $intUserID,
								'username'=> $this->pdh->get('user', 'name', array($intUserID)),
							);
						} elseif(stripos($strUsername, $strSearchValue) !== false){
							$out['relevant']['user:'.$intUserID] = array(
									'user_id' => $intUserID,
									'username'=> $this->pdh->get('user', 'name', array($intUserID)),
							);
						}
					}

					return $out;

				}elseif($strSearchIn == 'userid'){
					$strSearchValue = intval($strSearchFor);
					$strUsername = $this->pdh->get('user', 'name', array($strSearchValue));
					if($strUsername && $strUsername != ""){
						$out['direct']['user:'.$intUserID] = array(
								'user_id' => $strSearchValue,
								'username'=> $strUsername,
						);
					}

					return $out;
				}elseif($strSearchIn == 'charname'){
					$arrUsers = $this->pdh->get('member', 'id_list');
					$strSearchValue = utf8_strtolower($strSearchFor);
					foreach($arrUsers as $intUserID){
						$strUsername = $this->pdh->get('member', 'name', array($intUserID));
						$strUsername = utf8_strtolower($strUsername);

						$roles = $this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($intUserID))));
						if (is_array($roles)){
							$arrRoles = array();
							foreach ($roles as $roleid => $rolename){
								$arrRoles['role:'.$roleid] = array(
										'id'		=> $roleid,
										'name'		=> $rolename,
										'default'	=> ((int)$this->pdh->get('member', 'defaultrole', array($intUserID)) == $roleid) ? 1 : 0,
								);
							}
						}

						//Raidgroups
						$arrRaidgroups = array();
						$arrTotalRaidgroups = $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list')));
						if(count($arrTotalRaidgroups)){
							foreach($arrTotalRaidgroups as $raidgroupid => $raidgroupname) {
								$status = $this->pdh->get('raid_groups_members', 'membership_status', array($intUserID, $raidgroupid));
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

						//Profiledata
						$arrData = $this->pdh->get('member', 'profiledata', array($intUserID));


						if($strUsername == $strSearchValue){
							$out['direct']['member:'.$intUserID] = array(
									'id' 			=> $intUserID,
									'name'			=> $this->pdh->get('member', 'name', array($intUserID)),
									'name_export'	=> $this->game->handle_export_charnames($this->pdh->get('member', 'name', array($intUserID)), $intUserID),
									'main'			=> $this->pdh->get('member', 'is_main', array($intUserID)),
									'class'			=> $this->pdh->get('member', 'classid', array($intUserID)),
									'classname'		=> $this->pdh->get('member', 'classname', array($intUserID)),
									'roles'			=> $arrRoles,
									'raidgroups'	=> $arrRaidgroups,
									'profiledata'	=> $arrData,
							);
						} elseif(stripos($strUsername, $strSearchValue) !== false){
							$out['relevant']['member:'.$intUserID] = array(
									'id' 			=> $intUserID,
									'name'			=> $this->pdh->get('member', 'name', array($intUserID)),
									'name_export'	=> $this->game->handle_export_charnames($this->pdh->get('member', 'name', array($intUserID)), $intUserID),
									'main'			=> $this->pdh->get('member', 'is_main', array($intUserID)),
									'class'			=> $this->pdh->get('member', 'classid', array($intUserID)),
									'classname'		=> $this->pdh->get('member', 'classname', array($intUserID)),
									'roles'			=> $arrRoles,
									'raidgroups'	=> $arrRaidgroups,
									'profiledata'	=> $arrData,
							);
						}
					}

					return $out;
				}elseif($strSearchIn == 'charid'){
					$intUserID = intval($strSearchFor);
					$strMembername = $this->pdh->get('member', 'name', array($intUserID));
					if($strMembername && $strMembername != ""){
						$roles = $this->pdh->get('roles', 'memberroles', array($this->pdh->get('member', 'classid', array($intUserID))));
						if (is_array($roles)){
							$arrRoles = array();
							foreach ($roles as $roleid => $rolename){
								$arrRoles['role:'.$roleid] = array(
										'id'		=> $roleid,
										'name'		=> $rolename,
										'default'	=> ((int)$this->pdh->get('member', 'defaultrole', array($intUserID)) == $roleid) ? 1 : 0,
								);
							}
						}

						//Raidgroups
						$arrRaidgroups = array();
						$arrTotalRaidgroups = $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list')));
						if(count($arrTotalRaidgroups)){
							foreach($arrTotalRaidgroups as $raidgroupid => $raidgroupname) {
								$status = $this->pdh->get('raid_groups_members', 'membership_status', array($intUserID, $raidgroupid));
								if($status !== false){
									$status = $status+1;
								} else {
									$status = (count($arrTotalRaidgroups) === 1) ? 1 : 0;
								}

								$arrRaidgroups['raidgroup:'.$raidgroupid] = array(
										'id'			=> $raidgroupid,
										'name'			=> $raidgroupname,
										'default'		=> ($this->pdh->get('raid_groups', 'standard', array($raidgroupid))) ? 1 : 0,
										'color'			=> $this->pdh->get('raid_groups', 'color', array($raidgroupid)),
										'status'		=> $status,
										'profiledata'	=> $arrData,
								);
							}
						}

						//Profiledata
						$arrData = $this->pdh->get('member', 'profiledata', array($intUserID));

						$out['direct']['member:'.$intUserID] = array(
								'id' 			=> $intUserID,
								'name'			=> $this->pdh->get('member', 'name', array($intUserID)),
								'name_export'	=> $this->game->handle_export_charnames($this->pdh->get('member', 'name', array($intUserID)), $intUserID),
								'main'			=> $this->pdh->get('member', 'is_main', array($intUserID)),
								'class'			=> $this->pdh->get('member', 'classid', array($intUserID)),
								'classname'		=> $this->pdh->get('member', 'classname', array($intUserID)),
								'roles'			=> $arrRoles,
								'raidgroups'	=> $arrRaidgroups,
								'profiledata'	=> $arrData,
						);

					}
				}

			} else {
				return $this->pex->error('access denied');
			}

			return $out;
		}
	}
}
