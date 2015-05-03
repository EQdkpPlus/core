<?php
/*	Project:	EQdkp-Plus
 *	Package:	elfinder connector
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

define('EQDKP_INC', true);
$eqdkp_root_path = './../../../';

include_once ($eqdkp_root_path . 'common.php');

if (!register('user')->is_signedin()){
	echo('You have no permission to see this page as you are not logged in'); exit;
}

include_once $eqdkp_root_path.'libraries/elfinder/php/elFinderConnector.class.php';
include_once $eqdkp_root_path.'libraries/elfinder/php/elFinder.class.php';
include_once $eqdkp_root_path.'libraries/elfinder/php/elFinderVolumeDriver.class.php';
include_once $eqdkp_root_path.'libraries/elfinder/php/elFinderVolumeLocalFileSystem.class.php';


/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from  '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {
	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}

$opts = array(
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem',   // driver for accessing file system (REQUIRED)
			'path'          => register('pfh')->FolderPath('users/'.register('user')->id, 'files'),         // path to files (REQUIRED)
			'URL'           => register('pfh')->FileLink('users/'.register('user')->id.'/', 'files', 'absolute'), // URL to files (REQUIRED)
			'accessControl' => 'access',             // disable and hide dot starting files (OPTIONAL)
			'uploadAllow'	=> array('image/jpeg', 'image/png', 'image/gif'),
			'uploadDeny'	=> array('all'),
			//'uploadOrder'	=> array('allow', 'deny'),
			'disabled'		=> array('extract', 'archive','mkdir', 'mkfile','help','rename','download','edit'),
			'maxFilesInDir' => MAX_FILES_USERFOLDER,
			'tmbPathMode'	=> get_chmod(true),
		)
	)
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();

?>