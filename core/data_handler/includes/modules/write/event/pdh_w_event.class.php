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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_event')) {
	class pdh_w_event extends pdh_w_generic {

		private $arrLogLang = array(
			'event_name' 	=> "{L_NAME}",
			'event_value'	=> "{L_VALUE}",
			'event_icon'	=> "{L_ICON}",
			'default_itempool' => "{L_ITEMPOOL}",
			'event_show_profile' => "{L_SHOW_ON_PROFILE}",
		);

		public function add_event($name, $value, $icon, $itempool=0, $showOnProfile=1) {
			$arrSet = array(
				'event_name' 	=> $name,
				'event_value'	=> $value,
				'event_icon'	=> $icon,
				'event_added_by'=> $this->admin_user,
				'default_itempool'=> $itempool,
				'event_show_profile' => $showOnProfile,
			);

			$objQuery = $this->db->prepare("INSERT INTO __events :p")->set($arrSet)->execute();

			if($objQuery) {
				$id = $objQuery->insertId;
				$log_action = array(
					'{L_NAME}'		=> $name,
					'{L_VALUE}'		=> $value,
					'{L_ICON}'		=> $icon,
					'{L_ITEMPOOL}'	=> $itempool,
					'{L_SHOW_ON_PROFILE}'	=> $showOnProfile,
				);
				$this->log_insert('action_event_added', $log_action, $id, $name);
				$this->pdh->enqueue_hook('event_update', array($id));

				if($this->hooks->isRegistered('event_added')){
					$this->hooks->process('event_added', array('id' => $id, 'data' => array()));
				}

				return $id;
			}
			return false;
		}

		public function update_event($id, $name, $value, $icon, $itempool=0, $showOnProfile=1) {
			$old['name']	= $this->pdh->get('event', 'name', array($id));
			$old['value']	= $this->pdh->get('event', 'value', array($id));
			$old['icon']	= $this->pdh->get('event', 'icon', array($id));
			$old['default_itempool'] = $this->pdh->get('event', 'def_itempool', array($id));
			$old['event_show_profile'] = $this->pdh->get('event', 'show_profile', array($id));

			$arrSet = array(
				'event_name' 	=> $name,
				'event_value'	=> $value,
				'event_icon'	=> $icon,
				'event_updated_by'=> $this->admin_user,
				'default_itempool' => $itempool,
				'event_show_profile' => $showOnProfile,
			);

			$objQuery = $this->db->prepare("UPDATE __events :p WHERE event_id =?")->set($arrSet)->execute($id);

			if($objQuery) {
				$arrOld = array(
					'event_name' 	=> $old['name'],
					'event_value'	=> $old['value'],
					'event_icon'	=> $old['icon'],
					'default_itempool' => $old['default_itempool'],
					'event_show_profile' => $old['show_profile'],
				);
				$arrNew = array(
					'event_name' 	=> $name,
					'event_value'	=> $value,
					'event_icon'	=> $icon,
					'default_itempool'=> $itempool,
					'event_show_profile' => $showOnProfile,
				);
				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
				if ($log_action) $this->log_insert('action_event_updated', $log_action, $id, $old['name']);

				if($this->hooks->isRegistered('event_updated')){
					$this->hooks->process('event_updated', array('id' => $id, 'data' => $arrNew));
				}

				$this->pdh->enqueue_hook('event_update', array($id));
				return true;
			}
			return false;
		}

		public function delete_event($id) {
			$old['name'] = $this->pdh->get('event', 'name', array($id));
			$old['value'] = $this->pdh->get('event', 'value', array($id));
			$old['icon'] = $this->pdh->get('event', 'icon', array($id));
			$old['default_itempool'] = $this->pdh->get('event', 'def_itempool', array($id));
			$old['event_show_profile'] = $this->pdh->get('event', 'show_profile', array($id));

			$this->db->beginTransaction();

			$objQuery = $this->db->prepare("DELETE FROM __events WHERE event_id = ?;")->execute($id);

			if($objQuery) {
				$log_action = array(
					'{L_NAME}'		=> $old['name'],
					'{L_VALUE}'		=> $old['value'],
					'{L_ICON}'		=> $old['icon'],
					'{L_ITEMPOOL}'	=> $old['default_itempool'],
					'{L_SHOW_ON_PROFILE}'=> $old['event_show_profile'],
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

				if($this->hooks->isRegistered('event_deleted')){
					$this->hooks->process('event_deleted', array('id' => $id, 'data' => $old));
				}
			}
			$this->db->rollbackTransaction();
			return false;
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __events;");
			$this->db->query("TRUNCATE TABLE __multidkp2event;");
			$this->pdh->enqueue_hook('event_update');

			if($this->hooks->isRegistered('event_reset')){
				$this->hooks->process('event_reset', array());
			}
		}
	}
}
