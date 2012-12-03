<?php
 /*
 * Project:     EQdkp Plus Patcher
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://www.eqdkp-plus.com
 * @package     plus patcher
 * @version     $Rev$
 *
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

if ( !class_exists( "task" ) ) {
  require_once($eqdkp_root_path . 'maintenance/includes/task.aclass.php');
}

class sql_update extends task {
	public $form_method = "GET";
	public $author = "sql_update";
	public $version = "0.0.0";

	protected $form = '';

	private $needed_updates = array();
	private $calling_update = array();
	private $row_class = 2;

	public function __construct($calling_task, $all=false) {
		global $core;
		$this->version = $core->config['plus_version'];
		$this->use_steps = true;
		$this->parse_only = true;
		$this->calling_update['code'] = $calling_task[0];
		$this->calling_update['desc'] = $calling_task[1];
		$this->calling_update['version'] = $calling_task[2];
		$this->plugin_path = $calling_task[3];
		$this->calling_update['name'] = $calling_task[4];
		$this->get_needed_updates($all);
		uksort($this->needed_updates, 'version_compare');
		foreach($this->needed_updates as $upd) {
			$this->step_order[] = $upd['code'];
		}
	}

	public function construct() {
		$this->form = $this->step_data['form'];
	}

	public function destruct() {
		$this->step_data['form'] = $this->form;
	}

	public function first_step() {
		global $user, $in;
		$this->current_step = 'first';
		$output = '';
		if(count($this->needed_updates) > 1) {
			$output = '<b>'.$user->lang['following_updates_necessary']."</b><br /><ul>";
			foreach($this->needed_updates as $version => $upd) {
				$output .= "<li>".$version." - ".$upd['desc']."</li>";
			}
			$output .= "</ul><input type='submit' name='start_sql_update' value='".$user->lang['start_update']."' class=\"mainoption\"/><br /><br />";
		}
		$output .= '<b>'.$user->lang['only_this_update']."</b></br><ul><li>".$this->calling_update['version']." - ".$this->calling_update['desc']."</li></ul><input type='submit' name='single_update' value='".$this->calling_update['name']."' class=\"mainoption\"/><input type='hidden' name='single_update_code' value='".$this->calling_update['code']."' />";
		$output .= ($in->get('update_all', 0)) ? "<input type='hidden' name='update_all' value='1' />" : "";

		return $output;
	}

	public function parse_first_step() {
		global $in;
		if($in->get('single_update', '')) {
			$this->step_order = array('first', $in->get('single_update_code'));
		} elseif(!$in->get('start_sql_update', '')) {
			return false;
		}
		$this->steps = array_keys($this->step_order);
		asort($this->steps);
		return true;
	}

	public function get_step($step) {
		//not necessary, since we have no output
	}

	public function parse_step($step) {
		global $eqdkp_root_path;
		include_once($this->task_list[$step]);
		$current = new $step();
		$current->init_lang();
		$this->plugin_path = $current->plugin_path;
		$this->do_sql($current->sqls, $current->version, $current->lang, $current->name);
		if(method_exists($current, 'update_function')) {
			$func = $current->update_function();
			$this->form .= '<tr class="row'.$this->row_class.'"><td><img src="'.$eqdkp_root_path.'images/glyphs/status_'.(($func) ? 'green' : 'red').'.gif"> '.$current->lang['update_function'].'</td></tr>';
		}
		unset($current);
		return true;
	}

	public function step_end() {
		global $eqdkp_root_path, $user;
		return $this->form."<tr><td align='center'><a href='".$eqdkp_root_path."maintenance/task_manager.php'>".$user->lang['task_manager']."</a></td></tr></table>";
	}

	protected function do_sql($sqls, $version, $lang, $task_name) {
		global $db, $eqdkp_root_path, $core, $user;
      //run all queries if this task is necessary
  		$this->form .= '<table width="80%" align="center">';
		$this->form .= '<tr><th class="th_sub">'.sprintf($user->lang['executed_tasks'], $task_name).'</th></tr>';
  		foreach($sqls as $key => $sql) {
  			$this->form .= '<tr class="'.$core->switch_row_class().'"><td>';
  			if($db->query($sql)) {
  				$this->form .= '<img src="'.$eqdkp_root_path.'images/glyphs/status_green.gif"> ';
  			} else {
  				$this->form .= '<img src="'.$eqdkp_root_path.'images/glyphs/status_red.gif"> ';
  			}
  			$this->form .=  $lang[$key].'</td></tr>';
				
  		}
  		if($this->plugin_path) {
  			$db->query("UPDATE __plugins SET plugin_version = '".$version."' WHERE plugin_path = '".$this->plugin_path."';");
  		} else {
  			$core->config_set('plus_version', $version);
		}
	}

	private function get_needed_updates($all=false) {
		global $eqdkp_root_path;
		include_once($eqdkp_root_path.'maintenance/includes/mmtaskmanager.class.php');
		$mmt = new MMTaskManager();
		$this->task_list = $mmt->get_task_list(true);
		foreach($this->task_list as $task => $file) {
            if(strpos($task, (($all) ? 'update_' : 'update_'.$this->plugin_path)) !== false) {
				include_once($file);
				$current_task = new $task();
				if($current_task->is_necessary() AND ($all OR (!$all AND $current_task->plugin_path == $this->plugin_path))) {
                	$current_task->init_lang();
					$this->needed_updates[$current_task->version]['code'] = $task;
					$this->needed_updates[$current_task->version]['name'] = $current_task->name;
                	$this->needed_updates[$current_task->version]['desc'] = $current_task->lang[$task];
					$this->needed_updates[$current_task->version]['plugin_path'] = $current_task->plugin_path;
				}
				unset($current_task);
			}
		}
	}

	public function is_necessary() { return false; }
	public function is_applicable(){ return true; }
}
?>