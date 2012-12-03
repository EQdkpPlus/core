<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

if($in->get('type') == 'css'){
	header('Content-type: text/css; charset=UTF-8');
	ob_start("compress");
	$md5_cssfile = md5($in->get('css'));
	//d(unserialize(base64_decode($in->get('css')))).'<br>';die($md5_cssfile);
	if(is_file($pcache->FolderPath('cache/css', 'eqdkp').$md5_cssfile)){
		echo file_get_contents($pcache->FolderPath('cache/css', 'eqdkp').$md5_cssfile);
	}else{
		$files = unserialize(base64_decode($in->get('css')));
		$rpath = unserialize(base64_decode($in->get('rpth')));
		if (!empty($files)) {
			$cssdata = '';
			foreach($files as $file) {
				$tmpcssdata = file_get_contents($file);

				// Manipulate it, baby!
				$mydirname = str_replace($rpath , '', dirname($file));
				$tmpcssdata = str_replace('url(images/' , 'url('.$mydirname.'/images/', $tmpcssdata);

				// Add it to the file...
				$cssdata .= $tmpcssdata;
			}
			// Remove old, unused files older than two months
			// TODO!
			
			// write data to the cache
			$pcache->putContent($cssdata, $pcache->FolderPath('cache/css', 'eqdkp', false).$md5_cssfile);
			echo $cssdata;
		}
	}
	ob_end_flush();
	
// Merge the JS into one file and set it to the cache!
}elseif($in->get('type') == 'js'){
	header('Content-type: text/javascript; charset=UTF-8');
	$md5_jsfile = md5($in->get('js'));
	if(is_file($pcache->FolderPath('cache/js', 'eqdkp').$md5_jsfile)){
		echo file_get_contents($pcache->FolderPath('cache/js', 'eqdkp').$md5_jsfile);
	}else{
		$files = unserialize(base64_decode($in->get('js')));
		if (!empty($files)) {
			$jsdata = '';
			foreach($files as $file) {
				$jsdata .= file_get_contents($file);
			}

			#$pcache->Delete($pcache->FolderPath('cache/js', 'eqdkp', false));
			
			// Remove old, unused files older than two months
			// TODO!
			
			// write data to the cache
			$pcache->putContent($jsdata, $pcache->FolderPath('cache/js', 'eqdkp', false).$md5_jsfile);
			echo $jsdata;
		}
	}
}

function compress($buffer) {
	/* remove comments */
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	/* remove tabs, spaces, newlines, etc. */
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
	return $buffer;
}
?>