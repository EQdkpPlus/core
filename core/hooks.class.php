<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

class hooks extends gen_class {

	public static $shortcuts = array();
	public static $dependencies = array('pm');
	
	private $hooks = array();
	
	/*
	 * Register Hook
	 * @string				$strHook  The Hookname
	 * @string				$strClassname	Name of the HookClass where the hook method is implemented
	 * @string				$strMethodname	Name of the Method the Hook is calling
	 * @string				$strCallpath	Path to the $strClassname, without eqdkp_root_path
	 * return @array
	 */
	public function register($strHook, $strClassname, $strMethodname, $strClasspath){
		if (!isset($this->hooks[$strHook])) $this->hooks[$strHook] = array();
		$this->hooks[$strHook][] = array('class'=> $strClassname, 'method'=> $strMethodname, 'classpath'=>$strClasspath);
	}
	
	/*
	 * Process Hook
	 * @string				$strHook  		The Hookname
	 * @array				$arrParams		Param-Array that should be given to the hook method
	 * @boolean				$blnRecursive	If true, each hook method will modify the output-array
	 * return @array
	 */
	public function process($strHook, $arrParams=array(), $blnRecursive=false){
		//Init Plugins and Portal modules for registering hooks
		register('pm');
		register('portal');
		
		if (!isset($this->hooks[$strHook])) return ($blnRecursive) ? $arrParams : array();
		
		$arrOutput = ($blnRecursive) ? $arrParams : array();
		foreach($this->hooks[$strHook] as $hook){
			
			include_once($this->root_path.$hook['classpath'].'/'.$hook['class'].'.class.php');
			$objHookClass = register($hook['class']);
			if ($objHookClass) {
				$strMethodname = $hook['method'];
				if ($blnRecursive){
					$arrOutput = $objHookClass->$strMethodname($arrOutput);
				} else {
					$arrOutput[$hook['class']] = $objHookClass->$strMethodname($arrParams);
				}
			}
		}
		return $arrOutput;
	}
	
}

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_hooks', hooks::$shortcuts);
?>