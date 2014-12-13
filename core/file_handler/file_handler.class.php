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
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("file_handler")) {
	class file_handler extends gen_class {
		/**
		* Initiate the cacheHandler
		*/
		private $fhandler = false;
		
		public function __construct($globalcache=false, $fhandler=false) {
			if(!$fhandler) $fhandler = (isset($this->use_ftp) && $this->use_ftp) ? 'filehandler_ftp' : 'filehandler_php';
			if(!interface_exists( "plus_filehandler" )) require_once($this->root_path . 'core/file_handler/file_handler.iface.php');
			require($this->root_path . 'core/file_handler/'.$fhandler.'.php');
			$this->fhandler				= new $fhandler($globalcache);
		}

		/**
		* Functions optimized for data-folder. When using outside of data-Folder, set $plugin false.
		*
		* Example:
		* Using with data-Folder: $pfh->secure_folder('backup', 'eqdkp');
		* Using outside data-Folder: $pfh->secure_folder($this->root_path.'templates', FALSE);
		*/
		
		public function secure_folder($foldername, $plugin=false, $deny_all=true){
			return $this->fhandler->secure_folder($foldername, $plugin, $deny_all);
		}
		
		public function CheckCreateFolder($path, $plugin=false){
			return $this->fhandler->CheckCreateFolder($path, $plugin);
		}
		
		public function CheckCreateFile($path, $plugin=false, $blnCreate=true){
			return $this->fhandler->CheckCreateFile($path, $plugin, $blnCreate);
		}
		
		public function Delete($path, $plugin=false){
			return $this->fhandler->Delete($path, $plugin);
		}

		public function FileDate($filename, $plugin=false){
			return $this->fhandler->FileDate($filename, $plugin);
		}

		public function FilePath($path, $plugin=false, $blnCreateFile=true, $linkType = 'relative'){
			$strFilePath = $this->fhandler->FilePath($path, $plugin, $blnCreateFile);
			
			switch ($linkType){
				case 'relative': return $strFilePath;
				break;
				
				case 'absolute': return registry::register('environment')->link.$this->remove_rootpath($strFilePath);
				break;
				
				case 'serverpath' : return $this->server_path.$this->remove_rootpath($strFilePath);
				break;
				
				default: return $this->remove_rootpath($strFilePath);
			}
		}

		public function FolderPath($foldername, $plugin=false, $linkType = 'relative'){
			$strFilePath = $this->fhandler->FolderPath($foldername, $plugin, false);
			switch ($linkType){
				case 'relative': return $strFilePath;
				break;
				
				case 'absolute': return registry::register('environment')->link.$this->remove_rootpath($strFilePath);
				break;
				
				case 'serverpath' : return $this->server_path.$this->remove_rootpath($strFilePath);
				break;
				
				default: return $this->remove_rootpath($strFilePath);
			}
			
		}

		public function FileSize($file, $plugin=false){
			return $this->fhandler->FileSize($file,$plugin);
		}
		
		/**
		* Return a file link to the file
		* 
		* @param $path		Path, including file
		* @param $plugin    Plugin name, p.e. 'raidplan'
		* @param $linkType  plain/absolute/relative
		* @return Link to the file
		*/
		public function FileLink($path, $plugin=false, $linkType = 'plain'){
			if ($plugin === false){
				$link = $path;
			} else {
				$cachefolder = $this->fhandler->get_cachefolder(true);
				
				$link = $cachefolder.$plugin;
				if ($path != "") $link .= '/'.$path;
			}
			
			switch ($linkType){
				case 'relative': return $link;
				break;
				
				case 'absolute': return registry::register('environment')->link.$this->remove_rootpath($link);
				break;
				
				case 'serverpath': return $this->server_path.$this->remove_rootpath($link);
				break;
				
				default: return $this->remove_rootpath($link);
			}
		}
		
		/**
		* The following functions need filename or foldername with $this->root_path
		*
		* Example:
		* $pfh->copy($this->root_path.'/templates/file.php', $this->root_path.'/libraries/test.php');
		*/

		public function testWrite($file=false){
			return $this->fhandler->testWrite($file);
		}
		
		public function copy($source, $dest){
			return $this->fhandler->copy($source, $dest);
		}

		public function rename($old_file, $new_file){
			return $this->fhandler->rename($old_file, $new_file);
		}

		public function FileMove($filename, $tofile, $tmpmove=false){
			return $this->fhandler->FileMove($filename, $tofile, $tmpmove);
		}

		public function thumbnail($image, $thumbfolder, $filename, $resize_value=400){
			return $this->fhandler->thumbnail($image, $thumbfolder, $filename, $resize_value);
		}

		public function putContent($filename, $data){
			return $this->fhandler->putContent($filename, $data);
		}
		
		public function addContent($filename, $data){
			return $this->fhandler->addContent($filename, $data);
		}

		public function is_writable($file, $testfile=false){
			return $this->fhandler->is_writable($file, $testfile);
		}

		public function get_errors(){
			return $this->fhandler->get_errors();
		}
		
		public function get_cachefolder($blnPlain=false){
			return $this->fhandler->get_cachefolder($blnPlain);
		}

		/**
		* Check if the cache is writable
		* @return bool
		*/
		public function CacheWritable(){
			$errors = $this->fhandler->get_errors();
			return (is_array($errors) && count($errors) > 0) ? false : true;
		}

		private function remove_rootpath($string){
			if (strpos($string, $this->root_path) === 0){
				return substr($string, strlen($this->root_path));
			}
			return $string;
		}
	}
}
?>