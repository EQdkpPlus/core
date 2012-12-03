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

if (!class_exists("file_handler")) {
	class file_handler
	{

		/**
		* Initiate the cacheHandler
		*/
		public function __construct($globalcache=false, $fhandler='filehandler_php'){
			global $eqdkp_root_path;
			require_once($eqdkp_root_path . 'core/file_handler/'.$fhandler.'.php');
			$this->fhandler				= new $fhandler($globalcache);
		}

		public function secure_folder($foldername, $plugin='', $deny_all=true){
			return $this->fhandler->secure_folder($foldername, $plugin, $deny_all);
		}

		public function CheckWrite(){
			return $this->fhandler->CheckWrite();
		}

		public function CheckCreateFolder($path){
			return $this->fhandler->CheckCreateFolder($path);
		}

		public function CheckCreateFile($path, $createFile){
			return $this->fhandler->CheckCreateFile($path, $createFile);
		}

		public function Delete($dir, $deletefolder=false){
			return $this->fhandler->Delete($dir, $deletefolder);
		}

		public function copy($source, $dest){
			return $this->fhandler->copy($source, $dest);
		}

		public function rename($old_file, $new_file){
			return $this->fhandler->rename($old_file, $new_file);
		}

		public function FileMove($filename, $tofile){
			return $this->fhandler->FileMove($filename, $tofile);
		}

		public function FileDate($filename, $plugin=''){
			return $this->fhandler->FileDate($filename, $plugin);
		}

		public function FilePath($filename, $plugin='', $createFile=true){
			return $this->fhandler->FilePath($filename, $plugin, $createFile);
		}

		public function FileSize($file){
			return $this->fhandler->FileSize($file);
		}

		public function FolderPath($foldername, $plugin='', $plain=true){
			return $this->fhandler->FolderPath($foldername, $plugin, $plain);
		}

		public function putContent($data, $filename){
			return $this->fhandler->putContent($data, $filename);
		}

		/**
		* Check if the cache is writable
		*/
		public function CacheWritable(){
			$errors = $this->fhandler->get_errors();
			return (is_array($errors) && count($errors) > 0) ? false : true;
		}

		/**
		* Checks if a file is available or not
		* 
		* @param $filename    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @return 1/0
		*/
		public function FileExists($filename, $plugin=''){
			if(is_file($this->fhandler->FilePath($filename, $plugin, true))){
				return 1;
			}else{
				return 0;
			}
		}

		/**
		* Return a file link to the file
		* 
		* @param $filename    The name of the file
		* @param $plugin      Plugin name, p.e. 'raidplan'
		* @return Link to the file
		*/
		public function FileLink($filename, $plugin='', $folder=''){
			$realfolder = ($folder) ? $folder.'/' : '';
			$cachefolder = $this->fhandler->get_cachefolder(true);
			if($plugin != ""){
				return $cachefolder.$plugin.'/'.$realfolder.$filename;
			}else{
				return $cachefolder.$realfolder.$filename;
			}
		}
	}
}
?>