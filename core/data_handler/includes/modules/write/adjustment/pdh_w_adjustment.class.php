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

if ( !class_exists( "pdh_w_adjustment" ) ){
	class pdh_w_adjustment extends pdh_w_generic{
		public static function __shortcuts() {
			$shortcuts = array('pdh', 'db', 'game');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

		public function __construct(){
			parent::__construct();
		}

		public function add_adjustment($adjustment_value, $adjustment_reason, $member_ids, $event_id, $raid_id=NULL, $time=false, $group_key = null) {
			$time = ($time) ? $time : time();
			$group_key = ($group_key == null) ? $this->gen_group_key($time, array($adjustment_reason, $adjustment_value, $event_id, $raid_id)) : $group_key;
			$ids = array();
			if(!empty($member_ids) && !is_array($member_ids)) $member_ids = array($member_ids);
			foreach($member_ids as $member_id){
				if(!$this->db->query('INSERT INTO __adjustments :params', array(
						'adjustment_value'			=> $adjustment_value,
						'adjustment_date'			=> $time,
						'member_id'					=> $member_id,
						'event_id'					=> $event_id,
						'adjustment_reason'			=> $this->db->escape($adjustment_reason),
						'raid_id'					=> $raid_id,
						'adjustment_group_key'		=> $group_key,
						'adjustment_added_by'		=> $this->admin_user))){
					return false;
				}
				$ids[] = $this->db->insert_id();
			}

			$member_names = $this->pdh->aget('member', 'name', 0, array($member_ids));
			$log_action = array(
				'{L_ADJUSTMENT}'	=> $adjustment_value,
				'{L_REASON}'		=> $adjustment_reason,
				'{L_MEMBERS}'		=> implode(', ', $member_names),
				'{L_EVENT}'			=> $this->pdh->get('event', 'name', $event_id),
				'{L_ADDED_BY}'		=> $this->admin_user
			);
			$this->log_insert('action_indivadj_added', $log_action);
			$this->pdh->enqueue_hook('adjustment_update', $ids);
			return $ids;
		}

		public function update_adjustment($group_key_or_id, $adj_value, $adj_reason, $member_ids, $event_id, $raid_id=NULL, $time=false, $id=false, $recalculate_group_key = true){
			$time = ($time) ? $time : time();
			$new_group_key = $this->gen_group_key($time, array($adj_reason, $adj_value, $event_id, $raid_id));
			
			//fetch old-data
			$group_key = (!$id) ? $group_key_or_id : $this->pdh->get('adjustment', 'group_key', array($group_key_or_id));
			if($recalculate_group_key == false) $new_group_key = $group_key;  
			$old['ids'] = $this->pdh->get('adjustment', 'ids_of_group_key', array($group_key));
			foreach($old['ids'] as $adjustment_id){
				$old['members'][$adjustment_id] = $this->pdh->get('adjustment', 'member', array($adjustment_id));
			}
			$old['value'] = $this->pdh->get('adjustment', 'value', array($adjustment_id));
			$old['reason'] = $this->pdh->get('adjustment', 'reason', array($adjustment_id));
			$old['event'] = $this->pdh->get('adjustment', 'event', array($adjustment_id));

			$retu = array(true);
			$updated_mems = array();
			$added_mems = array();
			$adjs2del = $old['members'];
			pd($member_ids);
			pd($old['members']);
			$log_action['{L_GROUP_KEY}'] = $new_group_key;
			foreach($member_ids as $member_id){
				$adj_id = array_search($member_id, $old['members']);
				if($adj_id !== false){
					$updated_mems[] = $member_id;
					$hook_id[] = $adj_id;
					unset($adjs2del[$adj_id]);
					$arrSet = array(
						'member_id' 			=> $member_id,
						'adjustment_value'		=> $adj_value,
						'adjustment_reason' 	=> $adj_reason,
						'event_id' 				=> $event_id,
						'raid_id' 				=> $raid_id,
						'adjustment_date'		=> $time,
						'adjustment_group_key'	=> $new_group_key,
						'adjustment_updated_by' => $this->admin_user,
						
					);
					if(!$this->db->query("UPDATE __adjustments SET :params WHERE adjustment_id = ?", $arrSet, $adj_id)){
						$retu[] = false;
						break;
					}
				}else{
					$added_mems[] = $member_id;
					$arrSet = array(
						'member_id' 			=> $member_id,
						'adjustment_value'		=> $adj_value,
						'adjustment_reason' 	=> $adj_reason,
						'event_id' 				=> $event_id,
						'raid_id' 				=> $raid_id,
						'adjustment_date'		=> $time,
						'adjustment_group_key'	=> $new_group_key,
						'adjustment_added_by'	=> $this->admin_user,		
					);

					if(!$this->db->query("INSERT INTO __adjustments :params", $arrSet)){
						$retu[] = false;
						break;
					}
				}
			}
			if(is_array($adjs2del)){
				foreach($adjs2del as $adj_id => $unimportant){
					if(!$this->db->query("DELETE FROM __adjustments WHERE adjustment_id = ?;", false, $adj_id)){
						$retu[] = false;
						break;
					}
					$hook_id[] = $adj_id;
				}
			}
			if(!in_array(false, $retu)){
				$old_names = $this->pdh->aget('member', 'name', 0, array($old['members']));
				$member_string = get_coloured_names($updated_mems, $added_mems, $adjs2del);
				// Logging
				$log_action = array(
					'{L_ADJUSTMENT_BEFORE}'	=> $old['value'],
					'{L_REASON_BEFORE}'		=> $old['reason'],
					'{L_MEMBERS_BEFORE}'	=> implode(', ', $old_names),
					'{L_EVENT_BEFORE}'		=> $this->pdh->get('event', 'name', array($old['event'])),
					'{L_ADJUSTMENT_AFTER}'	=> ($old['value'] != $adj_value) ? "<span class=\"negative\">".$adj_value."</span>" : $adj_value,
					'{L_REASON_AFTER}'		=> ($old['reason'] != $adj_reason) ? "<span class=\"negative\">".$adj_reason."</span>" : $adj_reason,
					'{L_MEMBERS_AFTER}'		=> $member_string,
					'{L_EVENT_AFTER}'		=> ($old['event'] != $event_id) ? "<span class=\"negative\">".$this->pdh->get('event', 'name', array($event_id))."</span>" : $this->pdh->get('event', 'name', array($event_id)),
					'{L_UPDATED_BY}'		=> $this->admin_user
				);
				$this->log_insert('action_indivadj_updated', $log_action);
				$this->pdh->enqueue_hook('adjustment_update', $hook_id);
				return true;
			}
			return false;
		}

		public function delete_adjustment($adjustment_id){
			//fetch old-data
			$old['member'] = $this->pdh->get('adjustment', 'member', array($adjustment_id));
			$old['value'] = $this->pdh->get('adjustment', 'value', array($adjustment_id));
			$old['reason'] = $this->pdh->get('adjustment', 'reason', array($adjustment_id));
			$old['event'] = $this->pdh->get('adjustment', 'event', array($adjustment_id));

			if($this->db->query("DELETE FROM __adjustments WHERE adjustment_id = ?;", false, $adjustment_id)){
				//insert log
				$log_action = array(
					'{L_ID}'		=> $adjustment_id,
					'{L_MEMBER}'	=> $this->pdh->get('member', 'name', array($old['member'])),
					'{L_VALUE}'		=> $old['value'],
					'{L_REASON}'	=> $old['reason'],
					'{L_EVENT}'		=> $old['event']
				);
				$this->log_insert('action_indivadj_deleted', $log_action);
				$this->pdh->enqueue_hook('adjustment_update', $adjustment_id);
				return true;
			}
			return false;
		}

		public function delete_adjustments_by_group_key($group_key){
			$adj_ids = $this->pdh->get('adjustment', 'ids_of_group_key', array($group_key));
			if($this->db->query("DELETE FROM __adjustments WHERE adjustment_group_key = ?;", false, $group_key)){
				//insert log
				$log_action = array(
					'group_key'	=> $group_key,
				);
				$this->log_insert('action_indivadj_deleted', $log_action);
				$this->pdh->enqueue_hook('adjustment_update', $adj_ids);
				return true;
			}
			return false;
		}
		
		public function delete_adjustmentsofraid($raid_id) {
			$adjs = $this->pdh->get('adjustment', 'adjsofraid', array($raid_id));
			if(count($adjs) < 1) return true;
			$this->db->query("DELETE FROM __adjustments WHERE adjustment_id IN ('".implode("', '", $adjs)."');");
			$log_action = array(
				'{L_ID}'		=> implode(', ', $adjs),
				'{L_RAID_ID}'	=> $raid_id,
			);
			$this->log_insert('action_indivadjofraid_deleted', $log_action);
			$this->pdh->enqueue_hook('adjustment_update', $adjs);
			return true;
		}
		
		public function delete_adjustmentsofevent($event_id) {
			$adjs = $this->pdh->get('adjustment', 'adjsofeventid', array($event_id));
			if(count($adjs) < 1) return true;
			$this->db->query("DELETE FROM __adjustments WHERE adjustment_id IN ('".implode("', '", $adjs)."');");
			$log_action = array(
				'{L_ID}'		=> implode(', ', $adjs),
				'{L_EVENT_ID}'	=> $event_id,
			);
			$this->log_insert('action_indivadjofevent_deleted', $log_action);
			$this->pdh->enqueue_hook('adjustment_update', $adjs);
			return true;
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __adjustments;");
			$this->pdh->enqueue_hook('adjustment_update');
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_adjustment', pdh_w_adjustment::__shortcuts());
?>