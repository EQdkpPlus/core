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
define('ITEMSTATS', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
$user->check_auth('u_member_view');

if($in->get('member_id',0) > 0)
{
	require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
  	$member_id		= $in->get('member_id', 0);
  	$member_name	= $pdh->get('member', 'name', array($member_id));
  
  	if($member_name == '')
  	{
    	message_die($user->lang['error_invalid_name_provided']);
  	}

 	//Sort
 	$rsort					= $in->get('rsort', '');
  	$asort					= $in->get('asort', '');
  	$isort					= $in->get('isort', '');

	//Points
  	$view_list = $pdh->get('member', 'other_members', array($member_id));
  	$view_list[] = $member_id;
  	$hptt_page_settings = $pdh->get_page_settings('viewmember', 'hptt_viewmember_memberlist');
  	$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array(), $member_id, 'psort');
  	$tpl->assign_vars(array ('POINT_OUT' => $hptt->get_html_table($asort, '&member_id='.$member_id.$sort_suffix),));
  
  	// Raid Attendance
  	$rstart				= $in->get('rstart', 0);
  	$sort_suffix  = !empty($isort) ? '&amp;isort='.$isort : '';
  	$sort_suffix .= !empty($asort) ? '&amp;asort='.$asort : '';
  
  	$view_list = $pdh->get('raid', 'raidids4memberid', array($member_id));
  	$hptt_page_settings = $pdh->get_page_settings('viewmember', 'hptt_viewmember_raidlist');
  	$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewraid.php', '%link_url_suffix%' => '', '%with_twink%' => true), $member_id, 'rsort');
  	$tpl->assign_vars(
    	array (
      	'RAID_OUT'        => $hptt->get_html_table($rsort, '&member_id='.$member_id.$pagination_suffix.$sort_suffix, $rstart, $user->data['user_rlimit']),
      	'RAID_PAGINATION' => generate_pagination('viewcharacter.php'.$SID.'&member_id='.$member_id.$sort_suffix, count($view_list), $user->data['user_rlimit'], $rstart, 'rstart')
    	)
  	);

  	// Item History
  	$istart = $in->get('istart', 0);
  	$sort_suffix  = !empty($rsort) ? '&amp;rsort='.$rsort : '';
  	$sort_suffix .= !empty($asort) ? '&amp;asort='.$asort : '';
  
  	$view_list = $pdh->get('item', 'itemids4memberid', array($member_id));
  	$hptt_page_settings = $pdh->get_page_settings('viewmember', 'hptt_viewmember_itemlist');
  	$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewitem.php', '%link_url_suffix%' => ''), $member_id, 'isort');
  	$tpl->assign_vars(
    	array (
      	'ITEM_OUT'        => $hptt->get_html_table($isort, '&member_id='.$member_id.$pagination_suffix.$sort_suffix, $istart, $user->data['user_ilimit']),
      	'ITEM_PAGINATION' => generate_pagination('viewcharacter.php'.$SID.'&member_id='.$member_id.$sort_suffix, count($view_list), $user->data['user_ilimit'], $istart, 'istart')
    	)
  	);

	//
  	// Individual Adjustment History
  	//
  	$sort_suffix  = !empty($rsort) ? '&amp;rsort='.$rsort : '';
  	$sort_suffix .= !empty($isort) ? '&amp;isort='.$isort : '';
  	$view_list = $pdh->get('adjustment', 'adjsofmember', array($member_id));
  	$hptt_page_settings = $pdh->get_page_settings('viewmember', 'hptt_viewmember_adjlist');
  	$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array(), $member_id, 'asort');
  	$tpl->assign_vars(
    	array (
      	'ADJUSTMENT_OUT' => $hptt->get_html_table($asort, '&member_id='.$member_id.$sort_suffix),
    	)
  	);
	
	// Load member Data to an array
	$member			= $pdh->get('member', 'array', array($member_id));
	$last_update	= ($member['last_update']) ? date($user->lang['uc_changedate'],$member['last_update']) : '--';
	
	// The Multigame Profiles..
	$profilefolder = $eqdkp_root_path.'games/'.$game->get_game().'/profiles/';
	if(is_file($profilefolder.'profile_additions.php'))
	{
		// include a game specific file
		include($profilefolder.'profile_additions.php');
	}
	
	// Remove the trailing . in the ./.. to indicate its a path..
	$pcomments->SetVars(array('attach_id'=>$member_id, 'page'=>'member'));
	$jquery->Tab_header('profile_information');
	$profile_pic = $pdh->get('member', 'picture',		array($member_id));
	$customNoPic = (is_file('games/'.$game->get_game().'/profiles/no_pic.png')) ? 'games/'.$game->get_game().'/profiles/no_pic.png' : 'images/no_pic.png';

	//Member DKP
	$member_points = $pdh->geth('member', 'member_points',	array($member_id));


	
	$profile_out = array(
	    'PROFILE_OUTPUT'									=> ((is_file($profilefolder.'profile_view.html')) ? $profilefolder.'profile_view.html' : 'profile_view.html'),
	    'COMMENT' 											=> ($core->config['pk_enable_comments'] == 1) ? $pcomments->Show() : '',	    
	    'DATA_GUILDTAG'										=> $core->config['guildtag'],
	    'DATA_NAME'     									=> $member_name,
	    'DATA_RACENAME'     								=> $member['race_name'],
	    'DATA_CLASSNAME'     								=> $member['class_name'],
	    'SIGNATUR'     										=> $game->callFunc('showAllvatarWoW_Signatur', array($member_name, $pdh->get('member', 'classid', array($member_id)))),
	    'SHIRT_SHOP'   										=> $plus->createShirtBox($member_id),
	    'PROFILE_PICTURE'									=> ($profile_pic) ? $pcache->FolderPath('upload', 'charmanager').$profile_pic : $customNoPic,
	    'LAST_UPDATE'										=> $last_update,
	    'MEMBER_POINTS'										=> $member_points,
	    
	    // Language Vars..
	    'L_SERVICE'						            		=> $user->lang['service'],
	    'L_RAID_ATTENDANCE_HISTORY'       					=> $user->lang['raid_attendance_history'],
	    'L_ITEM_PURCHASE_HISTORY'         					=> $user->lang['item_purchase_history'],
	    'L_INDIVIDUAL_ADJUSTMENT_HISTORY' 					=> $user->lang['individual_adjustment_history'],
	    'L_ATTENDANCE_BY_EVENT'           					=> $user->lang['attendance_by_event'],
	    'L_EVENT'                         					=> $user->lang['event'],
	    'L_PERCENT'                       					=> $user->lang['percent'],
	    'L_TAB_POINTS'										=> $user->lang['tab_points'],
	    'L_TAB_RAIDS'										=> $user->lang['tab_raids'],
	    'L_TAB_ITEMS'										=> $user->lang['tab_items'],
	    'L_TAB_ADJUSTMENTS'									=> $user->lang['tab_adjustments'],
	    'L_TAB_ATTENDANCE'									=> $user->lang['tab_attendance'],
	    'L_TAB_NOTES'										=> $user->lang['tab_notes'],
	    'L_LAST_UPDATE2'									=> $user->lang['uc_last_update'],
	    'L_UNKNOWN'											=> '--',
	
		'L_account'											=> $user->lang['Multi_kontoname_short'],
		'L_current'											=> $user->lang['current'],
		'L_earned'											=> $user->lang['earned'],
		'L_spent'											=> $user->lang['spent'],
		'L_adjustment'										=> $user->lang['adjustment'],
		'L_attendance'										=> $user->lang['attendance'],
		'L_DKP_NAME'										=> $core->config['dkp_name']." ".$user->lang['information'],
	    
	    // Sortfunctioniality
	    'O_EVENT'                         					=> $current_order['uri'][0],
	    'O_PERCENT'                       					=> $current_order['uri'][1],
	    
	    // TODO: id!!
	    'U_VIEW_MEMBER'                   					=> 'viewcharacter.php' . $SID . '&amp;name=' . $member['name'] . '&amp;'
  	);

  	// Add the game-specific Fields...
  	foreach($member as $profile_id=>$profile_value)
  	{
  		$profile_out['DATA_'.strtoupper($profile_id)]	= $profile_value;
  		$profile_out['L_'.strtoupper($profile_id)]		= $game->glang($profile_id);
  	}
  
	// Start the Output
  	$tpl->assign_vars($profile_out);
  	$pm->do_hooks('/viewcharacter.php');

  	$core->set_vars(array(
      'page_title'    => sprintf($user->lang['viewmember_title'], $member['member_name']),
      'template_file' => 'viewcharacter.html',
      'display'       => true
    	)
  	);
}else
{
	message_die($user->lang['error_invalid_name_provided']);
}
?>