<?php
/******************************
 * EQDKP PLUGIN: Charmanager
 * (c) 2006 by WalleniuM [Simon Wallmann]
 * http://www.wallenium.de   
 * ------------------
 * charmanager.php
 * Changed: Thu December 28, 2006
 * 
 ******************************/

define('EQDKP_INC', true);
define('PLUGIN', 'charmanager');
$eqdkp_root_path = '../../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
global $table_prefix;

$user->check_auth('u_member_view');

if (!defined('MEMBER_ADDITION_TABLE')) { define('MEMBER_ADDITION_TABLE', $table_prefix . 'member_additions'); }


if ( (isset($_GET[URI_NAME])) && (strval($_GET[URI_NAME] != '')) )
{
    $sort_order = array(
        0 => array('raid_name', 'raid_name desc'),
        1 => array('raid_count desc', 'raid_count')
    );

    $current_order = switch_order($sort_order);

$sql = 'SELECT ma.*, m.member_id, m.member_name, m.member_earned, m.member_spent, m.member_adjustment, (m.member_earned-member_spent+m.member_adjustment) AS member_current,
              m.member_firstraid, m.member_lastraid, m.member_class_id, m.member_race_id
            FROM ' . MEMBERS_TABLE . " m
            LEFT JOIN " . MEMBER_ADDITION_TABLE . " ma ON (ma.member_id=m.member_id)
            WHERE member_name='".$_GET[URI_NAME]."'";

    if ( !($member_result = $db->query($sql)) )
    {
        message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
    }

    // Make sure they provided a valid member name
    if ( !$member = $db->fetch_record($member_result) )
    {
        message_die($user->lang['error_invalid_name_provided']);
    }

    // Find the percent of raids they've attended in the last 30, 60 and 90 days
    $percent_of_raids = array(
        '30'       => raid_count(mktime(0, 0, 0, date('m'), date('d')-30, date('Y')), time(), $member['member_name']),
        '60'       => raid_count(mktime(0, 0, 0, date('m'), date('d')-60, date('Y')), time(), $member['member_name']),
        '90'       => raid_count(mktime(0, 0, 0, date('m'), date('d')-90, date('Y')), time(), $member['member_name']),
        'lifetime' => raid_count($member['member_firstraid'], $member['member_lastraid'], $member['member_name'])
    );


        //===========================
		// Find the race name

		$sql = 'SELECT race_name
				FROM ' . RACE_TABLE . '
				WHERE race_id = ' . $member['member_race_id'] ;

		$result = $db->query($sql);
		$race_name = $db->fetch_record($result) ;


	$db->free_result($result);
    //===========================


        //===========================
		// Find the class name

		$sql = 'SELECT class_name
				FROM ' . CLASS_TABLE . '
				WHERE class_id = ' . $member['member_class_id'] ;

		$result = $db->query($sql);
		$class_name = $db->fetch_record($result) ;


	$db->free_result($result);
    //===========================


    //
    // Raid Attendance
    //
    $rstart = ( isset($_GET['rstart']) ) ? $_GET['rstart'] : 0;

    // Find $current_earned based on the page.  This prevents us having to pass the
    // current earned as a GET variable which could result in user error
    if ( (!isset($_GET['rstart'])) || ($_GET['rstart'] == '0') )
    {
        $current_earned = $member['member_earned'];
    }
    else
    {
        $current_earned = $member['member_earned'];
        $sql = 'SELECT raid_value
                FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
                WHERE (ra.raid_id = r.raid_id)
                AND (ra.member_name='" . $member['member_name']."')
                ORDER BY r.raid_date DESC
                LIMIT " . $rstart;
        if ( !($earned_result = $db->query($sql)) )
        {
            message_die($user->lang['uc_error_raidinfos'], '', __FILE__, __LINE__, $sql);
        }
        while ( $ce_row = $db->fetch_record($earned_result) )
        {
            $current_earned -= $ce_row['raid_value'];
        }
        $db->free_result($earned_result);
    }

    $sql = 'SELECT r.raid_id, r.raid_name, r.raid_date, r.raid_note, r.raid_value
            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . ' ra
            WHERE (ra.raid_id = r.raid_id)
            AND (ra.member_name=\'' . $member['member_name'] . '\')
            ORDER BY r.raid_date DESC
            LIMIT 5';
    if ( !($raids_result = $db->query($sql)) )
    {
        message_die($user->lang['uc_error_raidinfos'], '', __FILE__, __LINE__, $sql);
    }
    while ( $raid = $db->fetch_record($raids_result) )
    {
        $tpl->assign_block_vars('raids_row', array(
            'ROW_CLASS'      => $eqdkp->switch_row_class(),
            'DATE'           => ( !empty($raid['raid_date']) ) ? date($user->style['date_notime_short'], $raid['raid_date']) : '&nbsp;',
            'U_VIEW_RAID'    => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '='.$raid['raid_id'],
            'NAME'           => ( !empty($raid['raid_name']) ) ? stripslashes($raid['raid_name']) : '&lt;<i>Not Found</i>&gt;',
            'NOTE'           => ( !empty($raid['raid_note']) ) ? stripslashes($raid['raid_note']) : '&nbsp;',
            'EARNED'         => $raid['raid_value'],
            'CURRENT_EARNED' => sprintf("%.2f", $current_earned))
        );
        $current_earned -= $raid['raid_value'];
    }

    $db->free_result($raids_result);

    $sql = 'SELECT count(*)
            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
            WHERE (ra.raid_id = r.raid_id)
            AND (ra.member_name='" . addslashes($member['member_name']) . "')";
    $total_attended_raids = $db->query_first($sql);

    //
    // Item Purchase History
    //
    $istart = ( isset($_GET['istart']) ) ? $_GET['istart'] : 0;

    if ( (!isset($_GET['istart'])) || ($_GET['istart'] == '0') )
    {
        $current_spent = $member['member_spent'];
    }
    else
    {
        $current_spent = $member['member_spent'];
        $sql = 'SELECT item_value
                FROM ' . ITEMS_TABLE . "
                WHERE (item_buyer='" . $member['member_name'] . "')
                ORDER BY item_date DESC
                LIMIT " . $istart;
        if ( !($spent_result = $db->query($sql)) )
        {
            message_die($user->lang['uc_error_iteminfos'], '', __FILE__, __LINE__, $sql);
        }
        while ( $cs_row = $db->fetch_record($spent_result) )
        {
            $current_spent -= $cs_row['item_value'];
        }
        $db->free_result($spent_result);
    }

    $sql = 'SELECT i.item_id, i.item_name, i.item_value, i.item_date, i.raid_id, r.raid_name
            FROM ( ' . ITEMS_TABLE . ' i
            LEFT JOIN ' . RAIDS_TABLE . ' r
            ON r.raid_id = i.raid_id )
            WHERE (i.item_buyer=\'' . $member['member_name'] . '\')
            ORDER BY i.item_date DESC
            LIMIT 5';
    if ( !($items_result = $db->query($sql)) )
    {
        message_die($user->lang['uc_error_itemraidi'], 'Database error', __FILE__, __LINE__, $sql);
    }
    while ( $item = $db->fetch_record($items_result) )
    {
        $tpl->assign_block_vars('items_row', array(
            'ROW_CLASS'     => $eqdkp->switch_row_class(),
            'DATE'          => ( !empty($item['item_date']) ) ? date($user->style['date_notime_short'], $item['item_date']) : '&nbsp;',
            'U_VIEW_ITEM'   => 'viewitem.php'.$SID.'&amp;' . URI_ITEM . '=' . $item['item_id'],
            'U_VIEW_RAID'   => 'viewraid.php'.$SID.'&amp;' . URI_RAID . '=' . $item['raid_id'],
            'NAME'          => itemstats_decorate_name(stripslashes($item['item_name'])),
            'RAID'          => ( !empty($item['raid_name']) ) ? stripslashes($item['raid_name']) : '&lt;<i>Not Found</i>&gt;',
            'SPENT'         => $item['item_value'],
            'CURRENT_SPENT' => sprintf("%.2f", $current_spent))
        );
        $current_spent -= $item['item_value'];
    }
    $db->free_result($items_result);

    $total_purchased_items = $db->query_first('SELECT count(*) FROM ' . ITEMS_TABLE . " WHERE item_buyer='" . $member['member_name'] . "' ORDER BY item_date DESC");
		
		// Profile Extensions
		include_once('profileext/ts.php');
		// Profile Extensions

    $tpl->assign_vars(array(
	      'GUILDTAG' 												=> $eqdkp->config['guildtag'],
	      'NAME'     												=> $member['member_name'],
		    'RACENAME'     										=> $race_name['race_name'],
		    'CLASSNAME'     									=> $class_name['class_name'],
				'PROFILE_PICTURE'									=> ( $member['picture'] ) ? 'upload/'.$member['picture'] : 'no_pic.png',
				'GUILD'														=> $member['guild'],
				
				'BLASC_ID'												=> $member['blasc_id'],
				'CTPROFILE_ID'										=> $member['ct_profile'],
				'ALLA_ID'													=> $member['allakhazam'],
				'CURSE_ID'												=> $member['curse_profiler'],
				'TALENT_URL'											=> $member['talentplaner'],
				'SHOW_BLASC'											=> ( $member['blasc_id'] ) ? true : false,
				'SHOW_CTPROFILE'									=> ( $member['ct_profile'] ) ? true : false,
				'SHOW_ALLA'												=> ( $member['allakhazam'] ) ? true : false,
				'SHOW_CURSE'											=> ( $member['curse_profiler'] ) ? true : false,
				'SHOW_TALENT'											=> ( $member['talentplaner'] ) ? true : false,

        'L_EARNED'                        => $user->lang['earned'],
        'L_SPENT'                         => $user->lang['spent'],
        'L_ADJUSTMENT'                    => $user->lang['adjustment'],
        'L_CURRENT'                       => $user->lang['current'],
        'L_RAIDS_30_DAYS'                 => sprintf($user->lang['raids_x_days'], 30),
        'L_RAIDS_60_DAYS'                 => sprintf($user->lang['raids_x_days'], 60),
        'L_RAIDS_90_DAYS'                 => sprintf($user->lang['raids_x_days'], 90),
        'L_RAIDS_LIFETIME'                => sprintf($user->lang['raids_lifetime'],
                                                date($user->style['date_notime_short'], $member['member_firstraid']),
                                                date($user->style['date_notime_short'], $member['member_lastraid'])),
        'L_ARCANE'												=> $user->lang['uc_res_arcane'],
    		'L_FIRE'													=> $user->lang['uc_res_fire'],
    		'L_NATURE'												=> $user->lang['uc_res_nature'],
    		'L_FROST'													=> $user->lang['uc_res_frost'],
    		'L_SHADOW'												=> $user->lang['uc_res_shadow'],
    		'L_LAST_5_RAIDS'									=> $user->lang['uc_last_5_raids'],
    		'L_LAST_5_ITEMS'									=> $user->lang['uc_last_5_items'],
    		'L_CLASS'													=> $user->lang['class'],
    		'L_RACE'													=> $user->lang['race'],
    		'L_GUILD'													=> $user->lang['uc_guild'],
    		'L_EXT_PROFILE'										=> $user->lang['uc_ext_profile'],
    		'L_RAID_INFOS'										=> $user->lang['uc_raid_infos'],
        'L_RAID_ATTENDANCE_HISTORY'       => $user->lang['raid_attendance_history'],
        'L_DATE'                          => $user->lang['date'],
        'L_NAME'                          => $user->lang['name'],
        'L_SKILL'													=> $user->lang['uc_tab_skills'],
        'L_NOTE'                          => $user->lang['note'],
        'L_EARNED'                        => $user->lang['earned'],
        'L_CURRENT'                       => $user->lang['current'],
        'L_ITEM_PURCHASE_HISTORY'         => $user->lang['item_purchase_history'],
        'L_RAID'                          => $user->lang['raid'],
        'L_INDIVIDUAL_ADJUSTMENT_HISTORY' => $user->lang['individual_adjustment_history'],
        'L_REASON'                        => $user->lang['reason'],
        'L_ADJUSTMENT'                    => $user->lang['adjustment'],
        'L_ATTENDANCE_BY_EVENT'           => $user->lang['attendance_by_event'],
        'L_EVENT'                         => $user->lang['event'],
        'L_PERCENT'                       => $user->lang['percent'],
        'L_CHAR_INFO'											=> $user->lang['uc_char_info'],
        
        'L_BUFFED'												=> $user->lang['uc_buffed'],
        'L_ALLAKHAZAM'										=> $user->lang['uc_allakhazam'],
        'L_CTPROFILES'										=> $user->lang['uc_ctprofiles'],
        'L_CURSEPROFILES'									=> $user->lang['uc_curse_profiler'],
        'L_TALENTPLANER'									=> $user->lang['uc_talentplaner'],
        
        // the modules languages
        'L_RECEIVES'											=> $user->lang['uc_receives'],

				'FIRE'														=> ( $member['fir'] ) ? $member['fir'] : 0,
       	'ARCANE'													=> ( $member['ar'] ) ? $member['ar'] : 0,
       	'FROST'														=> ( $member['frr'] ) ? $member['frr'] : 0,
       	'NATURE'													=> ( $member['nr'] ) ? $member['nr'] : 0,
       	'SHADOW'													=> ( $member['sr'] ) ? $member['sr'] : 0,
       	'SKILL'														=> ( $member['skill_1'] or $member['skill_2'] or $member['skill_3']) ? $member['skill_1'].' - '.$member['skill_2'].' - '.$member['skill_3'] : '--',
        'EARNED'         									=> $member['member_earned'],
        'SPENT'         									=> $member['member_spent'],
        'ADJUSTMENT'     									=> $member['member_adjustment'],
        'CURRENT'        									=> $member['member_current'],
        'RAIDS_30_DAYS'  									=> sprintf($user->lang['of_raids'], $percent_of_raids['30']),
        'RAIDS_60_DAYS'  									=> sprintf($user->lang['of_raids'], $percent_of_raids['60']),
        'RAIDS_90_DAYS'  									=> sprintf($user->lang['of_raids'], $percent_of_raids['90']),
        'RAIDS_LIFETIME' 									=> sprintf($user->lang['of_raids'], $percent_of_raids['lifetime']),

        'C_ADJUSTMENT'     								=> color_item($member['member_adjustment']),
        'C_CURRENT'        								=> color_item($member['member_current']),
        'C_RAIDS_30_DAYS'  								=> color_item($percent_of_raids['30'], true),
        'C_RAIDS_60_DAYS'  								=> color_item($percent_of_raids['60'], true),
        'C_RAIDS_90_DAYS'  								=> color_item($percent_of_raids['90'], true),
        'C_RAIDS_LIFETIME' 								=> color_item($percent_of_raids['lifetime'], true),

				'L_VERSION'             					=> $pm->get_data('charmanager', 'version'),
    		'ICON_INFO'												=> 'images/info.png',
    		'L_CREDIT_NAME'										=> $user->lang['uc_credit_name'],
    		'L_ABOUT_HEADER'									=> $user->lang['about_header'],

        'U_VIEW_MEMBER' 									=> 'viewmember.php' . $SID . '&amp;' . URI_NAME . '=' . $member['member_name'] . '&amp;')
    );

    $db->free_result($adjustments_result);

    $pm->do_hooks('/viewmember.php');

    $eqdkp->set_vars(array(
        'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['viewmember_title'], $member['member_name']),
        'template_file' => 'profile.html',
        'template_path' => $pm->get_data('charmanager', 'template_path'),
        'display'       => true)
    );
}
else
{
    message_die($user->lang['error_invalid_name_provided']);
}

// ---------------------------------------------------------
// Page-specific functions
// ---------------------------------------------------------
function raid_count($start_date, $end_date, $member_name)
{
    global $db;

    $raid_count = $db->query_first('SELECT count(*) FROM ' . RAIDS_TABLE . ' WHERE (raid_date BETWEEN ' . $start_date . ' AND ' . $end_date . ')');

    $sql = 'SELECT count(*)
            FROM ' . RAIDS_TABLE . ' r, ' . RAID_ATTENDEES_TABLE . " ra
            WHERE (ra.raid_id = r.raid_id)
            AND (ra.member_name='" . $member_name . "')
            AND (r.raid_date BETWEEN " . $start_date . ' AND ' . $end_date . ')';
    $individual_raid_count = $db->query_first($sql);

    $percent_of_raids = ( $raid_count > 0 ) ? round(($individual_raid_count / $raid_count) * 100) : 0;

    $raid_count_stats = array(
        'percent'     => $percent_of_raids,
        'total_count' => $raid_count,
        'indiv_count' => $individual_raid_count);

    return $raid_count_stats['percent']; // Only thing needed ATM
}
?>