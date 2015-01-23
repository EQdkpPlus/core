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