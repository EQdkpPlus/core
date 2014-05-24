<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2012-02-17 21:01:28 +0100 (Fr, 17 Feb 2012) $
 * -----------------------------------------------------------------------
 * @author		$Author: Godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11690 $
 * 
 * $Id: plus_exchange.class.php 11690 2012-02-17 20:01:28Z Godmod $
 */

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if( !class_exists( "LuaParser" ) ) {
	class LuaParser{
		
		private $blnOneTable = true;
		
		public function __construct($blnOneTable=true){
			$this->blnOneTable = $blnOneTable;
		}
		
		public function array2lua($arrData){
			if (!is_array($arrData)) return false;
				
			if ($this->blnOneTable) {
				$out = "response = {";
			}else $out = "";
			
			foreach($arrData as $key => $value){
				if ($this->blnOneTable) {
					if (is_array($value)){
						$val = $this->_parseArray2lua($value);
						$out .= '['.$this->_parseKey($key).'] = {'.$val.'},';
					} else $out .= '["'.$key.'"] = "'.$value.'",';
					
				} else {
					if (is_array($value)){
						$val = $this->_parseArray2lua($value);
						$out .= $key.' = {'.$val.'}'."\n";
					} else $out .= $key.' = {['.$this->_parseKey($key).'] = "'.$value.'"}'."\n";
				}
			}
			
			if ($this->blnOneTable) {
				$out = substr($out, 0, -1);
				$out .= "}";
			}
			return $out;
		}
		
		private function _parseArray2lua($arrData){
			foreach($arrData as $key => $value){
				if (is_array($value)){
					$val = $this->_parseArray2lua($value);
					$out .= '['.$this->_parseKey($key).'] = {'.$val.'},';
				} else $out .= '['.$this->_parseKey($key).'] = "'.$value.'",';
			}
			$out = substr($out, 0, -1);
			return $out;
		}
		
		private function _parseKey($strKey){
			$output_array = array();
			if(preg_match("/(.*):([0-9]*)/", $strKey, $output_array)){
				return $output_array[2];
			};
			return '"'.$strKey.'"';
		}
	}
}
