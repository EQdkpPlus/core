<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can apcutribute it and/or modify
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

if ( !defined('EQDKP_INC') ) {
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_datacache" ) ) {
	require_once($eqdkp_root_path . 'core/cache/cache.iface.php');
}

if ( !class_exists( "cache_apcu" ) ) {
	class cache_apcu extends gen_class implements plus_datacache{


		public function __construct(){
			if(!function_exists('apcu_add')){
				throw new Exception('No apcu available');
			}

		}

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = $global_prefix.$key;

			return apcu_store($key, serialize($data), $ttl);
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = $global_prefix.$key;

			$retval = apcu_fetch($key);

			return ($retval === false) ? null : @unserialize_noclasses($retval);
		}

		public function del( $key, $global_prefix ) {
			$key = $global_prefix.$key;
			apcu_delete($key);
			return true;
		}

		public function get_cachesize($key, $global_prefix){
			return 0;
		}
	}//end class
}//end if
