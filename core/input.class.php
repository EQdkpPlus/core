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
	header('HTTP/1.0 404 Not Found');exit;
}

class input extends gen_class {

	public $_cache		= array();
	public $_caching	= true;

	/**
	* Get the Superglobal we're using
	*
	* @var string
	* @access private
	*/
	private function _superglobal($key){
		if (filter_has_var(INPUT_POST, $key)){
			return INPUT_POST;
		}

		if (filter_has_var(INPUT_GET, $key)){
			return INPUT_GET;
		}

		if (filter_has_var(INPUT_COOKIE, $key)){
			return INPUT_COOKIE;
		}

		if (filter_has_var(INPUT_SERVER, $key)){
			return INPUT_SERVER;
		}

		if (filter_has_var(INPUT_ENV, $key)){
			return INPUT_ENV;
		}

		// the fallback
		return false;
	}

	/**
	* Get the type of the thing..
	*
	* @var string
	* @access private
	*/
	private function _type($default, $nonget=false){
		$type = ($nonget) ? $default : gettype($default);
		switch($type){
			case 'int':					$out = FILTER_SANITIZE_NUMBER_INT;		break;
			case 'integer':				$out = FILTER_SANITIZE_NUMBER_INT;		break;
			case 'htmlescape':			$out = FILTER_SANITIZE_SPECIAL_CHARS;	break;
			case 'raw':					$out = FILTER_UNSAFE_RAW;				break;
			//float and double need a special handling, so "0,5" can be correctly interpreted as 0.5 (function floatvalue() )
			case 'float':
			case 'double':				$out = FILTER_SANITIZE_NUMBER_FLOAT; 	break;
			case 'string':
			default:					$out = FILTER_SANITIZE_STRING;
		}
		return $out;
	}

	private function _convert($value, $type){
		switch($type){
			case FILTER_SANITIZE_NUMBER_FLOAT:		$out = $this->floatvalue($value);	break;
			case FILTER_SANITIZE_NUMBER_INT:		$out = intval($value);		break;
			default:								$out = $value;
		}
		return $out;
	}

	private function _options($filter){
		switch($filter){
			case FILTER_SANITIZE_NUMBER_FLOAT:	$out = FILTER_FLAG_ALLOW_FRACTION;	break;
			//case FILTER_SANITIZE_STRING:		$out = FILTER_FLAG_NO_ENCODE_QUOTES; break;
			default:							$out = '';
		}
		return $out;
	}

	/**
	* Get an input variable from a superglobal. POST > SESSION > GET
	* if the value which is wanted is deeper in the array.
	*
	* @param array $keys Input keys
	* @param string $default Default variable to return if $key is not set
	* @return mixed
	*/
	private function _get_deep($keys, $default){
		$retval = $default;
		
		foreach($keys as &$key) {
		$key = preg_replace('/[^0-9A-Za-z_]/', '', filter_var($key, FILTER_SANITIZE_STRING));	//clean the key
		}
		unset($key);
		$allkey = "['".implode("']['", $keys)."']";		//build the key string

		//get the data
		eval("if(isset(\$_GET".$allkey.")) \$get = \$_GET".$allkey.";");
		eval("if(isset(\$_POST".$allkey.")) \$post = \$_POST".$allkey.";");
		eval("if(isset(\$_SESSION".$allkey.")) \$session = \$SESSION".$allkey.";");
		eval("if(isset(\$_COOKIE".$allkey.")) \$cookie = \$_COOKIE".$allkey.";");
		if(isset($get)) {
			$retval = $get;
		}
		if(isset($post)) {
			$retval = $post;
		} elseif(isset($session)) {
			$retval = $session;
		}
		if(isset($cookie)) {
			$retval = $cookie;
		}
		return $retval;
	}

	/**
	* A shortcut method to request an input variable. Calls the appropriate
	* type-specifc method based on the variable type of $default
	*
	* @param string $key 			Input key, to get to values, deeper in array use ":"
	* @param mixed $default		Default variable to return if $key is not set. This also determines the type of data cleaning performed.
	* @param mixed $forcetype Set an own type
	* @return mixed
	*/
	public function get($key, $default='', $owntype=''){
		$filter	= (($owntype) ? $this->_type($owntype, true) : $this->_type($default));
	
		if($this->_caching && isset($this->_cache[$filter][$key])){
			$out = $this->_cache[$filter][$key];
		}else{	
			if(strpos($key,':')) {
				$out		= filter_var($this->_get_deep(explode(':', $key),$default), $filter, $this->_options($filter));
			} else {
				$out		= filter_input($this->_superglobal($key), $key, $filter, $this->_options($filter));
			}
			$out		= ($out === false || $out === NULL || $out === '') ? $default : $out;
			$this->_cache[$filter][$key] = $out;
		}
		return (isset($filter) && $filter != '') ? $this->_convert($out, $filter) : $out;
	}

	/**
	* Clean and fetch an input variable that is an array, for example an array
	* of checkbox IDs. Depending on $type, the appropriate cleaning method will be
	* called on each element.
	*
	* @param string $key Input key, to get to values, deeper in array use ":"
	* @param string $type String-based variable type ('int', 'string', etc.)
	* @param string $max_depth Maximum depth to recurse to
	* @return array
	*/
	public function getArray($key, $type, $max_depth = 10){
		if(strpos($key, ':')) {
			$checkarr = $this->_get_deep(explode(':', $key), array());
		} else {
			$checkarr = is_array($key) ? $_POST : (isset($_POST[$key])) ? $_POST[$key] : false;
		}
		$valarr		= is_array($type) ? $type : $this->_type($type, true);
		return (!$checkarr) ? array() : filter_var_array($checkarr, $valarr);
	}

	/**
	* Checks if $key exists as an input value.
	*
	* @param string $key Input key
	* @return boolean
	*/
	public function exists($key, $type=false){
		$retval = false;
		if($type){
			if(is_array($this->getArray($key, $type))){
				$retval = true;
			}
		}else{
			if($this->_superglobal($key) !== false){
				$retval = true;
			}
		}
		return $retval;
	}

	/**
	* float and double need a special handling, so "0,5" can be correctly interpreted as 0.5 (function floatvalue() )
	*
	* @param string $value Input key
	* @return float
	*/
	public function floatvalue($value) {
		return floatval(preg_replace('#^([-]*[0-9\.,]+?)((\.|,)([0-9-]+))*$#e', "str_replace(array('.', ','), '', '\\1') . '.\\4'", $value));
	}

	/**
	* Converts a string with html_entity decode to html and decode quotes as well. Usefull for JS
	*
	* @param string $value Input key
	* @return string
	*/
	public function decode_entity($value){
		return html_entity_decode($value, ENT_QUOTES);
	}
}
?>