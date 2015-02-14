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

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');exit;
}
class db_access extends install_generic {
	public static $shortcuts = array('pfh' => array('file_handler', array('installer')));
	public static $before 		= 'ftp_access';

	public $next_button		= 'test_db';

	//default settings
	private $table_prefix	= 'eqdkp20_';
	private $dbtype			= 'mysqli';
	private $dbhost			= 'localhost';
	private $dbname			= '';
	private $dbuser			= '';
	
	public static function before() {
		return self::$before;
	}

	public function get_output() {
		$content = '
		<table width="100%" border="0" cellspacing="1" cellpadding="2" class="no-borders">
			<tr>
				<td width="40%" align="right"><strong>'.$this->lang['dbtype'].':</strong></td>
				<td width="60%">
					<select name="dbtype" class="input">
					';
		// Build the database drop-down
		include_once($this->root_path.'libraries/dbal/dbal.class.php');
		foreach ( dbal::available_dbals() as $db_type => $db_name ){
			$selected = ($db_type == $this->dbtype) ? ' selected="selected"' : '';
			$content .= '	<option value="'.$db_type.'"'.$selected.'>'.$db_name.'</option>
					';
		}
		$content .= '</select>
				</td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['dbhost'].': </strong></td>
				<td><input type="text" name="dbhost" size="25" value="'.$this->dbhost.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['dbname'].': </strong></td>
				<td><input type="text" name="dbname" size="25" value="'.$this->dbname.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['dbuser'].': </strong></td>
				<td><input type="text" name="dbuser" size="25" value="'.$this->dbuser.'" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['dbpass'].': </strong></td>
				<td><input type="password" name="dbpass" size="25" value="" class="input" /></td>
			</tr>
			<tr>
				<td align="right"><strong>'.$this->lang['table_prefix'].': </strong></td>
				<td><input type="text" name="table_prefix" size="25" value="'.$this->table_prefix.'" class="input" /></td>
			</tr>
		</table>';
		return $content;
	}
	public function get_filled_output() {
		$this->dbtype = registry::get_const('dbtype');
		$this->dbhost = registry::get_const('dbhost');
		$this->dbname = registry::get_const('dbname');
		$this->dbuser = registry::get_const('dbuser');
		$this->table_prefix = registry::get_const('table_prefix');
		return $this->get_output();
	}

	public function parse_input() {
		$this->dbtype		= $this->in->get('dbtype');
		$this->dbhost		= $this->in->get('dbhost', $this->dbhost);
		$this->dbname		= $this->in->get('dbname');
		$this->dbuser		= $this->in->get('dbuser');
		$this->table_prefix	= $this->in->get('table_prefix', $this->table_prefix);
		$this->dbpass		= $this->in->get('dbpass', '', 'raw');

		// check table_prefix
		if (!$this->table_prefix || !preg_match('/^[a-zA-Z]+$/', substr($this->table_prefix,0,1))) {
			$this->pdl->log('install_error', $this->lang['prefix_error']);
			return false;
		}

		$error = array();
		include_once($this->root_path.'libraries/dbal/dbal.class.php');
		try {
			$db = dbal::factory(array('dbtype' => $this->dbtype));
			$db->connect($this->dbhost, $this->dbname, $this->dbuser, $this->dbpass);
		
		} catch(DBALException $e){
			$this->pdl->log('install_error', $e->getMessage());
			return false;
		}
		
		//Check the Table Prefix
		if (strpos($this->table_prefix, '.') !== false || strpos($this->table_prefix, '\\') !== false || strpos($this->table_prefix, '/') !== false) {
			$this->pdl->log('install_error', $this->lang['INST_ERR_PREFIX_INVALID']);
			return false;
		}		
		
		//Check for existing Installation
		$arrTables = $db->listTables();
		foreach($arrTables as $tbl) {
			if(strncasecmp($tbl, $this->table_prefix, strlen($this->table_prefix)) == 0) {
				$this->pdl->log('install_error', $this->lang['INST_ERR_PREFIX']);
				return false;
			}
		}
		
		$this->pdl->log('install_success', $this->lang['dbcheck_success']);
		
		//Before writing the config-file, we have to check the writing-permissions of the tmp-folder
		if($this->use_ftp && !$this->pfh->testWrite()){
			$this->pdl->log('install_error', sprintf($this->lang['ftp_tmpwriteerror'], $this->pfh->get_cachefolder(true)));
			return false;
		}
		
		$this->configfile_fill();
		registry::$aliases['db'] = 'dbal_'.$this->dbtype;
		include_once($this->root_path.'libraries/dbal/'.$this->dbtype.'.dbal.class.php');
		return true;

		//maybe show version?
		#$server_version	= mysql_get_server_info();
		#$client_version	= mysql_get_client_info();
	}

	private function configfile_fill() {
		$content = substr(file_get_contents($this->root_path.'config.php'), 0, -2); //discard last two symbols (? >)
		$content = preg_replace('/^\$(dbtype|dbhost|dbname|dbuser|dbpass|table_prefix) = \'(.*)\';$/m', "", $content);
		$content = preg_replace('/\\n{3,}/', "\n\n", $content);
		$content .= '$dbtype = \''.$this->dbtype.'\';'."\n";
		$content .= '$dbhost = \''.$this->dbhost.'\';'."\n";
		$content .= '$dbport = false;'."\n";
		$content .= '$dbname = \''.$this->dbname.'\';'."\n";
		$content .= '$dbuser = \''.$this->dbuser.'\';'."\n";
		$content .= '$dbpass = \''.$this->dbpass.'\';'."\n";
		$content .= '$dbpers = false;'."\n";
		$content .= '$table_prefix = \''.$this->table_prefix.'\';'."\n\n";
		$content .= '?>';
		$this->pfh->putContent($this->root_path.'config.php', $content);
	}
}
?>