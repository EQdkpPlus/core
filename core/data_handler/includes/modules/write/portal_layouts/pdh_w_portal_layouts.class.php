<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
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