<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       11.01.2008
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


if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

/**
 * URL Reader Class written by Stefan "Corgan" Knaak
 * thanks to Wallenium for parts of the import code
 * try to get an URL with curl, fsockopen, filegetcontent
 */
class urlreader
{

	var $isuser_agent 		= 'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.1.2) Gecko/20070220 Firefox/2.0.0.2';  # Set Useragent
	var $curl_timeout 		= 10; 		#Timeout for curl
	var $checkurltimeout 	= 5;  		#Timeout for Url check
	var $checkURL_first 	= false;	#Should we check the server first, bevor we try to get the url


	/**
	 * Cunstrutor
	 *
	 * @return urlreader
	 */
	function urlreader()
	{

	}

	/**
	 * Return the Data
	 * Checks all given methods to get the date from the url
	 *
	 * @param String $geturl
	 * @return string
	 */
	function GetURL($geturl)
	{
		$ret_val = null;

		//checl if accessible
		if ($this->checkURL_first)
		{
			//if not return null
			if (!$this->Check_Link($geturl))
			{
				return $ret_val;
			}
		}

		//lets try first with curl
		$ret_val = $this->Get_CURL($geturl);

		if (!$ret_val)
		{
			//no curl, ok lets try with file_get_contents
			$ret_val = $this->Get_file_get_contents($geturl);
		}

		if (!$ret_val)
		{
			//no curl, ok lets try with fsockopen
			$ret_val = $this->Get_fsockopen($geturl);
		}

		return $ret_val;
	}


	/**
	 * Try to get the data from the URL via the curl function
	 *
	 * @param string $geturl
	 * @return string
	 */
	function Get_CURL($geturl)
	{
		$getdata = null;
		if($this->Check_CURL())
		{ // curl
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_URL, $geturl);
			@curl_setopt($ch, CURLOPT_TIMEOUT, $this->curl_timeout);
    		@curl_setopt($ch, CURLOPT_USERAGENT, $this->isuser_agent);
		    if (!(@ini_get("safe_mode") || @ini_get("open_basedir")))
		    {
		      @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		    }

			$getdata = @curl_exec($ch);
			curl_close($ch);
		}
		return $getdata;
	}

	/**
	 * Try to get the data from the URL via the file_get_contents function
	 *
	 * @param string $geturl
	 * @return string
	 */
	function Get_file_get_contents($geturl)
	{
		$getdata = null;
		if ($this->Check_file_get_contents())
		{
			$getdata = @file_get_contents($geturl);
		}

		return $getdata;
	}


	/**
	 * Try to get the data from the URL via the file() function
	 *
	 * @param string $geturl
	 * @return string
	 */
	function Get_file_($geturl)
	{
		$getdata = null;

		$pparray = file ($geturl);
		if (is_array($pparray))
		{
			$getdata = $pparray[0];
		}

		return $getdata;
	}






	/**
	 * Try to get the data from the URL via the fsockopen function
	 *
	 * @param string $geturl
	 * @return string
	 */
	function Get_fsockopen($geturl)
	{
		if(function_exists('ini_set'))
		{
			@ini_set('user_agent', 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1) Gecko/20061010 Firefox/2.0');
		}else
		{
			$_SERVER["HTTP_USER_AGENT"] = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; fr; rv:1.8.1) Gecko/20061010 Firefox/2.0';
		}
		$url_array = parse_url($geturl);
		$fp = fsockopen($url_array['host'], 80, $errno, $errstr, 5);

		if ($fp)
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
			$getdata = '';
			// Read the raw data from the socket in 1kb chunks
			while (!feof($fp))
			{
				$getdata .= fgets($fp, 1024);
			}
			fclose($fp);
		}

		return $getdata;
	}	# end function


	/**
	 * Check_Link()
	 * Check if the Host of the given URL is accessible
	 *
	 * @param String $url
	 * @return Boolean
	 */
	function Check_Link($url)
	{

	  	if($url)
	  	{
	  		$_url = $this->parse_url_compat($url,PHP_URL_HOST);
	  		$dat = @fsockopen($_url, 80, $errno, $errstr, $this->checkurltimeout);
	  	}
	  	if($dat)
	  	{
	  		return true;

	    	fclose($dat);
	  	} else
	  	{
	    	return false;
	  	}
	}

	/**
	 * Check if curl installed
	 *
	 * @return boolean
	 */
	function Check_CURL()
	{
		if (function_exists('curl_init')) {
			return true;
		}else {
			return false;
		}
	}

	/**
	 * Check if file_get_content installed
	 *
	 * @return boolean
	 */
	function Check_file_get_contents()
	{
		if (function_exists('file_get_contents')) {
			return true;
		}else {
			return false;
		}
	}


	/**
	 * function to add the $component option in for PHP4.
	 *
	 * @param STRING $url
	 * @param STRING $component
	 * @return STRING URL
	 */
	function parse_url_compat($url, $component=NULL)
	{

	    ## Defines only available in PHP 5, created for PHP4
	    if(!defined('PHP_URL_SCHEME')) define('PHP_URL_SCHEME', 1);
	    if(!defined('PHP_URL_HOST')) define('PHP_URL_HOST', 2);
	    if(!defined('PHP_URL_PORT')) define('PHP_URL_PORT', 3);
	    if(!defined('PHP_URL_USER')) define('PHP_URL_USER', 4);
	    if(!defined('PHP_URL_PASS')) define('PHP_URL_PASS', 5);
	    if(!defined('PHP_URL_PATH')) define('PHP_URL_PATH', 6);
	    if(!defined('PHP_URL_QUERY')) define('PHP_URL_QUERY', 7);
	    if(!defined('PHP_URL_FRAGMENT')) define('PHP_URL_FRAGMENT', 8);

        if(!$component) return parse_url($url);

        ## PHP 5
        if(phpversion() >= 5)
            return parse_url($url, $component);

        ## PHP 4
        $bits = parse_url($url);

        switch($component)
        {
            case 'PHP_URL_SCHEME'	: return $bits['scheme'];
            case 'PHP_URL_HOST'		: return $bits['host'];
            case 'PHP_URL_PORT'		: return $bits['port'];
            case 'PHP_URL_USER'		: return $bits['user'];
            case 'PHP_URL_PASS'		: return $bits['pass'];
            case 'PHP_URL_PATH'		: return $bits['path'];
            case 'PHP_URL_QUERY'	: return $bits['query'];
            case 'PHP_URL_FRAGMENT'	: return $bits['fragment'];
        }
    }


}#end class