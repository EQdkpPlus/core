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
if ( !class_exists( "task" ) ){
  abstract class task{
    public $form_method = "GET";
    public $author = "unknown";
	public $name = "unknown";
    public $version = "0.0.0";

    public $dependencies = array();
    public $type = 'update'; //types: update, plugin_update, fix, import

    //Language stuff
    protected $default_lang = 'english';
    public $lang = array();

    //Stepwise Tasks
    protected $use_steps = false;
    protected $parse_only = false;
	protected $step_order = array('first'); //names of the steps which shall be performed
	protected $steps = array();  //holds keys of steps that shall be performed during this task
	protected $step_data = array(); //holds data which is needed for multiple steps
	protected $current_step = '';
	protected $end = false;

	private $task_name = false;

	public function __construct() {
        $this->task_name = get_class($this);
    }

    public function init_lang(){
    global $user, $eqdkp_root_path;
      $lang_inc = $eqdkp_root_path.'maintenance/includes/tasks/'.$this->task_name.'/language/'.$user->data['user_lang'].'.php';
      if(!is_file($lang_inc)){
      	$lang_inc = $eqdkp_root_path.'maintenance/includes/tasks/'.$this->task_name.'/language/'.$this->default_lang.'.php';
      }
      if(is_file($lang_inc)){
        require($lang_inc);
        $this->lang = $lang;
      }
    }

    public function get_description(){
      return $this->lang[$this->task_name];
    }

    public function a_is_necessary() {
		global $core;
		//if we're in a step-wise task, its necessary to complete it
		return (((strlen($core->config['maintenance_step_standby']) > 3 AND $core->config['maintenance_step_standby'] != 'first' AND $core->config['maintenance_task'] == $this->task_name) ? true : false) OR $this->is_necessary());
	}

	public function a_is_applicable() {
		return $this->is_applicable();
	}

    public abstract function is_necessary();
    public abstract function is_applicable();

    public function a_get_form_content() {
    	global $pdl, $core, $in;
    	if(!$this->use_steps) {
    		if(is_callable(array($this, 'get_form_content'))) {
    			return $this->get_form_content();
    		} else {
    			$pdl->log('maintenance', 'Couldn\'t find method "get_form_content" in task "'.$this->task_name.'".');
    			return false;
    		}
    	}
    	$needed_functions = array('first_step', 'parse_first_step', 'step_end', 'parse_step', 'get_step');
    	foreach($needed_functions as $func) {
    		if(!is_callable(array($this, $func))) {
    			$pdl->log('maintenance', 'Couldn\' find all necessary methods for stepwise Task "'.$this->task_name.'". At least "'.implode('", "', $needed_functions).'" must be defined.');
            	return false;
    		}
    	}

		$this->a_construct();

		//get step where we stopped lasttime
		$key = array_search($core->config['maintenance_step_standby'], $this->step_order);
		if($key !== false AND $core->config['maintenance_step_standby'] !== NULL) {
			$output = $this->next_step($key);
		} elseif($in->get('start_sql_update', false)) {
			$output = $this->next_step(0);
		} else {
			$output = $this->first_step();
		}

		$this->a_destruct();

		return $output;
	}

    private function a_construct() {
    	global $core;
        //load steps we shall do
        $this->steps = explode(',',$core->config['maintenance_this_steps']);
        if(!in_array(0, $this->steps)) {
            array_push($this->steps, 0);
        }
        asort($this->steps);
        $this->step_data = unserialize($core->config['maintenance_step_data']);
        $this->construct();
    }

    public function a_destruct() {
    	global $core, $settings;
    	$this->destruct();
    	if(!$this->end) {
    		$core->config_set('maintenance_task', $this->task_name);
    		$core->config_set('maintenance_step_standby', $this->current_step);
    		$core->config_set('maintenance_this_steps', implode(',',$this->steps));
    		$core->config_set('maintenance_step_data', serialize($this->step_data));
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
		global $db,$tpl, $core;
		$core->config_del('maintenance_task');
		$core->config_del('maintenance_step_standby');
		$core->config_del('maintenance_this_steps');
		$core->config_del('maintenance_step_data');
		$tpl->assign_vars(array(
			'S_STEP_END'	=> true,
		));
		return $this->step_end();
	}
  }
}
?>