<?php
//::///////////////////////////////////////////////
//::
//:: EQDKP PLUGIN: Tradeskills
//:: © 2006 CNSDEV (http://cnsdev.dk)
//:: Contact: Cralex_NS - cns@cnsdev.dk
//::
//:://////////////////////////////////////////////
//::
//:: File: tradeskills.php (user option page)
//:: Created on: 20. Jan 2006
//::
//:: DEPENDENCIES:
//:: * Itemstats2 or higher
//::
//:://////////////////////////////////////////////

define('EQDKP_INC', true);
define('PLUGIN', 'raidplan');
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');
//include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');

# $user->check_auth('u_ts_list');

if (!$pm->check(PLUGIN_INSTALLED, 'ts')) { message_die('The Tradeskill plugin is not installed.'); }
$ts = $pm->get_plugin('ts');

global $table_prefix;
if (!defined('RP_TRADESKILL_TABLE')) { define('RP_TRADESKILL_TABLE', $table_prefix . 'tradeskills'); }
if (!defined('RP_RECIPES_TABLE')) { define('RP_RECIPES_TABLE', $table_prefix . 'tradeskill_recipes'); }
if (!defined('RP_USERS_TABLE')) { define('RP_USERS_TABLE', $table_prefix . 'tradeskill_users'); }
if (!defined('RP_TUSERS_TABLE')) { define('RP_TUSERS_TABLE', $table_prefix . 'user_tradeskills'); }
//Achaz
if (!defined('TS_CONFIG_TABLE')) { define('TS_CONFIG_TABLE', $table_prefix . 'tradeskill_config'); }

//Achaz
//GET CONFIGS
$sql = 'SELECT * FROM ' . TS_CONFIG_TABLE;
if (!($settings_result = $db->query($sql))) { message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql); }
while($roww = $db->fetch_record($settings_result)) {
  $conf[$roww['config_name']] = $roww['config_value'];
}

//$conf['ts_show_cooking'];

if ($_POST['member_id']) { $info = update_tradeskill($_POST['member_id'], $_POST['trade1'], $_POST['trade2']); }


function get_primary($member_id, $trade_id, $primary)
{
	global $db;

	$sql = 'SELECT trade_id, member_id, ps
        	FROM ' . RP_TUSERS_TABLE . '
		WHERE trade_id= ' . $trade_id . '
		AND member_id = ' . $member_id . '
		AND ps = ' . $primary;

	$result = $db->query($sql);
	$row = $db->fetch_record($result);
	#DEBUG print "primary:" . $member_id . " - " . $trade_id;
	if ($row)
	{	
		return $row['ps'];
	}
	else
	{
		return N;
	}

}

function update_tradeskill($member_id, $trade1, $trade2)
{
	global $db, $user;

	$trade = array(1 => $trade1, 2 => $trade2);
	$user_id = $user->data['user_id'];
	$run = 0;

	$userowns = $db->query_first('SELECT count(*) FROM ' . MEMBER_USER_TABLE . ' 
					WHERE user_id=' . $user_id . '
					AND member_id =' . $member_id);

	if (!$userowns) { message_die('You are trying to alter the tradeskills of a character that you do not own.. wtf?!'); }
	
	while ($run < 2) 
	{
		$run++;

		$sql = 'SELECT member_id, trade_id, ps
        	FROM ' . RP_TUSERS_TABLE . '
		WHERE member_id = ' . $member_id . '
		AND ps = ' . $run;
	
		$result = $db->query($sql);
		$row = $db->fetch_record($result);

		if ($row)
		{
			// UPDATE RECIPE REAGENTS
			$query = $db->build_query('UPDATE', array(
        			'trade_id' => $trade[$run],
				));
       		
		$db->query('UPDATE ' . RP_TUSERS_TABLE . ' SET ' . $query . ' WHERE member_id=' . $member_id . ' AND ps =' . $run);
			#DEBUG print "updating"; print $trade[$run];
		}
		else
		{
			// ADD RECIPE
			$query = $db->build_query('INSERT', array(
        		'trade_id'				=> $trade[$run],
        		'member_id'				=> $member_id,
			'ps'					=> $run,
			));

       			$db->query('INSERT INTO ' . RP_TUSERS_TABLE . $query);
			#DEBUG print "adding new";
		}

	}
	
	
}

if ($conf['ts_restrict_professions'] != 1)
{
  $tpl->assign_vars(array('E_NO_USE_PROFS' => $user->lang['ts_no_profsinuse'], 'NOPROFS' => TRUE) );
}
else
{
  $tpl->assign_vars(array('PROFS' => TRUE) );
}

if ($conf['ts_show_cooking'] != 1)
{
  $tpl->assign_vars(array('COOKING' => FALSE) );
}
else
{
  $tpl->assign_vars(array('COOKING' => TRUE) );
}


$member_count = $db->query_first('SELECT count(*) FROM ' . MEMBER_USER_TABLE . ' as muser, ' . MEMBERS_TABLE . ' as member
	WHERE muser.user_id = ' . $user->data['user_id'] . '
	AND muser.member_id = member.member_id
	ORDER BY member.member_name ASC');

if ($member_count < 1) 
{ 
	$tpl->assign_vars(array('E_NO_MEMBER' => $user->lang['ts_nomember'], 'NOMEMBER' => TRUE));
}

$member_sql = 'SELECT member.member_name, muser.member_id, muser.user_id
        FROM ' . MEMBER_USER_TABLE . ' as muser, ' . MEMBERS_TABLE . ' as member
	WHERE muser.user_id = ' . $user->data['user_id'] . '
	AND muser.member_id = member.member_id
	ORDER BY member.member_name ASC';


if (!($member_result = $db->query($member_sql))) { message_die('Could not obtain member information', '', __FILE__, __LINE__, $member_sql); }


while ( $member_row = $db->fetch_record($member_result) )
{ 
	$member_id = $member_row['member_id'];

	$tpl->assign_block_vars('member_row', array(
        'MEMBER_NAME' => $member_row['member_name'],
	'MEMBER_ID' => $member_row['member_id']
	));

	$trade_sql = 'SELECT trade_id, trade_icon, trade_name, inuse
        	FROM ' . RP_TRADESKILL_TABLE . '
		WHERE trade_id >=0
		ORDER BY trade_id ASC';

	if (!($trade_result = $db->query($trade_sql))) { message_die('Could not obtain trade information', '', __FILE__, __LINE__, $recipe_sql); }

	while ( $trade_row = $db->fetch_record($trade_result) )
	{
		//if($trade_row['trade_id'] != 7 || $conf['ts_show_cooking'] == 1)
		if($trade_row['trade_id'] != 7 && $trade_row['inuse']== 1)
		//if($trade_row['trade_id'] != 7)
		{
		$tpl->assign_block_vars('member_row.trade_row', array(
		'TRADESKILL_ICON' => $trade_row['trade_icon'],
       		'TRADESKILL_NAME' => $trade_row['trade_name'],
       		'ID' => $trade_row['trade_id'],
		'TRADE1' => ( get_primary($member_id, $trade_row['trade_id'],1) != 'N' ? 'checked' : $null ),
		'TRADE2' => ( get_primary($member_id, $trade_row['trade_id'],2) != 'N' ? 'checked' : $null ),
		));
		}
	}
		
}

$tpl->assign_vars(array(
    'L_MANAGE_TRADESKILLS' => $user->lang['ts_managetrade'],
    'L_MEMBER_TRADESKILLS' => $user->lang['ts_tradeskills'],
    'L_SELECT_TRADESKILLS' => $user->lang['ts_select_tradeskills'],
    'L_COOKING_SHOWN' => $user->lang['ts_cooking'],
    'L_PROF1' => $user->lang['ts_prof1'],
    'L_PROF2' => $user->lang['ts_prof2'],
    'F_UPDATE' => "tradeskills.php",
    
    'O_RECIPE' => $current_order['uri'][0],
    
    'U_LIST_RECIPE' => 'index.php'.$SID.'&amp;',
));

$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['ts'],

	'template_file' => 'tradeskills.html',
	'template_path' => $pm->get_data('ts', 'template_path'),
	'display'       => true)
);
       
?>
