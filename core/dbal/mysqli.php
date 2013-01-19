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

class dbal_mysqli extends dbal_common {
	protected $dbalName = 'mysqli'; // equals php extension
	protected $dbmsName = 'MySQLi'; // user visible name
	protected $max_identifier_len = 64;

	public function open($host, $database, $user, $password) {
		// connect to the server
		// note: persistent connections are not recommended to be used
		$this->link = @mysqli_connect($host, $user, $password, $database);
		if(!$this->link) {
			if($this->die_gracefully) {
				$this->error[] = 'mysqli_connect() failed';
				return false;
			} else {
				throw new DBALException('mysqli_connect() failed: ' . mysqli_connect_errno() . ' ' . mysqli_connect_error());
			}
		}

		if(function_exists('mysqli_set_charset')) // PHP 5 >= 5.0.5
			mysqli_set_charset($this->link, 'utf8');

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
			die($query);
		// log the query
		$this->pdl->log($this->debug_prefix . 'sql_query', $query_string);
		// execute it
		$this->query = mysqli_query($this->link, $query_string);
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
		return $assoc ? mysqli_fetch_assoc($query_resource) : mysqli_fetch_row($query_resource);
	}

	public function fetch_rowset($query_resource, $assoc = true) {
		// read all rows into an array and return it
		if($assoc)
			while ($row = mysqli_fetch_assoc($query_resource)){
				$result[] = $row;
			}
		else
			while ($row = mysqli_fetch_row($query_resource)){
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
		return mysqli_num_rows($query_resource);
	}

	public function num_fields($query_resource) {
		return mysqli_num_fields($query_resource);
	}

	public function affected_rows() {
		return mysqli_affected_rows($this->link);
	}

	public function insert_id() {
		return mysqli_insert_id($this->link);
	}

	public function free_result($query_resource) {
		mysqli_free_result($query_resource);
	}

	public function escape($string, $array = false) {
		if(is_object($this->link)) {
			if(is_array($array))
				return $this->_implode($string, $array);
			else
				return (is_numeric($string)) ? $string : mysqli_real_escape_string($this->link, $string);
		} else
			return $string;
	}

	public function close() {
		return (is_object($this->link)) ? mysqli_close($this->link) : true;
	}

	protected function _error() {
		if ($this->link){
			$num = mysqli_errno($this->link);
			$msg = mysqli_error($this->link);
		} else {
			$num = mysqli_errno();
			$msg = mysqli_error();
		}

		$num = $num ? $num : 0;
		$msg = $msg ? $msg : "no error";
		return array(
			'code'		=> $num,
			'message'	=> $msg
		);
	}

	public function client_version() { // used for PDL output
		return mysqli_get_client_info();
	}

	public function server_version($link = false) {
		return mysqli_get_server_info($link ? $link : $this->link);
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
		$arrFields = mysqli_fetch_fields($result);
		$arrOutFields = array();

		foreach($arrFields as $meta) {

			$arrOutFields[] = array(
				'blob'		=> ($meta->flags & 16) ? 1 : 0,//
				'max_length'	=> $meta->max_length,//
				'multiple_key'=> ($meta->flags & 16384) ? 1 : 0,//
				'name'		=> (string)$meta->name,//
				'not_null'	=> ($meta->flags & 1) ? 1 : 0,//
				'numeric'		=> ($meta->flags & 32768) ? 1 : 0,
				'primary_key'	=> ($meta->flags & 2) ? 1 : 0,
				'table'		=> $meta->table,//
				'type'		=> $meta->type,//
				'unique_key'	=> ($meta->flags & 4) ? 1 : 0,
				'unsigned'	=> ($meta->flags & 32) ? 1 : 0,
				'zerofill'	=> ($meta->flags & 64) ? 1 : 0,
			);

		}
		$this->free_result($result);
		return $arrOutFields;
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
// public functions removed:
_sql_transaction

//public functions renamed:
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_dbal_mysqli', dbal_mysqli::$shortcuts);
?>
