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

if(!class_exists('pdh_w_portal_blocks')) {
	class pdh_w_portal_blocks extends pdh_w_generic {

		public function delete($id) {
			$objQuery = $this->db->prepare("DELETE FROM __portal_blocks WHERE id=?" )->execute($id);
			$this->pdh->enqueue_hook('portal_blocks_update');
		}
		
		public function add($strName, $intWideContent){
			$objQuery = $this->db->prepare("INSERT INTO __portal_blocks :p")->set(array(
				'name' 			=> $strName,
				'wide_content'	=> $intWideContent,
			))->execute();

			if($objQuery){
				$this->pdh->enqueue_hook('portal_blocks_update');
				return $objQuery->insertId;
			}
			
			return false;
		}
		
		public function update($id, $strName, $intWideContent){
			$objQuery = $this->db->prepare("UPDATE __portal_blocks :p WHERE id=?")->set(array(
				'name' 			=> $strName,
				'wide_content'	=> $intWideContent,
			))->execute($id);
						
			if ($objQuery){
				$this->pdh->enqueue_hook('portal_blocks_update');
				return $id;
			}
			
			return false;
		}
		
		
	}
}
?>