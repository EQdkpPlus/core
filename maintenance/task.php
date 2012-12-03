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
// EQdkp required files/vars
define('EQDKP_INC', true);

$eqdkp_root_path = '../';

require_once('./common_lite.php');
require_once('./includes/template_wrap.class.php');

//Check the auth
$core->check_auth();

$tpl = new Template_Wrap('maintenance', 'task.html', 'maintenance_message.html');

$task_list = $task_manager->get_task_list(true);

if(isset($_GET['task'])){
  $task = $_GET['task'];
}elseif(isset($_POST['task'])){
  $task = $_POST['task'];
}else{
  $tpl->message_die($user->lang['unknown_task_warning']);
}

if(!in_array($task, array_keys($task_list))){
  $tpl->message_die($user->lang['unknown_task_warning']);
}else{
  $timer_start = microtime(true);
  $pdl->register_type('maintenance');
  require_once($task_list[$task]);
  $task_obj = new $task();

  if(!$task_obj->is_applicable()){
	$tpl->message_die($user->lang['application_warning']);
  }

  $task_obj->init_lang();

  if(!empty($task_obj->dependencies)){
    //check which dependencies are not fullfilled
    $form = '<table width="100%" border="1">';
    $form .= '<tr><th>'.$user->lang['dependency_warning'].'</th></tr>';
    foreach($task_obj->dependencies as $dependency){
      require_once($task_list[$dependency]);
      $dep_obj = new $dependency();
      $dep_obj->init_lang();
      $form .= '<tr><td>'.$dep_obj->get_description().'</td></tr>';
    }
    $form .= '<tr><th><a href="./task.php?task='.$task_obj->dependencies[0].'">'.$user->lang['start_here'].'</a></th>';

    $form .= '</table>';
		
    $tpl->assign_vars(array(
      'FORM_METHOD' => 'GET',
      'TASK_NAME'   => $task_obj->name,
	  'TASK_DESC'		=> $task_obj->lang[$task],
	  'TASK_OUTPUT' => $form,
    ));
  }else{

    $tpl->assign_vars(array(
      'FORM_METHOD' => $task_obj->form_method,
      'TASK_NAME'   => $task,
			'TASK_DESC'		=> $task_obj->lang[$task],
    	'TASK_OUTPUT' => $task_obj->a_get_form_content(),
    ));
  }
  $timer_end = microtime(true);
	$core->create_breadcrump($user->lang[$task_obj->type], 'task_manager.php?type='.$task_obj->type);
	$core->create_breadcrump($task_obj->lang[$task]);
	

  $tpl->assign_vars(array(
    'TIMER_OUT' => substr(($timer_end - $timer_start), 0, 5),
		'L_APPLICABLE_WARNING'	=> $user->lang['applicable_warning'],
		'S_APPLICABLE_WARNING'	=> ($task_obj->type == 'update' || $task_obj->type == 'plugin_update') && !$task_obj->a_is_necessary() && $in->get('single_update_code') == '' && $in->get('update_all') == '',
		'L_STEPEND_INFO'	=> $user->lang['stepend_info'],
  ));
}

$tpl->page_header();
$tpl->page_tail();

?>