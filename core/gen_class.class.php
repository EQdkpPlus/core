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

abstract class gen_class {
	public static $dependencies = array();
	public static $shortcuts = array();
	public static $singleton = true;
	
	public $class_hash = '';

	public function __get($name) {
		$shorts = static::__shortcuts();
		if(isset($shorts[$name])) {
			if(is_array($shorts[$name])) {
				return registry::register($shorts[$name][0], $shorts[$name][1]);
			} else {
				return registry::register($shorts[$name]);
			}
		}
		if(is_int(array_search($name, $shorts))) {
			if(isset(registry::$aliases[$name])) {
				if(is_array(registry::$aliases[$name])) {
					return registry::register(registry::$aliases[$name][0], registry::$aliases[$name][1]);
				} else {
					return registry::register(registry::$aliases[$name]);
				}
			} elseif(registry::class_exists($name)) return registry::register($name);
		}
		if($const = registry::get_const($name)) return $const;
		return null;
	}
	
	public function __isset($name) {
		return registry::isset_const($name);
	}
	
	public static function __dependencies() {
		return static::$dependencies;
	}
	
	public static function __shortcuts() {
		return static::$shortcuts;
	}
	
	public function __destruct() {
		#echo '<span style="color:#ffff00;" >destruct called: '.get_class($this).'</span><br />';
		registry::destruct(get_class($this), $this->class_hash);
	}
}
?>