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
	class filehandler_ftp implements plus_filehandler
	{
		public $errors   		= array();
		private $CacheFolder	= '';
		private $CacheFolder2	= '';
		private $ftp_Folder		= '';
		private $tmp_Folder		= '';

		/**
		* Initiate the cacheHandler
		*/
		public function __construct($globalcache){
			global $dbname, $core, $eqdkp_root_path;
			global $ftphost, $ftpport, $ftpuser, $ftppass, $ftproot;

			// init the ftp
			$myDBName			= md5((($globalcache) ? $globalcache : $dbname));

			require_once('ftp_helper.class.php');
			$this->ftp = new ftp_handler($ftpuser, $ftppass, $ftphost, $ftpport);

			if($this->ftp->showerror()){
				$this->errors[] = $this->ftp->showerror();
				return;
			}else{
				$this->ftp->setRootDir($ftproot);
				if($this->ftp->cd('data/')){
					$this->CacheFolder	= $eqdkp_root_path.'data/'.$myDBName.'/';
					$this->CacheFolder2 = 'data/'.$myDBName.'/';
					$this->ftp_Folder	= $ftproot.'data/'.$myDBName.'/';
					$this->tmp_Folder	= $this->CacheFolder.'tmp/';
					$this->CheckCreateFolder('data/'.$myDBName);
				}else{
					$this->errors[] = 'lib_cache_notwriteable';
				}
				$this->ftp->setRootDir($this->ftp_Folder);
				
				// We need the temp folder.. create it!
				$this->CheckCreateFolder('tmp/');
				$this->ftp->chmod('tmp/', 0777);
				$this->ftp->setTempDir($this->tmp_Folder);
				
				
				// The Cache for PCLZIP
				if(!$globalcache){
					define('PCLZIP_TEMPORARY_DIR', $this->CacheFolder.'pclzip/');
					$this->CheckCreateFolder('pclzip/');
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
			$this->ftp->mkdir_r($name, $chmod=0777);
		}

		public function secure_folder($foldername, $plugin='', $deny_all=true){
			$this->make_index($plugin.'/'.$foldername, false);

			//Create a .htaccess
			if($deny_all){
				return $this->putContent("<Files *>\nOrder Allow,Deny\nDeny from All\n</Files>\n", $this->FolderPath($foldername, $plugin, false).".htaccess");
			}else{
				return true;	
			}
		}

		public function putContent($data, $filename){
			$filename = $this->cleanFTPpath($filename);
			return $this->ftp->put_string($filename, $data);
		}

		/**
		* Return a path to the file
		* 
		* @param $filename    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @param $createFile  Should the file be created on check if not available?    
		* @return Link to the file
		*/
		public function FilePath($filename, $plugin='', $createFile=true){
			if(!$filename){ return ''; }
			if($plugin != ""){
				$tmpfolder = $this->CacheFolder.$plugin;
				$this->CheckCreateFolder($plugin);
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
			$tmpfolder = (($plugin) ? $plugin : '');
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
			return ($plain) ? $this->CacheFolder.$tmpfilelink : $tmpfilelink;
		}
		
		/**
		* Get the filesize of a file
		*/
		public function FileSize($file){
			$file = $this->cleanFTPpath($file);
			return $this->ftp->file_size($file);
		}
		
		/**
		* Remove the folder vars out of the path
		*/
		private function cleanFTPpath($input){
			return str_replace($this->CacheFolder, '', $input);
		}
		
		/**
		* Test if a file could be written
		*/
		public function CheckWrite(){
			return true;
		}

		/**
		* Check if a Folder is available or must be created
		*/
		public function CheckCreateFolder($path){
			$path = $this->cleanFTPpath($path);
			if(!$this->ftp->is_dir($path)){
				$this->ftp->mkdir_r($path);
			}
		}

		/**
		* Check if a File is available or must be created
		*/
		public function CheckCreateFile($path, $createFile){
			$path = $this->cleanFTPpath($path);
			if(!is_file($this->ftp_Folder.$path) && $createFile){
				$this->ftp->put_string($path, '');
			}
			if(is_file($this->ftp_Folder.$path)){
				$this->ftp->chmod($path, 0777);
			}
		}

		/**
		* Copy a File/Folder
		*/
		public function copy($source, $dest){
			$source	= $this->cleanFTPpath($source);
			$dest	= $this->cleanFTPpath($dest);
			$this->ftp->ftp_copy($source, $dest);
		}
		
		/**
		* Rename a File/Folder
		*/
		public function rename($old_file, $new_file){
			$old_file	= $this->cleanFTPpath($old_file);
			$new_file	= $this->cleanFTPpath($new_file);
			$this->ftp->rename($old_file, $new_file);
		}

		/**
		* Delete a File/Folder
		*/
		public function Delete($dir, $folder=false){
			$dir = $this->cleanFTPpath($dir);
			if($folder){
				$this->ftp->ftp_rmdirr($dir);
			}else{
				$this->ftp->delete($dir);
			}
		}

		/**
		* If you want to move a file..
		*/
		public function FileMove($filename, $tofile) {
			if($this->ftp->rename($filename, $tofile)){
				$this->ftp->chmod($tofile, 0777);
				return true;
			}else{
				return false;	
			}
		}

		/**
		* returns false or modification date of a file.
		*/
		public function FileDate($filename, $plugin=''){
			$filename = $this->FilePath($filename, $plugin, false);
			if(is_file($filename)){
				$filename = $this->cleanFTPpath($filename);
				$output = $this->ftp->filedate($filename);
			}
			return (($output) ? $output : false);
		}
	}
}
?>