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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_portal_layouts" ) ) {
	class pdh_r_portal_layouts extends pdh_r_generic{

		public $default_lang = 'english';
		public $layouts;

		public $hooks = array(
			'portal_layouts_update'
		);
		
		public $presets = array(
			'portal_layout_name' 	=> array('name', array('%layout_id%'), array()),
			'portal_layout_blocks'	=> array('blocks', array('%layout_id%'), array()),
			'portal_layout_usedby'	=>  array('usedby', array('%layout_id%'), array()),
			'portal_layout_editicon' => array('editicon', array('%layout_id%'), array()),
		);

		public function reset(){
			$this->pdc->del('pdh_portal_layouts_table');
			$this->layouts = NULL;
		}

		public function init(){
			$this->layouts	= $this->pdc->get('pdh_portal_layouts_table');
			if($this->layouts !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __portal_layouts");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->layouts[intval($drow['id'])] = array(
						'id'				=> intval($drow['id']),
						'name'				=> $drow['name'],
						'blocks'			=> unserialize($drow['blocks']),
						'modules'			=> unserialize($drow['modules'])
					);
				}
				
				$this->pdc->put('pdh_portal_layouts_table', $this->layouts, null);
			}
		}

		public function get_id_list() {
			return array_keys($this->layouts);
		}
		
		public function get_name($intLayoutID){
			if (isset($this->layouts[$intLayoutID])){
				return $this->layouts[$intLayoutID]['name'];
			}
			return false;
		}

		public function get_blocks($intLayoutID){
			if (isset($this->layouts[$intLayoutID])){
				return $this->layouts[$intLayoutID]['blocks'];
			}
			return false;
		}
		
		public function get_html_blocks($intLayoutID){
			$arrBlocks = $this->get_blocks($intLayoutID);
			if ($arrBlocks){
				foreach($arrBlocks as $strBlockID){
					if (strpos($strBlockID, 'block') === 0) {
						$arrOut[] = $this->pdh->get('portal_blocks', 'name', array(str_replace('block', '', $strBlockID)));
					} else {
						$arrOut[] = $this->user->lang('portalplugin_'.$strBlockID);
					}
				}
				return implode(', ', $arrOut);
			}
			return '';
		}
		
		public function get_modules($intLayoutID){
			if (isset($this->layouts[$intLayoutID])){
				return $this->layouts[$intLayoutID]['modules'];
			}
			return false;
		}
		
		public function get_usedby($intLayoutID){
			return $this->pdh->get('article_categories', 'used_portallayout_number', array($intLayoutID));
		}
		
		public function get_editicon($intLayoutID){
			return '<a href="'.$this->root_path.'admin/manage_portal.php'.$this->SID.'&amp;l='.$intLayoutID.'"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
		
		public function get_checkbox_check($intLayoutID){
			if ($intLayoutID == 1) return false;
			return true;
		}
		
	}//end class
}//end if
?>