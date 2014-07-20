<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2014-02-05 21:00:21 +0100 (Mi, 05 Feb 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy_leon $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13999 $
 * 
 * $Id: gravatar.class.php 13999 2014-02-05 20:00:21Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
} 

class admin_tasks extends gen_class {

	public function getTasks(){
		$arrTasks = array(
				//Confirm new Chars
				'confirmChars' => array(
						'name'			=> 'uc_confirm_list',
						'icon'			=> 'fa fa-check',
						'notify_func'	=> array($this, 'ntfyConfirmChars'),
						'content_func'	=> array($this, 'contentConfirmChars'),
						'action_func'	=> array($this, 'actionHandleChars'),
						'actions'		=> array(
								'confirm' => array('icon' => 'fa fa-check', 'title' => 'uc_confirm_char', 'permissions' => array('a_members_man')),
								'delete'  => array('icon' => 'fa-trash-o', 'title' => 'delete_member', 'permissions' => array('a_members_man')),
						),
				),
					
		
				//Hard Delete soft-deleted Chars
				'deleteChars' => array(
						'name'			=> 'uc_delete_list',
						'icon'			=> 'fa fa-trash-o',
						'notify_func'	=> array($this, 'ntfyDeleteChars'),
						'content_func'	=> array($this, 'contentDeleteChars'),
						'action_func'	=> array($this, 'actionHandleChars'),
						'actions'		=> array(
								'restore' => array('icon' => 'fa fa-refresh', 'title' => 'uc_rewoke_char', 'permissions' => array('a_members_man')),
								'delete'  => array('icon' => 'fa-trash-o', 'title' => 'delete_member', 'permissions' => array('a_members_man')),
						),
				),
		
				//InactiveUsers
				'inactiveUsers' => array(
						'name'			=> 'activate_list',
						'icon'			=> 'fa fa-group',
						'notify_func'	=> array($this, 'ntfyInactiveUsers'),
						'content_func'	=> array($this, 'contentInactiveUsers'),
						'action_func'	=> array($this, 'actionInactiveUsers'),
						'actions'		=> array(
								'activate' => array('icon' => 'fa fa-check', 'title' => 'activate_user', 'permissions' => array('a_users_man')),
								'delete'  => array('icon' => 'fa-trash-o', 'title' => 'delete_user', 'permissions' => array('a_users_man')),
						),
				),
		);
		
		//Hook System
		if($this->hooks->isRegistered('admin_tasks')){
			$arrHooks = $this->hooks->process('admin_tasks');
		
			if (count($arrHooks) > 0){
				foreach($arrHooks as $arrHook){
					if(is_array($arrHook)) $arrTasks = array_merge($arrTasks, $arrHook);
				}
			}
		}
		
		
		return $arrTasks;
	}
	


	public function createNotifications(){
		$arrTasks = $this->getTasks();
	
		foreach($arrTasks as $taskID => $arrTask){
			if (isset($arrTask['content_func'])){
	
				//Check Permissions
				$blnPermission = false;
				foreach($arrTask['actions'] as $actionID => $arrActions){
					if (count($arrActions['permissions'])){
						if (!$blnPermission && $this->user->check_auths($arrActions['permissions'], 'OR', false)) $blnPermission = true;
						break;
					}
				}
	
				if (!$blnPermission) continue;
	
				$arrContent= call_user_func($arrTask['notify_func']);
				if (is_array($arrContent) && count($arrContent)){
					foreach($arrContent as $val){
						$this->ntfy->add($val['type'], $val['category'],$val['msg'], $this->server_path.'admin/manage_tasks.php'.$this->SID.'#t_'.md5($taskID), $val['count']);
					}
				}
			}
		}
	}
	
	
	
	
	public function contentConfirmChars(){
		$arrContent = array();
		
		//Confirm members
		$confirm = $this->pdh->get('member', 'confirm_required');
		if (count($confirm) > 0){
			$nothing = false;
			foreach ($confirm as $member){
				$userId = $this->pdh->get('member', 'user', array($member));
				
				$arrContent[] = array(
						'id' => $member,
						'name' => $this->pdh->get('member', 'name_decorated', array($member)),
						'level' => $this->pdh->get('member', 'level', array($member)),
						'user' => ($userId) ? $this->pdh->get('user', 'name', array($userId)) : '',
				);
			}
		}
		
		return $arrContent;
	}
	
	public function ntfyConfirmChars(){
		$deletion = $this->pdh->get('member', 'confirm_required');
		if (count($deletion) > 0){
			return array(array(
					'type' => 'yellow',
					'count'=> count($deletion),
					'msg'  => sprintf($this->user->lang('notification_char_confirm_required'), count($deletion)),
					'category' => $this->user->lang('manage_members'),
			));
		}
	
		return array();
	}
	
	
	public function contentInactiveUsers(){
		$arrContent = array();
		
		//Inactive Users
		$inactive = $this->pdh->get('user', 'inactive');
		if (count($inactive) > 0){
			$nothing = false;
			foreach ($inactive as $member){
				$arrContent[] = array(
					'id' => $member,
					'name' => $this->pdh->get('user', 'name', array($member)),
					'email' => ($this->pdh->get('user', 'email', array($member))) ? '<a href="mailto:'.$this->pdh->get('user', 'email', array($member)).'">'.$this->pdh->get('user', 'email', array($member)).'</a>' : '',
					'registered_at' => $this->time->user_date($this->pdh->get('user', 'regdate', array($member)), true), 	
				);
			}
		}
		
		return $arrContent;
	}
	
	public function ntfyInactiveUsers(){
		if ($this->config->get('account_activation') != 2) return array();
		
		$arrNotifications = array(
			
		);
		
		$inactive = $this->pdh->get('user', 'inactive');
		if (count($inactive) > 0){
			return array(array(
				'type' => 'yellow',
				'count'=> count($inactive),
				'msg'  => sprintf($this->user->lang('notification_user_enable'), count($inactive)),
				'category' => $this->user->lang('manage_users'),
			));
		}
		
		return array();
	}
	
	
	public function contentDeleteChars(){
		$arrContent = array();
		
		$deletion = $this->pdh->get('member', 'delete_requested');
		if (count($deletion) > 0){
			$nothing = false;
			foreach ($deletion as $member){
				$arrContent[] = array(
					'id' => $member,
					'name' => $this->pdh->get('member', 'name_decorated', array($member)),
					'level' => $this->pdh->get('member', 'level', array($member)),
				);
			}
		}
		
		return $arrContent;
	}
	
	public function ntfyDeleteChars(){
		$deletion = $this->pdh->get('member', 'delete_requested');
		if (count($deletion) > 0){
			return array(array(
					'type' => 'yellow',
					'count'=> count($deletion),
					'msg'  => sprintf($this->user->lang('notification_char_delete_requested'), count($deletion)),
					'category' => $this->user->lang('manage_members'),
			));
		}
	
		return array();
	}

	
	public function actionHandleChars($strAction, $arrIDs, $strTaskID){
		if ($strAction == 'confirm'){
			if (count($arrIDs)){
				foreach($arrIDs as $char_id){
					$this->pdh->put('member', 'confirm', array((int)$char_id));
				}
				$this->pdh->process_hook_queue();
				$this->core->message($this->user->lang('success'), $this->user->lang('uc_confirm_char'), 'green');
			}
		}
		
		if ($strAction == 'restore'){
			if (count($arrIDs)){
				foreach($arrIDs as $char_id){
					$this->pdh->put('member', 'revoke', array((int)$char_id));
				}
				$this->pdh->process_hook_queue();
				$this->core->message($this->user->lang('uc_revoke_char'), $this->user->lang('success'),'green');
			
			}
		}
		
		if($strAction == 'delete'){
			if (count($arrIDs)){
				foreach($arrIDs as $char_id){
					$this->pdh->put('member', 'delete_member', array((int)$char_id));
				}
				$this->pdh->process_hook_queue();
				$this->core->message($this->user->lang('uc_delete_char'), $this->user->lang('success'),'green');
			
			}
		}
	}
	
	public function actionInactiveUsers($strAction, $arrIDs, $strTaskID){
		if($strAction == 'delete'){
			if (count($arrIDs)){
				foreach($arrIDs as $user_id){
					$this->pdh->put('user', 'delete_user', array((int)$user_id, true));
				}
				$this->pdh->process_hook_queue();
				$this->core->message($this->user->lang('delete_user'), $this->user->lang('success'), 'green');
					
			}
		}
		
		if($strAction == 'activate'){
			if (count($arrIDs)){
				$this->pdh->put('user', 'activate', array($arrIDs));
				$this->pdh->process_hook_queue();
				$this->core->message($this->user->lang('activate_user'), $this->user->lang('success'), 'green');
			}
		}
	}
}

?>