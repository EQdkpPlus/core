<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_config_man');


class ManageTasks extends EQdkp_Admin
{

	function ManageTasks(){
		global $db, $core, $user, $tpl, $pm, $in, $pdh, $SID;

		parent::eqdkp_admin(); 
			
		$this->assoc_buttons(array(
			'form'		=> array(
			'name'		=> '',
			'process'	=> 'display_form',
			'check'		=> 'a_config_man'))
		);

		$this->assoc_params(array(
			'confirm_all'	=> array(
				'name'		=> 'mode',
				'value'		=> 'confirm_all',
				'process'	=> 'process_confirm_all',
				'check'		=> 'a_members_man'
			),
			'confirm'	=> array(
				'name'		=> 'mode',
				'value'		=> 'confirm',
				'process'	=> 'process_confirm',
				'check'		=> 'a_members_man',
			),
			'delete_all'	=> array(
				'name'		=> 'mode',
				'value'		=> 'delete_all',
				'process'	=> 'process_delete',
				'check'		=> 'a_members_man',
			),
			'delete'	=> array(
				'name'		=> 'mode',
				'value'		=> 'delete',
				'process'	=> 'process_delete',
				'check'		=> 'a_members_man',
			),
			'rewoke'	=> array(
				'name'		=> 'mode',
				'value'		=> 'rewoke',
				'process'	=> 'process_rewoke',
				'check'		=> 'a_members_man',
			),
			'activate_all'	=> array(
				'name'		=> 'mode',
				'value'		=> 'activate_all',
				'process'	=> 'process_activate_all',
				'check'		=> 'a_users_man',
			),
			'activate'	=> array(
				'name'		=> 'mode',
				'value'		=> 'activate',
				'process'	=> 'process_activate',
				'check'		=> 'a_users_man',
			),
			'delete_user'	=> array(
				'name'		=> 'mode',
				'value'		=> 'delete_user',
				'process'	=> 'process_delete_user',
				'check'		=> 'a_users_man',
			),
		));
	}

	function process_confirm_all(){
		global $db, $CharTools, $SID;
		$CharTools->ConfirmAllChars();
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_confirm(){
		global $db, $CharTools, $SID, $in;
		if ($in->exists('member')){
			$CharTools->ConfirmChar($in->get('member',0));
		}
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_delete(){
		global $db, $CharTools, $SID, $in;
		if ($in->exists('member')){
			$CharTools->DeleteChar($in->get('member',0));
		}
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_delete_all(){
		global $db, $CharTools, $SID, $in;
		$CharTools->DeleteAllChars();
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_activate(){
		global $db, $CharTools, $SID, $in, $pdh;
		if ($in->exists('user')){
			$db->query("UPDATE __users SET :params WHERE user_id=".$db->escape($in->get('user')), array(
				'user_active' => '1',
			));
		}
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_activate_all(){
		global $db, $CharTools, $SID, $in, $pdh;
		$inactive = $pdh->get('user', 'inactive');
		foreach ($inactive as $user_id){
			$db->query("UPDATE __users SET :params WHERE user_id=".$db->escape($user_id), array(
				'user_active' => '1',
			));
		}
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_rewoke(){
		global $db, $CharTools, $SID, $in, $pdh;
		if ($in->exists('member')){
			$CharTools->RewokeChar($in->get('member', 0));
		}
		redirect('admin/manage_tasks.php'.$SID);
	}

	function process_delete_user(){
		global $db, $CharTools, $SID, $in, $pdh;
		if ($in->exists('user')){
			$pdh->put('user', 'delete_user', array($in->get('user', 0), true));
		}
		redirect('admin/manage_tasks.php'.$SID);
	}

	function display_form(){
		global $db, $core, $user, $tpl, $pm, $in, $pdh, $SID, $game, $eqdkp_root_path, $time, $jquery;
		$nothing = true;

		//Confirm members
		$confirm = $pdh->get('member', 'confirm_required');
		if (count($confirm) > 0){
			$nothing = false;
			foreach ($confirm as $member){
				$tpl->assign_block_vars('confirm_row', array(
					'ROW_CLASS'		=> $core->switch_row_class(),
					'ID'			=> $member,
					'NAME'			=> $pdh->get('member', 'html_memberlink', array($member, $eqdkp_root_path.'viewcharacter.php', '')),
					'LEVEL'			=> $pdh->get('member', 'level', array($member)),
					'CLASS_ICON'	=> $game->decorate('classes', array($pdh->get('member', 'classid', array($member)))),
					'RACE_ICON'		=> $game->decorate('races', array($pdh->get('member', 'raceid', array($member)))),
				));
			}
		}

		//Delete members
		$deletion = $pdh->get('member', 'delete_requested');
		if (count($deletion) > 0){
			$nothing = false;
			foreach ($deletion as $member){
				$tpl->assign_block_vars('delete_row', array(
					'ROW_CLASS'		=> $core->switch_row_class(),
					'ID'			=> $member,
					'NAME'			=> $pdh->get('member', 'html_memberlink', array($member, $eqdkp_root_path.'viewcharacter.php', '')),
					'LEVEL'			=> $pdh->get('member', 'level', array($member)),
					'CLASS_ICON'	=> $game->decorate('classes', array($pdh->get('member', 'classid', array($member)))),
					'RACE_ICON'		=> $game->decorate('races', array($pdh->get('member', 'raceid', array($member)))),
				));
			}
		}

		//Inactive Users
		$inactive = $pdh->get('user', 'inactive');
		if (count($inactive) > 0){
			$nothing = false;
			foreach ($inactive as $member){
				$tpl->assign_block_vars('activate_row', array(
					'ROW_CLASS'		=> $core->switch_row_class(),
					'ID'			=> $member,      
					'NAME'			=> $pdh->get('user', 'name', array($member)),
					'EMAIL'			=> ($pdh->get('user', 'email', array($member))) ? '<a href="mailto:'.$pdh->get('user', 'email', array($member)).'">'.$pdh->get('user', 'email', array($member)).'</a>' : '',
					'REG'			=> $time->date($user->style['date_time'], $pdh->get('user', 'regdate', array($member))),
				));
			}
		}

		$jquery->Dialog('ConfirmAllChars', '', array('url' => 'manage_tasks.php'.$SID.'&mode=confirm_all', 'message' => $user->lang['uc_confirm_msg_all']), 'confirm');
		$jquery->Dialog('DeleteChar', '', array('url'=>'manage_tasks.php'.$SID.'&mode=delete&member=\'+v+\'', 'withid'=>'member', 'message'=>$user->lang['uc_del_warning']), 'confirm');
		$jquery->Dialog('DeleteAllChars', '', array('url'=>'manage_tasks.php'.$SID.'&mode=delete_all', 'message'=>$user->lang['uc_del_msg_all']), 'confirm');
		$jquery->Dialog('ActivateAllUsers', '', array('url'=>'manage_tasks.php'.$SID.'&mode=activate_all', 'message'=>$user->lang['activate_all_warning']), 'confirm');
		$jquery->Dialog('DeleteUser', '', array('url'=>'manage_tasks.php'.$SID.'&mode=delete_user&user=\'+v+\'', 'withid'=>'member', 'message'=>$user->lang['confirm_delete_user']), 'confirm');

		$tpl->assign_vars(array(
			'F_ACTION'			=> 'manage_tasks.php'.$SID,
			'S_CONFIRM'				=> (count($confirm) > 0) ? true : false,
			'S_DELETE'				=> (count($deletion) > 0) ? true : false,
			'S_INACTIVE'			=> (count($inactive) > 0) ? true : false,
			'S_NOTHING'				=> $nothing,

			'L_NAME'				=> $user->lang['name'],
			'L_EMAIL'				=> $user->lang['email'],
			'L_LEVEL'				=> $user->lang['level'],
			'L_ACTION'				=> $user->lang['action'],
			'L_DELETE_CHARS'		=> $user->lang['uc_delete_char'],
			'L_CONFIRM_CHARS'		=> $user->lang['uc_confirm_char'],
			'L_CONFIRM_LIST'		=> $user->lang['uc_confirm_list'],
			'L_CONFIRM_ALL'			=> $user->lang['uc_confirm_all'],
			'L_DELETE_LIST'			=> $user->lang['uc_delete_list'],
			'L_DELETE_ALL'			=> $user->lang['uc_delete_allchar'],
			'L_REWOKE_CHARS'		=> $user->lang['uc_rewoke_char'],
			'L_REGISTERED'			=> $user->lang['registered_at'],
			'L_ACTIVATE_LIST'		=> $user->lang['activate_list'],
			'L_ACTIVATE_ALL'		=> $user->lang['activate_all'],
			'L_ACTIVATE_USER'		=> $user->lang['activate_user'],
			'L_DELETE_USER'			=> $user->lang['delete_user'],
			'L_NO_TASKS'			=> $user->lang['uc_no_tasks'],
			'L_TASKS_INFO'			=> $user->lang['uc_tasks_info'],

			'FC_CONFIRM'			=> sprintf($user->lang['listmembers_footcount'], count($confirm)),
			'FC_DELETE'				=> sprintf($user->lang['listmembers_footcount'], count($deletion)),
		));

		$core->set_vars(array(
			'page_title'	=> $user->lang['uc_delete_manager'],
			'template_file'	=> 'admin/manage_tasks.html',
			'display'		=> true
		));
	}

}
$managetasks = new ManageTasks;
$managetasks->process();
?>