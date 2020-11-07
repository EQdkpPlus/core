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

if ( !class_exists( "apa_timedecay_current" ) ) {
	class apa_timedecay_current extends apa_type_generic {
		public static $shortcuts = array('apa' => 'auto_point_adjustments');

		protected $ext_options = array(
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
			'event'	=> array(
				'type'		=> 'dropdown',
				'options'	=> array(),
			),
		);

		private $modules_affected = array('current_timedecay');

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

		public function update_point_cap($apa_id) {

		}

		public function modules_affected($apa_id) {
			return $this->modules_affected;
		}

		public function get_last_run($date, $apa_id) { return; }
		public function get_next_run($apa_id) { return 0; }

		public function get_value($apa_id, $cache_date, $module, $dkp_id, $data, $refdate, $debug=false) {
			$adjustment_event_id = $this->apa->get_data('event', $apa_id);
			// Fetch decay time (in days) and convert to seconds.
			$decay_time = $this->apa->get_data('decay_time', $apa_id) * 24*60*60;

			$member_id = $data['member_id'];
			$multidkp_id = $data['multidkp_id'];
			$event_id = $data['event_id'];
			$itempool_id = $data['itempool_id'];
			$with_twink = $data['with_twink'];

			// The time decay works as follows:
			// We decay any positive earnings after a decay period if these earnings have not been spent.
			// The spending of points works in a first in, first out basis, i.e., the earliest earnings are spent first.
			// The main assumption is that the given event ID is *only* used for these automatic adjustments.
			// We define "earnings" as the points earned in raids *and* positive adjustments.
			// We need to decay all such "earnings" after the `decay_time` and can check what the last decayed
			// earning was by looking at the most recent adjustment for our event ID.
			// The algorithm to calculate what amount to decay is fairly simple. Note, however, that it needs
			// to group all "earnings" that happened at the same time together.
			// Let's say we have a set of "earnings" at time t1, which will decay at time t2.
			// We can calculate whether all of these earnings have been spent at time t2 by getting the point balance
			// at time t2 and checking whether it is larger than all of the earnings from t1+1 to t2.
			// If it is larger, we need to adjust the difference as these points decayed.

			// Prevent the case, that main+twinks(=> with_twink=true) is adjusted,
			// if twinks are shown (so main and twink get own points).
			if(!($with_twink != $this->config->get('show_twinks'))){
				return array($data['val'], false, 0);
			}

			// Get the most recent decay adjustment made.
			$last_adjustment_id = $this->pdh->get('adjustment', 'most_recent_adj_of_event_member', array($adjustment_event_id, $member_id, $with_twink));
			if($last_adjustment_id !== false) {
				// Fetch last adjustment date if there is any.
				$last_adjustment_date = $this->pdh->get('adjustment', 'date', array($last_adjustment_id));
				// From here, calculate the last adjusted earnings date (adjustment date - decay time).
				$last_decayed_earning_date = $last_adjustment_date - $decay_time;
			} else {
				// Else, we don't have any adjustments yet and want to start with the start date set.
				$last_decayed_earning_date = $this->apa->get_data('start_date', $apa_id);
			}

			// Retrieve all decayed earnings to be processed (we only need dates):
			// $last_decayed_earning_date < earning date <= current time - $decay_time
			$earning_dates = array();
			// Add all raids.
			$raids = $this->pdh->get('raid', 'raids_of_member_in_interval', array($member_id, $last_decayed_earning_date + 1, $this->time->time - $decay_time, $with_twink));
			foreach($raids as $raid_id) {
				$earning_date = $this->pdh->get('raid', 'date', array($raid_id));
				$earning_value = $this->pdh->get('raid', 'value', array($raid_id));
				if(!array_key_exists($earning_date, $earning_dates)) {
					$earning_dates[$earning_date] = 0;
				}
				$earning_dates[$earning_date] += $earning_value;
			}

			// We need to add positive adjustments in that period.
			$adjustments = $this->pdh->get('adjustment', 'adj_of_member_in_interval', array($member_id, $last_decayed_earning_date + 1, $this->time->time - $decay_time, $with_twink));
			foreach($adjustments as $adj_id) {
				$adj_value = $this->pdh->get('adjustment', 'value', array($adj_id));
				if($adj_value > 0) {
					$earning_date = $this->pdh->get('adjustment', 'date', array($adj_id));
					$earning_value = $this->pdh->get('adjustment', 'value', array($adj_id));
					if(!array_key_exists($earning_date, $earning_dates)) {
						$earning_dates[$earning_date] = 0;
					}
					$earning_dates[$earning_date] += $earning_value;
				}
			}

			// Get unique, sorted dates of earnings to decay.
			ksort($earning_dates);

			// Process them in order.
			$adjustments_sum = 0;
			foreach($earning_dates as $earning_date => $earning_value) {
				// Calculate balance at the end of the decay.
				$decay_date = $earning_date + $decay_time;
				$end_balance = $this->pdh->get('points', 'current_history', array($member_id, $multidkp_id, 0, $decay_date, $event_id, $itempool_id, $with_twink, false));

				// Calculate non-decayed earnings during the time period $earning_date+1 to $decay_date.
				$non_decayed_earnings = 0;
				$raids = $this->pdh->get('raid', 'raids_of_member_in_interval', array($member_id, $earning_date + 1, $decay_date, $with_twink));
				foreach($raids as $raid_id) {
					$raid_value = $this->pdh->get('raid', 'value', array($raid_id));
					$non_decayed_earnings += $raid_value;
				}
				// Add only positive adjustments here.
				$adjustments = $this->pdh->get('adjustment', 'adj_of_member_in_interval', array($member_id, $earning_date + 1, $decay_date, $with_twink));
				foreach($adjustments as $adj_id) {
					$adj_value = $this->pdh->get('adjustment', 'value', array($adj_id));
					if($adj_value > 0) {
						$non_decayed_earnings += $adj_value;
					}
				}

				// If the balance is greater than the non-decayed earnings, we need to adjust the points.
				if($end_balance > $non_decayed_earnings) {
					$adjustment_value = -min($end_balance - $non_decayed_earnings, $earning_value);
					$adjustments_sum += $adjustment_value;
					$this->pdh->put('adjustment', 'add_adjustment', array($adjustment_value, $this->apa->get_data('name', $apa_id) . ' for ' . $this->time->user_date($earning_date), $member_id, $adjustment_event_id, NULL, $decay_date));
					$this->pdh->process_hook_queue();
				}
			}

			// Return updated value.
			return array($data['val'] + $adjustments_sum, false, 0);
		}

		public function recalculate($apa_id){
			return true;
		}
	}//end class
}//end if
