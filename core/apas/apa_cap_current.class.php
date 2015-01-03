<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

if ( !class_exists( "apa_cap_current" ) ) {
	class apa_cap_current extends apa_type_generic {
		public static $shortcuts = array('apa'=>'auto_point_adjustments');

		protected $ext_options = array(
			'upper_cap'	=> array(
				'type'		=> 'spinner',
				'step'		=> 10,
				'size'		=> 5,
				'default'	=> 100
			),
			'lower_cap' => array(
				'type'		=> 'spinner',
				'step'		=> 10,
				'size'		=> 5,
				'default'	=> 0
			),
			'interval' => array(
				'type'		=> 'spinner',
				'step'		=> 1,
				'size'		=> 2,
				'default'	=> 1
			),
			'event'	=> array(
				'type'		=> 'dropdown',
				'options'	=> array(),
			),
			'start_date' => array(
				'type'		=> 'datepicker',
				'default'	=> 'now',
			),
			'twinks'	=> array(
				'type'		=> 'radio',
				'default' 	=> 0,
			),
		);
		
		private $modules_affected = array();
		
		private $cached_data = array();

		public function __construct() {
			$this->ext_options['start_date']['value'] = $this->time->time;
			$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
			if(!empty($events)) {
				foreach($events as $id => $name) {
					$this->ext_options['event']['options'][$id] = $name;
				}
			}
			$this->options = array_merge($this->options, $this->ext_options);
		}
		
		public function pre_save_func($apa_id, $options) {
			// strip time off of start-date
			$options['start_date'] -= ($this->time->date('H', $options['start_date'])*3600 + $this->time->date('i', $options['start_date'])*60);
			list($h,$i) = explode(':',$options['exectime']);
			$this->timekeeper->add_cron('pointcap', array('active' => true, 'start_time' => $options['start_date'] + $h*3600 + $i*60), true);
			$this->timekeeper->run_cron('pointcap', true);
			$cron = $this->timekeeper->list_crons('pointcap');
			return $options;
		}
		
		public function update_point_cap($apa_id) {
			$next_run = $this->config->get('apa_cap_next_run');
			if(!$next_run) {
				list($h,$i) = explode(':',$this->apa->get_data('exectime', $apa_id));
				$next_run = $this->apa->get_data('start_date', $apa_id) + $h*3600 + $i*60;
			}
			if($next_run > $this->time->time) return;
			
			// check for points over cap for each character
			$this->pdh->process_hook_queue();
			$char_ids = $this->pdh->get('member', 'id_list', array(true, false, true, $this->apa->get_data('twinks', $apa_id)));
			$pools = $this->apa->get_data('pools', $apa_id);
			foreach($pools as $pool) {
				$points = $this->pdh->aget('points', 'current_history', 0, array($char_ids, $pool, 0, $next_run-1, 0, 0, !$this->apa->get_data('twinks', $apa_id)));
				foreach($char_ids as $char_id) {
					if($points[$char_id] > $this->apa->get_data('upper_cap', $apa_id)) {
						$value = $this->apa->get_data('upper_cap', $apa_id) - $points[$char_id];
						$this->pdh->put('adjustment', 'add_adjustment', array($value, $this->apa->get_data('name', $apa_id), $char_id, $this->apa->get_data('event', $apa_id), NULL, $next_run+1));
					} elseif($points[$char_id] < $this->apa->get_data('lower_cap', $apa_id)) {
						$value = $this->apa->get_data('lower_cap', $apa_id) - $points[$char_id];
						$this->pdh->put('adjustment', 'add_adjustment', array($value, $this->apa->get_data('name', $apa_id), $char_id, $this->apa->get_data('event', $apa_id), NULL, $next_run+1));
					}
				}
			}
			
			// calculate next check date
			$next_run = $next_run + $this->apa->get_data('interval', $apa_id)*86400;
			$this->config->set('apa_cap_next_run', $next_run);
			// run again if we have a backlog
			if($next_run < $this->time->time) $this->update_point_cap($apa_id);
		}
		
		public function modules_affected($apa_id) { return array(); }
		public function get_cache_date($date, $apa_id) { return; }
		public function get_decay_val($apa_id, $cache_date, $module, $dkp_id, $data) { return; }
		
		public function recalculate($apa_id){
			$this->db->prepare("DELETE FROM __adjustments WHERE adjustment_reason=? AND event_id=? ")->execute($this->apa->get_data('name', $apa_id), intval($this->apa->get_data('event', $apa_id)));
			$this->config->del('apa_cap_next_run');
			$this->pdh->enqueue_hook('adjustment_update');
			$this->timekeeper->run_cron('pointcap', true);
		}
	}//end class
}//end if
?>