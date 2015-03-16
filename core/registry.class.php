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
			if($classname == 'plus_debug_logger') die('Files not uploaded correctly. Please ensure you have all files uploaded.');
			self::register('plus_debug_logger')->class_doesnt_exist($classname);
		}
		if(!class_exists($classname)) {
			$lite = registry::get_const('lite_mode') ? 'lite/' : '';
			$path = self::$const['root_path'].self::$locs[$classname];
			if(!file_exists($path.$lite.$classname.'.class.php')) $lite = '';
			if(!file_exists($path.$lite.$classname.'.class.php')) {
				self::register('plus_debug_logger')->file_not_found($path.$lite.$classname.'.class.php');
			}
			include_once($path.$lite.$classname.'.class.php');
		}
	}
	
	public static function register($classname, $params=array(), $diff_inst=false) {
		$hash = 'default';
		if($diff_inst) {
			$hash = $diff_inst;
		} elseif(!empty($params)) {
			try {
				$hash = md5(serialize($params));
			} catch(Exception $e){
				$hash = md5(rand());
			}
		}
		if(isset(self::$inst[$classname][$hash])) {
			return self::$inst[$classname][$hash];
		}
		self::load($classname);
		if(empty($params)) {
			#if($classname::$singleton) return new $classname();
			self::$inst[$classname][$hash] = new $classname();
		} else {
			$ref = new ReflectionClass($classname);
			#if($classname::$singleton) return $ref->newInstanceArgs($params);
			self::$inst[$classname][$hash] = $ref->newInstanceArgs($params);
		}
		self::$inst[$classname][$hash]->class_hash = $hash;
		return self::$inst[$classname][$hash];
	}
	
	public static function grab($classname, $hash) {
		if(isset(self::$inst[$classname][$hash])) return self::$inst[$classname][$hash];
		return null;
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
	
	/**
	 * Add a class to the registry, so it's accessable with register();
	 * 
	 * @param string $strClassname - your classname, e.g. plus_datahandler
	 * @param string $strLocation - the location of the class, without a rootpath, e.h. plugins/blupp/classes/
	 * @param string $strAlias - an alias for your classname, for shorter accessability, e.g. pdh
	 * @return false if classname has been already registered. Otherwise Classname or Alias, if Alias has been set successfully
	 */
	public static function add_class($strClassname, $strLocation, $strAlias=false){
		if(!isset(registry::$locs[$strClassname])){
			registry::$locs[$strClassname] = str_replace(registry::get_const('root_path'), "", $strLocation);
			
			if($strAlias !== false){
				if(!isset(registry::$aliases[$strAlias])){
					registry::$aliases[$strAlias] = $strClassname;
					return $strAlias;
				}
			}
			return $strClassname;
		} 
		
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
		#self::$destruct_started = false;
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