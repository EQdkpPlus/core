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

if ( !class_exists( "pdh_r_portal_blocks" ) ) {
	class pdh_r_portal_blocks extends pdh_r_generic{

		public $default_lang = 'english';
		public $blocks;

		public $hooks = array(
			'portal_blocks_update'
		);

		public function reset(){
			$this->pdc->del('pdh_portal_blocks_table');
			$this->blocks = NULL;
		}
		
		public $presets = array(
				'portal_block_name' 		=> array('name', array('%block_id%'), array()),
				'portal_block_wide_content'	=> array('wide_content', array('%block_id%'), array()),
				'portal_block_usedby'		=>  array('usedby', array('%block_id%'), array()),
				'portal_block_editicon' 	=> array('editicon', array('%block_id%'), array()),
				'portal_block_templatevar' 	=> array('templatevar', array('%block_id%'), array()),
		);

		public function init(){
			$this->blocks	= $this->pdc->get('pdh_portal_blocks_table');
			if($this->blocks !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __portal_blocks");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->blocks[intval($drow['id'])] = array(
						'id'				=> intval($drow['id']),
						'name'				=> $drow['name'],
						'wide_content'		=> intval($drow['wide_content']),
					);
				}
				
				$this->pdc->put('pdh_portal_blocks_table', $this->blocks, null);
			}		
		}

		public function get_id_list() {
			return (empty($this->blocks)) ? array() : array_keys($this->blocks);
		}
		
		public function get_name($intBlockID){
			if (isset($this->blocks[$intBlockID])){
				return $this->blocks[$intBlockID]['name'];
			}
			return false;
		}
		
		public function get_wide_content($intBlockID){
			if (isset($this->blocks[$intBlockID])){
				return $this->blocks[$intBlockID]['wide_content'];
			}
			return false;
		}
		
		public function get_html_wide_content($intBlockID){
			if ($this->get_wide_content($intBlockID)){
				return $this->user->lang('yes');
			}
			return $this->user->lang('no');
		}
		
		public function get_usedby($intBlockID){
			$arrLayoutIDs = $this->pdh->get('portal_layouts', 'id_list');
			$intLayoutCount = 0;
			foreach($arrLayoutIDs as $intLayoutID){
				$arrBlocks = $this->pdh->get('portal_layouts', 'blocks', array($intLayoutID));
				if (in_array('block'.$intBlockID, $arrBlocks)) $intLayoutCount++;
			}
			return $intLayoutCount;
		}
		
		public function get_editicon($intBlockID){
			return '<a href="'.$this->root_path.'admin/manage_portal.php'.$this->SID.'&amp;b='.$intBlockID.'"><i class="fa fa-pencil fa-lg" title="'.$this->user->lang('edit').'"></i></a>';
		}
		
		public function get_templatevar($intBlockID){
			return '{PORTAL_BLOCK'.$intBlockID.'}';
		}
		
	}//end class
}//end if
?>