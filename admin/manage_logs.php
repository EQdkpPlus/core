<?php
 /*
 * Project:     EQdkp RaidPlanner
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2005
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2005-2008 Simon (Wallenium) Wallmann
 * @link        http://eqdkp-plus.com
 * @package     raidplan
 * @version     $Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

// Check user permission
$user->check_auth('a_logs_view');

$order = $in->get('o', '0.0');
$red = 'RED'.str_replace('.', '', $order);
	
  $sort_order = array(
      0 => array('log_date desc', 'log_date'),
      1 => array('log_tag', 'log_tag desc'),
      2 => array('user_id', 'user_id desc'),
      3 => array('log_result', 'log_result desc'),
      4 => array('log_ipaddress', 'log_ipaddress desc'),
			5 => array('log_plugin', 'log_plugin desc'),
  );
  
  $current_order = switch_order($sort_order);

  // Obtain var settings
  $log_id = ( $in->get('logid', 0) > 0) ? intval($_REQUEST['logid']) : false;
  $action = ( $log_id ) ? 'view' : 'list';
  
  // Delete old...
	if ($in->get('reset') != '' && $user->check_auth('a_logs_del', false)){
		$ret = $logs->Truncate();	
		$logs->add( '{L_ACTION_LOGS_DELETED}', array('header' => '{L_ACTION_LOGS_DELETED}', '{L_NUMBER_OF_LOGS}' => $ret));
	}
	
	if ($in->get('del_errors') && $user->check_auth('a_logs_del', false)){
		
		$pcache->Delete($eqdkp_root_path.'data/php_error.log');
		$pcache->Delete($eqdkp_root_path.'data/sql_error.log');
	}
	
  if($in->get('dellogdays') && $user->check_auth('a_logs_del', false)){   
		$ret = $logs->Clean($in->get('dellogdays'));
		$logs->add( '{L_ACTION_OLD_LOGS_DELETED}', array('header' => '{L_ACTION_LOGS_DELETED}', '{L_CLEAR_LAST_LOGS}' => $in->get('dellogdays').' {L_DAYS}', '{L_NUMBER_OF_LOGS}' => $ret));
  }
  
  switch ( $action ){
    case 'view':
    
      // Get log info
        $tmplog = $logs->GetList($log_id);
        $log = $tmplog[$log_id];
        
        eval($log['value']);
        
        if ( !empty($log_action['header']) ){
          $log_header = $logs->lang_replace($log_action['header']);
        }

        foreach ( $log_action as $k => $v ){
          if ( $k != 'header' ){
            $tpl->assign_block_vars('log_row', array(
                    'ROW_CLASS'   => $core->switch_row_class(),
                    'KEY'         => $logs->lang_replace(stripslashes($k)).':',
                    'VALUE'       => $logs->lang_replace(stripslashes($v)))
            );
          }
        }
				
        $tpl->assign_vars(array(
            'S_LIST'          => false,

            'L_LOG_VIEWER'    => $user->lang['viewlogs_title'],
            'L_DATE'          => $user->lang['date'],
            'L_USERNAME'      => $user->lang['username'],
            'L_IP_ADDRESS'    => $user->lang['ip_address'],
            'L_SESSION_ID'    => $user->lang['session_id'],
						'L_PLUGIN'			=> $user->lang['pi_title'],
						'L_BACK'			=> $user->lang['back'],
						
						'LOG_PLUGIN'				=> ($user->lang[$log['plugin']]) ? $user->lang[$log['plugin']] : (($log['plugin'] != 'core') ? ucfirst($log['plugin']) : ''),
            'LOG_DATE'        => ( !empty($log['date']) ) ? date($user->style['date_time'], $log['date']) : '&nbsp;',
            'LOG_USERNAME'    => ( !empty($log['user']) ) ? $log['user'] : '&nbsp;',
            'LOG_IP_ADDRESS'  => $log['ip'],
            'LOG_SESSION_ID'  => $log['sid'],
            'LOG_ACTION'      => ( !empty($log_header) ) ? $log_header : '&nbsp;')
        );
        
      break;
    case 'list':

			if ($in->get('plugin') != ""){
				$logs->ChangePlugin($in->get('plugin'));
				$show_plugin = true;
			}
      $lgtmarray = $logs->GetList('',$show_plugin);
      if(is_array($lgtmarray)){
        foreach($lgtmarray as $row){
    		  $tpl->assign_block_vars('logs_row', array(
    		          'ROW_CLASS' 			=> $core->switch_row_class(),
    		          'U_VIEW_LOG'      => 'manage_logs.php?logid='.$row['id'],
									'U_VIEW_PLUGIN'      => 'manage_logs.php?plugin='.$row['plugin'],
    				      'DATE'            => ( !empty($row['date']) ) ? date($user->style['date_time'], $row['date']) : '&nbsp;',
    				      'RESULT'          => $row['result'],
    				      'USER'            => $row['user'],
    				      'TAG'             => (($row['flag'] == 1)? '<img src="'.$eqdkp_root_path.'images/admin/updates.png" title="'.$user->lang['admin_action'].'"> ': '').$row['tag'],
									'IP'							=> $row['ip'],
									'PLUGIN'					=>  ($user->lang[$row['plugin']]) ? $user->lang[$row['plugin']] : (($row['plugin'] != 'core') ? ucfirst($row['plugin']) : ''),
    				      'C_RESULT'        => ($row['result'] == $user->lang['success']) ? 'positive' : 'negative',
            ));
    		  }
    		}
				
				$plugin_query = $db->query("SELECT log_plugin FROM __logs GROUP BY log_plugin");
				$plugin_list[''] = '';
				while ($row = $db->fetch_record($plugin_query)){
					if (in_array($row['log_plugin'], $logs->plugins)){
						$name = ($user->lang[$row['log_plugin']]) ? $user->lang[$row['log_plugin']] : ucfirst($row['log_plugin']);
						$plugin_list[$row['log_plugin']] = $name;
					}
				}
        $start = $in->get('start', 0);
        $jquery->Dialog('delete_warning', '', array('url'=>'manage_logs.php?reset=true', 'message'=>$user->lang['confirm_delete_logs']), 'confirm');
				$jquery->Tab_header('log_tabs', true);
				
				$error_type_array = array(
					'warning'	=> 'WARNING',
					'fatal'		=> 'FATAL ERROR',
					'parse'		=> 'PARSING ERROR',
					'compile'	=> 'COMPILE ERROR',
					'error'		=> 'ERROR',
				);
				
				$time_array = $error_array = $type_array = array();
				//PHP-Errors
				$php_errors = $pdl->get_file_log('php_error');
				if ($php_errors['entries']){
					foreach ($php_errors['entries'] as $key=> $value) {
						if ($in->get('error') != '' && 'php' != $in->get('error')){
								break;
						};
						
						
						if ($in->get('type') != '' && (strpos($php_errors['entries'][$key+1], $error_type_array[$in->get('type')]) === false || strpos($php_errors['entries'][$key+1], $error_type_array[$in->get('type')]) > 0)){
										continue;
						};
						if (preg_match('/([0-9][0-9]\.[01][0-9]\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}\s)/', $value)){
							$error_array[] = $php_errors['entries'][$key+1];
							$type_array[] = 'php';
							$time_array[] = strtotime($value);
						}
					}
				}
			
				//MySQL
				$sql_errors = $pdl->get_file_log('sql_error');
				if ($sql_errors['entries']){
					foreach ($sql_errors['entries'] as $key=>$value){
						if ($in->get('error') != '' && 'db' != $in->get('error')){
							break;
						};
						
							if (preg_match('/([0-9][0-9]\.[01][0-9]\.[0-9]{4}\s[0-9]{2}\:[0-9]{2}\:[0-9]{2}\s)/', $value)){
								if ($in->get('type') != '' && strpos($sql_errors['entries'][$key+1], $error_type_array[$in->get('type')]) === false){
										continue;
								};
							
								$error_array[] = $sql_errors['entries'][$key+1];
								$type_array[] = 'db';
								$time_array[] = strtotime($value);
							}				
					}
				}
				
				array_multisort($time_array, (($in->get('o', '0.0') == '0.0') ? SORT_DESC : SORT_ASC), SORT_NUMERIC, $error_array, SORT_DESC, SORT_NUMERIC, $type_array);
				
				$total_errors = count($time_array);
				
				$time_array = array_slice($time_array, $start, 100);
				$error_array = array_slice($error_array, $start, 100);
				$type_array = array_slice($type_array, $start, 100);
				
				foreach ($time_array as $key => $value){
										
					$tpl->assign_block_vars('error_row', array(
						'DATE'	=> date($user->style['date_time'], $value),
						'MESSAGE'	=> $error_array[$key],
						'TYPE'=> $type_array[$key],
						'ROW_CLASS' => $core->switch_row_class(),	
					));
					
				}
				
				$error_list = array(
					''	=> '',
					'php'	=> 'PHP',
					'db'	=> 'DB'
				);
				$type_list = array(
					''	=> '',
					'warning'	=> 'Warning',
					'error'		=> 'Error',
					'fatal'	=> 'Fatal Error',
					'parse'	=> 'Parse Error',
					'compile'	=> 'Compile Error',
				);

        $tpl->assign_vars(array(
                  'S_LIST'        => true,
                  'F_DELLOG'      => 'manage_logs.php'.$SID,
                  'RPDELLOGDAYS'  => '30',
									
                  'L_ACTIONS'      => $user->lang['actions'],
									'L_ERRORS'      => $user->lang['error'],
                  'L_USER'        => $user->lang['user'],
                  'L_DATE'        => $user->lang['date'],
                  'L_RESULT'      => $user->lang['result'],
                  'L_TAG'         => $user->lang['type'],
                  'L_DELETE'      => $user->lang['delete'],
                  'L_DEL_ALL_LOGS'=> $user->lang['clear_all_logs'],
									'L_DEL_LAST_LOGS'=> $user->lang['clear_last_logs'],
                  'L_DAYS'        => $user->lang['days'],
									'L_TYPE'        => $user->lang['type'],
            			'L_VIEW_ACTION' => $user->lang['view_action'],
            			'L_USER'        => $user->lang['user'],
            			'L_IP_ADDRESS'  => $user->lang['ip_address'],
									'L_VIEW'        => $user->lang['view'],
									'L_DELETE_LOGS' => $user->lang['clear_logs'],
									'L_PLUGIN'			=> $user->lang['pi_title'],
									'L_FILTER'			=> $user->lang['filter_plugins'],
									'L_FILTER_TYPE'	=> $user->lang['filter_type'],
									'L_MESSAGE'			=> $user->lang['description'],
									'L_HEADLINE'		=> $user->lang['viewlogs_title'],
									'FILTER_SELECT'	=> $html->DropDown('plugin', $plugin_list, $in->get('plugin'), '', 'onchange="window.location=\'manage_logs.php?plugin=\'+document.post.plugin.value"'),
                  'ERROR_FILTER_SELECT'	=> $html->DropDown('error_dd', $error_list, $in->get('error'), '', 'onchange="window.location=\'manage_logs.php?error=\'+document.post2.error_dd.value"'),
									'ERROR_TYPE_SELECT'	=> $html->DropDown('error_type_dd', $type_list, $in->get('type'), '', 'onchange="window.location=\'manage_logs.php?type=\'+document.post2.error_type_dd.value"'),
									
                  $red 								=> '_red',
									'O_0'	=> $current_order['uri'][0],
									'O_1'	=> $current_order['uri'][1],	
									'O_2'	=> $current_order['uri'][2],	
									'O_3'	=> $current_order['uri'][3],	
									'O_4'	=> $current_order['uri'][4],	
									'O_5'	=> $current_order['uri'][5],
									'L_SORT_DESC'	=> $user->lang['sort_desc'],
									'L_SORT_ASC'	=> $user->lang['sort_asc'],
									         
                  'U_LOGS'        => 'manage_logs.php'.$SID.'&amp;start='.$start.'&amp;'.(($in->get('plugin') != "") ? 'plugin='.$in->get('plugin').'&amp;' : ''),

                  
                  'VL_FOOTCOUNT'  => sprintf($user->lang['viewlogs_footcount'], count($lgtmarray), 100),
									'EL_FOOTCOUNT'  => sprintf($user->lang['viewlogs_footcount'], $total_errors, 100),
                  'VL_PAGINATION' => generate_pagination('manage_logs.php'.$SID.'&amp;o='.$current_order['uri']['current'], $total_logs, '100', $start),
									'EL_PAGINATION'	=> generate_pagination('manage_logs.php'.$SID.'&amp;error='.sanitize($in->get('error')).'&amp;type='.sanitize($in->get('type')), $total_errors, '100', $start)
    		));
      break;
}  
    $core->set_vars(array(
	    'page_title'             => $user->lang['viewlogs_title'],
			'template_file'          => 'admin/manage_logs.html',
			'display'                => true)
    );  
?>
