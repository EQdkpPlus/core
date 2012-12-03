<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class ManageTasks extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'config', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$handler = array(
			'mode' => array(
				array('process' => 'confirm_all',	'value' => 'confirm_all',	'check' => 'a_members_man', 'csrf'=>true),
				array('process' => 'confirm',		'value' => 'confirm',		'check' => 'a_members_man', 'csrf'=>true),
				array('process' => 'delete_all',	'value' => 'delete_all',	'check' => 'a_members_man', 'csrf'=>true),
				array('process' => 'delete',		'value' => 'delete',		'check' => 'a_members_man', 'csrf'=>true),
				array('process' => 'revoke',		'value' => 'revoke',		'check' => 'a_members_man', 'csrf'=>true),
				array('process' => 'activate',		'value' => 'activate',		'check' => 'a_users_man', 'csrf'=>true),
				array('process' => 'activate_all',	'value' => 'activate_all',	'check' => 'a_users_man', 'csrf'=>true),
				array('process' => 'delete_user',	'value' => 'delete_user',	'check' => 'a_users_man', 'csrf'=>true))
		);
		$this->user->check_auths(array('a_users_man', 'a_members_man'), 'OR');
		parent::__construct(false, $handler);
		$this->process();


	}

	public function confirm_all(){
		$confirm = $this->pdh->get('member', 'confirm_required');
		if(is_array($confirm)){
			foreach ($confirm as $member){
				$this->pdh->put('member', 'confirm', array($member));
			}
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}

	public function confirm(){
		if ($this->in->exists('member')){
			$this->pdh->put('member', 'confirm', array($this->in->get('member',0)));
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}

	public function delete(){
		if ($this->in->exists('member')){
			$this->pdh->put('member', 'delete_member', array($this->in->get('member',0)));
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}

	public function delete_all(){
		$deletion = $this->pdh->get('member', 'delete_requested');
		foreach($deletion as $member){
			$this->pdh->put('member', 'delete_member', array($member));
		}
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function activate(){
		if ($this->in->exists('user')) {
			$this->pdh->put('user', 'activate', array($this->in->get('user', 0)));
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}

	public function activate_all(){
		$this->pdh->put('user', 'activate', array($this->pdh->get('user', 'inactive')));
		$this->pdh->process_hook_queue();
		$this->display();
	}

	public function revoke(){
		if ($this->in->exists('member')){
			$this->pdh->put('member', 'revoke', array($this->in->get('member', 0)));
			$this->pdh->process_hook_queue();
		}
		$this->display();
	}

	public function delete_user(){
		if ($this->in->exists('user')){
			$this->pdh->put('user', 'delete_user', array($this->in->get('user', 0), true));
		}
		$this->display();
	}

	public function display(){
		$nothing = true;

		//Confirm members
		$confirm = $this->pdh->get('member', 'confirm_required');
		if (count($confirm) > 0){
			$nothing = false;
			foreach ($confirm as $member){
				$this->tpl->assign_block_vars('confirm_row', array(
					'ID'			=> $member,
					'NAME'			=> $this->pdh->get('member', 'html_memberlink', array($member, $this->root_path.'viewcharacter.php', '')),
					'LEVEL'			=> $this->pdh->get('member', 'level', array($member)),
					'CLASS_ICON'	=> $this->game->decorate('classes', array($this->pdh->get('member', 'classid', array($member)))),
					'RACE_ICON'		=> $this->game->decorate('races', array($this->pdh->get('member', 'raceid', array($member)), $this->pdh->get('member', 'gender', array($member)))),
				));
			}
		}

		//Delete members
		$deletion = $this->pdh->get('member', 'delete_requested');
		if (count($deletion) > 0){
			$nothing = false;
			foreach ($deletion as $member){
				$this->tpl->assign_block_vars('delete_row', array(
					'ID'			=> $member,
					'NAME'			=> $this->pdh->get('member', 'html_memberlink', array($member, $this->root_path.'viewcharacter.php', '')),
					'LEVEL'			=> $this->pdh->get('member', 'level', array($member)),
					'CLASS_ICON'	=> $this->game->decorate('classes', array($this->pdh->get('member', 'classid', array($member)))),
					'RACE_ICON'		=> $this->game->decorate('races', array($this->pdh->get('member', 'raceid', array($member)), $this->pdh->get('member', 'gender', array($member)))),
				));
			}
		}

		//Inactive Users
		$inactive = $this->pdh->get('user', 'inactive');
		if (count($inactive) > 0){
			$nothing = false;
			foreach ($inactive as $member){
				$this->tpl->assign_block_vars('activate_row', array(
					'ID'			=> $member,
					'NAME'			=> $this->pdh->get('user', 'name', array($member)),
					'EMAIL'			=> ($this->pdh->get('user', 'email', array($member))) ? '<a href="mailto:'.$this->pdh->get('user', 'email', array($member)).'">'.$this->pdh->get('user', 'email', array($member)).'</a>' : '',
					'REG'			=> $this->time->user_date($this->pdh->get('user', 'regdate', array($member)), true),
				));
			}
		}

		$this->jquery->Dialog('ConfirmAllChars', '', array('url' => 'manage_tasks.php'.$this->SID.'&mode=confirm_all&link_hash='.$this->CSRFGetToken('mode'), 'message' => $this->user->lang('uc_confirm_msg_all')), 'confirm');
		$this->jquery->Dialog('DeleteChar', '', array('url'=>'manage_tasks.php'.$this->SID.'&mode=delete&link_hash='.$this->CSRFGetToken('mode').'&member=\'+member+\'', 'withid'=>'member', 'message'=>$this->user->lang('uc_del_warning')), 'confirm');
		$this->jquery->Dialog('DeleteAllChars', '', array('url'=>'manage_tasks.php'.$this->SID.'&mode=delete_all&link_hash='.$this->CSRFGetToken('mode'), 'message'=>$this->user->lang('uc_del_msg_all')), 'confirm');
		$this->jquery->Dialog('ActivateAllUsers', '', array('url'=>'manage_tasks.php'.$this->SID.'&mode=activate_all&link_hash='.$this->CSRFGetToken('mode'), 'message'=>$this->user->lang('activate_all_warning')), 'confirm');
		$this->jquery->Dialog('DeleteUser', '', array('url'=>'manage_tasks.php'.$this->SID.'&mode=delete_user&link_hash='.$this->CSRFGetToken('mode').'&user=\'+v+\'', 'withid'=>'member', 'message'=>$this->user->lang('confirm_delete_user')), 'confirm');

		$this->tpl->assign_vars(array(
			'F_ACTION'			=> 'manage_tasks.php'.$this->SID,
			'S_CONFIRM'				=> (count($confirm) > 0) ? true : false,
			'S_DELETE'				=> (count($deletion) > 0) ? true : false,
			'S_INACTIVE'			=> (count($inactive) > 0) ? true : false,
			'S_NOTHING'				=> $nothing,
			'FC_CONFIRM'			=> sprintf($this->user->lang('listmembers_footcount'), count($confirm)),
			'FC_DELETE'				=> sprintf($this->user->lang('listmembers_footcount'), count($deletion)),
			'CSRF_MODE_TOKEN'		=> $this->CSRFGetToken('mode'),
		));

		$this->core->set_vars(array(
			'page_title'	=> $this->user->lang('uc_delete_manager'),
			'template_file'	=> 'admin/manage_tasks.html',
			'display'		=> true
		));
	}

}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ManageTasks', ManageTasks::__shortcuts());
registry::register('ManageTasks');
?>