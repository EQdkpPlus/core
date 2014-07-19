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
			'zero_time'	=> array(
				'type'		=> 'dropdown',
				'options'	=> array(),
				'default'	=> 3,
			),
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
			),			
			'calc_func' => array(
				'type'		=> 'dropdown',
				'options'	=> array(),
				'required'	=> true,
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
			if(($decay_start%86400) > $exectime) $decay_start += 86400;
			$decay_start = $decay_start + $exectime - $decay_start%86400;
			//length of decay-period
			$date -= ($date - $decay_start)%$max_ttl;
			return $date;
		}
		
		public function get_decay_val($apa_id, $cache_date, $module, $dkp_id, $data) {
			// load decay parameters, set decay_start to its proper timestamp (from somewhere at that day to exectime)
			$decay_start = $this->apa->get_data('start_date', $apa_id);	
			$exectime = $this->apa->get_data('exectime', $apa_id);
			if(($decay_start%86400) > $exectime) $decay_start += 86400;
			$decay_start = $decay_start + $exectime - $decay_start%86400;
			$decay_time = $this->apa->get_data('decay_time', $apa_id)*86400;
			
			// check if it's the first calculation
			if($cache_date == $decay_start) {
				$value = $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], 0, $decay_start, $data['event_id'], $data['itempool_id'], $data['with_twink']));
				
			// check if it's in the zero-time
			} elseif(($this->time->time - $this->apa->get_data('zero_time', $apa_id)*2592000) > $cache_date) {
				$value = 0;
				
			// normal calculation, get points from previous decay and add currently earned points
			} else {
				$previous_calc = $cache_date-$decay_time;
				$value = $this->apa->get_decay_val($module, $dkp_id, $previous_calc, $data);
				$value += $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], $previous_calc, $cache_date, $data['event_id'], $data['itempool_id'], $data['with_twink']));
			}
			
			// got points up until now (now = cache date), decay them
			$ref_value = 0; // probably unnecessary 
			$decayed_val = $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($value, $cache_date, $this->time->time, $ref_value));
			$decay_adj = $value - $decayed_val;
			
			// if this is the most recent decay, add current points from from last cache date to now
			if(($cache_date + $decay_time) > $this->time->time) {
				$decayed_val += $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], $cache_date, $this->time->time+1, $data['event_id'], $data['itempool_id'], $data['with_twink']));
			}
			
			$ttl = $decay_time*3; //3 times decay_period
			return array($decayed_val, $decay_adj, $ttl);
		}
	}//end class
}//end if
?>