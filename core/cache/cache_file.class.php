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

if(!class_exists( "cache_file")){
	class cache_file extends gen_class implements plus_datacache {
		public static $shortcuts = array('pfh');

		private $cache_folder				= "./data/";
		private $file_extension				= '.cf.php';
		private $file_extension_length		= 0;

		public function __construct( ) {
			$this->cache_folder				= $this->pfh->FolderPath( 'data', 'cache' );
			$this->file_extension_length	= strlen( $this->file_extension );
		}

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = $global_prefix.$key;
			if( $compress ) {
				$ret = $this->pfh->putContent($this->cache_folder.md5( $key ).$this->file_extension, gzcompress( serialize( $data ), 9 ));
			} else {
				$ret = $this->pfh->putContent($this->cache_folder.md5( $key ).$this->file_extension, serialize( $data ));
			}
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = $global_prefix.$key;
			$filename = $this->cache_folder.md5($key).$this->file_extension;
			$result = false;
			if(is_file($filename)){
				$result = file_get_contents($filename);
			}

			//file read error
			if( $result === false ) {
				return null;
			}

			//all fine
			if( $uncompress ) {
				return unserialize( gzuncompress( $result ) );
			} else {
				return unserialize( $result );
			}
		}

		public function del( $key, $global_prefix ) {
			$key = $global_prefix.$key;
			$file = $this->cache_folder.md5( $key ).$this->file_extension;
			if( file_exists( $file ) ) {
				$ret = $this->pfh->Delete($file);
			} else {
				$ret = true;
			}
		}
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_cache_file', cache_file::$shortcuts);
?>