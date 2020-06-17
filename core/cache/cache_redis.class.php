<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if ( !class_exists( "cache_redis" ) ) {
	class cache_redis extends gen_class implements plus_datacache{

		public $server = 'localhost';
		public $redis;

		public function __construct(){
			if(!class_exists('Redis')){
				throw new Exception('No Redis available');
			}

			$this->redis = new Redis();

			$intPort = ($this->config->get('port', 'pdc') === false) ? 6379 : $this->config->get('port', 'pdc');

			$blnConnectionResult = $this->redis->connect($this->config->get('server', 'pdc'), $intPort);
			if(!$blnConnectionResult){
				throw new Exception('No connection to redis server');
			}

			$strPrefix = substr(md5(registry::get_const('dbname')), 0, 8);

			$this->redis->setOption(\Redis::OPT_PREFIX, $strPrefix.':');
		}

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = $global_prefix.$key;


			return $this->redis->setex($key, $ttl, serialize($data));
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = $global_prefix.$key;


			$retval = $this->redis->get($key);
			return ($retval === false) ? null : @unserialize_noclasses($retval);
		}

		public function del( $key, $global_prefix ) {
			$key = $global_prefix.$key;
			$this->redis->del($key);
			return true;
		}

		public function get_cachesize($key, $global_prefix){
			return 0;
		}
	}//end class
}//end if
