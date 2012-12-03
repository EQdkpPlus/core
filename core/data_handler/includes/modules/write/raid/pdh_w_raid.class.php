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

if ( !class_exists( "pdh_w_raid" ) ) {
	class pdh_w_raid extends pdh_w_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'game', 'config', 'time');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_raid($raid_date, $raid_attendees, $event_id, $raid_note, $raid_value) {
			//check for correct data
			if(!$raid_date OR !is_array($raid_attendees) OR !$event_id) {
				return false;
			}

			//insert raid
			if($this->db->query('INSERT INTO __raids :params', array(
				'event_id'		=> $event_id,
				'raid_date'		=> $raid_date,
				'raid_note'		=> $raid_note,
				'raid_value'	=> $raid_value,
				'raid_added_by'	=> $this->admin_user
			))) {
				$raid_id = $this->db->insert_id();
			}

			//insert attendees
			$members = $this->pdh->maget('member', array('name', 'active'), 0, array($raid_attendees));
			foreach ( $members as $member_id => $member){
				$m_query[] = "('" . $raid_id . "', '" . $member_id . "')";
				if(!$member['active'] && ($this->time->time - $this->config->get('inactive_period')*24*3600) < $raid_date) $this->pdh->put('member', 'change_status', array($member_id, 1));
				$attendee_names[] = $member['name'];
			}

			$sql = 'INSERT INTO __raid_attendees (raid_id, member_id)
				VALUES ' . implode(', ', $m_query);
			if($this->db->query($sql) AND $raid_id) {
				//log insertion
				$log_action = array(
					'{L_ID}'		=> $raid_id,
					'{L_EVENT}'		=> $this->pdh->get('event', 'name', array($event_id)),
					'{L_ATTENDEES}'	=> implode(', ', $attendee_names),
					'{L_NOTE}'		=> $raid_note,
					'{L_VALUE}'		=> $raid_value,
					'{L_DATE}'		=> '{D_'.$raid_date.'}',
					'{L_ADDED_BY}'	=> $this->admin_user);
				$this->log_insert('action_raid_added', $log_action);
				//call pdh hooks
				$this->pdh->enqueue_hook('raid_update', $raid_id);
				return $raid_id;
			} else {
				return false;
			}
		}

		public function update_raid($raid_id, $raid_date, $raid_attendees, $event_id, $raid_note, $raid_value) {
			//get old-data
			$old['event'] = $this->pdh->get('raid', 'event', array($raid_id));
			$old['note'] = $this->pdh->get('raid', 'note', array($raid_id));
			$old['value'] = $this->pdh->get('raid', 'value', array($raid_id));
			$old['date'] = $this->pdh->get('raid', 'date', array($raid_id));
			$old['members'] = $this->pdh->get('raid', 'raid_attendees', array($raid_id));
			//get member names for log
			$old['m_names'] = $this->pdh->aget('member', 'name', 0, array($old['members']));

			$arrSet = array(
					'event_id' => $event_id,
					'raid_note' => $raid_note,
					'raid_value' => $raid_value,
					'raid_date' => $raid_date,
			);				
							
			if($this->db->query("UPDATE __raids SET :params WHERE raid_id=?", $arrSet, $raid_id)) {
				//update raid_attendees
				$add_atts = array_diff($raid_attendees, $old['members']);
				$del_atts = array_diff($old['members'], $raid_attendees);
				$upd_atts = array_diff($old['members'], $del_atts);
				foreach($add_atts as $add_att) {
					$this->db->query("INSERT INTO __raid_attendees :params", array(
						'raid_id' => $raid_id,
						'member_id' => $add_att,
					));
				}
				foreach($del_atts as $del_att) {
					$sql = "DELETE FROM __raid_attendees WHERE raid_id = '".$this->db->escape($raid_id)."' AND member_id = '".$this->db->escape($del_att)."';";
					$this->db->query($sql);
				}
				$member_string = get_coloured_names($upd_atts, $add_atts, $del_atts);
				$log_action = array(
					'{L_ID}'				=> $raid_id,
					'{L_EVENT_BEFORE}'		=> $this->pdh->get('event', 'name', array($old['event'])),
					'{L_ATTENDEES_BEFORE}'	=> implode(', ', $old['m_names']),
					'{L_NOTE_BEFORE}'		=> $old['note'],
					'{L_VALUE_BEFORE}'		=> $old['value'],
					'{L_DATE_BEFORE}'		=> '{D_'.$old['date'].'}',
					'{L_EVENT_AFTER}'		=> $this->pdh->get('event', 'name', array($event_id)),
					'{L_ATTENDEES_AFTER}'	=> $member_string,
					'{L_NOTE_AFTER}'		=> $raid_note,
					'{L_VALUE_AFTER}'		=> $raid_value,
					'{L_DATE_AFTER}'		=> '{D_'.$raid_date.'}',
					'{L_UPDATED_BY}'		=> $this->admin_user
				);
				$this->log_insert('action_raid_updated', $log_action);
				$this->pdh->enqueue_hook('raid_update', $raid_id);
				return true;
			}
			return false;
		}

		public function delete_raid($raid_id) {
			if(!$raid_id) {
				return false;
			}
			#$this->db->query("START TRANSACTION");
			//get old-data
			$old['event']		= $this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($raid_id))));
			$old['note']		= $this->pdh->get('raid', 'note', array($raid_id));
			$old['value']		= $this->pdh->get('raid', 'value', array($raid_id));
			$old['date']		= $this->pdh->get('raid', 'date', array($raid_id));
			$old['members']		= $this->pdh->aget('member', 'name', 0, array($this->pdh->get('raid', 'raid_attendees', array($raid_id))));

			$sql = "DELETE FROM __raids WHERE raid_id = '".$this->db->escape($raid_id)."';";
			if($this->db->query($sql)) {
				$retu = array();
				$items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
					if(is_array($items)) {
					foreach($items as $item_id) {
						$retu[] = $this->pdh->put('item', 'delete_item', array($item_id));
					}
				}
				unset($items);
				$adjs = $this->pdh->get('adjustment', 'adjsofraid', array($raid_id));
					if(is_array($adjs)) {
					foreach($adjs as $adj_id) {
						$retu[] = $this->pdh->put('adjustment', 'delete_adjustment', array($adj_id));
					}
				}
				unset($adjs);
				$sql = "DELETE FROM __raid_attendees WHERE raid_id = '".$this->db->escape($raid_id)."';";
				if(!in_array(false, $retu) AND $this->db->query($sql)) {
					//log it
					$log_action = array(
						'{L_ID}'		=> $raid_id,
						'{L_DATE}'		=> '{D_'.$old['date'].'}',
						'{L_EVENT}'		=> $old['event'],
						'{L_ATTENDEES}'	=> implode(', ', $old['members']),
						'{L_NOTE}'		=> $old['note'],
						'{L_VALUE}'		=> $old['value']
					);
					$this->log_insert('action_raid_deleted', $log_action);
					#$this->db->query("COMMIT");
					//call pdh hook
					$this->pdh->enqueue_hook('raid_update', $raid_id);
					return true;
				}
			}
			#$this->db->query("ROLLBACK");
			return false;
		}
		
		public function delete_raidsofevent($event_id) {
			$raids = $this->pdh->get('raid', 'raidids4eventid', array($event_id));
			if(count($raids) < 1) return true;
			foreach($raids as $raid_id) {
				$this->pdh->put('item', 'delete_itemsofraid', array($raid_id));
				$this->pdh->put('item', 'delete_adjsofraid', array($raid_id));
			}
			$this->db->query("DELETE FROM __raids WHERE raid_id IN ('".implode("', '", $raids)."');");
			$this->db->query("DELETE FROM __raid_attendees WHERE raid_id IN ('".implode("', '", $raids)."');");
			$log_action = array(
				'{L_ID}'	=> implode(', ', $raids),
				'{L_EVENT}'	=> $this->pdh->get('event', 'name', array($event_id))
			);
			$this->log_insert('action_raidsofevent_deleted', $log_action);
			$this->pdh->enqueue_hook('raid_update', $raids);
			return true;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __raids;");
			$this->db->query("TRUNCATE TABLE __raid_attendees;");
			$this->pdh->enqueue_hook('raid_update');
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_raid', pdh_w_raid::__shortcuts());
?>