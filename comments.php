<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
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

// Prevent Guests to write comments...
if(!$user->data['username']){
	die('No User Name!');
}

if($in->get('deleteid', 0)){
	$pcomments->Delete($in->get('page'), $in->get('rpath'));
}elseif($in->get('comment', '', 'htmlescape')){
	$pcomments->Save();
}else{
	echo $pcomments->Content($in->get('attach_id', 0), $in->get('page'), $in->get('rpath'), true, $in->get('lang_prefix'));
}
?>