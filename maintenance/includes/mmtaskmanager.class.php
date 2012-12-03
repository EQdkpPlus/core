<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "mmtaskmanager" ) ) {
	class mmtaskmanager extends gen_class {
		public static $shortcuts = array('db', 'config');

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
			$plug_res = $this->db->query("SELECT code FROM __plugins WHERE status = '1';");
			$plugs = array();
			while ( $row = $this->db->fetch_record($plug_res) ) {
				$plugs[] = $row['code'];
			}
			$this->db->free_result($plug_res);
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
					'author'		=> $task_obj->author,
					'name'			=> $task_obj->name
				);
				unset($task_obj);
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_mmtaskmanager', mmtaskmanager::$shortcuts);
?>