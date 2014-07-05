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

if ( !class_exists( "apa_decay_ria" ) ) {
	class apa_decay_ria extends apa_type_generic {
		public static $shortcuts = array('user', 'time', 'pdc', 'pdh', 'config', 'pdl', 'apa'=>'auto_point_adjustments');

		protected $ext_options = array(
			'zero_time'	=> array(
				'name'		=> 'zero_time',
				'type'		=> 'dropdown',
				'options'	=> array(),
				'value'		=> 3,
			),
			'decay_time' => array(
				'name'		=> 'decay_time',
				'type'		=> 'spinner',
				'max'		=> 99,
				'min'		=> 1,
				'step'		=> 1,
				'size'		=> 2,
				'value'		=> 1
			),
			'start_date' => array(
				'name'		=> 'start_date',
				'type'		=> 'datepicker',
				'options'	=> array('timepicker' => true),
				'value'		=> 0,
				'class'		=> 'input'
			),
			'calc_func' => array(
				'name'		=> 'calc_func',
				'type'		=> 'dropdown',
				'options'	=> array(),
				'value'		=> 0,
			)
		);
		
		private $modules_affected = array('item', 'raid', 'adjustment');
		
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
			$layout = $this->pdh->make_editable($this->config->get('eqdkp_layout'));
			if(!$layout) return false;
			//generate presets
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
							$new_presets[$i+1] = $preset;
							$new_presets[$i+1]['name'] = 'raid_decay_'.$apa_id;
							$added++;
						} elseif($preset['name'] == 'ivalue') {
							$new_presets[$i+1] = $preset;
							$new_presets[$i+1]['name'] = 'item_decay_'.$apa_id;
							$added++;
						} elseif($preset['name'] == 'adj_value') {
							$new_presets[$i+1] = $preset;
							$new_presets[$i+1]['name'] = 'adjustment_decay_'.$apa_id;
							$added++;
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
			return true;
		}
		
		public function update_layout_changes($apa_id) {
			foreach($this->modules_affected as $module) {
				$preset_name = $module.'_decay_'.$apa_id;
				$this->pdh->update_user_preset($preset_name, false, $this->apa->get_data('name', $apa_id));
			}
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
			foreach($this->modules_affected as $module) {
				$preset_name = $module.'_decay_'.$apa_id;
				$this->pdh->delete_user_preset($preset_name);
			}
			return true;
		}
		
		public function modules_affected() {
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
		
		public function get_decay_val($apa_id, $cache_date, $module, $dkp_id, $data) {
			$decay_start = $this->apa->get_data('start_date', $apa_id);
			//relevant item/raid/adj ?
			if($decay_start > $data['date']) return NULL;
			if(($cache_date - $this->apa->get_data('zero_time', $apa_id)*2592000) > $data['date']) {
				$decayed_val = 0;
			} else {
				$previous_calc = $cache_date - $this->apa->get_data('decay_time', $apa_id)*86400;
				$value = ($previous_calc < $data['date']) ? $data['value'] : $this->apa->get_decay_val($module, $dkp_id, $previous_calc, $data);
				$decayed_val = ($cache_date < $data['date']) ? $data['value'] : $this->apa->run_calc_func($this->apa->get_data('calc_func', $apa_id), array($value, $cache_date, $data['date'], $data['value']));
			}
			$ttl = $this->apa->get_data('decay_time', $apa_id)*86400*3; //3 times decay_period
			return array($decayed_val, $ttl);
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_apa_decay_ria', apa_decay_ria::$shortcuts);
?>