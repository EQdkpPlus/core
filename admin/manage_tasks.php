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

	public function __construct(){
		$handler = array(
			'confirmChars' 			=> array('process' => 'confirmChars', 'check' => 'a_members_man', 'csrf'=>true),
			'revokeChars'  			=> array('process' => 'revokeChars', 'check' => 'a_members_man', 'csrf'=>true),
			'deleteChars'  			=> array('process' => 'deleteChars', 'check' => 'a_members_man', 'csrf'=>true),
			'deleteConfirmChars'  	=> array('process' => 'deleteConfirmChars', 'check' => 'a_members_man', 'csrf'=>true),
			
			'deleteUser'			=> array('process' => 'deleteUser', 'check' => 'a_users_man', 'csrf'=>true),
			'activateUser'			=> array('process' => 'activateUser', 'check' => 'a_users_man', 'csrf'=>true),
		);
		$this->user->check_auths(array('a_users_man', 'a_members_man'), 'OR');
		parent::__construct(false, $handler);
		$this->process();


	}
	
	public function confirmChars(){
		$arrMemberIDs = $this->in->getArray('confirm_chars', 'int');
		if (count($arrMemberIDs)){
			foreach($arrMemberIDs as $char_id){
				$this->pdh->put('member', 'confirm', array($char_id));				
			}
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('success'), $this->user->lang('uc_delete_char'), 'green');
				
		}
		$this->display();	
	}
	
	public  function revokeChars(){
		$arrMemberIDs = $this->in->getArray('deleted_chars', 'int');
		if (count($arrMemberIDs)){
			foreach($arrMemberIDs as $char_id){
				$this->pdh->put('member', 'revoke', array($char_id));
			}
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('uc_delete_char'), $this->user->lang('success'),'green');
				
		}
		$this->display();	
	}
	
	public function deleteChars(){
		$arrMemberIDs = $this->in->getArray('deleted_chars', 'int');
		if (count($arrMemberIDs)){
			foreach($arrMemberIDs as $char_id){
				$this->pdh->put('member', 'delete_member', array($char_id));
			}
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('uc_delete_char'), $this->user->lang('success'),'green');
				
		}
		$this->display();	
	}
	
	public function deleteConfirmChars(){
		$arrMemberIDs = $this->in->getArray('confirm_chars', 'int');
		if (count($arrMemberIDs)){
			foreach($arrMemberIDs as $char_id){
				$this->pdh->put('member', 'delete_member', array($char_id));
			}
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('uc_delete_char'), $this->user->lang('success'), 'green');
				
		}
		$this->display();		
	}
	
	public function deleteUser(){
		$arrUserIDs = $this->in->getArray('selected_user', 'int');
		if (count($arrUserIDs)){
			foreach($arrUserIDs as $user_id){
				$this->pdh->put('user', 'delete_user', array($user_id, true));
			}
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('delete_user'), $this->user->lang('success'), 'green');
			
		}
		$this->display();
	}
	
	public function activateUser(){
		$arrUserIDs = $this->in->getArray('selected_user', 'int');
		if (count($arrUserIDs)){
			$this->pdh->put('user', 'activate', array($arrUserIDs));
			$this->pdh->process_hook_queue();
			$this->core->message($this->user->lang('activate_user'), $this->user->lang('success'), 'green');		
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
				$userId = $this->pdh->get('member', 'user', array($member));
				$this->tpl->assign_block_vars('confirm_row', array(
					'ID'			=> $member,
					'NAME'			=> $this->pdh->get('member', 'name_decorated', array($member)),
					'LEVEL'			=> $this->pdh->get('member', 'level', array($member)),
					'USER'			=> ($userId) ? $this->pdh->get('user', 'name', array($userId)) : '',
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
					'NAME'			=> $this->pdh->get('member', 'name_decorated', array($member)),
					'LEVEL'			=> $this->pdh->get('member', 'level', array($member)),
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
		
		$this->jquery->Dialog('ConfirmChars', '', array('url' =>'', 'message'=>$this->user->lang('uc_confirm_msg_all'), 'custom_js' => '$("#confirmChars").click();'), 'confirm');
		$this->jquery->Dialog('DeleteConfirmChars', '', array('url'=>'', 'message'=>$this->user->lang('uc_del_msg_all'), 'custom_js' => '$("#deleteConfirmChars").click();'), 'confirm');
		
		$this->jquery->Dialog('DeleteChars', '', array('url'=>'', 'message'=>$this->user->lang('uc_del_msg_all'), 'custom_js' => '$("#deleteChars").click();'), 'confirm');
		$this->jquery->Dialog('RevokeChars', '', array('url'=>'', 'message'=>$this->user->lang('uc_revoke_char_confirm'), 'custom_js' => '$("#revokeChars").click();'), 'confirm');
		
		$this->jquery->Dialog('ActivateAllUsers', '', array('url'=>'', 'message'=>$this->user->lang('activate_user_warning'), 'custom_js' => '$("#activateUser").click();'), 'confirm');
		$this->jquery->Dialog('DeleteUser', '', array('url'=>'', 'message'=>$this->user->lang('confirm_delete_user'), 'custom_js' => '$("#deleteUser").click();'), 'confirm');

		$this->jquery->selectall_checkbox("selall_user", "selected_user[]");
		$this->jquery->selectall_checkbox("selall_deleted_chars", "deleted_chars[]");
		$this->jquery->selectall_checkbox("selall_confirm_chars", "confirm_chars[]");
		
		$this->jquery->Dialog('EditChar', $this->user->lang('uc_edit_char'), array('withid'=>'editid', 'url'=>"../addcharacter.php".$this->SID."&adminmode=1&editid='+editid+'", 'width'=>'640', 'height'=>'520', 'onclose'=> 'manage_tasks.php'.$this->SID));
				
		$arrMenuItems = array(
			0 => array(
					'name'	=> $this->user->lang('activate_user'),
					'type'	=> 'button', //link, button, javascript
					'icon'	=> 'fa-check-square-o',
					'perm'	=> true,
					'link'	=> '#activateUserTrigger',
			),
			1 => array(
					'name'	=> $this->user->lang('delete'),
					'type'	=> 'button', //link, button, javascript
					'icon'	=> 'fa-trash-o',
					'perm'	=> true,
					'link'	=> '#deleteUserTrigger',
			),	
		);
		
		$arrDeleteCharsMenuItems = array(
			0 => array(
					'name'	=> $this->user->lang('delete'),
					'type'	=> 'button', //link, button, javascript
					'icon'	=> 'fa-trash-o',
					'perm'	=> true,
					'link'	=> '#deleteCharsTrigger',
			),
			1 => array(
					'name'	=> $this->user->lang('uc_rewoke_char'),
					'type'	=> 'button', //link, button, javascript
					'icon'	=> 'fa-refresh',
					'perm'	=> true,
					'link'	=> '#revokeCharsTrigger',
			),
		);
		
		$arrConfirmCharsMenuItems = array(
			
			0 => array(
					'name'	=> $this->user->lang('uc_confirm_char'),
					'type'	=> 'button', //link, button, javascript
					'icon'	=> 'fa-check-square-o',
					'perm'	=> true,
					'link'	=> '#confirmCharsTrigger',
			),
			1 => array(
					'name'	=> $this->user->lang('delete'),
					'type'	=> 'button', //link, button, javascript
					'icon'	=> 'fa-trash-o',
					'perm'	=> true,
					'link'	=> '#deleteConfirmCharsTrigger',
			),
		);
		
		$this->tpl->assign_vars(array(
			'S_CONFIRM'				=> (count($confirm) > 0) ? true : false,
			'S_DELETE'				=> (count($deletion) > 0) ? true : false,
			'S_INACTIVE'			=> (count($inactive) > 0) ? true : false,
			'S_NOTHING'				=> $nothing,
			'BUTTON_MENU_INACTIVE_USER'	=> $this->jquery->ButtonDropDownMenu('inaktive_user_menu', $arrMenuItems, array("input[name=\"selected_user[]\"]"), '', $this->user->lang('selected_user').'...', ''),
			'BUTTON_MENU_DELETE_CHARS'	=> $this->jquery->ButtonDropDownMenu('delete_char_menu', $arrDeleteCharsMenuItems, array("input[name=\"deleted_chars[]\"]"), '', $this->user->lang('selected_chars').'...', ''),
			'BUTTON_MENU_CONFIRM_CHARS'	=> $this->jquery->ButtonDropDownMenu('confirm_char_menu', $arrConfirmCharsMenuItems, array("input[name=\"confirm_chars[]\"]"), '', $this->user->lang('selected_chars').'...', ''),
		));

		$this->core->set_vars(array(
			'page_title'	=> $this->user->lang('uc_delete_manager'),
			'template_file'	=> 'admin/manage_tasks.html',
			'display'		=> true
		));
	}

}
registry::register('ManageTasks');
?>