<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

// Redirect to the start page..
if(registry::register('config')->get('start_page') ){
	$redirect_url = registry::register('config')->get('start_page');
	$redirect_url .= (strpos($redirect_url, '?') !== false) ? str_replace('?', '&', registry::get_const('SID')) : registry::get_const('SID');
	redirect($redirect_url);
}else{
	redirect('viewnews.php' . registry::get_const('SID'));
}

?>