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
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_itempool($name, $desc) {
			$arrSet = array(
				'itempool_name' => $name,
				'itempool_desc' => $desc,
			);
			if($this->db->query("INSERT INTO __itempool :params", $arrSet)) {
				$id = $this->db->insert_id();
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

			if($this->db->query("UPDATE __itempool SET :params WHERE itempool_id=?", $arrSet, $id)) {
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

			$this->db->query("START TRANSACTION;");
			if($this->db->query("DELETE FROM __itempool WHERE itempool_id = ?;", false, $id)) {
				if($this->db->query("DELETE FROM __multidkp2itempool WHERE multidkp2itempool_itempool_id =?", false, $id)) {
					if($this->db->query("UPDATE __items SET itempool_id = '1' WHERE itempool_id = ?", false, $id)) {
						$this->pdh->enqueue_hook('itempool_update', array($id));
						$items = $this->pdh->get('item', 'item_ids_of_itempool', array($id));
						$this->pdh->enqueue_hook('item_update', array($items));
						$this->db->query("COMMIT;");
						return true;
					}
				}
			}
			$this->db->query("ROLLBACK;");
			return false;
		}
		
		public function reset() {
			$this->db->query("DELETE FROM __itempool WHERE itempool_id != '1';");
			$this->db->query("TRUNCATE TABLE __multidkp2itempool;");
			$this->pdh->enqueue_hook('itempool_update');
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_itempool', pdh_w_itempool::__shortcuts());
?>