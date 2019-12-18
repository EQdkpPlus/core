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
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_w_raid" ) ) {
	class pdh_w_raid extends pdh_w_generic{

		private $arrLogLang = array(
				'event' 	=> '{L_EVENT}',
				'attendees' => '{L_ATTENDEES}',
				'note'		=> '{L_NOTE}',
				'value'		=> '{L_VALUE}',
				'date'		=> '{L_DATE}',
				'additional_data' => '{L_ADDITIONAL_INFOS}',
		);

		public function add_raid($raid_date, $raid_attendees, $event_id, $raid_note, $raid_value, $additional_data='') {
			//check for correct data
			if(!$raid_date OR !is_array($raid_attendees) OR !$event_id) {
				return false;
			}

			//insert raid
			$objQuery = $this->db->prepare('INSERT INTO __raids :p')->set(array(
				'event_id'		=> $event_id,
				'raid_date'		=> $raid_date,
				'raid_note'		=> $raid_note,
				'raid_value'	=> $raid_value,
				'raid_added_by'	=> $this->admin_user,
				'raid_additional_data' => $additional_data,
			))->execute();
			if($objQuery) {
				$raid_id = $objQuery->insertId;
			}

			//insert attendees
			$members = $this->pdh->maget('member', array('name', 'active'), 0, array($raid_attendees));
			foreach ( $members as $member_id => $member){
				$arrData[] = array(
					'raid_id'	=> $raid_id,
					'member_id' => $member_id
				);

				$intInactiveDays = (int)$this->config->get('inactive_period');
				if(!$member['active'] && (int)$this->config->get('auto_set_active')){
					if($intInactiveDays == 0){
						$this->pdh->put('member', 'change_status', array($member_id, 1));
					} elseif(($this->time->time - $intInactiveDays*24*3600) < $raid_date){
						$this->pdh->put('member', 'change_status', array($member_id, 1));
					}
				}

				$attendee_names[] = $member['name'];
			}

			$objQuery = $this->db->prepare('INSERT INTO __raid_attendees :p')->set($arrData)->execute();

			if($objQuery AND $raid_id) {
				//log insertion
				$log_action = array(
					'{L_EVENT}'		=> $this->pdh->get('event', 'name', array($event_id)),
					'{L_ATTENDEES}'	=> implode(', ', $attendee_names),
					'{L_NOTE}'		=> $raid_note,
					'{L_VALUE}'		=> $raid_value,
					'{L_DATE}'		=> '{D_'.$raid_date.'}',
					'{L_ADDITIONAL_INFOS}' => $additional_data,
				);
				$this->log_insert('action_raid_added', $log_action, $raid_id, $this->pdh->get('event', 'name', array($event_id)));
				//call pdh hooks
				$this->pdh->enqueue_hook('raid_update', $raid_id, array('action' => 'add', 'time' => $raid_date));

				if($this->hooks->isRegistered('raid_added')){
					$this->hooks->process('raid_added', array('id' => $raid_id));
				}

				return $raid_id;
			} else {
				return false;
			}
		}

		public function update_raid($raid_id, $raid_date, $raid_attendees, $event_id, $raid_note, $raid_value, $additional_data='', $connected=array()) {
			//get old-data
			$old['event'] = $this->pdh->get('raid', 'event', array($raid_id));
			$old['note'] = $this->pdh->get('raid', 'note', array($raid_id));
			$old['value'] = $this->pdh->get('raid', 'value', array($raid_id));
			$old['date'] = $this->pdh->get('raid', 'date', array($raid_id));
			$old['members'] = $this->pdh->get('raid', 'raid_attendees', array($raid_id, true));
			$old['additional_data'] = $this->pdh->get('raid', 'additional_data', array($raid_id));
			$old['connected_raids'] = $this->pdh->get('raid', 'connected_attendance', array($raid_id));
			
			//get member names for log
			$old['m_names'] = $this->pdh->aget('member', 'name', 0, array($old['members']));

			$arrSet = array(
					'event_id' => $event_id,
					'raid_note' => $raid_note,
					'raid_value' => $raid_value,
					'raid_date' => $raid_date,
					'raid_additional_data' => $additional_data,
					'raid_connected_attendance' => json_encode($connected),
			);

			//Reset Apa Cache if value or date was changed
			if($old['value'] != $raid_value || $old['raid_date'] != $raid_date){
				$arrSet['raid_apa_value'] = "";
			}

			$objQuery = $this->db->prepare("UPDATE __raids :p WHERE raid_id=?")->set($arrSet)->execute($raid_id);

			if($objQuery) {
				//update raid_attendees
				$add_atts = array_diff($raid_attendees, $old['members']);
				$del_atts = array_diff($old['members'], $raid_attendees);
				$upd_atts = array_diff($old['members'], $del_atts);

				foreach($add_atts as $add_att) {
					$objQuery = $this->db->prepare("INSERT INTO __raid_attendees :p")->set(array(
						'raid_id' => $raid_id,
						'member_id' => $add_att,
					))->execute();
				}
				foreach($del_atts as $del_att) {
					$objQuery = $this->db->prepare( "DELETE FROM __raid_attendees WHERE raid_id = ? AND member_id =?")->execute($raid_id, $del_att);
				}
				$member_string = get_coloured_names($upd_atts, $add_atts, $del_atts);

				$arrOld = array(
					'event' 	=> $this->pdh->get('event', 'name', array($old['event'])),
					'attendees' => implode(', ', $old['m_names']),
					'note'		=> $old['note'],
					'value'		=> $old['value'],
					'date'		=> '{D_'.$old['date'].'}',
					'additional_data' => $old['additional_data'],
				);
				$arrNew = array(
					'event' 	=> $this->pdh->get('event', 'name', array($event_id)),
					'attendees' => $member_string,
					'note'		=> $raid_note,
					'value'		=> $raid_value,
					'date'		=> '{D_'.$raid_date.'}',
					'additional_data' => $additional_data,
				);

				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);

				$this->log_insert('action_raid_updated', $log_action, $raid_id, $this->pdh->get('event', 'name', array($old['event'])));
				$this->pdh->enqueue_hook('raid_update', $raid_id, array('action' => 'update', 'time' => $raid_date, 'members' => array_merge($upd_atts, $add_atts, $del_atts)));

				if($this->hooks->isRegistered('raid_updated')){
					$this->hooks->process('raid_updated', array('id' => $raid_id));
				}

				return true;
			}
			return false;
		}

		public function delete_raid($raid_id) {
			if(!$raid_id) {
				return false;
			}

			//get old-data
			$old['event']		= $this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($raid_id))));
			$old['note']		= $this->pdh->get('raid', 'note', array($raid_id));
			$old['value']		= $this->pdh->get('raid', 'value', array($raid_id));
			$old['date']		= $this->pdh->get('raid', 'date', array($raid_id));
			$old['additional_data'] = $this->pdh->get('raid', 'additional_data', array($raid_id));
			$raid_attendees 	= $this->pdh->get('raid', 'raid_attendees', array($raid_id));
			$old['members']		= $this->pdh->aget('member', 'name', 0, array($raid_attendees));

			$objQuery = $this->db->prepare("DELETE FROM __raids WHERE raid_id = ?")->execute($raid_id);

			if($objQuery) {
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
				$objQuery = $this->db->prepare("DELETE FROM __raid_attendees WHERE raid_id =?")->execute($raid_id);

				if(!in_array(false, $retu) AND $objQuery) {
					//log it
					$log_action = array(
						'{L_DATE}'		=> '{D_'.$old['date'].'}',
						'{L_EVENT}'		=> $old['event'],
						'{L_ATTENDEES}'	=> implode(', ', $old['members']),
						'{L_NOTE}'		=> $old['note'],
						'{L_VALUE}'		=> $old['value'],
						'{L_ADDITIONAL_INFOS}' => $old['additional_data'],
					);
					$this->log_insert('action_raid_deleted', $log_action, $raid_id, $old['event']);

					if($this->hooks->isRegistered('raid_deleted')){
						$this->hooks->process('raid_deleted', array('id' => $raid_id));
					}

					//call pdh hook
					$this->pdh->enqueue_hook('raid_update', $raid_id, array('action' => 'delete', 'time' => $old['date'], 'members' => $raid_attendees));
					return true;
				}
			}

			return false;
		}

		public function delete_raidsofevent($event_id) {
			$raids = $this->pdh->get('raid', 'raidids4eventid', array($event_id));
			if(count($raids) < 1) return true;
			foreach($raids as $raid_id) {
				$this->pdh->put('item', 'delete_itemsofraid', array($raid_id));
				$this->pdh->put('adjustment', 'delete_adjustmentsofraid', array($raid_id));

				if($this->hooks->isRegistered('raid_deleted')){
					$this->hooks->process('raid_deleted', array('id' => $raid_id));
				}
			}
			$this->db->prepare("DELETE FROM __raids WHERE raid_id :in")->in($raids)->execute();
			$this->db->prepare("DELETE FROM __raid_attendees WHERE raid_id :in")->in($raids)->execute();

			$log_action = array(
				'{L_ID}'	=> implode(', ', $raids),
				'{L_EVENT}'	=> $this->pdh->get('event', 'name', array($event_id))
			);
			$this->log_insert('action_raidsofevent_deleted', $log_action, $event_id, $this->pdh->get('event', 'name', array($event_id)));
			$this->pdh->enqueue_hook('raid_update', $raids, array('action' => 'delete', 'time' => 0));
			return true;
		}

		public function delete_raid_attendance($raid_id, $member_id){
			$raid_attendees 	= $this->pdh->get('raid', 'raid_attendees', array($raid_id));
			$old['members']		= $this->pdh->aget('member', 'name', 0, array($raid_attendees));
			$old['event']		= $this->pdh->get('event', 'name', array($this->pdh->get('raid', 'event', array($raid_id))));

			$arrOld = array(
					'attendees' => implode(', ', $old['members']),
			);

			$intKey = array_search($member_id, $old['members']);
			if($intKey !== false) unset($old['members'][$intKey]);

			$arrNew = array(
					'attendees' => implode(', ', $new['members']),
			);

			$objQuery = $this->db->prepare( "DELETE FROM __raid_attendees WHERE raid_id = ? AND member_id =?")->execute($raid_id, $member_id);

			$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);

			$this->log_insert('action_raid_updated', $log_action, $raid_id, $this->pdh->get('event', 'name', array($old['event'])));

			$this->pdh->enqueue_hook('raid_update', $raid_id, array('action' => 'update', 'time' => $raid_date, 'members' => array($member_id)));

			return true;
		}

		public function update_apa_value($raid_id, $apa_id, $val){
			$arrCurrentApaValue = $this->pdh->get('raid', 'apa_value', array($raid_id));
			if(!$arrCurrentApaValue || !is_array($arrCurrentApaValue)) $arrCurrentApaValue = array();
			$arrCurrentApaValue[$apa_id] = $val;

			$objQuery = $this->db->prepare("UPDATE __raids :p WHERE raid_id=?")->set(array(
				'raid_apa_value' => serialize($arrCurrentApaValue),
			))->execute($raid_id);

			$this->pdh->enqueue_hook('raid_update', array($raid_id), array('action' => 'update', 'apa' => true));

			return true;
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __raids;");
			$this->db->query("TRUNCATE TABLE __raid_attendees;");
			$this->pdh->enqueue_hook('raid_update');

			if($this->hooks->isRegistered('raid_reset')){
				$this->hooks->process('raid_reset', array());
			}
		}
	}//end class
}//end if
