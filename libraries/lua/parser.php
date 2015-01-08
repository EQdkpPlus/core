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
		
		
		/**
		 * LUA data table parser
		 *
		 * This file contains the code that parses LUA data tables into a PHP array.
		 * Mostly used when extracting information from World of Warcraft addons.
		 *
		 * @author David Stangeby <david.stangeby@gmail.com>
		 * @version 0.1
		 * @package WLP
		 * @link http://fin.instinct.org/lua/ The code this class was based of
		 * @copyright  Copyright (c) 2005-2009 Arctic Solutions (http://www.arcticsolutions.no/)
		 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.txt GNU General Public License v2
		 */
		
		
		/**
		 * The variable contraining the lua lines to be parsed
		 * @var array
		 */
		protected $lua = array();
		 
		/**
		 * The current possition in the array we are parsing
		 * @var integer
		*/
		protected $position = 0;
		 
		/**
		 * The size of the lua array
		 * @var integer
		 */
		protected $lines = 0;
		 
		/**
		 * Array containing the result of the parse
		 * @var array
		 */
		protected $data = array();
		 
		 
		/**
		 * Returns the array the parsed from the lua file.
		 *
		 * @return array The resulting array from the parse
		 */
		public function toArray($input)
		{
			if(is_array($input)) {
				$this->lua = $input;
			} elseif(is_string($input)) {
				if(is_file($input)) {
					$this->lua = file($input);
				} else {
					$this->lua = explode("\n", $input);
				}
			}
			if(is_array($this->lua)) {
				$this->lines = count($this->lua);
			}
			// The array should be bigger than 1 line, else we have probably gotten
			// a invalid file as input.
			if($this->lines <= 1) {
				throw new Exception('Input did not validate as array');
			}
			$this->parse();
			
			return $this->data;
		}
		 
		/**
		 * Starts the parsing of the lua array.
		 *
		 * @return void
		 */
		protected function parse()
		{
			$this->data = $this->parser();
			// Clear the array containing the lua data file to save memory usage.
			unset($this->lua);
		}
		 
		/**
		 * Does the actually parsing of the lua table.
		 *
		 * @return void
		 */
		protected function parser(&$position = false)
		{
			if($position == false) {
				$position = &$this->position;
			}
			$data = array();
			$stop = false;
			if ($position < $this->lines) {
				for ($i = $position; $stop == false;) {
					if ($i >= $this->lines) {
						$stop = true;
						break;
					}
					 
					$strs = explode("=", utf8_decode($this->lua[$i]));
					 
					if (isset($strs[1]) && trim($strs[1]) == "{") {
						$i++;
						$data[$this->arrayId(trim($strs[0]))] = $this->parser($i);
					} elseif (trim($strs[0]) == "}" || trim($strs[0]) == "},") {
						$i++;
						$stop = true;
					} else {
						$i++;
						if (strlen($this->arrayId(trim($strs[0]))) > 0 && strlen($strs[1]) > 0) {
							$data[$this->arrayId(trim($strs[0]))] = $this->trimValue($strs[1]);
						}
					}
				}
			}
			$position = $i;
			return $data;
		}
		 
		/**
		 * Trims of leading and trailing quotationmarks and tailing comman from
		 * the value.
		 *
		 * Example:
		 *  Input: "Value",
		 *  Output: Value
		 *
		 * @param string $string String to be trimmed
		 * @return string Trimmed string
		 */
		protected function trimValue($string)
		{
			$string = trim($string);
			if (substr($string,0,1)=="\"") {
				$string = trim(substr($string,1,strlen($string)));
			}
			if (substr($string,-1,1)==",") {
				$string = trim(substr($string,0,strlen($string)-1));
			}
			if (substr($string,-1,1)=="\"") {
				$string = trim(substr($string,0,strlen($string)-1));
			}
			if ($string =='false') {
				$string = false;
			}
			if ($string =='true') {
				$string = true;
			}
			 
			return $string;
		}
		 
		/**
		 * Extracts the Key-Value for array indexing.
		 *
		 * String Example:
		 *  Input: ["Key"]
		 *  Output: Key
		 * Integer Example:
		 *  Input: [0]
		 *  Output: 0
		 *
		 *  @param string $string String to extract array index from
		 *  @return string Array index
		 */
		protected function arrayId($string)
		{
			$id = sscanf($string, "[%d]");
			if (strlen($id[0])>0) {
				return $id[0];
			} else {
				if (substr($string,0,1)=="[") {
					$string  = substr($string,1,strlen($string));
				}
				if (substr($string,0,1)=="\"") {
					$string  = substr($string,1,strlen($string));
				}
				if (substr($string,-1,1)=="]") {
					$string  = substr($string,0,strlen($string)-1);
				}
				if (substr($string,-1,1)=="\"") {
					$string  = substr($string,0,strlen($string)-1);
				}
				return $string;
			}
		}
		
		
		
	}
}
