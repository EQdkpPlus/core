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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class ManageTasks extends page_generic {

	public function __construct(){
		$handler = array(
			'mode' 					=> array('process' => 'handleAction', 'csrf'=>true),
		);
		
		$this->user->check_auth('a_');
		parent::__construct(false, $handler);
		$this->process();
	}
	
	public function handleAction(){
		$strAction = $this->in->get('mode');
		
		$strArrayName = $this->in->get('cbname');
		$arrIDs = $this->in->getArray($strArrayName, 'string');
		$strTask = $this->in->get('task');
		
		$arrTasks = $this->admin_tasks->getTasks();

		if (isset($arrTasks[$strTask])) {
			$arrTask = $arrTasks[$strTask];
			if (isset($arrTask['action_func'])){
				if (isset($arrTask['actions'][$strAction])){
					//Check Permission
					$action = $arrTask['actions'][$strAction];
					if ($this->user->check_auths($action['permissions'], 'OR', false)){
						call_user_func($arrTask['action_func'], $strAction, $arrIDs, $strTask);
					}
						
				}
			}
		}
		
		//flush PDH Queue
		$this->pdh->process_hook_queue();
	}

	
	public function display(){
		
		$arrTasks = $this->admin_tasks->getTasks();

		foreach($arrTasks as $taskID => $arrTask){
			if (isset($arrTask['content_func'])){
				$arrContent= call_user_func($arrTask['content_func']);
				if (is_array($arrContent) && count($arrContent)){
					
					//Actions
					$blnPermission = false;
					$arrMenuItems = array();
					foreach($arrTask['actions'] as $actionID => $arrActions){
						if (count($arrActions['permissions'])){
							if (!$blnPermission && $this->user->check_auths($arrActions['permissions'], 'OR', false)) $blnPermission = true;
							
							if ($this->user->check_auths($arrActions['permissions'], 'OR', false)){
								$arrMenuItems[] = array(
										'name'	=> $this->user->lang($arrActions['title']),
										'type'	=> 'button', //link, button, javascript
										'icon'	=> $arrActions['icon'],
										'perm'	=> true,
										'link'	=> '#t_'.md5($taskID.'.'.$actionID).'Trigger',
										'__action' => $actionID,
								);
							}
							
							
						}
					}
					
					if ($blnPermission){
					
						$this->tpl->assign_block_vars('task_row', array(
							'HEADLINE'	=> $this->user->lang($arrTask['name']),
							'ID'		=> 't_'.md5($taskID),
							'NAME'		=> $taskID,
							'MENU'		=> $this->jquery->ButtonDropDownMenu('menu_t_'.md5($taskID), $arrMenuItems, array("input[name=\"cb_t_".md5($taskID)."[]\"]"), '', $this->user->lang('selected_elements').'...', ''),
						));
						
						foreach($arrMenuItems as $val){
							$this->tpl->assign_block_vars('task_row.button_row', array(
								'ID' 	=> substr($val['link'],1),
								'VALUE' => $val['__action'],
							));
						}
						
						$this->jquery->selectall_checkbox('t_'.md5($taskID), 'cb_t_'.md5($taskID).'[]');
						
						//Output
						//TH
						foreach($arrContent[0] as $key => $val){
							if ($key == 'id') continue;
							
							$this->tpl->assign_block_vars('task_row.th_row', array(
								'CONTENT' => $this->user->lang($key),
							));
						}
						
						foreach($arrContent as $val){
							$this->tpl->assign_block_vars('task_row.content_row', array(
								'ID'	=> $val['id'],
							));
							
							foreach($val as $key => $val2){
								if ($key == 'id') continue;
								
								$this->tpl->assign_block_vars('task_row.content_row.td_row', array(
									'CONTENT' => $val2,
								));
							}
						}
					
					}
				}
			}
		}
			
		
		$this->core->set_vars(array(
			'page_title'	=> $this->user->lang('uc_delete_manager'),
			'template_file'	=> 'admin/manage_tasks.html',
			'display'		=> true
		));
	}

}
registry::register('ManageTasks');
?>