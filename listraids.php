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

if($core->config['pk_noRaids'] == 1) { redirect('viewnews.php');}

// Check user permission
$user->check_auth('u_raid_list');

//
// Build the from/to GET vars to pass back to the script
//
if ( (isset($_POST['submit'])) && ($_POST['submit'] == $user->lang['create_news_summary']) ){
	$fv = new Form_Validate();
	$fv->is_valid_date('summary_date_from', $user->lang['fv_date']);
	$fv->is_valid_date('summary_date_to', $user->lang['fv_date']);
  
  // Kick 'em back to the start if there was an error from above
  if ( $fv->is_error() ){
      header('Location: listraids.php'.$SID);
  }else{
      // Make the dates into mm-dd-yy and add them to the URI,
      // then redirect back to the script
      $date1 = substr($_POST['summary_date_from'],3,2) . '-' . substr($_POST['summary_date_from'],0,2) . '-' . substr($_POST['summary_date_from'],6);
      $date2 = substr($_POST['summary_date_to'],3,2) . '-' . substr($_POST['summary_date_to'],0,2) . '-' . substr($_POST['summary_date_to'],6);
      header('Location: listraids.php'.$SID.'&from='.$date1.'&to='.$date2);
  }
}elseif ( (isset($_GET['from'])) && (isset($_GET['to'])) ){
  $date1 = @explode('-', $_GET['from']);
  $mo1 = $date1[0];
  $d1  = $date1[1];
  $y1  = $date1[2];
  
  $date2 = @explode('-', $_GET['to']);
  $mo2 = $date2[0];
  $d2  = $date2[1] + 1; // Includes raids/items ON that day
  $y2  = $date2[2];
  
  $date_suffix = '&amp;from='.$_GET['from'].'&amp;to='.$_GET['to'];
  
  // Make sure both make a valid timestamp    
  $date1 = @mktime(0, 0, 0, $mo1, $d1, $y1);
  $date2 = @mktime(0, 0, 0, $mo2, $d2, $y2);
  
  $view_list = $pdh->get('raid', 'raididsindateinterval', array($date1, $date2));
  
  $date2 -= 86400; // Shows THAT day
}else{
  $view_list = $pdh->get('raid', 'id_list');
  $date1 = $date2 = time();
  $date_suffix = '';
}

require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');

$user->lang['manage_raids'] = "Manage raids";

//Sort
if (isset($_GET['sort'])){
  $sort = $in->get('sort');
}else{
  $sort = '0|desc';
}
$sort_suffix = '&amp;sort='.$sort;

//redirect on member management
if ( isset($_GET['manage_b']) && ($_GET['manage_b'] == $user->lang['manage_raids']) ){
  $manage_link = './admin/listraids.php';
  redirect($manage_link);
}

$start = 0;
if(isset($_GET['start'])){
  $start = $in->get('start', 0);
  $pagination_suffix = '&amp;start='.$start;
}

//Output
$hptt_page_settings = $pdh->get_page_settings('listraids', 'hptt_listraids_raidlist');
$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewraid.php', '%link_url_suffix%' => ''));

//footer
$raid_count = count($view_list);
$footer_text = sprintf($user->lang['listraids_footcount'], $raid_count ,$user->data['user_rlimit']);

$tpl->assign_vars(
  array (
  	'F_RAIDS'         => 'listraids.php'.$SID,
		'MANAGE_LINK'     => ($user->check_auth('a_raid_', false)) ? '<a href="admin/manage_raids.php'.$SID.'" title="'.$user->lang['manage_raids'].'"><img src="'.$eqdkp_root_path.'images/glyphs/edit.png" alt="'.$user->lang['manage_raids'].'"></a>' : '',

		'PAGE_OUT'        => $hptt->get_html_table($sort, $pagination_suffix.$date_suffix, $start, $user->data['user_rlimit'], $footer_text),
    'RAID_PAGINATION' => generate_pagination('listraids.php'.$SID.$sort_suffix.$date_suffix, $raid_count, $user->data['user_rlimit'], $start),
    
    'L_ENTER_DATES' => $user->lang['create_raid_summary'],
    'L_STARTING_DATE' => $user->lang['starting_date'],
    'L_ENDING_DATE' => $user->lang['ending_date'],
    'L_CREATE_NEWS_SUMMARY' => $user->lang['create_news_summary'],
    'L_UNCHECK'	=> $user->lang['cl_ms_uncheckall'],
		
    // Date Picker
    'DATEPICK_DATE_FROM'        => $jquery->Calendar('summary_date_from', date('d.m.Y', $date1)),
	  'DATEPICK_DATE_TO'        => $jquery->Calendar('summary_date_to', date('d.m.Y', $date2))
  )
);

$core->set_vars(
  array(
    'page_title'    => $user->lang['listraids_title'],
    'template_file' => 'listraids.html',
    'display'       => true
  )
);

?>