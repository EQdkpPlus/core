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

if ( !class_exists( "apa_decay_ria" ) ) {
	class apa_decay_ria extends apa_type_generic {
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
				'class'		=> 'input'
			),
			'modules' => array(
				'type' => 'multiselect',
				'options' => array('item' => 'Items', 'raid' => 'Raids', 'adjustment' => 'Adjustments'),
			),
			'calc_func' => array(
				'type'		=> 'dropdown',
				'options'	=> array(),
			)
		);

		private $modules_affected = array();

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

		public function add_layout_changes($apa_id) {
			$layout = $this->pdh->make_editable($this->config->get('eqdkp_layout'));
			if(!$layout) return false;
			//generate presets
			$this->modules_affected = $this->apa->get_data('modules', $apa_id);
			if(count($this->modules_affected) == 0) return true;


			foreach($this->modules_affected as $module) {
				$preset_name = $module.'_decay_'.$apa_id;
				$pools = $this->apa->get_data('pools', $apa_id);
				$preset = array($module, 'value', array('%'.$module.'_id%', $pools[0]), array($pools[0]));
				$this->pdh->update_user_preset($preset_name, $preset, $this->apa->get_data('name', $apa_id));
			}
			$layout_def = $this->pdh->get_eqdkp_layout($layout);
			//add new presets after original presets for raid/item/adj val
			foreach($layout_def['pages'] as $page_name => $page) {
				if(strpos($page_name, 'admin') !== false) continue;
				foreach($page as $single_page_name => $single_page) {
					$new_presets = array();
					$key_conv = array();
					$added = 0;
					$i = 0;
					foreach($single_page['table_presets'] as $preset) {
						$key_conv[$i] = $i+$added;
						if($preset['name'] == 'rvalue') {
							if(in_array('raid', $this->modules_affected)){
								$new_presets[$i+1] = $preset;
								$new_presets[$i+1]['name'] = 'raid_decay_'.$apa_id;
								$added++;
							}
						} elseif($preset['name'] == 'ivalue') {
							if(in_array('item', $this->modules_affected)){
								$new_presets[$i+1] = $preset;
								$new_presets[$i+1]['name'] = 'item_decay_'.$apa_id;
								$added++;
							}
						} elseif($preset['name'] == 'adj_value') {
							if(in_array('adjustment', $this->modules_affected)){
								$new_presets[$i+1] = $preset;
								$new_presets[$i+1]['name'] = 'adjustment_decay_'.$apa_id;
								$added++;
							}
						}
						$i++;
					}
					foreach($key_conv as $key => $nkey) {
						$new_presets[$nkey] = $single_page['table_presets'][$key];
					}
					ksort($new_presets);
					$layout_def['pages'][$page_name][$single_page_name]['table_presets'] = $new_presets;
				}
			}
			$this->pdl->debug($layout);
			$this->pdl->debug($layout_def);
			$this->pdh->save_layout($layout, $layout_def);
			$this->pdh->put('member', 'reset_points');
			return true;
		}

		public function update_layout_changes($apa_id) {
			$this->modules_affected = $this->apa->get_data('modules', $apa_id);
			foreach($this->modules_affected as $module) {
				$preset_name = $module.'_decay_'.$apa_id;
				$this->pdh->update_user_preset($preset_name, false, $this->apa->get_data('name', $apa_id));
			}
			$this->pdh->put('member', 'reset_points');
			return true;
		}

		public function delete_layout_changes($apa_id) {
			//remove presets from layout
			$layout_def = $this->pdh->get_eqdkp_layout($this->config->get('eqdkp_layout'));
			foreach($layout_def['pages'] as $page_name => $page) {
				if(strpos($page_name, 'admin') !== false) continue;
				foreach($page as $single_page_name => $single_page) {
					foreach($single_page['table_presets'] as $key => $preset) {
						if(in_array($preset['name'], array('raid_decay_'.$apa_id, 'item_decay_'.$apa_id, 'adjustment_decay_'.$apa_id))) unset($layout_def['pages'][$page_name][$single_page_name]['table_presets'][$key]);
					}
				}
			}
			$this->pdh->save_layout($this->config->get('eqdkp_layout'), $layout_def);
			$this->modules_affected = $this->apa->get_data('modules', $apa_id);
			foreach($this->modules_affected as $module) {
				$preset_name = $module.'_decay_'.$apa_id;
				$this->pdh->delete_user_preset($preset_name);
			}
			$this->pdh->put('member', 'reset_points');
			return true;
		}

		public function modules_affected($apa_id) {
			return $this->apa->get_data('modules', $apa_id);
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

		public function get_value($apa_id, $last_run, $module, $dkp_id, $data, $refdate, $debug=false) {
			// load decay parameters, set decay_start to its proper timestamp (from somewhere at that day to exectime)
			$decay_start = $this->apa->get_data('start_date', $apa_id);
			if ($decay_start > $this->time->time) {
				return array($data['value'], false, 0);
			}
			list($h,$i) = explode(':', date('H:i', $decay_start));
			$exectime = 3600*$h + 60*$i;//exectime as seconds from midnight
			if(($decay_start%86400) > $exectime) $decay_start += 86400;
			$decay_start = $decay_start + $exectime - $decay_start%86400;
			$decay_time = $this->apa->get_data('decay_time', $apa_id)*86400;

			//relevant item/raid/adj ?
			if($decay_start > $data['date']) return NULL;

			$blnNeedsRecalc = false;
			$blnFromCache = false;

			//Check Zero Time
			if(($last_run - $this->apa->get_data('zero_time', $apa_id)*2592000) > $data['date']) {
				$decayed_val = 0;
				$decay_adj = 0;

				switch($module){
					case 'item': $arrCache = $this->pdh->get('item', 'apa_value', array($data['id'], $apa_id));
					break;
					case 'raid': $arrCache = $this->pdh->get('raid', 'apa_value', array($data['id'], $apa_id));
					break;
					case 'adjustment': $arrCache = $this->pdh->get('adjustment', 'apa_value', array($data['id'], $apa_id));
					break;
				}

				if($arrCache == ""){
					$arrToSave = array('time' => $last_run, 'val' => $decayed_val);

					switch($module){
						case 'item': $this->pdh->put('item', 'update_apa_value', array($data['id'], $apa_id, $arrToSave));
						break;
						case 'raid': $this->pdh->put('raid', 'update_apa_value', array($data['id'], $apa_id, $arrToSave));
						break;
						case 'adjustment': $this->pdh->put('adjustment', 'update_apa_value', array($data['id'], $apa_id, $arrToSave));
						break;
					}
				}
			} else {

				//Get Cache Entry
				switch($module){
					case 'item': $arrCache = $this->pdh->get('item', 'apa_value', array($data['id'], $apa_id));
						break;
					case 'raid': $arrCache = $this->pdh->get('raid', 'apa_value', array($data['id'], $apa_id));
						break;
					case 'adjustment': $arrCache = $this->pdh->get('adjustment', 'apa_value', array($data['id'], $apa_id));
						break;
				}

				if($arrCache != "" && is_array($arrCache) && ($arrCache['time'] == $last_run)){
					$decayed_val = $arrCache['val'];
					$blnFromCache = true;
				} else {
					$previous_calc = $last_run-$decay_time;
					$value = ($previous_calc < $data['date']) ? $data['value'] : $this->apa->get_value($module, $dkp_id, $previous_calc, $data);
					$decayed_val = ($last_run < $data['date']) ? $data['value'] : $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($value, $last_run, $data['date'], $data['value']));
					$decay_adj = $value - $decayed_val;

					$blnNeedsRecalc = true;
				}
			}

			if($blnNeedsRecalc){
				$arrToSave = array('time' => $last_run, 'val' => $decayed_val);

				switch($module){
					case 'item': $this->pdh->put('item', 'update_apa_value', array($data['id'], $apa_id, $arrToSave));
					break;
					case 'raid': $this->pdh->put('raid', 'update_apa_value', array($data['id'], $apa_id, $arrToSave));
					break;
					case 'adjustment': $this->pdh->put('adjustment', 'update_apa_value', array($data['id'], $apa_id, $arrToSave));
					break;
				}
			}

			return array($decayed_val, $blnNeedsRecalc, $decay_adj);
		}

		public function recalculate($apa_id){
			$this->db->query("UPDATE __adjustments SET adjustment_apa_value='';");
			$this->db->query("UPDATE __items SET item_apa_value='';");
			$this->db->query("UPDATE __raids SET raid_apa_value='';");

			$this->pdh->enqueue_hook('adjustment_update');
			$this->pdh->enqueue_hook('item_update');
			$this->pdh->enqueue_hook('raid_update');
			$this->pdh->put('member', 'reset_points');

			$this->pdh->process_hook_queue();
		}

		public function reset_cache($apa_id, $module, $id){
			if($module == 'item'){
				$this->pdh->put('item', 'update_apa_value', array($id, $apa_id, ''));
			} elseif($module == 'adjustment'){
				$this->pdh->put('adjustment', 'update_apa_value', array($id, $apa_id, ''));
			} elseif($module == 'raid'){
				$this->pdh->put('raid', 'update_apa_value', array($id, $apa_id, ''));
			}
			$this->pdh->process_hook_queue();
		}
	}//end class
}//end if
