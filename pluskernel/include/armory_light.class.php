<?php
/******************************
 * EQDKP PLUGIN: PLUSkernel
 * (c) 2007 by EQDKP Plus Dev Team
 * originally written by S.Wallmann
 * http://www.eqdkp-plus.com   
 * ------------------
 * armory_light.class.php
 * Start: 2006
 * $Id$
 ******************************/
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class ArmoryLight
{
  var $version 	= '2.0.0 alpha XX';
  var $build		= '0811200717';
  
  var $links		= array(
										'eu'		=> 'http://eu.wowarmory.com/',
										'us'		=> 'http://www.wowarmory.com/'
									);
  
  function ArmoryLight($utf8test){
		$this->stringIsUTF8 = ($this->isUTF8($utf8test) == 1) ? true : false; 
	}
  
  /**
  * Build the Armory Link
  * 
  * @param $loc Location of Realm (eu/us)
  * @param $user Character Name
  * @param $server Realm Name  
  * @param $mode char or talent or guild
  * @return string URL
  */
	function BuildLink($loc, $user, $server, $mode='char'){
		$server	= ($this->stringIsUTF8) ? stripslashes(rawurlencode($server)) : stripslashes(rawurlencode(utf8_encode($server)));
		$user 	= ($this->stringIsUTF8)	? stripslashes(rawurlencode($user)) : stripslashes(rawurlencode(utf8_encode($user)));
		if($mode == 'char'){
      $url 		= $this->links[$loc].'character-sheet.xml?r='.$server.'&n='.$user;
    }elseif($mode == 'talent'){
      $url 		= $this->links[$loc].'character-talents.xml?r='.$server.'&n='.$user;
    }else{
      $url 		= $this->links[$loc].'guild-info.xml?r='.$server.'&n='.$user;
    }
    return $url;
	}
  
	/**
 * Returns <kbd>true</kbd> if the string or array of string is encoded in UTF8.
 *
 * Example of use. If you want to know if a file is saved in UTF8 format :
 * <code> $array = file('one file.txt');
 * $isUTF8 = isUTF8($array);
 * if (!$isUTF8) --> we need to apply utf8_encode() to be in UTF8
 * else --> we are in UTF8 :)
 * @param mixed A string, or an array from a file() function.
 * @return boolean
 */
	function isUTF8($string){
    if (is_array($string)){
    	$enc = implode('', $string);
    	return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    }else{
    	return (utf8_encode(utf8_decode($string)) == $string);
    }   
	}
	
	/**
  * Check if the String is UTF8 or not
  * 
  * @return bool
  */
	function CheckUTF8(){
		return $this->stringIsUTF8;
	}
	
	/**
  * Convert the String to UTF8 if needed
  * 
  * @param $string Input
  * @return bool UTF8 encoded string
  */
	function UTF8tify($string){
		if($this->stringIsUTF8 || !$this->XMLIsUTF8){
			return $string;
		}else{
			return utf8_decode($string);
		}
	}
}
?>
