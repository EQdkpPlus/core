<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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

class urlfetcher  extends gen_class {
	public static $shortcuts = array();

	private $useragent			= '';		// User Agent
	private $timeout			= 15;											// Timeout
	private $conn_timeout		= 5;											// Connection Timeout
	private $methods			= array('curl','file_gets','fopen');			// available function methods
	private $method				= '';											// the selected method

	public function get_method(){
		return $this->method;
	}

	public function __construct($method=false){
		$this->useragent = 'EQDKP-PLUS ('.EQDKP_PROJECT_URL.')';
		if($method){
			$this->method = $method;
		}else{
			foreach($this->methods as $methods){
				if(!$this->check_function($methods)){
					continue;
				}
				$this->method = $methods;
				break;
			}
		}
	}

	/**
	 * Return the Data
	 * Checks all given methods to get the date from the url
	 *
	 * @param String $geturl
	 * @return string
	 */
	public function fetch($geturl, $header='', $conn_timeout = false, $timeout = false){
		$this->method = ($this->method) ? $this->method : 'fopen';
		if (!$conn_timeout) $conn_timeout = $this->conn_timeout;
		if (!$timeout) $timeout = $this->timeout;
		return $this->{'get_'.$this->method}($geturl, $header, $conn_timeout, $timeout);
	}
	
	public function post($url, $data, $content_type = "text/html; charset=utf-8", $header='', $conn_timeout = false, $timeout = false){
		$this->method = ($this->method) ? $this->method : 'fopen';
		if (!$conn_timeout) $conn_timeout = $this->conn_timeout;
		if (!$timeout) $timeout = $this->timeout;
		return $this->{'post_'.$this->method}($url, $data, $content_type, $header, $conn_timeout, $timeout);
	}

	/**
	 * Try to get the data from the URL via the curl function
	 *
	 * @param string $geturl
	 * @return string
	 */
	private function get_curl($geturl, $header, $conn_timeout, $timeout){
		$curlOptions = array(
			CURLOPT_URL				=> $geturl,
			CURLOPT_USERAGENT		=> $this->useragent,
			CURLOPT_TIMEOUT			=> $timeout,
			CURLOPT_CONNECTTIMEOUT	=> $conn_timeout,
			CURLOPT_ENCODING		=> "gzip",
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYHOST	=> false,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_VERBOSE			=> false,
			CURLOPT_FOLLOWLOCATION	=> true,
			CURLOPT_HTTPAUTH		=> CURLAUTH_ANY,
			CURLOPT_HTTPHEADER		=> ((is_array($header) && count($header) > 0) ? $header : array())
		);
		if (!(@ini_get("safe_mode") || @ini_get("open_basedir"))) {
			$curlOptions[CURLOPT_FOLLOWLOCATION] = true;
		}
		$curl = curl_init();
		curl_setopt_array($curl, $curlOptions);
		$getdata = curl_exec($curl);
		curl_close($curl);
		return $getdata;
	}
	
	private function post_curl($url, $data, $content_type, $header, $conn_timeout, $timeout){
		if (is_array($header) && count($header) > 0){
			$header[] = "Content-type: ".$content_type;
			$header[] = "Content-Length: ".strlen($data);
		} else {
			$header = array();
			$header[] = "Content-type: ".$content_type;
			$header[] = "Content-Length: ".strlen($data);
		}
		
		$curlOptions = array(
			CURLOPT_URL				=> $url,
			CURLOPT_USERAGENT		=> $this->useragent,
			CURLOPT_TIMEOUT			=> $timeout,
			CURLOPT_CONNECTTIMEOUT	=> $conn_timeout,
			CURLOPT_ENCODING		=> "gzip",
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_SSL_VERIFYHOST	=> false,
			CURLOPT_SSL_VERIFYPEER	=> false,
			CURLOPT_VERBOSE			=> false,
			CURLOPT_HTTPAUTH		=> CURLAUTH_ANY,
			CURLOPT_HTTPHEADER		=> $header,
			CURLOPT_POST			=> 1,
			CURLOPT_POSTFIELDS		=> $data,
			CURLOPT_FOLLOWLOCATION	=> true,
		);
		if (!(@ini_get("safe_mode") || @ini_get("open_basedir"))) {
			$curlOptions[CURLOPT_FOLLOWLOCATION] = true;
		}
		$curl = curl_init();
		curl_setopt_array($curl, $curlOptions);
		$getdata = curl_exec($curl);
		curl_close($curl);
		return trim($getdata);
	}

	/**
	 * Try to get the data from the URL via the file_get_contents function
	 *
	 * @param string $geturl
	 * @return string
	 */
	private function get_file_gets($geturl, $header, $conn_timeout, $timeout){
		// set the useragent first. if not, you'll get the source....
		if(function_exists('ini_set')){
			@ini_set('user_agent', $this->useragent);
		}else{
			$_SERVER["HTTP_USER_AGENT"] = $this->useragent;
		}

		$opts = array (
			'http'	=>array (
				'method'	=> 'GET',
				'header'	=> ((is_array($header) && count($header) > 0) ? implode("\r\n", $header): ''),
				'timeout'	=> $conn_timeout,
				'user_agent'=>  $this->useragent,
			)
		);
		$context	= @stream_context_create($opts);
		$getdata	= @file_get_contents($geturl, false, $context);
		return $getdata;
	}
	
	private function post_file_gets($url, $data, $content_type, $header, $conn_timeout, $timeout){
		$header = "Content-type: ".$content_type."\r\n"
                . "Content-Length: " . strlen($data) . "\r\n";
		$header .= ((is_array($header) && count($header) > 0) ? implode("\r\n", $header): '');
	
		$opts = array (
			'http'	=>array (
				'method'	=> 'POST',
				'header'	=> $header,
				'timeout'	=> $conn_timeout,
				'user_agent'=> $this->useragent,
				'content'	=> $data,
			)
		);
		
		$context	= @stream_context_create($opts);
		var_dump($context);
		$getdata	= @file_get_contents($url, false, $context);
		return $getdata;
	
	}

	/**
	 * Try to get the data from the URL via the fsockopen function
	 *
	 * @param string $geturl
	 * @return string
	 */
	private function get_fopen($geturl, $header, $conn_timeout, $timeout){
		$url_array	= parse_url($geturl);
		$port = ($url_array['scheme'] == 'https') ? 443 : 80;
		$fsock_host = ($url_array['scheme'] == 'https') ? 'ssl://'.$url_array['host'] : $url_array['host'];
		
		$getdata = '';
		if (isset($url_array['host']) AND $fp = @fsockopen($fsock_host, $port, $errno, $errstr, $conn_timeout)){
			$out	 = "GET " .$url_array['path']."?".((isset($url_array['query'])) ? $url_array['query'] : '')." HTTP/1.0\r\n";
			$out	.= "Host: ".$url_array['host']." \r\n";
			$out	.= "User-Agent: ".$this->useragent."\r\n";
			$out	.= "Connection: Close\r\n";
			$out	.= ((is_array($header) && count($header) > 0) ? implode("\r\n", $header): '');
			$out	.= "\r\n";
			fwrite($fp, $out);

			// Get rid of the HTTP headers
			while ($fp && !feof($fp)){
				$headerbuffer = fgets($fp, 1024);
				if (urlencode($headerbuffer) == "%0D%0A"){
					// We've reached the end of the headers
					break;
				}
			}
			// Read the raw data from the socket in 1kb chunks
			while (!feof($fp)){
				$getdata .= fgets($fp, 1024);
			}
			fclose($fp);
		}
		return $getdata;
	}
	
	private function post_fopen($url, $data, $content_type, $header, $conn_timeout, $timeout){
		$url_array	= parse_url($url);
		$port = ($url_array['scheme'] == 'https') ? 443 : 80;
		$fsock_host = ($url_array['scheme'] == 'https') ? 'ssl://'.$url_array['host'] : $url_array['host'];
		
		$getdata = '';
		if (isset($url_array['host']) && $fp = @fsockopen($fsock_host, $port, $errno, $errstr, $conn_timeout)){
			$out	 = "POST " .$url_array['path']."?".((isset($url_array['query'])) ? $url_array['query'] : '')." HTTP/1.0\r\n";
			$out	.= "Host: ".$url_array['host']." \r\n";
			$out	.= "User-Agent: ".$this->useragent."\r\n";
			$out	.= "Content-type: ".$content_type."\r\n";
			$out	.= "Content-Length: ".strlen($data)."\r\n";
			$out	.= ((is_array($header) && count($header) > 0) ? implode("\r\n", $header): '');
			$out	.= "Connection: Close\r\n";
			$out	.= "\r\n";
			$out	.= $data;
			fwrite($fp, $out);

			// Get rid of the HTTP headers
			while ($fp && !feof($fp)){
				$headerbuffer = fgets($fp, 1024);
				if (urlencode($headerbuffer) == "%0D%0A"){
					// We've reached the end of the headers
					break;
				}
			}
			// Read the raw data from the socket in 1kb chunks
			while (!feof($fp)){
				$getdata .= fgets($fp, 1024);
			}
			fclose($fp);
		}
		return $getdata;
	}

	private function check_function($method){
		switch($method){
			case 'curl':			$func_ex = 'curl_init';			break;
			case 'file_gets':		$func_ex = 'file_get_contents';	break;
			case 'fopen':			$func_ex = 'fgets';				break;
			default: $func_ex =$method;
		}
		return (function_exists($func_ex)) ? true : false;
	}
}