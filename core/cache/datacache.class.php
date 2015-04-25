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

if( !defined( 'EQDKP_INC' ) ) {
	die( 'Do not access this file directly.' );
}

if( !class_exists( "datacache" ) ) {
	class datacache extends gen_class {
	
		private $cache			= null;

		private $cache_folder	= './cache/data/';
		private $expiry_dates	= array();

		private $default_ttl	= 86400;
		private $global_prefix	= "eqdkp_";

		public function __construct( $cache_type = false ) {
			$this->global_prefix	= ($this->config->get('prefix', 'pdc')) ? $this->config->get('prefix', 'pdc') : $this->table_prefix;
			$this->cache_folder		= $this->pfh->FolderPath( 'data', 'cache' );
			if($this->config->get('dttl', 'pdc')) $this->default_ttl = $this->config->get('dttl', 'pdc');

			//make sure the folder is protected by a .htaccess file
			$this->pfh->secure_folder( 'data', 'cache' );

			//read expiry date tab
			$result = @file_get_contents( $this->cache_folder.'expiry_dates.php' );
			if( $result !== false ) {
				$this->expiry_dates = unserialize( $result );
			} else {
				$this->save_expiry_dates();
			}

			//create our cache object
			if(!$cache_type) $cache_type = ($this->config->get('mode', 'pdc')) ? 'cache_'.$this->config->get('mode', 'pdc') : 'cache_none';
			require_once( $this->root_path.'core/cache/cache.iface.php' );
			require_once( $this->root_path.'core/cache/'.$cache_type.'.class.php' );
			$this->cache = registry::register($cache_type);

			//pdl fun
			if( !$this->pdl->type_known( "pdc_query" ) )
				$this->pdl->register_type( "pdc_query", null, array( $this, 'pdl_html_format_pdc_query' ), array( 2, 3, 4 ) );
		}

		function save_expiry_dates(){
			$this->pfh->putContent($this->cache_folder.'expiry_dates.php', serialize( $this->expiry_dates ));
		}

		function pdl_html_format_pdc_query( $log_entry ) {
			$pdc_query = implode( ' ', $log_entry['args'] );

			$keywords['red'] = array(
				'/(PUT)/',
				'/(DEL)/',
				'/(DEL PREFIX)/',
				'/(DEL SUFFIX)/',
				'/(CLEANUP)/',
				'/(FLUSH)/',
			);

			$keywords['green'] = array(
				'/(GET)/',
			);

			$red_replace = array_fill( 0, sizeof( $keywords['red'] ), '<span class="negative">\\1</span>' );
			$pdc_query = preg_replace( $keywords['red'], $red_replace, $pdc_query );

			$green_replace = array_fill( 0, sizeof( $keywords['green'] ), '<span class="positive">\\1</span>' );
			$pdc_query = preg_replace( $keywords['green'], $green_replace, $pdc_query );

			return $pdc_query;
		}

		private function check_global_prefix($global_prefix = false){
			if($global_prefix == false || $global_prefix = ''){
				return $this->global_prefix;
			}else{
				return $global_prefix;
			}
		}

		public function put( $key, $data, $ttl = null, $global_prefix = false, $compress = false ) {
			$global_prefix = $this->check_global_prefix();

			if($ttl == null)
				$ttl = $this->default_ttl;
			$ret = $this->cache->put( $key, $data, $ttl, $global_prefix, $compress );

			//write successful
			if($ret !== false){
				$this->expiry_dates[$global_prefix][$key] = time() + $ttl;
				$this->save_expiry_dates();
				$this->pdl->log( 'pdc_query', 'PUT', $global_prefix.$key );
			}else{
				$this->pdl->log( 'pdc_query', 'PUT ERROR', $global_prefix.$key );
			}
		}

		public function get( $key, $global_prefix = false, $uncompress = false, $ignoreExpired = false) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'GET', $global_prefix.$key );

			//not cached yet
			if(!isset($this->expiry_dates[$global_prefix][$key])){
				return null;
			}
			//expired
			if(!$ignoreExpired && $this->expiry_dates[$global_prefix][$key] < time()){
				return null;
			}
			return $this->cache->get( $key, $global_prefix, $uncompress );
		}

		public function del( $key, $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'DEL', $global_prefix.$key );
			$this->cache->del( $key, $global_prefix );
			unset($this->expiry_dates[$global_prefix][$key]);
			$this->save_expiry_dates(); 
		}

		public function del_prefix( $prefix, $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();		
			$this->pdl->log( 'pdc_query', 'DEL PREFIX', $global_prefix.$prefix.'*' );

			$prefix_len = strlen($prefix);
			if(isset($this->expiry_dates[$global_prefix]) && is_array($this->expiry_dates[$global_prefix])){
				foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
					//key too short would never match
					if( strlen($key) < $prefix_len )
					continue;

					if( substr( $key, 0, $prefix_len ) == $prefix ){
						$this->cache->del( $key, $global_prefix );
						unset($this->expiry_dates[$global_prefix][$key]);
					}
				}
			}
			$this->save_expiry_dates(); 
		}

		public function del_suffix( $suffix ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'DEL SUFFIX', $global_prefix.'*'.$suffix );
			$suffix_len = strlen($suffix);
			foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
				//key too short would never match
				if( strlen($key) < $suffix_len )
					continue;

				if( substr( $key, -$suffix_len ) == $suffix )
					$this->cache->del( $key, $global_prefix );
				unset($this->expiry_dates[$global_prefix][$key]);
			}
			$this->save_expiry_dates(); 
		}

		public function cleanup( $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'CLEANUP', $key );
			$ctime = time();
			foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
				if(!isset($this->expiry_dates[$global_prefix][$key]) || $this->expiry_dates[$global_prefix][$key] < $ctime) {
					$this->cache->del( $key, $global_prefix );
					unset($this->expiry_dates[$global_prefix][$key]);
				}
			}
			$this->save_expiry_dates();
		}

		public function flush( $global_prefix = false ) {
			$global_prefix = $this->check_global_prefix();
			$this->pdl->log( 'pdc_query', 'FLUSH'); #, $key ); // $key does not seem to be defined
			if(isset($this->expiry_dates[$global_prefix]) && is_array($this->expiry_dates[$global_prefix])){
				foreach($this->expiry_dates[$global_prefix] as $key => $expiry_date){
					$this->cache->del( $key, $global_prefix );
					unset($this->expiry_dates[$global_prefix][$key]);
				}
			}
			$this->save_expiry_dates();
		}

		public function listing( ) {
			$cache_arr = array();
			foreach($this->expiry_dates as $global_prefix => $keys){
				foreach($keys as $key => $expiry_date){
					//$cache_arr[$key]['size'] = filesize($this->cache_folder.md5($key).$this->file_extension); //size in bytes
					$cache_arr[$global_prefix][$key]['exp_date'] = $expiry_date;
				}
			}
			return $cache_arr;
		}

		public function get_cache_list( ) {
			$cache_types = array(
				'apc',
				'memcache',
				'none',
				'file',
				'xcache',
			);
		}
	}//end interface
}//end if

if( !class_exists( "cachePagination" ) ) {
	/**
	 * This Cache tries to load big Data into Chunks and handles the access to save memory and execution time, because it let's to the Database some Operations like Sorting and Searching
	 * 
	 * @author GodMod
	 */
	class cachePagination extends gen_class {
		
		protected $strCacheKey = "";
		protected $intItemsPerChunk = 50;
		protected $arrQuerys;
		protected $strID;
		protected $strTablename = "";
		
		protected $index = array();
		protected $data = array();
		
		/**
		 * Constructor
		 * 
		 * @param string $strCacheKey - Name of the Cache Key
		 * @param string $strID - Name of the Primary Key
		 * @param string $strTableName - Name of the Table, e.g. __items
		 * @param string $arrQuerys - Array with specific Querys, if the generic Query won't work
		 * @param number $intItemsPerChunk - Items per Chunk, Default 50
		 */
		public function __construct($strCacheKey, $strID, $strTableName, $arrQuerys=array('index' => '', 'chunk' => '', 'direct' => '', 'tag_direct' => ''), $intItemsPerChunk=50){
			$this->strCacheKey = $strCacheKey;
			$this->intItemsPerChunk = $intItemsPerChunk;
			$this->arrQuerys = $arrQuerys;
			$this->strID = $strID;
			$this->strTablename = $strTableName;
		}
				
		/**
		 * Builds the Index-File with all Primary IDs
		 * 
		 * @return boolean
		 */
		public function initIndex(){
			$this->index = $this->pdc->get('pdh_'.$this->strCacheKey.'_index');
			if ($this->index == null){
				$strQuery = (isset($this->arrQuerys['index']) && strlen($this->arrQuerys['index'])) ? $this->arrQuerys['index'] : "SELECT ".$this->strID." FROM ".$this->strTablename;
				
				$objQuery = $this->db->query($strQuery);
				if($objQuery){
					while($row = $objQuery->fetchAssoc()){
						$this->index[$row[$this->strID]] = $row[$this->strID];
					}
				}
				
				$this->pdc->put('pdh_'.$this->strCacheKey.'_index', $this->index, null);
			}
			return true;
		}
		
		/**
		 * Returns an array with all Primary IDs 
		 * 
		 * @return array: IDs
		 */
		public function getIndex(){
			return $this->index;
		}
		
		/**
		 * Gets a Object from the Database, saves it to the Cache-Chunks
		 * 
		 * @param integer $intObjectID
		 * @return boolean
		 */
		public function initObject($intObjectID){
			$intChunkID = $this->calculateChunkID($intObjectID);

			if (!isset($this->index[$intObjectID])) return false;
			
			if (!isset($this->data[$intChunkID])){
				//Load Chunk
				$arrCacheData = $this->pdc->get('pdh_'.$this->strCacheKey.'_chunk_'.$intChunkID);
				if ($arrCacheData == null){
					$strQuery = (isset($this->arrQuerys['chunk']) && strlen($this->arrQuerys['chunk'])) ? $this->arrQuerys['chunk'] : "SELECT * FROM ".$this->strTablename." WHERE ".$this->strID." >= ? AND ".$this->strID." < ?";
					$objQuery = $this->db->prepare($strQuery)->execute($intChunkID*$this->intItemsPerChunk, ($intChunkID+1)*$this->intItemsPerChunk);
					if($objQuery){
						while($drow = $objQuery->fetchAssoc()){
							$cache_result[$drow[$this->strID]] = $drow;
						}
					}
					
					//Additional Cache Data
					if (isset($this->arrQuerys['additionalData']) && strlen($this->arrQuerys['additionalData'])){
						$objQuery = $this->db->prepare($this->arrQuerys['additionalData'])->execute($intChunkID*$this->intItemsPerChunk, ($intChunkID+1)*$this->intItemsPerChunk);

						if($objQuery){
							while($drow = $objQuery->fetchAssoc()){
								if (isset($cache_result[$drow['object_key']])){
									$cache_result[$drow['object_key']]['additional'][] = $drow; 
								}
							}
						}
					}
					
					$this->pdc->put('pdh_'.$this->strCacheKey.'_chunk_'.$intChunkID, $cache_result, null);
					$this->data[$intChunkID] = $cache_result;
					unset($cache_result);
					if (isset($this->data[$intChunkID][$intObjectID])) return true;
				} else {
					$this->data[$intChunkID] = $arrCacheData;
					if (isset($this->data[$intChunkID][$intObjectID])) return true;
				}
			} else {
				if (isset($this->data[$intChunkID][$intObjectID])) return true;			
			}
			return false;
		}
		
		
		/**
		 * Internal function for getting Object from Cache
		 * 
		 * @param integer $intObjectID
		 * @return array/boolean Data
		 */
		private function getObject($intObjectID){
			$blnResult = $this->initObject($intObjectID);
			if (!$blnResult) return false;
			$intChunkID = $this->calculateChunkID($intObjectID);
			if (isset($this->data[$intChunkID][$intObjectID])) return $this->data[$intChunkID][$intObjectID];
			
			return false;
		}
		
		/**
		 * Returns an Object; You can filter the returned data for a specific Column
		 * 
		 * @param integer $intObjectID
		 * @param string $strObjectTag
		 * @return array/boolean Data
		 */
		public function get($intObjectID, $strObjectTag = false){
			$dataSet = $this->getObject($intObjectID);
			if ($dataSet){
				if ($strObjectTag){
					if (isset($dataSet[$strObjectTag])) return $dataSet[$strObjectTag];
				} else return $dataSet;
			}
			return false;
		}
		
		
		/**
		 * Returns an Object direct from the Database; You can filter the returned data for a specific Column
		 * 
		 * @param integer $intObjectID
		 * @param string $strObjectTag
		 * @return array/boolean Data
		 */
		public function getDirect($intObjectID, $strObjectTag = false){
			$strQuery = (isset($this->arrQuerys['direct']) && strlen($this->arrQuerys['direct'])) ? $this->arrQuerys['direct'] : "SELECT * FROM ".$this->strTablename." WHERE ".$this->strID." = ?";
			$objQuery = $this->db->prepare($strQuery)->execute($intObjectID);
			if($objQuery){
				$row = $objQuery->fetchAssoc();
				if($strObjectTag){
					if (isset($row[$strObjectTag])) return $row[$strObjectTag];
				} else return $row;
			}
			return false;
		}
		
		/**
		 * Returns an Array where the Index is the ObjectID, and the Value is the ObjectTag Value
		 * E.g. getAssocTagDirect("item_name") returns array(1 => 'Axt', 2 => 'Handschuhe', ...)
		 * 
		 * @param string $strObjectTag
		 * @return array IDs
		 */
		public function getAssocTag($strObjectTag){
			$arrOut = array();
			foreach($this->index as $id){
				$tag = $this->get($id, $strObjectTag);
				if ($tag) $arrOut[$id] = $tag;
			}
			return $arrOut;
		}
		

		/**
		 * Returns an Array directly from the Database where the Index is the ObjectID, and the Value is the ObjectTag Value
		 * E.g. getAssocTagDirect("item_name") returns array(1 => 'Axt', 2 => 'Handschuhe', ...)
		 * 
		 * @param string $strObjectTag
		 * @return array IDs
		 */
		public function getAssocTagDirect($strObjectTag){
			$strQuery = (isset($this->arrQuerys['tag_direct']) && strlen($this->arrQuerys['tag_direct'])) ? $this->arrQuerys['tag_direct'] : "SELECT ".$this->strID.",".$strObjectTag." FROM ".$this->strTablename;
			
			$objQuery = $this->db->prepare($strQuery)->execute($strObjectTag);
			$arrOut = array();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrOut[$row[$this->strID]] = $row[$strObjectTag];
				}
			}
			return $arrOut;
		}
		

		/**
		 * Calculates the Chunk-ID for a ObjectID
		 * 
		 * @param integer $intObjectID
		 * @return number
		 */
		private function calculateChunkID($intObjectID){
			return ($intObjectID-($intObjectID%$this->intItemsPerChunk))/$this->intItemsPerChunk;
		}
		

		/**
		 * Reset Cache and Index
		 * 
		 * @param array $mixedIDs
		 * @return boolean
		 */
		public function reset($mixedIDs = false){
			//Delete Everything
			if ($mixedIDs === false) {
				$this->pdc->del_prefix('pdh_'.$this->strCacheKey);
				return true;
			}
			
			//Delete specific Objects
			$this->pdc->del('pdh_'.$this->strCacheKey.'_index'); //Delete Index
			if(!is_array($mixedIDs)) $mixedIDs = array($mixedIDs);
			foreach($mixedIDs as $id) {
				if(!is_numeric($id)) return $this->reset();
				$intChunkID = $this->calculateChunkID($id);
				$this->pdc->del('pdh_'.$this->strCacheKey.'_chunk_'.$intChunkID);
				if (isset($this->data[$intChunkID])) unset($this->data[$intChunkID]);
			}
			return true;
		}
		

		/**
		 * Sort for a specific column
		 * 
		 * @param string $strObjectTag Column
		 * @param string $strSortDirection Sort-direction
		 * @return array IDs
		 */
		public function sort($strObjectTag, $strSortDirection = "asc"){
			$strSortDirection = (strtolower($strSortDirection) == "asc") ? "ASC" : "DESC";
			$strQuery = (isset($this->arrQuerys['sort']) && strlen($this->arrQuerys['sort'])) ? $this->arrQuerys['sort'] : "SELECT ".$this->strID." FROM ".$this->strTablename." ORDER BY ";
			$strQuery .= $strObjectTag." ".$strSortDirection;
			
			$objQuery = $this->db->prepare($strQuery)->execute();
			$arrOut = array();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrOut[] = $row[$this->strID]; 
				}
			}
			return $arrOut;
		}
		
		


		/**
		 * Search for a specific Tag-Value
		 * 
		 * @param string $strObjectTag Column
		 * @param string $strSearchValue SearchValue
		 * @param boolean $allowParts False if Result must be identical with SearchValue, True if SearchValue can be a part of result
		 * @return array IDs
		 */
		public function search($strObjectTag, $strSearchValue, $allowParts=false){
			$strSearchValue = utf8_strtolower($strSearchValue);
			
			if ($allowParts){
				$escapedString = $this->db->escapeString('%'.$strSearchValue.'%');
				$strQuery = (isset($this->arrQuerys['search']) && strlen($this->arrQuerys['search'])) ? $this->arrQuerys['search'] : "SELECT ".$this->strID." FROM ".$this->strTablename." WHERE LOWER(".$strObjectTag.") LIKE ".$escapedString;	
			} else {
				$escapedString = $this->db->escapeString($strSearchValue);
				$strQuery = (isset($this->arrQuerys['search']) && strlen($this->arrQuerys['search'])) ? $this->arrQuerys['search'] : "SELECT ".$this->strID." FROM ".$this->strTablename." WHERE LOWER(".$strObjectTag.") = ".$escapedString;
			}

			$objQuery = $this->db->query($strQuery);
			$arrOut = array();
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$arrOut[] = $row[$this->strID];
				}
			}
			return $arrOut;
			
		}

	}
}
?>