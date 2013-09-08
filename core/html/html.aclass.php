<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2013
 * Date:		$Date: 2013-04-24 10:23:19 +0200 (Mi, 24 Apr 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2013 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13337 $
 * 
 * $Id: super_registry.class.php 13337 2013-04-24 08:23:19Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

abstract class html extends gen_class {
	public static $singleton = false;
	// field type
	protected static $type = '';
	
	abstract public function __construct($name, $options=array());
	
	abstract public function __toString();
	
	abstract public function inpval();
	
	public static function __get($name) {
		if($name == 'type') return self::$type;
		return parent::__get($name);
	}
	
	protected function cleanid($input) {
			if(strpos($input, '[') === false && strpos($input, ']') === false) return $input;
			$out = str_replace(array('[', ']'), array('_', ''), $input);
			return 'clid_'.$out;
	}
}
?>