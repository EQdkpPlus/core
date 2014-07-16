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

if(!class_exists('pdh_w_portal_layouts')) {
	class pdh_w_portal_layouts extends pdh_w_generic {

		public function __construct() {
			parent::__construct();
		}

		public function delete($id) {
			$objQuery = $this->db->prepare("DELETE FROM __portal_layouts WHERE id =?")->execute($id);
			
			$arrQuery = array(
				'portal_layout' => 1,
			);
			
			$objQuery = $this->db->prepare("UPDATE __article_categories :p WHERE id=?")->set($arrQuery)->execute($id);
			
			$this->pdh->enqueue_hook('article_categories_update');
			$this->pdh->enqueue_hook('portal_layouts_update');
			return $objQuery;
		}
		
		public function add($strName, $arrBlocks, $arrModules){
			$objQuery = $this->db->prepare("INSERT INTO __portal_layouts :p")->set(array(
				'name' 			=> $strName,
				'blocks'		=> serialize($arrBlocks),
				'modules'		=> serialize($arrModules),
			))->execute();
			if($objQuery){
				$this->pdh->enqueue_hook('portal_layouts_update');
				return $objQuery->insertId;
			}
			
			return false;
		}
		
		public function update($id, $strName, $arrBlocks, $arrModules){
			$objQuery = $this->db->prepare("UPDATE __portal_layouts :p WHERE id=?")->set(array(
				'name' 			=> $strName,
				'blocks'		=> serialize($arrBlocks),
				'modules'		=> serialize($arrModules),
			))->execute($id);
						
			if ($objQuery){
				$this->pdh->enqueue_hook('portal_layouts_update');
				return $id;
			}
			
			return false;
		}
		
		
	}
}
?>