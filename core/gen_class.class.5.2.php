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
	
	public $class_hash = '';

	public function __get($name) {
		$deps = registry::get_const('short_'.get_class($this));
		if(isset($deps[$name])) {
			if(is_array($deps[$name])) {
				return registry::register($deps[$name][0], $deps[$name][1]);
			} else {
				return registry::register($deps[$name]);
			}
		}
		if(is_array($deps) && is_int(array_search($name, $deps))) {
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

	public static function __isset($name) {
		return registry::isset_const($name);
	}
	
	public function __destruct() {
		registry::destruct(get_class($this), $this->class_hash);
	}
}
?>