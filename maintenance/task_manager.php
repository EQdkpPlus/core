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

if ($in->get('activate') != ""){
	$core->config_set('pk_maintenance_mode', 1);
	$core->config_set('pk_maintenance_message', $in->get('maintenance_message'));
}
if ($in->get('leave') != ""){
	redirect('admin/index.php');
}


$tpl = new Template_Wrap('maintenance', 'task_manager.html', 'maintenance_message.html');

$task_list = $task_manager->get_task_list(true);

$task_data = array();
$types = array();
$nec_types = array();
$necessary = array('necessary_tasks' => false, 'applicable_tasks' => false, 'not_applicable_tasks' => false);
foreach ($task_list as $task => $task_path){
  require_once($task_path);
  $task_obj = new $task();
  $task_obj->init_lang();
  $types[$task_obj->type] = 0;
  $key = '';
  if($in->get('type', 'home') != $task_obj->type AND $in->get('type', 'home') != 'home') continue;
  if($task_obj->a_is_applicable()) {
	$necessary['applicable_tasks'] = true;
	$key = 'applicable_tasks';
  }else{
    $necessary['not_applicable_tasks'] = true;
	$key = 'not_applicable_tasks';
  }
  if($task_obj->a_is_necessary()) {
	$necessary['necessary_tasks'] = true;
	$key = 'necessary_tasks';
	$nec_types[] = $task_obj->type;
  }
  $task_data[$key][$task] = array(
  	'desc' => $task_obj->get_description(),
  	'type' => $task_obj->type,
  	'necessary' => $task_obj->a_is_necessary(),
  	'applicable' => $task_obj->a_is_applicable(),
  	'version' => $task_obj->version,
  	'author' => $task_obj->author,
	'name' => $task_obj->name
  );
  unset($task_obj);
}

//disable if no necessary tasks
if ($in->get('disable') == true || $in->get('start_tour') != "" || $in->get('no_tour') != "" || $in->get('guild_import') != "") {
	if(!$necessary['necessary_tasks']) {
		$core->config_set('pk_maintenance_mode', 0);
		if($in->get('start_tour') == "true") {
			$redirect_url = 'admin/?tour=start';
		} elseif($in->get('no_tour' == "true")) {
			$redirect_url = 'admin/settings.php';
		} elseif($in->get('guild_import') == "true") {
			$rediret_url = 'admin/settings.php#fragments-game';
		} else {
			$redirect_url = 'admin/index.php';
		}
		redirect($redirect_url);
	} else {
		$tpl->assign_vars(array(
			'NO_LEAVE' => 1,
			'L_NO_LEAVE' => $user->lang['no_leave'],
			'L_NO_LEAVE_ACCEPT' => $user->lang['no_leave_accept'])
		);
	}
}

//task-sort function
function sort_tasks($a, $b) {
	$ret = strcmp($a['type'], $b['type']);
	if($ret == 0) {
		return compareVersion($a['version'], $b['version']);
	}
	return $ret;
}
//output tasks
if($in->get('type', 'home') == 'home') {
  $update_all = false;
  if(count($task_data['necessary_tasks']) > 0) {
	$tpl->assign_vars(array(
		'S_NEC_TASKS' => true,
		'L_NEC_TASK' => $user->lang['nec_tasks'])
	);
	foreach($types as $type => $unused) {
	  if(in_array($type, $nec_types)) {
		$tpl->assign_block_vars('tasks_list', array(
			'L_TASKS' => $user->lang[$type])
		);
		//sort task_data 1st by type, 2nd by version
		uasort($task_data['necessary_tasks'], 'sort_tasks');
		foreach($task_data['necessary_tasks'] as $task => $data) {
		  if($data['type'] == $type) {
			$i = ($i == 1) ? 2 : 1;
			$tpl->assign_block_vars('tasks_list.spec_task_list', array(
				'CLASS' => 'row'.$i,
				'STATUS'	=> $tpl->StatusIcon('false'),
				'NAME' => $data['name'],
				'LINK' => "./task.php?task=".$task,
				'DESCRIPTION' => $data['desc'],
				'AUTHOR' => $data['author'],
				'VERSION' => $data['version'])
			);
			$update_all = $task;
		  }
		}
	  }
	}
  } else {
	$tpl->assign_vars(array(
		'L_NO_NEC_TASK' => $user->lang['no_nec_tasks'],
		'S_HIDE_TASK_TABLE'	=> true,
		'S_NO_NEC_TASKS' => true)
	);
  }
} else {
	if (count($task_data['necessary_tasks']) > 0){
		$tpl->assign_vars(array(
			'S_NEC_TASKS' => true,
			'L_NEC_TASK' => $user->lang['nec_tasks_available'])
		);
	}
		
	foreach($necessary as $key => $nec) {
	  if($nec) {
		$tpl->assign_block_vars('tasks_list', array(
			'L_TASKS' => $user->lang[$key],
			'S_APLLICABLE_TASKS'	=> ($key == 'applicable_tasks') ? true : false,
		));

		//sort task_data 1st by type, 2nd by version
		uasort($task_data[$key], 'sort_tasks');
		foreach($task_data[$key] as $task => $data) {
			$i = ($i == 1) ? 2 : 1;
			$tpl->assign_block_vars('tasks_list.spec_task_list', array(
				'CLASS' => 'row'.$i,
				'NAME' => $data['name'],
				'STATUS'	=> $tpl->StatusIcon(($key == 'necessary_tasks') ? 'false' : 'ok'),
				'LINK' => "./task.php?task=".$task,
				'DESCRIPTION' => $data['desc'],
				'AUTHOR' => $data['author'],
				'VERSION' => $data['version'],
				'S_NOT_APPLICABLE' => ($key == 'not_applicable_tasks') ? true : false)
			);
		}
	  }
	}
}

// Check if Safe Mode
$pcache_errors = array();
$pcache_error = false;
if($pcache->safe_mode){
  if($pcache->CheckWrite()){
    $pcache_errors[] = $user->lang['pcache_safemode_error'];
	$pcache_error = true;
  }
}
// check if Data Folder is writable
if(is_array($pcache->errors)){
	foreach($pcache->errors as $cacheerrors){
    	$pcache_errors[] = $user->lang[$cacheerrors];
		$pcache_error = true;
    }
}
foreach($pcache_errors as $error) {
	$tpl->assign_block_vars('pcache_errors', array(
		'PCACHE_ERROR' => $error,
	));
}

//output type-tabs
ksort($types);
$tpl->assign_block_vars('task_types', array(
	'TYPE' => 'home',
	'ACTIVE' => ($in->get('type', 'home') == 'home') ? 'class="active"' : '',
	'L_TYPE' => $user->lang['home'],
));
foreach($types as $type => $unused) {
	$tpl->assign_block_vars('task_types', array(
		'TYPE' => $type,
		'ACTIVE' => ($in->get('type', 'home') == $type) ? 'class="active"' : '',
		'L_TYPE' => $user->lang[$type],
	));
}
$core->create_breadcrump($user->lang[$in->get('type', 'home')]);
$tpl->assign_vars(array (
	'L_NAME' => $user->lang['name'],
	'L_DESCRIPTION' => $user->lang['description'],
	'L_AUTHOR' => $user->lang['author'],
	'L_VERSION' => $user->lang['version'],
	'L_MMODE_INFO'	=> $user->lang['mmode_info'],
	'L_APPLICABLE_INFO'	=> $user->lang['applicable_info'],
	'L_PCACHE_ERROR' => $user->lang['mmode_pcache_error'],
	'UPDATE_ALL' => ($update_all) ? $update_all : false,
	'L_UPDATE_ALL' => $user->lang['start_update'],
	'S_PCACHE_ERROR' => $pcache_error,
	'S_HOME' => ($in->get('type', 'home') == 'home') ? true : false,
	'L_CLICK_ME'	=> $user->lang['click_me'],
));
$tpl->page_header();
$tpl->page_tail();

?>