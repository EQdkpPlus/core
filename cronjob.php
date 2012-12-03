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

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

@set_time_limit(0);
@ignore_user_abort(true); 

//single task
if ($in->get('task') != ""){
	$force_run = ($in->get('force') == 'true' && $user->data['user_id'] != ANONYMOUS) ? true : false;
	$timekeeper->execute_cron($in->get('task'), $force_run);
} else {
	//all cronjobs with flag external
	$timekeeper->handle_crons(true);
}

?>