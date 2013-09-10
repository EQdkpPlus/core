<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date: 2013-01-29 17:35:08 +0100 (Di, 29 Jan 2013) $
* -----------------------------------------------------------------------
* @author		$Author: wallenium $
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev: 12937 $
*
* $Id: pdh_w_portal_blocks.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_portal_blocks')) {
	class pdh_w_portal_blocks extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'pfh', 'user', 'time',  'bbcode'=>'bbcode', 'embedly'=>'embedly');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

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