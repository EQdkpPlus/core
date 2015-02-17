<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Logs extends page_generic {

	public function __construct(){
		$this->user->check_auth('a_logs_view');

		$handler = array(
			'reset'			=> array('process' => 'reset_logs',			'check' => 'a_logs_del', 'csrf'=>true),
			'del_errors'	=> array('process' => 'delete_errors',		'check' => 'a_logs_del', 'csrf'=>true),
			'dellogdays'	=> array('process' => 'delete_log_days',	'check' => 'a_logs_del', 'csrf'=>true)
		);
		parent::__construct(false, $handler, array(), null, 'selected_ids[]', 'logid');
		if($this->url_id > 0) $this->view_log();
		$this->process();
	}
	
	public function delete(){
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			$ret = $this->pdh->put('logs', 'delete_ids', array($this->in->getArray('selected_ids','int')));
			$this->pdh->process_hook_queue();
			$this->logs->add( 'action_logs_deleted', array('{L_NUMBER_OF_LOGS}' => count($this->in->getArray('selected_ids', 'int'))), '');
			$this->display();
		}	
	}

	public function reset_logs(){
		$ret = $this->pdh->put('logs', 'truncate_log', array());
		$this->pdh->process_hook_queue();
		$this->logs->add( 'action_logs_deleted', array('{L_NUMBER_OF_LOGS}' => $ret), '');
		$this->display();
	}

	public function delete_errors(){
		$arrLogFiles = $this->pdl->get_logfiles();
		foreach($arrLogFiles as $logfile){
			$this->pdl->delete_logfile(str_replace(".log", "", $logfile));
		}
		$this->display();
	}

	public function delete_log_days(){
		$ret = $this->pdh->put('logs', 'clean_log', array($this->in->get('dellogdays')));
		$this->pdh->process_hook_queue();
		$this->logs->add( 'action_old_logs_deleted', array('{L_CLEAR_LAST_LOGS}' => $this->in->get('dellogdays').' {L_DAYS}', '{L_NUMBER_OF_LOGS}' => $ret), '');
		$this->display();
	}

	public function view_log() {
		$log_value = unserialize($this->pdh->get('logs', 'value', array($this->url_id)));
		
		$blnCompare = false;
		if(is_array($log_value)) {
			foreach ($log_value as $k => $v){
				if($k != 'header'){
					//Enable Compare view
					if (is_array($v)){
						$blnCompare = true;
						
						if ($v['flag'] == 1){
							require_once($this->root_path.'libraries/diff/diff.php');
							require_once($this->root_path.'libraries/diff/engine.php');
							require_once($this->root_path.'libraries/diff/renderer.php');
							$diff = new diff(xhtml_entity_decode($this->logs->lang_replace($v['old'])), xhtml_entity_decode($this->logs->lang_replace($v['new'])), true);
							$renderer = new diff_renderer_inline();
							
							$new = $content = $renderer->get_diff_content($diff);
						} else {
							$new = nl2br($this->logs->lang_replace($v['new']));
						}
						
						$this->tpl->assign_block_vars('log_compare_row', array(
								'KEY'			=> $this->logs->lang_replace(stripslashes($k)).':',
								'OLD'			=> nl2br($this->logs->lang_replace($v['old'])),
								'NEW'			=> $new,
								'FLAG'			=> $v['flag'],
						));
					} else {				
						$this->tpl->assign_block_vars('log_row', array(
							'KEY'			=> $this->logs->lang_replace(stripslashes($k)).':',
							'VALUE'			=> $this->logs->lang_replace(stripslashes($v)))
						);
					}
				}
			}
		}
		$plugin = $this->pdh->get('logs', 'plugin', array($this->url_id));
		$this->tpl->assign_vars(array(
			'LOG_PLUGIN'		=> ($plugin != 'core') ? (($this->user->lang($plugin)) ? $this->user->lang($plugin) : ucfirst($plugin)) : '',
			'LOG_DATE'			=> $this->pdh->geth('logs', 'date', array($this->url_id, true)),
			'LOG_USERNAME'		=> $this->pdh->geth('logs', 'user', array($this->url_id)),
			'LOG_IP_ADDRESS'	=> $this->pdh->geth('logs', 'ipaddress', array($this->url_id)),
			'LOG_SESSION_ID'	=> $this->pdh->geth('logs', 'sid', array($this->url_id)),
			'LOG_ACTION'		=> $this->pdh->geth('logs', 'tag', array($this->url_id)),
			'LOG_RECORD'		=> $this->pdh->geth('logs', 'record', array($this->url_id)),
			'LOG_RECORD_ID'		=> $this->pdh->geth('logs', 'recordid', array($this->url_id)),
			'S_COMPARE_VIEW'	=> $blnCompare,
			'S_MORE_INFOS'		=> count($log_value),
		));
		$this->tpl->add_js('
			$("#back2view").click(function(){
				window.location="manage_logs.php'.$this->SID.'";
			});', 'docready');
		$this->tpl->css_file($this->root_path.'libraries/diff/diff.css');
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('viewlogs_title'),
			'template_file'		=> 'admin/manage_logs_view.html',
			'display'			=> true)
		);
	}

	public function display(){
	
		$plugin_list['']	= '';
		$arrLogins = $this->pdh->get('logs', 'plugins');
		if (is_array($arrLogins)){
			foreach($arrLogins as $pluginname){
				if (in_array($pluginname, $this->logs->plugins)){
					$name = ($this->user->lang($pluginname)) ? $this->user->lang($pluginname) : ucfirst($pluginname);
					$plugin_list[$pluginname] = $name;
				}
			}
		}
		
		$user_list[-1]	= '';
		$arrUsers = $this->pdh->get('logs', 'grouped_users', array());
		foreach($arrUsers as $user_id){
			$user_list[$user_id] = $this->pdh->get('user', 'name', array($user_id));
		}
		
		$type_list['']	= '';
		$arrTags = $this->pdh->get('logs', 'grouped_tags', array());
		foreach($arrTags as $tag){
			$type_list[$tag] = $this->user->lang($tag, true, false);
		}
		natcasesort($type_list);
		
		$result_list = array(
			'-1' => '',
			'0' => $this->user->lang('error'),
			'1' => $this->user->lang('success'),
		);
	
		
		//Prepare Filter
		$blnFilter = false;
		$strFilterSuffix = "";
		if ($this->in->exists('filter')){
			//Change Filter options here
			$plugin = ($this->in->get('filter_plugin') != "") ? $this->in->get('filter_plugin') : false;
			$result = ($this->in->exists('filter_result') && $this->in->get('filter_result', 0) >= 0) ? $this->in->get('filter_result', 0) : false;
			$ip		= ($this->in->get('filter_ip') != "") ? $this->in->get('filter_ip') : false;
			$sid	= ($this->in->get('filter_sid') != "") ? $this->in->get('filter_sid') : false;
			$tag	= ($this->in->get('filter_type') != "") ? $this->in->get('filter_type') : false;
			$user_id= ($this->in->exists('filter_user') && $this->in->get('filter_user', 0) >= 0) ? $this->in->get('filter_user', 0) : false;
			$value	= ($this->in->get('filter_value') != "") ? $this->in->get('filter_value') : false;
			$recordid= ($this->in->get('filter_recordid') != "") ? $this->in->get('filter_recordid') : false;
			$record	= ($this->in->get('filter_record') != "") ? $this->in->get('filter_record') : false;
			$date_from = ($this->in->get('filter_date_from') != "") ? $this->time->fromformat($this->in->get('filter_date_from','1.1.1970').' 00:00', 1) : false;
			$date_to = ($this->in->get('filter_date_to') != "") ? $this->time->fromformat($this->in->get('filter_date_to','1.1.1970').' 00:00', 1) : false;
			if (!$date_from) {$date_from = ($this->in->get('f_date_from', 0)) ? $this->in->get('f_date_from', 0) : false;}
			if (!$date_to) {$date_to = ($this->in->get('f_date_to', 0)) ? $this->in->get('f_date_to', 0) : false;}

			//Do we have filters?
			if ($plugin !== false || $result !== false || $ip !== false || $sid !== false || $tag !== false || $user_id !== false || $value !== false || $date_from !== false || $date_to !== false || $record !== false || $recordid !== false){
				$blnFilter = true;
				//Get filtered ID list
				$view_list = $this->pdh->get('logs', 'filtered_id_list', array($plugin, $result, $ip, $sid, $tag, $user_id, $value, $date_from, $date_to, $recordid, $record));
				
				//Build GET-Params for Sorting and Pagination
				$strFilterSuffix .= "&amp;filter=1";
				if ($plugin !== false) $strFilterSuffix .= "&amp;filter_plugin=".$plugin;
				if ($result !== false) $strFilterSuffix .= "&amp;filter_result=".$result;
				if ($ip !== false) $strFilterSuffix .= "&amp;filter_ip=".$ip;
				if ($sid !== false) $strFilterSuffix .= "&amp;filter_sid=".$sid;
				if ($tag !== false) $strFilterSuffix .= "&amp;filter_type=".$tag;
				if ($user_id !== false) $strFilterSuffix .= "&amp;filter_user=".$user_id;
				if ($value !== false) $strFilterSuffix .= "&amp;filter_value=".$value;
				if ($date_from !== false) $strFilterSuffix .= "&amp;f_date_from=".$date_from;
				if ($date_to !== false) $strFilterSuffix .= "&amp;f_date_to=".$date_to;
				if ($recordid !== false) $strFilterSuffix .= "&amp;filter_recordid=".$recordid;
				if ($record !== false) $strFilterSuffix .= "&amp;filter_record=".$record;

				$_date_from = ($date_from !== false) ? $this->time->user_date($date_from , false, false, false, function_exists('date_create_from_format')) : '';
				$_date_to	= ($date_to !== false) ? $this->time->user_date($date_to , false, false, false, function_exists('date_create_from_format')) : '';
				//Template Vars
				$this->tpl->assign_vars(array(
					'FILTER_PLUGINS' => new hdropdown('filter_plugin', array('options' => $plugin_list, 'value' => (($plugin !== false) ? $plugin : ''))),
					'FILTER_USER'	 => new hdropdown('filter_user', array('options' => $user_list, 'value' => (($user_id !== false) ? $user_id : ''))),
					'FILTER_TYPE'	 => new hdropdown('filter_type', array('options' => $type_list, 'value' => (($tag !== false) ? $tag : ''))),
					'FILTER_RESULT'  => new hdropdown('filter_result', array('options' => $result_list, 'value' => (($result !== false) ? $result : -1))),
					'FILTER_IP'		=> $ip,
					'FILTER_SID'	=> $sid,
					'FILTER_VALUE'	=> $value,
					'FILTER_RECORD' => $record,
					'FILTER_RECORDID' => $recordid,
					'FILTER_DATE_FROM'		=> $this->jquery->Calendar('filter_date_from', $_date_from, '', array('change_year' => true,'change_month' => true,'other_months' => true, 'number_months' => 3, 'onclose' => ' $( "#filter_date_to" ).datepicker( "option", "minDate", selectedDate );')),
					'FILTER_DATE_TO'		=> $this->jquery->Calendar('filter_date_to', $_date_to, '', array('change_year' => true,'change_month' => true,'other_months' => true, 'number_months' => 3,  'onclose' => ' $( "#filter_date_from" ).datepicker( "option", "maxDate", selectedDate );')),
				));
			}
			
		}
		
		if (!$blnFilter){
			//Common Filter Output
			$this->tpl->assign_vars(array(
				'FILTER_PLUGINS' => new hdropdown('filter_plugin', array('options' => $plugin_list)),
				'FILTER_USER'	 => new hdropdown('filter_user', array('options' => $user_list)),
				'FILTER_TYPE'	 => new hdropdown('filter_type', array('options' => $type_list)),
				'FILTER_RESULT'  => new hdropdown('filter_result', array('options' => $result_list, 'value' => -1)),
				'FILTER_DATE_FROM'		=> $this->jquery->Calendar('filter_date_from', '', '', array('change_year' => true,'change_month' => true, 'other_months' => true, 'number_months' => 3, 'onclose' => ' $( "#cal_filter_date_to" ).datepicker( "option", "minDate", selectedDate );')),
				'FILTER_DATE_TO'		=> $this->jquery->Calendar('filter_date_to', '', '', array('change_year' => true,'change_month' => true,'other_months' => true, 'number_months' => 3,  'onclose' => ' $( "#cal_filter_date_from" ).datepicker( "option", "maxDate", selectedDate );')),		
			));
			$view_list			= $this->pdh->get('logs', 'id_list', array());
		}
	
		//
		//ERRORS ================================================================================
		//

		//Search Fatal Error ID
		if($this->in->exists('search_fatal_id')){
			$arrMatch = $this->pdl->search_fatal_error_id($this->in->get('fatal_error_id'));
			if($arrMatch){
				$this->tpl->assign_vars(array(
					'S_FATAL_ERROR' => true,
					'FATAL_ERROR_ID' => sanitize($this->in->get('fatal_error_id')),
					'FATAL_ERROR_TYPE' => str_replace(".log", "", $arrMatch['file']),
					'FATAL_ERROR_MSG'  => preg_replace("/\t(.*?)\:/s", "<br /><b style='font-weight:bold;'>$1:</b>", nl2br($arrMatch['error'])),
				));
			}
		}
		
		$start = $this->in->get('start', 0);
		
		$arrLogFiles = $this->pdl->get_logfiles();
		foreach($arrLogFiles as $logfile){
			
			$arrErrors = $this->pdl->get_file_log(str_replace(".log", "", $logfile), 50, $start);
			
			$this->tpl->assign_block_vars('errorlogs', array(
					'TYPE' 			=> str_replace(".log", "", $logfile),
					'PAGINATION'	=> generate_pagination('manage_logs.php'.$this->SID.'&amp;error='.sanitize($this->in->get('error')).'&amp;type='.sanitize($this->in->get('type')), $arrErrors['count'], 50, $start),
					'FOOTCOUNT'		=> sprintf($this->user->lang('viewlogs_footcount'), $arrErrors['count'], 50),
			));
			
			foreach($arrErrors['entries'] as $key => $entry) {
				if(($key % 2) === 1){
					$this->tpl->assign_block_vars('errorlogs.error_row', array(
						'DATE'			=> $this->time->user_date($entry, true),
						'MESSAGE'		=> nl2br($arrErrors['entries'][$key-1]),
					));
				}
			}

		}

		$actionlog_count	= count($view_list);
		$hptt_psettings		= $this->pdh->get_page_settings('admin_manage_logs', 'hptt_managelogs_actions');
		$hptt				= $this->get_hptt($hptt_psettings, $view_list, $view_list, array('%link_url%' => 'manage_logs.php', '%link_url_suffix%' => '', md5($strFilterSuffix)));
		$footer_text		= sprintf($this->user->lang('viewlogs_footcount'), $actionlog_count, 100);
		$page_suffix		= '&amp;start='.$this->in->get('start', 0).$strFilterSuffix;
		$sort_suffix		= $this->SID.'&amp;sort='.$this->in->get('sort').$strFilterSuffix;
		$logs_list = $hptt->get_html_table($this->in->get('sort',''), $page_suffix, $this->in->get('start', 0), 100, $footer_text);
		
		$this->jquery->Dialog('delete_all_warning', '', array('url'=>'manage_logs.php'.$this->SID.'&reset=true&link_hash='.$this->CSRFGetToken('reset'), 'message'=>$this->user->lang('confirm_delete_logs')), 'confirm');
		$this->confirm_delete($this->user->lang('confirm_delete_partial_logs'));
		$this->jquery->Tab_header('log_tabs', true);
		$this->jquery->Tab_header('errorlog_tabs', true);
		$this->jquery->Collapse('#toggleFilter', true);
		$this->tpl->assign_vars(array(
			'LOGS_LIST'				=> $logs_list,
			'LOGS_PAGINATION'		=> generate_pagination('manage_logs.php'.$sort_suffix.$strFilterSuffix, $actionlog_count, 100, $this->in->get('start', 0)),
			'HPTT_LOGS_COUNT'		=> $hptt->get_column_count(),
		));
		
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('viewlogs_title'),
			'template_file'		=> 'admin/manage_logs.html',
			'display'			=> true)
		);
	}
}
registry::register('Manage_Logs');
?>