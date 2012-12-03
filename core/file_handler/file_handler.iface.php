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
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_filehandler" ) ){
	interface plus_filehandler{
		public function secure_folder($folder, $plugin=false, $deny_all=true);
		public function CheckCreateFolder($path, $plugin=false);
		public function CheckCreateFile($path, $plugin=false, $blnCreate=true);
		public function Delete($path, $plugin=false);
		public function FileDate($filename, $plugin=false);
		public function FilePath($path, $plugin=false, $blnCreateFile=true);
		public function FolderPath($foldername, $plugin=false, $blnPlain = false);
		public function FileSize($file, $plugin=false);
						
		public function testWrite($file=false);
		public function copy($source, $dest);
		public function rename($old_file, $new_file);
		public function FileMove($filename, $tofile, $tmpmove=false);
		public function thumbnail($image, $thumbfolder, $filename, $resize_value=400);
		public function putContent($filename, $data);
		public function is_writable($file, $testfile=false);
		public function get_errors();
		public function get_cachefolder($blnPlain=false);
	}//end interface
}//end if
?>