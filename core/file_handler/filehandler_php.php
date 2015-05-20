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

if (!class_exists("filehandler_php")) {
	class filehandler_php extends gen_class implements plus_filehandler
	{

		public $errors				= array();
		public $CacheFolder			= '';
		public $CacheFolderPlain	= '';

		/**
		* Initiate the cacheHandler
		*/
		public function __construct($globalcache){
			$myDBName		= md5((($globalcache) ? $globalcache : $this->table_prefix.$this->dbname));
			if(is_writable($this->root_path.'data/')){
				$this->CacheFolder			= $this->root_path.'data/'.$myDBName.'/';
				$this->CacheFolderPlain 	= 'data/'.$myDBName.'/';
				
				//Create cache folder
				$this->CheckCreateFolder($this->CacheFolder, false);
				$this->CheckCreateFolder('', 'tmp');
				
			} else {
				if(!$this->CheckCreateFolder($this->root_path.'data/', false) || !is_writable($this->root_path.'data/')){
					$this->errors[] = 'lib_cache_notwriteable';
				}
			}
		}

		public function get_errors(){
			return $this->errors;
		}

		public function get_cachefolder($blnPlain=false){
			return (($blnPlain) ? $this->CacheFolderPlain : $this->CacheFolder);
		}

		//Creates an empty index.html to prevent directory-listening if .htaccess doesn't work
		private function make_index($path, $plugin=false){

			return $this->FilePath($path.'/index.html', $plugin);
		}

		private function mkdir_r($name, $chmod=0775){
			$dirs = explode('/', $name);
			$dir	= $part = '';
			foreach ($dirs as $part) {
				$dir.=$part.'/';
				if (!is_dir($dir) && strlen($dir)>0){
					$result = mkdir($dir, $chmod);
					if ($result) $this->make_index($dir);				
				}
			}
		}

		public function secure_folder($folder, $plugin=false, $deny_all=true){

			$this->make_index($folder, $plugin);
			//Create a .htaccess
			if($deny_all){
				$htaccess = $this->FilePath($folder.'/.htaccess', $plugin, true);
				$blnWritten = $this->putContent($htaccess, "<Files *>\nOrder Allow,Deny\nDeny from All\n</Files>\n");
				return $blnWritten;
			}else{
				return true;	
			}
		}

		public function putContent($filename, $data){
			$intBits = file_put_contents($filename, $data);
			if(!$this->on_iis()) @chmod($filename, $this->get_chmod());
			return ($intBits !== false) ? true : false;
		}
		
		public function addContent($filename, $data){
			$tmpHandle = fopen($filename, 'a');
			if ($tmpHandle){
				$intBits = fwrite($tmpHandle, $data);
				fclose($tmpHandle);
				return ($intBits !== false) ? true : false;
			}
			return false;
		}

		/**
		* Return a path to the file
		* 
		* @param $filepath    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @param $createFile  Should the file be created on check if not available?    
		* @return Link to the file
		*/
		public function FilePath($filepath, $plugin=false, $blnCreateFile=true){
			if(!strlen($filepath)) return '';
			
			if ($plugin === false){
				$this->CheckCreateSubfolder($filepath, $this->root_path);
				$this->CheckCreateFile($filepath, $plugin, $blnCreateFile);
				return $filepath;
			} else {
				$pluginFolder = $this->CacheFolder.$plugin;
				$this->CheckCreateFolder($pluginFolder);
				$fileLink = $pluginFolder.'/'.$filepath;
				$this->CheckCreateSubfolder($filepath, $pluginFolder);
				$this->CheckCreateFile($filepath, $plugin, $blnCreateFile);
				
				return $fileLink;
			}
		}

		/**
		* Return a path to a folder
		* 
		* @param $filename    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @return Link to the file
		*/
		public function FolderPath($foldername, $plugin=false, $blnPlain = false){
			if (is_array($foldername)){
				$foldername = implode("/",$foldername);
			}
			
			if(substr($foldername,-1) != "/") {
				$foldername .= '/';
			}
			
			$this->CheckCreateFolder($foldername, $plugin);
			
			if ($plugin === false){
				$this->CheckCreateFolder($foldername, $plugin);
				return $foldername;
			} else {
				$this->CheckCreateFolder($foldername, $plugin);			
				return ($blnPlain) ? $this->CacheFolderPlain.$plugin.'/'.$foldername : $this->CacheFolder.$plugin.'/'.$foldername;
			}
		}
		
		/**
		* Get the filesize of a file
		*/
		public function FileSize($file, $plugin=false){
			if ($plugin === false){
				return filesize($file);
			} else {
				return filesize($this->FilePath($file, $plugin));
			}
		}
		
		/**
		* Test if a file could be written
		*/
		public function testWrite($file=false){
			$file2check	= ($file) ? $file : $this->CacheFolder.'test_file';
			$write = $this->putContent($file2check, 'test');
			$this->Delete($file2check);
			return $write;
		}

		public function is_writable($file, $testfile=false){
			if($testfile){
				return $this->testWrite();
			}else{
				return is_writable($file);
			}
		}

		/**
		* Check if a Folder is available or must be created
		*/
		public function CheckCreateFolder($path, $plugin=false){
			$path = (($plugin === false) ? $path : $this->CacheFolder.$plugin.'/'.$path);

			if(!is_dir($path)){
				$old = umask(0); 
				$this->mkdir_r($path, $this->get_chmod(true));
				umask($old);
			}
			return (is_dir($path)) ? true : false;
		}

		/**
		* Check if a filename contains a folder and creates it if required
		*/
		public function CheckCreateSubfolder($filename, $basefolder){
			
			if(strpos($filename, '/')) {
				$folders = explode('/', $filename);
				unset($folders[max(array_keys($folders))]);
				foreach($folders as $folder) {
					if ($folder == '.' || $folder == '..' || $folder == '') continue;
					$this->CheckCreateFolder($basefolder.'/'.$folder);
					$basefolder .= '/'.$folder;
				}
			}
		}

		/**
		* Check if a File is available or must be created
		*/
		public function CheckCreateFile($path, $plugin=false, $blnCreate=true){
			
			$path = ($plugin === false) ? $path : $this->CacheFolder.$plugin.'/'.$path;
			
			if(!is_file($path) && $blnCreate){
				$myhandl = @fopen($path, "w");
				if(@is_resource($myhandl)){
					@fclose($myhandl);
				}
			}
			if(is_file($path)){
				if(!$this->on_iis()) @chmod($path, $this->get_chmod());
				return true;
			}
			
			return false;
		}

		/**
		* Copy a File/Folder
		*/
		public function copy($source, $dest){
			$this->CheckCreateSubfolder($dest, $this->root_path);
			return copy($source, $dest);
		}
		
		/**
		* Rename a File/Folder
		*/
		public function rename($old_file, $new_file){
			return rename($old_file, $new_file);
		}

		/**
		* Delete a File/Folder V3
		*/
		public function Delete($path, $plugin=false) {
			$directory = ($plugin === false) ? $path : $this->CacheFolder.$plugin.'/'.$path;
		
			if(is_file($directory)){
				// its a file, remove it!
				@unlink($directory);
			}else{
				if(substr($directory,-1) == "/") {
					$directory = substr($directory,0,-1);
				}

				if(!file_exists($directory) || !is_dir($directory)) {
					return false;
				} elseif(!is_readable($directory)) {
					return false;
				} else {
					$directoryHandle = opendir($directory);
		
					while ($contents = readdir($directoryHandle)) {
						if($contents != '.' && $contents != '..') {
							$path = $directory . "/" . $contents;

							if(is_dir($path)) {
								$this->Delete($path);
							} else {
								unlink($path);
							}
						}
					}

					closedir($directoryHandle);
					if(!rmdir($directory)) {
							return false;
					}

					return true;
				}
			}
		}

		/**
		* If you want to move a file..
		*/
		public function FileMove($filename, $tofile, $tmpmove=false) {
			$blnResult = $this->rename($filename, $tofile);
			#unlink($filename);
			if(!$this->on_iis()) @chmod($tofile, $this->get_chmod());
			
			return $blnResult;
		}

		/**
		* returns false or modification date of a file.
		*/
		public function FileDate($filename, $plugin=false){
			$filename = $this->FilePath($filename, $plugin);
			if(is_file($filename)){
				$output = filemtime($filename);
			}
			return (($output) ? $output : false);
		}

		/**
		* create a thumbnail of an image to a specified folder
		*/
		public function thumbnail($image, $thumbfolder, $filename, $resize_value=400){
			// Create the new image
			$imageInfo		= GetImageSize($image);
			$filename		= ($filename) ? $filename : $image;
			switch($imageInfo[2]){
				case 1:	$imgOld = ImageCreateFromGIF($image);	break;	// GIF
				case 2:	$imgOld = ImageCreateFromJPEG($image);	break;	// JPG
				case 3:
					$imgOld = ImageCreateFromPNG($image);
					imageAlphaBlending($imgOld, false);
					imageSaveAlpha($imgOld, true);
					break;	// PNG
			}

			// variables...
			$width			= $imageInfo[0];
			$height			= $imageInfo[1];

			// Resize me!
			if($width > $resize_value){
				$scale		= $resize_value/$width;
				$heightA	= round($height * $scale);
				$img		= ImageCreateTrueColor($resize_value,$heightA);

				// This is a fix for transparent 24bit png...
				if($imageInfo[2] == 3){
					imagefill($img, 0, 0, imagecolorallocatealpha($img, 0, 0, 0, 127));
					imageSaveAlpha($img, true);
				}

				ImageCopyResampled($img, $imgOld, 0,0, 0,0, $resize_value,$heightA, ImageSX($imgOld),ImageSY($imgOld));
				switch($imageInfo[2]){
					case 1:	ImageGIF($img,	$thumbfolder.$filename);	break;	// GIF
					case 2:	ImageJPEG($img,	$thumbfolder.$filename, 95);	break;	// JPG
					case 3:	ImagePNG($img,	$thumbfolder.$filename, 0);	break;	// PNG
				}
			} else {
				$this->copy($image, $thumbfolder.$filename);
			}
			
			
			if(!$this->on_iis()) @chmod($thumbfolder.$filename, $this->get_chmod());
		}
		

		//These methods here have been defined somewhere else. But the pfh is called so early in super registry, that they are not available when pfh needs it.
		//Therefore they have been redeclared here.
		
		private function on_iis() {
			$sSoftware = strtolower( $_SERVER["SERVER_SOFTWARE"] );
			if ( strpos($sSoftware, "microsoft-iis") !== false )
				return true;
			else
				return false;
		}
		
		private function get_chmod(){
			if(defined('CHMOD')) return CHMOD;
			return 0775;
		}
	}
}
?>