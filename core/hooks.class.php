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

class hooks extends gen_class {

	public static $shortcuts = array();
	public static $dependencies = array('pm');
	
	private $hooks = array();
	private $blnScanned = false;
	
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
	
	public function isRegistered($strHookname){
		if (!$this->blnScanned){
			//Init Plugins and Portal modules for registering hooks
			register('pm');
			register('portal');
			//Register global hooks
			$this->scanGlobalHookFolder();
			$this->blnScanned = true;
		}
		
		if (isset($this->hooks[$strHookname])) return true;
		
		return false;
	}
	
	private function scanGlobalHookFolder(){
		if($dir = @opendir($this->root_path . 'core/hooks/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/hooks/' . $file)) && valid_folder($file)){
					$path_parts = pathinfo($file);
					$filename = str_replace("_hook", "", $path_parts['filename']);
					$filename= str_replace(".class", "", $filename);
					$start = strpos($filename, '_');
					
					
					$strHook = substr($filename, $start+1);
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