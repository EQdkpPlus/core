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

	class xmltools extends gen_class
	{

		// Prepare an xml string to save
		public function prepareSave($xml){
			$xml = addslashes($xml);
			$xml = @base64_encode(gzcompress(serialize($xml)));
			return $xml;
		}

		// Prepare an xml string to load
		public function prepareLoad($xml){
			$xml = @base64_decode($xml);
			$xml = @gzuncompress($xml);
			$xml = @unserialize($xml);
			$xml = stripslashes($xml);
			return $xml;
		}

		// Converts an Array to an serialized XML object to be saved
		// in a MySQL database
		public function Array2Database($array, $name="config" ,&$xml=null ){
			$mysxml = $this->array2simplexml($array, $name);
			return serialize($mysxml->asXML());
		}

		// Convert a serialized XML object back to an array
		public function Database2Array($fieldname){
			$unser_fieldname	= ($this->isSerialized($fieldname)) ? @unserialize($fieldname) : $fieldname;
			$xml_obj			= simplexml_load_string($unser_fieldname);
			return $this->simplexml2array($xml_obj);
		}

		// Array to SimpleXML converter
		public function array2simplexml($array, $name="config" ,&$xml=null ){
			if(is_null($xml)){
				$xml = new SimpleXMLElement("<{$name}/>");
			}

			foreach($array as $key => $value){
				if($key === '@attributes'){
					if(is_array($value)){
						foreach($value as $name => $val){
							$xml->addAttribute($this->handleKey($name), $val);
						}
					}
				}else{
					if(is_array($value)){
						$xml->addChild($this->handleKey($key));
						$key_ = $this->handleKey($key);
						$this->array2simplexml($value, $name, $this->get_latest_key($xml->$key_));
					}else{
						$xml->addChild($this->handleKey($key), $value);
					}
				}
			}
			return $xml;
		}

		public function get_latest_key($xml){
			foreach ($xml as $value){
				$out = $value;
			}
			return $out;
		}

		// SimpleXML to Array Converter
		public function simplexml2array($knoten, $type = false){
			$xmlArray = array();
			if(is_object($knoten)){
				settype($knoten,'array') ;
			}
			if(is_array($knoten)){
				foreach($knoten as $key=>$value){
					if(is_array($value)||is_object($value)){
						$xmlArray[$key] = $this->simplexml2array($value, $type);
					}else{
						if($type == true){
							if(is_numeric($value)){
								$value = 0+$value;
							}else{
								if($value == "true"){
									$value = true;
								}else if($parameter == "false"){
									$value = false;
								}
							}
						}
						$xmlArray[$key] = $value;
					}
				}
			}
			return $xmlArray;
		}

		public function handleKey($key){
			if (is_numeric($key)){
				return 'i'.$key;
			} elseif(strpos($key, ':') !== false) {
				return substr($key, 0, strpos($key, ':'));
			}
			return $key;
		}

		// Check if the file is serialized
		public function isSerialized($str) {
			return ($str == serialize(false) || @unserialize($str) !== false);
		}

		// Strip invalid chars for XML
		public function stripInvalidXml($value){
			$ret = "";
			$current;
			if (empty($value)){
				return $ret;
			}

			$length = strlen($value);
			for ($i=0; $i < $length; $i++){
				$current = ord($value{$i});
				if	(($current == 0x9) || ($current == 0xA) || ($current == 0xD) || (($current >= 0x20) && ($current <= 0xD7FF)) || (($current >= 0xE000) && ($current <= 0xFFFD)) || (($current >= 0x10000) && ($current <= 0x10FFFF))){
					$ret .= chr($current);
				}else{
					$ret .= " ";
				}
			}
			return $ret;
		}
	}
?>