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
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');
	exit;
}

class Input
{
	var $_cache		= array();
	var $_caching	= true;

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
			case 'float':			$out = FILTER_SANITIZE_NUMBER_FLOAT;	break;
			case 'double':			$out = FILTER_SANITIZE_NUMBER_FLOAT;	break;
			case 'int':				$out = FILTER_SANITIZE_NUMBER_INT;		break;
			case 'integer':			$out = FILTER_SANITIZE_NUMBER_INT;		break;
			case 'string':			$out = FILTER_SANITIZE_STRING;			break;
			case 'htmlescape':		$out = FILTER_SANITIZE_SPECIAL_CHARS;	break;
			default:				$out = FILTER_SANITIZE_STRING;
		}
		return $out;
	}

	private function _options($filter){
		switch($filter){
			case FILTER_SANITIZE_NUMBER_FLOAT:	$out = FILTER_FLAG_ALLOW_FRACTION;	break;
			default:							$out = 't';
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
		eval("\$get = \$_GET".$allkey.";");
		eval("\$post = \$_POST".$allkey.";");
		eval("\$session = \$_SESSION".$allkey.";");
		eval("\$cookie = \$_COOKIE".$allkey.";");
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
		if(is_array($this->_caching) && isset($this->_cache[$key])){
			$out = $this->_cache[$key];
		}else{
			if(strpos($key,':')) {
				$filter = (($owntype) ? $this->_type($owntype, true) : $this->_type($default));
				$out		= filter_var($this->_get_deep(explode(':', $key),$default), $filter, $this->_options($filter));
			} else {
				$filter	= (($owntype) ? $this->_type($owntype, true) : $this->_type($default));
				$out		= filter_input($this->_superglobal($key), $key, $filter, $this->_options($filter));
				$out		= (!$out) ? $default : $out;
			}
			$this->_cache[$key] = $out;
		}
		return $out;
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
			$checkarr = $this->_get_deep(explode(':', $key), $type);
		} else {
			$checkarr = is_array($key) ? $_POST : $_POST[$key];
		}
		$valarr		= is_array($type) ? $type : $this->_type($type, true, true);
		return (!$checkarr) ? array() : filter_var_array($checkarr, $valarr);
	}

	/**
	* Checks if $key exists as an input value.
	*
	* @param string $key Input key
	* @return boolean
	*/
	function exists($key, $type=false){
		$retval = false;
		if($type){
			if(is_array($this->getArray($key, $type))){
				$retval = true;
			}
		}else{
			if($this->get($key)){
				$retval = true;
			}
		}
		return $retval;
	}
}

?>