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

if ( !class_exists( "pdh_w_item" ) ) {
	class pdh_w_item extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'game');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_item($item_name, $item_buyers, $raid_id, $game_item_id, $item_value, $itempool_id, $time=false) {
			$time		= ($time) ? $time : time();
			$group_key	= $this->gen_group_key($time, array($item_name, $raid_id, $item_value));
			$query		= array();

			if(!is_array($item_buyers) AND $item_buyers) {
				$member			= $item_buyers;
				$item_buyers	= array($member);
			}
			foreach($item_buyers as $member_id){
				$blnResult = $this->db->query("INSERT INTO __items :params", array(
					'item_name'			=> $item_name,
					'member_id'			=> $member_id,
					'raid_id'			=> $raid_id,
					'item_value'		=> $item_value,
					'item_date'			=> $time,
					'item_group_key'	=> $group_key,
					'item_added_by'		=> $this->admin_user,
					'game_itemid'		=> ($game_item_id) ? $game_item_id : 0,
					'itempool_id'		=> $itempool_id
				));
			}

			if($blnResult) {
				//insert log
				$item_id = $this->db->insert_id();
				$log_action = array(
					'{L_ID}'		=> $item_id,
					'{L_NAME}'		=> $item_name,
					'{L_BUYERS}'	=> implode(', ', $this->pdh->aget('member', 'name', '0', array($item_buyers))),
					'{L_RAID_ID}'	=> $raid_id,
					'{L_VALUE}'		=> $item_value,
					'{L_ITEMPOOL}'	=> $this->pdh->get('itempool', 'name', $itempool_id),
					'{L_ADDED_BY}'	=> $this->admin_user
				);
				$this->log_insert('action_item_added', $log_action);
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
	
			#$this->db->query("START TRANSACTION;");
			$retu = array(true);
			$updated_mems = array();
			$added_mems = array();
			$items2del = array();
			if($id || (count($item_buyers) == 1 && count($old['buyers']) == 1))	{
				$succ_data = $this->db->query("UPDATE __items SET :params WHERE item_id=?;",array(
					'item_name'			=> $item_name,
					'item_value'		=> $item_value,
					'member_id'			=> $item_buyers[0],
					'raid_id'			=> $raid_id,
					'item_date'			=> $time,
					'item_group_key'	=> $new_group_key,
					'game_itemid'		=> ($game_item_id) ? $game_item_id : 0,
					'itempool_id'		=> $itempool_id
				), $item_id);
				
				if(!$succ_data) {
					#$this->db->query("ROLLBACK;");
					return false;
				}
				$hook_id = $item_id;
			} else {
				$items2del = $old['buyers'];
				foreach($item_buyers as $member_id) {
					$item_id = array_search($member_id, $old['buyers']);
					if($item_id !== false) {
						$updated_mems[] = $member_id;
						unset($items2del[$item_id]);
						$succ_data = $this->db->query("UPDATE __items SET :params WHERE item_group_key = ?;", array(
							'item_name'			=> $item_name,
							'item_value'		=> $item_value,
							'member_id'			=> $member_id,
							'raid_id'			=> $raid_id,
							'item_date'			=> $time,
							'item_group_key'	=> $new_group_key,
							'game_itemid'		=> ($game_item_id) ? $game_item_id : 0,
							'itempool_id'		=> $itempool_id,
							'item_updated_by'	=> $this->admin_user
						), $group_key);

						if(!$succ_data) {
							$retu[] = false;
							break;
						}
						$hook_id[] = $item_id;
					} else {
						$added_mems[] = $member_id;
						$blaa = $this->db->query("INSERT INTO __items :params", array(
							'item_name'			=> $item_name,
							'item_value'		=> $item_value,
							'member_id'			=> $member_id,
							'raid_id'			=> $raid_id,
							'item_date'			=> $time,
							'item_group_key'	=> $new_group_key,
							'item_added_by'		=> $this->admin_user,
							'itempool_id'		=> $itempool_id
						));
						if(!$blaa) {
							$retu[] = false;
							break;
						}
					}
				}
				if(is_array($items2del)) {
					foreach($items2del as $item_id => $member_id) {
						if(!$this->db->query("DELETE FROM __items WHERE item_id = ?", false, $item_id)) {
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
				$log_action = array(
					'{L_ID}'				=> $item_id,
					'{L_NAME_BEFORE}'		=> $old['name'],
					'{L_BUYERS_BEFORE}'		=> implode(', ', $old_names),
					'{L_RAID_ID_BEFORE}'	=> $old['raid_id'],
					'{L_VALUE_BEFORE}'		=> $old['value'],
					'{L_ITEMPOOL_BEFORE}'	=> $this->pdh->get('itempool', 'name', $old['itempool_id']),
					'{L_NAME_AFTER}'		=> ($old['name'] != $item_name) ? "<span class=\"negative\">".$item_name."</span>" : $item_name,
					'{L_BUYERS_AFTER}'		=> $new_name_string,
					'{L_RAID_ID_AFTER}'		=> ($old['raid_id'] != $raid_id) ? "<span class=\"negative\">".$raid_id."</span>" : $raid_id,
					'{L_VALUE_AFTER}'		=> ($old['value'] != $item_value) ? "<span class=\"negative\">".$item_value."</span>" : $item_value,
					'{L_ITEMPOOL_AFTER}'	=> ($old['itempool_id'] != $itempool_id) ? "<span class=\"negative\">".$itempool."</span>" : $itempool,
					'{L_UPDATED_BY}'		=> $this->admin_user
				);
				$this->log_insert('action_item_updated', $log_action);
				#$this->db->query("COMMIT;");
				$this->pdh->enqueue_hook('item_update', $hook_id);
				return true;
			}
			#$this->db->query("ROLLBACK;");
			return false;
		}

		public function delete_item($item_id) {
			//fetch old-data
			$old['name'] = $this->pdh->get('item', 'name', array($item_id));
			$old['buyer'] = $this->pdh->get('item', 'buyer', array($item_id));
			$old['value'] = $this->pdh->get('item', 'value', array($item_id));
			$old['raid_id'] = $this->pdh->get('item', 'raid_id', array($item_id));
			$old['itempool'] = $this->pdh->get('itempool', 'name', array($this->pdh->get('item', 'itempool_id', array($item_id))));
			if($this->db->query("DELETE FROM __items WHERE item_id = ?;", false, $item_id)) {
				//insert log
				$log_action = array(
					'{L_ID}'		=> $item_id,
					'{L_NAME}'		=> $old['name'],
					'{L_BUYERS}'	=> $old['buyer'],
					'{L_RAID_ID}'	=> $old['raid_id'],
					'{L_ITEMPOOL}'	=> $old['itempool'],
					'{L_VALUE}'		=> $old['value']);
				$this->log_insert('action_item_deleted', $log_action);
				$this->pdh->enqueue_hook('item_update', $item_id);
				return true;
			}
			return false;
		}
		
		public function delete_itemsofraid($raid_id) {
			$items = $this->pdh->get('item', 'itemsofraid', array($raid_id));
			if(count($items) < 1) return true;
			$this->db->query("DELETE FROM __items WHERE item_id IN ('".implode("', '", $items)."');");
			$log_action = array(
				'{L_ID}'		=> implode(', ', $items),
				'{L_RAID_ID}'	=> $raid_id
			);
			$this->log_insert('action_itemsofraid_deleted', $log_action);
			$this->pdh->enqueue_hook('item_update', $items);
			return true;
		}
		
		public function reset() {
			$this->db->query("TRUNCATE TABLE __items;");
			$this->pdh->enqueue_hook('item_update');
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_item', pdh_w_item::__shortcuts());
?>