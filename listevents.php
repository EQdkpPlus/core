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

if($core->config['pk_noEvents'] == 1) { redirect('viewnews.php');}

$user->check_auth('u_event_list');

require_once($eqdkp_root_path.'core/html_pdh_tag_table.class.php');

$user->lang['manage_events'] = "Manage events";

//Sort
if (isset($_GET['sort'])){
  $sort = $in->get('sort');
}else{
  $sort = '0|desc';
}
$sort_suffix = '&amp;sort='.$sort;

//redirect on event management
if ( isset($_GET['manage_b']) && ($_GET['manage_b'] == $user->lang['manage_events']) ){
  $manage_link = './admin/listevents.php';
  redirect($manage_link);
}

$start = 0;
if(isset($_GET['start'])){
  $start = $in->get('start', 0);
  $pagination_suffix = '&amp;start='.$start;
}

//Output
$view_list = $pdh->get('event', 'id_list');

//footer
$event_count = count($view_list);
$footer_text = sprintf($user->lang['listevents_footcount'], $event_count ,$user->data['user_elimit']);

$hptt_page_settings = $pdh->get_page_settings('listevents', 'hptt_listevents_eventlist');
$hptt = new html_pdh_tag_table($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'viewevent.php', '%link_url_suffix%' => ''));

$tpl->assign_vars(
  array (
  	'F_EVENTS'         => 'listevents.php'.$SID,
    'EVENT_OUT'        => $hptt->get_html_table($sort, $pagination_suffix, $start, $user->data['user_elimit'], $footer_text),
    'MANAGE_LINK'     => ($user->check_auth('a_event_', false)) ? '<a href="admin/manage_events.php'.$SID.'" title="'.$user->lang['manevents_title'].'"><img src="'.$eqdkp_root_path.'images/glyphs/edit.png" alt="'.$user->lang['manevents_title'].'"></a>' : '',
    
		'EVENT_PAGINATION' => generate_pagination('listevents.php'.$SID.$sort_suffix, $event_count, $user->data['user_elimit'], $start),
		'L_EVENTS'	=> $user->lang['events'],
  )
);

$core->set_vars(
  array(
    'page_title'    => $user->lang['events'],
    'template_file' => 'listevents.html',
    'display'       => true
  )
);

?>