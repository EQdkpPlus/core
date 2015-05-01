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

class urlfetcher  extends gen_class {

	private $useragent			= '';		// User Agent
	private $timeout			= 15;											// Timeout
	private $conn_timeout		= 5;											// Connection Timeout
	private $methods			= array('curl','file_gets','fopen');			// available function methods
	private $method				= '';											// the selected method
	private $maxRedirects		= 5;

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
		
		if(!$this->pdl->type_known('urlfetcher')) $this->pdl->register_type('urlfetcher', null, null, array(2,3,4));
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
		$this->pdl->log('urlfetcher', 'fetch url: '.$geturl.' method: '.$this->method);
		return $this->{'get_'.$this->method}($geturl, $header, $conn_timeout, $timeout);
	}
	
	public function post($url, $data, $content_type = "text/html; charset=utf-8", $header='', $conn_timeout = false, $timeout = false){
		if(is_array($data)){
			$data = http_build_query($data);
		}
		
		$this->method = ($this->method) ? $this->method : 'fopen';
		if (!$conn_timeout) $conn_timeout = $this->conn_timeout;
		if (!$timeout) $timeout = $this->timeout;
		$this->pdl->log('urlfetcher', 'post url: '.$geturl.' method: '.$this->method);
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
			CURLOPT_HTTPAUTH		=> CURLAUTH_ANY,
			CURLOPT_HTTPHEADER		=> ((is_array($header) && count($header) > 0) ? $header : array())
		);
		if (@ini_get('open_basedir') == '' && (!@ini_get('safe_mode') || ini_get('safe_mode') == 'Off')) {
			$curlOptions[CURLOPT_FOLLOWLOCATION] = true;
			
			$curl = curl_init();
			curl_setopt_array($curl, $curlOptions);
			$getdata = curl_exec($curl);
			
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			$arrCurlInfo = curl_getinfo($curl);
			$curl_error = curl_errno($curl);
			$this->pdl->log('urlfetcher', 'Curl Error Nr. '.$curl_error);
			$this->pdl->log('urlfetcher', 'Curl Info: '.print_r($curl_error, true));
			$this->pdl->log('urlfetcher', 'Response Code: '.$code);
			$this->pdl->log('urlfetcher', 'Response: '.strlen($getdata).'; First 200 Chars: '.substr($getdata, 0, 200));
			
			curl_close($curl);
			if(intval($code) >= 400) return false;
			
			return $getdata;	
		} else {
			$curlOptions[CURLOPT_HEADER] = true;
			$curlOptions[CURLOPT_FORBID_REUSE] = false;
			$curlOptions[CURLOPT_RETURNTRANSFER] = true;
			
			$maxRedirects = $this->maxRedirects;
			
			$curl = curl_init();
			curl_setopt_array($curl, $curlOptions);
			
			$newurl = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
			$code = 0;
			do {
				curl_setopt($curl, CURLOPT_URL, $newurl);
				$header = curl_exec($curl);
				if (curl_errno($curl)) {
					$code = 0;
				} else {
					$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
					if ($code == 301 || $code == 302) {
						preg_match('/Location:(.*?)\n/', $header, $matches);
						$newurl = trim(array_pop($matches));
						if(stripos($newurl, '://') === false){
							$curlData = curl_getinfo($curl);						
							$urlData = parse_url($curlData['url']);
							$newurl = $urlData['scheme'].'://'.$urlData['host'].$newurl;
						}
						curl_setopt($curl, CURLOPT_POSTFIELDS, null); //also switch modes after Redirect
						curl_setopt($curl, CURLOPT_HTTPGET, true);
						$this->pdl->log('urlfetcher', 'Redirect to '.$newurl.' because of Code '.$code);
					} else {
						$code = 0;
					}
				}
			
			} while ($code && --$maxRedirects);
			
			if ($maxRedirects < 0) {
				trigger_error('Too many redirects. When following redirects, libcurl hit the maximum amount.', E_USER_WARNING);
				return false;
			}
			
			curl_setopt($curl, CURLOPT_URL, $newurl);
			$getdata = curl_exec($curl);
			$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			
			$arrCurlInfo = curl_getinfo($curl);
			$curl_error = curl_errno($curl);
			$this->pdl->log('urlfetcher', 'Curl Error Nr. '.$curl_error);
			$this->pdl->log('urlfetcher', 'Curl Info: '.print_r($curl_error, true));
			
			curl_close($curl);
			//Remove Header
			list ($header,$page) = preg_split('/\r\n\r\n/',$getdata,2); 
			
			$this->pdl->log('urlfetcher', 'Response Code: '.$code);
			$this->pdl->log('urlfetcher', 'Reponse Header: '.$header);
			$this->pdl->log('urlfetcher', 'Response: '.strlen($page).'; First 200 Chars: '.substr($page, 0, 200));
			 
			return $page;
		}
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
		if (@ini_get('open_basedir') == '' && !@ini_get('safe_mode')) {
			$curlOptions[CURLOPT_FOLLOWLOCATION] = true;
		}
		$curl = curl_init();
		curl_setopt_array($curl, $curlOptions);
		$getdata = curl_exec($curl);
		$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		
		$arrCurlInfo = curl_getinfo($curl);
		$curl_error = curl_errno($curl);
		$this->pdl->log('urlfetcher', 'Curl Error Nr. '.$curl_error);
		$this->pdl->log('urlfetcher', 'Curl Info: '.print_r($curl_error, true));
		
		curl_close($curl);
		
		$this->pdl->log('urlfetcher', 'Response Code: '.$code);	
		$this->pdl->log('urlfetcher', 'Response: '.strlen($getdata).'; First 200 Chars: '.substr($getdata, 0, 200));
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
		
		$this->pdl->log('urlfetcher', 'Response: '.strlen($getdata).'; First 200 Chars: '.substr($getdata, 0, 200));
		
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
		$getdata	= @file_get_contents($url, false, $context);
		
		$this->pdl->log('urlfetcher', 'Response: '.strlen($getdata).'; First 200 Chars: '.substr($getdata, 0, 200));
		
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
		
		$this->pdl->log('urlfetcher', 'Response: '.strlen($getdata).'; First 200 Chars: '.substr($getdata, 0, 200));
		
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
		
		$this->pdl->log('urlfetcher', 'Response: '.strlen($getdata).'; First 200 Chars: '.substr($getdata, 0, 200));
		
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