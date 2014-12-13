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
require_once($eqdkp_root_path . 'common.php');

class EQDKPBackup extends page_generic{
	public function __construct(){
		$this->user->check_auth('a_backup');
		$handler = array(
			'backup' => array('process' => 'process_backup', 'csrf'=>true),
			'restore' => array('process' => 'process_restore', 'csrf'=>true),
			'backup_delete' => array('process' => 'process_delete', 'csrf'=>true),
			'backup_download' => array('process' => 'process_download', 'csrf'=>true),
		);
		parent::__construct(false, $handler, false, null, 'user_id');
		$this->process();
	}

	// ---------------------------------------------------------
	// Display menu
	// ---------------------------------------------------------
	public function display($tab = '0'){
		// Check if the tables have a prefix. This will affect how plugin tables are backed up.
		if(! strlen($this->table_prefix) ){
			$tp_warning = $this->user->lang('backup_no_table_prefix');
		}else{
			$tp_warning = false;
		}

		//Read out all of our backups
		$path = $this->pfh->FolderPath('backup', 'eqdkp');
		$arrFiles = sdir($path);
				
		//Generate backup-array, list eqdkp-backups and .sql files
		foreach ($arrFiles as $elem){
			$strExtension = strtolower(pathinfo($elem, PATHINFO_EXTENSION));
			$matches = array();
			if (preg_match('/eqdkp-(.?)backup_([0-9]{10})_(.*)\.(sql|zip)/', $elem, $matches)){
				$backups[$elem] = $matches[2]; //Save Time
				if ($matches[1] === "f") $full[] = $elem; //Its fullbackup
			}elseif($strExtension === 'sql'){
				$backups[$elem] = filemtime($path.$elem);
			}
		}
		
		if (isset($backups) && is_array($backups)){
			//Sort the arrays the get the newest ones on top
			array_multisort($backups, SORT_DESC);
		}

		$js_output = '';
		
		//Brink the Backups to template
		if (isset($backups) && is_array($backups)){
			foreach ($backups as $key=>$elem){
				if (file_exists($this->pfh->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key).'.meta.php')){

					$result = @file_get_contents($this->pfh->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key.'.meta.php'));
					if($result !== false){
						$metadata[$key] = unserialize($result);
					}
				}
				$addition = '';

				if (!in_array($key, $full)){
					$addition = '*';
				}
				$this->tpl->assign_block_vars('backup_list', array(
					'FILE'		=> $key,
					'NAME'		=> date("Y-m-d H:i", $elem).$addition,
				));
				if (is_array($metadata[$key])){

					$js_output .= 'metadata["'.$key.'"]= "<b>'.$this->user->lang('name').'</b>: '.$key.'<br /><b>'.$this->user->lang('date').':</b> '.date("Y-m-d H:i", $elem).'<br /><b>EQdkp-'.$this->user->lang('version').':</b> '.$metadata[$key]['eqdkp_version'].'<br /><b>'.$this->user->lang('table_prefix').':</b> '.$metadata[$key]['table_prefix'].'<br /><b>'.$this->user->lang('tables').':</b> ';
					if ($metadata[$key]['uncomplete']){
						$js_output .= '<ul>';
						foreach ($metadata[$key]['tables'] as $table){
							$js_output .= '<li>'.$table.'</li>';
						}
						$js_output .= '</ul>';
					} else {
						$js_output .= $this->user->lang('cl_all');
					}
					$js_output .= '";';

				} else {
					$js_output .= 'metadata["'.$key.'"]= "<b>'.$this->user->lang('name').'</b>: '.$key.'<br />'.$this->user->lang('no_metadata').'";';
				}

			}
		}

		$arrTables = $this->db->listTables();
		foreach($arrTables as $name){
			if (!$this->db->isEQdkpTable($name)) continue;
			$tables[$name] = $name;
		}

		$this->jquery->Dialog('delete_warning', '', array('custom_js'=>"submit_form('backup_delete');", 'message'=>$this->user->lang('confirm_delete_backup')), 'confirm');
		$this->jquery->Dialog('restore_warning', '', array('custom_js'=>"submit_form('restore');", 'message'=>$this->user->lang('confirm_restore_backup'), 'height' => 300), 'confirm');
		$this->jquery->Tab_header('backup_tabs');
		$this->jquery->Tab_Select('backup_tabs', $tab);

		// Assign the rest of the variables.
		$this->tpl->assign_vars(array(
			'NO_BACKUPS'				=> (!isset($backups) || count($backups) == 0) ? true : false,
			'TABLE_PREFIX_WARNING'		=> $tp_warning,
			'TABLE_SELECT'				=> $this->jquery->MultiSelect('tables', $tables, $tables, array('width' => 300, 'height' => 300)),
			'BACKUP_UPLOAD_INFO'		=> sprintf($this->user->lang('backup_upload_path'), $this->pfh->FolderPath('backup/', 'eqdkp')),
		));

		$this->tpl->add_js("
			function submit_form(button){
				$('#mode').attr('name', button);
				$(\"#backup_form\").submit();
			}

			function restore_data(value){
				if (value){
					$('#restore_data').val(1);
				} else {
					$('#restore_data').val(0);
				}
			}

			function show_metadata(value){
				var metadata = new Array();
				".$js_output."
				$('#metadata').html(metadata[value]);
			}
		");

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('backup'),
			'template_file'		=> 'admin/manage_backup.html',
			'display'			=> true
		));
	}

	public function process_backup(){
		$arrTables = $this->in->getArray('tables', 'string');
		$strFormat = $this->in->get('method', 'zip');
		$strAction = $this->in->get('action');
		$blnCreateTable = ($strAction == 'store') ?  true : (($this->in->get('create_table', 'Y') == 'Y') ? true : false);

		$strFilename = $this->backup->createDatabaseBackup($strFormat, $blnCreateTable, $arrTables, (($strAction == 'store' || $strAction == 'both') ?  true : false));
		$blnResult = ($strFilename && strlen($strFilename)) ? true : false;
		
		//Create Log entry
		if ($strAction == 'store' || $strAction == 'both'){
			if ($blnResult){
				$log_action = array(
					'{L_TABLES}' => implode(', ', $arrTables),
				);
				$this->logs->add("action_backup_created", $log_action, $this->config->get('plus_version'), true);
				$this->core->message($this->user->lang('backup_store_success'),$this->user->lang('backup'),'green');
			
				//Move File to the correct folder
				$strFilename = $this->backup->moveBackupToBackupFolder($strFilename);
			
			} else {
				$this->core->message($this->user->lang('error'),$this->user->lang('backup'),'red');
				$this->logs->add("action_backup_created", $log_action, $this->config->get('plus_version'), false);
			}

		}
		
		//Download the File
		if ($strAction == 'download' || $strAction == 'both') {
			if ($blnResult){
				$name = pathinfo($strFilename, PATHINFO_FILENAME);
				
				switch ($strFormat){
					case 'zip':
		
						@header('Pragma: no-cache');
						@header("Content-Type: application/zip; name=\"$name.zip\"");
						@header("Content-disposition: attachment; filename=$name.zip");
						readfile($strFilename);
						if ($this->in->get('action') == 'download'){
							$this->pfh->Delete($strFilename);
						}
						exit;
						break;
		
					default:
						@header('Pragma: no-cache');
						@header("Content-Type: text/x-sql; name=\"$name.sql\"");
						@header("Content-disposition: attachment; filename=$name.sql");
						readfile($strFilename);
						if ($this->in->get('action') == 'download'){
							$this->pfh->Delete($strFilename);
						}
						exit;
				}
			}
		
		}
		
	} //close process_backup
	
	public function process_delete(){
		$file = $this->pfh->FolderPath('backup', 'eqdkp').$this->in->get('backups');
		$metafile = $this->pfh->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($this->in->get('backups'), strpos($this->in->get('backups'), '.')), "", $this->in->get('backups')).'.meta.php';
		if (file_exists($file)) {
			$this->pfh->Delete($file);
			$this->core->message($this->user->lang('backup_delete_success'),$this->user->lang('backup'),'green');
		};
		if (file_exists($metafile)){
			$this->pfh->Delete($metafile);
		}
		$this->display('1');
	}
	
	
	public function process_download(){
		$strFilename = $this->in->get('backups');
		//Sanitize Filename a bit
		$strFilename = preg_replace("/[^a-zA-Z0-9-_.]/", "", $strFilename);
		
		if ($strFilename != ""){
			$strFilename = $this->pfh->FolderPath('backup', 'eqdkp').$strFilename;
			$strFormat = pathinfo($strFilename, PATHINFO_EXTENSION);
			$name = pathinfo($strFilename, PATHINFO_FILENAME);
			
			switch ($strFormat){
				case 'zip':
			
					@header('Pragma: no-cache');
					@header("Content-Type: application/zip; name=\"$name.zip\"");
					@header("Content-disposition: attachment; filename=$name.zip");
					@set_time_limit(0);
					
					$fp = @fopen($strFilename, 'rb');
					if ($fp !== false){
						while (!feof($fp)){
							echo fread($fp, 8192);
						}
						fclose($fp);
					}
					flush();

					exit;
					break;
			
				default:
					@header('Pragma: no-cache');
					@header("Content-Type: text/x-sql; name=\"$name.sql\"");
					@header("Content-disposition: attachment; filename=$name.sql");
					
					$fp = @fopen($strFilename, 'rb');
					if ($fp !== false){
						while (!feof($fp)){
							echo fread($fp, 8192);
						}
						fclose($fp);
					}
					flush();

					exit;
			}

			exit();
			//$this->display('1');
		}
	}

	public function process_restore(){
		$strFilename = $this->in->get('backups');
		//Sanitize Filename a bit
		$strFilename = preg_replace("/[^a-zA-Z0-9-_.]/", "", $strFilename);
		$strPlainFilename = $strFilename;
		
		if ($strFilename != ""){
			$strFilename = $this->pfh->FolderPath('backup', 'eqdkp').$strFilename;
			$strFormat = pathinfo($strFilename, PATHINFO_EXTENSION);
			$this->backup->restoreDatabaseBackup($strFilename);

			//Flush cache
			$this->pdc->flush();
			
			if (preg_match('/eqdkp-(.?)backup_([0-9]{10})_(.*)\.(sql|zip)/', $strPlainFilename, $matches)){
				$strTime = $matches[2]; //Save Time
				$this->core->message(sprintf($this->user->lang('backup_restore_success'), $this->time->date("Y-m-d H:i", $strTime)),$this->user->lang('backup'),'green');
			} else {
				$this->core->message($this->user->lang('action_backup_restored'),$this->user->lang('backup'),'green');
			}
				
			//Insert Log
			$this->logs->add('action_backup_restored', array(), $this->time->date("Y-m-d H:i", $matches[1]), $strPlainFilename);
		}
		
		$this->display('1');
	}

}
registry::register('EQDKPBackup');
?>