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