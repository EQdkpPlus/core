<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * listraids.php
 * Began: Thu December 19 2002
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');
if($conf_plus['pk_noRaids'] == 1) { redirect('viewnews.php');}

$user->check_auth('u_raid_list');

$sort_order = array(
    0 => array('raid_date desc', 'raid_date'),
    1 => array('raid_name', 'raid_name desc'),
    2 => array('raid_note', 'raid_note desc'),
    3 => array('raid_value desc', 'raid_value')
);

// RSS Feed
include ($eqdkp_root_path."libraries/UniversalFeedCreator/UniversalFeedCreator.class.php");
$rss = new UniversalFeedCreator();

$rss->title           = "Last Raids";
$rss->description     = $eqdkp->config['main_title']." EQdkp-Plus - Last Raids" ;
$rss->link            = $pcache->BuildLink();
$rss->syndicationURL  = $pcache->BuildLink().$_SERVER['PHP_SELF'];


$current_order = switch_order($sort_order);

$total_raids = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE);

$start = ( isset($_GET['start']) ) ? $_GET['start'] : 0;

$sql = 'SELECT raid_id, raid_name, raid_date, raid_note, raid_value, raid_added_by
        FROM ' . RAIDS_TABLE . '
        ORDER BY '.$current_order['sql']. '
        LIMIT '.$start.','.$user->data['user_rlimit'];

if ( !($raids_result = $db->query($sql)) )
{
    message_die('Could not obtain raid information', '', __FILE__, __LINE__, $sql);
}

if ( ($pm->check(PLUGIN_INSTALLED, 'bosssuite')) && ($eqdkp->config['bs_linkBL']) ){
  require_once ($eqdkp_root_path.'plugins/bosssuite/mods/note2link.php');
}else{
  function bl_note2link($raidnote, $raidname){
    return $raidnote;
  }
}
     
while ( $row = $db->fetch_record($raids_result) )
{
		$event_icon = getEventIcon($row['raid_name']);
     
    $tpl->assign_block_vars('raids_row', array(
        'ROW_CLASS' => $eqdkp->switch_row_class(),
        'DATE' => ( !empty($row['raid_date']) ) ? date($user->style['date_notime_short'], $row['raid_date']) : '&nbsp;',
        'U_VIEW_RAID' => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$row['raid_id'],
        'NAME' => ( !empty($row['raid_name']) ) ? $event_icon.stripslashes($row['raid_name']) : '&lt;<i>Not Found</i>&gt;',
        'NOTE' => ( !empty($row['raid_note']) ) ? bl_note2link(stripslashes($row['raid_note']), stripslashes($row['raid_name'])) : ' ',
        'VALUE' => runden($row['raid_value']))
    );
    
    //Create Items RSS
    if ($i < 10) 
    {
        $rssitem = new FeedItem();
        $rssitem->title        = $row['raid_name'] ;
        $rssitem->link         = $pcache->BuildLink().'viewraid.php?r='.$row['raid_id'];
        $rssitem->description  = $row['raid_note'];
        $rssitem->date         = $row['raid_date'] ;
        $rssitem->source       = $rss->link;
        $rssitem->author       = $row['raid_added_by']  ;
        
        $additionals = array('DKP_VALUE'  => runden($row['raid_value']),
        					 'NOTE_LINKED'  => "<![CDATA[".preg_replace("/\.\//ms", $pcache->BuildLink(),bl_note2link(stripslashes($row['raid_note'])))."]]>",        					 
        					 'EVENT_ICON'  => "<![CDATA[".preg_replace("/\.\//ms", $pcache->BuildLink(),$event_icon,1)."]]>"
        					 
        
        					 );
        $rssitem->additionalElements = $additionals;
        
        $rss->addItem($rssitem);    
		$i++;    	
    }
    
}

$rss->saveFeed("RSS2.0", $pcache->FilePath('last_raids.xml', 'eqdkp'),false);


$tpl->assign_vars(array(
    'L_DATE' => $user->lang['date'],
    'L_NAME' => $user->lang['name'],
    'L_NOTE' => $user->lang['note'],
    'L_VALUE' => $user->lang['value'],

    'O_DATE' => $current_order['uri'][0],
    'O_NAME' => $current_order['uri'][1],
    'O_NOTE' => $current_order['uri'][2],
    'O_VALUE' => $current_order['uri'][3],

    'U_LIST_RAIDS' => 'listraids.php'.$SID.'&amp;',
    'RSS_FEED'        => '<link rel="alternate" type="application/rss+xml" title="EQDkp-Plus Raids XML" href="'.$pcache->BuildLink().$pcache->FileLink('last_raids.xml', 'eqdkp').'" />' ,

    'START' => $start,
    'LISTRAIDS_FOOTCOUNT' => sprintf($user->lang['listraids_footcount'], $total_raids, $user->data['user_rlimit']),
    'RAID_PAGINATION' => generate_pagination('listraids.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_raids, $user->data['user_rlimit'], $start))
);

$eqdkp->set_vars(array(
    'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['listraids_title'],
    'template_file' => 'listraids.html',
    'display'       => true)
);
?>
