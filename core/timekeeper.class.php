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

class timekeeper extends gen_class {
	public static $shortcuts = array('pfh', 'time', 'tpl');
	public static $dependencies = array('pfh');

	private $times = array();
	private $time_file = 'times.php';
	private $save_necessary = false;

	public function __construct(){
		$this->init_timekeeper();
		$this->init_cronsystem();
		$this->pfh->secure_folder('timekeeper', 'eqdkp');
	}

	private function init_timekeeper(){
		$this->time_file  = $this->pfh->FolderPath('timekeeper', 'eqdkp').'times.php';
		$result = @file_get_contents($this->time_file);
		if($result !== false){
			$this->times = unserialize($result);
		}else{
			$this->pfh->putContent($this->time_file, serialize(array()));
			//file_put_contents($this->time_file, serialize(array()));
		}
	}

	public function put($class, $event, $time = null, $force_write = false){
		if($time == null){
			$time = $this->time->time;
		}
		$this->times[$class][$event] = $time;
		if($force_write){
			$this->saveToFile();
		}else{
			$this->save_necessary = true;
		}
	}

	public function get($class, $event=null){
		if($event == null){
			if(is_array($this->times[$class])){
				$max_time = 0;
				foreach($this->times[$class] as $event => $time){
					if($time > $max_time)
						$max_time = $time;
				}
			}
		}else{
			$max_time = (isset($this->times[$class][$event])) ? $this->times[$class][$event] : 0;
		}
		return $max_time;
	}

	public function del($class, $event=null){
		if($event == null){
			unset($this->times[$class]);
		}else{
			unset($this->times[$class][$event]);
		}
		$this->save_necessary = true;
	}

	public function clear(){
		$this->times = array();
		$this->save_necessary = true;
	}

	public function saveToFile(){
		$this->pfh->putContent($this->time_file, serialize($this->times));
		//file_put_contents($this->time_file, serialize($this->times));
		$this->save_necessary = false;
	}

	public function __destruct(){
		if($this->save_necessary)
			$this->saveToFile();
	}

	//cron functionality
	public $MAX_DATE			= 1577836800;	//mktime(0,0,0,1,1,2020);
	public $MIN_DATE			= 315532800;	//mktime(0,0,0,1,1,1980);

	public $SECOND				= 1;
	public $MINUTE				= 60;
	public $HOUR				= 3600;
	public $DAY					= 86400;
	public $WEEK				= 604800;

	private $crontab			= array();
	private $system_cron_dir	= '';
	private $crontab_file		= "crontab.php";
	private $crontask_defaults = array(
		'repeat_interval'	=> 1,
		'start_time'		=> null,
		'extern'			=> false,
		'ajax'				=> true,
		'delay'				=> true,
		'repeat'			=> false,
		'repeat_type'		=> 'hourly',
		'multiple'			=> false,
		'active'			=> false,
		'path'				=> 'core/crons/',
		'params'			=> array(),
		'editable'			=> false,
		'description'		=> null
	);

	private function init_cronsystem(){
		$this->system_cron_dir = $this->root_path.'core/crons/';
		$this->crontab_file  = $this->pfh->FolderPath('timekeeper', 'eqdkp').'crontab.php';
		$this->load_crontab();
		$this->scan_system_crontasks();
	}

	public function scan_system_crontasks(){
		$dh = opendir($this->system_cron_dir);
		while (false !== ($file = readdir($dh))) {
			if (substr($file, -19) == '_crontask.class.php'){
				$task_name = substr($file, 0, -19);
				if(!array_key_exists($task_name, $this->crontab)){
					require($this->system_cron_dir.$file);
					if($task_name){
						$task_class = $task_name.'_crontask';
						$task = registry::register($task_class);
						call_user_func(array($this, 'add_cron'), $task_name, $task->defaults);
						unset($task);
					}
				}
			}
		}
	}

	public function add_cron($task_name, $custom_params = array(), $is_update = false){
		if(array_key_exists($task_name, $this->crontab) && $is_update == false)
			return false;

		if(!$is_update){
			$params = $this->crontask_defaults;
			if(!empty($custom_params)){
				$valid_keys = array_keys($this->crontask_defaults);
				foreach($custom_params as $key => $value){
					if(in_array($key, $valid_keys)){
						$params[$key] = $value;
					}
				}
			}
			if($params['start_time'] == null)
				$params['start_time'] = $this->time->time;
			if($params['description'] == null)
				$params['description'] = $task_name;
			$params['last_run'] = 0;
		}else{
			$params = $this->crontab[$task_name];
			if (!$params){
				$params = $this->crontask_defaults;
			}
			if(!empty($custom_params)){
				$valid_keys = array_keys($this->crontask_defaults);
				foreach($custom_params as $key => $value){
					if(in_array($key, $valid_keys)){
						$params[$key] = $value;
					}
				}
			}
		}

		$params['next_run'] = $this->calculate_next_run($params['repeat_interval'], $params['repeat_type'], false, $params['start_time'], $params['start_time']);
		$this->crontab[$task_name] = $params;
		$this->save_crontab($this->crontab);
		return true;
	}

	public function del_cron($task_name){
		if(isset($this->crontab[$task_name])){
			unset($this->crontab[$task_name]);
			$this->save_crontab($this->crontab);
		}
	}

	public function run_cron($task_name, $force_run = false){
		if(!$force_run && !$this->cron_necessary($task_name)){
			return false;
		}

		if ($this->crontab[$task_name]['ajax'] === true){		
			if ($force_run){
				$this->tpl->add_js('$.get("'.$this->root_path.'cronjob.php'.$this->SID.'&task='.$task_name.'&force=true");');
			} else {
				$this->tpl->add_js('$.get("'.$this->root_path.'cronjob.php'.$this->SID.'&task='.$task_name.'");');
			}
		} else {
			$this->execute_cron($task_name, $force_run);
		}
	}

	public function execute_cron($task_name, $force_run = false){
		
		if(!$force_run && !$this->cron_necessary($task_name)){
			return false;
		}
		
		$file_name	= $task_name.'_crontask.class.php';
		$file_path	= $this->root_path.$this->crontab[$task_name]['path'].$file_name;

		if(!file_exists($file_path)){
			$this->del_cron($task_name);
			return false;
		}else{
			require($file_path);
			$class = $task_name.'_crontask';
			$cron_task = registry::register($class);
			$params = $this->crontab[$task_name]['params'];
			if (!$params){
				$params = array();
			}
			call_user_func_array(array($cron_task, 'run'), $params);
			$this->crontab[$task_name]['last_run'] = $this->time->time;
			if (!$force_run){
				$this->crontab[$task_name]['next_run'] = $this->calculate_next_run($this->crontab[$task_name]['repeat_interval'], $this->crontab[$task_name]['repeat_type'], $this->crontab[$task_name]['multiple'], $this->crontab[$task_name]['next_run'], $this->crontab[$task_name]['start_time']);
			}
			$this->save_crontab($this->crontab);
		}
	}

	public function cron_necessary($task_name){
		//task active?
		if($this->crontab[$task_name]['active'] == true){
			//single run or repeated task?
			if($this->crontab[$task_name]['repeat'] == false){
				//never run and due
				if($this->crontab[$task_name]['next_run'] < $this->time->time && !$this->crontab[$task_name]['last_run']){
					return true;
				}
			}else{
				//never run and due
				if($this->crontab[$task_name]['next_run'] < $this->time->time){
					return true;
				}
			}
		}
		return false;
	}

	//Runs necessary Crons
	public function handle_crons($extern = false){
		$runcount = 0;
		foreach(array_keys($this->crontab) as $task_name){
			//For external run
			if ($extern){
				if ($this->crontab[$task_name]['extern']){
					$this->run_cron($task_name);
				}
			} elseif ($this->cron_necessary($task_name)) {
				//Führ nur alle wichtigen aus und verzögere den Rest
				if ($this->crontab[$task_name]['delay'] === false){
					$this->run_cron($task_name);
					$runcount++;
				} else {
					if ($runcount < 3){
						$this->run_cron($task_name);
						$runcount++;
					}
				}
			}
		}
	}

	public function list_crons($cron=''){
		return isset($this->crontab[$cron]) ? $this->crontab[$cron] : $this->crontab;
	}

	private function load_crontab(){
		$result = @file_get_contents($this->crontab_file);
		if($result !== false){
			$this->crontab = unserialize($result);
		}else{
			$this->save_crontab(array());
		}
	}

	private function save_crontab($crontab){
		$this->pfh->putContent($this->crontab_file, serialize($crontab));
		//file_put_contents($this->crontab_file, serialize($crontab));
	}

	public function calculate_next_run($repeat_interval, $repeat_type = 'minutely', $multiple = false, $next_run_eta = false, $start_time = false){
		if ($next_run_eta && $next_run_eta > 1){
			$relative_time = $next_run_eta;
		} elseif ($start_time){
			$relative_time = $start_time;
		} else {
			$relative_time = $this->time->time;
		}

		switch ($repeat_type){
			case 'minutely' : $next_run = strtotime("+".($repeat_interval*60)." seconds", $relative_time);
			break;

			case 'hourly' : $next_run = strtotime("+".$repeat_interval." hours", $relative_time);
			break;

			case 'daily' : $next_run = strtotime("+".$repeat_interval." days", $relative_time);
			break;

			case 'weekly':	$next_run = strtotime("+".$repeat_interval." weeks", $relative_time);
			break;

			case 'monthly': $next_run = $this->add_months($repeat_interval, $relative_time);
			break;

			case 'yearly':	$next_run = strtotime("+".$repeat_interval." years", $relative_time);
			break;
			default: $next_run = strtotime("+1 hour", $relative_time);
		}

		if ($next_run < $this->time->time){
			if ($multiple){
				//Gebe die Zeit zurück, damit der Cron mehrfach ausgeführt wird
				return $next_run;
			} else {
				//Berechne eine Zeit, die in der Zukunft liegt, also den nächsten Run
				$next_run_a =  $next_run;
				while($next_run_a < $this->time->time){
					$next_run_a = $this->calculate_next_run($repeat_interval, $repeat_type, false, $next_run_a);
				}
				return $next_run_a;
			}
		}
		return $next_run;
	}
	
	public function add_months($number, $time){
		if (date("d", $time) == date("t", $time)){
			for ($i = 0; $i < $number; $i++){
				$time = $this->addRealMonth($time);
			}
			return $time;
		} else {
		
			return strtotime("+".$number." month", $time);
		}
	}
	
	public function lastDayOfMonth($month, $year)
	{
		 $result = strtotime("{$year}-{$month}-01");
		 $result = strtotime('-1 second', strtotime('+1 month', $result));
		 return date('Y-m-d', $result);
	}
	
	public function addRealMonth($timeStamp)
	{
			// Check if it's the end of the year and the month and year need to be changed
			$tempMonth = date('m', $timeStamp);
			$tempYear  = date('Y', $timeStamp);
			if($tempMonth == "12")
			{
					$tempMonth = 1;
					$tempYear++;
			}
			else
					$tempMonth++;
		 
			$newDate = $this->lastDayOfMonth($tempMonth, $tempYear);
			return strtotime($newDate." ".date("H", $timeStamp).":".date("i", $timeStamp));
	} 
}





abstract class crontask extends gen_class {
	public $defaults = array(
		'start_time'		=> null,
		'extern'			=> false,
		'ajax'				=> true,
		'delay'				=> true,
		'repeat'			=> false,
		'repeat_type'		=> 'hourly',
		'repeat_interval'	=> 1,
		'multiple'			=> false,
		'active'			=> false,
		'path'				=> 'core/crons/',
		'params'			=> array(),
		'editable'			=> false,
		'description'		=> null
	);

	//overwritten in real implementations
	abstract public function run();
	
	public function options() {
		if(isset($this->options)) return $this->options;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) {
	registry::add_const('short_timekeeper', timekeeper::$shortcuts);
	registry::add_const('dep_timekeeper', timekeeper::$dependencies);
}
?>