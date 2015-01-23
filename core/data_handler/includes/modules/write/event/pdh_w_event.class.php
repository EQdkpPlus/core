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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_event')) {
	class pdh_w_event extends pdh_w_generic {
		
		private $arrLogLang = array(
			'event_name' 	=> "{L_NAME}",
			'event_value'	=> "{L_VALUE}",
			'event_icon'	=> "{L_ICON}",
		);

		public function add_event($name, $value, $icon) {
			$arrSet = array(
				'event_name' 	=> $name,
				'event_value'	=> $value,
				'event_icon'	=> $icon,
				'event_added_by'=> $this->admin_user,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __events :p")->set($arrSet)->execute();
			
			if($objQuery) {
				$id = $objQuery->insertId;
				$log_action = array(
					'{L_NAME}'		=> $name,
					'{L_VALUE}'		=> $value,
					'{L_ICON}'		=> $icon,
				);
				$this->log_insert('action_event_added', $log_action, $id, $name);
				$this->pdh->enqueue_hook('event_update', array($id));
				return $id;
			}
			return false;
		}

		public function update_event($id, $name, $value, $icon) {
			$old['name']	= $this->pdh->get('event', 'name', array($id));
			$old['value']	= $this->pdh->get('event', 'value', array($id));
			$old['icon']	= $this->pdh->get('event', 'icon', array($id));

			$arrSet = array(
				'event_name' 	=> $name,
				'event_value'	=> $value,
				'event_icon'	=> $icon,
				'event_updated_by'=> $this->admin_user,
			);
			
			$objQuery = $this->db->prepare("UPDATE __events :p WHERE event_id =?")->set($arrSet)->execute($id);
			
			if($objQuery) {
				$arrOld = array(
					'event_name' 	=> $old['name'],
					'event_value'	=> $old['value'],
					'event_icon'	=> $old['icon'],
				);
				$arrNew = array(
					'event_name' 	=> $name,
					'event_value'	=> $value,
					'event_icon'	=> $icon,
				);
				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
				if ($log_action) $this->log_insert('action_event_updated', $log_action, $id, $old['name']);
								
				$this->pdh->enqueue_hook('event_update', array($id));
				return true;
			}
			return false;
		}

		public function delete_event($id) {
			$old['name'] = $this->pdh->get('event', 'name', array($id));
			$old['value'] = $this->pdh->get('event', 'value', array($id));
			$old['icon'] = $this->pdh->get('event', 'icon', array($id));
			
			$this->db->beginTransaction();
			
			$objQuery = $this->db->prepare("DELETE FROM __events WHERE event_id = ?;")->execute($id);

			if($objQuery) {
				$log_action = array(
					'{L_NAME}'		=> $old['name'],
					'{L_VALUE}'		=> $old['value'],
					'{L_ICON}'		=> $old['icon']
				);
				$this->log_insert('action_event_deleted', $log_action, $id, $old['name']);

				//delete raids and adjustments
				$retu = array(true);
				$retu[] = $this->pdh->put('raid', 'delete_raidsofevent', array($id));
				$retu[] = $this->pdh->put('adjustment', 'delete_adjustmentsofevent', array($id));

				//delete multidkp2event data
				$objQuery = $this->db->prepare("DELETE FROM __multidkp2event WHERE multidkp2event_event_id = ?;")->execute($id);
				if(!in_array(false, $retu, true) AND $objQuery) {
					$this->db->commitTransaction();
					$this->pdh->enqueue_hook('event_update', array($id));
					return true;
				}
			}
			$this->db->rollbackTransaction();
			return false;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __events;");
			$this->db->query("TRUNCATE TABLE __multidkp2event;");
			$this->pdh->enqueue_hook('event_update');
		}
	}
}
?>