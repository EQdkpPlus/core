<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
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

if(!class_exists('pdh_w_plugins')) {
	class pdh_w_plugins extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function update_version($version, $plugin_code) {
			if($this->db->query("UPDATE __plugins SET :params WHERE code=?;", array('version'	=> $version), $plugin_code)) {
				$this->pdh->enqueue_hook('plugins_update', array($plugin_code));
				return true;
			}
			return false;
		}

		public function delete_plugin($plugin_code) {
			if($this->db->query("DELETE FROM __plugins WHERE `code` = ?", false, $plugin_code)) {
				$this->pdh->enqueue_hook('plugins_update', array($plugin_code));
				return true;
			}
			return false;
		}

		public function add_plugin($code, $version) {
			if($this->db->query("INSERT INTO __plugins :params", array(
				'code'		=> $code,
				'status'	=> '0',
				'version'	=> $version
			))) {
				$this->pdh->enqueue_hook('plugins_update', array($code));
				return true;
			}
			return false;
		}
		
		public function set_status($code, $status) {
			if($this->db->query("UPDATE __plugins SET :params WHERE code=?;", array('status' => $status), $code)) {
				$this->pdh->enqueue_hook('plugins_update', array($code));
				return true;
			}
			return false;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_plugins', pdh_w_plugins::__shortcuts());
?>