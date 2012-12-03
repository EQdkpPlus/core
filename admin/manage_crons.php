<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * styles.php
 * Began: Thu January 16 2003
 *
 * $Id$
 *
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class ManageCrons extends EQdkp_Admin
{
    var $crons = array();	
		
    function ManageCrons()
    {
        global $db, $core, $user, $tpl, $pm, $timekeeper;
        global $SID;

        parent::eqdkp_admin();
			
        // Variables
       $this->crons = $timekeeper->list_crons();

        $this->assoc_buttons(array(
            'update' => array(
                'name'    => 'update',
                'process' => 'process_update',
                'check'   => 'a_config_man'),
						
            'form' => array(
                'name'    => '',
                'process' => 'display_list',
                'check'   => 'a_config_man'))
        );

        $this->assoc_params(array(
            'run' => array(
                'name'    => 'mode',
                'value'   => 'run',
                'process' => 'process_run',
                'check'   => 'a_config_man'),
						'enable' => array(
                'name'    => 'mode',
                'value'   => 'enable',
                'process' => 'process_enable',
                'check'   => 'a_config_man'),
						'disable' => array(
                'name'    => 'mode',
                'value'   => 'disable',
                'process' => 'process_disable',
                'check'   => 'a_config_man'),
            'edit' => array(
                'name'    => 'mode',
								'value'   => 'edit',
                'process' => 'display_form',
                'check'   => 'a_config_man'))
        );

    }

    function error_check()
    {
        return false;
    }
		
		function process_run(){
			global $db, $in, $user, $core, $timekeeper, $SID;

			$timekeeper->run_cron($in->get('cron'), true);
			$this->crons[$in->get('cron')]['last_run'] = time();
			
			$core->message(sprintf($user->lang['cron_run_success'], sanitize($in->get('cron'))), $user->lang['success'], 'green');
			$this->display_list();
		}
		
		
		function process_enable(){
			global $db, $in, $user, $core, $timekeeper, $SID;
			if ($this->crons[$in->get('cron')]['editable']){
				$timekeeper->add_cron($in->get('cron'), array('active' => true), true);
			}
			redirect('admin/manage_crons.php'.$SID);
		}
		
		function process_disable(){
			global $db, $in, $user, $core,$timekeeper, $SID;
			
			if ($this->crons[$in->get('cron')]['editable']){
				$timekeeper->add_cron($in->get('cron'), array('active' => false), true);
			}			
			redirect('admin/manage_crons.php'.$SID);
		}

		function process_update(){
			global $db, $in, $user, $core,$timekeeper, $SID, $time;
			$options = array();
			
			if ($this->crons[$in->get('cron')]['editable']){
				$options['description'] = $in->get('cron_desc');
				$options['repeat'] = ($in->get('cron_repeat') == 1) ? true : false;
				$options['params'] = $in->getArray('params', 'string');
				$options['params'] = is_array($options['params']) ? $options['params'] : array();		
				
				$options['repeat_interval'] = $in->get('repeat_value', 0);
				$options['repeat_type'] =  $in->get('repeat_key', 'hourly');
				
				list($start_day, $start_month, $start_year) = explode('.', $in->get('start_date', '0.0.0'));
				
				$options['start_time'] = $time->mktime($in->get('start_h', 0), $in->get('start_m', 0), 0, $start_month, $start_day, $start_year);
				$options['start_time'] = ($in->get('start_date') != '') ? $options['start_time'] : $time->time;
				
				$timekeeper->add_cron($in->get('cron'), $options, true);
			}		
			redirect('admin/manage_crons.php'.$SID);
		}
		
    // ---------------------------------------------------------
    // Display
    // ---------------------------------------------------------
    function display_list(){
			global $db, $core, $user, $tpl, $pm, $SID, $jquery, $eqdkp_root_path, $html, $pcache, $timekeeper, $time;

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
									$next_run = $time->date($user->style['date_time'], $value['next_run']);
								} else {
									$last_run =  $time->date($user->style['date_time'],$value['last_run']);
									$next_run = ' - ';
								}
								$repeat = '';
							}else{
								if ($value['last_run'] == 0){
									$last_run = ' - ';
									$next_run = $time->date($user->style['date_time'], $value['next_run']);
								} else {
									$last_run = $time->date($user->style['date_time'],$value['last_run']);
									$next_run = $time->date($user->style['date_time'], $value['next_run']);
								}
								$repeat = (($value['repeat_interval'] < 2) ? '' : $value['repeat_interval'].'-').$user->lang[$value['repeat_type']];
							}
						} else {
							$last_run = ($value['last_run'] != 0) ? $time->date($user->style['date_time'],$value['last_run']) : ' - ';
							$next_run = ' - ';
							$repeat = '';
						}
					
						$tpl->assign_block_vars('cron_row', array(
							'ID'				=> $key,
							'NAME'			=>	$value['description'],
							'ACTIVE'		=> $value['active'],
							'REPEAT'		=> $repeat,
							'ENABLE_ICON'	=> ($value['active']) ? 'green' : 'red',
							'L_ENABLE'	=> ($value['active']) ? $user->lang['deactivate'] : $user->lang['activate'],
							'S_EDITABLE'	=> $value['editable'],
							'LAST_RUN'	=> $last_run,
							'NEXT_RUN'	=> $next_run,
							'START'			=> ($value['active']) ? $time->date($user->style['date_time'], $value['start_time']) : ' - ',
							'ROW_CLASS'	=> $core->switch_row_class(),
							'ACTIVATE_ICON'	=> ($value['active']) ? 'disable' : 'enable',
							'U_RUN_CRON'	=> 'manage_crons.php'.$SID.'&mode=run&cron='.$key,
							'U_EDIT_CRON'	=> 'manage_crons.php'.$SID.'&mode=edit&cron='.$key,
							'U_ACTIVATE_CRON'	=> 'manage_crons.php'.$SID.'&mode='.(($value['active']) ? 'disable' : 'enable').'&cron='.$key,
						));

					
				} //close foreach	
			} //close if array
			

			$tpl->assign_vars(array(
				'L_ACTION'	=> $user->lang['action'],
				'L_NAME'		=> $user->lang['name'],
				'MANAGE_CRONJOBS'	=> $user->lang['manage_cronjobs'],
				'L_RUN'			=> $user->lang['execute'],
				'L_EDIT'		=> $user->lang['edit'],
				'L_REPEAT'		=> $user->lang['repeat_interval'],
				'L_LAST_RUN'		=> $user->lang['last_run'],
				'L_NEXT_RUN'		=> $user->lang['next_run'],
				'L_START'		=> $user->lang['cron_start_time'],
				'FC_CRONJOBS'	=> sprintf($user->lang['footcount_cronjobs'], $iActiveCrons, count($tmp_crons)),
			));
			
			$core->set_vars(array(
            'page_title'    => $user->lang['manage_cronjobs'],
            'template_file' => 'admin/manage_crons.html',
            'display'       => true)
			);
    }

    function display_form(){
			global $db, $core, $user, $tpl, $pm, $jquery, $SID, $game, $html, $in, $pcache, $eqdkp_root_path, $time;
			
			if (!$this->crons[$in->get('cron')] || $this->crons[$in->get('cron')]['editable'] == false){
				$this->display_list();
			}
			
			$cron_data = $this->crons[$in->get('cron')];
			
			
			$file_name = $in->get('cron').'_crontask.class.php';
			$file_path = $eqdkp_root_path.$this->crons[$in->get('cron')]['path'].$file_name;
			
			if(file_exists($file_path)){
				require($file_path);
				$class = $in->get('cron').'_crontask';
				$cron_task = new $class();
				$params = $this->crons[$in->get('cron')]['params'];
				$options = $cron_task->options;
			}
			if (is_array($options)){
				foreach ($options as $key=>$value){
					$value['value'] = $params[$key];
					$value['name'] = 'params['.$value['name'].']';
					$tpl->assign_block_vars('param_row', array(
						'NAME'	=> ($user->lang['cron_'.$in->get('cron').'_'.$key]) ? $user->lang['cron_'.$in->get('cron').'_'.$key] : $value['lang'],
						'FIELD'	=> $html->widget($value),
					));
				}
			}
			
			$repeat_dd = array(
				'minutely'	=> $user->lang['minutely'],			
				'hourly'		=> $user->lang['hourly'],	
				'dayly'			=> $user->lang['daily'],	
				'weekly'		=> $user->lang['weekly'],	
				'monthly'		=> $user->lang['monthly'],	
				'yearly'		=> $user->lang['yearly'],	
			);
			
			$tpl->assign_vars(array(
				'S_PARAMS'		=> (count($options) > 0) ? true : false,
				'S_EDIT'			=> true,
				'MANAGE_CRONJOBS'	=> $user->lang['manage_cronjobs'],
				'CRON_NAME'		=> sanitize($in->get('cron')),
				'CRON_DESC'		=> sanitize($cron_data['description']),
				'CRON_REPEAT'	=> ($cron_data['repeat']) ? 'checked' : '',
				'CRON_REPEAT_VALUE'	=> $cron_data['repeat_interval'],
				'START_PICKER'	=> $jquery->Calendar('start_date', $time->date('d.m.Y', $cron_data['start_time'])),
				'START_H'			=> $time->date('H', $cron_data['start_time']),
				'START_M'			=> $time->date('i', $cron_data['start_time']),
				'REPEAT_DD'		=> $html->DropDown('repeat_key', $repeat_dd, $cron_data['repeat_type']),			
				
				'L_REPEAT_INTERVAL'		=> $user->lang['repeat_interval'],
				'L_REPEAT'		=> $user->lang['repeat'],
				'L_SETTINGS'		=> $user->lang['settings'],
				'L_NAME'		=> $user->lang['name'],
				'L_DESC'		=> $user->lang['description'],
				'L_SAVE'		=> $user->lang['save'],
				'L_CANCEL'	=> $user->lang['cancel'],
				'L_DAYS'		=> $user->lang['days'],
				'L_HOURS'		=> $user->lang['hours'],
				'L_MINUTES'		=> $user->lang['minutes'],
				'L_SECONDS'		=> $user->lang['seconds'],
				'L_START'		=> $user->lang['cron_start_time'],
				'L_REPEAT_INFO'	=> $user->lang['repeat_note'],
			));
			
			$core->set_vars(array(
            'page_title'    => $user->lang['manage_cronjobs'],
            'template_file' => 'admin/manage_crons.html',
            'display'       => true)
			);


}
}
$manage_styles = new ManageCrons;
$manage_styles->process();
?>

