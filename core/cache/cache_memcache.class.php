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

if ( !defined('EQDKP_INC') ) {
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_datacache" ) ) {
	require_once($eqdkp_root_path . 'core/cache/cache.iface.php');
}

if ( !class_exists( "cache_memcache" ) ) {
	class cache_memcache extends gen_class implements plus_datacache{

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
			$flags = ($uncompress) ? MEMCACHE_COMPRESSED : 0 ;
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
?>