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
* $Id: pdh_r_portal_layouts.class.php 12937 2013-01-29 16:35:08Z wallenium $
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_portal_layouts" ) ) {
	class pdh_r_portal_layouts extends pdh_r_generic{
	
		public static function __shortcuts() {
			$shortcuts = array('pdc', 'db', 'user', 'pdh');
			return array_merge(parent::$shortcuts, $shortcuts);
		}

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

			$pff_result = $this->db->query("SELECT * FROM __portal_layouts");
			while($drow = $this->db->fetch_record($pff_result) ){
				$this->layouts[intval($drow['id'])] = array(
					'id'				=> intval($drow['id']),
					'name'				=> $drow['name'],
					'blocks'			=> unserialize($drow['blocks']),
					'modules'			=> unserialize($drow['modules'])
				);
			}
			
			$this->db->free_result($pff_result);
			$this->pdc->put('pdh_portal_layouts_table', $this->layouts, null);
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
			return '<a href="'.$this->root_path.'admin/manage_portal.php'.$this->SID.'&amp;l='.$intLayoutID.'"><img src="'.$this->root_path.'images/glyphs/edit.png" alt="edit"/></a>';
		}
		
		public function get_checkbox_check($intLayoutID){
			if ($intLayoutID == 1) return false;
			return true;
		}
		
	}//end class
}//end if
?>