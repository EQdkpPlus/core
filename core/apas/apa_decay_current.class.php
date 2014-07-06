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

if ( !class_exists( "apa_decay_current" ) ) {
	class apa_decay_current extends apa_type_generic {
		public static $shortcuts = array('apa'=>'auto_point_adjustments');

		protected $ext_options = array(
			'decay_time' => array(
				'type'		=> 'spinner',
				'max'		=> 99,
				'min'		=> 1,
				'step'		=> 1,
				'size'		=> 2,
				'default'	=> 1
			),
			'start_date' => array(
				'type'		=> 'datepicker',
				'timepicker' => true,
				'default'	=> 'now',
				'class'		=> 'input'
			),			
			'calc_func' => array(
				'type'		=> 'dropdown',
				'options'	=> array(),
			),
		);
		
		private $modules_affected = array('current');
		
		private $cached_data = array();

		public function __construct() {
			for($i=1; $i<10; $i++) {
				$this->ext_options['zero_time']['options'][$i] = sprintf($this->user->lang('apa_zero_time_dd'), $i);
			}
			$this->ext_options['start_date']['value'] = $this->time->time;
			$funcs = $this->apa->get_calc_function();
			foreach($funcs as $func) {
				$this->ext_options['calc_func']['options'][$func] = $func;
			}
			$this->options = array_merge($this->options, $this->ext_options);
		}
		
		public function add_layout_changes($apa_id) {
			return true;
		}
		
		public function update_layout_changes($apa_id) {
			return true;
		}
		
		public function delete_layout_changes($apa_id) {
			return true;
		}
		
		public function modules_affected($apa_id) {
			return $this->modules_affected;
		}
		
		// get date of last calculation
		public function get_cache_date($date, $apa_id) {
			$max_ttl = $this->apa->get_data('decay_time', $apa_id)*86400; //decay time in days
			$exectime = $this->apa->get_data('exectime', $apa_id); //exectime as seconds from midnight
			$decay_start = $this->apa->get_data('start_date', $apa_id); //start_date for fetching first day (wether we start on monday, thursday or w/e)
			//set decay_start to next exectime
			$exectime = $this->apa->get_data('exectime', $apa_id);
			if(($decay_start%86400) > $exectime) $decay_start += 86400;
			$decay_start = $decay_start + $exectime - $decay_start%86400;
			//length of decay-period
			$date -= ($date - $decay_start)%$max_ttl;
			return $date;
		}
		
		
		private function calculate_points($apa_id, $dkp_id, $data, $from, $nextCalInterval, $blnStart=false){
			$decayed_val = $value = 0;
			if ($blnStart){
				//Get Points until decay start
				$decayed_val = $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], 0, $this->apa->get_data('start_date', $apa_id), $data['event_id'], $data['itempool_id'], $data['with_twink'])); 
				$data['value'] = $decayed_val;
			} else {
				//Calculate decayed val
				$value = $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], $from, $from+$nextCalInterval, $data['event_id'], $data['itempool_id'], $data['with_twink']));
				$ref_val = $value;
				$value = $value + $data['value'];
				$decayed_val = $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($value, $from, $from, $ref_value));
			}

			if ($from > $this->get_cache_date($this->time->time, $apa_id)){
				return $value;
			} else {
				$data['value'] = $decayed_val;
				$decayed_val = $this->calculate_points($apa_id, $dkp_id, $data, $from+$nextCalInterval+1, $nextCalInterval);
			}
			
			return $decayed_val;
		}
		
		private function calculate_points_history($dataArray, $apa_id, $dkp_id, $data, $from, $nextCalInterval, $blnStart=false){
			$decayed_val = $value = 0;
			if ($blnStart){
				//Get Points until decay start
				$decayed_val = $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], 0, $this->apa->get_data('start_date', $apa_id), $data['event_id'], $data['itempool_id'], $data['with_twink'])); 
				$data['value'] = $decayed_val;
			} else {
				//Calculate decayed val
				$value = $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], $from, $from+$nextCalInterval, $data['event_id'], $data['itempool_id'], $data['with_twink']));
				$ref_val = $value;
				$value = $value + $data['value'];
				$decayed_val = $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($value, $from, $from+$nextCalInterval, $ref_value));
				if ($from <= $this->get_cache_date($this->time->time, $apa_id)) $dataArray[] = array($from+$nextCalInterval, $decayed_val, $value);
			}

			if ($from > $this->get_cache_date($this->time->time, $apa_id)){
				return $dataArray;
			} else {
				$data['value'] = $decayed_val;
				$decayed_val = $this->calculate_points_history($dataArray, $apa_id, $dkp_id, $data, $from+$nextCalInterval+1, $nextCalInterval);
			}
			
			return $decayed_val;
		}
		
		public function get_decay_history($apa_id, $cache_date, $module, $dkp_id, $data){
			$decay_start = $this->apa->get_data('start_date', $apa_id);
			$nextCalculationInterval = $this->apa->get_data('decay_time', $apa_id)*86400;
			$arrHistory = $this->calculate_points_history(array(), $apa_id, $dkp_id, $data, $decay_start, $nextCalculationInterval, true);
			$arrOut = array();
			foreach($arrHistory as $val){
				if ($val[1] == 0 && $val[2] == 0) continue;
				$arrOut[] = array(
					'value' => $val[1] - $val[2],
					'date'	=> $val[0],
					'type'	=> 'apa',
					'id'	=> $apa_id,
					'character' => $data['member_id'],
				);
			}
			
			return $arrOut;
		}
		
		public function get_decay_val($apa_id, $cache_date, $module, $dkp_id, $data) {	
			$decay_start = $this->apa->get_data('start_date', $apa_id);
			
			$nextCalculationInterval = $this->apa->get_data('decay_time', $apa_id)*86400;
			
			$decayed_val = $this->calculate_points($apa_id, $dkp_id, $data, $decay_start, $nextCalculationInterval, true);
			
			$ttl = $this->apa->get_data('decay_time', $apa_id)*86400*3; //3 times decay_period
			return array($decayed_val, $ttl);
		}
	}//end class
}//end if
?>