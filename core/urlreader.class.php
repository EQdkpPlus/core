<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2010
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2010 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

class urlreader
{
	private $user_agent				= 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2';		// User Agent
	private $timeout				= array(
										'curl'			=> 10,		// Timeout for curl
										'url_check'	=> 5,			// Timeout for Url check
									); 				
	public $checkURL_first 			= false;						// Should we check the server first, bevor we try to get the url
	private $fetch_method			= '';
	
	/**
	 * Return the Data
	 * Checks all given methods to get the date from the url
	 *
	 * @param String $geturl
	 * @return string
	 */
	public function GetURL($geturl, $language='en_en')
	{
		$ret_val = null;
		
		// Check the language and bring it to the format "de_de"
		$language = (strlen($language) == 5) ? $language : $language.'_'.$language;
		
		//check if accessible, if it is not available, return NULL
		if ($this->checkURL_first)
		{
			if (!$this->Check_Link($geturl))
			{
				return $ret_val;
			}
		}
		
		/*****************************
		 * FETCH DATA
		 *****************************/
		$ret_val = $this->Get_CURL($geturl, $language);										// First attempt: Curl

		if (!$ret_val){
			$ret_val = $this->Get_file_get_contents($geturl, $language);		// No Data with Curl, try Get_File..
		}

		if (!$ret_val){
			$ret_val = $this->Get_fsockopen($geturl, $language);						// No Data with Curl or Get_File.., lets try fsockopen
		}
		
		if (!$ret_val){
			$ret_val = 'ERROR';																							// No method worked, return Error!
		}
		
		return $ret_val;
	}
	
	public function get_method(){
		return $this->fetch_method;
	}

	/**
	 * Try to get the data from the URL via the curl function
	 *
	 * @param string $geturl
	 * @return string
	 */
	private function Get_CURL($geturl, $language)
	{
		$getdata = null;
		if($this->Check_Method('curl'))
		{
			$ch = @curl_init($geturl);
			@curl_setopt($ch, CURLOPT_COOKIE, "cookieLangId=".$language.";");
  		@curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
  		@curl_setopt($ch, CURLOPT_TIMEOUT, $this-> timeout['curl']);
  		if (!(@ini_get("safe_mode") || @ini_get("open_basedir"))) {
      	@curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
      }
  		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			$getdata = @curl_exec($ch);
			curl_close($ch);
		}
		$this->fetch_method = 'curl';
		return $getdata;
	}

	/**
	 * Try to get the data from the URL via the file_get_contents function
	 *
	 * @param string $geturl
	 * @return string
	 */
	private function Get_file_get_contents($geturl, $language)
	{	
		$getdata = null;
		if ($this->Check_Method('fgetcontents'))
		{
			// set the useragent first. if not, you'll get the source....
			if(function_exists('ini_set')){
				@ini_set('user_agent', $this->user_agent);
			}else{
				$_SERVER["HTTP_USER_AGENT"] = $this->user_agent;
			}
		
			// its a bit tricky to get the cookie to work: http://www.testticker.de/tipps/article20060414003.aspx
			$opts = array (
                  'http'=>array (
                  'method' => 'GET',
                  'header' => "Cookie: cookieLangId=".$language.";\r\n"
                    )
      				);
      $context = @stream_context_create($opts);
			$getdata = @file_get_contents($geturl, false, $context);
		}
		$this->fetch_method = 'file_gets';
		return $getdata;
	}
	
	/**
	 * Try to get the data from the URL via the fsockopen function
	 *
	 * @param string $geturl
	 * @return string
	 */
	private function Get_fsockopen($geturl, $language)
	{
		$cheader   = array("http" => array ("header" => "Cookie: cookieLangId=".$language.";\r\n"));
		$url_array = parse_url($geturl);
		$fp = fsockopen($url_array['host'], 80, $errno, $errstr, 5);

		if ($fp)
		{
			$out  = "GET " .$url_array['path']."?".$url_array['query']." HTTP/1.0\r\n";
			$out .= "Host: ".$url_array['host']." \r\n";
			$out .= "User-Agent: ".$this->user_agent;
			$out .= "Connection: Close\r\n";
			$out .= "Cookie: ".$cookie."\r\n";
			$out .= "\r\n";
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
			$getdata = '';
			// Read the raw data from the socket in 1kb chunks
			while (!feof($fp))
			{
				$getdata .= fgets($fp, 1024);
			}
			fclose($fp);
		}
		$this->fetch_method = 'fopen';
		return $getdata;
	}
	
	public function Check_Method($method)
	{
		switch($method){
			case 'curl':					$func_ex = 'curl_init';					break;
			case 'fgetcontents':	$func_ex = 'file_get_contents';	break;
			default: $func_ex =$method;
		}
		return (function_exists($func_ex)) ? true : false;
	}
	
	/**
	 * Check_Link()
	 * Check if the Host of the given URL is accessible
	 *
	 * @param String $url
	 * @return Boolean
	 */
	private function Check_Link($url)
	{
		if($url)
		{
			$_url = parse_url($url,PHP_URL_HOST);
			$dat = @fsockopen($_url, 80, $errno, $errstr, $this-> timeout['url_check']);
		}

		if($dat)
		{
			return true;
			fclose($dat);
		} else {
			return false;
		}
	}
}