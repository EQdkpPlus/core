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

// EQdkp required files/vars
define('EQDKP_INC', true);

$eqdkp_root_path = './';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('u_member_list');

require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');
require_once($eqdkp_root_path.'core/html_leaderboard.class.php');

//User input selection
//Sort
if (isset($_GET['sort'])){
  $sort = $in->get('sort');
}

//DKP Id
if (!isset($_GET['mdkpid'])){
  $mdkpid = 0;
}else{
  $mdkpid = $in->get('mdkpid', 0);
}

$show_inactive = false;
$show_hidden = false;
$show_twinks = $core->config['pk_show_twinks'];

if(isset($_GET['show_inactive'])){
  $show_inactive = true;
}

if(isset($_GET['show_hidden'])){
  $show_hidden = true;
}

if(isset($_GET['show_twinks'])){
	$show_twinks = true;
}

//redirect on member compare
if ( isset($_GET['compare_b']) && ($_GET['compare_b'] == $user->lang['compare_members']) ){
  
  $sort_suffix = (isset($sort))? '&amp;sort='.$sort : '';
  
  if(isset($_GET['selected_ids'])){
    $compare_link = './listcharacters.php?mdkpid='.$mdkpid.$sort_suffix.'&amp;filter=Member:'.implode(',', $_GET['selected_ids']);
    redirect($compare_link);
  }else{
    $compare_link = './listcharacters.php?mdkpid='.$mdkpid.$sort_suffix;
    redirect($compare_link);
  }
}

//redirect on member compare
if ( isset($_GET['manage_b']) && ($_GET['manage_b'] == $user->lang['manage_members']) ){
  $manage_link = './admin/manage_members.php';
  redirect($manage_link);
}

if($mdkpid == 0){
  $hptt_page_settings = $pdh->get_page_settings('listmembers', 'hptt_listmembers_memberlist_overview');
}else{
  $hptt_page_settings = $pdh->get_page_settings('listmembers', 'hptt_listmembers_memberlist_detail');
  $mdkp_suffix = $mdkpid;
}

//Filter
if (isset($_GET['filter'])){
  $filter = $in->get('filter');
  if(strpos($filter, 'Member') !== false){
    $filter_array[] = array('name' => $user->lang['compare_members'], 'value' => $filter);
		$is_compare = true;
	}
}else{ 
  $filter = 'none';
}

//Multidkp selection output
$multilist = $pdh->get('multidkp', 'id_list', array());
$tpl->assign_block_vars('mdkpid_row', array (
    'VALUE' => 0,
    'SELECTED' => ($mdkpid == 0) ? ' selected="selected"' : '',
    'OPTION' => 'Overview'
));
if(!empty($multilist)){
  foreach ($multilist as $id) {
    $tpl->assign_block_vars('mdkpid_row', array (
	        'VALUE' => $id,
	        'SELECTED' => ($mdkpid == $id) ? ' selected="selected"' : '',
	        'OPTION' => $pdh->get('multidkp', 'name', array($id))
		));
	}
}


$filter_array = $game->get('filters');
    
foreach($filter_array as $details){
  $tpl->assign_block_vars('filter_row', array(
    'VALUE'    => $details['value'],
    'SELECTED' => ( ($filter != 'none') && ($filter == $details['value']) ) ? ' selected="selected"' : '',
    'OPTION'   => $details['name']
  ));
}

//Output
$full_list = $pdh->get('member', 'id_list', array(false, false, false));
$view_list = $pdh->get('member', 'id_list', array(!$show_inactive, !$show_hidden, true, !$show_twinks));
filter_view_list($filter);

function filter_view_list($filter_string){
global $pdh, $view_list;
  if($filter_string != ''){

    list($filter, $params) = explode(":", $filter_string);
    
    switch (strtolower($filter)){
    case  'none':   break;
    case 'class':   $classids = explode(',',$params);
                    if(is_array($classids) && !empty($classids)){
                      foreach($view_list as $index => $memberid){
                        if(in_array($pdh->get('member', 'classid', array($memberid)), $classids))
                          $temp[] =$memberid;
                      }
                      $view_list = $temp;
                    }
                    break;
    case 'member':  $memberids = explode(',',$params);
                    if(is_array($memberids) && !empty($memberids))
                      $view_list = array_intersect($view_list, $memberids);
                    break;
    }          
  }
}

//Create our suffix
$suffix  = '';
$suffix .= ($mdkpid > 0)      ? '&amp;mdkpid='.$mdkpid : '';
$suffix .= ($filter != 'none')? '&amp;filter='.$filter : '';
$suffix .= ($show_inactive)   ? '&amp;show_inactive=1' : '';
$suffix .= ($show_hidden)     ? '&amp;show_hidden=1'   : '';
$suffix .= ($show_twinks)     ? '&amp;show_twinks=1'   : '';

//footer stuff
if($is_compare){
	$footer_text = sprintf($user->lang['listmembers_compare_footcount'], count($view_list));
}else{
	$footer_text = sprintf($user->lang['listmembers_footcount'], count($view_list));
}


$hptt = new html_pdh_tag_table($hptt_page_settings, $full_list, $view_list, array('%dkp_id%' => $mdkpid, '%link_url%' => 'viewcharacter.php', '%link_url_suffix%' => '', '%with_twink%' => !$core->config['pk_show_twinks']), $mdkp_suffix);
$myleaderboard = new html_leaderboard();
$leaderboard_settings = $pdh->get_page_settings('listmembers', 'listmembers_leaderboard');
$tpl->assign_vars(
  array (
  	'F_MEMBERS'                 => 'listcharacters.php'.$SID,
  	'LEADERBOARD'               => $myleaderboard->get_html_leaderboard($mdkpid, $leaderboard_settings['classes'], $leaderboard_settings['maxperrow'], false, $leaderboard_settings['maxperclass'], -1, $leaderboard_settings['sort_direction']),
    'POINTOUT'                  => $hptt->get_html_table($sort, $suffix, null, null, $footer_text),
    'BUTTON_NAME'               => 'compare_b',
    'BUTTON_VALUE'              => $user->lang['compare_members'],
    'COMPARE_LINK'              => '<input type="image" src="'.$eqdkp_root_path.'images/glyphs/compare.png" name="compare_b" width="16" height="16" value="'.$user->lang['compare_members'].'" alt="'.$user->lang['compare_members'].'" title="'.$user->lang['compare_members'].'">',
    'MANAGE_LINK'               => ($user->check_auth('a_members_man', false)) ? '<input type="image" src="'.$eqdkp_root_path.'images/glyphs/edit.png" name="manage_b" width="16" height="16" value="'.$user->lang['manage_members'].'" alt="'.$user->lang['manage_members'].'" title="'.$user->lang['manage_members'].'">' : '',
    'SHOW_INACTIVE'             => $user->lang['show_inactive'],
    'SHOW_INACTIVE_CHECKED'     => ($show_inactive)?'checked="checked"':'',
    'SHOW_HIDDEN_RANKS'         => $user->lang['show_hidden_ranks'],
    'SHOW_HIDDEN_RANKS_CHECKED' => ($show_hidden)?'checked="checked"':'',
    'SHOW_TWINKS'				=> $user->lang['show_twinks'],
    'SHOW_TWINKS_CHECKED'		=> ($show_twinks)?'checked="checked"':'',
	'S_SHOW_TWINKS'				=> !$core->config['pk_show_twinks'],
  )
);
$core->set_vars(
  array(
    'page_title'    => $user->lang['listmembers_title'],
    'template_file' => 'listcharacters.html',
    'display'       => true
  )
);

?>