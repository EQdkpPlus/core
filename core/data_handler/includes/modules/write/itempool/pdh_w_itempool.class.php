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

if(!class_exists('pdh_w_itempool')) {
	class pdh_w_itempool extends pdh_w_generic {

		public function add_itempool($name, $desc) {
			$arrSet = array(
				'itempool_name' => $name,
				'itempool_desc' => $desc,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __itempool :p")->set($arrSet)->execute();
			
			if($objQuery) {
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('itempool_update', array($id));
				return $id;
			}
			return false;
		}

		public function update_itempool($id, $name, $desc) {
			$old_events = $this->pdh->get('itempool', 'event_ids', array($id));
			
			$arrSet = array(
				'itempool_name' => $name,
				'itempool_desc' => $desc,
			);

			$objQuery = $this->db->prepare("UPDATE __itempool :p WHERE itempool_id=?")->set($arrSet)->execute($id);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('itempool_update', array($id));
				return true;
			}
			return false;
		}

		public function delete_itempool($id) {
			//default itempool is not deletable
			if($id == 1) {
				return false;
			}
			
			$this->db->beginTransaction();
			if($this->db->prepare("DELETE FROM __itempool WHERE itempool_id = ?;")->execute($id)) {
				
				if($this->db->prepare("DELETE FROM __multidkp2itempool WHERE multidkp2itempool_itempool_id =?")->execute($id)) {
					if($this->db->prepare("UPDATE __items SET itempool_id = '1' WHERE itempool_id = ?")->execute($id)) {
						$this->pdh->enqueue_hook('itempool_update', array($id));
						$items = $this->pdh->get('item', 'item_ids_of_itempool', array($id));
						$this->pdh->enqueue_hook('item_update', array($items));
						$this->db->commitTransaction();
						return true;
					}
				}
			}
			$this->db->rollbackTransaction();
			return false;
		}
		
		public function reset() {
			$this->db->query("DELETE FROM __itempool WHERE itempool_id != '1';");
			$this->db->query("TRUNCATE TABLE __multidkp2itempool;");
			$this->pdh->enqueue_hook('itempool_update');
		}
	}
}
?>