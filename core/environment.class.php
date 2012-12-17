<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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
if (!class_exists("environment")) {
	class environment extends gen_class {
		public static $shortcuts = array('config');

		public $ip, $useragent, $request, $request_page, $request_query, $ssl, $current_page, $server_name, $server_path, $httpHost, $phpself, $link;


		public function __construct() {
			$this->ip 				= $this->get_ipaddress();
			$this->useragent 		= $this->get_useragent();
			$this->request 			= $this->get_request();
			$this->request_page 	= $this->get_request_page();
			$this->eqdkp_request_page = $this->get_eqdkp_request_page();
			$this->request_query 	= $this->get_request_query();
			$this->current_page		= $this->get_current_page();
			$this->ssl				= $this->is_ssl();
			$this->server_name		= $this->get_server_name();
			$this->server_path		= $this->get_server_path();
			$this->httpHost			= $this->httpHost();
			$this->phpself			= $this->get_phpself();
			$this->link				= $this->buildlink();
		}

		private function get_ipaddress(){

			if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
				//This is needed, because HTTP_X_FORWARDED_FOR can contain more than one IP-Address
				$ips = $_SERVER['HTTP_X_FORWARDED_FOR'];
				$arrIps = explode(',', $ips);
				if (strlen(trim($arrIps[0]))){
					return trim($arrIps[0]);
				} else {
					return $_SERVER['REMOTE_ADDR'];
				}
			} else {
				return $_SERVER['REMOTE_ADDR'];
			}
		}

		private function get_useragent(){
			$strUserAgent = (!empty($_SERVER['HTTP_USER_AGENT']))	? $_SERVER['HTTP_USER_AGENT']	: $_ENV['HTTP_USER_AGENT'];
			$strUserAgent = strip_tags($strUserAgent);
			$strUserAgent = preg_replace('/javascript|vbscri?pt|script|applet|alert|document|write|cookie/i', '', $strUserAgent);
			return $strUserAgent;
		}

		private function get_request(){
			$strRequest = (!empty($_SERVER['REQUEST_URI'])) ?  $_SERVER['REQUEST_URI'] : $this->get_server_path().$this->get_request_page().$this->get_request_query();
			return $strRequest;
		}

		private function get_request_page(){
			$strRequest = basename($_SERVER['SCRIPT_NAME']);
			return $this->clean_request($strRequest);
		}

		private function get_request_query(){
			$strRequest = (!empty($_SERVER['QUERY_STRING'])) ? '?' . $_SERVER['QUERY_STRING'] : '';
			return $this->clean_request($strRequest);
		}

		private function get_eqdkp_request_page(){
			$strPage = $this->clean_request($this->get_request());
			return $strPage;
		}

		private function get_current_page(){
			$strPage = $this->clean_request($this->get_request());
			$url_parts = parse_url($strPage);
			$path_parts = pathinfo($url_parts['path']);
			$retStrPage = ((isset($path_parts['dirname']) && $path_parts['dirname'] != '' && $path_parts['dirname'] != '.') ? $path_parts['dirname'].'/' : '').$path_parts['filename'];
			if (isset($url_parts['query']) && $url_parts['query'] != ''){
				$query = preg_replace('#(&)?s\=([0-9A-Za-z]{1,40})?#', '', $url_parts['query']);
				if ($query != '') $retStrPage .= ((substr($query, 0, 1) != '&') ? '&': '').$query;
			}

			return $retStrPage;
		}

		private function clean_request($strRequest){
			$pos = stripos($strRequest, $this->config->get('server_path'));
			if (is_int($pos)) {
					return substr($strRequest, $pos + strlen($this->config->get('server_path')));
			}
    	// Most likely false or null
    	return $strRequest;
		}

		private function get_server_path(){
			return str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
		}

		private function get_server_name(){
			$strServerName = (!empty($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
			return preg_replace('/[^A-Za-z0-9\.:-]/', '', $strServerName);
		}

		private function get_phpself(){
			return $_SERVER['SCRIPT_NAME'];
		}


		protected function is_ssl(){
			return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || isset($_SERVER['SSL_SESSION_ID']));
		}

		protected function httpHost(){
			$protocol = (isset($_SERVER['SSL_SESSION_ID']) || (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1))) ? 'https://' : 'http://';
 			$xhost    = preg_replace('/[^A-Za-z0-9\.:-]/', '',(isset( $_SERVER['HTTP_X_FORWARDED_HOST']) ?  $_SERVER['HTTP_X_FORWARDED_HOST'] : ''));
			$host		= $_SERVER['HTTP_HOST'];
			if (empty($host)){
				$host	 = $_SERVER['SERVER_NAME'];
				$host	.= ($_SERVER['SERVER_PORT'] != 80) ? ':' . $_SERVER['SERVER_PORT'] : '';
			}
			return $protocol.preg_replace('/[^A-Za-z0-9\.:-]/', '', (!empty($xhost) ? $xhost : $host));
		}

		public function buildlink() {
			$script_name = preg_replace('/^\/?(.*?)\/?$/', '\1', trim($this->config->get('server_path')));
			$script_name = ( $script_name != '' ) ? $script_name . '/' : '';
			return $this->httpHost.'/'.$script_name;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_environment', environment::$shortcuts);
?>
