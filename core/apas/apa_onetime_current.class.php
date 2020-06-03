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

if ( !class_exists( "apa_onetime_current" ) ) {

	class apa_onetime_current extends apa_type_generic {
		public static $shortcuts = array('apa'=>'auto_point_adjustments');

		protected $ext_options = array(
			'event'	=> array(
				'type'		=> 'dropdown',
				'options'	=> array(),
			),
			'twinks'	=> array(
				'type'		=> 'radio',
				'default' 	=> 0,
			),
			'calc_func' => array(
					'type'		=> 'dropdown',
					'options'	=> array(),
					'required'	=> true,
			),
		);

		protected $required = array('name', 'pools');

		protected $multiple = true;

		private $modules_affected = array();

		private $cached_data = array();

		private $last_run = 0;

		public function __construct() {
			$events = $this->pdh->aget('event', 'name', 0, array($this->pdh->get('event', 'id_list')));
			if(!empty($events)) {
				foreach($events as $id => $name) {
					$this->ext_options['event']['options'][$id] = $name;
				}
			}
			$funcs = $this->apa->get_calc_function();
			foreach($funcs as $func) {
				$this->ext_options['calc_func']['options'][$func] = $func;
			}
			$arrPresets = $this->pdh->get_preset_list('%dkp_id%', array('%dkp_id%', '%member_id%', '%with_twink%'));
			$arrPresetDropdown = [];
			foreach($arrPresets as $key => $arrPreset){
				if($arrPreset[2][0] != '%member_id%' || $arrPreset[2][1] != '%dkp_id%') continue;
				$arrPresetDropdown[$key] = $this->pdh->get_preset_description($key, false);
			}

			$this->ext_options['preset'] = array(
				'type' => 'dropdown',
				'options' => $arrPresetDropdown,
				'default' => 'current',
			);
			

			$this->options = array_merge($this->options, $this->ext_options);
		}

		public function pre_save_func($apa_id, $options) {
			$this->update_current($apa_id, $options);

			return $options;
		}

		public function update_current($apa_id, $options) {
			$this->pdh->process_hook_queue();
			$char_ids = $this->pdh->get('member', 'id_list', array(true, false, true, !(int)$options['twinks']));

			$pools = $options['pools'];
			$blnHaveAdjustmentMade = false;

			foreach($pools as $pool) {

				//Check if Event is in Same MDKP Pool
				$eventID = $options['event'];
				$arrEventPools = $this->pdh->get('event', 'multidkppools', array($eventID));
				if(!$eventID || !in_array($pool, $arrEventPools)) continue;
				//echo "Pool ".$pool."<br/>";
				//With Decay value
				
				$strPreset = $options['preset'];
				if(!strlen($strPreset)) $strPreset = 'points';
				
				$arrPreset = $this->pdh->pre_process_preset($strPreset, array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''));
				$points = $this->pdh->aget($arrPreset[0][0], $arrPreset[0][1], 0, array($char_ids, $pool, 0, 0, !(int)$options['twinks'], true));
				
				foreach($char_ids as $char_id) {
					$currentValue = $points[$char_id];
					$newValue = $this->apa->run_calc_func($options['calc_func'], array($points[$char_id], $this->time->time-10, $this->time->time, $points[$char_id]));

					$adjValue = $newValue - $currentValue;

					if($adjValue == 0) continue;

					$this->pdh->put('adjustment', 'add_adjustment', array($adjValue, $options['name'], $char_id, $options['event']));
					$blnHaveAdjustmentMade = true;
					//echo "insert adjustment";
				}

				if($blnHaveAdjustmentMade) {
					//echo "There were adjustments made";
					$this->pdh->process_hook_queue();
				}
			}

			// calculate next check date
			$this->apa->reset_local_cache();
		}

		public function modules_affected($apa_id) { return array(); }
		public function get_last_run($date, $apa_id) {
			return $this->last_run;
		}
		public function get_next_run($apa_id) { return 0; }
		public function get_value($apa_id, $cache_date, $module, $dkp_id, $data, $refdate, $debug=false) { return; }

		public function recalculate($apa_id){
			// check for points over cap for each character
			$this->pdh->process_hook_queue();
			$char_ids = $this->pdh->get('member', 'id_list', array(true, false, true, !(int)$this->apa->get_data('twinks', $apa_id)));

			$pools = $this->apa->get_data('pools', $apa_id);
			$blnHaveAdjustmentMade = false;

			$strPreset = $this->apa->get_data('preset', $apa_id);
			if(!strlen($strPreset)) $strPreset = 'points';
			
			$arrPreset = $this->pdh->pre_process_preset($strPreset, array('name' => 'earned', 'sort' => true, 'th_add' => '', 'td_add' => ''));

			foreach($pools as $pool) {
				//Check if Event is in Same MDKP Pool
				$eventID = $this->apa->get_data('event', $apa_id);
				$arrEventPools = $this->pdh->get('event', 'multidkppools', array($eventID));
				#if(!$eventID || !in_array($pool, $arrEventPools)) continue;
				//echo "Pool ".$pool."<br/>";
				//With Decay value
				$points = $this->pdh->aget($arrPreset[0][0], $arrPreset[0][1], 0, array($char_ids, $pool, 0, 0, !(int)$this->apa->get_data('twinks', $apa_id), true));
				
				foreach($char_ids as $char_id) {
					$currentValue = $points[$char_id];
					$newValue = $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($points[$char_id], $this->time->time, $this->time->time, $points[$char_id]));

					$adjValue = $newValue - $currentValue;

					if($adjValue == 0) continue;

					$this->pdh->put('adjustment', 'add_adjustment', array($adjValue, $this->apa->get_data('name', $apa_id), $char_id, $this->apa->get_data('event', $apa_id)));
					$blnHaveAdjustmentMade = true;
					//echo "insert adjustment";
				}

				if($blnHaveAdjustmentMade) {
					//echo "There were adjustments made";
					$this->pdh->process_hook_queue();
				}
			}

			$this->apa->reset_local_cache();

			return;
		}
	}//end class
}//end if
