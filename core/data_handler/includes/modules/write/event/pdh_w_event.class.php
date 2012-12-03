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
					'id'			=> $id,
					'{L_NAME}'		=> $name,
					'{L_VALUE}'		=> $value,
					'{L_ICON}'		=> $icon,
					'{L_ADDED_BY}'	=> $this->admin_user
				);
				$this->log_insert('action_event_added', $log_action);
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
				$log_action = array(
					'id'				=> $id,
					'{L_NAME_BEFORE}'	=> $old['name'],
					'{L_VALUE_BEFORE}'	=> $old['value'],
					'{L_ICON_BEFORE}'	=> $old['icon'],
					'{L_NAME_AFTER}'	=> ($old['name'] != $name) ? '<span class=\"negative\">'.$name.'</span>' : $name,
					'{L_VALUE_AFTER}'	=> ($old['value'] != $value) ? '<span class=\"negative\">'.$value.'</span>' : $value,
					'{L_ICON_AFTER}'	=> ($old['icon'] != $icon) ? '<span class=\"negatvie\">'.$icon.'</span>' : $icon,
					'{L_UPDATED_BY}'	=> $this->admin_user
				);
				$this->log_insert('action_event_updated', $log_action);
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
					'id'			=> $id,
					'{L_NAME}'		=> $old['name'],
					'{L_VALUE}'		=> $old['value'],
					'{L_ICON}'		=> $old['icon']
				);
				$this->log_insert('action_event_deleted', $log_action);

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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_event', pdh_w_event::__shortcuts());
?>