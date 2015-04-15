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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('MAINTENANCE_MODE',1);

$eqdkp_root_path = './../';
$lite = true;
define('DEBUG', 3);
require_once($eqdkp_root_path.'common.php');
require_once($eqdkp_root_path.'maintenance/includes/task.aclass.php');
class task_manager_display extends gen_class {
	public static $shortcuts = array(
		'core'	=> array('core', array('maintenance', 'task_manager.html', 'maintenance_message.html')),
		'mmt'	=> 'mmtaskmanager',
	);

	public function __construct() {
		//Check the auth
		$this->core->check_auth();
		$this->core->page_header();

		if ($this->in->get('activate') != ""){
			$this->config->set('pk_maintenance_mode', 1);
			$this->config->set('pk_maintenance_message', $this->in->get('maintenance_message', '', 'raw'));
			$this->tpl->assign_vars(array(
				'S_MMODE_ACTIVE'	=> true,
			));
		}
		if ($this->in->get('leave') != ""){
			redirect('admin/index.php', false, false, false);
		}
		$this->mmt->init_tasks();
		$task_data = $this->mmt->task_data;
		$types = $this->mmt->types;
		$nec_types = $this->mmt->nec_types;
		$necessary = $this->mmt->status;

		// Check if Safe Mode
		$pfh_error = false;
		if(isset($this->pfh->safe_mode)) {
			if($this->pfh->testWrite()){
				$this->tpl->assign_block_vars('pfh_errors', array(
					'PFH_ERROR' => $this->user->lang('pfh_safemode_error'),
				));;
				$pfh_error		= true;
			}
		}
		// check if Data Folder is writable
		$errors = $this->pfh->get_errors();
		if(is_array($errors)){
			foreach($errors as $error){
				$this->tpl->assign_block_vars('pfh_errors', array(
					'PFH_ERROR' => $this->user->lang($error),
				));
				$pfh_error		= true;
			}
		}

		//disable if no necessary tasks or errors
		if($this->in->get('disable') == true || $this->in->get('start_tour') != "" || $this->in->get('no_tour') != "" || $this->in->get('guild_import') != ""){
			if(!$necessary['necessary_tasks'] && !$pfh_error){
				$this->config->set('pk_maintenance_mode', 0);
				if($this->in->get('start_tour') == "true") {
					$redirect_url	= 'admin/'.$this->SID.'&tour=start';
				}elseif($this->in->get('no_tour' == "true")){
					$redirect_url	= 'admin/settings.php'.$this->SID;
				}elseif($this->in->get('guild_import') == "true"){
					$redirect_url	= 'admin/manage_settings.php'.$this->SID.'#fragment-game';
				}else{
					$redirect_url	= 'admin/index.php'.$this->SID;
				}

				redirect($redirect_url, false, false, false);
			} else {
				$this->tpl->assign_vars(array(
					'NO_LEAVE'			=> 1,
					'L_NO_LEAVE'		=> $this->user->lang('no_leave'),
					'L_NO_LEAVE_ACCEPT'	=> $this->user->lang('no_leave_accept'))
				);
			}
		}

		//task-sort function
		function sort_tasks($a, $b) {
			$ret = strcmp($a['type'], $b['type']);
			if($ret == 0) {
				return compareVersion($a['version'], $b['version']);
			}
			return $ret;
		}
		
		//output tasks
		if($this->in->get('type', 'home') == 'home'){
			$update_all = false;
			if(isset($task_data['necessary_tasks']) && count($task_data['necessary_tasks']) > 0){
				$this->tpl->assign_vars(array(
					'S_NEC_TASKS'		=> true,
					'L_NEC_TASK'		=> $this->user->lang('nec_tasks'))
				);
				foreach($types as $type => $unused) {
					if(in_array($type, $nec_types)) {
						$this->tpl->assign_block_vars('tasks_list', array(
							'L_TASKS'	=> $this->user->lang($type))
						);
						//sort task_data 1st by type, 2nd by version
						uasort($task_data['necessary_tasks'], 'sort_tasks');
						foreach($task_data['necessary_tasks'] as $task => $data) {
							if($data['type'] == $type) {
								$this->tpl->assign_block_vars('tasks_list.spec_task_list', array(
									'STATUS'		=> $this->core->StatusIcon('false'),
									'NAME'			=> $data['name'],
									'LINK'			=> "./task.php".$this->SID."&amp;task=".$task,
									'DESCRIPTION'	=> $data['desc'],
									'AUTHOR'		=> $data['author'],
									'VERSION'		=> ((isset($data['ext_version']) && $data['ext_version']) ? $data['ext_version'] : $data['version'])
								));
								$update_all = $task;
							}
						}
					}
				}
			}else{
				$this->tpl->assign_vars(array(
					'L_NO_NEC_TASK'		=> $this->user->lang('no_nec_tasks'),
					'S_HIDE_TASK_TABLE'	=> true,
					'S_NO_NEC_TASKS'	=> true)
				);
			}
		}else{
			if(isset($task_data['necessary_tasks']) && count($task_data['necessary_tasks']) > 0){
				$this->tpl->assign_vars(array(
					'S_NEC_TASKS'		=> true,
					'L_NEC_TASK'		=> $this->user->lang('nec_tasks_available'))
				);
			}

			foreach($necessary as $key => $nec){
				if($nec){
					$this->tpl->assign_block_vars('tasks_list', array(
						'L_TASKS'				=> $this->user->lang($key),
						'S_APLLICABLE_TASKS'	=> ($key == 'applicable_tasks') ? true : false,
					));

					//sort task_data 1st by type, 2nd by version
					uasort($task_data[$key], 'sort_tasks');
					foreach($task_data[$key] as $task => $data) {
						if($this->in->get('type', 'home') != $data['type'] AND $this->in->get('type', 'home') != 'home') continue;
						$this->tpl->assign_block_vars('tasks_list.spec_task_list', array(
							'NAME'				=> $data['name'],
							'STATUS'			=> $this->core->StatusIcon(($key == 'necessary_tasks') ? 'false' : 'ok'),
							'LINK'				=> "./task.php".$this->SID."&amp;task=".$task,
							'DESCRIPTION'		=> $data['desc'],
							'AUTHOR'			=> $data['author'],
							'VERSION'			=> ((isset($data['ext_version']) && $data['ext_version']) ? $data['ext_version'] : $data['version']),
							'S_NOT_APPLICABLE'	=> ($key == 'not_applicable_tasks') ? true : false)
						);
					}
				}
			}
		}

		//output type-tabs
		ksort($types);
		$this->tpl->assign_block_vars('task_types', array(
			'TYPE'			=> 'home',
			'ACTIVE'		=> ($this->in->get('type', 'home') == 'home') ? 'class="active"' : '',
			'L_TYPE'		=> $this->user->lang('home'),
		));
		foreach($types as $type => $unused) {
			$this->tpl->assign_block_vars('task_types', array(
				'TYPE'		=> $type,
				'ACTIVE'	=> ($this->in->get('type', 'home') == $type) ? 'class="active"' : '',
				'L_TYPE'	=> $this->user->lang($type),
			));
		}

		$this->tpl->assign_vars(array(
			'L_NAME'			=> $this->user->lang('name'),
			'L_DESCRIPTION'		=> $this->user->lang('description'),
			'L_AUTHOR'			=> $this->user->lang('author'),
			'L_VERSION'			=> $this->user->lang('version'),
			'L_APPLICABLE_INFO'	=> $this->user->lang('applicable_info'),
			'L_pfh_ERROR'		=> $this->user->lang('mmode_pfh_error'),
			'UPDATE_ALL'		=> (isset($update_all)) ? $update_all : false,
			'L_UPDATE_ALL'		=> $this->user->lang('start_update'),
			'S_PFH_ERROR'		=> $pfh_error,
			'S_HOME'			=> ($this->in->get('type', 'home') == 'home') ? true : false,
			'L_CLICK_ME'		=> $this->user->lang('click_me'),
		));
		$this->core->page_tail();
	}
}
registry::register('task_manager_display');
?>