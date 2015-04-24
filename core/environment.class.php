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
if (!class_exists("environment")) {
	class environment extends gen_class {

		public $ip, $useragent, $request, $request_page, $request_query, $ssl, $current_page, $server_name, $server_path, $httpHost, $phpself, $link, $agent, $path, $is_ajax, $referer;

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
			$this->path				= $this->path();
			$this->agent			= $this->agent();
			$this->is_ajax			= $this->is_ajax();
			$this->referer			= $this->get_referer();
		}
		
		/**
		 * By Contao CMS
		 * 
		 * Operating systems (check Windows CE before Windows and Android before Linux!)
		 */
		public $arrOS = array
		(
				'Macintosh'     => array('os'=>'mac',        'mobile'=>false),
				'Windows CE'    => array('os'=>'win-ce',     'mobile'=>true),
				'Windows Phone' => array('os'=>'win-ce',     'mobile'=>true),
				'Windows'       => array('os'=>'win',        'mobile'=>false),
				'iPad'          => array('os'=>'ios',        'mobile'=>false),
				'iPhone'        => array('os'=>'ios',        'mobile'=>true),
				'iPod'          => array('os'=>'ios',        'mobile'=>true),
				'Android'       => array('os'=>'android',    'mobile'=>true),
				'BB10'          => array('os'=>'blackberry', 'mobile'=>true),
				'Blackberry'    => array('os'=>'blackberry', 'mobile'=>true),
				'Symbian'       => array('os'=>'symbian',    'mobile'=>true),
				'WebOS'         => array('os'=>'webos',      'mobile'=>true),
				'Linux'         => array('os'=>'unix',       'mobile'=>false),
				'FreeBSD'       => array('os'=>'unix',       'mobile'=>false),
				'OpenBSD'       => array('os'=>'unix',       'mobile'=>false),
				'NetBSD'        => array('os'=>'unix',       'mobile'=>false),
		);
		
		
		/**
		 * By Contao CMS
		 * 
		 * Browsers (check OmniWeb and Silk before Safari and Opera Mini/Mobi before Opera!)
		*/
		public $arrBrowser = array
		(
				'MSIE'       => array('browser'=>'ie',           'shorty'=>'ie', 'engine'=>'trident', 'version'=>'/^.*?MSIE (\d+(\.\d+)*).*$/'),
				'Firefox'    => array('browser'=>'firefox',      'shorty'=>'fx', 'engine'=>'gecko',   'version'=>'/^.*Firefox\/(\d+(\.\d+)*).*$/'),
				'Chrome'     => array('browser'=>'chrome',       'shorty'=>'ch', 'engine'=>'webkit',  'version'=>'/^.*Chrome\/(\d+(\.\d+)*).*$/'),
				'OmniWeb'    => array('browser'=>'omniweb',      'shorty'=>'ow', 'engine'=>'webkit',  'version'=>'/^.*Version\/(\d+(\.\d+)*).*$/'),
				'Silk'       => array('browser'=>'silk',         'shorty'=>'si', 'engine'=>'silk',    'version'=>'/^.*Silk\/(\d+(\.\d+)*).*$/'),
				'Safari'     => array('browser'=>'safari',       'shorty'=>'sf', 'engine'=>'webkit',  'version'=>'/^.*Version\/(\d+(\.\d+)*).*$/'),
				'Opera Mini' => array('browser'=>'opera-mini',   'shorty'=>'oi', 'engine'=>'presto',  'version'=>'/^.*Opera Mini\/(\d+(\.\d+)*).*$/'),
				'Opera Mobi' => array('browser'=>'opera-mobile', 'shorty'=>'om', 'engine'=>'presto',  'version'=>'/^.*Version\/(\d+(\.\d+)*).*$/'),
				'Opera'      => array('browser'=>'opera',        'shorty'=>'op', 'engine'=>'presto',  'version'=>'/^.*Version\/(\d+(\.\d+)*).*$/'),
				'IEMobile'   => array('browser'=>'ie-mobile',    'shorty'=>'im', 'engine'=>'trident', 'version'=>'/^.*IEMobile (\d+(\.\d+)*).*$/'),
				'Camino'     => array('browser'=>'camino',       'shorty'=>'ca', 'engine'=>'gecko',   'version'=>'/^.*Camino\/(\d+(\.\d+)*).*$/'),
				'Konqueror'  => array('browser'=>'konqueror',    'shorty'=>'ko', 'engine'=>'webkit',  'version'=>'/^.*Konqueror\/(\d+(\.\d+)*).*$/')
		);
		

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

		public function get_current_page($blnWithQuery = true){
			$strPage = $this->clean_request($this->get_request());
			$url_parts = parse_url($strPage);
			$path_parts = isset($url_parts['path']) ? pathinfo($url_parts['path']) : array();
			$retStrPage = ((isset($path_parts['dirname']) && $path_parts['dirname'] != '' && $path_parts['dirname'] != '.') ? $path_parts['dirname'].'/' : '').((isset($path_parts['filename'])) ? $path_parts['filename'] : '');
			if (isset($url_parts['query']) && $url_parts['query'] != '' && $blnWithQuery){
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
			if(defined('NO_SSL') && NO_SSL) return false;
			return ((isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)) || isset($_SERVER['SSL_SESSION_ID']) || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ));
		}

		protected function httpHost(){
			$protocol = ($this->is_ssl()) ? 'https://' : 'http://';
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
		
		public function path(){
			$path_info = !empty($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : (!empty($_SERVER['ORIG_PATH_INFO']) ? $_SERVER['ORIG_PATH_INFO'] : '');
			if (!strlen($path_info)) return '';
			return filter_var($path_info, FILTER_SANITIZE_STRING);
		}
		
		public function is_ajax(){
			return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
		}
		
		public function get_referer(){
			if (!isset($_SERVER['HTTP_REFERER'])) return '';
			$ref = filter_var($_SERVER['HTTP_REFERER'], FILTER_SANITIZE_STRING);
			$ref = str_replace(array($this->buildlink(), 'index.php/'), '', $ref);
			return $ref;
		}
		
		public function agent(){
			$ua = $this->get_useragent();
			
			$return = new stdClass();
			$return->string = $ua;
			
			$os = 'unknown';
			$mobile = false;
			$browser = 'other';
			$shorty = '';
			$version = '';
			$engine = '';
			
			// Operating system
			foreach ($this->arrOS as $k=>$v)
			{
				if (stripos($ua, $k) !== false)
				{
					$os = $v['os'];
					$mobile = $v['mobile'];
					break;
				}
			}
			
			// Android tablets are not mobile
			if ($os == 'android' && stripos($ua, 'mobile') === false)
			{
				$mobile = false;
			}
			
			$return->os = $os;
			
			// Browser and version
			foreach ($this->arrBrowser as $k=>$v)
			{
				if (stripos($ua, $k) !== false)
				{
					$browser = $v['browser'];
					$shorty  = $v['shorty'];
					$version = preg_replace($v['version'], '$1', $ua);
					$engine  = $v['engine'];
					break;
				}
			}
			
			$versions = explode('.', $version);
			$version  = $versions[0];
			
			$return->class = $os . ' ' . $browser . ' ' . $engine;
			
			// Add the version number if available
			if ($version != '')
			{
				$return->class .= ' ' . $shorty . $version;
			}
			
			// Mark mobile devices
			if ($mobile)
			{
				$return->class .= ' mobile';
			}
						
			$return->browser  = $browser;
			$return->shorty   = $shorty;
			$return->version  = $version;
			$return->engine   = $engine;
			$return->versions = $versions;
			$return->mobile   = $mobile;
			return $return;
		}
		
		
		/**
		 * Checks if Operating System is Windows IIS
		 * 
		 * @return boolean
		 */
		function on_iis() {
			$sSoftware = strtolower( $_SERVER["SERVER_SOFTWARE"] );
			if ( strpos($sSoftware, "microsoft-iis") !== false )
				return true;
			else
				return false;
		}
		
		/**
		 * Returns String for Operating System
		 * 
		 * @return mixed
		 */
		function get_operating_system(){
			return filter_var($_SERVER["SERVER_SOFTWARE"], FILTER_SANITIZE_STRING);;
		}
		
		/**
		 * Checks if Useragent is Bot. If yes, returns the Botname. Returns false if not a Bot.
		 * 
		 * @param string $strUseragent
		 * @return Ambigous <string>|boolean
		 */
		public function is_bot($strUseragent){
			$arrBots = array(
				array( // row #0
					'bot_agent' => 'AdsBot-Google',
					'bot_name' => 'AdsBot [Google]',
				),
				array( // row #1
					'bot_agent' => 'ia_archiver',
					'bot_name' => 'Alexa [Bot]',
				),
				array( // row #2
					'bot_agent' => 'Scooter/',
					'bot_name' => 'Alta Vista [Bot]',
				),
				array( // row #3
					'bot_agent' => 'Ask Jeeves',
					'bot_name' => 'Ask Jeeves [Bot]',
				),
				array( // row #4
					'bot_agent' => 'Baiduspider+(',
					'bot_name' => 'Baidu [Spider]',
				),
				array( // row #5
					'bot_agent' => 'Exabot/',
					'bot_name' => 'Exabot [Bot]',
				),
				array( // row #6
					'bot_agent' => 'FAST Enterprise Crawler',
					'bot_name' => 'FAST Enterprise [Crawler]',
				),
				array( // row #7
					'bot_agent' => 'FAST-WebCrawler/',
					'bot_name' => 'FAST WebCrawler [Crawler]',
				),
				array( // row #8
					'bot_agent' => 'http://www.neomo.de/',
					'bot_name' => 'Francis [Bot]',
				),
				array( // row #9
					'bot_agent' => 'Gigabot/',
					'bot_name' => 'Gigabot [Bot]',
				),
				array( // row #10
					'bot_agent' => 'Mediapartners-Google',
					'bot_name' => 'Google Adsense [Bot]',
				),
				array( // row #11
					'bot_agent' => 'Google Desktop',
					'bot_name' => 'Google Desktop',
				),
				array( // row #12
					'bot_agent' => 'Feedfetcher-Google',
					'bot_name' => 'Google Feedfetcher',
				),
				array( // row #13
					'bot_agent' => 'Googlebot',
					'bot_name' => 'Google [Bot]',
				),
				array( // row #14
					'bot_agent' => 'heise-IT-Markt-Crawler',
					'bot_name' => 'Heise IT-Markt [Crawler]',
				),
				array( // row #15
					'bot_agent' => 'heritrix/1.',
					'bot_name' => 'Heritrix [Crawler]',
				),
				array( // row #16
					'bot_agent' => 'ibm.com/cs/crawler',
					'bot_name' => 'IBM Research [Bot]',
				),
				array( // row #17
					'bot_agent' => 'ICCrawler - ICjobs',
					'bot_name' => 'ICCrawler - ICjobs',
				),
				array( // row #18
					'bot_agent' => 'ichiro/',
					'bot_name' => 'ichiro [Crawler]',
				),
				array( // row #19
					'bot_agent' => 'MJ12bot/',
					'bot_name' => 'Majestic-12 [Bot]',
				),
				array( // row #20
					'bot_agent' => 'MetagerBot/',
					'bot_name' => 'Metager [Bot]',
				),
				array( // row #21
					'bot_agent' => 'msnbot-NewsBlogs/',
					'bot_name' => 'MSN NewsBlogs',
				),
				array( // row #22
					'bot_agent' => 'msnbot/',
					'bot_name' => 'MSN [Bot]',
				),
				array( // row #23
					'bot_agent' => 'msnbot-media/',
					'bot_name' => 'MSNbot Media',
				),
				array( // row #24
					'bot_agent' => 'NG-Search/',
					'bot_name' => 'NG-Search [Bot]',
				),
				array( // row #25
					'bot_agent' => 'http://lucene.apache.org/nutch/',
					'bot_name' => 'Nutch [Bot]',
				),
				array( // row #26
					'bot_agent' => 'NutchCVS/',
					'bot_name' => 'Nutch/CVS [Bot]',
				),
				array( // row #27
					'bot_agent' => 'OmniExplorer_Bot/',
					'bot_name' => 'OmniExplorer [Bot]',
				),
				array( // row #28
					'bot_agent' => 'online link validator',
					'bot_name' => 'Online link [Validator]',
				),
				array( // row #29
					'bot_agent' => 'psbot/0',
					'bot_name' => 'psbot [Picsearch]',
				),
				array( // row #30
					'bot_agent' => 'Seekbot/',
					'bot_name' => 'Seekport [Bot]',
				),
				array( // row #31
					'bot_agent' => 'Sensis Web Crawler',
					'bot_name' => 'Sensis [Crawler]',
				),
				array( // row #32
					'bot_agent' => 'SEO search Crawler/',
					'bot_name' => 'SEO Crawler',
				),
				array( // row #33
					'bot_agent' => 'Seoma [SEO Crawler]',
					'bot_name' => 'Seoma [Crawler]',
				),
				array( // row #34
					'bot_agent' => 'SEOsearch/',
					'bot_name' => 'SEOSearch [Crawler]',
				),
				array( // row #35
					'bot_agent' => 'Snappy/1.1 ( http://www.urltrends.com/ )',
					'bot_name' => 'Snappy [Bot]',
				),
				array( // row #36
					'bot_agent' => 'http://www.tkl.iis.u-tokyo.ac.jp/~crawler/',
					'bot_name' => 'Steeler [Crawler]',
				),
				array( // row #37
					'bot_agent' => 'SynooBot/',
					'bot_name' => 'Synoo [Bot]',
				),
				array( // row #38
					'bot_agent' => 'crawleradmin.t-info@telekom.de',
					'bot_name' => 'Telekom [Bot]',
				),
				array( // row #39
					'bot_agent' => 'TurnitinBot/',
					'bot_name' => 'TurnitinBot [Bot]',
				),
				array( // row #40
					'bot_agent' => 'voyager/1.0',
					'bot_name' => 'Voyager [Bot]',
				),
				array( // row #41
					'bot_agent' => 'W3 SiteSearch Crawler',
					'bot_name' => 'W3 [Sitesearch]',
				),
				array( // row #42
					'bot_agent' => 'W3C-checklink/',
					'bot_name' => 'W3C [Linkcheck]',
				),
				array( // row #43
					'bot_agent' => 'W3C_*Validator',
					'bot_name' => 'W3C [Validator]',
				),
				array( // row #44
					'bot_agent' => 'http://www.WISEnutbot.com',
					'bot_name' => 'WiseNut [Bot]',
				),
				array( // row #45
					'bot_agent' => 'yacybot',
					'bot_name' => 'YaCy [Bot]',
				),
				array( // row #46
					'bot_agent' => 'Yahoo-MMCrawler/',
					'bot_name' => 'Yahoo MMCrawler [Bot]',
				),
				array( // row #47
					'bot_agent' => 'Yahoo! DE Slurp',
					'bot_name' => 'Yahoo Slurp [Bot]',
				),
				array( // row #48
					'bot_agent' => 'Yahoo! Slurp',
					'bot_name' => 'Yahoo [Bot]',
				),
				array( // row #49
					'bot_agent' => 'YahooSeeker/',
					'bot_name' => 'YahooSeeker [Bot]',
				),
				array( // row #50
					'bot_agent' => 'YandexBot/',
					'bot_name' => 'Yandex [Bot]',
				),
				
			);
			
			foreach ($arrBots as $row){
				if (preg_match('#' . str_replace('\*', '.*?', preg_quote($row['bot_agent'], '#')) . '#i', $strUseragent)){
					return $row['bot_name'];
				}
			}
			return false;
		}
		
		public function server_to_rootpath($strPath){
			//String starts with the server_path
			$strServerpath = $this->config->get('server_path');
			if(stripos($strPath, $strServerpath ) === 0){
				$strPath = $this->root_path.substr($strPath, strlen($strServerpath));
			} elseif(stripos($strPath, $this->root_path) === false){
				//String starts not with root_path, means he starts with nothing
				$strPath = $this->root_path.$strPath;
			}
			return $strPath;
		}
		
		public function root_to_serverpath($strPath){
			$strServerpath = $this->config->get('server_path');
			if(stripos($strPath, $this->root_path ) === 0){
				$strPath = $strServerpath.substr($strPath, strlen($this->root_path));
			} elseif(stripos($strPath, $strServerpath) === false){
				//String starts not with server_path, means he starts with nothing
				$strPath = $strServerpath.$strPath;
			}
			
			return $strPath;
		}
	}
}
?>
