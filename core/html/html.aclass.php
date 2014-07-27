<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

abstract class html {
	// field type
	protected static $type = '';
	
	protected static $ignore = array('text', 'text2', 'type');
	
	public function __construct($name, $options=array()) {
		$this->name = $name;
		foreach($options as $key => $option) {
			if(in_array($key, self::$ignore)) continue;
			$this->$key = $option;
		}
		if(empty($this->value) && isset($this->default)) $this->value = $this->default;
		if(method_exists($this, '_construct')) $this->_construct();
	}
	
	abstract protected function _toString();
	
	public function inpval() {
		$value = $this->_inpval();
		if(isset($this->required) && $this->required && empty($value)) {
			throw new FormException(sprintf(registry::fetch('user')->lang('fv_php_required'),$this->name));
		}
		if(!empty($this->pattern) && !empty($value)) {
			// add a delimiter to pattern
			$pattern = $this->pattern($this->pattern);
			$pattern = (strpos($pattern,'~') === false) ? '~'.$pattern.'~' : '#'.$pattern.'#';
			if(!preg_match($pattern,$value)) {
				throw new FormException(sprintf(registry::fetch('user')->lang('fv_php_sample_pattern'),$this->name));
			}
		}
		return $value;
	}
	
	public function __get($name) {
		if($name == 'type') return self::$type;
		$class = register($name);
		if($class) return $class;
		return null;
	}
	
	public function __toString() {
		return $this->_toString();
	}
	
	protected function cleanid($input) {
			if(strpos($input, '[') === false && strpos($input, ']') === false) return $input;
			$out = str_replace(array('[', ']'), array('_', ''), $input);
			return 'clid_'.$out;
	}
	
	/*
	 *	some predefined patterns
	 */
	protected function pattern($pattern) {
		switch( $pattern ){
			case 'email':
				if(empty($this->placeholder)) $this->placeholder = 'email@example.com';
				return '\w+(\.\w+)*@\w+(\.\w+)+';
				
			case 'url':
				if(empty($this->placeholder)) $this->placeholder = 'http(s)://example.com';
				return 'http(s){0,1}://\w+(\.\w+)+';
				
			case 'password':
				$minlength = 6;
				if(empty($this->placeholder)) $this->placeholder = sprintf(registry::fetch('user')->lang('fv_password_placeholder'), $minlength-1);
				return '.{'.$minlength.',}';
				
			default: 
				return $pattern;
		}
	}
}
?>