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
	header('HTTP/1.0 404 Not Found'); exit;
}

class dbal {
	public static function factory($options = array()) {
		$dbtype = (isset($options['dbtype'])) ? $options['dbtype'] : registry::get_const('dbtype');
		if(empty($dbtype)) throw new DBALException('dbtype not set');

		require_once(registry::get_const('root_path') . 'core/dbal/' . $dbtype . '.php');
		$classname = 'dbal_' . $dbtype;
		if(!extension_loaded($dbtype)) throw new DBALException('PHP-Extension ' . $dbtype . ' not available');
		return registry::register($classname, array($options));
	}

	public static function available_dbals() {
		$arrDbals = array('mysqli'	=> 'MySQLi', 'mysql' => 'MySQL');
		foreach ($arrDbals as $key => $name){
			if(!extension_loaded($key)){
				unset($arrDbals[$key]);
			}
		}
		return $arrDbals;
	}
}

class DBALException extends Exception {
}

abstract class dbal_common extends gen_class {
	public static $shortcuts = array('pdl');

	public $link;
	public $query;
	public $query_count;
	public $err_array = array();
	public $error = array();

	protected $debug_prefix = '';
	protected $die_gracefully = false;

	private $keys 	= array(); //cache for keys (multidimensional arrays)
	private $tmp_prefix = '';

	private $in_construct = true;

	public function __construct($options=array()) {
		// set local variables
		if(isset($options['root_path'])) $this->root_path = $options['root_path'];
		if(isset($options['table_prefix'])) $this->table_prefix	= $options['table_prefix'];
		if(isset($options['pdl'])) $this->pdl = $options['pdl'];
		if(isset($options['debug_prefix'])) $this->debug_prefix = $options['debug_prefix'];
		if(isset($options['die_gracefully'])) $this->die_gracefully	= $options['die_gracefully'];

		// register logging handlers
		if(!$this->pdl->type_known($this->debug_prefix.'sql_error'))
			$this->pdl->register_type($this->debug_prefix.'sql_error', array($this, 'pdl_pt_format_sql_error'), array($this, 'pdl_html_format_sql_error'), array(2,3,4), true);
		if(!$this->pdl->type_known($this->debug_prefix.'sql_query'))
			$this->pdl->register_type($this->debug_prefix.'sql_query', null, array($this, 'pdl_html_format_sql_query'), array(2,3,4));
		if(isset($options['open'])) {
			$this->open($this->dbhost, $this->dbname, $this->dbuser, $this->dbpass);
			//dont print any error-messages for this query
			if(defined('DEBUG') && DEBUG) $this->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");
		}
		$this->in_construct = false;
	}

	public function __destruct() {
		$this->close();
		parent::__destruct();
	}

	// pdl html format function for sql queries
	public function pdl_html_format_sql_query($log_entry) {
		$text = '';
		//shorten really long queries (e.g. gzipped cache updates)
		if(strlen($log_entry['args'][0]) > 1000)
			$log_entry['args'][0] = substr($log_entry['args'][0], 0, 1000) . ' (...)';
		$text = $this->highlight(htmlentities(wordwrap($log_entry['args'][0],120,"\n",true)));
		return $text;
	}

	// pdl html format function for sql errors
	public function pdl_html_format_sql_error($log_entry) {
		$text = '<b>Query:</b>'		. htmlentities($log_entry['args'][0]) . '<br /><br />
			<b>Message:</b> '		. $log_entry['args'][1] . '<br /><br />
			<b>Code:</b>'			. $log_entry['args'][2] . '<br />
			<b>Database:</b>'		. $log_entry['args'][3] . '<br />
			<b>Table Prefix:</b>'	. $log_entry['args'][4] . '<br />
			<b>PHP:</b>'			. phpversion() . ' | Database: ' . $this->dbalName . '/' . $this->dbmsName . $this->client_version() . '<br /><br />
			is your EQdkp updated? <a href="' . $this->root_path . 'admin/manage_live_update.php'.$this->SID.'">click to check</a> ';
		return $text;
	}

	// pdl plaintext (logfile) format function for sql errors
	public function pdl_pt_format_sql_error($log_entry) {
		$text = 'Qry: '	. $log_entry['args'][0] . "\t
			Msg: "		. $log_entry['args'][1] . "\t
			Code: "		. $log_entry['args'][2] . "\t
			DB: "		. $log_entry['args'][3] . "\t
			Prfx: "		. $log_entry['args'][4] . "\t
			Trace:\n"	. $log_entry['args'][5];
		return $text;
	}

	// Highlight certain keywords in a SQL query
	public function highlight($sql) {
		$red_keywords = array('/(INSERT INTO)/', '/(UPDATE\s+)/', '/(DELETE FROM\s+)/', '/(CREATE TABLE)/', '/(IF (NOT)? EXISTS)/', '/(ALTER TABLE)/', '/(CHANGE)/');
		$green_keywords = array('/(SELECT)/', '/(FROM)/', '/(WHERE)/', '/(LIMIT)/', '/(ORDER BY)/', '/(GROUP BY)/', '/(\s+AND\s+)/', '/(\s+OR\s+)/',
		'/(BETWEEN)/', '/(DESC)/', '/(LEFT JOIN)/', '/(LIKE)/', '/(SHOW TABLE STATUS)/', '/(SHOW)/');
		$sql = preg_replace('/(' . $this->table_prefix . ')(\S+?)([\s\.,]|$)/', "<b>$1$2$3</b>", $sql); // bold table names
		$sql = preg_replace($red_keywords, "<span class=\"negative\">$1</span>", $sql); // active keywords
		$sql = preg_replace($green_keywords, "<span class=\"positive\">$1</span>", $sql); //passive keywords
		return $sql;
	}

	private function build_keysvalues($data) {
		$values = array();
		if(empty($this->keys)) {
			foreach($data as $k => $v) {
				$this->keys[] = $k;
			}
		}
		//ensure correct order
		foreach($this->keys as $k) {
			$v = $data[$k];
			if(is_null($v) || $v === 'NULL')
				$values[] = 'NULL';
			elseif(is_int($v))
				$values[] = $v;
			elseif(strpos($v, $k) === 0){
				$v = trim(substr($v, strlen($k)));
				$sign = substr($v, 0, 1);
				$values[] = $k.' '.$sign.' '.intval(trim(substr($v, 2)));
			}
			elseif(is_string($v))
				$values[] = "'" . $this->escape($v) . "'";
			elseif(is_float($v))
				$values[] = "'" . number_format($v, registry::register('config')->get('pk_round_precision'), '.', '') . "'";
			elseif(is_bool($v))
				$values[] = "'" . intval($v) . "'";
			else
				$values[] = "'" . $v . "'";
		}
		return '(' . implode(', ', $values) . ')';
	}

	public function build_query($query, $array = false) {
		if (!(is_array($array) && count($array) > 0))
			return false;
		$keys = array();
		$values = array();
		switch ($query) {
			case 'REPLACE':
			case 'INSERT':
				// Returns a string in the form: (<key1>, <key2> ...) VALUES ('<value1>', <(int)value2>, ...)
				$this->keys = array();
				$values_done = false;
				foreach($array as $k => $v) {
					// Hack to prevent assigning $array directly from a fetch_record call
					// injecting number-based indices into the built query
					if(is_numeric($k) && is_array($v)) {
						// may also be multidimensional array
						$values[] = $this->build_keysvalues($v);
						$values_done = true;
					}
				}
				if(!$values_done) $values[] = $this->build_keysvalues($array);
				if(!empty($this->keys) && !empty($values)) $query = ' (`' . implode('`, `', $this->keys) . '`) VALUES ' . implode(', ', $values);
				break;
			case 'UPDATE':
				// Returns a string in the form: <key1> = '<value1>', <key2> = <(int)value2>, ...
				foreach ($array as $k => $v) {
					// Hack to prevent assigning $array directly from a fetch_record call
					// injecting number-based indices into the built query
					if(is_numeric($k))
						continue;
					if(is_null($v))
						$values[] = "$k = NULL";
					elseif(is_int($v))
						$values[] = "$k = $v";
					elseif(strpos($v, $k) === 0){
						$v = trim(substr($v, strlen($k)));
						$sign = substr($v, 0, 1);
						$values[] = "$k = $k ".$sign." '".intval(trim(str_replace($sign, '', $v)))."'";
					}
					elseif(is_string($v))
						$values[] = "$k = '" . $this->escape($v) . "'";
					elseif(is_float($v))
						$values[] = "$k = '" . number_format($v, registry::register('config')->get('pk_round_precision'), '.', '') . "'";
					elseif(is_bool($v))
						$values[] = "$k = '" . intval($v) . "'";
					else
						$values[] = "$k = '$v'";
				}
				$query = implode(', ', $values);
				break;
			default:
				return false;
				break;
		}
		return $query;
	}

	public function error($sql = '', $return=false) {
		$error = $this->_error(); // get specific error code from dbms layer
		static $sys_message = false;
		if(!$this->in_construct && !$this->lite_mode && registry::fetch('user')->check_auth('a_', false)) {
			$blnDebugDisabled = (DEBUG < 2) ? true : false;
			$strEnableDebugMessage = "<li><a href=\"".$this->root_path."admin/manage_settings.php".registry::get_const('SID')."\" target=\"_blank\">Go to your settings, enable Debug Level > 1</a> and <a href=\"javascript:location.reload();\">reload this page.</a></li>";

			registry::register('core')->message("<b>SQL Error</b> <ul>".(($blnDebugDisabled) ? $strEnableDebugMessage : '<li>See error message on the bottom</li>')."<li><a href=\"".$this->root_path."admin/manage_logs.php".registry::get_const('SID')."&amp;error=db#errors\">Check your error logs</a></li></ul>", 'Error', 'red');
			$sys_message = true ;
		}
		if($return) return $error;
		$exception = new Exception();
		$this->pdl->log($this->debug_prefix."sql_error",$sql, $error['message'], $error['code'], $this->dbname, $this->table_prefix, $exception->getTraceAsString());
	}

	public function check_connection($error_connect, &$error, $lang, $table_prefix, $host, $user, $password, $database) {
		// check prefix for invalid characters and length, for more details see
		// http://dev.mysql.com/doc/refman/5.0/en/identifiers.html
		if (strpos($table_prefix, '.') !== false || strpos($table_prefix, '\\') !== false || strpos($table_prefix, '/') !== false) {
			$error[] = $lang['INST_ERR_PREFIX_INVALID'];
			return false;
		}
		$prefix_length = $this->max_identifier_len - 28; // 28 is max table name length
		if (strlen($table_prefix) > $prefix_length) {
			$error[] = sprintf($lang['INST_ERR_PREFIX_TOO_LONG'], $prefix_length);
			return false;
		}
		$test = $this->open($host, $database, $user, $password);
		$db_error = $this->_error();
		if ($test === false) {

			$error[] = $lang['INST_ERR_DB_CONNECT'] . '<br />' . (($db_error['message']) ? $db_error['message'] : $lang['INST_ERR_DB_NO_ERROR']);
		} else {
			// check for an existing installation
			$tables = $this->get_tables();
			foreach($tables as $tbl)
				if(strncasecmp($tbl, $table_prefix, strlen($table_prefix)) == 0) {
					$error[] = $lang['INST_ERR_PREFIX'];
					break;
				}
			// todo: implement version checking of client and server
		}
		if ($error_connect && (!isset($error) || !sizeof($error)))
			return true;
		return false;
	}

	// make sure that arrays are escaped correctly
	protected function _implode($delim, $array) {
		if(!is_array($array) || count($array) == 0)
			return '';
		foreach($array as $k => $v)
			$array[$k] = $this->escape($v);
		return implode($delim, $array);
	}

	public function is_eqdkp_table($strTableName){
		if (strlen($this->table_prefix)){
			if ((strpos($strTableName, $this->table_prefix) === 0))
				return true;
			return false;
		} else {
			//No prefix, so every Table is an eqdkp Table
			return true;
		}
	}

	public function set_prefix($prefix){
		if (strlen($prefix)){
			$this->tmp_prefix = $this->table_prefix;
			$this->table_prefix = $prefix;
			return true;
		}
		return false;
	}

	public function reset_prefix(){
		if (strlen($this->tmp_prefix)){
			$this->table_prefix = $this->tmp_prefix;
			return true;
		}
		return false;
	}
}

/*
// functions removed:
error_die
sql_transaction

//functions renamed:
sql_close -> close
sql_build_query -> build_query
sql_error -> error
sql_highlight -> highlight
*/
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_dbal_common', dbal_common::$shortcuts);
?>
