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

if ( !class_exists( "task" ) ) {
	require_once($eqdkp_root_path . 'maintenance/includes/task.aclass.php');
}

class sql_update extends task {
	public static function __shortcuts() {
		$shortcuts = array('config', 'user', 'in', 'db',
			'mmt'	=> 'mmtaskmanager'
		);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $form_method		= "get";
	public $author			= "sql_update";
	public $version			= "0.0.0";

	protected $form = '';

	private $needed_updates = array();
	private $calling_update = array();
	private $row_class = 2;

	public function __construct($calling_task, $all=false) {
		$this->version = $this->config->get('plus_version');
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
		$this->current_step = 'first';
		$output = '';
		if(count($this->needed_updates) > 1) {
			$output = '<b>'.$this->user->lang('following_updates_necessary')."</b><br /><ul>";
			foreach($this->needed_updates as $version => $upd) {
				$output .= "<li>".$version." - ".$upd['desc']."</li>";
			}
			$output .= "</ul><input type='submit' name='start_sql_update' value='".$this->user->lang('start_update')."' class=\"mainoption\"/><br /><br />";
		}
		$output .= '<b>'.$this->user->lang('only_this_update')."</b></br><ul><li>".$this->calling_update['version']." - ".$this->calling_update['desc']."</li></ul><input type='submit' name='single_update' value='".$this->calling_update['name']."' class=\"mainoption\"/><input type='hidden' name='single_update_code' value='".$this->calling_update['code']."' />";
		$output .= ($this->in->get('update_all', 0)) ? "<input type='hidden' name='update_all' value='1' />" : "";

		return $output;
	}

	public function parse_first_step() {
		if($this->in->get('single_update', '')) {
			$this->step_order = array('first', $this->in->get('single_update_code'));
		} elseif(!$this->in->get('start_sql_update', '')) {
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
		include_once($this->task_list[$step]);
		$current = registry::register($step);
		$current->init_lang();
		$this->plugin_path = $current->plugin_path;
		if(method_exists($current, 'before_update_function')) {
			$func = $current->before_update_function();
			$this->form .= '<tr class="row'.$this->row_class.'"><td><img src="'.$this->root_path.'images/glyphs/status_'.(($func) ? 'green' : 'red').'.gif"> '.$current->lang['before_update_function'].'</td></tr>';
		}
		$this->do_sql($current->sqls, $current->version, $current->lang, $current->name);
		if(method_exists($current, 'update_function')) {
			$func = $current->update_function();
			$this->form .= '<tr class="row'.$this->row_class.'"><td><img src="'.$this->root_path.'images/glyphs/status_'.(($func) ? 'green' : 'red').'.gif"> '.$current->lang['update_function'].'</td></tr>';
		}
		unset($current);
		registry::register('datacache')->flush();
		return true;
	}

	public function step_end() {
		return $this->form."<tr><td align='center'><a href='".$this->root_path."maintenance/task_manager.php".$this->SID."'>".$this->user->lang('task_manager')."</a></td></tr></table>";
	}

	protected function do_sql($sqls, $version, $lang, $task_name) {
		//run all queries if this task is necessary
		$this->form .= '<table width="80%" align="center" class="colorswitch">';
		$this->form .= '<tr><th class="th_sub">'.sprintf($this->user->lang('executed_tasks'), $task_name).'</th></tr>';
		foreach($sqls as $key => $sql) {
			$this->form .= '<tr><td>';
			if($this->db->query($sql)) {
				$this->form .= '<img src="'.$this->root_path.'images/glyphs/status_green.gif"> ';
			} else {
				$this->form .= '<img src="'.$this->root_path.'images/glyphs/status_red.gif"> ';
			}
			$this->form .=  $lang[$key].'</td></tr>';
				
		}
		if($this->plugin_path) {
			$this->db->query("UPDATE __plugins SET version = '".$version."' WHERE code = '".$this->plugin_path."';");
		} else {
			$this->config->set('plus_version', $version);
		}
	}

	private function get_needed_updates($all=false) {
		$this->task_list = $this->mmt->get_task_list(true);
		foreach($this->task_list as $task => $file) {
			if(strpos($task, (($all) ? 'update_' : 'update_'.$this->plugin_path)) !== false) {
				include_once($file);
				$current_task = registry::register($task);
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_sql_update', sql_update::__shortcuts());
?>