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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class backup extends gen_class {
	public static $shortcuts = array('config', 'user', 'pfh', 'jquery', 'db', 'time', 'core');

	public function __construct(){
		//Secure the backup-folder
		//Check if .htaccess exists
		$this->pfh->secure_folder('backup/meta', 'eqdkp');
		$this->pfh->secure_folder('backup/tmp', 'eqdkp');

		if(file_exists($this->pfh->FolderPath('backup', 'eqdkp').".htaccess")){
		} else{
			//Create a .htaccess
			$is_secure = $this->pfh->secure_folder('backup', 'eqdkp');

			if ($is_secure){
				//$core->message('.htaccess has been created successfully', $user->lang('backup'),'green');
			}else{
				$this->core->message('.htaccess could not be created. Please check if the data-folder is writable and has CHMOD 777.',$this->user->lang('backup'),'red');
			}
		} //END Check .htaccess
	}

	public function create($action = 'store', $format = 'zip', $create_table = 'N', $in_tables = false){
		set_time_limit(0);
	
		if (!$in_tables){
			$in_tables		= $this->db->get_tables(true);
		}

		$uncomplete = false;

		$all_tables		= $this->db->get_tables(true);
		$tables = array();
		foreach( $all_tables as $tablename ){
			if (in_array($tablename, $in_tables)){
				$tables[]		= $tablename;
			} else {
				$uncomplete		= true;
			}
		}

		$time			= $this->time->time;
		$run_comp		= false;

		$create_table	= ($action == 'store') ?  'Y' : $create_table;
		$addition		= ($uncomplete) ? '' : 'f';

		//
		// Generate the backup
		//

		//Lets write our header
		$data = '';
		$data .= "-- EQDKP-PLUS SQL Dump " . "\n";
		$data .= "-- version " . $this->config->get('plus_version') . "\n";
		$data .= "-- ".EQDKP_PROJECT_URL . "\n";
		$data .= "-- \n";
		$data .= "-- Host: " . (!empty($this->dbhost) ? $this->dbhost : 'localhost') . "\n";
		$data .= "-- Database: " . $this->dbname . "\n";
		if( !empty($this->table_prefix) ){
			$data .= "-- Table-Prefix: ". $this->table_prefix . "\n";
		}
		$data .= "-- Generation Time: " . date('M d, Y \a\t g:iA', $time) . "\n";
		$data .= "-- \n";
		$data .= "-- --------------------------------------------------------" . "\n";
		$data .= "\n";

		foreach ( $tables as $table ){
			$tablename			= $table;
			$table_sql_string	= $this->_create_table_sql_string($tablename);
			$data_sql_string	= $this->_create_data_sql_string($tablename);

			// NOTE: Error checking for table or data sql strings here?
			if ( $create_table == 'Y' ){
				$data .= "\n" . "-- \n";
				$data .= "-- Table structure for table `{$tablename}`" . ";\n\n";
				$data .= $table_sql_string . "\n";
			}

			if ( $table != '__sessions' ) {
				$data .= "\n" . "-- \n";
				$data .= "-- Dumping data for table `{$tablename}`" . ";\n\n";
				$data .= (($data_sql_string) ? $data_sql_string : "-- No data available;\n");
			}
		}
		unset($tablename, $table_sql_string, $data_sql_string);

		$random = mt_rand();

		$name = 'eqdkp-'.$addition.'backup_' .$time.'_'.$random;

		switch($format){
			//Zip - with important data-folders
			case 'zip':
				switch($action){
					case 'store' :
					case 'both' :
						$result = $this->store($data, $name, $format);
						$this->save_metadata($name, $time, $random, $uncomplete, $tables, $this->config->get('plus_version'), $this->table_prefix);
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
						$this->save_metadata($name, $time, $random, $uncomplete, $tables, $this->config->get('plus_version'), $this->table_prefix);
						return $name;
					break;
					case 'download' :
						return $data;
					break;
					case 'both' :
						$name = 'eqdkp-'.$addition.'backup_' .$time.'_'.$random;
						$result = $this->store($data, $name, $format);
						$this->save_metadata($name, $time, $random, $uncomplete, $tables, $this->config->get('plus_version'), $this->table_prefix);
						if ($result){
							return $data;
						} else {
							return false;
						}
					break;
				}
		}
	} //close function

	public function save_metadata($name, $time, $random, $uncomplete, $tables = false, $eqdkp_version = false, $prefix = false){
		$data['time']			= $time;
		$data['random']			= $random;
		$data['uncomplete']		= $uncomplete;
		$data['tables']			= ($tables) ? $tables : 'all';
		$data['eqdkp_version']	= ($eqdkp_version) ? $eqdkp_version : $this->config->get('plus_version');
		$data['table_prefix']	= ($prefix) ? $prefix : $this->table_prefix;
		$result = $this->pfh->putContent($this->pfh->FolderPath('backup/meta', 'eqdkp').$name.'.meta.php', serialize($data));
		if ($result > 0){
			return true;
		} else {
			return false;
		}
	}

	public function store($data, $name, $format = 'zip'){
		//Save this sql-file temporarly
		if ($format == 'zip'){
			$folder = 'tmp/';
		}

		$filename = $folder.$name.'.sql';
		$result = $this->pfh->putContent($this->pfh->FolderPath('backup', 'eqdkp').$filename, $data);

		//And now create the whole zip-file with the special data-folders
		if ($format == 'zip'){
			$file = $this->pfh->FolderPath('backup', 'eqdkp').$name.'.zip';
			$archive = registry::register('zip', array($file));
			$archive->add($this->pfh->FolderPath('backup', 'eqdkp').$filename, $this->pfh->FolderPath('backup', 'eqdkp').$folder);
			$archive->add($this->pfh->FolderPath('apa', 'eqdkp'), $this->root_path);
			$archive->add($this->pfh->FolderPath('config', 'eqdkp'), $this->root_path);
			$archive->add($this->pfh->FolderPath('layouts', 'eqdkp'), $this->root_path);
			$archive->add($this->pfh->FolderPath('timekeeper', 'eqdkp'), $this->root_path);
			$archive->create();

			$this->pfh->Delete($this->pfh->FolderPath('backup', 'eqdkp').$filename);
		}

		if ($result > 0){
			return true;
		} else {
			return false;
		}
	}

	public function prune_backups($days = false, $count = false){
		//Read out all of our backups
		$path = $this->pfh->FolderPath('eqdkp/backup');
			if($dir=opendir($path)){
				while($file=readdir($dir)){
					if (!is_dir($file) && $file != "." && $file != ".." && $file != "index.html" && $file != ".htaccess"){
						$files[$file]	=$file;
					}
				}
				closedir($dir);
			}

		//Generate backup-array, only list eqdkp-backups
		if (is_array($files)){
			foreach ($files as $elem){
				if (preg_match('#^eqdkp-backup_([0-9]{10})_([0-9]{1,10})\.(sql(?:\.(?:gz|bz2))?)$#', $elem, $matches)){
					$backups[$elem]		= $matches[1];
				}
				if (preg_match('#^eqdkp-fbackup_([0-9]{10})_([0-9]{1,10})\.(sql(?:\.(?:gz|bz2))?)$#', $elem, $matches)){
					$backups[$elem]		= $matches[1];
					$full[]				= $elem;
				}
			}
			//Sort the arrays the get the newest ones on top
			array_multisort($backups, SORT_DESC);

			//Delete all backups except the x newest ones
			if ($count > 0){
				$tmp_backups = array_slice($backups, $count);
				foreach ($tmp_backups as $key=>$value){
					$file		= $this->pfh->FolderPath('backup', 'eqdkp').$key;
					$metafile	= $this->pfh->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key).'.meta.php';
					if (file_exists($file)) {
						$this->pfh->Delete($file);
					}
					if (file_exists($metafile)){
						$this->pfh->Delete($metafile);
					}
				}
			} //close delete all backups except the x newest ones

			//Delete backups older than x days
			if ($days > 0){
				foreach ($backups as $key => $value){
					if (($value + $days*86400) < time()){
						$file		= $this->pfh->FolderPath('backup', 'eqdkp').$key;
						$metafile	= $this->pfh->FolderPath('backup/meta/', 'eqdkp').str_replace(substr($key, strpos($key, '.')), "", $key).'.meta.php';
						if (file_exists($file)) {
							$this->pfh->Delete($file);
						}
						if (file_exists($metafile)){
							$this->pfh->Delete($metafile);
						}
					}
				}
			}
		}
	} //close function

	public function _create_table_sql_string($tablename){
		// Generate the SQL string for this table
		// NOTE: SHOW CREATE TABLE was added to MySQL version 3.23.20, so I think it's safe to use that instead of doing it all manually.

		$createTable	= $this->db->show_create_table($tablename);

		$sql_string		 = "DROP TABLE IF EXISTS `{$tablename}`;" . "\n";

		$sql_string		.= $createTable;
		$sql_string		.= ";\n\n";
		return $sql_string;
	}

	//This sql data construction method is thanks to phpBB3.
	public function _create_data_sql_string($tablename){
		// Initialise the sql string
		$sql_string		= "";

		// Get field names from MySQL and output to a string in the correct MySQL syntax
		$arrFields = $this->db->get_field_information($tablename);
		$field_set = array();
		foreach ($arrFields as $value){
			$field_set[] = $value['name'];
		}
		$search				= array("\\", "'", "\x00", "\x0a", "\x0d", "\x1a", '"');
		$replace			= array("\\\\", "\\'", '\0', '\n', '\r', '\Z', '\\"');
		$fields				= implode(', ', $field_set);
		$field_string		= 'INSERT INTO `' . $tablename . '` (' . $fields . ') VALUES ';

		//Get Content
		$result = $this->db->query("SELECT * FROM ".$this->db->escape($tablename));
		while($row = $this->db->fetch_record($result)){
			$values		= array();
			$query		= $field_string . '(';

			foreach ($arrFields as $key => $field){
				$name = $field['name'];
				if (!isset($row[$name]) || is_null($row[$name])){
					$values[$key]		= 'NULL';
				}else if ($field['numeric'] && ($field['type'] !== 'timestamp')){
					$values[$key]		= $row[$name];
				}else{
					$values[$key]		= "'" . str_replace($search, $replace, $row[$name]) . "'";
				}
			}

			$query			 .= implode(', ', $values) . ')';
			$sql_string		 .= $query . ";\n";
		}

		$this->db->free_result($result);
		return $sql_string;
	}

}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_backup', backup::$shortcuts);
?>
