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

$memberID = $in->get('id', 0);

$member_list = $pdh->get('member', 'id_list');
foreach ($member_list as $member){
  	$tpl->assign_block_vars('members_row', array(
      'VALUE'    => $member,
      'SELECTED' => ( $memberID == $member) ? ' selected="selected"' : '',
      'OPTION'   => $pdh->get('member', 'name', array($member)))
	);
}


if (intval($memberID)<1)
{
	if ( $user->data['user_id'] != ANONYMOUS )
	{
		$connections = $pdh->get('member_connection', 'connection', array($user->data['user_id']));
		$memberID	= $connections[0]['member_id'];
	}

	if (intval($memberID)<1){
		$connections = $pdh->get('member', 'id_list');
		$memberID	= $connections[0];
	}
	
}

    
		
		$shopurl = $plus->create_shop_link($pdh->get('member', 'name', array($memberID)),$pdh->get('member', 'classid', array($memberID)), $pdh->get('member', 'raceid', array($memberID)),$core->config['guildtag'],$core->config['uc_servername'],$pdh->get('member', 'level', array($memberID)), $pdh->get('member', 'profile_field', array($memberID, 'gender')));



$tpl->assign_vars(array(
    'CONTENT_TITLE' => "[".$core->config['guildtag'].'] Shop powered by Eqdkp-Plus',
    'SHOP_URL' => $shopurl,
    'L_ERROR_IFRAME' => $user->lang['error_iframe'],
    'L_NEW_WINDOW' => $user->lang['new_window'],
    'L_CHOOSE'	=> $user->lang['shirt_ad2'],
    'L_SHIRT4'	=> $user->lang['shirt_ad3'],
    'L_SHIRT4'	=> $user->lang['shirt_ad4']
    )
);


$core->set_vars(array(
    'page_title'    => '',
    'template_file' => 'shop.html',
    'display'       => true)
);
?>