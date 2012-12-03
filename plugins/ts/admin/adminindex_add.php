<?php
//::///////////////////////////////////////////////
//::
//:: EQDKP PLUGIN: Tradeskills
//:: © 2006 CNSDEV (http://cnsdev.dk)
//:: Contact: Cralex_NS - cns@cnsdev.dk
//::
//:://////////////////////////////////////////////
//::
//:: File: adminindex-add.php (main admin-script)
//:: Created on: 20. Jan 2006
//::
//:: DEPENDENCIES:
//:: * Itemstats2 or higher
//::
//:://////////////////////////////////////////////
//:: 02.10.2006 edited support for parsing infosite for reagents - Achaz
//:: 05.10.2006 edited 3rd permission option - Achaz
//:: 06.10.2006 edited bug to show names when trade is no longer checked - Achaz
//:: trying to add some support for blasc if the list name is not the wowname
//:: VERSION: 0.15alpha

//DONE: included configs, implement edit function, implement no prof restriction, implement char choosing, implement cooking (hard coded on ID 7)
//TODO: test char choosing w/o having a char = language
//FURTHER TODO: change RP_... to TS_..., at some points trade_id means recipe_id actually, for example in the tradeskill user Table ->confusing
//TODO in other files: make an admin page of this page (removing check auth a here) therefore move functions to an include file, work on viewrecipe to show owners and addedby and make admin section in it like adding a raid

// EQdkp required files/vars
define('IN_ADMIN', true);
define('EQDKP_INC', true);
define('PLUGIN', 'ts');
$eqdkp_root_path = './../../../';
include_once($eqdkp_root_path . 'common.php');
include_once($eqdkp_root_path . 'itemstats/eqdkp_itemstats.php');
include_once($eqdkp_root_path . 'itemstats/itemstats.php');
//-------Functions ausgelagert-------
include_once($eqdkp_root_path . 'plugins/ts/includes/functions.php');

if(!function_exists(itemstats_decorate_name)) { message_die('The <b>Itemstats</b> plugin was not found, the tradeskill plugin will not function properly without the itemstats plugin.'); }

if (!$pm->check(PLUGIN_INSTALLED, 'ts')) { message_die('The Tradeskill plugin is not installed.'); }

$user->check_auth('u_ts_list');

$ts = $pm->get_plugin(PLUGIN);

global $table_prefix;
global $twoProfs;
global $show_cooking;

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

$twoProfs=$conf['ts_restrict_professions'];
$show_cooking = 1;

//-----------------Edit Achaz--------------------------------
//if($eqdkp->config['default_game'] == 'WoW_german')
if($conf['ts_use_infosite'] == 'buffed')
{
            include_once( $eqdkp_root_path . 'plugins/ts/includes/blascReagents.php');
}
elseif($conf['ts_use_infosite'] != 'buffed')
{
            include_once( $eqdkp_root_path . 'plugins/ts/includes/allakhazamReagents.php');
}
else
{
    message_die('Could not obtain configuration data', '', __FILE__, __LINE__, $sql);
}
//-----------------Edit Achaz End-----------------------------

$sort_order = array(
    0 => array('quality desc, recipe_name', 'quality, recipe_name'),
    1 => array('recipe_name desc', 'recipe_name'),
);
 
$current_order = switch_order($sort_order);
$total_recipes = $db->query_first('SELECT count(*) FROM ' . RP_RECIPES_TABLE . ' WHERE id>0');

if ($_POST['member_id']) {$current_member_id=$_POST['member_id'];}
elseif($_GET['member_id']) {$current_member_id=$_GET['member_id'];}
else { $current_member_id = 0;}

if ($_POST['recipe_id']) { $info = update_tradeskill($_POST['recipe_id'], $user->data['user_id'],$current_member_id); }
if ($_GET['delete']) { $info = delete_tradeskill($_GET['delete'], $user->data['user_id'], $_GET['trade_id']); }
if ($_POST['new_recipe']) { $info = add_tradeskill(stripslashes($_POST['new_recipe']), stripslashes($_POST['new_reagent']), $_POST['new_trade_id'], $user->data['user_id'], $current_member_id); }

//Achaz
if ($_GET['edit'])
{
    $sql = 'SELECT recipe_name, reagents FROM ' . RP_RECIPES_TABLE . '
            WHERE id = '. $_GET['edit'];
    $result = $db->query($sql);
    $row = $db->fetch_record($result);
    $unedited_recipe_name = $row['recipe_name'];
    $unedited_reagents = $row['reagents'];
    $id_to_edit = $_GET['edit'];
}
if ($_POST['edited_name']) { $info = edit_tradeskill($_POST['edit_recipe'], $user->data['user_id'], $_POST['trade_id'],stripslashes($_POST['edited_name']),stripslashes($_POST['edited_reagents'])); }

if ($_GET['hide']) { $info = hide_tradeskill($_GET['hide'], $user->data['user_id'], 1); }
if ($_GET['show']) { $info = hide_tradeskill($_GET['show'], $user->data['user_id'], 0); }

$item_stats = new ItemStats();


/*//get rid of $currentmemberid for admin use
$members = array();
//function get_members($user_id)
//{
	//
		// Get members
		//
  //echo ($user->data['user_id']);
		if ($user->data['user_id'])
{
			$sql = "SELECT users.user_id, members.member_id, members.member_name, classes.class_name
				FROM (" . MEMBERS_TABLE . " as members, " . MEMBER_USER_TABLE . " as users, " . CLASS_TABLE . " as classes)
                WHERE members.member_class_id=classes.class_id
				AND members.member_id=users.member_id
				AND users.user_id=" . $user->data['user_id'];
			$result = $db->query($sql);
			while ($row = $db->fetch_record($result))
{
               //echo ($row['member_id']);
				//$members[$row['member_id']] = array(
				$members[$row['member_id']] = array(
					'id'			=> $row['member_id'],
					'name'			=> $row['member_name'],
					'class_name'	=> $row['class_name'],
				//	'1stskill'		=> $row[''],
				//	'2ndskill'		=> $row['']
                      );
                $memberid_helper[] = array(
                    'id'  => $row['member_id'],
                    //'name' => $row['member_name'],
                    );
}
			$db->free_result($result);
}
//  return;
//}

//if no member is via POST chosen, choose first one
if($current_member_id == 0)
{
   //    echo('Zero='. $current_member_id);
    $current_member_id = $memberid_helper[0]['id'];
 //   echo('Cause Zero set to:'.$current_member_id);
}
/*else
{
    echo('Post worked:' . $current_member_id);
} */
$current_member_id = 0;


$trade_sql = 'SELECT trade_id, trade_icon, trade_name
        FROM ' . RP_TRADESKILL_TABLE . '
		WHERE trade_id > 0
	ORDER BY trade_name ASC';


if (!($trade_result = $db->query($trade_sql))) { message_die('Could not obtain recipe information', '', __FILE__, __LINE__, $trade_sql); }

$recipe_total = $db->query_first('SELECT count(*) FROM ' . RP_RECIPES_TABLE . ' WHERE trade_id>0');

while ( $trade_row = $db->fetch_record($trade_result) )
{ 
	$trade_id = $trade_row['trade_id'];
	$user_id = $user->data['user_id'];
 
	$get_trade_match = get_tradeskill_match(0, $user->data['user_id'], $trade_id, $current_member_id);
	
	$recipe_count = $db->query_first('SELECT count(*) FROM ' . RP_RECIPES_TABLE . ' WHERE trade_id = ' . $trade_id . '');
	
    $tpl->assign_block_vars('trade_row', array(
        'TRADESKILL_ICON' => $trade_row['trade_icon'],
        'TRADESKILL' => $trade_row['trade_name'],
	'FOOTCOUNT' => $trade_row['trade_name'] . ' (' . $recipe_count . '/' . $recipe_total . ')',
	'L_ADD' => ( $user->check_auth('a_ts_admin', false) ? $user->lang['ts_add'] : $null ),
	'ADD' => 'adminindex_add.php' . $SID .'&add=' . $trade_id . '&member_id=' . $current_member_id . '#' . $trade_id,
	'F_ADDNEW' => "adminindex_add.php" . $SID . '#' . $trade_id ,
	'S_ADD'=> ( $_GET['add'] == $trade_id ? true : false ),
	//Achaz: Edit-Funktion interaction with html
	//Permission to show Boxes?
	'S_EDIT'=>  ( $_GET['edit'] && ($_GET['trade_id']== $trade_id) ? true : false ),  // $_GET..prüft ob leer?
	'F_EDIT' => "adminindex_add.php" . $SID . '#' . $trade_id ,
	//ende achaz
	'ROW_CLASS' => $eqdkp->switch_row_class(),
	'ID' => $trade_id,
	'INFO' => $user->lang[$info['info']],
	'S_INFO' => ( $info['info_id'] == $trade_id ? true : false ),
        'U_FULLRINFO' => ((!$_GET['rinfo']) ? 'adminindex_add.php?rinfo=1#' . $trade_id : 'adminindex_add.php#' . $trade_id . $SID ),
	'O_RECIPE' => $current_order['uri'][1] . '#' . $trade_id,
	'O_QUALITY' => $current_order['uri'][0] . '#' . $trade_id,
	'HIDE' => (hide($user_id, $trade_id) == TRUE) ? true : false,
	'SHOW' => (hide($user_id, $trade_id) == TRUE) ? "?show=" . $trade_id . "#" . $trade_id : "?hide=" . $trade_id . "#" . $trade_id,
	));

	$recipe_sql = 'SELECT id, trade_id, recipe_name, reagents, addedby
        	FROM ' . RP_RECIPES_TABLE . '
		WHERE trade_id = ' . $trade_id . '
		ORDER BY ' . $current_order['sql'];

	if (!($recipe_result = $db->query($recipe_sql))) { message_die('Could not obtain recipe information', '', __FILE__, __LINE__, $recipe_sql); }

	if (hide($user_id, $trade_id) == FALSE)
{

	while ( $recipe_row = $db->fetch_record($recipe_result) )
{
		if($twoProfs==1 && $trade_id != 7)//check if a member associated with user has profession with trade_id
{
		//----BLOCK CHANGED BY ACHAZ: only show members which currently have the trade checked ------------
		$owner_sql = 'SELECT tradeusers.member_id, tradeusers.trade_id, members.member_name, members.member_id, tusers.member_id, tusers.trade_id, tusers.ps
        	FROM ' . RP_USERS_TABLE . ' as tradeusers, ' . MEMBERS_TABLE . ' as members, ' . RP_TUSERS_TABLE . ' as tusers
		WHERE tradeusers.trade_id = ' . $recipe_row['id'] . '
		AND tradeusers.member_id = members.member_id
		AND tradeusers.member_id = tusers.member_id
		AND tusers.trade_id = ' . $recipe_row['trade_id'] . '
		AND tusers.ps != 0
		ORDER BY members.member_name';
}
		else
{
		$owner_sql = 'SELECT tradeusers.member_id, tradeusers.trade_id, members.member_name, members.member_id
        	FROM ' . RP_USERS_TABLE . ' as tradeusers, ' . MEMBERS_TABLE . ' as members
		WHERE tradeusers.trade_id = ' . $recipe_row['id'] . '
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
		$get_owner = get_owner($recipe_row['id'], $user->data['user_id'], $current_member_id);


		// GENERATE REAGENT LIST
		$r = $recipe_row['reagents'] . ",";
		$r_num = substr_count($r, ',');
		$reagents = $null;
		$r_st = 0;

		if ((substr_count($r, 'x') >= 1) && (!$_GET['rinfo'])) 
{ 
			$reagents = '<table border="0" width="" cellspacing="0" cellpadding="0" class="borderless"><tr>';
			for ($i = 0; $i < $r_num; $i++)
{ 
			$epos = strpos($r, ',', $i);

			$r_string = substr($r, $r_st, $epos);
			$spos = strpos($r_string, 'x', 0);
			$r_amount = substr($r_string, 0, $spos);
			$r_name = strip_name(substr($r_string, $spos+1));
			$r_image = $item_stats->getItemIconLink($r_name, true);

			$r = substr(strstr($r, ','),1);
			
			$reagents .= '<td width="40" align="right" nowrap>' . $r_amount . ' <img src="' . $r_image . '" title="' . $r_name . '" height="18" width="18"></td>';
#DEBUG print "DEBUG: " . $recipe_row['recipe_name'] . " ((STPOS=" . $r_st . " EPOS=" . $epos . "SPOS=" . $spos . "R_AMOUNT=" . $r_amount . "R_NAME=" . $r_name . "R_IMAGE=" . $r_image . "))<br>";
}
			$reagents .= '<td></td></tr></table>';
}
		else 
{
			$reagents = $recipe_row['reagents'];
}		
	

		$tpl->assign_block_vars('trade_row.recipe_row', array(
		'ROW_CLASS' => $eqdkp->switch_row_class(),
       		'RECIPE' => itemstats_decorate_name($recipe_row['recipe_name']),
       		//------Edit Achaz 1 line------------
      		'U_VIEW_RECIPE' => 'adminviewrecipe.php'.$SID.'&amp;' . URI_ITEM . '='.$recipe_row['id'],
       		'REAGENT' => $reagents,
		//'ISOWNER' => ($get_owner != 'N') ? 'checked' : '', //removed for admin (checkbox was it)
		'ID' => $recipe_row['id'],
		'VALUE' => $get_owner,
		'F_UPDATE' => "adminindex_add.php" . '#' . $trade_id . '',
                   //--------- Achaz Edit 1 line: changed permission -----------		
		//'S_TRADE'=> ( $user->check_auth('a_ts_admin', false) ? true : false ), //removed for admin (checkbox was it)
		'OWNER' => $owner,
		'S_AUTHOR'=> ( $user->check_auth('a_ts_admin', false) ? true : false ),
		'DELETE' => 'adminindex_add.php' . $SID .'&delete=' . $recipe_row['id'] . '&trade_id=' . $trade_id .'&member_id='. $current_member_id .'#' . $trade_id ,
        	'EDIT' => 'adminindex_add.php'. $SID . '&edit=' . $recipe_row['id'] . '&trade_id=' . $trade_id .'&member_id='. $current_member_id .'#' . $trade_id ,
		//so übergibt man $_GET richtig
		));
}
}

}

//get_members( $user->data['user_id']);
if ($user->check_auth('u_ts_confirm', false) && count($members)>0)
//if (count($members)>0)
{
    //echo (count($members));
			foreach ($members as $member)
{
				$tpl->assign_block_vars('members', array(
					'VALUE'		=> $member['id'],
					'NAME'		=> $member['name'],
					'CLASS'		=> $member['class_name'],
				));
}
}
else {
          $tpl->assign_vars(array(
					'S_NO_USER_ASSIGN'		=> true,
                    'L_NO_USER_ASSIGNED'       => $user->lang['ts_no_user_assign'],
				));
}
$current_member_name = $members[$current_member_id]['name'];

$tpl->assign_vars(array(
    'UNEDITEDNAME' => $unedited_recipe_name,
    'UNEDITREAGENTS' => $unedited_reagents,
    'IDTOEDIT' => $id_to_edit,
    'L_CHAR_CHOSEN' => $user->lang['ts_char_chosen'],
    'L_CHAR' => $user->lang['ts_character'],
    'B_SELECTCHAR' => $user->lang['ts_b_selectchar'],
    'L_NOT_LOGGED_IN' => $user->lang['ts_not_logged_in'],
     'L_AD_MEMBERSKILLS_EXP' => $user->lang['ts_ad_tradeskills_exp'],
    //------------------------------------
    'L_RECIPE' => $user->lang['ts_recipe'],
    'L_REAGENT' => $user->lang['ts_reagent'],
    'L_FULLRINFO' => (($_GET['rinfo']) ? $user->lang['ts_collapse'] : $user->lang['ts_expand'] ),
    'L_OWNER' => $user->lang['ts_owner'],
    'L_QUALITY' => $user->lang['ts_quality'],
    'LSTAT_HIDDEN' => $user->lang['ts_show_list'],
    'LSTAT_SHOWN' => $user->lang['ts_hide_list'],
//-------------------------------------Edit Achaz 2 lines
    'L_ENTERNAME' => $user->lang['enternamestring'],
    'L_ENTERREAGENTS' => $user->lang['syntaxstring'],

    'U_LIST' => 'adminindex_add.php?',

));


$eqdkp->set_vars(array(
	'page_title'    => sprintf($user->lang['title_prefix'], $eqdkp->config['guildtag'], $eqdkp->config['dkp_name']).': '.$user->lang['ts'],

	'template_file' => 'admin/admints.html',
	'template_path' => $pm->get_data('ts', 'template_path'),
	'display'       => true)
);
       
?>
