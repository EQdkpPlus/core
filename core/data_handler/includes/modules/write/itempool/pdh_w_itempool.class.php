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