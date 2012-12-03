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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('MAINTENANCE_MODE',1);

$eqdkp_root_path = '../';
$lite = true;
define('DEBUG', 3);
require_once($eqdkp_root_path.'common.php');
require_once($eqdkp_root_path.'maintenance/includes/task.aclass.php');

class task_display extends gen_class {
	public static $shortcuts = array('tpl', 'user', 'in', 'pdl',
		'core' => array('core', array('maintenance', 'task.html', 'maintenance_message.html')),
		'mmt'	=> 'mmtaskmanager',
	);

	public function __construct() {
		//Check the auth
		$this->core->check_auth();
		$this->core->page_header();

		$task_list	= $this->mmt->get_task_list(true);

		if($this->in->exists('task')){
			$task = $this->in->get('task');
		} else {
			$this->core->message_die($this->user->lang('unknown_task_warning'));
		}

		if(!in_array($task, array_keys($task_list))){
			$this->core->message_die($this->user->lang('unknown_task_warning'));
		}else{
			$timer_start = microtime(true);
			if(!$this->pdl->type_known('maintenance')) $this->pdl->register_type('maintenance');
			require_once($task_list[$task]);
			$task_obj = registry::register($task);

			if(!$task_obj->is_applicable()){
				$this->core->message_die($this->user->lang('application_warning'));
			}

			$task_obj->init_lang();

			if(!empty($task_obj->dependencies)){
				//check which dependencies are not fullfilled
				$form = '<table width="100%" border="1">';
				$form .= '<tr><th>'.$this->user->lang('dependency_warning').'</th></tr>';
				foreach($task_obj->task_dependencies as $dependency){
					require_once($task_list[$dependency]);
					$dep_obj	= registry::register($dependency);
					$dep_obj->init_lang();
					$form		.= '<tr><td>'.$dep_obj->get_description().'</td></tr>';
				}
				$form .= '<tr><th><a href="./task.php'.$this->SID.'&amp;task='.$task_obj->dependencies[0].'">'.$this->user->lang('start_here').'</a></th></tr>';
				$form .= '</table>';

				$this->tpl->assign_vars(array(
					'FORM_METHOD'		=> 'GET',
					'TASK_NAME'			=> $task_obj->name,
					'TASK_DESC'			=> $task_obj->lang[$task],
					'TASK_OUTPUT'		=> $form,
				));
			}else{

				$this->tpl->assign_vars(array(
					'FORM_METHOD'		=> $task_obj->form_method,
					'TASK_NAME'			=> $task,
					'TASK_DESC'			=> $task_obj->lang[$task],
					'TASK_OUTPUT'		=> $task_obj->a_get_form_content(),
				));
			}
			$timer_end = microtime(true);
			$this->core->create_breadcrump($this->user->lang($task_obj->type), 'task_manager.php'.$this->SID.'&amp;type='.$task_obj->type);
			$this->core->create_breadcrump($task_obj->lang[$task]);


			$this->tpl->assign_vars(array(
				'TIMER_OUT'				=> substr(($timer_end - $timer_start), 0, 5),
				'L_APPLICABLE_WARNING'	=> $this->user->lang('applicable_warning'),
				'S_APPLICABLE_WARNING'	=> ($task_obj->type == 'update' || $task_obj->type == 'plugin_update') && !$task_obj->a_is_necessary() && $this->in->get('single_update_code') == '' && $this->in->get('update_all') == '',
				'L_STEPEND_INFO'		=> $this->user->lang('stepend_info'),
			));
		}

		$this->core->page_tail();
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_task_display', task_display::$shortcuts);
registry::register('task_display');
?>