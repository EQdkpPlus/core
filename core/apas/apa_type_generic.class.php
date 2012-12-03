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
 
if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if( !class_exists( "apa_type_generic" ) ) {
	abstract class apa_type_generic extends gen_class {
		protected $options = array(
			'name'	=> array(
				'name'		=> 'name',
				'type'		=> 'text',
				'size'		=> 20,
				'value'		=> '',
				'class'		=> 'input required'
			),
			'exectime'	=> array(
				'name'		=> 'exectime',
				'type'		=> 'timepicker',
				'value'		=> 14400,
				'hour'		=> 4,
				'min'		=> 0,
				'class'		=> 'required'
			),
			'pools'	=> array(
				'name'		=> 'pools',
				'type'		=> 'jq_multiselect',
				'value'		=> 0
			)
		);
		
		protected $required = array('name', 'exectime', 'pools');

		protected $ext_options = array();

		
		abstract public function modules_affected();
		abstract public function get_decay_val($apa_id, $date, $module, $dkp_id, $data);
		abstract public function get_cache_date($date, $apa_id);
		
		public function pre_save_func($apa_id, $options) {
			return $options;
		}

		public function required() {
			return $this->required;
		}

		public function set_values($apa){
			$this->options = $this->get_options();
			foreach($this->options as $key => $value){
				if(isset($apa[$key])){
					$this->options[$key]['value'] = $apa[$key];
				}
			}
			return $this->options;
		}

		public function get_options(){
			return $this->options;
		}
		
		//default functions; if layout changes are necessary, implement functions in specific type
		public function add_layout_changes($apa_id) {
			return true;
		}
		
		public function update_layout_changes($apa_id) {
			return true;
		}
		
		public function delete_layout_changes($apa_id) {
			return true;
		}
	}
}
?>