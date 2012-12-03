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

if ( !defined('EQDKP_INC') ) {
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_datacache" ) ) {
	require_once($eqdkp_root_path . 'core/cache/cache.iface.php');
}

if ( !class_exists( "cache_memcache" ) ) {
	class cache_memcache extends gen_class implements plus_datacache{
		public static $shortcuts = array('config' => 'core');

		public $server = 'localhost';
		public $memcache;

		public function __construct(){
			$this->memcache = new Memcache;
			$this->memcache->connect($this->config->get('server', 'pdc'), $this->config->get('port', 'pdc'));
		}

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = $global_prefix.$key;
			$flags = ($compress) ? MEMCACHE_COMPRESSED : false ;
			return $this->memcache->set($key, $data, $flags, $ttl);
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = $global_prefix.$key;
			$flags = ($compress) ? MEMCACHE_COMPRESSED : 0 ;
			$retval = $this->memcache->get($key, $flags);
			return ($retval == false) ? null : $retval;
		}

		public function del( $key, $global_prefix ) {
			$key = $global_prefix.$key;	
			$this->memcache->delete($key);
			return true;
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_cache_memcache', cache_memcache::$shortcuts);
?>