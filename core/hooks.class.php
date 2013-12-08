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
	public function register($strHook, $strClassname, $strMethodname, $strClasspath, $arrClassparams=array()){
		if (!isset($this->hooks[$strHook])) $this->hooks[$strHook] = array();
		$strHookHash = md5($strClassname.$strMethodname.$strClasspath.serialize($arrClassparams));
		if (!isset($this->hooks[$strHook][$strHookHash])) {
			$this->hooks[$strHook][$strHookHash] = array('class'=> $strClassname, 'method'=> $strMethodname, 'classpath'=>$strClasspath, 'class_params'=>$arrClassparams);
		}
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
		//Register global hooks
		$this->scanGlobalHookFolder();
		
		if (!isset($this->hooks[$strHook])) return ($blnRecursive) ? $arrParams : array();
		
		$arrOutput = ($blnRecursive) ? $arrParams : array();
		foreach($this->hooks[$strHook] as $hook_data){
			
			include_once($this->root_path.$hook_data['classpath'].'/'.$hook_data['class'].'.class.php');
			if(empty($hook_data['class_params'])) $hook_data['class_params'] = array();
			$objHookClass = register($hook_data['class'], $hook_data['class_params']);
			if ($objHookClass) {
				$strMethodname = $hook_data['method'];
				if ($blnRecursive){
					$arrOutput = $objHookClass->$strMethodname($arrOutput);
				} else {
					$arrOutput[$hook_data['class']] = $objHookClass->$strMethodname($arrParams);
				}
			}
		}
		return $arrOutput;
	}
	
	private function scanGlobalHookFolder(){
		if($dir = @opendir($this->root_path . 'core/hooks/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/hooks/' . $file)) && valid_folder($file)){
					$path_parts = pathinfo($file);
					$filename = str_replace("_hook", "", $path_parts['filename']);
					$filename= str_replace(".class", "", $filename);
					
					$strHook = substr($filename, strrpos($filename, '_')+1);
					$strClassname = str_replace(".class", "", $path_parts['filename']);
					$strMethodname = $strHook;
					$strClasspath ='core/hooks';
					$this->register($strHook, $strClassname, $strMethodname, $strClasspath);
				}
			}
		}
	}
	
}
?>