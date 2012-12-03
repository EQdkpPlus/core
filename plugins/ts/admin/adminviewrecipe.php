<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * viewrecipe.php
 * Began: Sun September 10 2009
 *
 * $Id: viewrecipe.php 1 2006-09-10 01:53:35Z Achaz $
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
include_once($eqdkp_root_path . 'plugins/ts/includes/functions.php');

if(!function_exists(itemstats_decorate_name)) { message_die('The <b>Itemstats</b> plugin was not found, the tradeskill plugin will not function properly without the itemstats plugin.'); }

if (!$pm->check(PLUGIN_INSTALLED, 'ts')) { message_die('The Tradeskill plugin is not installed.'); }

$user->check_auth('u_ts_list');

$ts = $pm->get_plugin(PLUGIN);

if ($_POST['recipe_id']) { $info = update_tradeskill($_POST['recipe_id'], 0 ,$_POST['chosen_member_id']);}

global $table_prefix;
global $twoProfs;
if (!defined('RP_TRADESKILL_TABLE')) { define('RP_TRADESKILL_TABLE', $table_prefix . 'tradeskills'); }
if (!defined('RP_RECIPES_TABLE')) { define('RP_RECIPES_TABLE', $table_prefix . 'tradeskill_recipes'); }
if (!defined('RP_USERS_TABLE')) { define('RP_USERS_TABLE', $table_prefix . 'tradeskill_users'); }
if (!defined('RP_TUSERS_TABLE')) { define('RP_TUSERS_TABLE', $table_prefix . 'user_tradeskills'); }
//Achaz
if (!defined('TS_CONFIG_TABLE')) { define('TS_CONFIG_TABLE', $table_prefix . 'tradeskill_config'); }

//GET CONFIGS
$sql = 'SELECT * FROM ' . TS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

$twoProfs=$conf['ts_restrict_professions'];
//-----


//---
function get_Profession($member_id, $number)
{
	global $db;

	$sql = 'SELECT trade_id, member_id, ps
        	FROM ' . RP_TUSERS_TABLE . '
		WHERE member_id = ' . $member_id . '
		AND ps = ' . $number;

	$result = $db->query($sql);
	$row = $db->fetch_record($result);
#DEBUG print "primary:" . $member_id . " - " . $trade_id;
	if ($row)
	{	
		return $row['trade_id'];
	}
	else
	{
		return;
	}
}

if ( (isset($_GET[URI_ITEM])) && (intval($_GET[URI_ITEM] > 0)) )
{

    // We want to view items by name and not id, so get the name
    $recipe_name = $db->query_first('SELECT recipe_name FROM ' . RP_RECIPES_TABLE . " WHERE id='".$_GET[URI_ITEM]."'");

    if ( empty($recipe_name) )
    {
        message_die($user->lang['error_invalid_item_provided']);
    }

    // Item stats
    if ( $pm->check(PLUGIN_INSTALLED, 'stats') )
    {
        $show_stats = true;

        //  $sql = 'SELECT item_id
       // FROM ' . ITEM_STATS_TABLE . "
       //     WHERE item_name='" . addslashes($item_name) . "'";
       // $stat_id = $db->query_first($sql);

        $sql = 'SELECT id
                FROM ' . ITEM_STATS_TABLE . "
                WHERE name='" . addslashes($recipe_name) . "'";
        $stat_id = $db->query_first($sql);

        if ( !$stat_id )
        {
            $show_stats = false;
            $u_view_stats = '';
        }
        else
        {
            $u_view_stats = $eqdkp_root_path . 'plugins/' . $pm->get_data('stats', 'path') . '/itemshot.php' . $SID . '&amp;' . URI_ITEM . '=' . $stat_id . '&amp;iframe=true';
        }
    }
    else
    {
        $show_stats = false;
        $u_view_stats = '';
    }
    
    
    //Construction of owner-List
$recipedata = $db->query('SELECT trade_id, addedby FROM ' . RP_RECIPES_TABLE . " WHERE id='".$_GET[URI_ITEM]."'");
$recipedataarray = $db->fetch_record($recipedata);

$trade_id = $recipedataarray['trade_id'];
$added_by = $recipedataarray['addedby'];

if($twoProfs==1 && $trade_id != 7)//check if a member associated with user has profession with trade_id
{
		//----BLOCK CHANGED BY ACHAZ: only show members which currently have the trade checked ------------
		$owner_sql = 'SELECT tradeusers.member_id, tradeusers.trade_id, members.member_name, members.member_id, tusers.member_id, tusers.trade_id, tusers.ps
        	FROM ' . RP_USERS_TABLE . ' as tradeusers, ' . MEMBERS_TABLE . ' as members, ' . RP_TUSERS_TABLE . ' as tusers
		WHERE tradeusers.trade_id = ' . $_GET[URI_ITEM] . '
		AND tradeusers.member_id = members.member_id
		AND tradeusers.member_id = tusers.member_id
		AND tusers.trade_id = ' . $trade_id . '
		AND tusers.ps != 0
		ORDER BY members.member_name';
}
		else
{
		$owner_sql = 'SELECT tradeusers.member_id, tradeusers.trade_id, members.member_name, members.member_id
        	FROM ' . RP_USERS_TABLE . ' as tradeusers, ' . MEMBERS_TABLE . ' as members
		WHERE tradeusers.trade_id = ' . $_GET[URI_ITEM] . '
		AND tradeusers.member_id = members.member_id
		ORDER BY members.member_name';		
}

		if (!($owner_result = $db->query($owner_sql))) { message_die('Could not obtain owner information', '', __FILE__, __LINE__, $owner_sql); }
		
		$owner = "";
		$orun = 0;

		// GENERATE OWNER LIST!
		while ( $owner_row = $db->fetch_record($owner_result) )
{
			$orun = $orun+1;
			if ($orun>1) 
{
				$owner = $owner . ', ' . $owner_row['member_name'];
}
			else
{
				$owner = $owner_row['member_name'];
}

}

//---------------------------------

$sql = 'SELECT member_name, member_id, member_status  FROM ' . MEMBERS_TABLE . ' ORDER BY member_name';

if ( !($members_result = $db->query($sql)) )
{
        message_die('Could not obtain member information', '', __FILE__, __LINE__, $sql);
}

while ( $row = $db->fetch_record($members_result) )
{
	$get_owner = get_owner($_GET[URI_ITEM], 0, $row['member_id']);
	
	$first = get_Profession($row['member_id'],1);
	$second = get_Profession($row['member_id'],2);
	
	$firstname = $db->query_first('SELECT trade_name FROM ' . RP_TRADESKILL_TABLE . " WHERE trade_id='".$first."'");
	$secondname = $db->query_first('SELECT trade_name FROM ' . RP_TRADESKILL_TABLE . " WHERE trade_id='".$second."'");
	
	$tpl->assign_block_vars('members_row', array(
		'ROW_CLASS'     => $eqdkp->switch_row_class(),
		'FIRST_SKILL' => ( ($first == $trade_id ) ?  $firstname  : '<i>' . $firstname . '</i>' ),
		'SECOND_SKILL' => ( ($second == $trade_id ) ?  $secondname  : '<i>' . $secondname . '</i>' ),
		'ID'    => $row['member_id'],
		 'NAME' => (( $row['member_status'] == '0' ) ? '<i>' . $row['member_name'] .'</i>' : $row['member_name']),
		 'F_UPDATE' => "adminviewrecipe.php" . $SID .'&amp;' . URI_ITEM . '='.$_GET[URI_ITEM].'&amp;',
		 'ISOWNER' => ($get_owner != 'N') ? 'checked' : '',
		 ));
}

//---------------------------------
$tradename = $db->query_first('SELECT trade_name FROM ' . RP_TRADESKILL_TABLE . " WHERE trade_id='".$trade_id."'");
$added_by_member = $db->query_first('SELECT username FROM ' . USERS_TABLE . " WHERE user_id='".$added_by."'");


    $tpl->assign_vars(array(
    	'L_FIRST_SKILL' => $user->lang['ts_prof1'],
	'L_SECOND_SKILL' => $user->lang['ts_prof2'],
    	'S_PROFS' => ( $twoProfs == 1 ? true : false ) ,
    	'L_MEMBERNAME' =>$user->lang['name'],
	'L_OWNER' => $user->lang['ts_owner'],
        'RECIPEID' => $_GET[URI_ITEM],
       	'ADDEDBY' => $added_by_member,
	'L_ADDEDBY' => $user->lang['ts_added_by'],
    	'TRADE' => $tradename,
	'L_TRADE' => $user->lang['ts_trade'],
        'OWNER' => $owner,
	'L_OWNER' => $user->lang['ts_owner'],
        'S_STATS' => $show_stats,
	'ITEM_STATS' => itemstats_get_html($recipe_name),

        'U_VIEW_ITEM' => 'viewrecipe.php'.$SID.'&amp;' . URI_ITEM . '='.$_GET[URI_ITEM].'&amp;',
        'U_VIEW_STATS' => $u_view_stats)
    );

    $pm->do_hooks('/viewrecipe.php');

    $eqdkp->set_vars(array(
        'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.sprintf($user->lang['viewitem_title'], stripslashes($recipe_name)),
        'template_file' => 'admin/viewrecipe.html',
        'template_path' => $pm->get_data('ts', 'template_path'),
        'display'       => true)
    );
}
else
{
    message_die($user->lang['error_invalid_item_provided']);
}
?>
