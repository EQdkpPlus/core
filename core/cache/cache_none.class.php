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

if ( !class_exists( "cache_none" ) ) {
	class cache_none extends gen_class implements plus_datacache{

		public function __construct(){
		}

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ){
			return true;
		}

		public function get( $key, $global_prefix, $uncompress = false ){
			return null;
		}

		public function del( $key, $global_prefix ){
			return null;
		}

		public function del_prefix($prefix, $no_global_prefix=false){
			return null;
		}

		public function del_suffix($suffix){
			return null;
		}

		public function cleanup(){
			return null;
		}

		public function flush(){
			return null;
		}

		public function listing(){
			return null;
		}

	}//end class
}//end if
?>