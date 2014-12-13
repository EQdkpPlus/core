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

		if(empty($this->value) && isset($this->default) && ($this->value!==0) && ($this->value!=='0')) $this->value = $this->default;
		if(method_exists($this, '_construct')) $this->_construct();
	}
	
	abstract protected function _toString();
	
	public function inpval() {
		$value = $this->_inpval();
		$strfieldname = (isset($this->_lang)) ? $this->_lang : $this->name;
		
		if(isset($this->required) && $this->required && empty($value)) {
			throw new FormException(sprintf(registry::fetch('user')->lang('fv_php_required'),$strfieldname));
		}
		if(!empty($this->pattern) && !empty($value)) {
			// add a delimiter to pattern
			$pattern = $this->pattern($this->pattern);
			$pattern = (strpos($pattern,'~') === false) ? '~'.$pattern.'~' : '#'.$pattern.'#';
			if(!preg_match($pattern,$value)) {
				throw new FormException(sprintf(registry::fetch('user')->lang('fv_php_sample_pattern'),$strfieldname));
			}
		}
		
		if(!empty($this->minlength) && !empty($value) && (strlen($value) < $this->minlength)) {
			throw new FormException(sprintf(registry::fetch('user')->lang('fv_php_minlength_error'),$strfieldname));
		}
		
		if(!empty($this->maxlength) && !empty($value) && (strlen($value) > $this->maxlength)) {
			throw new FormException(sprintf(registry::fetch('user')->lang('fv_php_maxlength_error'),$strfieldname));
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
	
	protected function gen_form_change($dep) {
		if(empty($dep)) return ' data-form-change=""';
		return ' data-form-change="'.(is_array($dep) ? implode(',', $dep) : $dep).'"';
	}
	
	/*
	 *	some predefined patterns
	 */
	protected function pattern($pattern) {
		$w = '[\w_-]';
		switch( $pattern ){
			case 'email':
				if(empty($this->placeholder)) $this->placeholder = 'email@example.com';
				return $w.'+(\.'.$w.'+)*@'.$w.'+(\.'.$w.'+)+';
				
			case 'url':
				if(empty($this->placeholder)) $this->placeholder = 'http(s)://example.com';
				return 'http(s){0,1}://'.$w.'+(\.'.$w.'+)+';
				
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