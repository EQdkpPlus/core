<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class ManageCrons extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'jquery', 'core', 'config', 'time', 'html', 'timekeeper'=>'timekeeper');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public $crons = array();
	
	public function __construct() {
		$this->user->check_auth('a_config_man');
		$handler = array(
			'mode' => array(
				array('process' => 'run', 'value' => 'run', 'csrf'=>true),
				array('process' => 'enable', 'value' => 'enable', 'csrf'=>true),
				array('process' => 'disable', 'value' => 'disable', 'csrf'=>true),
				array('process' => 'edit', 'value' => 'edit')
			),
		);
		parent::__construct(false, $handler);
		
		// Variables
		$this->crons = $this->timekeeper->list_crons();
		$this->process();
	}

	public function run() {
		$this->timekeeper->run_cron($this->in->get('cron'), true);
		$this->crons[$this->in->get('cron')]['last_run'] = time();
		
		$this->core->message(sprintf($this->user->lang('cron_run_success'), sanitize($this->in->get('cron'))), $this->user->lang('success'), 'green');
		$this->display();
	}

	public function enable(){
		if ($this->crons[$this->in->get('cron')]['editable']){
			$this->timekeeper->add_cron($this->in->get('cron'), array('active' => true), true);
		}
		$this->display();
	}
	
	public function disable(){
		if ($this->crons[$this->in->get('cron')]['editable']){
			$this->timekeeper->add_cron($this->in->get('cron'), array('active' => false), true);
		}			
		$this->display();
	}

	public function update(){
		$options = array();
			
		if ($this->crons[$this->in->get('cron')]['editable']){
			$options['description'] = $this->in->get('cron_desc');
			$options['repeat'] = ($this->in->get('cron_repeat') == 1) ? true : false;
			$options['params'] = $this->in->getArray('params', 'string');
			$options['params'] = is_array($options['params']) ? $options['params'] : array();		
			
			$options['repeat_interval'] = $this->in->get('repeat_value', 0);
			$options['repeat_type'] =  $this->in->get('repeat_key', 'hourly');
			
			$options['start_time'] = $this->time->time;
			if($this->in->exists('start_date')) $options['start_time'] = $this->time->fromformat($this->in->get('start_date', '0.0.0'), 1);
			$this->timekeeper->add_cron($this->in->get('cron'), $options, true);
		}		
		$this->display();
	}
	
	// ---------------------------------------------------------
	// Display
	// ---------------------------------------------------------
	public function display(){
		if ($this->in->exists('mode')){
			$this->crons = $this->timekeeper->list_crons();
		}
	
		if (is_array($this->crons)){
			foreach ($this->crons as $key=>$value){
				$tmp_crons[$key] = $value['active'];
			}
			array_multisort($tmp_crons, SORT_DESC, SORT_REGULAR);
			$iActiveCrons = 0;
				
			foreach ($tmp_crons as $key=>$value){
				$value = $this->crons[$key];
				if ($value['active']){
					$iActiveCrons++;
					//single run or repeated task?
					if($value['repeat'] == false){
						//Wurde noch nicht ausgeführt
						if ($value['last_run'] == 0){
							$last_run = ' - ';
							$next_run = $this->time->user_date($value['next_run'], true);
						} else {
							$last_run =  $this->time->user_date($value['last_run'], true);
							$next_run = ' - ';
						}
						$repeat = '';
					}else{
						if ($value['last_run'] == 0){
							$last_run = ' - ';
							$next_run = $this->time->user_date($value['next_run'], true);
						} else {
							$last_run = $this->time->user_date($value['last_run'], true);
							$next_run = $this->time->user_date($value['next_run'], true);
						}
						$repeat = (($value['repeat_interval'] < 2) ? '' : $value['repeat_interval'].'-').$this->user->lang($value['repeat_type']);
					}
				} else {
					$last_run = ($value['last_run'] != 0) ? $this->time->user_date($value['last_run'], true) : ' - ';
					$next_run = ' - ';
					$repeat = '';
				}
				$this->tpl->assign_block_vars('cron_row', array(
					'ID'				=> $key,
					'NAME'			=>	$value['description'],
					'ACTIVE'		=> $value['active'],
					'REPEAT'		=> $repeat,
					'ENABLE_ICON'	=> ($value['active']) ? 'green' : 'red',
					'L_ENABLE'	=> ($value['active']) ? $this->user->lang('deactivate') : $this->user->lang('activate'),
					'S_EDITABLE'	=> $value['editable'],
					'LAST_RUN'	=> $last_run,
					'NEXT_RUN'	=> $next_run,
					'START'			=> ($value['active']) ? $this->time->user_date($value['start_time'], true) : ' - ',
					'ACTIVATE_ICON'	=> ($value['active']) ? 'disable' : 'enable',
					'U_RUN_CRON'	=> 'manage_crons.php'.$this->SID.'&amp;mode=run&amp;cron='.$key.'&amp;link_hash='.$this->CSRFGetToken('mode'),
					'U_EDIT_CRON'	=> 'manage_crons.php'.$this->SID.'&amp;mode=edit&amp;cron='.$key.'&amp;link_hash='.$this->CSRFGetToken('mode'),
					'U_ACTIVATE_CRON'	=> 'manage_crons.php'.$this->SID.'&amp;mode='.(($value['active']) ? 'disable' : 'enable').'&amp;cron='.$key.'&amp;link_hash='.$this->CSRFGetToken('mode'),
				));
			} //close foreach	
		} //close if array

		$this->tpl->assign_var('FC_CRONJOBS', sprintf($this->user->lang('footcount_cronjobs'), $iActiveCrons, count($tmp_crons)));	
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_cronjobs'),
			'template_file'		=> 'admin/manage_crons.html',
			'display'			=> true)
		);
	}

	public function edit(){
		if (!$this->crons[$this->in->get('cron')] || $this->crons[$this->in->get('cron')]['editable'] == false){
			$this->display_list();
		}
		
		$cron_data = $this->crons[$this->in->get('cron')];

		$file_name = $this->in->get('cron').'_crontask.class.php';
		$file_path = $this->root_path.$this->crons[$this->in->get('cron')]['path'].$file_name;
		
		if(file_exists($file_path)){
			require($file_path);
			$class = $this->in->get('cron').'_crontask';
			$cron_task = registry::register($class);
			$params = $this->crons[$this->in->get('cron')]['params'];
			$options = $cron_task->options();
		}
		if (is_array($options)){
			foreach ($options as $key=>$value){
				if($value['type'] == 'dropdown' || $value['type'] == 'jq_multiselect' || $value['type'] == 'multiselect' || $value['type'] == 'checkbox')
					$value['selected'] = isset($params[$key]) ? $params[$key] : '';
				else $value['value'] = isset($params[$key]) ? $params[$key] : '';
				$value['name'] = 'params['.$value['name'].']';
				$this->tpl->assign_block_vars('param_row', array(
					'NAME'	=> ($this->user->lang('cron_'.$this->in->get('cron').'_'.$key, false, false)) ? $this->user->lang('cron_'.$this->in->get('cron').'_'.$key) : $value['lang'],
					'HELP'	=> ($this->user->lang('cron_'.$this->in->get('cron').'_'.$key.'_help', false, false)) ? $this->user->lang('cron_'.$this->in->get('cron').'_'.$key.'_help') : '',
					'FIELD'	=> $this->html->widget($value),
				));
			}
		}
		
		$repeat_dd = array(
			'minutely'		=> $this->user->lang('minutely'),
			'hourly'		=> $this->user->lang('hourly'),
			'daily'			=> $this->user->lang('daily'),
			'weekly'		=> $this->user->lang('weekly'),
			'monthly'		=> $this->user->lang('monthly'),
			'yearly'		=> $this->user->lang('yearly'),
		);
			
		$this->tpl->assign_vars(array(
			'S_PARAMS'				=> (count($options) > 0) ? true : false,
			'S_EDIT'				=> true,
			'CRON_NAME'				=> sanitize($this->in->get('cron')),
			'CRON_DESC'				=> sanitize($cron_data['description']),
			'CRON_REPEAT'			=> ($cron_data['repeat']) ? 'checked="checked"' : '',
			'CRON_REPEAT_VALUE'		=> $cron_data['repeat_interval'],
			'START_PICKER'			=> $this->jquery->Calendar('start_date', $this->time->user_date($cron_data['start_time'], true, false, false, function_exists('date_create_from_format')), '', array('timepicker' => true)),
			'REPEAT_DD'				=> $this->html->DropDown('repeat_key', $repeat_dd, $cron_data['repeat_type']),
		));
		
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_cronjobs'),
			'template_file'		=> 'admin/manage_crons.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ManageCrons', ManageCrons::__shortcuts());
registry::register('ManageCrons');
?>
