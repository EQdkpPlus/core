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

abstract class gen_class {
	public static $dependencies = array();
	public static $shortcuts = array();
	public static $singleton = true;
	
	private $_shorts = array();
	private $_shorts_loaded = false;
	private $_class_index = array();
	
	public $class_hash = '';

	public function __get($name) {
		if(isset($this->_class_index[$name])) {
			$obj = registry::grab($this->_class_index[$name][0], $this->_class_index[$name][1]);
			if($obj) return $obj;
		}
		if(!$this->_shorts_loaded) {
			$this->_shorts = static::__shortcuts();
			$this->_shorts_loaded = true;
		}
		$obj = false;
		if(isset($this->_shorts[$name])) {
			if(is_array($this->_shorts[$name])) {
				$obj = registry::register($this->_shorts[$name][0], $this->_shorts[$name][1]);
			} else {
				$obj = registry::register($this->_shorts[$name]);
			}
		} elseif(isset(registry::$aliases[$name])) {
			if(is_array(registry::$aliases[$name])) {
				$obj = registry::register(registry::$aliases[$name][0], registry::$aliases[$name][1]);
			} else {
				$obj = registry::register(registry::$aliases[$name]);
			}
		} elseif(registry::class_exists($name)) $obj = registry::register($name);
		if($obj) {
			$this->_class_index[$name] = array(get_class($obj), $obj->class_hash);
			return $obj;
		}
		if($const = registry::get_const($name)) return $const;
		return null;
	}
	
	public function __isset($name) {
		return registry::isset_const($name);
	}
	
	public static function __dependencies() {
		if(!isset(static::$dependencies)) return array();
		return static::$dependencies;
	}
	
	public static function __shortcuts() {
		if(!isset(static::$shortcuts)) return array();
		return static::$shortcuts;
	}
	
	public function __destruct() {
		#echo '<span style="color:#ffff00;" >destruct called: '.get_class($this).'</span><br />';
		registry::destruct(get_class($this), $this->class_hash);
	}
}
?>