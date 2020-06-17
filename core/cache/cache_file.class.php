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

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if(!class_exists( "cache_file")){
	class cache_file extends gen_class implements plus_datacache {

		private $cache_folder				= "./data/";
		private $file_extension				= '.cf.php';
		private $file_extension_length		= 0;

		public function __construct( ) {
			$this->cache_folder				= $this->pfh->FolderPath( 'data', 'cache' );
			$this->file_extension_length	= strlen( $this->file_extension );
		}

		public function put( $key, $data, $ttl, $global_prefix, $compress = false ) {
			$key = md5($global_prefix.$key);
			$this->pdl->log( 'pdc_query', '', $this->cache_folder.$key[0].DIRECTORY_SEPARATOR.$key.$this->file_extension.', size: '.human_filesize(strlen(serialize($data))));
			$this->pfh->FolderPath( 'data'.DIRECTORY_SEPARATOR.$key[0], 'cache' );
			if( $compress ) {
				$ret = $this->pfh->putContent($this->cache_folder.$key[0].DIRECTORY_SEPARATOR.$key.$this->file_extension, gzcompress( serialize( $data ), 9 ));
			} else {
				$ret = $this->pfh->putContent($this->cache_folder.$key[0].DIRECTORY_SEPARATOR.$key.$this->file_extension, serialize( $data ));
			}
		}

		public function get( $key, $global_prefix, $uncompress = false ) {
			$key = md5($global_prefix.$key);
			$filename = $this->cache_folder.$key[0].DIRECTORY_SEPARATOR.$key.$this->file_extension;
			$this->pdl->log( 'pdc_query', '', $filename.', size: '.human_filesize(filesize($filename)));
			$result = false;
			if(file_exists($filename)){
				$result = file_get_contents($filename);
				$this->pdl->log( 'pdc_query', '', $filename.', cache hit, size: '.human_filesize(filesize($filename)));
			} else {
				$this->pdl->log( 'pdc_query', '', $filename.', cache missed');
			}

			//file read error
			if( $result === false ) {
				return null;
			}

			//all fine
			if( $uncompress ) {
				return unserialize_noclasses( gzuncompress( $result ) );
			} else {
				return unserialize_noclasses( $result );
			}
		}

		public function del( $key, $global_prefix ) {
			$key = md5($global_prefix.$key);
			$file = $this->cache_folder.$key[0].DIRECTORY_SEPARATOR.$key.$this->file_extension;
			if( file_exists( $file ) ) {
				$ret = $this->pfh->Delete($file);
			} else {
				$ret = true;
			}
		}

		public function get_cachesize($key, $global_prefix){
			$key = md5($global_prefix.$key);
			$filename = $this->cache_folder.$key[0].DIRECTORY_SEPARATOR.$key.$this->file_extension;
			if(file_exists($filename)){
				return filesize($filename);
			}
			return 0;
		}
	}//end class
}//end if
