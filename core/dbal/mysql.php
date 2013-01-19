<?php
/*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if(!defined('EQDKP_INC')) {
	header('HTTP/1.0 404 Not Found');
	exit;
}

class dbal_mysql extends dbal_common {
	protected $dbalName = 'mysql'; // equals php extension
	protected $dbmsName = 'MySQL'; // user visible name
	protected $max_identifier_len = 64;

	public function open($host, $database, $user, $password) {
		// connect to the server
		// note: persistent connections are not recommended to be used
		$this->link = @mysql_connect($host, $user, $password, true);
		if(!$this->link) {
			if($this->die_gracefully) {
				$this->error[] = 'mysql_connect() failed';
				return false;
			} else {
				throw new DBALException('mysql_connect() failed: ' . mysql_errno() . ' ' . mysql_error());
			}
		}
		// todo: test with older php versions and create workaround if needed
		if(function_exists('mysql_set_charset')) // PHP >= 5.2.3 and (MySQL >= 5.0.7 or MySQL >= 4.1.13)
			mysql_set_charset('utf8', $this->link);
		// select the database
		if(!mysql_select_db($database, $this->link)) {
			if($this->die_gracefully) {
				$this->err_array[] = 'mysql_select_db() failed';
				return false;
			} else
				throw new Exception('mysql_select_db() failed: ' . mysql_errno() . ' ' . mysql_error());
		}
		return true;
	}

	public function query($query_string, $params = false, $id = false, $debug = false) {
		if($query_string == '')
			return false;
		// remove old query resource
		unset($this->query);
		// apply the table prefix replacement
		// todo: check regex, was only copy/paste
		if(preg_match('#[^\w]__[^\s]+#', $query_string) != 0)
			$query_string = str_replace('__', $this->table_prefix, $query_string);
		// replace ?
		if ($id !== false){
			$query_string = str_replace('?', "'".$this->escape($id)."'", $query_string);
		}
		
		// replace :params (todo: check)
		if (is_array($params)) {
			if (count($params) == 0) return false;
			$params = $this->build_query(preg_replace('/^(INSERT|REPLACE|UPDATE).+/', '\1', $query_string), $params);
			$query_string  = str_replace(':params', $params, $query_string);
		}

		// this is used for pdh module debugging because sql log is inappropriate
		if($debug)
			die($query_string);
		// log the query
		$this->pdl->log($this->debug_prefix . 'sql_query', $query_string);
		// execute it
		$this->query = mysql_query($query_string, $this->link);
		// check the result
		if($this->query === false)
			$this->error($query_string);
		else
			$this->query_count++;
		// return the result or boolean status code
		return $this->query;
	}

	public function query_first($query_string) {
		// get the first row of a query, then discard it
		$query_resource = $this->query($query_string);
		$record = $this->fetch_row($query_resource, false);
		$this->free_result($query_resource);
		return $record[0];
	}

	public function fetch_row($query_resource, $assoc = true) {
		// return the next row, either indexed by numbers or associative
		return $assoc ? mysql_fetch_assoc($query_resource) : mysql_fetch_row($query_resource);
	}

	public function fetch_rowset($query_resource, $assoc = true) {
		// read all rows into an array and return it
		if($assoc)
			while ($row = mysql_fetch_assoc($query_resource)){
				$result[] = $row;
			}
		else
			while ($row = mysql_fetch_row($query_resource)){
				$result[] = $row;
			}
		return $result;
	}

	public function fetch_array($sql) {
		$result = $this->query($sql);
		while ( $row = $this->fetch_record($result) )
			$return[] = $row;
		return $return;
	}

	public function num_rows($query_resource) {
		return mysql_num_rows($query_resource);
	}

	public function num_fields($query_resource) {
		return mysql_num_fields($query_resource);
	}

	public function affected_rows() {
		return mysql_affected_rows($this->link);
	}

	public function insert_id() {
		return mysql_insert_id($this->link);
	}

	public function free_result($query_resource) {
		if (is_resource($query_resource)) mysql_free_result($query_resource);
	}

	public function escape($string, $array = false) {
		if(is_resource($this->link)) {
			if(is_array($array))
				return $this->_implode($string, $array);
			else
				return (is_numeric($string)) ? $string : mysql_real_escape_string($string, $this->link);
		} else
			return $string;
	}

	public function close() {
		return (is_resource($this->link)) ? mysql_close($this->link) : true;
	}

	protected function _error() {
		if ($this->link){
			$num = mysql_errno($this->link);
			$msg = mysql_error($this->link);
		} else {
			$num = mysql_errno();
			$msg = mysql_error();
		}

		$num = $num ? $num : 0;
		$msg = $msg ? $msg : "no error";
		return array(
			'code'		=> $num,
			'message'	=> $msg
		);
	}

	public function client_version() { // used for PDL output
		return mysql_get_client_info();
	}

	public function server_version($link = false) {
		return mysql_get_server_info($link ? $link : $this->link);
	}

	public function get_tables($blnOnlyEQdkpTables = false) {
		$result = $this->query('SHOW TABLES');
		$tables = array();
		while ($row = $this->fetch_record($result)){
			$strRowName = current($row);
			if ($blnOnlyEQdkpTables && !$this->is_eqdkp_table($strRowName)) continue;
			$tables[] = $strRowName;
		}

		$this->free_result($result);
		return $tables;
	}

	public function get_table_information($tableName = false, $blnOnlyEQdkpTables = false){
		if (!$tableName){
			$result = $this->query('SHOW TABLE STATUS');
			$tables = array();
			while ($row = $this->fetch_record($result)) {
				if ($blnOnlyEQdkpTables && !$this->is_eqdkp_table($row['Name'])) continue;

				$tables[$row['Name']] = array(
					'engine' 		=> $row['Engine'],
					'rows'			=> $row['Rows'],
					'data_length'	=> $row['Data_length'],
					'index_length'	=> $row['Index_length'],
					'collation'		=> $row['Collation'],
					'auto_increment'=> $row['Auto_increment'],
				);
			}
			$this->free_result($result);
			return $tables;
		} else {
			$result = $this->query("SHOW TABLE STATUS LIKE '".$this->escape($tableName)."'");
			if ($result){
				$row = $this->fetch_record($result);
				$this->free_result($result);
				return array(
					'engine' 		=> $row['Engine'],
					'rows'			=> $row['Rows'],
					'data_length'	=> $row['Data_length'],
					'index_length'	=> $row['Index_length'],
					'collation'		=> $row['Collation'],
					'auto_increment'=> $row['Auto_increment'],
				);
			}
			return false;
		}
	}

	public function show_create_table($strTableName){
		$result = $this->query('SHOW CREATE TABLE '.$this->escape($strTableName));
		$row = $this->fetch_row($result);
		$this->free_result($result);
		if ($row) return $row['Create Table'];
		return '';
	}

	public function get_field_information($strTableName){
		$result = $this->query('SELECT * FROM '.$this->escape($strTableName).' LIMIT 1');
		$arrFields = array();
		$i = 0;
		while($i < $this->num_fields($result)){
			$meta = mysql_fetch_field($result, $i);

			$arrFields[] = array(
				'blob'			=> $meta->blob,
				'max_length'	=> $meta->max_length,
				'multiple_key'	=> $meta->multiple_key,
				'name'			=> $meta->name,
				'not_null'		=> $meta->not_null,
				'numeric'		=> $meta->numeric,
				'primary_key'	=> $meta->primary_key,
				'table'			=> $meta->table,
				'type'			=> $meta->type,
				'unique_key'	=> $meta->unique_key,
				'unsigned'		=> $meta->unsigned,
				'zerofill'		=> $meta->zerofill,
			);
			$i++;
		}
		$this->free_result($result);
		return $arrFields;
	}

	// deprecated:
	public function fetch_record($query_resource, $assoc = true) {
		return $this->fetch_row($query_resource, $assoc);
	}
	public function fetch_record_set($query_resource, $assoc = true) {
		return $this->fetch_rowset($query_resource, $assoc);
	}
}

/*
// functions removed:
_sql_transaction

//functions renamed:
sql_connect -> open
sql_query -> query
sql_query_first -> query_first
sql_fetchrow/fetch_record -> fetch_row
sql_fetchrowset/fetch_record_set -> fetchrowset
sql_fetchArray -> fetch_array
sql_numrows -> num_rows
sql_affectedrows -> affected_rows
sql_lastid -> insert_id
sql_freeresult -> free_result
sql_escape -> escape
_sql_close / close_db -> close
_sql_error -> error
get_version -> client_version
*/
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_dbal_mysql', dbal_mysql::$shortcuts);
?>
