<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
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
$eqdkp_root_path = './../../../';
include_once ($eqdkp_root_path . 'common.php');

if (!$user->check_auth('u_member_man', false) && !$user->check_auth('u_member_add', false)) {
	 message_die($user->lang['uc_no_prmissions']);
}

// Check if we have a MemberID
$myMemberID = ($in->get('member_id',0)) ? '?member_id='.$in->get('member_id',0) : '';
$yes_we_can = ($conf['uc_servername'] && $conf['uc_server_loc'] && $myMemberID) ? '&step=1' : '';
$hmtlout = '<meta http-equiv="refresh" content="0; URL=_helpers/armory.php'.$myMemberID.$yes_we_can.'">';
echo $hmtlout;
?>