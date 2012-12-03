<?php
/******************************
 * EQdkp
 * Copyright 2009
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * common_lite.php
 * begin: 2009
 *
 * $Id$
 *
 ******************************/

RunGlobalsFix();

if ( !defined('EQDKP_INC') )
{
    die('Do not access this file directly.');
}


if ( !class_exists( "MMTaskManager" ) ) {
  class MMTaskManager{
    private $tasks = array();

    public function scan_tasks(){
    global $eqdkp_root_path, $core;
      $task_dir = $eqdkp_root_path.'maintenance/includes/tasks/';
      $folder = scandir($task_dir);
      foreach($folder as $file) {
      	$task_file_path = $task_dir.$file.'/'.$file.'.class.php';
        if(is_file($task_file_path)){
          $this->tasks[$file] = $task_file_path;
        }
      }
      //scan update-tasks from plugins
      $task_dir = $eqdkp_root_path.'plugins/';
      $folder = scandir($task_dir);
      foreach($folder as $file) {
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
      //scan update-tasks for games
      $task_dir = $eqdkp_root_path.'games/'.$core->config['default_game'].'/updates';
			if(is_dir($task_dir)) {
				$folder = scandir($task_dir);
				foreach($folder as $file) {
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
  }//class
}//check
?>