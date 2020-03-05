<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
				'step'		=> 0.5,
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
			for($i=1; $i<37; $i++) {
				$this->ext_options['zero_time']['options'][$i] = sprintf($this->user->lang('apa_zero_time_dd'), $i);
			}
			$this->ext_options['start_date']['value'] = $this->time->time;
			$funcs = $this->apa->get_calc_function();
			foreach($funcs as $func) {
				$this->ext_options['calc_func']['options'][$func] = $func;
			}
			$this->options = array_merge($this->options, $this->ext_options);
		}

		public function modules_affected($apa_id) {
			return $this->modules_affected;
		}

		// get date of last calculation
		public function get_last_run($date, $apa_id) {
			$max_ttl = $this->apa->get_data('decay_time', $apa_id)*86400; //decay time in days
			$decay_start = $this->apa->get_data('start_date', $apa_id); //start_date for fetching first day (wether we start on monday, thursday or w/e)
			list($h,$i) = explode(':', date('H:i', $decay_start));
			$exectime = 3600*$h + 60*$i;//exectime as seconds from midnight

			$decay_start = $this->apa->get_data('start_date', $apa_id); //start_date for fetching first day (wether we start on monday, thursday or w/e)
			//set decay_start to next exectime
			if(($decay_start%86400) > $exectime) $decay_start += 86400;
			$decay_start = $decay_start + $exectime - $decay_start%86400;
			//length of decay-period
			$date -= ($date - $decay_start)%$max_ttl;
			return $date;
		}

		public function get_next_run($apa_id) {
			$max_ttl = $this->apa->get_data('decay_time', $apa_id)*86400; //decay time in days
			$currentLastRun = $this->get_last_run($this->time->time, $apa_id);

			return $currentLastRun+$max_ttl;
		}

		public function get_value($apa_id, $last_run, $module, $dkp_id, $data, $refdate) {
			// load decay parameters, set decay_start to its proper timestamp (from somewhere at that day to exectime)
			$decay_start = $this->apa->get_data('start_date', $apa_id);
			if ($decay_start > $this->time->time) {
				return array($data['current'], false, 0);
			}
			list($h,$i) = explode(':', date('H:i', $decay_start));
			$exectime = 3600*$h + 60*$i;//exectime as seconds from midnight

			if(($decay_start%86400) > $exectime) $decay_start += 86400;
			$decay_start = $decay_start + $exectime - $decay_start%86400;
			$decay_time = $this->apa->get_data('decay_time', $apa_id)*86400;

			$blnNeedsRecalc = false;
			$blnFromCache = false;
			$intCacheTime = 0;

			// check if it's the first calculation
			if($last_run == $decay_start) {
				$value = $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], 0, $decay_start, $data['event_id'], $data['itempool_id'], $data['with_twink']));
			// check if it's in the zero-time
			} elseif(($refdate - $this->apa->get_data('zero_time', $apa_id)*2592000) > $last_run) {
				$value = 0;
			// normal calculation, get points from previous decay and add currently earned points
			} else {
				//get from member_cache
				$arrMemberCache = $this->pdh->get('member', 'apa_points', array($data['member_id'], $apa_id, $data['dkp_id'], $data['with_twink']));
				if($arrMemberCache && $arrMemberCache['time'] == $last_run){
					$value = $arrMemberCache['val'];
					$intCacheTime = $arrMemberCache['time'];
					$blnFromCache = true;
				} else {
					//Recalculate Value until the Cache Date
					$previous_calc = $last_run-$decay_time;
					$value = $this->apa->get_value($module, $dkp_id, $previous_calc, $data, true);
					$value += $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], $previous_calc, $last_run, $data['event_id'], $data['itempool_id'], $data['with_twink']));
					$blnNeedsRecalc = true;
					$intCacheTime = ($arrMemberCache && isset($arrMemberCache['time'])) ? $arrMemberCache['time'] : 0;
				}
			}

			// got points up until now (now = cache date), decay them
			$ref_value = 0; // probably unnecessary
			if(!$blnFromCache){
				$decayed_val = $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($value, $last_run, $this->time->time, $ref_value));
				$decay_adj = $value - $decayed_val;
			} else {
				$decayed_val = $value;
				$decay_adj = 0;
			}

			$decayed_val = runden($decayed_val);

			// write to cache if the entry is new
			if($blnNeedsRecalc && ($last_run > $intCacheTime)){
				$this->pdh->put('member', 'apa_points', array($data['member_id'], $apa_id, $data['dkp_id'], $data['with_twink'], array('time' => $last_run, 'val' => $decayed_val)));
			}

			// if this is the most recent decay, add current points from from last cache date to now

			if(($last_run + $decay_time) > $refdate && ($refdate != $last_run)) {
				$decayed_val += $this->pdh->get('points', 'current_history', array($data['member_id'], $data['dkp_id'], $last_run, $this->time->time+1, $data['event_id'], $data['itempool_id'], $data['with_twink']));
			}

			return array($decayed_val, $blnNeedsRecalc, $decay_adj);
		}

		public function recalculate($apa_id){
			$this->pdh->put('member', 'reset_all_apa_points', array($apa_id));
			$this->pdh->process_hook_queue();
			return true;
		}

		public function add_layout_changes($apa_id) {
			$this->pdh->put('member', 'reset_all_apa_points', array($apa_id));
			$this->pdh->process_hook_queue();
			return true;
		}

		public function update_layout_changes($apa_id) {
			$this->pdh->put('member', 'reset_all_apa_points', array($apa_id));
			$this->pdh->process_hook_queue();
			return true;
		}

		public function delete_layout_changes($apa_id) {
			$this->pdh->put('member', 'reset_all_apa_points', array($apa_id));
			$this->pdh->process_hook_queue();
			return true;
		}

		public function reset_cache($apa_id, $module, $id){
			if($module == 'current'){
				list($mdkpid, $memberId, $twinks) = explode("_", $id);
				$this->pdh->put('member', 'reset_apa_points', array($memberId, $apa_id));
				$this->pdh->process_hook_queue();
			}
		}


	}//end class
}//end if
