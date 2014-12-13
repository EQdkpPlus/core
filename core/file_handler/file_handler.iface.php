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
		public function FolderPath($foldername, $plugin=false, $linkType = 'relative');
		public function FileSize($file, $plugin=false);
						
		public function testWrite($file=false);
		public function copy($source, $dest);
		public function rename($old_file, $new_file);
		public function FileMove($filename, $tofile, $tmpmove=false);
		public function thumbnail($image, $thumbfolder, $filename, $resize_value=400);
		public function putContent($filename, $data);
		public function addContent($filename, $data);
		public function is_writable($file, $testfile=false);
		public function get_errors();
		public function get_cachefolder($blnPlain=false);
	}//end interface
}//end if
?>