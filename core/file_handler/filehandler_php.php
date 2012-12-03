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
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !interface_exists( "plus_filehandler" ) ) {
	require_once($eqdkp_root_path . 'core/file_handler/file_handler.iface.php');
}

if (!class_exists("filehandler_php")) {
	class filehandler_php implements plus_filehandler
	{
		var $errors   		= array();
		var $CacheFolder	= '';
		var $CacheFolder2	= '';

		/**
		* Initiate the cacheHandler
		*/
		public function __construct($globalcache){
			global $dbname, $eqdkp_root_path;

			$myDBName		= md5((($globalcache) ? $globalcache : $dbname));
			if(is_writable($eqdkp_root_path.'data/')){
				$this->CacheFolder	= $eqdkp_root_path.'data/'.$myDBName.'/';
				$this->CacheFolder2 = 'data/'.$myDBName.'/';

				// The Cache for PCLZIP
				if(!$globalcache){
					define('PCLZIP_TEMPORARY_DIR', $this->CacheFolder.'pclzip/');
					$this->CheckCreateFolder($this->CacheFolder.'pclzip/');
				}
				$this->CheckCreateFolder($this->CacheFolder);
			}else{
				if(!$this->CheckCreateFolder($eqdkp_root_path.'data/')){
					$this->errors[] = 'lib_cache_notwriteable';
				}
			}
		}

		public function get_errors(){
			return $this->errors;
		}

		public function get_cachefolder($full=false){
			return (($full) ? $this->CacheFolder2 : $this->CacheFolder);
		}

		//Creates an empty index.html to prevent directory-listening if .htaccess doesn't work
		private function make_index($path, $use_path=true){
			return $this->FilePath('index.html', $path, true, $use_path);
		}

		private function mkdir_r($name, $chmod=0777){
			$dirs = explode('/', $name);
			$dir	= $part = '';
			foreach ($dirs as $part) {
				$dir.=$part.'/';
				if (!is_dir($dir) && strlen($dir)>0){
					mkdir($dir, $chmod);
					$this->make_index($dir);
				}
			}
		}

		public function secure_folder($foldername, $plugin='', $deny_all=true){
			$this->make_index($plugin.'/'.$foldername, false);
			//Create a .htaccess
			if($deny_all){
				$htaccess	= fopen($this->FolderPath($foldername, $plugin).".htaccess", "w");
				$result		= fputs($htaccess, "<Files *>\nOrder Allow,Deny\nDeny from All\n</Files>\n");
				fclose($htaccess);
				return ($result > 0) ? 1 : 0;
			}else{
				return true;	
			}
		}

		public function putContent($data, $filename){
			file_put_contents($filename, $data);
		}

		/**
		* Return a path to the file
		* 
		* @param $filename    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @param $createFile  Should the file be created on check if not available?    
		* @return Link to the file
		*/
		public function FilePath($filename, $plugin='', $createFile=true, $fullpath=false){
			if(!$filename){ return ''; }
			if($fullpath){
				$plugin = (substr($plugin, -1) == "/") ? $plugin : $plugin.'/';
				return $this->CheckCreateFile($plugin.$filename, $createFile);
			}elseif($plugin != ""){
				$tmpfolder = $this->CacheFolder.$plugin;
				$this->CheckCreateFolder($tmpfolder);
				$tmpfilelink=$tmpfolder.'/'.$filename;
				$this->CheckCreateFile($tmpfilelink, $createFile);
				return $tmpfilelink;
			}else{
				return $this->CacheFolder.$filename;
			}
		}

		/**
		* Return a path to a folder
		* 
		* @param $filename    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @return Link to the file
		*/
		public function FolderPath($foldername, $plugin='', $plain=true){
			$tmpfolder = ($plugin) ? $this->CacheFolder.$plugin : $this->CacheFolder;
			$this->CheckCreateFolder($tmpfolder);

			if(is_array($foldername)){
				$mytmpfoldr = $tmpfolder;
				foreach($foldername as $fname){
					$mytmpfoldr = $mytmpfoldr.'/'.$fname;
					$this->CheckCreateFolder($mytmpfoldr);
				}
				$myFolders = implode("/",$foldername);
			}else{
				$myFolders = $foldername;
			}

			$tmpfilelink=$tmpfolder.'/'.$myFolders.'/';
			$this->CheckCreateFolder($tmpfilelink);
			return $tmpfilelink;
		}
		
		/**
		* Get the filesize of a file
		*/
		public function FileSize($file){
			return filesize($file);
		}
		
		/**
		* Test if a file could be written
		*/
		public function CheckWrite(){
			$write = false;
			$fp = @fopen($this->CacheFolder.'test_file', 'wb');
			if ($fp !== false){
				$write = true;
			}
			@fclose($fp);
			@unlink($this->CacheFolder.'test_file');
			return $write;
		}

		/**
		* Check if a Folder is available or must be created
		*/
		public function CheckCreateFolder($path){
			if(!is_dir($path)){
				$old = umask(0); 
				$this->mkdir_r($path, 0777);
				umask($old);
			}
			return (is_dir($path)) ? true : false;
		}

		/**
		* Check if a File is available or must be created
		*/
		public function CheckCreateFile($path, $createFile){
			if(!is_file($path) && $createFile){
				$myhandl = fopen($path, "w");
				if(is_resource($myhandl)){
					fclose($myhandl);
				}
			}
			if(is_file($path)){
				@chmod($path, 0777);
				return true;
			}
		}

		/**
		* Copy a File/Folder
		*/
		public function copy($source, $dest){
			copy($source, $dest);
		}
		
		/**
		* Rename a File/Folder
		*/
		public function rename($old_file, $new_file){
			rename($old_file, $new_file);
		}

		/**
		* Delete a File/Folder V3
		*/
		function Delete($directory, $empty = false) {
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

					if($empty == false) {
						if(!rmdir($directory)) {
							return false;
						}
					}

					return true;
				}
			}
		}

		/**
		* If you want to move a file..
		*/
		public function FileMove($filename, $tofile) {
			copy($filename, $tofile);
			unlink ($filename);
			@chmod($tofile, 0777);
		}

		/**
		* returns false or modification date of a file.
		*/
		public function FileDate($filename, $plugin=''){
			$filename = $this->FilePath($filename, $plugin);
			if(is_file($filename)){
				$output = filemtime($filename);
			}
			return (($output) ? $output : false);
		}
	}
}
?>