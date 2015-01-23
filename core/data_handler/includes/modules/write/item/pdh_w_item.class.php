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

if ( !class_exists( "pdh_w_item" ) ) {
	class pdh_w_item extends pdh_w_generic {
		
		private $arrLogLang = array(
				'item_name'			=> '{L_NAME}',
				'member_id'			=> '{L_BUYERS}',
				'raid_id'			=> '{L_RAID_ID}',
				'item_value'		=> '{L_VALUE}',
				'item_date'			=> '{L_DATE}',
				'game_itemid'		=> '{L_ITEM_ID}',
				'itempool'			=> '{L_ITEMPOOL}',
		);

		public function add_item($item_name, $item_buyers, $raid_id, $game_item_id, $item_value, $itempool_id, $time=false) {
			$time		= ($time) ? $time : time();
			$group_key	= $this->gen_group_key($time, array($item_name, $raid_id, $item_value));
			$query		= array();

			if(!is_array($item_buyers) AND $item_buyers) {
				$member			= $item_buyers;
				$item_buyers	= array($member);
			}
			foreach($item_buyers as $member_id){
				$objQuery = $this->db->prepare("INSERT INTO __items :p")->set(array(
					'item_name'			=> $item_name,
					'member_id'			=> $member_id,
					'raid_id'			=> $raid_id,
					'item_value'		=> $item_value,
					'item_date'			=> $time,
					'item_group_key'	=> $group_key,
					'item_added_by'		=> $this->admin_user,
					'game_itemid'		=> $game_item_id,
					'itempool_id'		=> $itempool_id
				))->execute();
			}

			if($objQuery) {
				//insert log
				$item_id = $objQuery->insertId;
				$arrNew = array(
					'item_name'			=> $item_name,
					'member_id'			=> implode(', ', $this->pdh->aget('member', 'name', '0', array($item_buyers))),
					'raid_id'			=> $raid_id,
					'item_value'		=> $item_value,
					'item_date'			=> '{D_'.$time.'}',
					'game_itemid'		=> $game_item_id,
					'itempool'			=> $this->pdh->get('itempool', 'name', $itempool_id),
				);
				
				$log_action = $this->logs->diff(false, $arrNew, $this->arrLogLang);
				$this->log_insert('action_item_added', $log_action, $item_id, $item_name);
				$this->pdh->enqueue_hook('item_update', $item_id);
				return $item_id;
			}
			return false;
		}

		public function update_item($group_key_or_id, $item_name, $item_buyers, $raid_id, $game_item_id, $item_value, $itempool_id, $time=false, $id=false) {
			$time = ($time) ? $time : time();
			$new_group_key = $this->gen_group_key($time, array($item_name, $raid_id, $item_value, $itempool_id));
			//fetch old-data
			if(!$id) {
				$group_key = $group_key_or_id;
				$old['ids'] = $this->pdh->get('item', 'ids_of_group_key', array($group_key));
				foreach($old['ids'] as $item_id) {
					$old['buyers'][$item_id] = $this->pdh->get('item', 'buyer', array($item_id));
				}
			} else {
				$item_id = $group_key_or_id;
				$old['buyers'][$item_id] = $this->pdh->get('item', 'buyer', array($item_id));
			}
			$old['value'] = $this->pdh->get('item', 'value', array($item_id));
			$old['name'] = $this->pdh->get('item', 'name', array($item_id));
			$old['raid_id'] = $this->pdh->get('item', 'raid_id', array($item_id));
			$old['itempool_id'] = $this->pdh->get('item', 'itempool_id', array($item_id));
			$old['date'] = $this->pdh->get('item', 'date', array($item_id));
			$old['game_itemid'] = $this->pdh->get('item', 'game_itemid', array($item_id));
			
			$retu = array(true);
			$updated_mems = array();
			$added_mems = array();
			$items2del = array();
			
			$this->db->beginTransaction();
			
			if($id || (count($item_buyers) == 1 && count($old['buyers']) == 1))	{
				$objQuery = $this->db->prepare("UPDATE __items :p WHERE item_id=?;")->set(array(
					'item_name'			=> $item_name,
					'item_value'		=> $item_value,
					'member_id'			=> $item_buyers[0],
					'raid_id'			=> $raid_id,
					'item_date'			=> $time,
					'item_group_key'	=> $new_group_key,
					'game_itemid'		=> $game_item_id,
					'itempool_id'		=> $itempool_id
				))->execute($item_id);
				
				if(!$objQuery) {
					$retu[] = false;
					break;
				}
				$hook_id = $item_id;
			} else {
				$items2del = $old['buyers'];
				foreach($item_buyers as $member_id) {
					$item_id = array_search($member_id, $old['buyers']);
					if($item_id !== false) {
						$updated_mems[] = $member_id;
						unset($items2del[$item_id]);
						
						$objQuery = $this->db->prepare("UPDATE __items :p WHERE item_id = ?;")->set(array(
							'item_name'			=> $item_name,
							'item_value'		=> $item_value,
							'member_id'			=> $member_id,
							'raid_id'			=> $raid_id,
							'item_date'			=> $time,
							'item_group_key'	=> $new_group_key,
							'game_itemid'		=> $game_item_id,
							'itempool_id'		=> $itempool_id,
							'item_updated_by'	=> $this->admin_user
						))->execute($item_id);
	
						if(!$objQuery) {
							$retu[] = false;
							break;
						}
						$hook_id[] = $item_id;
					} else {
						$added_mems[] = $member_id;
						$objQuery = $this->db->prepare("INSERT INTO __items :p")->set(array(
							'item_name'			=> $item_name,
							'item_value'		=> $item_value,
							'member_id'			=> $member_id,
							'raid_id'			=> $raid_id,
							'item_date'			=> $time,
							'item_group_key'	=> $new_group_key,
							'item_added_by'		=> $this->admin_user,
							'itempool_id'		=> $itempool_id
						))->execute();
						
						if(!$objQuery) {
							$retu[] = false;
							break;
						}
					}
				}
				if(is_array($items2del)) {
					foreach($items2del as $item_id => $member_id) {
						$objQuery = $this->db->prepare("DELETE FROM __items WHERE item_id = ?")->execute($item_id);
						
						if(!$objQuery) {
							$retu[] = false;
							break;
						}
						$hook_id[] = $item_id;
					}
				}
			}
			if(!in_array(false, $retu)) {
				$old_names = $this->pdh->aget('member', 'name', '0', array($old['buyers']));
				$new_name_string = get_coloured_names($updated_mems, $added_mems, $items2del);
				$itempool = $this->pdh->get('itempool', 'name', $itempool_id);
				//insert log
				$arrOld = array(
						'item_name'			=> $old['name'],
						'member_id'			=> implode(', ', $old_names),
						'raid_id'			=> $old['raid_id'],
						'item_value'		=> $old['value'],
						'game_itemid'		=> $old['game_itemid'],
						'itempool'			=> $old['itempool_id'],
						'item_date'			=> '{D_'.$old['date'].'}',
				);
				$arrNew = array(
						'item_name'			=> $item_name,
						'member_id'			=> $new_name_string,
						'raid_id'			=> $raid_id,
						'item_value'		=> $item_value,
						'item_date'			=> '{D_'.$time.'}',
						'game_itemid'		=> $game_item_id,
						'itempool'			=> $itempool_id
				);
				
				$log_action = $this->logs->diff($arrOld, $arrNew, $this->arrLogLang);
				$this->log_insert('action_item_updated', $log_action, $item_id, $old['name']);
				$this->pdh->enqueue_hook('item_update', $hook_id);
				$this->db->commitTransaction();
				return true;
			}
			$this->db->rollbackTransaction();
			return false;
		}

		public function delete_item($item_id) {
			//fetch old-data
			$old['name'] = $this->pdh->get('item', 'name', array($item_id));
			$old['buyer'] = $this->pdh->get('item', 'buyer', array($item_id));
			$old['value'] = $this->pdh->get('item', 'value', array($item_id));
			$old['raid_id'] = $this->pdh->get('item', 'raid_id', array($item_id));
			$old['itempool'] = $this->pdh->get('itempool', 'name', array($this->pdh->get('item', 'itempool_id', array($item_id))));
			$old['date'] = $this->pdh->get('item', 'date', array($item_id));
			
			$objQuery = $this->db->prepare("DELETE FROM __items WHERE item_id = ?;")->execute($item_id);
			
			if($objQuery) {
				//insert log
				$log_action = array(
					'{L_NAME}'		=> $old['name'],
					'{L_BUYERS}'	=> $old['buyer'],
					'{L_RAID_ID}'	=> $old['raid_id'],
					'{L_ITEMPOOL}'	=> $old['itempool'],
					'{L_DATE}'		=> '{D_'.$old['date'].'}',
					'{L_VALUE}'		=> $old['value']);
				
				$this->log_insert('action_item_deleted', $log_action, $item_id, $old['name']);
				$this->pdh->enqueue_hook('item_update', $item_id);
				return true;
			}
			return false;
		}
		
		public function delete_itemsofraid($raid_id) {
			$items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
			if(count($items) < 1) return true;
			$objQuery = $this->db->prepare("DELETE FROM __items WHERE item_id :in")->in($items)->execute();

			$log_action = array(
				'{L_ID}'		=> implode(', ', $items),
				'{L_RAID_ID}'	=> $raid_id
			);
			$this->log_insert('action_itemsofraid_deleted', $log_action, $raid_id, $this->pdh->get('raid', 'event_name', array($raid_id)));
			$this->pdh->enqueue_hook('item_update', $items);
			return true;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __items;");
			$this->pdh->enqueue_hook('item_update');
		}
	}//end class
}//end if
?>