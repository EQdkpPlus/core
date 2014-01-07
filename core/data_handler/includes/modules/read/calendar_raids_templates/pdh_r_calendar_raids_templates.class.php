<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('pdh_r_calendar_raids_templates')){
	class pdh_r_calendar_raids_templates extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		private $rctemplates;
		public $hooks = array(
			'calendar_templates_update',
		);

		/**
		* Constructor
		*/
		public function __construct(){
		}

		/**
		* reset
		*/
		public function reset(){
			$this->pdc->del('pdh_calendar_raids_table.templates');
			$this->pdc->del_prefix('plugin.guests');
			$this->rctemplates = NULL;
		}

		/**
		* init
		*
		* @returns boolean
		*/
		public function init(){
			// try to get from cache first
			$this->guests		= $this->pdc->get('pdh_calendar_raids_table.templates');
			if($this->rctemplates !== NULL){
				return true;
			}

			// empty array as default
			$this->rctemplates	= array();
			$myresult		= $this->db->query('SELECT * FROM __calendar_raid_templates;');
			while ($row = $this->db->fetch_record($myresult)){
				$templatearray = json_decode($row['tpldata'], true);
				$this->rctemplates[$row['id']]['name'] = $row['name'];
				if(is_array($templatearray)){
					foreach($templatearray as $tplkey=>$tplvalue){
						$this->rctemplates[$row['id']][$tplkey] = $tplvalue;
					}
				}
			}
			if($myresult) $this->pdc->put('pdh_calendar_raids_table.templates', $this->rctemplates, NULL);
			return true;
		}

		public function get_id_list(){
			return array_keys($this->rctemplates);
		}

		public function get_dropdowndata(){
			$out = array(''=>'----');
			if(is_array($this->rctemplates)){
				foreach($this->rctemplates as $tplid=>$data){
					$out[$tplid]	= $data['name'];
				}
			}
			return $out;
		}

		public function get_idbyname($name){
			if(is_array($this->rctemplates)){
				foreach($this->rctemplates as $tplid=>$data){
					if($data['name'] == $name){
						return $tplid;
					}
				}
			}
		}

		public function get_name($id){
			return $this->rctemplates[$id]['name'];
		}

		public function get_templates($id=''){
			return ($id) ? $this->rctemplates[$id] : $this->rctemplates;
		}

	} //end class
} //end if class not exists

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_calendar_raids_templates', pdh_r_calendar_raids_templates::__shortcuts());
?>