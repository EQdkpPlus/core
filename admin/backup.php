<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * manage_members.php
 * Began: Sun January 5 2003
 * 
 * $Id$
 * 
 ******************************/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
require_once($eqdkp_root_path . 'common.php');

class EQDKPBackup extends EQdkp_Admin
{
    function eqdkpbackup()
    {
        parent::eqdkp_admin();
        
        $this->assoc_buttons(array(
            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_backup'
            ),
            'backup' => array(
                'name'    => 'backup',
                'process' => 'do_backup',
                'check'   => 'a_backup'
            ),
						'restore' => array(
                'name'    => 'restore',
                'process' => 'restore_backup',
                'check'   => 'a_backup'
            ),
						'backup_delete' => array(
                'name'    => 'backup_delete',
                'process' => 'delete_backup',
                'check'   => 'a_backup'
            ),
						'backup_download' => array(
                'name'    => 'backup_download',
                'process' => 'download_backup',
                'check'   => 'a_backup'
            ),
			
        ));
    }
    
    function error_check()
    {
        return $this->fv->is_error();
    }
    
    // ---------------------------------------------------------
    // Display menu
    // ---------------------------------------------------------
    function display_form($tab = '0')
    {
        global $core, $user, $tpl, $table_prefix, $pcache, $SID, $jquery, $backup, $pcache;
        

        // Check if the tables have a prefix. This will affect how plugin tables are backed up.
        if( empty($table_prefix) )
        {
            $tp_warning = $user->lang['backup_no_table_prefix'];
        }
        else
        {
            $tp_warning = false;
        }
		
		
		
		//Read out all of our backups
		$path = $pcache->FolderPath('eqdkp/backup');
			if($dir=opendir($path))
			{
			 while($file=readdir($dir))
			 {
			  if (!is_dir($file) && $file != "." && $file != ".." && $file != "index.html" && $file != ".htaccess")
			  {
			   $files[$file]=$file;
			  }
			 }
			closedir($dir);
			}
		//Generate backup-array, only list eqdkp-backups
		if (is_array($files)){
			foreach ($files as $elem){
				if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $elem, $matches)){
					$backups[$elem] = $matches[1];	
				}
				if (preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $elem, $matches)){
					$backups[$elem] = $matches[1];
					$full[] = $elem;

				}
				
			}
			if (is_array($backups)){	
				//Sort the arrays the get the newest ones on top
				array_multisort($backups, SORT_DESC);
			}
		}
		
		$js_output = '';
		//Brink the Backups to template
		if (is_array($backups)){
			foreach ($backups as $key=>$elem){
				if (file_exists($pcache->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key).'.meta.php')){

					$result = @file_get_contents($pcache->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key.'.meta.php'));
					if($result !== false){
					  $metadata[$key] = unserialize($result);
					} 
				}
				$addition = '';
				
				if (!in_array($key, $full)){
					$addition = '*';
				}
				$tpl->assign_block_vars('backup_list', array(
					'FILE'    => $key,
					'NAME'	  => date("Y-m-d H:i", $elem).$addition,
				));
				if (is_array($metadata[$key])){

					$js_output .= 'metadata["'.$key.'"]= "<b>'.$user->lang['name'].'</b>: '.$key.'<br /><b>'.$user->lang['date'].':</b> '.date("Y-m-d H:i", $elem).'<br /><b>EQdkp-'.$user->lang['version'].':</b> '.$metadata[$key]['eqdkp_version'].'<br /><b>'.$user->lang['table_prefix'].':</b> '.$metadata[$key]['table_prefix'].'<br /><b>'.$user->lang['tables'].':</b> ';
					if ($metadata[$key]['uncomplete']){
						$js_output .= '<ul>';
						foreach ($metadata[$key]['tables'] as $table){
							$js_output .= '<li>'.$table.'</li>';
						}
						$js_output .= '</ul>';
					} else {
						$js_output .= $user->lang['cl_all'];
					}
					$js_output .= '";';
					
				} else {
					$js_output .= 'metadata["'.$key.'"]= "'.$user->lang['no_metadata'].'";';
				}

			}
		}
		
		     $tables = array();

        // Attempt to find all the tables associated with this installation of EQdkp
        if( !empty($table_prefix) )
        {
            $all_tables = $backup->_get_tables($db);
            
            // Only add the tables for EQdkp
            foreach( $all_tables as $tablename )
            {
                if( strpos($tablename, $table_prefix) !== false )
                {
                    $tables[$tablename] = $tablename;
                }
            }
        }
        else
        {
            // In this case, plugin tables won't be discovered and backed up.
            $tables = get_default_tables();
            
            foreach( $tables as $key => $table )
            {
                $tables[$this->_generate_table_name($table)] = $this->_generate_table_name($table);
            }
        }
				
				$jquery->Dialog('delete_warning', '', array('custom_js'=>"submit_form('backup_delete');", 'message'=>$user->lang['confirm_delete_backup']), 'confirm');
				$jquery->Dialog('restore_warning', '', array('custom_js'=>"submit_form('restore');", 'message'=>$user->lang['confirm_restore_backup']), 'confirm');
				$jquery->Tab_header('backup_tabs');
				$jquery->Tab_Select('backup_tabs', $tab);
				
        // Assign the rest of the variables.
        $tpl->assign_vars(array(
            'NO_BACKUPS'				=> (count($backups) == 0) ? true : false,
						'F_BACKUP'             		=> 'backup.php'.$SID,
            'L_BACKUP_DATABASE'    		=> $user->lang['backup_database'],
            'L_BACKUP_TITLE'       		=> $user->lang['backup_title'],
            'L_BACKUP_TYPE'        		=> $user->lang['backup_type'],
						'L_BACKUP_SYSTEM'        	=> $user->lang['backup_system'],
						'L_DB'										=> $user->lang['backup_system_db'],
						'L_DATA_FOLDER'						=> $user->lang['backup_system_data'],
						'L_RECOMMENDED'						=> $user->lang['recommended'],
            'L_CREATE_TABLE'       		=> $user->lang['create_table'],
						'L_SELECT_TABLES'       	=> $user->lang['select_tables'],
            'L_SKIP_NONESSENTIAL'  		=> $user->lang['skip_nonessential'],
            'TABLE_PREFIX_WARNING' 		=> $tp_warning,
            'L_YES'                		=> $user->lang['yes'],
            'L_NO'                 		=> $user->lang['no'],
						'JS_METADATA'							=> $js_output,
						'L_METADATA'							=> $user->lang['metadata'],
						'L_BACKUP_ACTION'    			=> $user->lang['backup_action'],
						'L_STORE'    							=> $user->lang['backup_action_store'],
						'L_DOWNLOAD'    					=> $user->lang['backup_action_download'],
						'L_BOTH'    							=> $user->lang['backup_action_both'],
						'L_BACKUP_RESTORE'    		=> $user->lang['backup_restore'],
						'L_RESTORE_INFO'    			=> $user->lang['backup_restore_info'],
						'L_SELECT_BACKUP'    			=> $user->lang['backup_select'],
						'L_START_RESTORE'    			=> $user->lang['backup_restore_button'],
						'L_BACKUP_DELETE'    			=> $user->lang['backup_delete'],
						'L_BACKUP_DOWNLOAD'    		=> $user->lang['backup_download'],
						'L_NO_BACKUPS'    				=> $user->lang['backup_no_files'],
						'L_UNCOMPLETE_INFO'				=> $user->lang['backup_uncomplete_info'],
						'TABLE_SELECT'						=> $jquery->MultiSelect('tables', $tables, $tables, 300,300),
	        ));
        
        $core->set_vars(array(
            'page_title'    	=> $user->lang['backup'],
            'template_file' 	=> 'admin/backup.html',
            'display'       	=> true
        ));
    }
    
	// ---------------------------------------------------------
  // Restore a Backup
  // ---------------------------------------------------------
	function restore_backup(){
		global $in, $db, $pcache, $user, $core, $eqdkp_root_path;
		$file_name = $in->get('backups');
		$file = $pcache->FolderPath('backup', 'eqdkp').$file_name;

		if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $file_name, $matches) || preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $file_name, $matches)){

			switch ($matches[3])
			{
				case 'sql':
					$fp = fopen($file, 'rb');
				break;
	
				case 'zip':
					//Copy the archive to the tmp-folder
					$new_file = $pcache->FolderPath('backup/tmp', 'eqdkp').$file_name;				
					$pcache->copy($file,  $new_file);
					$base = pathinfo($file_name);
					$archive = new PclZip($new_file);
					$archive->extract(PCLZIP_OPT_BY_NAME, $base['filename'].'.sql', PCLZIP_OPT_ADD_PATH, $pcache->FolderPath('backup/tmp', 'eqdkp'));
					
					//Now extract the data-Folder and replace existing files
					if ($in->get('restore_data', 0) == 1){
						$archive->delete(PCLZIP_OPT_BY_NAME, $base['filename'].'.sql');		
						$archive->extract(PCLZIP_OPT_PATH, $eqdkp_root_path);
					}
	
					$pcache->Delete($new_file);
					$backup_file = $pcache->FolderPath('backup/tmp', 'eqdkp').$base['filename'].'.sql';
					
					$fp = fopen($backup_file, 'rb');
				break;
			}
			$read = 'fread';
			$seek = 'fseek';
			$eof = 'feof';
			$close = 'fclose';
			$fgetd = 'fgetd';
			
			while (($sql = $this->$fgetd($fp, ";\n", $read, $seek, $eof)) !== false)
				{

					if (strpos($sql, "--") === false && $sql != ""){
						$db->query($sql);
						
					}

				}
				
			$core->message(sprintf($user->lang['backup_restore_success'], date("Y-m-d H:i", $matches[1])),$user->lang['backup'],'green');
			
		}
		if (strlen($backup_file)){
			$pcache->Delete($backup_file);
		}
		
		$this->display_form('1');
		
		
	}
	
	// ---------------------------------------------------------
    // Delete a stored backup
    // ---------------------------------------------------------
	function delete_backup(){
		global $pcache, $in, $user, $core;
		$file = $pcache->FolderPath('backup', 'eqdkp').$in->get('backups');
		$metafile = $pcache->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($in->get('backups'), strpos($in->get('backups'), '.')), "", $in->get('backups')).'.meta.php';
		if (file_exists($file)) {
			$pcache->Delete($file);
			$core->message($user->lang['backup_delete_success'],$user->lang['backup'],'green');
		};
		if (file_exists($metafile)){
			$pcache->Delete($metafile);
		}
		$this->display_form('1');
		
	}
	
	
	// ---------------------------------------------------------
    // Download a stored backup
    // ---------------------------------------------------------
	function download_backup(){
		global $pcache, $in;

		if ($in->get('backups') != ""){

			$file_name = $pcache->FolderPath('backup', 'eqdkp').$in->get('backups');

			
			if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $in->get('backups'), $matches) || preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql|zip?)$#', $in->get('backups'), $matches) ){

				$name = 'eqdkp-backup_' . date('Y-m-d_Hi', $matches[1]).".".$matches[3];
		
				switch ($matches[3])
				{
					case 'sql':
						$mimetype = 'text/x-sql';
					break;
					case 'zip':
						$mimetype = 'application/x-gzip';
					break;
				}
		
				header('Pragma: no-cache');
				header("Content-Type: $mimetype; name=\"$name\"");
				header("Content-disposition: attachment; filename=$name");
		
				@set_time_limit(0);
		
				$fp = @fopen($file_name, 'rb');
		
				if ($fp !== false)
				{
					while (!feof($fp))
					{
						echo fread($fp, 8192);
					}
					fclose($fp);
				}
		
				flush();
			}
		} else {

			$this->display_form('1');
		}
	}
	
	
	

	
    // ---------------------------------------------------------
    // Main Backup Script
    // ---------------------------------------------------------
    function do_backup()
    {
        global $core, $eqdkp_root_path, $user, $tpl, $pm, $in, $time;
        global $db, $dbhost, $dbname, $table_prefix, $pcache, $backup;
				
				if ($in->get('system') == 'data'){
				//Download data-Folder
					$file = $pcache->FolderPath('backup/tmp', 'eqdkp').md5(rand()).'.zip.gz';
					$archive = new PclZip($file);
					$archive->add($pcache->FolderPath(), PCLZIP_OPT_REMOVE_PATH, $pcache->FolderPath());
					$archive->delete(PCLZIP_OPT_BY_NAME, 'cache/');
					$archive->delete(PCLZIP_OPT_BY_NAME, 'eqdkp/armory/');
					$archive->delete(PCLZIP_OPT_BY_NAME, 'eqdkp/backup/tmp/');
					$archive->delete(PCLZIP_OPT_BY_NAME, 'pclzip/');
					if (file_exists($file)){
							$name = 'data_'.date('Y-m-d_Hi', time()).'.zip.gz';
							@header('Pragma: no-cache');
							@header("Content-Type: application/x-gzip; name=\"$name\"");
							@header("Content-disposition: attachment; filename=$name");
							readfile($file);
							$pcache->Delete($file);
							die();
					}

				} else {
				//SQL-Backup
					$in_tables = $in->getArray('tables', 'string');
					$format = $in->get('method', 'zip');		
					$action = $in->get('action');
					$create_table = ($action == 'store') ?  'Y' : $in->get('create_table');
					
					$result = $backup->create($action, $format, $create_table, $in_tables);
									
					if ($in->get('action') == 'store' || $in->get('action') == 'both'){
						if ($result){
							$core->message($user->lang['backup_store_success'],$user->lang['backup'],'green');		
						} else {
							$core->message($user->lang['error'],$user->lang['backup'],'red');		
						}		
					}	
					
					if ($in->get('action') == 'download' || $in->get('action') == 'both') {
						if ($result){
							
							$name = 'eqdkp-backup_'  . $time->date('Y-m-d_Hi');
							switch ($format)
							{
									case 'zip':
	
										@header('Pragma: no-cache');
										@header("Content-Type: application/zip; name=\"$name.zip\"");
										@header("Content-disposition: attachment; filename=$name.zip");
										readfile($pcache->FolderPath('backup', 'eqdkp').$result.'.zip');
										if ($in->get('action') == 'download'){
											$pcache->Delete($pcache->FolderPath('backup', 'eqdkp').$result.'.zip');
										}
										
										die();
									break;
									
									default:
										@header('Pragma: no-cache');
										@header("Content-Type: text/x-sql; name=\"$name.sql\"");
										@header("Content-disposition: attachment; filename=$name.sql");
										echo $result;
										die();
							}

					} 
				
				}
			}
			$this->display_form();
    } //close function




	
	// modified from PHP.net
	function fgetd(&$fp, $delim, $read, $seek, $eof, $buffer = 8192)
	{
		$record = '';
		$delim_len = strlen($delim);
	
		while (!$eof($fp))
		{
			$pos = strpos($record, $delim);
			if ($pos === false)
			{
				$record .= $read($fp, $buffer);
				if ($eof($fp) && ($pos = strpos($record, $delim)) !== false)
				{
					$seek($fp, $pos + $delim_len - strlen($record), SEEK_CUR);
					return trim(substr($record, 0, $pos));
				}
			}
			else
			{
				$seek($fp, $pos + $delim_len - strlen($record), SEEK_CUR);
				return trim(substr($record, 0, $pos));
			}
		}
	
		return false;
	}
}

$eqdkpbackup = new EQDKPBackup;
$eqdkpbackup->process();

?>
