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

if ( !class_exists( "mmtaskmanager" ) ) {
	class mmtaskmanager extends gen_class {

		private $tasks		= array();
		public $task_data	= array();
		public $types		= array();
		public $nec_types	= array();
		public $status		= array('necessary_tasks' => false, 'applicable_tasks' => false, 'not_applicable_tasks' => false);

		public function scan_tasks() {
			$task_dir = $this->root_path.'maintenance/includes/tasks/';
			$folder = scandir($task_dir);
			foreach($folder as $file) {
				$task_file_path = $task_dir.$file.'/'.$file.'.class.php';
				if(is_file($task_file_path)){
					$this->tasks[$file] = $task_file_path;
				}
			}
			//scan update-tasks from installed plugins
			$objQuery = $this->db->query("SELECT code FROM __plugins WHERE status = '1';");
			$plugs = array();
			if ($objQuery){
				while ( $row = $objQuery->fetchAssoc() ) {
					$plugs[] = $row['code'];
				}
			}
			
			$task_dir = $this->root_path.'plugins/';
			$folder = scandir($task_dir);
			foreach($folder as $file) {
				if(!in_array($file, $plugs)) continue;
				$this->task_scan($file, $task_dir);
			}
			//scan update-tasks for games
			$task_dir = $this->root_path.'games/'.$this->config->get('default_game').'/updates';
			if(is_dir($task_dir)) {
				$folder = scandir($task_dir);
				foreach($folder as $file) {
					$this->task_scan($file, $task_dir);
				}
			}
		}
				
		private function task_scan($file, $task_dir) {
			$path = (is_dir($task_dir.$file.'/includes/')) ? $task_dir.$file.'/includes/updates/' : $task_dir.$file.'/include/updates/';
			if(is_dir($path)) {
				$task_folder = scandir($path);
				foreach($task_folder as $task_file) {
					$task_file_path = $path.$task_file;
					$task_name = substr($task_file, 0, -10);
					if(is_file($task_file_path) AND substr($task_file, 0, 7) == 'update_' AND substr($task_file, -10) == '.class.php' AND !isset($this->tasks[$task_name])) {
						$this->tasks[$task_name] = $task_file_path;
					}
				}
			}
		}

		public function get_task_list($with_path=false){
			if(empty($this->tasks)){
				$this->scan_tasks();
			}
			return ($with_path) ? $this->tasks : array_keys($this->tasks);
		}

		public function get_task_count(){
			if(empty($this->tasks)){
				$this->scan_tasks();
			}
			return count($this->tasks);
		}
		
		public function get_task_hash(){
			if(empty($this->tasks)){
				$this->scan_tasks();
			}
			
			$arrTasks = $this->tasks;
			asort($arrTasks);
			
			$arrKeys = array_keys($arrTasks);
			$strHash = md5(serialize($arrKeys));
			return $strHash;
		}
		
		public function init_tasks() {
			require_once($this->root_path.'maintenance/includes/task.aclass.php');
			$this->get_task_list();
			foreach ($this->tasks as $task => $task_path){
				require_once($task_path);
				$task_obj = registry::register($task);
				$task_obj->init_lang();
				$this->types[$task_obj->type] = 0;
				$key = '';
				if($task_obj->a_is_applicable()) {
					$this->status['applicable_tasks'] = true;
					$key = 'applicable_tasks';
				}else{
					$this->status['not_applicable_tasks'] = true;
					$key = 'not_applicable_tasks';
				}
				if($task_obj->a_is_necessary()) {
					$this->status['necessary_tasks']	= true;
					if(!defined('MAINTENANCE_MODE')) return;
					$key = 'necessary_tasks';
					$this->nec_types[] = $task_obj->type;
				}
				$this->task_data[$key][$task] = array(
					'desc'			=> $task_obj->get_description(),
					'type'			=> $task_obj->type,
					'necessary'		=> $task_obj->a_is_necessary(),
					'applicable'	=> $task_obj->a_is_applicable(),
					'version'		=> $task_obj->version,
					'ext_version'	=> $task_obj->ext_version,
					'author'		=> $task_obj->author,
					'name'			=> $task_obj->name
				);
				unset($task_obj);
			}
		}
	}
}
?>