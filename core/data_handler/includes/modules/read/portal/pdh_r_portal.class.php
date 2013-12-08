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

if ( !class_exists( "pdh_r_portal" ) ) {
	class pdh_r_portal extends pdh_r_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $portal;

		public $hooks = array(
			'update_portal'
		);

		public function reset(){
			$this->pdc->del('pdh_portal_table');
			$this->portal = NULL;
		}

		public function init(){
			$this->portal	= $this->pdc->get('pdh_portal_table');
			if($this->portal !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __portal");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()){
					$this->portal[$drow['id']] = array(
						'name'			=> $drow['name'],
						'path'			=> $drow['path'],
						'version'		=> $drow['version'],
						'plugin'		=> $drow['plugin'],
						'child'			=> $drow['child'],
					);
				}
			}

			$this->pdc->put('pdh_portal_table', $this->portal, null);
		}

		private function filter($type, $ids) {
			if(is_array($type[1])) {
				foreach($type[1] as $s_type) {
					$ids = $this->filter(array($type[0], $s_type), $ids);
				}
			} elseif(is_array($ids)) {
				foreach($ids as $key => $id) {
					$val = $this->portal[$id][$type[0]];
					if(is_array($val)) {
						if(!in_array($type[1], $val)) unset($ids[$key]);
					} else {
						if(($type[1] === 0 AND $val !== $type[1]) OR ($type[1] !== 0 AND $val != $type[1])) unset($ids[$key]);
					}
				}
				return $ids;
			}
		}

		public function get_portal($id=''){
			return ($id) ? $this->portal[$id] : $this->portal;
		}

		public function get_id_list($filters=array()) {
			$ids = (!empty($this->portal)) ? array_keys($this->portal) : array();
			if(count($filters) > 0) {
				foreach($filters as $filter => $val) {
					$ids = $this->filter(array($filter, $val), $ids);
				}
			}
			return $ids;
		}

		public function get_path($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['path'] : false;
		}

		public function get_plugin($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['plugin'] : false;
		}

		public function get_name($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['name'] : false;
		}

		public function get_version($id) {
			return (isset($this->portal[$id])) ? $this->portal[$id]['version'] : false;
		}

		public function get_child($id){
			return (isset($this->portal[$id])) ? (int)$this->portal[$id]['child'] : false;
		}
	}//end class
}//end if
?>