<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class mmocms_backup {
		
		function __construct(){
			global $core, $user, $pcache, $jquery;
			//Secure the backup-folder
			//Check if .htaccess exists
			$pcache->secure_folder('backup/meta', 'eqdkp');
			$pcache->secure_folder('backup/tmp', 'eqdkp');
			
			if(file_exists($pcache->FolderPath('backup', 'eqdkp').".htaccess")){
			} else{	
				//Create a .htaccess
				$is_secure = $pcache->secure_folder('backup', 'eqdkp');

				if ($is_secure){
					//$core->message('.htaccess has been created successfully', $user->lang['backup'],'green');
				}
				else{
					$core->message('.htaccess could not be created. Please check if the data-folder is writable and has CHMOD 777.',$user->lang['backup'],'red');
				}	
			} //END Check .htaccess
			
		}
		
		
		function create($action = 'store', $format = 'zip', $create_table = 'N', $in_tables = false){
			
		global $core, $eqdkp_root_path, $user, $tpl, $pm, $in;
      	global $db, $dbhost, $dbname, $table_prefix, $pcache;
			 
				if (!$in_tables){
					if( !empty($table_prefix) ) {
						$in_tables = $this->_get_tables($db);
					} else {
						$in_tables = get_default_tables();
					}
				}
				
        $tables = array();
		$uncomplete = false;
				
        // Attempt to find all the tables associated with this installation of EQdkp
        if( !empty($table_prefix) )
        {
            $all_tables = $this->_get_tables($db);
            
            // Only add the tables for EQdkp
            foreach( $all_tables as $tablename )
            {
                if( strpos($tablename, $table_prefix) !== false )
                {
                    if (in_array($tablename, $in_tables)){
											$tables[] = $tablename;
										} else {
											$uncomplete = true;
										}
                }
            }
        }
        else
        {
            // In this case, plugin tables won't be discovered and backed up.
            $tables = get_default_tables();
            
            foreach( $tables as $key => $table )
            {
                if (in_array($this->_generate_table_name($table), $in_tables)){
									$tables[$key] = $this->_generate_table_name($table);
								} else {
									$uncomplete = true;
								}
            }
        }
          
        $time = time();
        $run_comp = false;

				$create_table = ($action == 'store') ?  'Y' : $create_table;

				$addition = ($uncomplete) ? '' : 'f';
         
        // 
        // Generate the backup
        //
        
        //Lets write our header
        $data = '';
        $data .= "-- EQDKP-PLUS SQL Dump " . "\n";
        $data .= "-- version " . $core->config['plus_version'] . "\n";
        $data .= "-- http://www.eqdkp-plus.com" . "\n";
        $data .= "-- \n";
        $data .= "-- Host: " . (!empty($dbhost) ? $dbhost : 'localhost') . "\n";
				$data .= "-- Database: " . $dbname . "\n";
				if( !empty($table_prefix) ){
					$data .= "-- Table-Prefix: ". $table_prefix . "\n";
				}	
        $data .= "-- Generation Time: " . date('M d, Y \a\t g:iA', $time) . "\n";
        $data .= "-- \n";
        $data .= "-- --------------------------------------------------------" . "\n";
        $data .= "\n";
        
        foreach ( $tables as $table )
        {
            $tablename        = $table;
            $table_sql_string = $this->_create_table_sql_string($tablename);
            $data_sql_string  = $this->_create_data_sql_string($tablename);
        
            // NOTE: Error checking for table or data sql strings here?
                    
            if ( $create_table == 'Y' )
            {
                $data .= "\n" . "-- \n";
                $data .= "-- Table structure for table `{$tablename}`" . ";\n\n";
                $data .= $table_sql_string . "\n";
            }

            if ( $table != '__sessions' ) 
            {
                $data .= "\n" . "-- \n";
                $data .= "-- Dumping data for table `{$tablename}`" . ";\n\n";
                $data .= (($data_sql_string) ? $data_sql_string : "-- No data available") . ";\n";
            }
        
        }
        unset($tablename, $table_sql_string, $data_sql_string);
		
		$time = time();
		$random = mt_rand();
		
		$name = 'eqdkp-'.$addition.'backup_' .$time.'_'.$random;
		
		switch($format){
				//Zip - with important data-folders
				case 'zip':
					switch($action){
						case 'store' :
						case 'both' :
							$result = $this->store($data, $name, $format);
							$this->save_metadata($name, $time, $random, $uncomplete, $tables, $core->config['plus_version'], $table_prefix);
							return $name;
						break;
						case 'download' :					
							$result = $this->store($data, $name, $format);
							return $name;
						break;
					}
				break;
				
				
				//TEXT - Pure SQL
				default:
					switch($action){
						case 'store' : 
							$name = 'eqdkp-'.$addition.'backup_' .$time.'_'.$random;
							$result = $this->store($data, $name, $format);
							$this->save_metadata($name, $time, $random, $uncomplete, $tables, $core->config['plus_version'], $table_prefix);
							return $name;
						break;
						case 'download' :
							return $data;
						break;
						case 'both' :
							$name = 'eqdkp-'.$addition.'backup_' .$time.'_'.$random;
							$result = $this->store($data, $name, $format);
							$this->save_metadata($name, $time, $random, $uncomplete, $tables, $core->config['plus_version'], $table_prefix);
							if ($result){
								return $data;
							} else {
								return false;
							}
						break;
					}
					
		}

      
		} //close function
		
		
		function save_metadata($name, $time, $random, $uncomplete, $tables = false, $eqdkp_version = false, $prefix = false){
			global $core, $eqdkp_root_path, $user, $tpl, $pm, $in;
      		global $db, $dbhost, $dbname, $table_prefix, $pcache;
			
			
			$data['time'] = $time;
			$data['random'] = $random;
			$data['uncomplete'] = $uncomplete;
			$data['tables'] = ($tables) ? $tables : 'all';
			$data['eqdkp_version'] = ($eqdkp_version) ? $eqdkp_version : $core->config['plus_version'];
			$data['table_prefix'] = ($prefix) ? $prefix : $table_prefix;
			$fp = fopen($pcache->FolderPath('backup/meta', 'eqdkp').$name.'.meta.php', "w");
			$result = fputs($fp, serialize($data));
			fclose($fp);
			if ($result > 0){
				return true;
			} else {
				return false;
			}
			
		}
		
		function store($data, $name, $format = 'zip'){
			global $core, $eqdkp_root_path, $user, $tpl, $pm, $in;
      global $db, $dbhost, $dbname, $table_prefix, $pcache;
					
				//Save this sql-file temporarly
				if ($format == 'zip'){
					$folder = 'tmp/';
				}
										
				$filename = $folder.$name.'.sql';

				$fp = fopen($pcache->FolderPath('backup', 'eqdkp').$filename, "w");
				$result = fputs($fp, $data);
				fclose($fp);
				
				//And now create the whole zip-file with the special data-folders
				if ($format == 'zip'){
					$file = $pcache->FolderPath('backup', 'eqdkp').$name.'.zip';
					$archive = new PclZip($file);
					$archive->add($pcache->FolderPath('backup', 'eqdkp').$filename, PCLZIP_OPT_REMOVE_PATH, $pcache->FolderPath('backup', 'eqdkp').$folder);
					$archive->add($pcache->FolderPath('apa', 'eqdkp'), PCLZIP_OPT_REMOVE_PATH, '../');
					$archive->add($pcache->FolderPath('config', 'eqdkp'), PCLZIP_OPT_REMOVE_PATH, '../');
					$archive->add($pcache->FolderPath('layouts', 'eqdkp'), PCLZIP_OPT_REMOVE_PATH, '../');
					$archive->add($pcache->FolderPath('timekeeper', 'eqdkp'), PCLZIP_OPT_REMOVE_PATH, '../');
					
					$pcache->Delete($pcache->FolderPath('backup', 'eqdkp').$filename);
				}
				
				if ($result > 0){
					return true;
				} else {
					return false;
				}
		}
		
		function prune_backups($days = false, $count = false){
			global $pcache;
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
					if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql(?:\.(?:gz|bz2))?)$#', $elem, $matches)){
						$backups[$elem] = $matches[1];	
					}
					if (preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql(?:\.(?:gz|bz2))?)$#', $elem, $matches)){
						$backups[$elem] = $matches[1];
						$full[] = $elem;
	
					}
					
				}
				//Sort the arrays the get the newest ones on top
				array_multisort($backups, SORT_DESC);
	
				
				//Delete all backups except the x newest ones
				if ($count > 0){
					$tmp_backups = array_slice($backups, $count); 
					foreach ($tmp_backups as $key=>$value){
						$file = $pcache->FolderPath('backup', 'eqdkp').$key;
						$metafile = $pcache->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key).'.meta.php';
						if (file_exists($file)) {
							$pcache->Delete($file);
						}
						if (file_exists($metafile)){
							$pcache->Delete($metafile);
						}
					}
					
				} //close delete all backups except the x newest ones
				
				//Delete backups older than x days
				if ($days > 0){
					foreach ($backups as $key => $value){
						if (($value + $days*86400) < time()){
							$file = $pcache->FolderPath('backup', 'eqdkp').$key;
							$metafile = $pcache->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key).'.meta.php';
							if (file_exists($file)) {
								$pcache->Delete($file);
							}
							if (file_exists($metafile)){
								$pcache->Delete($metafile);
							}
						}
					}
				}
		
			}
		} //close function
		
    function _create_table_sql_string($tablename)
    {
        global $db;
        // Generate the SQL string for this table
        // NOTE: SHOW CREATE TABLE was added to MySQL version 3.23.20, so I think it's safe to use that instead of doing it all manually.

        $sql = 'SHOW CREATE TABLE ' . $tablename;
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);

        $sql_string  = "DROP TABLE IF EXISTS `{$tablename}`;" . "\n";

        $sql_string .= $row['Create Table'];
        $sql_string .= ";\n\n";

        $db->sql_freeresult($result);
        
        return $sql_string;
    }
    
    //This sql data construction method is thanks to phpBB3.
    function _create_data_sql_string($tablename)
    {
        global $db;
        
        // Initialise the sql string
        $sql_string = "";
        
        // Get field names from MySQL and output to a string in the correct MySQL syntax
        $sql = "SELECT * FROM $tablename";
        $result = mysql_unbuffered_query($sql, $db->link_id);

        if ($result != false)
        {
            $fields_cnt = mysql_num_fields($result);
    
            // Get field information
            $field = array();
            for ($i = 0; $i < $fields_cnt; $i++)
            {
                $field[] = mysql_fetch_field($result, $i);
            }
            $field_set = array();
            
            for ($j = 0; $j < $fields_cnt; $j++)
            {
                $field_set[] = $field[$j]->name;
            }

            // Set some constant values for the table
            $search         = array("\\", "'", "\x00", "\x0a", "\x0d", "\x1a", '"');
            $replace        = array("\\\\", "\\'", '\0', '\n', '\r', '\Z', '\\"');
            $fields         = implode(', ', $field_set);
            $field_string   = 'INSERT INTO `' . $tablename . '` (' . $fields . ') VALUES ';

            // Generate the data for the table. 
            // Note that the data dump is done without multi-values.
            while ($row = mysql_fetch_row($result))
            {
                $values = array();

                $query = $field_string . '(';

                for ($j = 0; $j < $fields_cnt; $j++)
                {
                    if (!isset($row[$j]) || is_null($row[$j]))
                    {
                        $values[$j] = 'NULL';
                    }
                    else if ($field[$j]->numeric && ($field[$j]->type !== 'timestamp'))
                    {
                        $values[$j] = $row[$j];
                    }
                    else
                    {
                        $values[$j] = "'" . str_replace($search, $replace, $row[$j]) . "'";
                    }
                }
                $query .= implode(', ', $values) . ')';

                $sql_string .= $query . ";\n";
            }
            mysql_free_result($result);
        }
        
        return $sql_string;
    }
      
    function _generate_table_name($val)
    {
        global $table_prefix;
        
        $val = preg_replace('#__([^\s]+)#', $table_prefix . '\1', $val);
        return $val;
    }
    
    function _get_tables($db)
    {
			global $db;
      switch ($db->sql_layer)
      {
          case 'mysql':
          case 'mysql4':
          case 'mysqli':
              $sql = 'SHOW TABLES';
          break;
      }

      $result = $db->query($sql);
  
      $tables = array();
  
      while ($row = $db->fetch_record($result))
      {
          $tables[] = current($row);
      }
  
      $db->free_result($result);
  
      return $tables;
    }
  
}    

?>
