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

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_23290 extends sql_update_task {
	public static $shortcuts = array('mmt'	=> 'mmtaskmanager');
	
	public $author			= 'GodMod';
	public $version			= '2.3.29.0'; //new plus-version
	public $ext_version		= '2.3.29'; //new plus-version
	public $name			= '2.3.29 Update';
	
	public function __construct(){
		parent::__construct();
		
		$this->langs = array(
				'english' => array(
						'update_23290'		=> 'EQdkp Plus 2.3.29 Update',
						'update_function'	=> 'Handle Maintenance-Updates',
				),
				'german' => array(
						'update_23290'		=> 'EQdkp Plus 2.3.29 Update',
						'update_function'	=> 'Verwalte Wartungsupdates',
				),
		);
		
		$this->sqls = array();
	}
	
	public function update_function(){
		$this->mmt->init_tasks();
		$task_data = $this->mmt->task_data;
		
		$arrNecessaryTasks = $task_data['necessary_tasks'];
		
		foreach($arrNecessaryTasks as $strTask => $arrTaskdata){
			if($arrTaskdata['type'] == 'update'){
				$currentVersion = $this->config->get('plus_version');
				if(version_compare($currentVersion, $arrTaskdata['version']) >= 0){
					$this->config->set($strTask, $this->time->time, 'maintenance_task');
				}
			}
			
			if($arrTaskdata['type'] == 'plugin_update'){
				$currentVersion = $arrTaskdata['current_running_version'];
				if(version_compare($currentVersion, $arrTaskdata['version']) >= 0){
					$this->config->set($strTask, $this->time->time, 'maintenance_task');
				}
			}
			
			if($arrTaskdata['type'] == 'game_update'){
				$currentVersion = $this->config->get('game_version');
				if(version_compare($currentVersion, $arrTaskdata['version']) >= 0){
					$this->config->set($strTask, $this->time->time, 'maintenance_task');
				}
			}
			
		}
		
		return true;
		
	}
	
	
}

?>