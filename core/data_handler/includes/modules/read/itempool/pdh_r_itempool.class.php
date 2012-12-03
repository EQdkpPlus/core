<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_itempool')){
	class pdh_r_itempool extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang	= 'english';
		public $itempools		= array();

		public $hooks = array(
			'itempool_update'
		);

		public function reset(){
			$this->pdc->del('pdh_itempools_table');
			$this->itempools = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->itempools = $this->pdc->get('pdh_itempools_table');
			if($this->itempools !== NULL){
				return true;
			}

			$this->itempools = array();
			$i_sql = "SELECT itempool_id, itempool_name, itempool_desc FROM __itempool;";
			$i_result = $this->db->query($i_sql);
			while( $row = $this->db->fetch_record($i_result)){
				$this->itempools[$row['itempool_id']]['name'] = $row['itempool_name'];
				$this->itempools[$row['itempool_id']]['desc'] = $row['itempool_desc'];
			}
			$this->db->free_result($i_result);
			$this->pdc->put('pdh_itempools_table', $this->itempools, null);
		}

		public function get_id_list(){
			return array_keys($this->itempools);
		}

		public function get_id($itempool_name){
			foreach($this->itempools as $id => $itempool){
				if($itempool['name'] == $name){
					return $id;
				}
			}
		}

		public function get_name($itempool_id){
			return (isset($this->itempools[$itempool_id]['name'])) ? $this->itempools[$itempool_id]['name'] : '';
		}

		public function get_desc($itempool_id){
			return (isset($this->itempools[$itempool_id]['desc'])) ? $this->itempools[$itempool_id]['desc'] : '';
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_itempool', pdh_r_itempool::__shortcuts());
?>