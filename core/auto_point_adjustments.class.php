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
	class auto_point_adjustments extends gen_class {
		public static $shortcuts = array('pfh', 'pdc', 'time', 'user');
		public static $dependencies = array('pdc');

		private $apa_tab			= array();
		private $apa_tab_file		= 'apatab.php';
		
		private $apa_func_file		= 'apafunc.php';
		private $calc_func			= array();
		private $func_args			= array('value', 'date', 'ref_date');
		private $valid_symbols		= array('+', '-', '*', '/', ',', '(', ')', '?', ':', '==', '<=', '>=', '>', '<', '!=', '!', '&&', '||', '%'); //symbols left of a symbol should not occur in symbols to the right of them
		
		private $decayed_pools		= array();
		
		private $apa_types_inst		= array();
		
		private $cached_data		= array();
		private $ttls				= array();
		private $needs_update		= array();
		
		public $available_types		= array();

		public function __construct(){
			$this->apa_tab_file  = $this->pfh->FilePath('apa/apatab.php', 'eqdkp', 'relative');
			$this->apa_func_file = $this->pfh->FilePath('apa/apafunc.php', 'eqdkp', 'relative');
			$this->load_apa_tab();
			$this->load_calc_func();
			$this->needs_update = $this->pdc->get('apa_update_table');
			if(empty($this->needs_update)) $this->needs_update = array();
		}

		private function load_apa_tab(){
			$result = false;
			if(is_file($this->apa_tab_file)){
				$result = @file_get_contents($this->apa_tab_file);
			}
			if($result !== false) $this->apa_tab = unserialize($result);
		}

		private function save_apa_tab($apa_tab){
			$this->pfh->putContent($this->apa_tab_file, serialize($apa_tab));
		}
		
		public function get_data($type, $id) {
			if(isset($this->apa_tab[$id][$type])) return $this->apa_tab[$id][$type];
			return false;
		}
		
		public function type_exists($type) {
			$this->scan_types();
			if(in_array($type, $this->available_types)) return true;
			return false;
		}

		public function add_apa($type, $options=array()) {
			//generate unique id
			$unique_id = uniqid();
			//check if all necessary information are given
			if(!$this->type_exists($type)) return false;
			$required = $this->get_apa_type($type)->required();
			foreach($required as $field) {
				if(!isset($options[$field])) return false;
			}
			$options['type'] = $type;
			//run pre-save-hook
			$options = $this->get_apa_type($type)->pre_save_func($unique_id, $options);
			//add apa to apatab
			$this->apa_tab[$unique_id] = $options;
			//save apatab
			$this->save_apa_tab($this->apa_tab);
			//maybe layout change necessary
			$this->get_apa_type($type)->add_layout_changes($unique_id);
			//run apa?
			return true;
		}

		public function update_apa($apa_id, $options=false){
			if(!isset($this->apa_tab[$apa_id])) return false;
			if(is_array($options)) {
				foreach($options as $key => $val) {
					$this->apa_tab[$apa_id][$key] = $val;
				}
			}
			//save apatab
			$this->save_apa_tab($this->apa_tab);
			//run apa?
			$this->get_apa_type($this->apa_tab[$apa_id]['type'])->update_layout_changes($apa_id);
			return true;
		}

		public function del_apa($apa_id){
			if(!isset($this->apa_tab[$apa_id])) return true;
			//delete layout changes
			$this->get_apa_type($this->apa_tab[$apa_id]['type'])->delete_layout_changes($apa_id);
			
			//remove cached entries
			$this->pdc->del_prefix('apa_'.$apa_id);
			
			//remove apa from apatab
			unset($this->apa_tab[$apa_id]);
			//save apatab
			$this->save_apa_tab($this->apa_tab);
			return true;
		}

		public function list_apas(){
			return $this->apa_tab;
		}
		
		public function get_pools_used($type) {
			if(empty($this->apa_tab)) return array();
			$pools = array();
			foreach($this->apa_tab as $options) {
				if(!isset($options['pools']) || !is_array($options['pools']) || $type != $options['type']) continue;
				foreach($options['pools'] as $pool) {
					$pools[] = $pool;
				}
			}
			return $pools;
		}
		
		public function get_apa_id($dkp_id, $module) {
			foreach($this->apa_tab as $apa_id => $options) {
				if(!isset($apa_modules[$options['type']])) $apa_modules[$options['type']] = $this->get_apa_type($options['type'])->modules_affected();
				if(in_array($dkp_id, $options['pools']) && in_array($module, $apa_modules[$options['type']])) return $apa_id;
			}
			return false;
		}
		
		public function get_apa_idsbytype($type) {
			$ids = array();
			foreach($this->apa_tab as $apa_id => $options) {
				if($type == $options['type']) $ids[] = $apa_id;
			}
			return $ids;
		}
		
		public function scan_types($force_update=false) {
			if(count($this->available_types) > 0 && !$force_update) return $this->available_types;
			$files = scandir($this->root_path.'core/apas');
			$ignore = array('.', '..', '.svn', 'apa_type_generic.class.php');
			foreach($files as $file) {
				if(in_array($file, $ignore)) continue;
				$this->available_types[] = substr($file, 4, -10);
			}
			return $this->available_types;
		}

		public function get_apa_type($type, $new=false) {
			if(!$this->type_exists($type)) return false;
			$type = 'apa_'.$type;
			if(!isset($this->apa_types_inst[$type])) {
				include_once($this->root_path.'core/apas/apa_type_generic.class.php');
				include_once($this->root_path.'core/apas/'.$type.'.class.php');
				$this->apa_types_inst[$type] = true;
			}
			return registry::register($type);
		}

		public function is_decay($module, $pool) {
			if(empty($this->apa_tab)) return false;
			if(empty($this->decayed_pools)) {
				foreach($this->apa_tab as $apa) {
					$modules = $this->get_apa_type($apa['type'])->modules_affected();
					foreach($apa['pools'] as $dkp_id) {
						foreach($modules as $module) {
							$this->decayed_pools[$dkp_id][] = $module;
						}
					}
				}
			}
			if(!empty($this->decayed_pools[$pool]) && in_array($module, $this->decayed_pools[$pool])) return true;
			return false;
		}
		
		public function get_caption($module, $pool) {
			foreach($this->apa_tab as $apa) {
				foreach($apa['pools'] as $dkp_id) {
					if($pool == $dkp_id) return $apa['name'];
				}
			}
			return false;
		}
		
		/*
		 * get decayed value
		 *
		 * @string		$module		Pdh-Module, calling the function
		 * @timestamp	$date		Date for when to get the value
		 * @int			$dkp_id		ID of the Multidkp-Pool
		 * @array		$data		Data needed for calculation, see lines below
		 * 	contains	'id'		ID of the Data-Set
		 *				'value'		value at the date (from below)
		 *				'date'		date at which the value applied
		 *
		 * @return 		float
		 */
		public function get_decay_val($module, $dkp_id, $date=0, $data=array()) {
			if(!$date) $date = $this->time->time;
			$apa_id = $this->get_apa_id($dkp_id, $module);
			//load cached data
			$cache_date = $this->get_apa_type($this->apa_tab[$apa_id]['type'])->get_cache_date($date, $apa_id);
			if(!isset($this->cached_data[$apa_id][$cache_date])) $this->cached_data[$apa_id][$cache_date] = $this->pdc->get('apa_'.$apa_id.'_'.$cache_date);
			//check if update is necessary
			if(!isset($this->cached_data[$apa_id][$cache_date][$module][$data['id']]) || $this->needs_update($module, $data['id'])) {
				list($val, $ttl) = $this->get_apa_type($this->apa_tab[$apa_id]['type'])->get_decay_val($apa_id, $cache_date, $module, $dkp_id, $data);
				$this->cached_data[$apa_id][$cache_date][$module][$data['id']] = $val;
				$this->ttls[$apa_id][$cache_date] = $ttl;
				$this->update_done($module, $data['id']);
			}
			return $this->cached_data[$apa_id][$cache_date][$module][$data['id']];
		}
		
		public function enqueue_update($module, $ids) {
			if(!is_array($ids)) $this->needs_update[$module][$ids] = true;
			else {
				foreach($ids as $id) {
					$this->needs_update[$module][$id] = true;
				}
			}
		}
		
		private function needs_update($module, $id) {
			if(isset($this->needs_update[$module][$id])) return true;
			return false;
		}
		
		private function update_done($module, $id) {
			if(isset($this->needs_update[$module][$id])) unset($this->needs_update[$module][$id]);
		}

		public function get_apa_edit_form($apa_id){
			if(!isset($this->apa_tab[$apa_id])) return null;
			$apa_obj = $this->get_apa_type($this->apa_tab[$apa_id]['type']);
			$apa_obj->set_values($this->apa_tab[$apa_id]);
			return $apa_obj->get_options();
		}

		public function get_apa_add_form($apa_type){
			if(!$this->type_exists($apa_type)) return false;
			return $this->get_apa_type($apa_type)->get_options();
		}
		
		public function run_calc_func($name, $params) {
			if(!isset($this->calc_func[$name])) return false;
			if(function_exists($name)) return call_user_func_array($name, $params);
			$file = $this->pfh->FilePath('apa/'.$name.'.func.php', 'eqdkp', 'relative');
			if(is_file($file)) include_once($file);
			if(function_exists($name)) return call_user_func_array($name, $params);
			$params = array();
			foreach($this->func_args as $arg) {
				$params[] = '$'.$arg;
			}
			$function = '<?php'."\n".'function '.$name.'('.implode(', ', $params).') {'."\n";
			foreach($this->calc_func[$name] as $key => $expr) {
				$function .= "\t".'$Var'.$key.' = '.$expr.';'."\n";
			}
			$function .= "\t".'return $Var'.$key.';'."\n";
			$function .= '}'."\n".'?>';
			$this->pfh->CheckCreateFile($file);
			$this->pfh->putContent($file, $function);
			include_once($file);
			return call_user_func_array($name, $params);
		}

		private function load_calc_func() {
			if(is_file($this->apa_func_file)){
				$result = file_get_contents($this->apa_func_file);
				if($result) $this->calc_func = unserialize($result);
			}
		}
		
		private function save_calc_function() {
			$this->pfh->putContent($this->apa_func_file, serialize($this->calc_func));
		}
		
		public function get_calc_function($name='') {
			if(!$name) return array_keys($this->calc_func);
			if(isset($this->calc_func[$name])) return $this->calc_func[$name];
			return false;
		}
		
		public function get_calc_args($for_html=false) {
			$args = $this->func_args;
			if($for_html) {
				$args['no_sel'] = $this->user->lang('apa_arg_choose');
				foreach($args as $key => $arg) {
					if($key === 'no_sel') continue;
					$args[$arg] = $this->user->lang('apa_arg_'.$arg).' ('.$arg.')';
					unset($args[$key]);
				}
			}
			return $args;
		}
		
		public function get_func_valid_symbols() {
			return $this->valid_symbols;
		}

		public function update_calc_function($name, $body) {
			if(!$name) return false;
			$this->calc_func[$name] = $body;
			$this->pfh->Delete($this->pfh->FilePath('apa/'.$name.'.func.php', 'eqdkp'));
			$this->save_calc_function();
			return true;
		}
		
		public function delete_calc_function($name) {
			if(isset($this->calc_func[$name])) unset($this->calc_func[$name]);
			$this->pfh->Delete($this->pfh->FilePath('apa/'.$name.'.func.php', 'eqdkp'));
			$this->save_calc_function();
			return true;
		}

		public function __destruct() {
			#foreach($this->ttls as $apa_id => $data) {
			#	foreach($data as $cache_date => $ttl) {
			#		$this->pdc->put('apa_'.$apa_id.'_'.$cache_date, $this->cached_data[$apa_id][$cache_date], $ttl);
			#	}
			#}
			#$this->pdc->put('apa_update_table', $this->needs_update, 15768000); //cache half a year
			#unset($this->cached_data);
			#unset($this->ttls);
			parent::__destruct();
		}
	}//end class
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_auto_point_adjustments', auto_point_adjustments::$shortcuts);
	registry::add_const('dep_auto_point_adjustments', auto_point_adjustments::$dependencies);
}
?>