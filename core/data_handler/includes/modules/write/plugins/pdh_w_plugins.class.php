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

		public function update_version($version, $plugin_code) {
			$objQuery = $this->db->prepare("UPDATE __plugins :p WHERE code=?;")->set(array(
					'version'	=> $version
			))->execute($plugin_code);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($plugin_code));
				return true;
			}
			return false;
		}

		public function delete_plugin($plugin_code) {
			$objQuery = $this->db->prepare("DELETE FROM __plugins WHERE `code` = ?")->execute($plugin_code);
				
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($plugin_code));
				return true;
			}
			return false;
		}

		public function add_plugin($code, $version) {
			$objQuery = $this->db->prepare("INSERT INTO __plugins :p")->set(array(
				'code'		=> $code,
				'status'	=> '0',
				'version'	=> $version
			))->execute();
			
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($code));
				return true;
			}
			return false;
		}
		
		public function set_status($code, $status) {
			$objQuery = $this->db->prepare("UPDATE __plugins :p WHERE code=?;")->set(array('status' => $status))->execute($code);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('plugins_update', array($code));
				return true;
			}
			return false;
		}
	}
}
?>