<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

if( !defined( 'EQDKP_INC' ) ) {
	die('Do not access this file directly.');
}

if( !class_exists( "plus_datahandler")){
	class plus_datahandler extends gen_class {
		public static $shortcuts = array('pdl', 'config', 'time', 'user', 'pfh', 'core',
			'timekeeper' => 'timekeeper',
		);
		
		public static $dependencies = array('pfh');

		//paths
		private $wm_path = './includes/modules/read/';
		private $rm_path = './includes/modules/write/';

		//module lists
		private $read_modules	= array( );
		private $write_modules	= array( );

		//preset stuff
		private $preset_list				= array( );
		private $preset_lang				= array( );
		private $preset_aliases				= array( );
		private $user_presets 				= array( );
		private $user_presets_lang			= array( );
		private $presets_loaded 			= false;
		private $presets_changed 			= false;
		
		//layout stuff
		private $system_settings			= array( );
		
		//page_objects stuff
		private $page_objects = array();
		private $page_objects_loaded = false;
		private $page_objects_changed = false;

		//hooks
		private $registered_hooks			= array( );
		private $undone_hooks				= array( );
		private $session_hooks				= array( );
		private $hook_callbacks				= array( );

		private $sort_cache = array(0 => 1); //important: sortcache index

		//Constructor
		public function __construct(){
			if(!$this->pdl->type_known('pdh_error')) $this->pdl->register_type('pdh_error', null, array($this, 'html_format_errors'), array(3, 4));

			$this->init_module_path();

			require_once( $this->rm_path.'pdh_r_generic.class.php' );
			require_once( $this->wm_path.'pdh_w_generic.class.php' );
			$this->scan_read_modules( );
		}
		
		//Destructor
		public function __destruct() {
			$this->save_page_objects();
			$this->save_user_presets();
			parent::__destruct();
		}

		//pdl format function (has to be public!)
		public function html_format_errors( $log_entry ) {
			$text = '<span class="negative">'.$log_entry['args'][2]."</span>";
			if(!empty($log_entry['args'][3])) {
				if(is_array($log_entry['args'][3])) {
					foreach($log_entry['args'][3] as $line) {
						$text .= '<br />'.$line;
					}
				} else {
					$text .= '<br />'.$log_entry['args'][3];
				}
			}
			$text .= "<br />File: ".$log_entry['args'][0];
			$text .= "<br />Line: ".$log_entry['args'][1];

			return $text;
		}

		/*
		* Module update methods
		*/
		private function set_module_update_time( $module_name, $time ) {
			$this->timekeeper->put( 'pdh_mu_times', $module_name, $time, true );
		}

		public function get_module_update_time( $module_name ) {
			return $this->timekeeper->get( 'pdh_mu_times', $module_name );
		}

		private function set_hook_call_time( $hook ) {
		}

		public function get_hook_call_time( $hook ) {
			return $this->timekeeper->get( 'pdh_hk_times', $hook );
		}

		public function get_hooks_for_read_module( $module ) {
			if(isset($this->read_modules[$module])) return $this->rm($module)->get_hooks( );
		}

		public function module_needs_update($module_name){
			$needs_update = false;
			foreach( $this->registered_hooks as $hook => $modules ) {
				if( in_array( $module_name, $modules ) ) {
					if( $this->timekeeper->get( 'pdh_hk_times', $hook ) >= $this->timekeeper->get( 'pdh_mu_times', $module_name ) ) {
						$needs_update = true;
						break;
					}
				}
			}
			return $needs_update;
		}

		public function process_hook_queue(){
			foreach( $this->undone_hooks as $hook => $ids ) {
				$this->timekeeper->put( 'pdh_hk_times', $hook, $this->time->time );
			}
			$this->timekeeper->saveToFile( );
			foreach( $this->undone_hooks as $hook => $ids ) {
				if( is_array( $this->registered_hooks[$hook] ) ) {
					foreach( $this->registered_hooks[$hook] as $module ) {
						$this->rm($module)->reset($ids);
						$this->read_modules[$module] = false;
					}
				}
				if( isset( $this->hook_callbacks[$hook] ) && is_array( $this->hook_callbacks[$hook] ) ) {
					foreach( $this->hook_callbacks[$hook] as $callback ) {
						call_user_func( $callback );
					}
				}
				$this->session_hooks[] = $hook;
			}
			$this->undone_hooks = array( );
		}

		public function register_hook_callback( $callback, $hooks ) {
			if( is_array( $hooks ) ) {
				foreach( $hooks as $hook ) {
					$this->register_hook_callback( $callback, $hook );
				}
			} else {
				$this->hook_callbacks[$hooks][] = $callback;
				if( in_array( $hooks, $this->session_hooks ) ){
					call_user_func( $callback );
				}
			}
		}

		public function enqueue_hook( $hook, $ids = array() ) {
			if(!is_array($ids) && !empty($ids)) $ids = array($ids);
			if(!empty($ids) && isset($this->undone_hooks[$hook])) $ids = array_merge($this->undone_hooks[$hook], $ids);
			$this->undone_hooks[$hook] = (!empty($ids)) ? $ids : array();
		}


		/*
		* Module Initialisation
		*/
		private function init_module_path( ) {
			$this->rm_path = $this->root_path.'core/data_handler/includes/modules/read/';
			$this->wm_path = $this->root_path.'core/data_handler/includes/modules/write/';
		}

		private function scan_read_modules( ) {
			//Scan "local" read modules
			$dh = opendir( $this->rm_path );
			while( false !== ( $file = readdir( $dh ) ) ) {
				if( $file != '.' && $file != '..' && $file != '.svn' && is_dir( $this->rm_path.$file ) && is_file( $this->rm_path.$file.'/pdh_r_'.$file.'.class.php' ) ) {
					$this->register_read_module( $file, $this->rm_path.$file );
				}
			}
		}
		
		private function rm($module) {
			if(isset($this->read_modules[$module])) return registry::register('pdh_r_'.$module);
		}

		public function register_read_module( $module_name, $module_path){
			if( isset( $this->read_modules[$module_name] ) ) {
				return false;
			}
			//include class
			$module = 'pdh_r_'.$module_name;
			require( $module_path.'/'.$module.'.class.php' );
			//it's not yet initialised
			$this->read_modules[$module_name] = false;

			//register hooks
			$hooks = $this->rm($module_name)->get_hooks( );
			if( is_array( $hooks ) ) {
				foreach( $hooks as $hook ) {
					$this->registered_hooks[$hook][] = $module_name;
				}
			}

			//init language
			$this->rm($module_name)->init_lang( $module_path );
			if( is_array( $this->rm($module_name)->preset_lang ) )
				$this->preset_lang = array_merge( $this->preset_lang, $this->rm($module_name)->preset_lang );

			unset( $hooks );
			return true;
		}

		private function init_read_module( $module_name ) {
			if( !$this->read_modules[$module_name] ) {
				$module = $this->rm($module_name);
				if( $this->module_needs_update( $module_name ) ) {
					$module->init( );
					$this->timekeeper->put( 'pdh_mu_times', $module_name, $this->time->time, true );
				} else {
					$module->init( );
				}
				$this->read_modules[$module_name] = true;
			}
		}

		public function unregister_read_module( $module_name ) {
			//unset hooks
			$hooks = $this->rm($module_name)->get_hooks( );
			if( is_array( $hooks ) ) {
				foreach( $hooks as $hook ) {
					$array_keys = array_keys( $this->registered_hooks[$hook], $module_name );
					foreach( $array_keys as $key )
						unset( $this->registered_hooks[$hook][$key] );
				}
			}

			//unset language
			$preset_lang = $this->rm($module_name)->preset_lang;
			if( is_array( $preset_lang ) ) {
				foreach( array_keys( $preset_lang ) as $lang_key )
					unset( $this->preset_lang[$lang_key] );
			}

			//not initialized anymore
			$this->read_modules[$module_name] = false;
		}
		
		private function wm($module) {
			if(isset($this->write_modules[$module])) return registry::register('pdh_w_'.$module);
		}

		public function register_write_module( $module_name, $module_path = null ) {
			//check if its already initialised
			if( isset( $this->write_modules[$module_name] ) )
				return true;

			// not yet initialised => try to initialise
			if( !isset( $module_path ) )
				$module_path = $this->wm_path.$module_name;

			$module = 'pdh_w_'.$module_name;
			$class_file = $module_path.'/'.$module.'.class.php';

			require_once( $class_file );
			$this->write_modules[$module_name] = true;
			return true;
		}

		public function unregister_write_module( $module_name ) {
			unset( $this->write_modules[$module_name] );
		}
		
		/*********************************************************************************************************************************
		*
		* Check Methods
		*/
		public function check_read_module( $module_name, $error=false ) {
			if($error && !isset($this->read_modules[$module_name])) {
				$data = debug_backtrace();
				$this->pdl->log('pdh_error', $data[2]['file'], $data[2]['line'], 'Invalid read module', array('module: '.$module_name, 'function: '.$data[1]['function']));
				return false;
			}
			return isset($this->read_modules[$module_name]);
		}
		
		public function check_write_module( $module_name, $error=false ) {
			//try registering first
			try {
				$this->register_write_module($module_name);
			} catch(Exception $e) {
			}
			if($error && !isset($this->write_modules[$module_name])) {
				$data = debug_backtrace();
				$this->pdl->log('pdh_error', $data[2]['file'], $data[2]['line'], 'Invalid write module', array('module: '.$module_name, 'function: '.$data[1]['function']));
				return false;
			}
			return isset($this->write_modules[$module_name]);
		}
		

		/*********************************************************************************************************************************
		*
		* GET Methods
		*/
		public function get( $module, $tag, $params = array( ), $sub_arr = array( ) ) {
			if(!$this->check_read_module($module, true)) return null;
			if( !is_array( $params ) ) {
				$params = array(
					$params,
				);
			}
			if( !empty( $sub_arr ) ) {
				$params = $this->post_process_preset( $params, $sub_arr );
			}
			$method = 'get_'.$tag;
			if( method_exists( $this->rm($module), $method ) ) {
				$this->init_read_module( $module );
				return call_user_func_array( array( $this->rm($module), $method ), $params );
			} else {
				$data = debug_backtrace();
				$extra = array('module: '.$module, 'tag: '.$tag, 'params: '.implode( ", ", $params ), 'sub_array: '.implode( ", ", $sub_arr ));
				$this->pdl->log( 'pdh_error', $data[1]['file'], $data[1]['line'], 'Invalid *get* call', $extra);
				return null;
			}
		}

		public function aget( $module, $tag, $expand_key, $params = array( ), $assoc = false, $sub_arr = null, $ret_arr = array() ) {
			if(!$this->check_read_module($module, true)) return null;
			$params_copy = $params;
			if(isset($params[$expand_key]) && is_array($params[$expand_key])){
				foreach( $params[$expand_key] as $expandme ) {
					$params_copy[$expand_key] = $expandme;
					if( $assoc ) {
						$ret_arr[$expandme][$tag] = $this->get( $module, $tag, $params_copy, $sub_arr );
					} else {
						$ret_arr[$expandme] = $this->get( $module, $tag, $params_copy, $sub_arr );
					}
				}
				return $ret_arr;
			}
		}

		public function maget( $modules, $tags, $expand_key, $params = array(), $sub_arr = null, $multiple_params = false, $html_func = false ) {
			$ret_arr = array();
			$func = ($html_func) ? 'ageth' : 'aget';
			$multiple_module = (is_array($modules)) ? true : false;
			foreach($tags as $key => $tag) {
				$cur_params = ($multiple_params) ? $params[$key] : $params;
				$cur_module = ($multiple_module) ? $modules[$key] : $modules;
				$ret_arr = $this->$func( $cur_module, $tag, $expand_key, $cur_params, true, $sub_arr, $ret_arr );
			}
			return $ret_arr;
		}

		public function geth( $module, $tag, $params, $sub_arr = null ) {
			if(!$this->check_read_module($module, true)) return null;
			if( !is_array( $params ) ) {
				$params = array(
					$params,
				);
			}
			if( $sub_arr != null ) {
				$params = $this->post_process_preset( $params, $sub_arr );
			}
			$method = 'get_html_'.$tag;
			if( method_exists( $this->rm($module), $method ) ) {
				$this->init_read_module( $module );
				return call_user_func_array( array( $this->rm($module), $method ), $params );
			} else {
				return $this->get( $module, $tag, $params );
			}
		}

		public function ageth( $module, $tag, $expand_key, $params = array( ), $assoc = false, $sub_arr = null, $ret_arr = array() ) {
			if(!$this->check_read_module($module, true)) return null;
			$params_copy = $params;
			foreach( $params[$expand_key] as $expandme ) {
				$params_copy[$expand_key] = $expandme;
				if( $assoc ) {
					$ret_arr[$expandme][$tag] = $this->geth( $module, $tag, $params_copy, $sub_arr );
				} else {
					$ret_arr[$expandme] = $this->geth( $module, $tag, $params_copy, $sub_arr );
				}
			}
			return $ret_arr;
		}

		public function get_caption( $module, $tag, $params ) {
			if(!$this->check_read_module($module, true)) return null;
			if( !is_array( $params ) ) {
				$params = array(
					$params,
				);
			}
			$method = 'get_caption_'.$tag;
			if( method_exists( $this->rm($module), $method ) ) {
				$this->init_read_module( $module );
				return call_user_func_array( array( $this->rm($module), $method ), $params );
			} else {
				if( isset( $this->rm($module)->module_lang[$tag] ) ) {
					return $this->rm($module)->module_lang[$tag];
				} else {
					return $tag;
				}
			}
		}

		public function get_html_caption( $module, $tag, $params, $preset='' ) {
			if(!$this->check_read_module($module, true)) return null;
			if($preset) $this->init_preset_list();
			if( !is_array( $params ) ) {
				$params = array(
					$params,
				);
			}
			$method = 'get_html_caption_'.$tag;
			if( method_exists( $this->rm($module), $method ) ) {
				$this->init_read_module( $module );
				return call_user_func_array( array( $this->rm($module), $method ), $params );
			} else {
				return $this->get_caption( $module, $tag, $params );
			}
		}

		public function get_dt_tags($module) {
			return $this->rm($module)->detail_twink;
		}

		public function get_lang( $module, $lang ) {
			return $this->rm($module)->module_lang[$lang];
		}

		/*************************************************************************************************************************
		*
		* Compare
		*/
		public function comp( $module, $tag, $direction, $params1, $params2 ) {
			if(!$this->check_read_module($module, true)) return null;
			if( !is_array( $params1 ) ) {
				$params1 = array(
					$params1,
				);
			}
			if( !is_array( $params2 ) ) {
				$params2 = array(
					$params2,
				);
			}
			$comp_method = 'comp_'.$tag;
			if( method_exists( $this->rm($module), $comp_method ) ) {
				$this->init_read_module( $module );
				return $direction * call_user_func_array( array( $this->rm($module), $comp_method ), array( $params1, $params2 ) );
			} else {
				$get_method = 'get_'.$tag;
				$value1		= $this->get( $module, $tag, $params1 );
				$value2		= $this->get( $module, $tag, $params2 );
				if(!is_numeric($value1) && !is_numeric($value2)) return strcasecmp($value1, $value2)*$direction;
				if( $value1 > $value2 ) {
					return 1 * $direction;
				}
				if( $value1 < $value2 ) {
					return( - 1 ) * $direction;
				}
			}
		}

		public function sort( $id_list, $module, $tag, $direction = 'asc', $params = array( ), $id_position = 0 ) {
			if(empty($id_list) || !is_array($id_list) || !$this->check_read_module($module, true)) return $id_list;

			//check for a sort function in read-module
			if(method_exists($this->rm($module), 'sort')) {
				$this->init_read_module( $module );
				return $this->rm($module)->sort($id_list, $tag, $direction, $params, $id_position);
			}
			// select a clean cache instance
			if( !empty( $this->sort_cache[$this->sort_cache[0]] ) ) $this->sort_cache[0]++;

			//setup sort infos
			$this->sort_cache[$this->sort_cache[0]]['module']			= $module;
			$this->sort_cache[$this->sort_cache[0]]['tag']			= $tag;
			$this->sort_cache[$this->sort_cache[0]]['direction']		= ( $direction == 'desc' ) ? - 1 : 1;
			$this->sort_cache[$this->sort_cache[0]]['params']			= $params;
			$this->sort_cache[$this->sort_cache[0]]['id_position']	= $id_position;

			//sort
			uasort( $id_list, array( &$this, "sort_by_tag" ) );

			//cleanup cache
			$this->sort_cache[$this->sort_cache[0]] = array();
			if($this->sort_cache[0] > 1) $this->sort_cache[0]--; //reduce one level if in nested sort
			return $id_list;
		}

		private function sort_by_tag( $id1, $id2 ) {
			$module				= $this->sort_cache[$this->sort_cache[0]]['module'];
			$tag				= $this->sort_cache[$this->sort_cache[0]]['tag'];
			$direction			= $this->sort_cache[$this->sort_cache[0]]['direction'];
			$id_pos				= $this->sort_cache[$this->sort_cache[0]]['id_position'];
			$params1			= $this->sort_cache[$this->sort_cache[0]]['params'];
			$params2			= $this->sort_cache[$this->sort_cache[0]]['params'];
			$params1[$id_pos]	= $id1;
			$params2[$id_pos]	= $id2;

			return $this->comp( $module, $tag, $direction, $params1, $params2 );
		}

		public function limit( $id_list, $start = 0, $length = 1 ) {
			return array_slice( $id_list, $start, $length );
		}

		/********************************************************************************************************************************************************
		*
		* Write
		*/
		public function put( $module, $function, $params = array( ) ) {
			#if(!$this->check_write_module($module, true)) return null;
			if( !is_array( $params ) ) {
				$params = array(
					$params,
				);
			}
			if( $this->register_write_module( $module ) ) {
				return call_user_func_array( array( $this->wm($module), $function ), $params );
			} else {
				$data = debug_backtrace();
				$extra = array('module: '.$module, 'function: '.$function, 'params: '.implode( ", ", $params ));
				$this->pdl->log( "pdh_error", $data[1]['file'], $data[1]['line'], "Invalid put call", $extra );
				return false;
			}
		}

		/*******************************************************************************************************************************************************
		*
		*   Preset Methods
		*/

		public function get_preset_list( $primary_id = "", $available_subs = array( ), $layout_subs = array( ) ) {
			if( empty( $this->preset_list ) ) {
				$this->init_preset_list( );
			}

			if( empty( $primary_id ) ) {
				return $this->preset_list;
			}

			$typed_presets = array( );
			foreach( $this->preset_list as $preset_name => $preset ) {
				//primary id invalid
				if( $preset[2][0] != $primary_id )
					continue;

				//check call params
				foreach( $preset[2] as $cparam ) {
					if( $this->is_substitution( $cparam ) ) {
						if( in_array( $cparam, $this->global_subs ) ) {
							continue;
						}
						if( in_array( $cparam, (array) $layout_subs ) ) {
							continue;
						}
						if( in_array( $cparam, (array) $available_subs ) ) {
							continue;
						}
						continue 2;
					}
				}

				//check description params
				foreach( $preset[2] as $dparam ) {
					if( $this->is_substitution( $dparam ) ) {
						if( in_array( $dparam, $this->global_subs ) ) {
							continue;
						}
						if( in_array( $cparam, $layout_subs ) ) {
							continue;
						}
						if( in_array( $dparam, $available_subs ) ) {
							continue;
						}
						continue 2;
					}
				}

				$typed_presets[$preset_name] = $preset;
			}
			return $typed_presets;
		}

		public function is_substitution( $param ) {
			return( is_string( $param ) && $param[0] == "%" && $param[strlen( $param ) - 1] == "%" );
		}

		public $global_subs = array(
			'%ALL_IDS%',
		);

		private function init_preset_list( ) {
			if($this->presets_loaded) return true;
			//init module presets
			foreach( $this->read_modules as $module_name => $initiliazed ) {
				if( method_exists( $this->rm($module_name), 'gen_presets' ) ) {
					$this->rm($module_name)->gen_presets( );
				}

				if( is_array( $this->rm($module_name)->presets ) ) {
					//this is ugly as hell and will be rewritten.
					foreach( $this->rm($module_name)->presets as $presetname => $params ) {
						$temp_arr[$presetname] = array(
							$module_name,
							$params[0],
							$params[1],
							$params[2],
						);
					}
					if(isset($temp_arr) && is_array($temp_arr)) $this->preset_list = array_merge( $this->preset_list, $temp_arr );
				}
			}

			//get users custom presets
			$this->preset_list = array_merge( $this->preset_list, $this->get_user_presets( ));
			$this->preset_lang = array_merge( $this->preset_lang, $this->get_user_preset_lang( ));
			$this->presets_loaded = true;
		}

		public function get_preset_description( $preset_name ) {
			return (isset($this->preset_lang[$preset_name])) ? $this->preset_lang[$preset_name] : $preset_name;
		}

		public function get_preset( $preset_name ) {
			$this->init_preset_list( );

			$preset_name = ( array_key_exists( $preset_name, $this->system_settings['aliases'] ) ) ? $this->system_settings['aliases'][$preset_name] : $preset_name;
			return $this->preset_list[$preset_name];
		}

		public function pre_process_preset( $preset_name, $add_values = array( ), $index = null ) {
			$preset = $this->get_preset( $preset_name );
			if( $preset == "" ) {
				$data = debug_backtrace();
				$this->pdl->log( "pdh_error", $data[1]['file'], $data[1]['line'], "Invalid preset", 'preset: '.$preset_name );
				return null;
			}
			$return_array = array( );
			$preset[2]    = $this->post_process_preset( $preset[2], $this->system_settings['subs'] );
			$preset[3]    = $this->post_process_preset( $preset[3], $this->system_settings['subs'] );
			if(( in_array( '%ALL_IDS%', $preset[2], true ) ) || ( in_array( '%ALL_IDS%', $preset[3], true ) ) ) {
				$mlist = $this->get( 'multidkp', 'id_list', array( ) );
				$ra = array( );
				foreach( $mlist as $id ) {
					$ra[] = array_merge( array( 0 => $preset[0], 1 => $preset[1], 2 => $this->post_process_preset( $preset[2], array( '%ALL_IDS%' => $id ) ), 3 => $this->post_process_preset( $preset[3], array( '%ALL_IDS%' => $id ) ), ), $add_values );
				}
				$return_array = $ra;
			} else {
				$preset = array_merge( $preset, $add_values );
				$return_array = array(
					$preset,
				);
			}

			if( $index === null ) {
				return $return_array;
			} else {
				return $return_array[$index];
			}
		}

		public function post_process_preset( $param_arr, $sub_arr = array( ) ) {
			if( empty( $sub_arr ) || !is_array( $sub_arr ) ) {
				return $param_arr;
			} else {
				$search  = array_keys( $sub_arr );
				$replace = array_values( $sub_arr );
				$length  = sizeof( $param_arr );
				for( $i = 0; $i < $length; $i++ ) {
					$param_arr[$i] = str_replace( $search, $replace, $param_arr[$i] );
				}
				return $param_arr;
			}
		}
		
		public function get_user_presets( ) {
			if(empty($this->user_presets)) {
				$user_presets = array( );
				$preset_file = $this->pfh->FilePath( 'layouts/presets_user.php', 'eqdkp' );
				if(is_file($preset_file)) {
					include($preset_file);
				}
				$this->user_presets = (!isset($user_presets)) ? array() : $user_presets;
			}
			return $this->user_presets;
		}

		public function get_user_preset_lang( ) {
			if(empty($this->user_presets_lang)) {
				$preset_file = $this->pfh->FilePath( 'layouts/presets_user.php', 'eqdkp' );
				if(is_file($preset_file)) {
					include($preset_file);
				}
				$this->user_presets_lang = (!isset($user_presets_lang)) ? array() : $user_presets_lang;
			}
			return $this->user_presets_lang;
		}

		public function update_user_preset($preset_name, $preset=false, $preset_lang='') {
			$this->init_preset_list();
			if(!$preset_name) return false;
			if(!isset($this->user_presets[$preset_name]) && isset($this->preset_list[$preset_name])) return false; //preset name already used
			if($preset) $this->user_presets[$preset_name] = $preset;
			if($preset_lang) $this->user_presets_lang[$preset_name] = $preset_lang;
			$this->presets_changed = true;
			return true;
		}

		public function delete_user_preset($preset_name) {
			$this->init_preset_list();
			if(!$preset_name || !isset($this->user_presets[$preset_name])) return false;
			unset($this->user_presets[$preset_name]);
			if(isset($this->user_presets_lang[$preset_name])) unset($this->user_presets_lang[$preset_name]);
			$this->presets_changed = true;
			return true;
		}

		private function save_user_presets( ) {
			if(!$this->presets_changed) return true;
			$file  = $this->pfh->FilePath( 'layouts/presets_user.php', 'eqdkp' );
			$data  = "<?php\n";
			$data .= "if (!defined('EQDKP_INC')){\n\tdie('You cannot access this file directly.');\n}\n";
			$data .= '$user_presets = ';
			$data .= var_export( $this->user_presets, true );
			$data .= ";\n";
			$data .= '$user_presets_lang = ';
			$data .= var_export( $this->user_presets_lang, true );
			$data .= ";\n";
			$data .= "\n?";
			$data .= ">";
			$this->pfh->putContent( $file, $data );
		}

		/**************************************************************************************************
		*
		* Layout methods
		*/
		public function init_eqdkp_layout( $layout ) {
			$this->system_settings = array( );
			$this->system_settings = $this->get_eqdkp_layout( $layout );
		}
		
		public function get_layout_config( $name = false ){
			if (!$name) { 
				return $this->system_settings['config'];
			} elseif (isset($this->system_settings['config'][$name])) {
				return $this->system_settings['config'][$name];
			}
			return false;
		}

		public function get_eqdkp_layout( $layout ) {
			//check if it is a default system file
			$sys_file = $this->root_path.'core/data_handler/includes/systems/'.$layout.'.esys.php';
			if( file_exists( $sys_file ) ) {
				require( $sys_file );
			} else {
				//ok, it wasn't.. lets check the data folder
				$fp = $this->pfh->FolderPath( 'layouts', 'eqdkp' );
				if( file_exists( $fp.$layout.'.esys.php' ) ) {
					require( $fp.$layout.'.esys.php' );
				} else {

					require( $this->root_path.'core/data_handler/includes/systems/normal.esys.php' );
				}
			}

			//prepare substitution array
			$system_def['subs'] = array( );
			if( is_array( $system_def['substitutions'] ) && !empty( $system_def['substitutions'] ) ) {
				foreach( $system_def['substitutions'] as $substitution => $options ) {
					$system_def['subs']['%'.$substitution.'%'] = $options['value'];
				}
			}

			//prepare substitution array
			$system_def['config'] = array( );
			if( is_array( $system_def['options'] ) && !empty( $system_def['options'] ) ) {
				foreach( $system_def['options'] as $name => $options ) {
					$system_def['config'][$name] = $options['value'];
				}
			}

			//add plugin/portal pages/objects
			$this->init_page_objects();
			if(isset($this->page_objects[$layout]) && is_array($this->page_objects[$layout])) $system_def['pages'] = array_merge_recursive($this->page_objects[$layout], $system_def['pages']);

			return $system_def;
		}

		public function get_eqdkp_layout_description( $layout ) {
			$layout_def = $this->get_eqdkp_layout( $layout );
			return $layout_def['data']['description'];
		}
		
		public function get_eqdkp_base_layout($layout){
			$layout_def = $this->get_eqdkp_layout( $layout );
			return $layout_def['base_layout'];
		}

		public function get_page_list( $layout = '' ) {
			if( $layout == '' ) {
				return array_keys( $this->system_settings['pages'] );
			} else {
				$layout = $this->get_eqdkp_layout( $layout );
				return array_keys( $layout['pages'] );
			}
		}

		public function get_page_settings( $page = null, $object = null, $layoutname = '' ) {
			if( $layoutname == '' ) {
				$layout = &$this->system_settings;
			} else {
				$layout = $this->get_eqdkp_layout( $layoutname );
			}

			if( $page != null ) {
				if( $object == null ) {
					$settings = (isset($layout['pages'][$page])) ? $layout['pages'][$page] : null;
				} else {
					$settings = (isset($layout['pages'][$page][$object])) ? $layout['pages'][$page][$object] : null;
				}
				if( $settings != null ) {
					return $settings;
				} else {
					if( $layoutname == '' ) {
						return $this->get_page_settings( $page, $object, $this->system_settings['base_layout'] );
					} else {
						$this->auto_update_layout($layoutname);
						$layout = $this->get_eqdkp_layout( $layoutname );
						if( $object == null ) {
							$settings = (isset($layout['pages'][$page])) ? $layout['pages'][$page] : null;
						} else {
							$settings = (isset($layout['pages'][$page][$object])) ? $layout['pages'][$page][$object] : null;
						}
						if( $settings != null ) {
							return $settings;
						} else {
							$data = debug_backtrace();
							$extra = array('page: '.$page, 'object: '.$object, 'layout: '.$layoutname);
							$this->pdl->log( "pdh_error", $data[1]['file'], $data[1]['line'], "Page not found", $extra);
						}
						return null;
					}
				}
			}

			return $layout;
		}

		public function get_layout_list( $default_layouts = true, $user_layouts = true ) {
			$systems = array( );

			if( $default_layouts ) {
				$dir = $this->root_path.'core/data_handler/includes/systems/';
				$dh = opendir( $dir );
				while( false !== ( $file = readdir( $dh ) ) ) {
					if( is_file($dir.$file) && strpos($file, '.esys.php') ) {
						$systems[] = substr( $file, 0, - 9 );
					}
				}
			}

			if( $user_layouts ) {
				$fp = $this->pfh->FolderPath( 'layouts', 'eqdkp' );
				$dh = opendir( $fp );
				while( false !== ( $file = readdir( $dh ) ) ) {
					if( is_file($fp.$file) && strpos($file, '.esys.php') ) {
						$systems[] = substr( $file, 0, - 9 );
					}
				}
			}
			return $systems;
		}

		public function layout_exists( $layout ) {
			$layouts = $this->get_layout_list( true, true );
			return in_array( $layout, $layouts );
		}

		public function user_layout_exists( $layout ) {
			$layouts = $this->get_layout_list( false, true );
			return in_array( $layout, $layouts );
		}

		public function make_editable( $layout ) {
			if(!$this->layout_exists($layout)) return false;
			if($this->user_layout_exists($layout)) return $layout;
			if($this->config->get('eqdkp_layout') == $layout) $this->config->set('eqdkp_layout', 'user_'.$layout);
			$this->save_layout('user_'.$layout, $this->get_eqdkp_layout($layout));
			return 'user_'.$layout;
		}

		public function save_layout( $filename, $layout ) {
			$fp		 = $this->pfh->FolderPath( 'layouts', 'eqdkp' );
			$file	 = $fp."$filename.esys.php";
			$data	 = "<?php\n";
			$data	.= "if (!defined('EQDKP_INC')){\n\tdie('You cannot access this file directly.');\n}\n";
			$data	.= '$system_def = ';
			$data	.= var_export( $layout, true );
			$data	.= "\n?";
			$data	.= ">";
			$this->pfh->putContent( $file, $data );
		}

		public function get_default_value( $name ) {
			return( isset( $this->system_settings['defaults'][$name] ) ) ? $this->system_settings['defaults'][$name] : null;
		}

		public function auto_update_layout( $layout_name ) {
			$layout_def = $this->get_eqdkp_layout( $layout_name );
			//check if base layout got some new settings
			$layout_base = $this->get_eqdkp_layout( $layout_def['base_layout'] );
			$changes = false;
			$fields = array(
				'data',
				'aliases',
				'defaults',
				'options',
				'substitutions',
				'pages',
			);
			foreach( $fields as $field ) {
				foreach( $layout_base[$field] as $key => $value ) {
					if( !isset( $layout_def[$field][$key] ) ) {
						$layout_def[$field][$key] = $layout_base[$field][$key];
						$changes = true;
					}
				}
			}

			//delete outdated objects
			foreach( $layout_def['pages'] as $page => $page_objects ) {
				foreach( $page_objects as $page_object => $options ) {
					if( !isset( $layout_base['pages'][$page] ) ) {
						unset( $layout_def['pages'][$page] );
						$changes = true;
						continue;
					}
					if( !isset( $layout_base['pages'][$page][$page_object] ) ) {
						unset( $layout_def['pages'][$page][$page_object] );
						$changes = true;
						continue;
					}
				}
			}

			//add new objects
			foreach( $layout_base['pages'] as $page => $page_objects ) {
				foreach( $page_objects as $page_object => $options ) {
					if( !isset( $layout_def['pages'][$page] ) ) {
						$layout_def['pages'][$page] = $layout_base['pages'][$page];
						$changes = true;
						continue;
					}
					if( !isset( $layout_def['pages'][$page][$page_object] ) ) {
						$layout_def['pages'][$page][$page_object] = $layout_base['pages'][$page][$page_object];
						$changes = true;
						continue;
					}
				}
			}

			if( $changes )
				$this->save_layout( $layout_name, $layout_def );
		}

		/******************************************************************************************************************************
		 *
		 * Adding new Pages/Objects
		 */

		public function add_page( $layout, $page, $data ) {
			$this->init_page_objects();
			if(isset($this->page_objects[$layout][$page])) return false;
			$this->page_objects[$layout][$page] = $data;
			$this->page_objects_changed = true;
		}

		public function add_object( $layout, $page, $object, $data ) {
			$this->init_page_objects();
			if(isset($this->page_objects[$layout][$page][$object])) return false;
			$this->page_objects[$layout][$page][$object] = $data;
			$this->page_objects_changed = true;
		}
		
		public function add_object_tablepreset( $layout, $page, $object, $data ) {
			$this->init_page_objects();
			if (isset($this->page_objects[$layout][$page][$object]['table_presets'])){
				foreach ($this->page_objects[$layout][$page][$object]['table_presets'] as $val){
					if ($val['name'] == $data['name']) return false;
				}
			}
			
			$this->page_objects[$layout][$page][$object]['table_presets'][] = $data;
			$this->page_objects_changed = true;
		}
		
		

		public function delete_page( $layout, $page ) {
			$this->init_page_objects();
			if(!isset($this->page_objects[$layout][$page])) return true;
			unset($this->page_objects[$layout][$page]);
			$this->page_objects_changed = true;
		}

		public function delete_object( $layout, $page, $object ) {
			$this->init_page_objects();
			if(!isset($this->page_objects[$layout][$page][$object])) return true;
			unset($this->page_objects[$layout][$page][$object]);
			$this->page_objects_changed = true;
		}

		private function init_page_objects() {
			if($this->page_objects_loaded) return true;
			$file = $this->pfh->FolderPath('layouts', 'eqdkp').'page_objects.php';
			if(!file_exists($file)) return true; //nothing saved yet
			include($file);
			$this->page_objects = $page_objects;
			$this->page_objects_loaded = true;
		}

		private function save_page_objects() {
			if(!$this->page_objects_changed) return true;
			$file = $this->pfh->FolderPath('layouts', 'eqdkp').'page_objects.php';
			$data	 = "<?php\n";
			$data	.= "if (!defined('EQDKP_INC')){\n\tdie('You cannot access this file directly.');\n}\n";
			$data	.= '$page_objects = ';
			$data	.= var_export( $this->page_objects, true );
			$data	.= "\n?";
			$data	.= ">";
			$this->pfh->putContent( $file, $data );
		}
	}
	//end class
}
//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_plus_datahandler', plus_datahandler::$shortcuts);
?>