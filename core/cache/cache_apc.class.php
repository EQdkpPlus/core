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

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_datacache" ) ) {
	require_once($eqdkp_root_path . 'core/cache/cache.iface.php');
}

if ( !class_exists( "cache_apc" ) ){
	class cache_apc extends gen_class implements plus_datacache {
				
		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = $global_prefix.$key;
			$data = ($compress) ? $this->compress($data) : $data ;
			return apc_store($key, $data, $ttl);
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = $global_prefix.$key;
			$result = apc_fetch($key);
			//Read error
			if ($result === false) return null;
			
			return ($uncompress) ? $this->uncompress($result) : $result;
		}

		public function del( $key, $global_prefix ) {
			$key = $global_prefix.$key;
			return apc_delete($key);
		}

		public function compress(&$data){
			return gzcompress(serialize($data),9);
		}

		public function uncompress(&$data){
			return unserialize(gzuncompress($data));
		}
	}//end class
}//end if
?>