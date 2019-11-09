<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

class cronjobs extends gen_class {
	public static $dependencies = array('pfh');

	public function __construct(){
		include_once($this->root_path.'core/cronjobs/crontask.aclass.php');
		$this->init_cronsystem();
	}

	private $crontab			= array();
	private $system_cron_dir	= '';
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
		$this->system_cron_dir = $this->root_path.'core/cronjobs/';
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

	public function set_active($task_name, $intStartTime=null){
		if($intStartTime === null) $intStartTime = $this->crontab[$task_name]['start_time'];
		if(!$intStartTime) $intStartTime = $this->time->time;

		$this->pdh->put('cronjobs', 'setActive', array($task_name, array('start_time' => $intStartTime)));
		$this->pdh->process_hook_queue();
		//Reload Crontab
		$this->load_crontab();
	}

	public function set_inactive($task_name){
		$this->pdh->put('cronjobs', 'setInactive', array($task_name));
		$this->pdh->process_hook_queue();
		//Reload Crontab
		$this->load_crontab();
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

			$this->pdh->put('cronjobs', 'add', array($task_name, $params['start_time'], $params['repeat'], $params['repeat_type'], $params['repeat_interval'], $params['extern'], $params['ajax'],$params['delay'], $params['multiple'], $params['active'], $params['editable'], $params['path'], $params['params'], $params['description']));

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

			$this->pdh->put('cronjobs', 'update', array($task_name, $params['start_time'], $params['repeat'], $params['repeat_type'], $params['repeat_interval'], $params['extern'], $params['ajax'],$params['delay'], $params['multiple'], $params['active'], $params['editable'], $params['path'], $params['params'], $params['description']));
		}

		$params['next_run'] = $this->calculate_next_run($params['repeat_interval'], $params['repeat_type'], false, $params['start_time'], $params['start_time']);

		$this->pdh->put('cronjobs', 'setLastAndNextRun', array($task_name, $params['last_run'], $params['next_run']));
		$this->pdh->process_hook_queue();

		$this->crontab[$task_name] = $params;

		return true;
	}


	public function del_cron($task_name){
		if(isset($this->crontab[$task_name])){
			$this->pdh->put('cronjobs', 'delete', array($task_name));
		}
	}


	public function run_cron($task_name, $force_run = false, $force_non_ajax=false){
		if(!$force_run && !$this->cron_necessary($task_name)){
			return false;
		}

		if ($this->crontab[$task_name]['ajax'] == true && !$force_non_ajax){
			if ($force_run){
				$this->tpl->add_js('$.get("'.$this->server_path.'cronjob.php'.$this->SID.'&task='.$task_name.'&force=true", function( data ) {
					if (typeof cronjob_admin_callback !== \'undefined\' && $.isFunction(cronjob_admin_callback)){
						cronjob_admin_callback(data, "'.$task_name.'");
					}
				});');
			} else {
				$this->tpl->add_js('$.get("'.$this->server_path.'cronjob.php'.$this->SID.'&task='.$task_name.'");');
			}
		} else {
			$this->execute_cron($task_name, $force_run);
		}
	}


	public function execute_cron($task_name, $force_run = false){
		define("IN_CRON", true);

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

			//Create Lock File
			$strLockFile = $this->pfh->FolderPath('timekeeper', 'eqdkp').'cron_lock_'.md5($task_name).'.txt';
			if(is_file($strLockFile)){
				$strLockContent = file_get_contents($strLockFile);
				if($strLockContent){
					//Lock file was created more than 0.5 hour ago, so delete it and try again
					if((intval($strLockContent)+1800) < time()){
						$this->pfh->Delete($strLockFile);
					}
				}
				//Return false because of lock file
				return false;
			} else {
				$this->pfh->putContent($strLockFile, time());
			}
			try {
				call_user_func_array(array($cron_task, 'run'), $params);
			}catch (Exception $e){
				//Remove Lock File
				$this->pfh->Delete($strLockFile);
			}
			$this->crontab[$task_name]['last_run'] = $this->time->time;
			if (!$force_run){
				$this->crontab[$task_name]['next_run'] = $this->calculate_next_run($this->crontab[$task_name]['repeat_interval'], $this->crontab[$task_name]['repeat_type'], $this->crontab[$task_name]['multiple'], $this->crontab[$task_name]['next_run'], $this->crontab[$task_name]['start_time']);
			}

			//Save Last and Next Run
			$this->pdh->put('cronjobs', 'setLastAndNextRun', array($task_name, $this->crontab[$task_name]['last_run'], $this->crontab[$task_name]['next_run']));
			$this->pdh->process_hook_queue();
			$this->pfh->Delete($strLockFile);
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
				if ($this->crontab[$task_name]['delay'] == false){
					$this->run_cron($task_name);
					$runcount++;
				} else {
					if ($runcount < 2){
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
		$arrCrontab = $this->pdh->get('cronjobs', 'crontab', array());
		$this->crontab = $arrCrontab;
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
