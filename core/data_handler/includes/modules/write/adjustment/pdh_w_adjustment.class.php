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

		private $arrLogLang = array(
				'value' 	=> '{L_ADJUSTMENT}',
				'reason'	=> '{L_REASON}',
				'members'	=> '{L_MEMBERS}',
				'event'		=> '{L_EVENT}',
				'raid'		=> '{L_RAID}',
		);
		
		public function add_adjustment($adjustment_value, $adjustment_reason, $member_ids, $event_id, $raid_id=NULL, $time=false, $group_key = null) {
			$time = ($time) ? $time : $this->time->time;
			$group_key = ($group_key == null) ? $this->gen_group_key($time, array($adjustment_reason, $adjustment_value, $event_id, $raid_id)) : $group_key;
			$ids = array();
			if(!empty($member_ids) && !is_array($member_ids)) $member_ids = array($member_ids);
			foreach($member_ids as $member_id){
				$objQuery = $this->db->prepare('INSERT INTO __adjustments :p')->set(array(
						'adjustment_value'			=> $adjustment_value,
						'adjustment_date'			=> $time,
						'member_id'					=> $member_id,
						'event_id'					=> $event_id,
						'adjustment_reason'			=> $adjustment_reason,
						'raid_id'					=> $raid_id,
						'adjustment_group_key'		=> $group_key,
						'adjustment_added_by'		=> $this->admin_user
				))->execute();
				
				if(!$objQuery){
					return false;
				}
				$ids[] = $objQuery->insertId;
			}

			$member_names = $this->pdh->aget('member', 'name', 0, array($member_ids));
			
			$log_action = array(
				'{L_ADJUSTMENT}'	=> $adjustment_value,
				'{L_REASON}'		=> $adjustment_reason,
				'{L_MEMBERS}'		=> implode(', ', $member_names),
				'{L_EVENT}'			=> $this->pdh->get('event', 'name', $event_id),
				'{L_RAID}'			=> $raid_id,
			);
			$this->log_insert('action_indivadj_added', $log_action, $group_key, $adjustment_reason);
			$this->pdh->enqueue_hook('adjustment_update', $ids);
			return $ids;
		}

		public function update_adjustment($group_key_or_id, $adj_value, $adj_reason, $member_ids, $event_id, $raid_id=NULL, $time=false, $id=false, $recalculate_group_key = true){
			$time = ($time) ? $time : $this->time->time;
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
					$objQuery = $this->db->prepare("UPDATE __adjustments :p WHERE adjustment_id = ?")->set($arrSet)->execute($adj_id);
					if(!$objQuery){
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
					$objQuery = $this->db->prepare("INSERT INTO __adjustments :p")->set($arrSet)->execute();
					
					if(!$objQuery){
						$retu[] = false;
						break;
					}
				}
			}
			if(is_array($adjs2del)){
				foreach($adjs2del as $adj_id => $unimportant){
					if(!$this->db->prepare("DELETE FROM __adjustments WHERE adjustment_id = ?;")->execute($adj_id)){
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
				$arrOld = array(
					'value'		=> $old['value'],
					'reason'	=> $old['reason'],
					'members'	=> implode(', ', $old_names),
					'event'		=> $this->pdh->get('event', 'name', array($old['event'])),
					'raid'		=> $this->pdh->get('adjustment', 'raid_id', array($adjustment_id)),
				);
				
				$arrNew = array(
					'value'		=> $adj_value,
					'reason'	=> $adj_reason,
					'members'	=> $member_string,
					'event'		=> $this->pdh->get('event', 'name', array($event_id)),
					'raid'		=> $raid_id,
				);
				
				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
				
				$this->log_insert('action_indivadj_updated', $log_action, $new_group_key, $old['reason']);
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
			$old['raid'] = $this->pdh->get('adjustment', 'raid_id', array($adjustment_id));
			
			if($this->db->prepare("DELETE FROM __adjustments WHERE adjustment_id = ?;")->execute($adjustment_id)){
				//insert log
				$log_action = array(
					'{L_MEMBER}'	=> $this->pdh->get('member', 'name', array($old['member'])),
					'{L_VALUE}'		=> $old['value'],
					'{L_REASON}'	=> $old['reason'],
					'{L_EVENT}'		=> $old['event'],
					'{L_RAID}'		=> $old['raid'],
				);
				$this->log_insert('action_indivadj_deleted', $log_action, $adjustment_id, $old['reason']);
				$this->pdh->enqueue_hook('adjustment_update', $adjustment_id);
				return true;
			}
			return false;
		}

		public function delete_adjustments_by_group_key($group_key){
			$adj_ids = $this->pdh->get('adjustment', 'ids_of_group_key', array($group_key));
			foreach($adj_ids as $adjustment_id){
				$old['member'][] = $this->pdh->get('adjustment', 'member', array($adjustment_id));
				$old['value'] = $this->pdh->get('adjustment', 'value', array($adjustment_id));
				$old['reason'] = $this->pdh->get('adjustment', 'reason', array($adjustment_id));
				$old['event'] = $this->pdh->get('adjustment', 'event', array($adjustment_id));
				$old['raid'] = $this->pdh->get('adjustment', 'raid_id', array($adjustment_id));
			}
			
			if($this->db->prepare("DELETE FROM __adjustments WHERE adjustment_group_key = ?")->execute($group_key)){
				
				$old_names = $this->pdh->aget('member', 'name', 0, array($old['member']));
				
				//insert log
				$log_action = array(
					'{L_MEMBER}'	=> implode(', ', $old_names),
					'{L_VALUE}'		=> $old['value'],
					'{L_REASON}'	=> $old['reason'],
					'{L_EVENT}'		=> $old['event'],
					'{L_RAID}'		=> $old['raid'],
				);
				
				$this->log_insert('action_indivadj_deleted', $log_action, $group_key, $old['reason']);
				$this->pdh->enqueue_hook('adjustment_update', $adj_ids);
				return true;
			}
			return false;
		}
		
		public function delete_adjustmentsofraid($raid_id) {
			$adjs = $this->pdh->get('adjustment', 'adjsofraid', array($raid_id));
			if(count($adjs) < 1) return true;
			$this->db->prepare("DELETE FROM __adjustments WHERE adjustment_id :in;")->in($adjs)->execute();
			$log_action = array(
				'{L_ID}'		=> implode(', ', $adjs),
				'{L_RAID_ID}'	=> $raid_id,
			);
			$this->log_insert('action_indivadjofraid_deleted', $log_action, $raid_id);
			$this->pdh->enqueue_hook('adjustment_update', $adjs);
			return true;
		}
		
		public function delete_adjustmentsofevent($event_id) {
			$adjs = $this->pdh->get('adjustment', 'adjsofeventid', array($event_id));
			if(count($adjs) < 1) return true;
			$this->db->prepare("DELETE FROM __adjustments WHERE adjustment_id :in;")->in($adjs)->execute();
			
			$log_action = array(
				'{L_ID}'		=> implode(', ', $adjs),
				'{L_EVENT_ID}'	=> $event_id,
			);
			$this->log_insert('action_indivadjofevent_deleted', $log_action, $event_id);
			$this->pdh->enqueue_hook('adjustment_update', $adjs);
			return true;
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __adjustments;");
			$this->pdh->enqueue_hook('adjustment_update');
		}
	}//end class
}//end if
?>