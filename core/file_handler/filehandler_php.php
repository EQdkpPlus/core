<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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

		private function mkdir_r($name, $chmod=0777){
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
			
				if (is_file($folder.'/.htaccess')) return true;
				$htaccess = $this->FilePath($folder.'/.htaccess', $plugin, true);
				$blnWritten = $this->putContent($htaccess, "<Files *>\nOrder Allow,Deny\nDeny from All\n</Files>\n");
				return $blnWritten;
			}else{
				return true;	
			}
		}

		public function putContent($filename, $data){
			$intBits = @file_put_contents($filename, $data);
			@chmod($filename, 0777);
			return ($intBits !== false) ? true : false;
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
				$this->mkdir_r($path, 0777);
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
				@chmod($path, 0777);
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
			$blnResult = $this->copy($filename, $tofile);
			unlink ($filename);
			@chmod($tofile, 0777);
			
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
			}
			@chmod($thumbfolder.$filename, 0777);
		}
	}
}
?>