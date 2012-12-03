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

if ( !class_exists( "task" ) ){
	abstract class task extends gen_class {
		public static $shortcuts = array('user', 'config', 'tpl');

		public $form_method			= "get";
		public $author				= "unknown";
		public $name				= "unknown";
		public $version				= "0.0.0";

		public $task_dependencies	= array();
		public $type				= 'update';			//types: update, plugin_update, fix, import

		//Language stuff
		protected $default_lang		= 'english';
		public $lang				= array();

		//Stepwise Tasks
		protected $use_steps		= false;
		protected $parse_only		= false;
		protected $step_order		= array('first');	//names of the steps which shall be performed
		protected $steps			= array();			//holds keys of steps that shall be performed during this task
		protected $step_data		= array();			//holds data which is needed for multiple steps
		protected $current_step		= '';
		protected $end				= false;

		private $task_name			= false;

		public function __construct() {
			$this->task_name = get_class($this);
		}

		public function init_lang(){
			$lang_inc = $this->root_path.'maintenance/includes/tasks/'.$this->task_name.'/language/'.$this->user->data['user_lang'].'.php';
			if(!is_file($lang_inc)){
				$lang_inc = $this->root_path.'maintenance/includes/tasks/'.$this->task_name.'/language/'.$this->default_lang.'.php';
			}
			if(is_file($lang_inc)){
				require($lang_inc);
				$this->lang = $lang;
			}
		}

		public function get_description(){
			if(isset($this->lang[$this->task_name])) {
				return $this->lang[$this->task_name];
			}
			return '';
		}

		public function a_is_necessary() {
			//if we're in a step-wise task, its necessary to complete it
			return ($this->is_necessary() || (strlen($this->config->get('maintenance_step_standby_'.$this->task_name)) > 3 AND $this->config->get('maintenance_step_standby_'.$this->task_name) != 'first') ? true : false);
		}

		public function a_is_applicable() {
			return $this->is_applicable();
		}

		public abstract function is_necessary();
		public abstract function is_applicable();

		public function a_get_form_content() {
			if(!$this->use_steps) {
				if(is_callable(array($this, 'get_form_content'))) {
					return $this->get_form_content();
				} else {
					$this->pdl->log('maintenance', 'Couldn\'t find method "get_form_content" in task "'.$this->task_name.'".');
					return false;
				}
			}
			$needed_functions = array('first_step', 'parse_first_step', 'step_end', 'parse_step', 'get_step');
			foreach($needed_functions as $func) {
				if(!is_callable(array($this, $func))) {
					$this->pdl->log('maintenance', 'Couldn\' find all necessary methods for stepwise Task "'.$this->task_name.'". At least "'.implode('", "', $needed_functions).'" must be defined.');
					return false;
				}
			}

			$this->a_construct();

			//get step where we stopped lasttime
			$key = array_search($this->config->get('maintenance_step_standby_'.$this->task_name), $this->step_order);
			if($key !== false AND $this->config->get('maintenance_step_standby_'.$this->task_name) !== NULL) {
				$output = $this->next_step($key);
			} elseif($this->in->get('start_sql_update', false)) {
				$output = $this->next_step(0);
			} else {
				$output = $this->first_step();
			}
			$this->a_destruct();
			return $output;
		}

		private function a_construct() {
			//load steps we shall do
			$this->steps = explode(',',$this->config->get('maintenance_this_steps_'.$this->task_name));
			if(!in_array(0, $this->steps)) {
				array_push($this->steps, 0);
			}
			asort($this->steps);
			$this->step_data = unserialize($this->config->get('maintenance_step_data_'.$this->task_name));
			$this->construct();
		}

		public function a_destruct() {
			$this->destruct();
			if(!$this->end) {
				$this->config->set('maintenance_task_'.$this->task_name, $this->task_name);
				$this->config->set('maintenance_step_standby_'.$this->task_name, $this->current_step);
				$this->config->set('maintenance_this_steps_'.$this->task_name, implode(',',$this->steps));
				$this->config->set('maintenance_step_data_'.$this->task_name, serialize($this->step_data));
			}
		}

		private function next_step($key) {
			//parse the form-data
			if($key === 0) {
				$parse = $this->parse_first_step();
			} elseif(in_array($key, $this->steps)) {
				$parse = $this->parse_step($this->step_order[$key]);
			} else {
				$parse = true;
			}
			if($parse == false AND ($this->parse_only == false OR $key == 0)) {
				return $this->previous_form($key);
			}
			//get data for new form
			$key++;
			if($key > max($this->steps)) {
				$this->end = true;
				return $this->a_step_end();
			}
			if(in_array($key, $this->steps) AND !$this->parse_only) {
				return $this->get_step($this->step_order[$key]);
			} else {
				return $this->next_step($key);
			}
			//fallback
			return $this->first_step();
		}

		private function previous_form($key) {
			if($key === 0) {
				return $this->first_step();
			} else {
				if(in_array($key, $this->steps)) {
					return $this->get_step($this->step_order[$key]);
				} else {
					$key--;
					return $this->previous_form($key);
				}
			}
		}

		private function a_step_end() {
			$this->config->del('maintenance_task_'.$this->task_name);
			$this->config->del('maintenance_step_standby_'.$this->task_name);
			$this->config->del('maintenance_this_steps_'.$this->task_name);
			$this->config->del('maintenance_step_data_'.$this->task_name);
			$this->tpl->assign_vars(array(
				'S_STEP_END'	=> true,
			));
			return $this->step_end();
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_task', task::$shortcuts);
?>