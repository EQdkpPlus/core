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

if(!class_exists('pdh_w_event')) {
	class pdh_w_event extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}
		
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
			if($this->db->query("INSERT INTO __events :params", $arrSet)) {
				$id = $this->db->insert_id();
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
			
			if($this->db->query("UPDATE __events SET :params WHERE event_id =?", $arrSet, $id)) {
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

			$this->db->query("START TRANSACTION");
			if($this->db->query("DELETE FROM __events WHERE event_id = ?;", false, $id)) {
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
				if(!in_array(false, $retu, true) AND $this->db->query("DELETE FROM __multidkp2event WHERE multidkp2event_event_id = ?;", false, $id)) {
					$this->db->query("COMMIT");
					$this->pdh->enqueue_hook('event_update', array($id));
					return true;
				}
			}
			$this->db->query("ROLLBACK");
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