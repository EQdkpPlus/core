<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
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
						$this->ntfy->add_persistent($val['type'], $val['msg'], $this->server_path.'admin/manage_tasks.php'.$this->SID.'#t_'.md5($taskID), $val['prio'], $val['icon']);
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
					'id'	=> $member,
					'name'	=> $this->pdh->get('member', 'name_decorated', array($member)),
					'level'	=> $this->pdh->get('member', 'level', array($member)),
					'user'	=> ($userId) ? $this->pdh->get('user', 'name', array($userId)) : '',
				);
			}
		}
		
		return $arrContent;
	}
	
	public function ntfyConfirmChars(){
		$deletion = $this->pdh->get('member', 'confirm_required');
		if (count($deletion) > 0){
			return array(array(
				'type'		=> 'eqdkp_char_confirm_required',
				'prio'		=> 1,
				'count'		=> count($deletion),
				'msg'		=> sprintf($this->user->lang('notification_char_confirm_required'), count($deletion)),
				'icon'		=> 'fa-user',
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
					'id'			=> $member,
					'name'			=> $this->pdh->get('user', 'name', array($member)),
					'email'			=> ($this->pdh->get('user', 'email', array($member))) ? '<a href="mailto:'.$this->pdh->get('user', 'email', array($member)).'">'.$this->pdh->get('user', 'email', array($member)).'</a>' : '',
					'registered_at'	=> $this->time->user_date($this->pdh->get('user', 'regdate', array($member)), true), 	
				);
			}
		}
		return $arrContent;
	}

	public function ntfyInactiveUsers(){
		if ($this->config->get('account_activation') != 2) return array();

		$inactive = $this->pdh->get('user', 'inactive');
		if (count($inactive) > 0){
			return array(array(
				'type'		=> 'eqdkp_user_enable_requested',
				'count'		=> count($inactive),
				'msg'		=> sprintf($this->user->lang('notification_user_enable'), count($inactive)),
				'icon'		=> 'fa-users',
				'prio'		=> 1,
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
					'id'		=> $member,
					'name'		=> $this->pdh->get('member', 'name_decorated', array($member)),
					'level'		=> $this->pdh->get('member', 'level', array($member)),
				);
			}
		}
		return $arrContent;
	}
	
	public function ntfyDeleteChars(){
		$deletion = $this->pdh->get('member', 'delete_requested');
		if (count($deletion) > 0){
			return array(array(
					'type'		=> 'eqdkp_char_delete_requested',
					'count'		=> count($deletion),
					'msg'		=> sprintf($this->user->lang('notification_char_delete_requested'), count($deletion)),
					'icon'		=> 'fa-trash',
					'prio'		=> 1,
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
				foreach($arrIDs as $user_id){
					$this->pdh->put('user', 'activate', array($user_id));
				}
				$this->pdh->process_hook_queue();
				$this->core->message($this->user->lang('activate_user'), $this->user->lang('success'), 'green');
			}
		}
	}
}

?>