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
	
		public function install($path, $plugin='', $name='', $version='', $child = false) {
			$dbdata = array(
				'name'			=> $name,
				'path'			=> $path,
				'plugin'		=> $plugin,
				'version'		=> $version,
				'child'			=> ($child) ? 1 : 0,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __portal :p")->set($dbdata)->execute();
			
			if($objQuery) {
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('update_portal', array($id));
				return $id;
			}
			return false;
		}

		public function delete($id, $type='id') {
			if($type != 'id' && $type != 'path') $type = 'id';
			if($id){
				$objQuery = $this->db->prepare("DELETE FROM __portal WHERE ".$type." = ?")->execute($id);
				if ($objQuery){
					$this->pdh->enqueue_hook('update_portal', array($id));
					return true;
				}
			}
			return false;
		}

		public function update($id, $data) {
			if (!$id) return false;
			
			$objQuery = $this->db->prepare("UPDATE __portal :p WHERE id = ?;")->set($data)->execute($id);
			if($objQuery) {
				$this->pdh->enqueue_hook('update_portal', array($id));
				return true;
			}
			return false;
		}
		
	}//end class
}//end if
?>