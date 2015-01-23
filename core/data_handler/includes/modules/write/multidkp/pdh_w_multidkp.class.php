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

if(!class_exists('pdh_w_multidkp')) {
	class pdh_w_multidkp extends pdh_w_generic {

		public function add_multidkp($name, $desc, $events, $itempools, $no_atts=array()) {
			$arrSet = array(
				'multidkp_name' => $name,
				'multidkp_desc' => $desc,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __multidkp :p")->set($arrSet)->execute();
			
			if($objQuery) {
				$id = $objQuery->insertId;
				$retu = array(true);
				foreach($events as $event_id) {
					$s_no_att = (in_array($event_id, $no_atts)) ? 1 : 0;
					$arrSet = array(
						'multidkp2event_multi_id' => $id,
						'multidkp2event_event_id' => $event_id,
						'multidkp2event_no_attendance' => $s_no_att,
					);				
					$retu[] = ($this->db->prepare("INSERT INTO __multidkp2event :p")->set($arrSet)->execute()) ? true : false;
				}
					
				foreach($itempools as $itempool_id) {
					$retu[] = ($this->db->prepare("INSERT INTO __multidkp2itempool :p")->set(array(
							'multidkp2itempool_multi_id' => $id, 
							'multidkp2itempool_itempool_id' => $itempool_id))->execute()) ? true : false;
				}
				if(!in_array(false, $retu)) {
					$this->pdh->enqueue_hook('multidkp_update',array($id));
					return $id;
				}
			}
			return false;
		}
		
		public function save_sort($arrData){
			foreach($arrData as $sortid => $id){
				$arrSet = array(
					'multidkp_sortid' => $sortid,
				);
					
				$objQuery = $this->db->prepare("UPDATE __multidkp :p WHERE multidkp_id=?")->set($arrSet)->execute($id);
			}
			$this->pdh->enqueue_hook('multidkp_update',array());
		}

		public function update_multidkp($id, $name, $desc, $events, $itempools, $no_atts) {
			$old_events = $this->pdh->get('multidkp', 'event_ids', array($id));
			$old_itempools = $this->pdh->get('multidkp', 'itempool_ids', array($id));
			$old_no_atts = $this->pdh->get('multidkp', 'no_attendance', array($id));

			$arrSet = array(
				'multidkp_name' => $name,
				'multidkp_desc' => $desc,
			);
			
			$this->db->beginTransaction();
			$objQuery = $this->db->prepare("UPDATE __multidkp :p WHERE multidkp_id=?")->set($arrSet)->execute($id);
			if($objQuery) {
				$all_events = array_merge($events, $old_events);
				foreach($all_events as $event_id){
					if(in_array($event_id, $old_events) AND in_array($event_id, $events)) {
						if(in_array($event_id, $no_atts) AND !in_array($event_id, $old_no_atts)) {
							$objQuery = $this->db->prepare("UPDATE __multidkp2event :p WHERE multidkp2event_multi_id=? AND multidkp2event_event_id=?")->set(array(
								'multidkp2event_no_attendance' => 1
							))->execute($id, $event_id);

							$retu[] = ($objQuery);
						} elseif(!in_array($event_id, $no_atts) AND in_array($event_id, $old_no_atts)) {
							$objQuery = $this->db->prepare("UPDATE __multidkp2event :p WHERE multidkp2event_multi_id=? AND multidkp2event_event_id=?")->set(array(
									'multidkp2event_no_attendance' => 0
							))->execute($id, $event_id);
							
							$retu[] = ($objQuery);
						}
					} elseif(!in_array($event_id, $old_events)) {
						$s_no_att = (in_array($event_id, $no_atts)) ? 1 : 0;
						$objQuery = $this->db->prepare("INSERT INTO __multidkp2event :p")->set(array(
							'multidkp2event_multi_id' => $id,
							'multidkp2event_event_id' => $event_id,
							'multidkp2event_no_attendance' => $s_no_att,
						))->execute();
						
						$retu[] = ($objQuery);
					} elseif(!in_array($event_id, $events)) {
						$objQuery = $this->db->prepare("DELETE FROM __multidkp2event WHERE multidkp2event_multi_id = ? AND multidkp2event_event_id =?")->execute($id, $event_id);
						$retu[] = ($objQuery);
					}
				}
				$all_itempools = (is_array($old_itempools)) ? array_unique(array_merge($itempools, $old_itempools)) : $itempools;
				$retu = array(true);
				foreach($all_itempools as $itempool_id) {
					if(!$old_itempools OR !in_array($itempool_id, $old_itempools)) {
						$objQuery = $this->db->prepare("INSERT INTO __multidkp2itempool :p")->set(array(
								'multidkp2itempool_multi_id' => $id,
								'multidkp2itempool_itempool_id' => $itempool_id,
						))->execute();

						$retu[] = ($objQuery);
					}elseif(!in_array($itempool_id, $itempools)) {
						$objQuery = $this->db->prepare("DELETE FROM __multidkp2itempool WHERE multidkp2itempool_multi_id=? AND multidkp2itempool_itempool_id=?")->execute($id, $itempool_id);
						
						$retu[] = ($objQuery);
					}
				}
				if(!in_array(false, $retu)) {
					$this->db->commitTransaction();
					$this->pdh->enqueue_hook('multidkp_update', array($id));
					return true;
				}
			}
			$this->db->rollbackTransaction();
			return false;
		}

		public function delete_multidkp($id) {
			$this->db->beginTransaction();
			$objQuery = $this->db->prepare("DELETE FROM __multidkp WHERE multidkp_id =?")->execute($id);
			if($objQuery) {
				$objQuery = $this->db->prepare("DELETE FROM __multidkp2event WHERE multidkp2event_multi_id=?")->execute($id);
				$retu[] = ($objQuery);
				$objQuery = $this->db->prepare("DELETE FROM __multidkp2itempool WHERE multidkp2itempool_multi_id =?")->execute($id);
				$retu[] = ($objQuery);
				if(!in_array(false, $retu)) {
					$this->db->commitTransaction();
					$this->pdh->enqueue_hook('multidkp_update', array($id));
					return true;
				}
			}
			$this->db->rollbackTransaction();
			return false;
		}
		
		public function add_multidkp2event($event_id, $mdkps) {
			if(!is_array($mdkps) || count($mdkps) < 1) return true;
			$this->db->prepare("DELETE FROM __multidkp2event WHERE multidkp2event_event_id = ?")->execute($event_id);
			
			$sqls = array();
			foreach($mdkps as $mdkp_id) {
				$sqls[] = array(
					'multidkp2event_event_id' => $event_id,
					'multidkp2event_multi_id' => $mdkp_id
				);
			}
			$objQuery = $this->db->prepare("INSERT INTO __multidkp2event :p")->set($sqls)->execute();
			
			if($objQuery) {
				$this->pdh->enqueue_hook('multidkp_update');
				return true;
			}
			return false;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __multidkp;");
			$this->pdh->enqueue_hook('multidkp_update');
		}
	}
}
?>