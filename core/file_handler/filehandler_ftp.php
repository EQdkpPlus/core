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

if (!class_exists("filehandler_ftp")) {
	class filehandler_ftp extends gen_class implements plus_filehandler
	{
		
		public $errors				= array();
		private $CacheFolder		= '';
		private $CacheFolderPlain	= '';
		private $ftp_Folder			= '';
		private $tmp_Folder			= '';
		private $ftp_Root			= '';
		private $ftp				= NULL;

		/**
		* Initiate the cacheHandler
		*/
		public function __construct($globalcache){
			// init the ftp
			$myDBName			= md5((($globalcache) ? $globalcache : $this->table_prefix.$this->dbname));
			$this->CacheFolder		= $this->root_path.'data/'.$myDBName.'/';
			$this->CacheFolderPlain = 'data/'.$myDBName.'/';
			
			//Correct ftproot-path
			if ($this->ftproot == '/') $this->ftproot = '';
			if(strlen($this->ftproot) && substr($this->ftproot,-1) != "/") {
				$this->ftproot .= '/';
			}
								
		}
		
		private function init_ftp(){
			if ($this->ftp == NULL){
				require_once('ftp_helper.class.php');
				$this->ftp = new ftp_handler($this->ftpuser, $this->ftppass, $this->ftphost, $this->ftpport);
				if ($this->ftp->showerror()){
					echo "FTP-Error: ".$this->ftp->showerror();
					$this->ftp = false;
					return false;
				} else {
					$this->ftp->setRootDir($this->ftproot);
					
					if($this->ftp->cd('data/')){
						
						
						$this->ftp_Folder		= $this->ftproot.$this->CacheFolderPlain;
						$this->ftp_Root			= $this->ftproot;
						$this->tmp_Folder		= $this->CacheFolder.'tmp/';	
						$this->ftp->cd('../');
						//Create data folder
						$blnResult = $this->CheckCreateFolder($this->CacheFolder);
						if (!$blnResult){
							echo 'FTP-Error: Could not create data-folder';
							$this->ftp = false;
							return false;
						}
					} else{
						echo 'FTP-Error: FTP Root dir not correct';
						$this->ftp = false;
						return false;
					}

					// We need the temp folder.. create it!
					$blnResult = $this->CheckCreateFolder('', 'tmp');
					$this->ftp->chmod($this->remove_rootpath($this->tmp_Folder), 0777);
					$this->ftp->setTempDir($this->tmp_Folder);
					if (!$blnResult){
						echo 'FTP-Error: Could not create tmp-folder';
						$this->ftp = false;
						return false;
					}
						
				}
			}
			return true;
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
			if (!$this->init_ftp()) return false;
			$name = $this->remove_rootpath($name);
			$this->ftp->mkdir_r($name, $chmod);
		}

		public function secure_folder($folder, $plugin=false, $deny_all=true){
			$this->make_index($folder, $plugin);

			//Create a .htaccess
			if($deny_all){
				if (!$this->CheckCreateFile($folder.'/.htaccess', $plugin, false)){	
					$htaccess = $this->FilePath($folder.'/.htaccess', $plugin, true);
					$blnWritten = $this->putContent($htaccess, "<Files *>\nOrder Allow,Deny\nDeny from All\n</Files>\n");
					return $blnWritten;
				} else {
					return true;
				}
			}else{
				return true;
			}
		}

		public function putContent($filename, $data){
			if (!$this->init_ftp()) return false;
			$filename = $this->remove_rootpath($filename);
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
			if (!$this->init_ftp()) return false;
			$file = ($plugin === false) ? $file : $this->FilePath($file, $plugin);
			$file = $this->remove_rootpath($file);
			return $this->ftp->file_size($file);
		}


		/**
		* Test if a file could be written
		*/
		public function testWrite($file=false){
			if (!$this->init_ftp()) return false;
			$file2check	= ($file) ? $file : $this->CacheFolder.'test_file.php';
			
			$test = $this->ftp->put_string($this->remove_rootpath($file2check), 'test');
			$write = is_file($file2check);
			$this->Delete($file2check);
			return $write;
		}

		public function is_writable($file, $testfile=false){
			if($testfile){
				return $this->testWrite();
			}else{
				return 'ttt';
			}
		}

		/**
		* Check if a Folder is available or must be created
		*/
		public function CheckCreateFolder($path, $plugin=false){
			$path = ($plugin === false) ? $path : $this->CacheFolder.$plugin.'/'.$path;
			if(substr($path,-1) != "/") {
				$path .= '/';
			}
			$path = $this->remove_rootpath($path);
			if(!is_dir($this->root_path.$path)){
				if (!$this->init_ftp()) return false;
				$this->ftp->mkdir_r($path);
			}

			return is_dir($this->root_path.$path);
		}

		/**
		* Check if a filename contains a folder and creates it if required
		*/
		public function CheckCreateSubfolder($filename, $basefolder = ''){
			if(strpos($filename, '/')) {
				$path = pathinfo($filename, PATHINFO_DIRNAME);
				if ($basefolder != '') $basefolder = $basefolder.'/';
				$this->CheckCreateFolder($basefolder.$path);
			}
		}

		/**
		* Check if a File is available or must be created
		*/
		public function CheckCreateFile($path, $plugin=false, $blnCreate=true){
			
			$path = ($plugin === false) ? $path : $this->CacheFolder.$plugin.'/'.$path;
			
			$path = $this->remove_rootpath($path);
			
			if(!is_file($this->root_path.$path) && $blnCreate){
				if (!$this->init_ftp()) return false;
				$this->ftp->put_string($path, '');
			}
			if(is_file($this->root_path.$path)){
				if (!$this->init_ftp()) return false;
				//$this->ftp->chmod($path, 0777);
			}
			return is_file($this->root_path.$path);
		}

		/**
		* Copy a File/Folder
		*/
		public function copy($source, $dest){
			if (!$this->init_ftp()) return false;
			$this->CheckCreateSubfolder($dest);
			$source = $this->remove_rootpath($source);
			$dest = $this->remove_rootpath($dest);
			return $this->ftp->ftp_copy($source, $dest);
		}

		/**
		* Rename a File/Folder
		*/
		public function rename($old_file, $new_file){
			if (!$this->init_ftp()) return false;
			$old_file = $this->remove_rootpath($old_file);
			$new_file = $this->remove_rootpath($new_file);
			$result = $this->ftp->rename($old_file, $new_file);
			$this->ftp->chmod($new_file, 0777);
			return $result;
		}

		/**
		* Delete a File/Folder
		*/
		public function Delete($path, $plugin=false){
			if (!$this->init_ftp()) return false;
			$path = ($plugin === false) ? $path : $this->CacheFolder.$plugin.'/'.$path;
			
			$path = $this->remove_rootpath($path);
			
			if(is_dir($this->root_path.$path)){
				$this->ftp->ftp_rmdirr($path);
			}else{
				$this->ftp->delete($path);
			}
		}

		/**
		* If you want to move a file..
		*/
		public function FileMove($filename, $tofile, $tmpmove=false) {
			if (!$this->init_ftp()) return false;
			$filename = $this->remove_rootpath($filename);
			$tofile = $this->remove_rootpath($tofile);
			
			if($tmpmove){
				$this->ftp->moveuploadedfile($filename, $tofile);
			}else{
				if($this->ftp->rename($filename, $tofile, $tmpmove)){
					return true;
				}else{
					return false;
				}
			}
		}

		/**
		* returns false or modification date of a file.
		*/
		public function FileDate($filename, $plugin=false){	
			$filename = $this->FilePath($filename, $plugin);

			if(is_file($filename)){
				if (!$this->init_ftp()) return false;
				$output = $this->ftp->filedate($this->remove_rootpath($filename));
			}
			return (($output) ? $output : false);
		}

		/**
		* create a thumbnail of an image to a specified folder
		*/
		public function thumbnail($image, $thumbfolder, $filename, $resize_value=400){
			if(is_file($image)){
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
	
					// create the thumb in a temporary folder, copy to ftp &
					ImageCopyResampled($img, $imgOld, 0,0, 0,0, $resize_value,$heightA, ImageSX($imgOld),ImageSY($imgOld));
					switch($imageInfo[2]){
						case 1:	ImageGIF($img,	$this->tmp_Folder.$filename);		break;	// GIF
						case 2:	ImageJPEG($img,	$this->tmp_Folder.$filename, 95);		break;	// JPG
						case 3:	ImagePNG($img,	$this->tmp_Folder.$filename, 0);		break;	// PNG
					}
				}
				$this->rename($this->tmp_Folder.$filename, $thumbfolder.$filename);
				$this->Delete($this->tmp_Folder.$filename);
			}
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