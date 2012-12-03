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

if(!class_exists('pdh_w_calendars')) {
	class pdh_w_calendars extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendars;");
			$this->db->query("INSERT INTO __calendars (id,name,color,private,feed,system, type) VALUES ('1','Raids','00628c','0',NULL,'1', '1');");
			$this->db->query("INSERT INTO __calendars (id,name,color,private,feed,system, type) VALUES ('2','Standard','ba1e1e','0',NULL,'0', '2');");
			$this->pdh->enqueue_hook('calendar_update');
		}

		public function update_calendar($id, $name, $color, $feed, $private, $type){
			$old['name']	= $this->pdh->get('calendars', 'name', array($id));
			$old['feed']	= $this->pdh->get('calendars', 'feed', array($id));
			$old['private']	= $this->pdh->get('calendars', 'private', array($id));
			$old['color']	= $this->pdh->get('calendars', 'color', array($id));
			$old['type']	= $this->pdh->get('calendars', 'type', array($id));
			$changes		= false;
			foreach($old as $varname => $value) {
				if(${$varname} != $value) {
					$changes = true;
				}
			}
			if($changes) {
				$statt = $this->db->query("UPDATE __calendars SET :params WHERE id=?", array(
					'name'		=> $this->db->escape($name),
					'feed'		=> $this->db->escape($feed),
					'private'	=> ($private) ? 1 : 0,
					'color'		=> $color,
					'type'		=> $type
				), $id);
				if(!$statt) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('calendar_update', array($id));
			return true;
		}

		public function add_calendar($id, $name, $color, $feed, $private, $type){;
			$result = $this->db->query('INSERT INTO __calendars :params', array(
				'feed'		=> ($feed) ? $feed : '',
				'name'		=> $name,
				'color'		=> $color,
				'private'	=> ($private) ? 1 : 0,
				'type'		=> $type
			));
			$id = $this->db->insert_id();
			$this->pdh->enqueue_hook('calendar_update', array($id));
			return $id;
		}

		public function delete_calendar($id){
			if(!$this->pdh->get('calendars', 'system', array($id))){
				$this->db->query("DELETE FROM __calendars WHERE id=?", false, $id);
				$this->pdh->enqueue_hook('calendar_update', array($id));
				return true;
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_calendars', pdh_w_calendars::__shortcuts());
?>