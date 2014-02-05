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
			
			$objQuery = $this->db->query("SELECT itempool_id, itempool_name, itempool_desc FROM __itempool;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->itempools[$row['itempool_id']]['name'] = $row['itempool_name'];
					$this->itempools[$row['itempool_id']]['desc'] = $row['itempool_desc'];
				}
				$this->pdc->put('pdh_itempools_table', $this->itempools, null);
			}
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
?>