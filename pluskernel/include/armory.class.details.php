<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

class ArmoryCharLoader
{
	var $version 	= '1.2.0';
	var $build		= '2508200701';
	var $links		= array(
										'eu'		=> 'http://eu.wowarmory.com/',
										'us'		=> 'http://www.wowarmory.com/'
									);
	
	function ArmoryCharLoader($utf8test)
	{
		$this->stringIsUTF8 = ($this->isUTF8($utf8test) == 1) ? true : false;
	}
	
	function GetArmoryData($user, $realm, $loc='us', $lang)
	{
		$wowurl = $this->BuildLink($loc, stripslashes($user), stripslashes($realm));
		$xmldata = $this->read_url($wowurl, $lang);
		return $xmldata;
	}
	
	// Converts Date to Timestamp
	function Date2Timestamp($armdate)
	{
    	return strtotime(trim($armdate));
  	}
	
	function BuildLink($loc, $user, $server, $mode='char')
	{
		$server = ($this->isUTF8) ? stripslashes(rawurlencode($server)) : stripslashes(rawurlencode(utf8_encode($server)));
		$user = ($this->isUTF8) ? stripslashes(rawurlencode($user)) : stripslashes(rawurlencode(utf8_encode($user)));
		
		if($mode == 'char')
		{
			$url = $this->links[$loc].'character-sheet.xml?r='.$server.'&n='.$user;
		}elseif($mode == 'talent')
		{
			$url = $this->links[$loc].'character-talents.xml?r='.$server.'&n='.$user;
		}else
		{
			$url = $this->links[$loc].'guild-info.xml?r='.$server.'&n='.$user;
		}
		return $url;
	}

	// get the XML
	function read_url($url, $lang='en')
	{
		
		// Try cURL first. If that isnt available, check if we're allowed to
		// use fopen on URLs.  If that doesn't work, just die.
		if (function_exists('curl_init'))
		{
			$curl = @curl_init($url);
			$useragent="Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2";
	
			@curl_setopt($curl, CURLOPT_USERAGENT, $useragent);
			@curl_setopt($curl, CURLOPT_TIMEOUT, 10);
			if (!(@ini_get("safe_mode") || @ini_get("open_basedir"))) 
			{
	    		@curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
	    	}
			@curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
	
			$xml_data = @curl_exec($curl);
			curl_close($curl);
		}elseif (ini_get('allow_url_fopen') == 1)
		{
			// set the useragent first. if not, you'll get the source....
			if(function_exists('ini_set'))
			{
				@ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1) Gecko/20061010 Firefox/2.0');
				$xml_data= @file_get_contents($url);
			}else
			{
				die("'ini_set' is not allowed. The script cannot set the user agent, it won't work on your host");
			}			
		}else
		{
		    // Thanks to Aki Uusitalo
		    // set the useragent first. if not, you'll get the source....
		    if(function_exists('ini_set'))
		    {
				@ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1) Gecko/20061010 Firefox/2.0');
			}else
			{
				$_SERVER["HTTP_USER_AGENT"] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1) Gecko/20061010 Firefox/2.0';
			}
				$url_array = parse_url($url);
				$fp = fsockopen($url_array['host'], 80, $errno, $errstr, 5); 
				if (!$fp)
				{
					#die("cURL isn't installed, 'allow_url_fopen' isn't set and socket opening failed. Socket failed because: <br /><br /> $errstr ($errno)");
				}else
				{
					$out = "GET " . $url_array[path] . "?" . $url_array[query] ." HTTP/1.0\r\n";
					$out .= "Host: " . $url_array[host] . " \r\n";
					$out .= "Connection: Close\r\n\r\n";
	
					fwrite($fp, $out);
	
					// Get rid of the HTTP headers
					while ($fp && !feof($fp))
					{
						$headerbuffer = fgets($fp, 1024);
						if (urlencode($headerbuffer) == "%0D%0A")
						{
	                    	// We've reached the end of the headers
							break;
						}
					}
	
					$xml_data = '';
					// Read the raw data from the socket in 1kb chunks
					while (!feof($fp))
					{
						$xml_data .= fgets($fp, 1024);
					}
					fclose($fp);
				}        	    
		}

		return $xml_data;
	}

	function isUTF8($string)
	{
    	if (is_array($string))
    	{
    		$enc = implode('', $string);
    		return @!((ord($enc[0]) != 239) && (ord($enc[1]) != 187) && (ord($enc[2]) != 191));
    	}else
    	{
    		return (utf8_encode(utf8_decode($string)) == $string);
    	}   
	}
	
	function CheckUTF8()
	{
		return $this->stringIsUTF8;
	}
	
	function UTF8tify($string)
	{
		if($this->stringIsUTF8)
		{
			return $string;
		}else{
			return utf8_decode($string);
		}
	}

}
?>
