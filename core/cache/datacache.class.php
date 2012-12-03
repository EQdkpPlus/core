<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if( !class_exists( "datacache" ) ) {
	class datacache extends gen_class {
		public static $shortcuts = array('pdl', 'pfh', 'config');
	
		private $cache			= null;

		private $cache_folder	= './cache/data/';
		private $expiry_dates	= array();

		private $default_ttl	= 86400;
		private $global_prefix	= "eqdkp_";

		public function __construct( $cache_type = false ) {
			$this->global_prefix	= ($this->config->get('prefix', 'pdc')) ? $this->config->get('prefix', 'pdc') : $this->table_prefix;
			$this->cache_folder		= $this->pfh->FolderPath( 'data', 'cache' );
			if($this->config->get('dttl', 'pdc')) $this->default_ttl = $this->config->get('dttl', 'pdc');

			//make sure the folder is protected by a .htaccess file
			$this->pfh->secure_folder( 'data', 'cache' );

			//read expiry date tab
			$result = @file_get_contents( $this->cache_folder.'expiry_dates.php' );
			if( $result !== false ) {
				$this->expiry_dates = unserialize( $result );
			} else {
				$this->save_expiry_dates();
			}

			//create our cache object
			if(!$cache_type) $cache_type = ($this->config->get('mode', 'pdc')) ? 'cache_'.$this->config->get('mode', 'pdc') : 'cache_none';
			require_once( $this->root_path.'core/cache/cache.iface.php' );
			require_once( $this->root_path.'core/cache/'.$cache_type.'.class.php' );
			$this->cache = registry::register($cache_type);

			//pdl fun
			if( !$this->pdl->type_known( "pdc_query" ) )
				$this->pdl->register_type( "pdc_query", null, array( $this, 'pdl_html_format_pdc_query' ), array( 2, 3, 4 ) );
		}

		function save_expiry_dates(){
			$this->pfh->putContent($this->cache_folder.'expiry_dates.php', serialize( $this->expiry_dates ));
		}

		function pdl_html_format_pdc_query( $log_entry ) {
			$pdc_query = implode( ' ', $log_entry['args'] );

			$keywords['red'] = array(
				'/(PUT)/',
				'/(DEL)/',
				'/(DEL PREFIX)/',
				'/(DEL SUFFIX)/',
				'/(CLEANUP)/',
				'/(FLUSH)/',
			);

			$keywords['green'] = array(
				'/(GET)/',
			);

			$red_replace = array_fill( 0, sizeof( $keywords['red'] ), '<span class="negative">\\1</span>' );
			$pdc_query = preg_replace( $keywords['red'], $red_replace, $pdc_query );

			$green_replace = array_fill( 0, sizeof( $keywords['green'] ), '<span class="positive">\\1</span>' );
			$pdc_query = preg_replace( $keywords['green'], $green_replace, $pdc_query );

			return $pdc_query;
		}

		private function check_global_prefix($global_prefix = false){
			if($global_prefix == false || $global_prefix = ''){
				return $this->global_prefix;
			}else{
				return $global_prefix;
			}
		}

		public function put( $key, $data, $ttl = null, $global_prefix = false, $compress = false ) {
			$global_prefix = $this->check_global_prefix();

			if($ttl == null)
				$ttl = $this->default_ttl;
			$ret = $this->cache->put( $key, $data, $ttl, $global_prefix, $compress );

			//write successful
			if($ret !== false){
				$this->expiry_dates[$global_prefix][$key] = time() + $ttl;
				$this->save_expiry_dates();
				$this->pdl->log( 'pdc_query', 'PUT', $global_prefix.$key );
			}else{
				$this->pdl->log( 'pdc_query', 'PUT ERROR', $global_prefix.$key );
			}
		}

		public function get( $key, $global_prefix = false, $uncompress = false, $ignoreExpired = false) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'GET', $global_prefix.$key );

			//not cached yet
			if(!isset($this->expiry_dates[$global_prefix][$key])){
				return null;
			}
			//expired
			if(!$ignoreExpired && $this->expiry_dates[$global_prefix][$key] < time()){
				return null;
			}
			return $this->cache->get( $key, $global_prefix, $uncompress );
		}

		public function del( $key, $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'DEL', $global_prefix.$key );
			$this->cache->del( $key, $global_prefix );
			unset($this->expiry_dates[$global_prefix][$key]);
			$this->save_expiry_dates(); 
		}

		public function del_prefix( $prefix, $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();		
			$this->pdl->log( 'pdc_query', 'DEL PREFIX', $global_prefix.$prefix.'*' );

			$prefix_len = strlen($prefix);
			if(isset($this->expiry_dates[$global_prefix]) && is_array($this->expiry_dates[$global_prefix])){
				foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
					//key too short would never match
					if( strlen($key) < $prefix_len )
					continue;

					if( substr( $key, 0, $prefix_len ) == $prefix ){
						$this->cache->del( $key, $global_prefix );
						unset($this->expiry_dates[$global_prefix][$key]);
					}
				}
			}
			$this->save_expiry_dates(); 
		}

		public function del_suffix( $suffix ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'DEL SUFFIX', $global_prefix.'*'.$suffix );
			$suffix_len = strlen($suffix);
			foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
				//key too short would never match
				if( strlen($key) < $suffix_len )
					continue;

				if( substr( $key, -$suffix_len ) == $suffix )
					$this->cache->del( $key, $global_prefix );
				unset($this->expiry_dates[$global_prefix][$key]);
			}
			$this->save_expiry_dates(); 
		}

		public function cleanup( $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'CLEANUP', $key );
			$ctime = time();
			foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
				if(!isset($this->expiry_dates[$global_prefix][$key]) || $this->expiry_dates[$global_prefix][$key] < $ctime)
					$this->cache->del( $key, $global_prefix );
				unset($this->expiry_dates[$global_prefix][$key]);
			}
			$this->save_expiry_dates();
		}

		public function flush( $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'FLUSH'); #, $key ); // $key does not seem to be defined
			if(isset($this->expiry_dates[$global_prefix]) && is_array($this->expiry_dates[$global_prefix])){
				foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
					$this->cache->del( $key, $global_prefix );
					unset($this->expiry_dates[$global_prefix][$key]);
				}
			}
			$this->save_expiry_dates();
		}

		public function listing( ) {
			$cache_arr = array();
			foreach($this->expiry_dates as $global_prefix => $keys){
				foreach($keys as $key => $expiry_date){
					//$cache_arr[$key]['size'] = filesize($this->cache_folder.md5($key).$this->file_extension); //size in bytes
					$cache_arr[$global_prefix][$key]['exp_date'] = $expiry_date;
				}
			}
			return $cache_arr;
		}

		public function get_cache_list( ) {
			$cache_types = array(
				'apc',
				'memcache',
				'none',
				'file',
				'xcache',
			);
		}
	}//end interface
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_datacache', datacache::$shortcuts);
?>