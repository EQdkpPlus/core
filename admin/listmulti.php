<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listmulti.php
 * Began: Fri Oktober 14 2006
 * 
 * Corgan 
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

$sort_order = array(
    0 => array('adjustment_date desc', 'adjustment_date'),
    1 => array('member_name', 'member_name desc'),
    2 => array('adjustment_reason', 'adjustment_reason desc'),
    3 => array('adjustment_value desc', 'adjustment_value'),
    4 => array('adjustment_added_by', 'adjustment_added_by desc'),
    5 => array('raid_name', 'raid_name desc')
);

$current_order = switch_order($sort_order);

//
// Individual Adjustments
//
if (!isset($_GET[URI_PAGE]))
{
    $user->check_auth('a_indivadj_');
     
    $page_title = sprintf($user->lang['admin_title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['Multi_pageheader'];
    
    $total_multi = $db->query_first('SELECT count(*) FROM ' . MULTIDKP_TABLE . ' WHERE multidkp_name IS NOT NULL');
    $start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;
    
    $s_group_adj = false;
    
    $sql = 'SELECT multidkp_name, multidkp_disc, multidkp_id
            FROM ' . MULTIDKP_TABLE . '
            WHERE multidkp_name IS NOT NULL'
            ;
    
    $listmulti_footcount = sprintf($user->lang['multi_footcount'], $total_multi, $user->data['user_alimit']);
    
}

if ( !($multi_result = $db->query($sql)) )
{
    message_die('Could not obtain MultiDKP information', '', __FILE__, __LINE__, $sql);
}

while ( $multi = $db->fetch_record($multi_result) )
{
	$sql_events = 'SELECT  multidkp2event_multi_id, multidkp2event_eventname 
  							 FROM ' . MULTIDKP2EVENTS_TABLE
  							 .' WHERE multidkp2event_multi_id ='.$multi['multidkp_id'] ;
        				
		if ( !($multi2event_results = $db->query($sql_events)) )
		{    
			message_die('Could not obta in MultiDKP -> Event information', '', __FILE__, __LINE__, $sql_events);
		}
		$multi2event = '' ;						  
	  while ( $a_multi = $db->fetch_record($multi2event_results) )
		{ // gehe alle Events durch, die einem Konto zugewiesen wurden
			$multi2event .= $a_multi['multidkp2event_eventname'].' , ' ;
		}
		
		# komma am ende entfernen
		$multi2event = preg_replace('# \, $#', '', $multi2event);
		
    $tpl->assign_block_vars('multi_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'U_ADD_ADMULTI' => (( $s_group_adj ) ? 'addmulti.php' : 'addmulti.php') . $SID.'&amp;' . URI_ADJUSTMENT . '='.$multi['multidkp_id'],
        'U_NAME' => $multi['multidkp_name'],
        'U_DISC' => $multi['multidkp_disc'],
        'U_EVENTS' => $multi2event,
							)
    );
}
$db->free_result($adj_result);

$tpl->assign_vars(array(
    
    
    'L_NAME' => $user->lang['Multi_kontoname_short'],
    'L_DISC' => $user->lang['Multi_discr'],
    'L_EVENTS' => $user->lang['Multi_events'],
   
    'START' => $start,

    'LISTMULTI_FOOTCOUNT' => $listmulti_footcount)
);

$eqdkp->set_vars(array(
    'page_title'    => $page_title,
    'template_file' => 'admin/listmulti.html',
    'display'       => true)
);
?>