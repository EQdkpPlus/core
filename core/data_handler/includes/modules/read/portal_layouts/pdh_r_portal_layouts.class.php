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