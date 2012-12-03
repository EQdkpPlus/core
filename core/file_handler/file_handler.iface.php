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
	die('Do not access this file directly.');
}

if ( !interface_exists( "plus_filehandler" ) ){
	interface plus_filehandler{
		public function secure_folder($foldername, $plugin='', $deny_all=true);
		public function CheckWrite();
		public function CheckCreateFolder($path);
		public function CheckCreateFile($path, $createFile);
		public function Delete($dir, $deletefolder=false);
		public function copy($source, $dest);
		public function rename($old_file, $new_file);
		public function FileMove($filename, $tofile);
		public function FileDate($filename, $plugin='');
		public function FilePath($filename, $plugin='', $createFile=true);
		public function FolderPath($foldername, $plugin='', $plain=true);
		public function FileSize($file);
		public function putContent($data, $filename);
		public function get_errors();
		public function get_cachefolder($full=false);
	}//end interface
}//end if
?>