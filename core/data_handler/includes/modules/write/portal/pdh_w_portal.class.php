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

if ( !class_exists( "pdh_w_portal" ) ) {
	class pdh_w_portal extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function install($path, $plugin='', $name='', $settings='0', $install=array(), $child = false) {
			$data = array(
				'name'			=> $name,
				'path'			=> $path,
				'settings'		=> $settings,
				'plugin'		=> $plugin,
				'enabled'		=> ((isset($install['autoenable'])) ? (($install['autoenable']) ? 1 : 0) : '0'),
				'position'		=> ((isset($install['defaultposition'])) ? $install['defaultposition'] : ''),
				'number'		=> ((isset($install['defaultnumber'])) ? (($install['defaultnumber']) ? 1 : 0) : ''),
				'visibility'	=> ((isset($install['visibility'])) ? serialize($install['visibility']) : serialize(array(0))),
				'collapsable'	=> ((isset($install['collapsable'])) ? (($install['collapsable']) ? 1 : 0) : '1'),
				'child'			=> ($child) ? 1 : 0,
			);
			if($this->db->query("INSERT INTO __portal :params", $data)) {
				$id = $this->db->insert_id();
				$this->pdh->enqueue_hook('update_portal', array($id));
				return $id;
			}
			return false;
		}

		public function delete($id, $type='id') {
			if($type != 'id' && $type != 'path') $type = 'id';
			if($id && $this->db->query("DELETE FROM __portal WHERE ".$this->db->escape($type)." = ?", false, $id)) {
				$this->pdh->enqueue_hook('update_portal', array($id));
				return true;
			}
			return false;
		}

		public function update($id, $data) {
			if($id && $this->db->query("UPDATE __portal SET :params WHERE id = ?;", $data, $id)) {
				$this->pdh->enqueue_hook('update_portal', array($id));
				return true;
			}
			return false;
		}

		public function disable_enable($id, $status='0') {
			if($this->pdh->get('portal', 'enabled', array($id)) == $status) return true;
			if($id && $this->db->query("UPDATE __portal SET enabled = '".$this->db->escape($status)."' WHERE id = ?;", false, $id)) {
				$this->pdh->enqueue_hook('update_portal', array($id));
				return true;
			}
			return false;
		}
		
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_portal', pdh_w_portal::__shortcuts());
?>