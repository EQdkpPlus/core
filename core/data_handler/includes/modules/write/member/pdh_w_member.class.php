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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_w_member" ) ) {
	class pdh_w_member extends pdh_w_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'in', 'user', 'config', 'xmltools'=>'xmltools', 'game');
		return array_merge(parent::$shortcuts, $shortcuts);
	}
		private $empty_profile = array();

		public function __construct(){
			parent::__construct();
		}

		public function addorupdate_member($member_id=0, $data=array(), $takechar=false) {
			if($member_id > 0){
				$member_id	= ($this->pdh->get('member', 'name', array($member_id))) ? $member_id : false;
			}else{
				$member_id	= $this->pdh->get('member', 'id', array($data['name']));
			}

			// Update the member
			if($member_id > 0) {
				$old['name']			= $this->pdh->get('member', 'name', array($member_id));
				$old['lvl']				= $this->pdh->get('member', 'level', array($member_id));
				$old['raceid']			= $this->pdh->get('member', 'raceid', array($member_id));
				$old['classid']			= $this->pdh->get('member', 'classid', array($member_id));
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
					$data['profiledata'] = $this->profilefields(array_merge($this->xmltools->Database2Array($old['profiledata']), $data));
				}
				if($changes == false && $old['profiledata'] == $data['profiledata']) {
					return true;
				}
			//add new member
			} else {
				if($takechar) {
					$data['mainid'] = $this->pdh->get('member','mainchar',array($this->user->data['user_id']));
				}
				if(empty($data['mainid']) || $data['mainid'] == 0) {
					$table_infos = $this->db->get_table_information('__members');
					$data['mainid'] = $table_infos['auto_increment'];
				}
				if(empty($data['profiledata'])) $data['profiledata'] = $this->profilefields($data);
			}
			
			
			//dont allow chars without a name
			if(empty($data['name'])) return false;
			$querystr = array(
				'member_name'		=> trim($data['name']),
				'member_level'		=> !empty($data['lvl']) ? $data['lvl'] : 0,
				'member_race_id'	=> !empty($data['raceid']) ? $data['raceid'] : 0,
				'member_class_id'	=> !empty($data['classid']) ? $data['classid'] : 0,
				'member_rank_id'	=> !empty($data['rankid']) ? $data['rankid'] : $this->pdh->get('rank', 'default', array()),
				'member_main_id'	=> $data['mainid'],
				'member_status'		=> isset($data['status']) ? $data['status'] : 1,
				'notes'				=> !empty($data['notes']) ? $data['notes'] : '',
				'profiledata'		=> $data['profiledata'],
				'last_update'		=> !empty($data['lastupdate']) ? $data['lastupdate'] : time(),
				'picture'			=> !empty($data['picture']) ? $data['picture'] : ''
			);
			if($member_id > 0) {
				if($this->db->query("UPDATE __members SET :params WHERE member_id = ?;", $querystr, $member_id)) {
					$log_action = array(
						'{L_ID}'				=> $member_id,
						'{L_NAME_BEFORE}'		=> $old['name'],
						'{L_LEVEL_BEFORE}'		=> $old['lvl'],
						'{L_RACE_BEFORE}'		=> $this->game->get_name('races', $old['raceid']),
						'{L_CLASS_BEFORE}'		=> $this->game->get_name('races', $old['classid']),
						'{L_RANK_BEFORE}'		=> $this->pdh->get('rank', 'name', array($old['rankid'])),
						'{L_MAINC_BEFORE}'		=> $this->pdh->get('member', 'name', array($old['mainid'])),
						'{L_STATUS_BEFORE}'		=> $old['status'],
						'{L_NAME_AFTER}'		=> ($old['name'] != $data['name']) ? '<span class=\"negative\">'.$data['name'].'</span>' : $data['name'],
						'{L_LEVEL_AFTER}'		=> ($old['lvl'] != $data['lvl']) ? '<span class=\"negative\">'.$data['lvl'] : $data['lvl'],
						'{L_RACE_AFTER}'		=> ($old['raceid'] != $data['raceid']) ? '<span class=\"negative\">'.$this->game->get_name('races', $data['raceid']).'</span>' : $this->game->get_name('races', $old['raceid']),
						'{L_CLASS_AFTER}'		=> ($old['classid'] != $data['classid']) ? '<span class=\"negative\">'.$this->game->get_name('classes', $data['classid']).'</span>' : $this->game->get_name('races', $old['classid']),
						'{L_RANK_AFTER}'		=> ($old['rankid'] != $data['rankid']) ? '<span class=\"negative\">'.$this->pdh->get('rank', 'name', array($data['rankid'])).'</span>' : $this->pdh->get('rank', 'name', array($old['rankid'])),
						'{L_MAINC_AFTER}'		=> ($old['mainid'] != $data['mainid']) ? '<span class=\"negative\">'.$this->pdh->get('member', 'name', array($data['mainid'])).'</span>' : $this->pdh->get('member', 'name', array($old['mainid'])),
						'{L_STATUS_AFTER}'		=> ($old['status'] != $data['status']) ? '<span class=\"negative\">'.$data['status'].'</span>' : $old['status'],
					);

					$this->log_insert('action_member_updated', $log_action);
					$this->pdh->enqueue_hook('member_update', array($member_id));
					return $member_id;
				}
			} else {
				$querystr['member_creation_date'] = $this->current_time;
				if(!$this->db->query("INSERT INTO __members :params", $querystr)) {
					return false;
				}else{
					$member_id	= $this->db->insert_id();
					if ($takechar){
						$this->pdh->put('member', 'takeover', array($member_id));
					}
					$log_action = array(
						'{L_NAME}'		=> $data['name'],
						'{L_LEVEL}'		=> $data['lvl'],
						'{L_RACE}'		=> $this->game->get_name('races', $data['raceid']),
						'{L_CLASS}'		=> $this->game->get_name('classes', $data['classid']),
						'{L_STATUS}'	=> !empty($data['status']) ? $data['status'] : 1,
					);
					if($member_id != $data['mainid']) $log_action['{L_MAINC}'] = $this->pdh->get('member', 'name', array($data['mainid']));
					$this->log_insert('action_member_added', $log_action);
					$this->pdh->enqueue_hook('member_update', array($member_id));
					return $member_id;
				}
			}
			return false;
		}
		
		public function profilefields($fielddata=array()) {
			$prof_fields = $this->pdh->get('profile_fields', 'fieldlist');
			$myxml = array();
			foreach($fielddata as $pfname => $value){
				$myxml[$pfname] = in_array($pfname, $prof_fields) ? $value : '';
			}
			return $this->xmltools->Array2Database($myxml);
		}

		public function delete_member($member_id, $no_log=false) {
			//get old data
			$old['name']	= $this->pdh->get('member', 'name', array($member_id));
			$old['lvl']		= $this->pdh->get('member', 'level', array($member_id));
			$old['race']	= $this->pdh->get('member', 'racename', array($member_id));
			$old['class']	= $this->pdh->get('member', 'classname', array($member_id));
			$old['rank']	= $this->pdh->get('member', 'rankname', array($member_id));
			$old['main']	= $this->pdh->get('member', 'mainname', array($member_id));
			$old['status']	= $this->pdh->get('member', 'active', array($member_id));

			if($this->db->query("DELETE FROM __members WHERE member_id = ?;", false, $member_id)) {
				if(!$no_log) {
					$log_action = array(
						'{L_NAME}' => $old['name'],
						'{L_LEVEL}' => $old['lvl'],
						'{L_RACE}' => $old['race'],
						'{L_CLASS}' => $old['class'],
						'{L_RANK}' => $old['rank'],
						'{L_MAINC}' => $old['main'],
						'{L_STATUS}' => $old['status']
					);
					$this->log_insert('action_member_deleted', $log_action);
				}
				//delete items of member
				$items = $this->pdh->get('item', 'itemids4memberid', array($member_id));
				foreach($items as $id => $det) {
					$this->pdh->put('item', 'delete_item', array($id));
				}
				//delete adjustments of member
				$adjs = $this->pdh->get('adjustment', 'adjsofmember', array($member_id));
				foreach($adjs as $id => $det) {
					$this->pdh->put('adjustment', 'delete_adjustment', array($id));
				}
				// delete calendar raid attendees
				$this->pdh->put('calendar_raids_attendees', 'delete_attendees', array($member_id));
				//delete raid_attendence
				$this->db->query("DELETE FROM __raid_attendees WHERE member_id = ?", false, $member_id);
				//delete member-user connection
				$this->db->query("DELETE FROM __member_user WHERE member_id = ?;",  false, $member_id);
				$this->pdh->enqueue_hook('member_update', array($member_id));
				$raids = $this->pdh->get('raid', 'raidids4memberid', array($member_id));
				$this->pdh->enqueue_hook('raid_update', $raids);
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
			$this->db->query('DELETE FROM __member_user WHERE user_id = ?', false, $user_id);

			if (is_array($member_id) && count($member_id) > 0){
				$sql = 'INSERT INTO __member_user
						(member_id, user_id)
						VALUES ';
				$query = array();
				foreach ( $member_id as $memberid ){
					$query[]	= '('.$this->db->escape($memberid).', '.$this->db->escape($user_id).')';
				}
				$sql	.= implode(', ', $query);
				$this->db->query($sql);
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
			$this->db->query("UPDATE __members SET :params WHERE member_id=?", array(
				'member_status'		=> '1',
				'requested_del'		=> '0',
				'require_confirm'	=> '0',
			), $member_id);
			$this->pdh->enqueue_hook('member_update');
			return true;
		}

		public function takeover($id){
			$this->db->query('INSERT INTO __member_user :params', array(
				'member_id'		=> $id,
				'user_id'		=> $this->user->data['user_id']
			));
			$this->pdh->enqueue_hook('member_update');
			return true;
		}

		public function suspend($member_id){
			if ($member_id == 'all'){
				$this->db->query("UPDATE __members SET :params", array(
				'member_status' => 0,
				'requested_del' => 1,
				));
			} else {
				$this->db->query("UPDATE __members SET :params WHERE member_id=?", array(
					'member_status' => 0,
					'requested_del' => 1,
				), $member_id);
			}
			$this->pdh->enqueue_hook('member_update');
		}
		

		public function revoke($member_id){
			$this->db->query("UPDATE __members SET :params WHERE member_id=?", array(
				'member_status' => 1,
				'requested_del' => 0,
			), $member_id);
			$this->pdh->enqueue_hook('member_update');
		}

		public function change_mainid($member_id, $mainid){
			if(is_array($member_id)){
				$this->db->query("UPDATE __members SET :params WHERE member_id IN(".implode(',', $member_id).");", array('member_main_id'	=> $mainid));
			}else{
				$this->db->query("UPDATE __members SET :params WHERE member_id = ?;", array('member_main_id'	=> $mainid), $member_id);
			}
			$this->pdh->enqueue_hook('member_update');
		}

		public function change_rank($member_id, $rankid){
			$result = $this->db->query("UPDATE __members SET :params WHERE member_id = ?;", array('member_rank_id'	=> $rankid), $member_id);
			$this->pdh->enqueue_hook('member_update');
			return $result;
		}

		public function change_status($member_id, $status){
			$result = $this->db->query("UPDATE __members SET :params WHERE member_id = ?;", array('member_status'	=> $status), $member_id);
			$this->pdh->enqueue_hook('member_update');
			return $result;
		}
		
		public function change_defaultrole($member_id, $roleid){
			$this->db->query("UPDATE __members SET :params WHERE member_id = ?;", array('defaultrole'	=> $roleid), $member_id);
			$this->pdh->enqueue_hook('member_update', array($member_id));
		}

		public function trans_member($fromid, $toid) {
			//raids
			//select raids of tomember
			$toid_raidids = $this->pdh->get('raid', 'raidids4memberid', $toid);
			$this->db->query("START TRANSACTION;");
			$noraids_string = '';
			foreach($toid_raidids as $raid_id){
				$noraids_string .= " AND raid_id != '".$this->db->escape($raid_id)."'";
			}
			$sql = "UPDATE __raid_attendees SET member_id = '".$this->db->escape($toid)."' WHERE member_id = '".$this->db->escape($fromid)."'".$noraids_string.";";
			if($this->db->query($sql)) {
				$sql = "UPDATE __adjustments SET member_id = '".$this->db->escape($toid)."' WHERE member_id = '".$this->db->escape($fromid)."';";
				if($this->db->query($sql)) {
					$sql = "UPDATE __items SET member_id = '".$this->db->escape($toid)."' WHERE member_id = '".$this->db->escape($fromid)."';";
					if($this->db->query($sql)) {
						if($this->delete_member($fromid, true)) {
							$log_action = array(
								'{L_FROM}'	=> $this->pdh->get('member', 'name', array($fromid)),
								'{L_TO}'	=> $this->pdh->get('member', 'name', array($toid)),
							);
							$this->log_insert('action_history_transfer', $log_action);
							$this->db->query("COMMIT;");
							$this->pdh->enqueue_hook('item_udpate');
							$this->pdh->enqueue_hook('adjustment_update');
							return true;
						}
					}
				}
			}
			$this->db->query("ROLLBACK;");
			return false;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __members;");
			$this->db->query("TRUNCATE TABLE __member_user;");
			$this->pdh->enqueue_hook('member_update');
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_member', pdh_w_member::__shortcuts());
?>
