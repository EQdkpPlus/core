<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
require_once($eqdkp_root_path . 'common.php');

class EQDKPBackup extends page_generic{
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pfh', 'jquery', 'core', 'config', 'db', 'time', 'backup'=>'backup', 'pdc');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_backup');
		$handler = array(
			'backup' => array('process' => 'do_backup', 'csrf'=>true),
			'restore' => array('process' => 'restore_backup', 'csrf'=>true),
			'backup_delete' => array('process' => 'delete_backup', 'csrf'=>true),
			'backup_download' => array('process' => 'download_backup', 'csrf'=>true),
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
		$files = array();
		$path = $this->pfh->FolderPath('backup', 'eqdkp');
		if($dir=opendir($path)){
			while($file=readdir($dir)){
				if (!is_dir($file) && $file != "." && $file != ".." && $file != "index.html" && $file != ".htaccess"){
					$files[$file]=$file;
				}
			}
			closedir($dir);
		}
		//Generate backup-array, only list eqdkp-backups
		foreach ($files as $elem){
			if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $elem, $matches)){
				$backups[$elem] = $matches[1];
			}
			if (preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $elem, $matches)){
				$backups[$elem] = $matches[1];
				$full[] = $elem;

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
					$js_output .= 'metadata["'.$key.'"]= "'.$this->user->lang('no_metadata').'";';
				}

			}
		}

		$arrTables = $this->db->get_tables(true);
		foreach($arrTables as $name){
			$tables[$name] = $name;
		}

		$this->jquery->Dialog('delete_warning', '', array('custom_js'=>"submit_form('backup_delete');", 'message'=>$this->user->lang('confirm_delete_backup')), 'confirm');
		$this->jquery->Dialog('restore_warning', '', array('custom_js'=>"submit_form('restore');", 'message'=>$this->user->lang('confirm_restore_backup')), 'confirm');
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
			function checkForm(value){
				//data-Folder
				if (value){
				$('#action_download, #method_zip, #create_table_n').attr('checked', true);
				$('#action_download, #action_store, #action_both, #create_table_y, #create_table_n, #method_zip, #method_text').attr('disabled', true);

				} else {
				//DB
				$('#action_store, #create_table_y').attr('checked', true);
				$('#action_download, #action_store, #action_both, #create_table_y, #create_table_n, #method_zip, #method_text').attr('disabled', false);

				}
			}

			function submit_form(button){
				$('#mode').attr('name', button);
				$(\"form\").submit();
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

	// ---------------------------------------------------------
	// Restore a Backup
	// ---------------------------------------------------------
	public function restore_backup(){
		$file_name = $this->in->get('backups');
		$file = $this->pfh->FolderPath('backup', 'eqdkp').$file_name;

		if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $file_name, $matches) || preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $file_name, $matches)){

			switch ($matches[3])
			{
				case 'sql':
					$fp = fopen($file, 'rb');
				break;

				case 'zip':
					//Copy the archive to the tmp-folder
					$new_file = $this->pfh->FolderPath('backup/tmp', 'eqdkp').$file_name;
					$this->pfh->copy($file,  $new_file);
					$base = pathinfo($file_name);
					$archive = registry::register('zip', array($new_file));
					$archive->extract($this->pfh->FolderPath('backup/tmp', 'eqdkp'), array($base['filename'].'.sql'));

					//Now extract the data-Folder and replace existing files
					if ($this->in->get('restore_data', 0) == 1){
						$archive->extract($this->root_path);
						$this->pfh->Delete($this->root_path.$base['filename'].'.sql');
					}

					$this->pfh->Delete($new_file);
					$backup_file = $this->pfh->FolderPath('backup/tmp', 'eqdkp').$base['filename'].'.sql';

					$fp = fopen($backup_file, 'rb');
				break;
			}
			$read = 'fread';
			$seek = 'fseek';
			$eof = 'feof';
			$close = 'fclose';
			$fgetd = 'fgetd';
			@set_time_limit(0);
			while (($sql = $this->$fgetd($fp, ";\n", $read, $seek, $eof)) !== false){
				if (strpos($sql, "--") === false && $sql != ""){
					$this->db->query($sql);
				}
			}
			$this->core->message(sprintf($this->user->lang('backup_restore_success'), $this->time->date("Y-m-d H:i", $matches[1])),$this->user->lang('backup'),'green');
			//Flush cache
			$this->pdc->flush();
		}
		if (strlen($backup_file)){
			$this->pfh->Delete($backup_file);
		}
		$this->display('1');
	}

	// ---------------------------------------------------------
	// Delete a stored backup
	// ---------------------------------------------------------
	public function delete_backup(){
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

	// ---------------------------------------------------------
	// Download a stored backup
	// ---------------------------------------------------------
	public function download_backup(){
		if ($this->in->get('backups') != ""){

			$file_name = $this->pfh->FolderPath('backup', 'eqdkp').$this->in->get('backups');

			if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $this->in->get('backups'), $matches) || preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $this->in->get('backups'), $matches) ){

				$name = 'eqdkp-backup_' . $this->time->date('Y-m-d_Hi', $matches[1]).".".$matches[3];

				switch ($matches[3]){
					case 'sql':
						$mimetype = 'text/x-sql';
					break;
					case 'zip':
						$mimetype = 'application/zip';
					break;
				}

				header('Pragma: no-cache');
				header("Content-Type: $mimetype; name=\"$name\"");
				header("Content-disposition: attachment; filename=$name");

				@set_time_limit(0);

				$fp = @fopen($file_name, 'rb');

				if ($fp !== false){
					while (!feof($fp)){
						echo fread($fp, 8192);
					}
					fclose($fp);
				}
				flush();
			}
			exit();
			//$this->display('1');
		} else {
		}
	}

	// ---------------------------------------------------------
	// Main Backup Script
	// ---------------------------------------------------------
	public function do_backup(){
				if ($this->in->get('system') == 'data'){
				//Download data-Folder
					$file = $this->pfh->FolderPath('backup/tmp', 'eqdkp').md5(rand()).'.zip';
					$archive = registry::register('zip', array($file));
					$archive->add($this->pfh->get_cachefolder(), $this->pfh->get_cachefolder());
					$archive->delete('cache/');
					$archive->delete('eqdkp/armory/');
					$archive->delete('eqdkp/backup/tmp/');
					$archive->delete('tmp/');
					$archive->delete('live_update/');
					$archive->delete('repository/');
					$archive->create();

					if (file_exists($file)){
							$name = 'data_'.date('Y-m-d_Hi', time()).'.zip';
							@header('Pragma: no-cache');
							@header("Content-Type: application/x-gzip; name=\"$name\"");
							@header("Content-disposition: attachment; filename=$name");
							readfile($file);
							$this->pfh->Delete($file);
							exit;
					}

				} else {
				//SQL-Backup
					$in_tables = $this->in->getArray('tables', 'string');
					$format = $this->in->get('method', 'zip');
					$action = $this->in->get('action');
					$create_table = ($action == 'store') ?  'Y' : $this->in->get('create_table');

					$result = $this->backup->create($action, $format, $create_table, $in_tables);

					if ($this->in->get('action') == 'store' || $this->in->get('action') == 'both'){
						if ($result){
							$this->core->message($this->user->lang('backup_store_success'),$this->user->lang('backup'),'green');
						} else {
							$this->core->message($this->user->lang('error'),$this->user->lang('backup'),'red');
						}
					}

					if ($this->in->get('action') == 'download' || $this->in->get('action') == 'both') {
						if ($result){
							$name = 'eqdkp-backup_'  . $this->time->date('Y-m-d_Hi');
							switch ($format){
									case 'zip':

										@header('Pragma: no-cache');
										@header("Content-Type: application/zip; name=\"$name.zip\"");
										@header("Content-disposition: attachment; filename=$name.zip");
										readfile($this->pfh->FolderPath('backup', 'eqdkp').$result.'.zip');
										if ($this->in->get('action') == 'download'){
											$this->pfh->Delete($this->pfh->FolderPath('backup', 'eqdkp').$result.'.zip');
										}
										exit;
									break;

									default:
										@header('Pragma: no-cache');
										@header("Content-Type: text/x-sql; name=\"$name.sql\"");
										@header("Content-disposition: attachment; filename=$name.sql");
										echo $result;
										exit;
							}
					}

				}
			}
			$this->display();
	} //close function

	// modified from PHP.net
	public function fgetd(&$fp, $delim, $read, $seek, $eof, $buffer = 8192){
		$record = '';
		$delim_len = strlen($delim);

		while (!$eof($fp)){
			$pos = strpos($record, $delim);
			if ($pos === false){
				$record .= $read($fp, $buffer);
				if ($eof($fp) && ($pos = strpos($record, $delim)) !== false){
					$seek($fp, $pos + $delim_len - strlen($record), SEEK_CUR);
					return trim(substr($record, 0, $pos));
				}
			}else{
				$seek($fp, $pos + $delim_len - strlen($record), SEEK_CUR);
				return trim(substr($record, 0, $pos));
			}
		}
		return false;
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_EQDKPBackup', EQDKPBackup::__shortcuts());
registry::register('EQDKPBackup');
?>