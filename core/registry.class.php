<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
	header('HTTP/1.0 404 Not Found');exit;
}

final class registry extends super_registry{
	protected static $order = array();
	protected static $inst = array();
	
	private static $loaded = array();
	
	private static $destruct_started = false;
	private static $all_deps = array();
	
	public static function load($classname) {
		if(isset(self::$loaded[$classname])) return true;
		self::$loaded[$classname] = true;
		if(!isset(self::$locs[$classname]) && !class_exists($classname) && !(self::get_const('lite_mode') && in_array($classname, self::$lite_igno))) {
			$data = debug_backtrace();
			foreach($data as $call) echo 'file: '.$call['file'].", line: ".$call['line']."<br />";
			die('call to not listed class: '.$classname); //use pdl here later
		}
		if(!class_exists($classname)) {
			$lite = registry::get_const('lite_mode') ? 'lite/' : '';
			$path = self::$const['root_path'].self::$locs[$classname];
			if(!file_exists($path.$lite.$classname.'.class.php')) $lite = '';
			include_once($path.$lite.$classname.'.class.php');
		}
	}
	
	public static function register($classname, $params=array(), $diff_inst=false) {
		$hash = 0;
		if($diff_inst) {
			$hash = $diff_inst;
		} elseif(!empty($params)) {
			$hash = serialize($params);
		}
		$hash = md5($hash);
		if(isset(self::$inst[$classname][$hash])) {
			return self::$inst[$classname][$hash];
		}
		self::load($classname);
		if(empty($params)) {
			self::$inst[$classname][$hash] = new $classname();
		} else {
			$ref = new ReflectionClass($classname);
			self::$inst[$classname][$hash] = $ref->newInstanceArgs($params);
			unset($ref);
		}
		self::$inst[$classname][$hash]->class_hash = $hash;
		return self::$inst[$classname][$hash];
	}

	public static function fetch($name, $params=array()) {
		if(isset(registry::$aliases[$name])) {
			if(is_array(registry::$aliases[$name])) {
				return self::register(registry::$aliases[$name][0], registry::$aliases[$name][1]);
			} else {
				return self::register(registry::$aliases[$name]);
			}
		} elseif(registry::class_exists($name)) return self::register($name, $params);
		return false;
	}

	public static function destruct($class, $class_hash='') {
		if(self::$destruct_started) return;
		self::$destruct_started = true;
		self::$all_deps = array();
		foreach(array_keys(self::$loaded) as $classname) {
			$deps = $classname::__dependencies();
			if(!empty($deps)) {
				foreach($deps as $name) {
					if(isset(registry::$aliases[$name])) {
						if(is_array(registry::$aliases[$name])) {
							$dep_class = registry::$aliases[$name][0];
						} else {
							$dep_class = registry::$aliases[$name];
						}
					} elseif(registry::class_exists($name)) $dep_class = $name;
					self::$all_deps[$dep_class][] = $classname;
				}
			}
		}
		self::_destruct($class, $class_hash);
		self::$destruct_started = false;
	}
	
	private static function _destruct($class, $class_hash='') {
		if(!empty(self::$all_deps[$class])) {
			foreach(self::$all_deps[$class] as $classname) {
				self::_destruct($classname);
			}
		}
		if($class_hash && $class_hash != "") unset(self::$inst[$class][$class_hash]);
		else unset(self::$inst[$class]);
	}
}
?>