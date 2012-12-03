<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
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

if (!$user->check_auth('u_member_man', false) && !$user->check_auth('u_member_add', false)) {
	 message_die($user->lang['uc_no_prmissions']);
}

if ($user->data['username']=="") { message_die($user->lang['uc_not_loggedin']); }

// save Connection
if($in->get('connection_submit')){	
	$CharTools->updateConnection($in->getArray('member_id', 'int'));	
	redirect('characters.php'.$SID);
}

// Delete Char
if($user->check_auth('u_member_del', false) && $in->get('delete_id', 0) > 0){
	$CharTools->SuspendChar($in->get('delete_id', 0));
	redirect('characters.php'.$SID);
}
	
// Build member drop-down
$sort_order = array(
	0 => array('member_name', 'member_name desc'),
	1 => array('member_level desc', 'member_level'),
	2 => array('member_class', 'member_class desc'),
	3 => array('member_rank', 'member_rank desc'),
	4 => array('guild', 'guild desc')
);

$current_order = switch_order($sort_order);

$member_count = 0;
$sort_index = explode('.', $current_order['uri']['current']);
$previous_source = preg_replace('/( (asc|desc))?/i', '', $sort_order[$sort_index[0]][$sort_index[1]]);               

$member_data = $pdh->get('member_connection', 'connection', array($user->data['user_id']));
if(is_array($member_data)){
	foreach($member_data as $row){
		$last_update = ($row['last_update']) ? date($user->lang['uc_changedate'],$row['last_update']) : '--';
		$member_count++;
		
		// Action Menu
		$cm_actions= array(
			0 => array(
				'name'		=> $user->lang['uc_edit_char'],
				'link'		=> "javascript:EditChar('".$row['member_id']."')",
				'img'		=> 'edit.png',
				'perm'		=> $user->check_auth('u_member_view', false),
			),
			1 => array(
				'name'		=> $user->lang['uc_delete_char'],
				'link'		=> "javascript:DeleteChar('".$row['member_id']."')",
				'img'		=> 'delete.png',
				'perm'		=> $user->check_auth('u_member_del', false),
			),
			2 => array(
				'name'		=> $user->lang['uc_updat_armory'],
				'link'		=> "javascript:UpdateChar('".$row['member_id']."')",
				'img'		=> 'update.png',
				'perm'		=> $game->get_importAuth('u_member_view', 'char_update'),
			),
		);

		$tpl->assign_block_vars('members_row', array(
			'ROW_CLASS'		=> $core->switch_row_class(),
			'ID'			=> $row['member_id'],
			'COUNT'			=> $member_count,
			'NAME'			=> $row['member_name'],
			'LEVEL'			=> $row['member_level'],

			'CLASSID'		=> $row['member_class_id'],
			'CLASS_ICON'	=> $game->decorate('classes', array($row['member_class_id'])),
			'RACE_ICON'		=> $game->decorate('races', array($row['member_race_id'])),
			'SPEC'			=> $spec1[icon],
			'SPEC2'			=> $spec2[icon],
			'RANK'			=> $row['rank_name'],
			'GUILD'			=> $row['guild'],
			'TBA'			=> ($row['require_confirm']) ? ' cm_confirm"' : '',
			'MBCONFIRMED'	=> ($row['require_confirm']) ? $user->lang['uc_need_confirmation'] : '',

			'ACTIONMENU'	=> $jquery->DropDownMenu('actionmenu'.$row['member_id'], $cm_actions, 'images/menues','<img src="images/global/edit.png" />'),
		));
	}
}

// Build member drop-down
$freemember_data = $pdh->get('member_connection', 'freechars', array($user->data['user_id']));

$mselect_list = $mselect_selected = array();
foreach($freemember_data as $row){
	$mselect_list[$row['member_id']] = $row['member_name'];
	if($row['user_id'] == $user->data['user_id']){
		$mselect_selected[] = $row['member_id'];
	}
}

	// Action Menu
	$cm_addmenu = array(
		0 => array(
			'name'		=> $user->lang['uc_add_char_plain'],
			'link'		=> "javascript:AddChar()",
			'img'		=> 'add.png',
			'perm'		=> $user->check_auth('u_member_add', false),
		),
		1 => array(
			'name'		=> $user->lang['uc_add_char_armory'],
			'link'		=> "javascript:AddCharArmory()",
			'img'		=> 'armory.png',
			'perm'		=> $game->get_importAuth('u_member_add', 'char_import'),
		),
		2 => array(
			'name'		=> $user->lang['uc_add_massupdate'],
			'link'		=> "javascript:MassUpdateChars()",
			'img'		=> 'update.png',
			'perm'		=> $game->get_importAuth('a_members_man', 'char_mupdate'),
		),
	);

	// Jquery stuff
	$jquery->Dialog('DeleteChar', '', array('message'=> $user->lang['uc_del_warning'], 'custom_js'=> "document.data.delete_id.value=v;document.data.submit();", 'withid'=>"editid"), 'confirm');
	$jquery->Dialog('AddChar', $user->lang['uc_add_char'], array('url'=>'addcharacter.php', 'width'=>'640', 'height'=>'450', 'onclose'=>'characters.php'));
	$jquery->Dialog('EditChar', $user->lang['uc_edit_char'], array('withid'=>'editid', 'url'=>"addcharacter.php?editid='+editid+'", 'width'=>'640', 'height'=>'450', 'onclose'=>'characters.php'));
	
	// The Importer things..
	if($game->get_importAuth('u_member_add', 'char_import')){
		$jquery->Dialog('AddCharArmory', $user->lang['uc_ext_import_sh'], array('url'=>$game->get_importers('char_import', true), 'width'=>'450', 'height'=>'180', 'onclose'=>'characters.php'));
	}
	if($game->get_importAuth('u_member_view', 'char_update')){
		$jquery->Dialog('UpdateChar', $user->lang['uc_ext_import_sh'], array('url'=>$game->get_importers('char_update', true)."?member_id='+memberid+'", 'width'=>'450', 'height'=>'180', 'onclose'=>'characters.php', 'withid'=>'memberid'));
	}
	if($game->get_importAuth('a_members_man', 'char_mupdate')){
		$jquery->Dialog('MassUpdateChars', $user->lang['uc_cache_update'], array('url'=>$game->get_importers('char_mupdate', true), 'width'=>'500', 'height'=>'130', 'onclose'=>'characters.php'));
	}

	if($pdh->get('member_connection', 'connection', array($user->data['user_id'])) < 1 && ($user->data['user_id'] != ANONYMOUS)){
		$show_no_conn_info = true;
	}
	$tpl->assign_vars(array(
		'F_UPDATE'				=> 'characters.php',
		'U_CHARACTERS'			=> 'characters.php' . $SID . '&amp;',
		'CLOSE_WINDOWS'			=> ( $closeWindows == true ) ? true : false,
		'IS_ADMIN'				=> ( $user->check_auth('a_plugins_man', false) ) ? true : false,

		'NEW_CHARS'				=> ( $user->check_auth('u_member_add', false)) ? true : false,
		'CONNECT_CHARS'			=> ( $user->check_auth('u_member_conn', false)) ? true : false,
		'DELETE_CHARS'			=> ( $user->check_auth('u_member_del', false)) ? true : false,

		// JS Code
		'JS_CONNECTIONS'		=> $jquery->MultiSelect('member_id', $mselect_list, $mselect_selected, '150'),
		'ADD_MENU'				=> $jquery->DropDownMenu('colortab', $cm_addmenu, 'images/menues','<img border="0" src="images/menues/add.png"/> '.$user->lang['uc_add_char']),

		'L_NAME'				=> $user->lang['name'],
		'L_LEVEL'				=> $user->lang['level'],
		'L_RANK'				=> $user->lang['rank'],
		'L_SPEC'				=> $user->lang['uc_tab_skills'],
		'L_GUILD'				=> $user->lang['uc_guild'],
		'L_EDIT_CHARS'			=> $user->lang['uc_edit_char'],
		'L_DELETE_CHARS'		=> $user->lang['uc_delete_char'],
		'L_SUBMIT'				=> $user->lang['uc_connectme'],
		'L_CONN_MEMBERS'		=> $user->lang['associated_members'],
		'L_ADD_CHAR'			=> $user->lang['uc_add_char'],
		'L_EDIT_MEMBER'			=> $user->lang['uc_edit_char'],
		'L_CONNECT_MEM'			=> $user->lang['uc_conn_members'],
		'L_CHAR_CONN'			=> $user->lang['uc_connections'],
		'L_CREDIT_NAME'			=> $user->lang['uc_credit_name'],
		'L_NO_CONN_INFO'		=> $user->lang['no_connected_char_info'],
		'S_SHOW_NO_CONN_INFO'	=> $show_no_conn_info,

		'L_TOOLTIP3'			=> $user->lang['uc_tt_n3'],
		'L_TOOLTIP2'			=> $user->lang['uc_tt_n2'],

		'O_NAME'				=> $current_order['uri'][0],
		'O_LEVEL'				=> $current_order['uri'][1],
		'O_CLASS'				=> $current_order['uri'][2],
		'O_RANK'				=> $current_order['uri'][3],
		'O_GUILD'				=> $current_order['uri'][4],

		'ICON_INFO'				=> 'images/info.png',
		'US_FOOTCOUNT'			=> sprintf($user->lang['listmembers_footcount'], count($member_data)),
	));

$core->set_vars(array(
	'page_title'		=> $user->lang['manage_members_titl'],
	'template_file'		=> 'characters.html',
	'display'			=> true)
);
?>