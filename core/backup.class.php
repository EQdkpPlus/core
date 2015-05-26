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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class backup extends gen_class {
	
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
	
	
	public function createDatabaseBackup($strFormat = 'zip', $blnCreateTableCmd = true, $arrTables=false, $blnForStorage=false){
		set_time_limit(0);
		
		if ($arrTables === false){
			$arrTables = $this->db->listTables();
		}
		
		$blnUncomplete = false;
		
		$arrAllTables = $this->db->listTables();
		
		//Check if its an uncomplete backup
		$arrTablesToBackup = array();
		foreach($arrAllTables as $strTablename){
			//Continue if not eqdkp Table
			if (!$this->db->isEQdkpTable($strTablename)) continue;
			
			if (in_array($strTablename, $arrTables)){
				$arrTablesToBackup[] = $strTablename;
			} else {
				$blnUncomplete = true;
			}
		}
		
		//Filenames
		$strRandom = substr(md5(generateRandomBytes()), 0, 8);
		$strFilepath = $this->pfh->FolderPath('backup/tmp', 'eqdkp');
		$strTime = $this->time->time;
		$strAddition = ($blnUncomplete) ? '' : 'f';
		$strFilename = 'eqdkp-'.$strAddition.'backup_' .$strTime.'_'.$strRandom;
		$strSQLFile = $strFilepath.$strFilename.'.sql';
		$strZipFile = $strFilepath.$strFilename.'.zip';
		
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
		$data .= "-- Generation Time: " . date('M d, Y \a\t g:iA', $strTime) . "\n";
		$data .= "-- \n";
		$data .= "-- --------------------------------------------------------" . "\n";
		$data .= "\n";
		
		$this->pfh->putContent($strSQLFile, $data);
		$data = "";
		
		foreach ( $arrTablesToBackup as $table ){
			$data = "";
			$tablename			= $table;
			$table_sql_string	= $this->_create_table_sql_string($tablename);
			$data_sql_string	= $this->_create_data_sql_string($tablename);
		
			// NOTE: Error checking for table or data sql strings here?
			if ( $blnCreateTableCmd ){
				$data .= "\n" . "-- \n";
				$data .= "-- Table structure for table `{$tablename}`" . ";\n\n";
				$data .= $table_sql_string . "\n";
			}
		
			if ( $table != '__sessions' ) {
				$data .= "\n" . "-- \n";
				$data .= "-- Dumping data for table `{$tablename}`" . ";\n\n";
				$data .= (($data_sql_string) ? $data_sql_string : "-- No data available;\n");
			}
			
			$this->pfh->addContent($strSQLFile, $data);
		}
		unset($tablename, $table_sql_string, $data_sql_string, $data);
		
		if($blnForStorage) $this->saveBackupMetadata($strFilename, $strTime, $blnUncomplete, $arrTablesToBackup);
		
		//Create Zip File
		if ($strFormat === 'zip'){
			$archive = registry::register('zip', array($strZipFile));
			$archive->add($strSQLFile, $this->pfh->FolderPath('backup/tmp', 'eqdkp'));
			$archive->create();
			
			$this->pfh->Delete($strSQLFile);
			return $strZipFile;
		}
		
		return $strSQLFile;
	}
	
	public function restoreDatabaseBackup($strFilename){
		$strFileExtension = strtolower(pathinfo($strFilename, PATHINFO_EXTENSION));
		$strSQLFile = "";
		
		if ($strFileExtension == 'zip'){
			//Copy the archive to the tmp-folder
			$strFrom = $strFilename;
			$strPlainFilename = pathinfo($strFilename, PATHINFO_FILENAME);
			$strTo = $this->pfh->FolderPath('backup/tmp', 'eqdkp').$strPlainFilename.'.'.$strFileExtension;
			$this->pfh->copy($strFrom, $strTo);
			
			//Lets unpack the File
			$archive = registry::register('zip', array($strTo));
			$strRandom = substr(md5(generateRandomBytes()), 0, 8);
			$archive->extract($this->pfh->FolderPath('backup/tmp/'.$strRandom, 'eqdkp'));
			$archive->close();
			//Try to find an .sql file
			$arrFiles = sdir($this->pfh->FolderPath('backup/tmp/'.$strRandom, 'eqdkp'));
			$strDeletePath = $this->pfh->FolderPath('backup/tmp/'.$strRandom, 'eqdkp');
			
			$this->pfh->Delete($strTo);
			
			foreach($arrFiles as $strFile){
				$strExt = strtolower(pathinfo($strFile, PATHINFO_EXTENSION));
				if ($strExt === 'sql') {
					$strSQLFile = $this->pfh->FolderPath('backup/tmp/'.$strRandom, 'eqdkp').$strFile;
					break;
				}
			}
			
		} elseif($strFileExtension == 'sql'){
			$strSQLFile = $strFilename;
		} else return false;
		
		if ($strSQLFile != "" && is_file($strSQLFile)){
		
			@set_time_limit(0);
			
			$fp = fopen($strSQLFile, 'rb');
			while (($sql = $this->fgetd($fp, ";\n", 'fread', 'fseek', 'feof')) !== false){
				if (strpos($sql, "--") === false && $sql != ""){
					$this->db->query($sql);
				}
			}
			fclose($fp);
		}
		
		if(isset($strDeletePath)) $this->pfh->Delete($strDeletePath);
	}
	
	
	private function saveBackupMetadata($strFilename, $strTime, $blnUncomplete, $arrTables){
		$data['time']			= $strTime;
		$data['uncomplete']		= $blnUncomplete;
		$data['tables']			= ($arrTables) ? $arrTables : 'all';
		$data['eqdkp_version']	= $this->config->get('plus_version');
		$data['table_prefix']	= $this->table_prefix;
		
		$result = $this->pfh->putContent($this->pfh->FolderPath('backup/meta', 'eqdkp').$strFilename.'.meta.php', serialize($data));
		if ($result > 0){
			return true;
		} else {
			return false;
		}
	}
	
	public function pruneBackups($intDays=false, $intCount=false){
		//Read out all of our backups
		$path = $this->pfh->FolderPath('backup', 'eqdkp');
		if($dir=opendir($path)){
			while($file=readdir($dir)){
				if (!is_dir($file) && $file != "." && $file != ".." && $file != "index.html" && $file != ".htaccess" && $file != 'meta' && $file != 'tmp'){
					$files[$file]	= $file;
				}
			}
			closedir($dir);
		}
		
		//Generate backup-array, only list eqdkp-backups
		if (is_array($files)){
		
			foreach ($files as $elem){
				if (preg_match('/eqdkp-(.?)backup_([0-9]{10})_(.*)\.(sql|zip)/', $elem, $matches)){
					$backups[$elem] = $matches[2]; //Save Time
					if ($matches[1] === "f") $full[] = $elem; //Its fullbackup
					
				}
			}
			//Sort the arrays the get the newest ones on top
			array_multisort($backups, SORT_DESC);
		
			//Delete all backups except the x newest ones
			if ($intCount > 0){
				$tmp_backups = array_slice($backups, $intCount);
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
			if ($intDays && intval($intDays) > 0){
		
				foreach ($backups as $key => $value){
						
					if (($value + intval($intDays)*86400) < time()){
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
	}
	
	public function moveBackupToBackupFolder($strFilename){
		$strOldFile = $strFilename;
		$strFilename = pathinfo($strOldFile, PATHINFO_FILENAME);
		$strExtension = pathinfo($strOldFile, PATHINFO_EXTENSION);
		$strNewFilename = $this->pfh->FolderPath('backup', 'eqdkp').$strFilename.'.'.$strExtension;
		$this->pfh->FileMove($strOldFile, $strNewFilename);
		
		return $strNewFilename;
	}

	private function _create_table_sql_string($tablename){
		// Generate the SQL string for this table

		$createTable	= $this->db->showCreateTable($tablename);

		$sql_string		 = "DROP TABLE IF EXISTS `{$tablename}`;" . "\n";

		$sql_string		.= $createTable;
		$sql_string		.= ";";
		return $sql_string;
	}

	//This sql data construction method is thanks to phpBB3.
	private function _create_data_sql_string($tablename){
		// Initialise the sql string
		$sql_string		= "";

		// Get field names from MySQL and output to a string in the correct MySQL syntax
		$arrFields = $this->db->listFields($tablename);

		$field_set = array();
		foreach ($arrFields as $key => $value){
			if (!is_numeric($key)) continue;
			$field_set[] = $value['name'];
		}
		$search				= array("\\", "'", "\x00", "\x0a", "\x0d", "\x1a");
		$replace			= array("\\\\", "\\'", '\0', '\n', '\r', '\Z');
		$fields				= implode('`, `', $field_set);
		$field_string		= 'INSERT INTO `' . $tablename . '` (`' . $fields . '`) VALUES ';

		//Get Content
		$objQuery = $this->db->query("SELECT * FROM ".$tablename);
		if ($objQuery){
			while($row = $objQuery->fetchAssoc()){
				$values		= array();
				$query		= $field_string . '(';
	
				foreach ($arrFields as $key => $field){
					if (!is_numeric($key)) continue;
	
					$name = $field['name'];
					if (!isset($row[$name]) || is_null($row[$name])){
						$values[$key]		= 'NULL';
					}else if (($field['numeric']) && ($field['type'] !== 'timestamp')){
						$values[$key]		= $row[$name];
					}else{
						$values[$key]		= "'" . str_replace($search, $replace, $row[$name]) . "'";
					}
				}
	
				$query			 .= implode(', ', $values) . ')';
				$sql_string		 .= $query . ";\n";
			}
		}
		return $sql_string;
	}
	
	// modified from PHP.net
	private function fgetd(&$fp, $delim, $read, $seek, $eof, $buffer = 8192){
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
?>