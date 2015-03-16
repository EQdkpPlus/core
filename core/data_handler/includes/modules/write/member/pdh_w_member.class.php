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

if ( !class_exists( "pdh_w_member" ) ) {
	class pdh_w_member extends pdh_w_generic{
		private $empty_profile = array();

		private $arrLogLang = array(
			'name'			=> "{L_NAME}",
			'rank'			=> "{L_RANK}",
			'main'			=> "{L_MAINCHAR}",
			'status'		=> "{L_STATUS}",
			'notes'			=> "{L_NOTE}",
			'picture'		=> "Avatar",
		);

		public function addorupdate_member($member_id=0, $data=array(), $takechar=false) {
			$orig_member_id = $member_id;
			
			if($member_id > 0){
				$member_id	= ($this->pdh->get('member', 'name', array($member_id))) ? $member_id : false;
				//Check Unique
				$membername = (isset($data['name'])) ? $data['name'] : $this->pdh->get('member', 'name', array($member_id));
				$uniqueID = $this->pdh->get('member', 'id', array($membername, $data));
				if ($uniqueID && $member_id !== $uniqueID) return false;
			}else{
				$member_id	= $this->pdh->get('member', 'id', array($data['name'], $data));
				//Check Unique
				if ($member_id) return false;
			}

			// Update the member
			if($member_id > 0) {
				$old['name']			= $this->pdh->get('member', 'name', array($member_id));
				$old['rankid']			= $this->pdh->get('member', 'rankid', array($member_id));
				$old['mainid']			= $this->pdh->get('member', 'mainid', array($member_id));
				$old['status']			= $this->pdh->get('member', 'active', array($member_id));
				$old['notes']			= $this->pdh->get('member', 'note', array($member_id));
				$old['lastupdate']		= $this->pdh->get('member', 'last_update', array($member_id));
				$old['picture']			= $this->pdh->get('member', 'picture', array($member_id));
				$changes = false;
				foreach($old as $type => $val) {
					if(!isset($data[$type])){
						$data[$type] = $val;
					} elseif($data[$type] != $val) $changes = true;
				}
				if($data['mainid'] == 0) {
					$data['mainid'] = $this->pdh->get('member', 'mainid', array($member_id));
				}
				//check profile fields
				if(empty($data['profiledata'])) {
					$old['profiledata'] = $this->pdh->get('member', 'profiledata', array($member_id));
					$data['profiledata'] = $this->profilefields(array_merge($old['profiledata'], $data));
				}
				if($changes == false && $old['profiledata'] == $data['profiledata']) {
					return true;
				}
			//add new member
			} else {
				$data['mainid'] = ($takechar) ? $this->pdh->get('member','mainchar',array($this->user->data['user_id'])) : $data['mainid'];
				if(empty($data['profiledata'])) $data['profiledata'] = $this->profilefields($data);
			}

			//dont allow chars without a name
			if(empty($data['name'])) return false;

			$querystr = array(
				'member_name'		=> trim($data['name']),
				'member_rank_id'	=> (!empty($data['rankid']) || $data['rankid'] === '0' || $data['rankid'] === 0) ? $data['rankid'] : $this->pdh->get('rank', 'default', array()),
				'member_main_id'	=> $data['mainid'],
				'member_status'		=> isset($data['status']) ? $data['status'] : 1,
				'notes'				=> !empty($data['notes']) ? $data['notes'] : '',
				'profiledata'		=> $data['profiledata'],
				'last_update'		=> !empty($data['lastupdate']) ? $data['lastupdate'] : time(),
				'picture'			=> !empty($data['picture']) ? $data['picture'] : ''
			);
			
			if($member_id > 0) {
				$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id = ?;")->set($querystr)->execute($member_id);
				
				if($objQuery) {
					$arrOld = array(
						'name'			=> $old['name'],
						'rank'			=> $this->pdh->get('rank', 'name', array($old['rankid'])),
						'main'			=> $this->pdh->get('member', 'name', array($old['mainid'])),
						'status'		=> $old['status'],
						'notes'			=> $old['notes'],
						'picture'		=> $old['picture'],
					);
					
					$arrNew = array(
						'name'			=> $querystr['member_name'],
						'rank'			=> $this->pdh->get('rank', 'name', array($querystr['member_rank_id'])),
						'main'			=> $this->pdh->get('member', 'name', array($querystr['member_main_id'])),
						'status'		=> $querystr['member_status'],
						'notes'			=> $querystr['notes'],
						'picture'		=> $querystr['picture'],
					);
					
					
					$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);

					$this->log_insert('action_member_updated', $log_action, $member_id, $old['name']);
					$this->pdh->enqueue_hook('member_update', array($member_id));
					
					//Überprüfe Ringabhängigkeit von Mainchars
					$this->pdh->process_hook_queue();
					if ($member_id != $data['mainid']){
						if (($this->pdh->get('member', 'mainid', array($data['mainid'])) == $member_id) && ($this->pdh->get('member', 'mainid', array($member_id)) == $data['mainid'])){
							$this->change_mainid($data['mainid'], $data['mainid']);
						}
					}
					return $member_id;
				}
			} else {
				$querystr['member_creation_date'] = $this->current_time;
				
				//Add defaultrole if there is only one role for the class
				$arrRoles = $this->pdh->get('roles', 'memberroles', array($data['profiledata'][$this->game->get_primary_classes(true)]));
				$arrRoleIDs = array_keys($arrRoles);
				if (count($arrRoleIDs) == 1) $querystr['defaultrole'] = $arrRoleIDs[0];
				
				$objQuery = $this->db->prepare("INSERT INTO __members :p")->set($querystr)->execute();
				
				if(!$objQuery) {
					return false;
				}else{
					$member_id	= $objQuery->insertId;
					if ($takechar){
						$this->takeover($member_id);
					}
					
					$arrNew = array(
							'name'			=> $querystr['member_name'],
							'rank'			=> $this->pdh->get('rank', 'name', array($querystr['member_rank_id'])),
							'main'			=> $this->pdh->get('member', 'name', array($querystr['member_main_id'])),
							'status'		=> $querystr['status'],
							'notes'			=> $querystr['notes'],
							'picture'		=> $querystr['picture'],
					);
					$log_action = $this->logs->diff(false, $arrNew, $this->arrLogLang);
					$this->log_insert('action_member_added', $log_action, $member_id, $data['name']);
					$this->pdh->enqueue_hook('member_update', array($member_id));

					// Set main ID
					if(empty($data['mainid']) || $data['mainid'] == 0) {
						$this->change_mainid($member_id, $member_id);
					}

					// Check for main-ID ring dependency
					$this->pdh->process_hook_queue();
					if ($member_id != $data['mainid']){
						if (($this->pdh->get('member', 'mainid', array($data['mainid'])) == $member_id) && ($this->pdh->get('member', 'mainid', array($member_id)) == $data['mainid'])){
							$this->change_mainid($data['mainid'], $data['mainid']);
						}
					}	
					return $member_id;
				}
			}
			return false;
		}
		
		public function profilefields($fielddata=array()) {
			$prof_fields = $this->pdh->get('profile_fields', 'fieldlist');
			$myxml = array();
			foreach($fielddata as $pfname => $value){
				if(in_array($pfname, $prof_fields)){
					$myxml[$pfname] = $value;
				}
			}
			return json_encode($myxml);
		}

		public function delete_member($member_id, $no_log=false) {
			//get old data
			$old['name']	= $this->pdh->get('member', 'name', array($member_id));
			$old['rank']	= $this->pdh->get('member', 'rankname', array($member_id));
			$old['main']	= $this->pdh->get('member', 'mainname', array($member_id));
			$old['status']	= $this->pdh->get('member', 'active', array($member_id));
			
			$objQuery = $this->db->prepare("DELETE FROM __members WHERE member_id = ?;")->execute($member_id);
			
			if($objQuery) {
				if(!$no_log) {
					$log_action = array(
						'{L_NAME}' => $old['name'],
						'{L_RANK}' => $old['rank'],
						'{L_MAINC}' => $old['main'],
						'{L_STATUS}' => $old['status']
					);
					$this->log_insert('action_member_deleted', $log_action, $member_id, $old['name']);
				}
				//delete items of member
				$items = $this->pdh->get('item', 'itemids4memberid', array($member_id));
				foreach($items as $itemid) {
					$this->pdh->put('item', 'delete_item', array($itemid));
				}
				//delete adjustments of member
				$adjs = $this->pdh->get('adjustment', 'adjsofmember', array($member_id));

				foreach($adjs as $key => $adjid) {
					$this->pdh->put('adjustment', 'delete_adjustment', array($adjid));
				}
				// delete calendar raid attendees
				$this->pdh->put('calendar_raids_attendees', 'delete_attendees', array($member_id));
				// delete raid_attendence
				$this->db->prepare("DELETE FROM __raid_attendees WHERE member_id = ?")->execute($member_id);
				// delete member-user connection
				$this->db->prepare("DELETE FROM __member_user WHERE member_id = ?;")->execute($member_id);
				$this->pdh->enqueue_hook('member_update', array($member_id));
				$raids = $this->pdh->get('raid', 'raidids4memberid', array());
				$this->pdh->enqueue_hook('raid_update', $raids);
				// check for new mainchar
				$twinks = $this->pdh->get('member', 'other_members', array());
				if(!empty($twinks)) {
					$new_main = $twinks[0];
					$this->change_mainid($twinks,$new_main);
				}
				return true;
			}
			return false;
		}

		public function update_connection($member_id, $user_id=0){
			$user_id	= ($user_id == 0) ? $this->user->data['user_id'] : $user_id;
			$userchars = $this->pdh->get('member', 'connection_id', array($user_id));
			
			//Change Mainid of all associated chars
			foreach ($userchars as $charid){
					$this->change_mainid((int)$charid, (int)$charid);
			}
		
			// Users -> Members associations
			$this->db->prepare('DELETE FROM __member_user WHERE user_id = ?')->execute($user_id);

			if (is_array($member_id) && count($member_id) > 0){

				$query = array();
				foreach ( $member_id as $memberid ){
					$query[]	= array(
						'member_id' => $memberid,
						'user_id'	=> $user_id,
					);
				}
				
				$this->db->prepare("INSERT INTO __member_user :p")->set($query)->execute();

				$myupdate	= true;
				
				$this->pdh->enqueue_hook('member_update', $member_id);
				$this->pdh->process_hook_queue();
	
				//Change Mainids of associated chars
				$mainchar = $this->pdh->get('member', 'mainchar', array($user_id));

				if (!$mainchar){
					$mainchar = (int)$member_id[0];
				}

				$this->change_mainid($member_id, $mainchar);
				
			}else{
				$myupdate	= false;
			}
			$this->pdh->enqueue_hook('update_connection', array($member_id));
			return $myupdate;
		}

		public function confirm($member_id){
			$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id=?")->set(array(
				'member_status'		=> '1',
				'requested_del'		=> '0',
				'require_confirm'	=> '0',
			))->execute($member_id);
			
			$this->pdh->enqueue_hook('member_update');
			return true;
		}

		public function takeover($id){
			$objQuery = $this->db->prepare('INSERT INTO __member_user :p')->set(array(
				'member_id'		=> $id,
				'user_id'		=> $this->user->id
			))->execute();
			
			$this->pdh->enqueue_hook('member_update');
			return true;
		}

		public function suspend($member_id){
			if ($member_id == 'all'){
				$id_list = $this->pdh->get('member', 'id_list');
				if (count($id_list)){
					$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id :in")->set(array(
						'member_status' => 0,
						'requested_del' => 1,
					))->in($id_list)->execute();
				}
			} else {
				$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id=?")->set(array(
					'member_status' => 0,
					'requested_del' => 1,
				))->execute($member_id);
			}
			$this->pdh->enqueue_hook('member_update');
		}
		

		public function revoke($member_id){
			$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id=?")->set(array(
				'member_status' => 1,
				'requested_del' => 0,
			))->execute($member_id);

			$this->pdh->enqueue_hook('member_update');
		}

		public function change_mainid($member_id, $mainid){
			if(is_array($member_id)){
				$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id :in")->set(array(
						'member_main_id'	=> $mainid
						
				))->in($member_id)->execute();

			}else{
				$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id =?")->set(array(
						'member_main_id'	=> $mainid		
				))->execute($member_id);
			}
			$this->pdh->enqueue_hook('member_update');
		}

		public function update_profilefield($member_id, $data){
			if(empty($data['profiledata'])) {
				$old['profiledata'] = $this->pdh->get('member', 'profiledata', array($member_id));
				$data['profiledata'] = $this->profilefields(array_merge($old['profiledata'], $data));
			}
			$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id = ?;")->set(array('profiledata'=>$data['profiledata']))->execute($member_id);
			$this->pdh->enqueue_hook('member_update');
			return ($objQuery) ? true : false;
		}

		public function change_rank($member_id, $rankid){
			$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id =?")->set(array(
					'member_rank_id'	=> $rankid			
			))->execute($member_id);
			$this->pdh->enqueue_hook('member_update');
			return ($objQuery) ? true : false;
		}

		public function change_status($member_id, $status){
			$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id =?")->set(array(
					'member_status'	=> $status
			))->execute($member_id);
			$this->pdh->enqueue_hook('member_update');
			return ($objQuery) ? true : false;
		}
		
		public function change_defaultrole($member_id, $roleid){
			$objQuery = $this->db->prepare("UPDATE __members :p WHERE member_id =?")->set(array(
				'defaultrole'	=> $roleid
			))->execute($member_id);
			$this->pdh->enqueue_hook('member_update', array($member_id));
			return ($objQuery) ? true : false;
		}

		public function trans_member($fromid, $toid) {
			//raids
			//select raids of tomember
			$toid_raidids = $this->pdh->get('raid', 'raidids4memberid', $toid);
			$this->db->beginTransaction();

			$noraids_string = '';
			foreach($toid_raidids as $raid_id){
				$noraids_string .= " AND raid_id != '".intval($raid_id)."'";
			}
			$sql = "UPDATE __raid_attendees SET member_id = '".intval($toid)."' WHERE member_id = '".intval($fromid)."'".$noraids_string.";";
			if($this->db->query($sql)) {
				$objQuery = $this->db->prepare("UPDATE __adjustments :p  WHERE member_id=?")->set(array('member_id' => $toid))->execute($fromid);
				if($objQuery) {
					$objQuery = $this->db->prepare("UPDATE __items :p  WHERE member_id=?")->set(array('member_id' => $toid))->execute($fromid);
					if($objQuery) {
						$log_action = array(
							'{L_FROM}'	=> $this->pdh->get('member', 'name', array($fromid)),
							'{L_TO}'	=> $this->pdh->get('member', 'name', array($toid)),
						);
						$this->log_insert('action_history_transfer', $log_action, $fromid, $this->pdh->get('member', 'name', array($fromid)));
						$this->db->commitTransaction();
						$this->pdh->enqueue_hook('item_udpate');
						$this->pdh->enqueue_hook('adjustment_update');
						return true;
					}
				}
			}
			$this->db->rollbackTransaction();
			return false;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __members;");
			$this->db->query("TRUNCATE TABLE __member_user;");
			$this->pdh->enqueue_hook('member_update');
		}
	}//end class
}//end if
?>