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
	header('HTTP/1.0 404 Not Found'); exit;
}

//Factory
class dbal{	

	public static function factory($arrOptions = array()) {	
		$dbtype = (isset($arrOptions['dbtype'])) ? $arrOptions['dbtype'] : registry::get_const('dbtype');
		if(empty($dbtype)) throw new DBALException('dbtype not set');
		if ($dbtype == 'mysql') $dbtype = 'mysqli';
		
		require_once(registry::get_const('root_path') . 'libraries/dbal/' . $dbtype . '.dbal.class.php');
		$classname = 'dbal_' . $dbtype;
		
		if (dbal::check_if_pdo($dbtype)){
			$pdo = dbal::check_if_pdo($dbtype);
			if (!dbal::check_pdo($pdo)) throw new DBALException('PHP PDO ' . $pdo . ' not available');
		}  else {
			if (!dbal::check_extension($dbtype)) throw new DBALException('PHP-Extension ' . $dbtype . ' not available');
		}

		return registry::register($classname, array($arrOptions));
	}

	public static function available_dbals() {
		$arrDbals = array(
			'mysqli'	=> 'MySQLi',
			'mysql_pdo' => 'MySQL PDO',
		);
		
		foreach ($arrDbals as $key => $name){
			if (dbal::check_if_pdo($key)){
				$blnCheckResult = dbal::check_pdo(dbal::check_if_pdo($key));
			} else {
				$blnCheckResult = dbal::check_extension($key);
			}
			
			if(!$blnCheckResult) unset($arrDbals[$key]);
		}
		return $arrDbals;
	}
	
	private static function check_extension($strExtensionName){
		if (extension_loaded($strExtensionName)) return true;

		return false;
	}
	
	private static function check_pdo($strDriverName){
		if ( extension_loaded('pdo_'.$strDriverName) ) {
			return true;
		}
		
		return false;
	}
	
	private static function check_if_pdo($strDBType){
		if (substr($strDBType, -4) == "_pdo") return substr($strDBType, 0, -4);
		
		return false;
	}
}

//Exceptions
class DBALException extends Exception {
}

class DBALQueryException extends Exception {
	private $strQuery = "";
	
	// Die Exceptionmitteilung neu definieren, damit diese nicht optional ist
	public function __construct($message, $code = 0, $strQuery = "") {
		$this->strQuery = $strQuery;
		parent::__construct($message, $code);
	}
	
	public function getQuery(){
		return $this->strQuery;
	}
}

class DBALResultException extends Exception {
	private $strQuery = "";
	
	// Die Exceptionmitteilung neu definieren, damit diese nicht optional ist
	public function __construct($message, $code = 0, $strQuery = "") {
		$this->strQuery = $strQuery;
		parent::__construct($message, $code);
	}
	
	public function getQuery(){
		return $this->strQuery;
	}
}


//abstract Database class
abstract class Database extends gen_class {
	private		$objLogger = null;

	protected 	$resConnection;
	protected	$blnDisableAutocommit = false;
	protected	$strTmpPrefix = '';
	protected	$strTablePrefix =  '';
	protected	$strDebugPrefix = '';
	protected	$strCharset = "utf8";
	private		$blnInConstruct = true;
	protected	$strDatabase = '';
	protected 	$strError = '';
	protected	$intErrno = '';
	protected	$intQueryCount = 0;
	
	
	public function __construct($arrOptions = array()){
		if (registry::get_const('dbcharset') != "") $this->strCharset = registry::get_const('dbcharset');
		$this->objLogger = registry::register('plus_debug_logger');
		$this->strTablePrefix = registry::get_const("table_prefix");

		// set local variables
		if(isset($arrOptions['table_prefix']))		$this->strTablePrefix	= $arrOptions['table_prefix'];
		if(isset($arrOptions['pdl']))				$this->objLogger = $arrOptions['pdl'];
		if(isset($arrOptions['debug_prefix']))		$this->strDebugPrefix = $arrOptions['debug_prefix'];
		if(isset($arrOptions['charset']))			$this->strCharset	= $arrOptions['charset'];

		// register logging handlers
		if(!$this->objLogger->type_known($this->strDebugPrefix.'sql_error'))
			$this->objLogger->register_type($this->strDebugPrefix.'sql_error', array($this, 'pdl_pt_format_sql_error'), array($this, 'pdl_html_format_sql_error'), array(2,3,4), true);
		if(!$this->objLogger->type_known($this->strDebugPrefix.'sql_query'))
			$this->objLogger->register_type($this->strDebugPrefix.'sql_query', null, array($this, 'pdl_html_format_sql_query'), array(2,3,4));
		if(isset($arrOptions['open'])) {
			$intPort = (registry::get_const("dbport") !== null) ? registry::get_const("dbport") : ini_get("mysqli.default_port");
			$blnPersistent = (registry::get_const("dbpers") !== null) ? registry::get_const("dbpers") : false;
			$this->connect(registry::get_const("dbhost"), registry::get_const("dbname"), registry::get_const("dbuser"), registry::get_const("dbpass"), $intPort, $blnPersistent);
			//dont print any error-messages for this query
			if(defined('DEBUG') && DEBUG) $this->query("SET SESSION sql_mode = 'STRICT_TRANS_TABLES'");
		}
		$this->blnInConstruct = false;
	}

	// pdl html format function for sql errors
	public function pdl_html_format_sql_error($log_entry) {
		$text =  '<b>Error ID: </b>'		. $log_entry['args'][0] . '<br /><br />
			<b>Query:</b>'		. htmlentities($log_entry['args'][1]) . '<br /><br />
			<b>Message:</b> '		. $log_entry['args'][2] . '<br /><br />
			<b>Code:</b>'			. $log_entry['args'][3] . '<br />
			<b>Database:</b>'		. $log_entry['args'][4] . '<br />
			<b>Table Prefix:</b>'	. $log_entry['args'][5] . '<br />
			<b>PHP:</b>'			. phpversion() . ' | Database: ' . $this->strDbalName . '/' . $this->strDbmsName . $this->get_client_version() . '<br /><br />
			is your EQdkp updated? <a href="' . $this->root_path . 'admin/manage_live_update.php'.$this->SID.'">click to check</a>';
		return $text;
	}

	// pdl plaintext (logfile) format function for sql errors
	public function pdl_pt_format_sql_error($log_entry) {
		$text = '>>>> '.$log_entry['args'][0]." <<<<\t 
			Query: "	. $log_entry['args'][1] . "\t
			Message: "		. $log_entry['args'][2] . "\t
			Code: "		. $log_entry['args'][3] . "\t
			Database: "		. $log_entry['args'][4] . "\t
			Prefix: "		. $log_entry['args'][5] . "\t
			Trace:\n"	. $this->clean_errormessage($log_entry['args'][6])." <<<<\n";
		return $text;
	}
	
	public function clean_errormessage($strErrorMessage){
		$strErrorMessage = str_replace(registry::get_const("dbpass"), '*******', $strErrorMessage);
		if (strlen(registry::get_const("dbuser")) > 3){
			$strSuffix = substr(registry::get_const("dbuser"), 0, 3);
			$strUserReplace = str_pad($strSuffix, strlen(registry::get_const("dbuser")), '*');
		}
		$strErrorMessage = str_replace(registry::get_const("dbuser"), $strUserReplace, $strErrorMessage);
		if (strlen(registry::get_const("dbhost")) > 6){
			$strSuffix = substr(registry::get_const("dbhost"), 0, 6);
			$strHostReplace = str_pad($strSuffix, strlen(registry::get_const("dbhost")), '*');
		}
		$strErrorMessage = str_replace(registry::get_const("dbhost"), $strHostReplace, $strErrorMessage);
		return $strErrorMessage;
	}
	
	// Highlight certain keywords in a SQL query
	public function highlight($sql) {
		$red_keywords = array('/(INSERT INTO)/', '/(UPDATE\s+)/i', '/(DELETE FROM\s+)/', '/(CREATE TABLE)/', '/(IF (NOT)? EXISTS)/', '/(ALTER TABLE)/', '/(CHANGE)/');
		$green_keywords = array('/(SELECT\s+)/i', '/(FROM)/i', '/(WHERE)/', '/(LIMIT)/', '/(ORDER BY)/', '/(GROUP BY)/', '/(\s+AND\s+)/', '/(\s+OR\s+)/',
		'/(BETWEEN)/', '/(DESC)/', '/(LEFT JOIN)/', '/(LIKE)/', '/(SHOW TABLE STATUS)/', '/(SHOW)/',  '/(\s+ON\s+)/');
		$sql = preg_replace('/(' . $this->strTablePrefix. ')(\S+?)([\s\.,]|$)/', "<b>$1$2$3</b>", $sql); // bold table names
		$sql = preg_replace($red_keywords, "<span style=\"color:orange;\">$1</span>", $sql); // active keywords
		$sql = preg_replace($green_keywords, "<span class=\"positive\">$1</span>", $sql); //passive keywords
		return $sql;
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
	
	/**
	 * Close the database connection
	 */
	public function __destruct(){
		$this->disconnect();
		parent::__destruct();
	}
	
	/**
	 * Return an object property
	 * @param string
	 * @return string|null
	 */
	public function __get($strKey) {
		if ($strKey == 'connerror') {
			return $this->get_connerror();
		}
		
		if ($strKey == 'client_version') {
			return $this->get_client_version();
		}
		
		if ($strKey == 'server_version') {
			return $this->get_server_version();
		}
		
		if ($strKey == 'error') {
			return $this->strError;
		}
		
		if ($strKey == 'errno') {
			return $this->intErrno;
		}
		
		if ($strKey == 'query_count') {
			return $this->intQueryCount;
		}

		return null;
	}
	
	protected function error($strErrorMessage, $strQuery, $strErrorCode = '') {
		static $sys_message = false;
		$this->strError = $strErrorMessage;
		$this->intErrno = $strErrorCode;
		
		$strErrorID = md5('db_error'.$strErrorMessage.time().$this->strDebugPrefix);

		$exception = new Exception();
		$this->objLogger->log($this->strDebugPrefix."sql_error", $strErrorID, $strErrorMessage, $strQuery, $strErrorCode, registry::get_const('dbname'), $this->strTablePrefix, $exception->getTraceAsString());
		
		if(defined('USER_INITIALIZED') && !$this->blnInConstruct && !registry::get_const("lite_mode") && registry::fetch('user')->check_auth('a_', false)) {
			$blnDebugDisabled = (DEBUG < 2) ? true : false;
			$strEnableDebugMessage = "<li><a href=\"".registry::get_const("server_path")."admin/manage_settings.php".registry::get_const('SID')."\" target=\"_blank\">Go to your settings, enable Debug Level > 1</a> and <a href=\"javascript:location.reload();\">reload this page.</a></li>";
	
			registry::register('core')->message("<b>SQL Error (".$strErrorID.")</b> <ul>".(($blnDebugDisabled) ? $strEnableDebugMessage : '<li>See error message on the bottom</li>')."<li><a href=\"".registry::get_const("server_path")."admin/manage_logs.php".registry::get_const('SID')."&amp;error=db#errors\">Check your error logs</a>.</li></ul>", 'Error', 'red');
			$sys_message = true ;
		}
	}
	
	
	/**
	 * Prepare a statement (return a Database_Statement object)
	 * @param  string
	 * @return Database_Statement
	 */
	public function prepare($strQuery){
		$this->intQueryCount++;
		$objStatement = $this->createStatement($this->resConnection, $this->strTablePrefix, $this->strDebugPrefix, $this->blnDisableAutocommit);
		return $objStatement->prepare($strQuery);
	}
	
	/**
	 * Execute a query (return a Database_Result object)
	 * @param string
	 * @return Database_Result
	 */
	public function execute($strQuery){
		$strQuery = preg_replace("/([^\w]|^)__(\w)/", '$1'.$this->strTablePrefix.'$2', $strQuery);
		
		// log the query
		$this->pdl->log($this->strDebugPrefix . 'sql_query', $strQuery);
		$this->intQueryCount++;
		
		return $this->prepare($strQuery)->execute();
	}
	
	/**
	 * Execute a raw query (return a Database_Result object)
	 * @param string
	 * @return Database_Result
	 */
	public function query($strQuery, $blnGetFirstRow=false){
		$strQuery = preg_replace("/([^\w]|^)__(\w)/", '$1'.$this->strTablePrefix.'$2', $strQuery);
		$this->intQueryCount++;
		$objStatement = $this->createStatement($this->resConnection, $this->strTablePrefix, $this->strDebugPrefix,$this->blnDisableAutocommit);
		try {
			$objQuery = $objStatement->query($strQuery);
			if($blnGetFirstRow){
				$arrResult = $objQuery->fetchAssoc();
				return $arrResult;
			} else return $objQuery;
		} catch(DBALQueryException $e){
			$this->error($e->getMessage(), $e->getQuery(), $e->getCode());
		}
		return false;
	}

	/**
	 * Checks if a Query is executed or not
	 * @param string
	 * @return boolean
	 */
	public function checkQuery($strQuery){
		$strQuery = preg_replace("/([^\w]|^)__(\w)/", '$1'.$this->strTablePrefix.'$2', $strQuery);
		$objStatement = $this->createStatement($this->resConnection, $this->strTablePrefix, $this->strDebugPrefix,$this->blnDisableAutocommit);
		try {
			$objQuery = $objStatement->query($strQuery);
			return true;
		} catch(DBALQueryException $e){
			return false;
		}
		return false;
	}
	
	/**
	 * Return all columns of a particular table as array
	 * @param string
	 * @param boolean
	 * @return array
	 */
	public function listTables($strDatabase = null){
		if ($strDatabase === null)
		{
			$strDatabase = $this->strDatabase;
		}
		
		$arrReturn = array();
		$objQuery = $this->query(sprintf($this->strListTables, $strDatabase));
		if ($objQuery){
			$arrTables = $objQuery->fetchAllAssoc();

			foreach ($arrTables as $arrTable)
			{
				$arrReturn[] = current($arrTable);
			}
		}
		
		return $arrReturn;
	}
	
	/**
	 * Determine if a particular database table exists
	 * @param string
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	public function tableExists($strTable, $strDatabase = null){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		return in_array($strTable, $this->listTables($strDatabase));
	}
	
	/**
	 * Return all columns of a particular table as array
	 * @param string
	 * @param boolean
	 * @return array
	 */
	public function listFields($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		$arrReturn = $this->list_fields($strTable);
		return $arrReturn;
	}
	
	public function fieldInformation($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		return $this->field_information($strTable);
	}
	
	/**
	 * Determine if a particular column exists
	 * @param string
	 * @param string
	 * @param boolean
	 * @return boolean
	 */
	public function fieldExists($strField, $strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		foreach ($this->listFields($strTable) as $arrField)
		{
			if ($arrField['name'] == $strField)
			{
				return true;
			}
		}

		return false;
	}
	
	/**
	 * Return the field names of a particular table as array
	 * @param string
	 * @param boolean
	 * @return array
	 */
	public function getFieldNames($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		
		$arrNames = array();
		$arrFields = $this->listFields($strTable);

		foreach ($arrFields as $arrField)
		{
			$arrNames[] = $arrField['name'];
		}

		return $arrNames;
	}
	
	/**
	 * Change the current database
	 * @param string
	 * @return boolean
	 */
	public function setDatabase($strDatabase=false){
		return $this->set_database($strDatabase);
	}
	
	public function setPrefix($strPrefix){
		$this->strTmpPrefix = $this->strTablePrefix;
		$this->strTablePrefix = $strPrefix;
	}
	
	public function resetPrefix(){
		$this->strTablePrefix = $this->strTmpPrefix;
		$this->strTmpPrefix = '';
	}
	
	/**
	 * Begin a transaction
	 */
	public function beginTransaction(){
		$this->begin_transaction();
	}
	
	/**
	 * Commit a transaction
	 */
	public function commitTransaction(){
		$this->commit_transaction();
	}
	
	/**
	 * Rollback a transaction
	 */
	public function rollbackTransaction(){
		$this->rollback_transaction();
	}
	
	/**
	 * Return the table size in bytes
	 * @param string
	 * @return integer
	 */
	public function getSizeOf($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		return $this->get_size_of($strTable);
	}
	
	/**
	 * Return the next autoincrement ID of a table
	 * @param  string
	 * @return integer
	 */
	public function getNextId($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		return $this->get_next_id($strTable);
	}
	
	public function showCreateTable($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		return $this->show_create_table($strTable);
	}
	
	public function isEQdkpTable($strTable){
		$strTable = str_replace('__', $this->strTablePrefix, $strTable);
		if (strlen($this->strTablePrefix)){
			if ((strpos($strTable, $this->strTablePrefix) === 0)) return true;
			return false;
		}
		return true;
	}
	
	public function escapeString($strString){
		$objStatement = $this->createStatement($this->resConnection, $this->strTablePrefix, $this->strDebugPrefix,$this->blnDisableAutocommit);
		return $objStatement->escapeString($strString);
	}
	
	abstract public function connect($strHost, $strUser, $strPassword, $strDatabase, $intPort=false, $blnPersistent=false);
	abstract protected function disconnect();
	abstract protected function get_client_version();
	abstract protected function get_server_version();
	abstract protected function get_error();
	abstract protected function get_connerror();
	abstract protected function begin_transaction();
	abstract protected function commit_transaction();
	abstract protected function rollback_transaction();
	abstract protected function list_fields($strTable);
	abstract protected function field_information($strTable);
	abstract protected function set_database($strDatabase);
	abstract protected function get_size_of($strTable);
	abstract protected function get_next_id($strTable);
	abstract protected function createStatement($resConnection, $strTablePrefix, $strDebugPrefix, $blnDisableAutocommit);
	abstract protected function show_create_table($strTable);
}


abstract class DatabaseStatement {
	protected $resConnection;
	protected $resResult;
	protected $strQuery;
	protected $blnDisableAutocommit = false;
	protected $blnDieGracefully = false;
	protected $objLogger = null;
	protected $strTablePrefix;
	protected $strDebugPrefix;
	
	/**
	 * Validate the connection resource and store the query
	 * @param resource
	 * @param boolean
	 * @throws Exception
	 */
	public function __construct($resConnection, $strTablePrefix, $strDebugPrefix, $blnDisableAutocommit=false){
		if (!is_resource($resConnection) && !is_object($resConnection))
		{
			throw new DBALQueryException('Invalid connection resource', $this->strQuery);
		}
		
		$this->objLogger = registry::register('plus_debug_logger');
		$this->resConnection = $resConnection;
		$this->blnDisableAutocommit = $blnDisableAutocommit;
		$this->strTablePrefix = $strTablePrefix;
		$this->strDebugPrefix = $strDebugPrefix;
	}
	
	protected function error($strErrorMessage, $strQuery, $strErrorCode = '') {
		$strErrorID = md5('db_error'.$strErrorMessage.time().$this->strDebugPrefix);
		
		if(!registry::get_const("lite_mode") && registry::fetch('user')->check_auth('a_', false)) {
			$blnDebugDisabled = (DEBUG < 2) ? true : false;
			$strEnableDebugMessage = "<li><a href=\"".registry::get_const("server_path")."admin/manage_settings.php".registry::get_const('SID')."\" target=\"_blank\">Go to your settings, enable Debug Level > 1</a> and <a href=\"javascript:location.reload();\">reload this page.</a></li>";
	
			registry::register('core')->message("<b>SQL Error (".$strErrorID.")</b> <ul>".(($blnDebugDisabled) ? $strEnableDebugMessage : '<li>See error message on the bottom</li>')."<li><a href=\"".registry::get_const("server_path")."admin/manage_logs.php".registry::get_const('SID')."&amp;error=db#errors\">Check your error logs</a></li></ul>", 'Error', 'red');
		}
		$exception = new Exception();
		$this->objLogger->log($this->strDebugPrefix."sql_error", $strErrorID, $strErrorMessage, $strQuery, $strErrorCode, registry::get_const('dbname'), $this->strTablePrefix, $exception->getTraceAsString());

	}
		
	
	/**
	 * Return a parameter
	 *
	 * Supported parameters:
	 * - query:        current query string
	 * - error:        last error message
	 * - affectedRows: number of affected rows
	 * - insertId:     last insert ID
	 *
	 * Throw an exception on requests for protected properties.
	 * @param string
	 * @return mixed
	 */
	public function __get($strKey) {
		switch ($strKey) {
			case 'query':
				return $this->strQuery;
				break;

			case 'error':
				return $this->get_error();
				break;
				
			case 'errno':
				return $this->get_errno();
				break;

			case 'affectedRows':
				return $this->affected_rows();
				break;

			case 'insertId':
				return $this->insert_id();
				break;

			default:
				return null;
				break;
		}
	}
	
	/**
	 * Prepare a statement
	 * @param string
	 * @return Database_Statement
	 * @throws Exception
	 */
	public function prepare($strQuery)
	{
		if (!strlen($strQuery))
		{
			throw new Exception('Empty query string');
		}

		$this->resResult = NULL;
		$this->strQuery = $this->prepare_query($strQuery);

		// Auto-generate the SET/VALUES subpart
		if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || strncasecmp($this->strQuery, 'REPLACE', 7) === 0 || strncasecmp($this->strQuery, 'UPDATE', 6) === 0)
		{
			$this->strQuery = str_replace(':p', '%p', $this->strQuery);
		}

		// Replace wildcards
		$arrChunks = preg_split("/('[^']*')/", $this->strQuery, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);

		foreach ($arrChunks as $k=>$v)
		{
			if (substr($v, 0, 1) == "'")
			{
				continue;
			}

			$arrChunks[$k] = str_replace('?', '%s', $v);
		}

		$this->strQuery = trim(implode('', $arrChunks));
		return $this;
	}


	/**
	 * Take an associative array and auto-generate the SET/VALUES subpart of a query
	 * 
	 * Usage example:
	 * $objStatement->prepare("UPDATE table %s")->set(array('id'=>'my_id'));
	 * will be transformed into "UPDATE table SET id='my_id'".
	 * @param array
	 * @return Database_Statement
	 */
	public function set($arrParams)
	{
		$arrKeys = array_keys($arrParams);
		
		if (isset($arrKeys[0]) && is_array($arrParams[$arrKeys[0]])){
			$arrParamsArray = $arrParams;
			
			if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || (strncasecmp($this->strQuery, 'REPLACE', 7) === 0))
			{
				$strQuery = sprintf('(%s) VALUES ', implode(', ', array_keys($arrParams[$arrKeys[0]])));
			}

			$arrQuery = array();
			
			foreach($arrParamsArray as $arrParams){
				$arrParams = $this->escapeParams($arrParams);
		
				// INSERT / REPLACE
				if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || (strncasecmp($this->strQuery, 'REPLACE', 7) === 0))
				{
					$arrQuery[] = sprintf('(%s)', str_replace('%', '%%', implode(', ', array_values($arrParams))));
				}
			}
			
			$strQuery .= implode(', ', $arrQuery);
			
			
		} else {
			$arrParams = $this->escapeParams($arrParams);
	
			// INSERT / REPLACE
			if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || (strncasecmp($this->strQuery, 'REPLACE', 7) === 0))
			{
				$strQuery = sprintf('(%s) VALUES (%s)',
									implode(', ', array_keys($arrParams)),
									str_replace('%', '%%', implode(', ', array_values($arrParams))));
			}
	
			// UPDATE
			elseif (strncasecmp($this->strQuery, 'UPDATE', 6) === 0)
			{
				$arrSet = array();
	
				foreach ($arrParams as $k=>$v)
				{
					$arrSet[] = $k . '=' . $v;
				}
	
				$strQuery = 'SET ' . str_replace('%', '%%', implode(', ', $arrSet));
			}	
		}
		
		


		$this->strQuery = str_replace('%p', $strQuery, $this->strQuery);
		return $this;
	}
	
	/**
	 * Create an IN-Statement
	 *
	 * Usage example:
	 * $objStatement->prepare("UPDATE table SET a=4 WHERE id %in")->set(array(1, 3, 4));
	 * will be transformed into "UPDATE table SET a=4 WHERE id IN(1,3,4);".
	 * @param array
	 * @return Database_Statement
	 */
	public function in($arrParams){
		if (!count($arrParams))
		{
			throw new Exception('Empty param array');
		}
		$arrParams = $this->escapeParams($arrParams, true);
		
		$this->strQuery = str_replace(':in', "IN (".implode(',', $arrParams).")", $this->strQuery);
		return $this;
	}
	
	
	/**
	 * Limit the current result to a certain number of rows and take an offset value as second argument
	 * @param integer
	 * @param integer
	 * @return Database_Statement
	 */
	public function limit($intRows, $intOffset = 0){
		$intRows = intval($intRows);
		$intOffset = intval($intOffset);
		
		if ($intRows <= 0)
		{
			$intRows = 30;
		}

		if ($intOffset < 0)
		{
			$intOffset = 0;
		}

		$this->limit_query($intRows, $intOffset);
		return $this;
	}
	
	/**
	 * Execute the current statement
	 * @return Database_Result
	 * @throws Exception
	 */
	public function execute(){
		$arrParams = func_get_args();

		if (isset($arrParams[0]) && is_array($arrParams[0]))
		{
			$arrParams = array_values($arrParams[0]);
		}

		$this->replaceWildcards($arrParams);
		try {
			$objResult = $this->query();
			return $objResult;
		} catch(DBALQueryException $e){
			$this->error($e->getMessage(), $e->getQuery(), $e->getCode());
		}
		
		return false;
	}
	
	/**
	 * Execute a query and return the result object
	 * @param string
	 * @return Database_Result
	 * @throws Exception
	 */
	public function query($strQuery=""){
		if (!empty($strQuery))
		{
			$this->strQuery = $strQuery;
		}

		// Make sure there is a query string
		if ($this->strQuery == '')
		{
			throw new Exception('Empty query string');
		}

		// Execute the query
		if (($this->resResult = $this->execute_query()) == false)
		{
			throw new DBALQueryException($this->error, $this->errno, $this->strQuery);
		}

		// No result set available
		if (!is_resource($this->resResult) && !is_object($this->resResult))
		{
			//$this->debugQuery();
			return $this;
		}

		// Instantiate a result object
		try {
			$objResult = $this->createResult($this->resResult, $this->strQuery);
		} catch (DBALResultException $e){
			$this->error($e->getMessage(), $e->getQuery(), $e->getCode());
		}
		return $objResult;
	}
	
	/**
	 * Build the query string
	 * @param array
	 * @throws Exception
	 */
	protected function replaceWildcards($arrParams){
		$arrParams = $this->escapeParams($arrParams);
		$this->strQuery = preg_replace('/(?<!%)%([^bcdufosxX%])/', '%%$1', $this->strQuery);

		// Replace wildcards
		if (($this->strQuery = @vsprintf($this->strQuery, $arrParams)) == false)
		{
			throw new Exception('Too few arguments to build the query string');
		}
	}
	
	/**
	 * Escape the parameters and serialize objects and arrays
	 * @param array
	 * @return array
	 */
	protected function escapeParams($arrParams, $blnIgnoreKeys=false){
		foreach ($arrParams as $k=>$v)
		{
			switch (gettype($v))
			{
				case 'string':
					if(!$blnIgnoreKeys && strpos($v, $k) === 0){
						$orig_v = $v;
						$v = trim(substr($v, strlen($k)));
						$sign = substr($v, 0, 1);

						if (in_array($sign, array('+', '-', '*'. '/'))){
							$arrParams[$k] = $k.' '.$sign.' '.intval(trim(substr($v, 2)));
						} else {
							$arrParams[$k] = $this->string_escape($orig_v);
						}

					} else {								
						$arrParams[$k] = $this->string_escape($v);
					}
					break;

				case 'boolean':
					$arrParams[$k] = ($v === true) ? 1 : 0;
					break;

				case 'object':
					$arrParams[$k] = $this->string_escape(serialize($v));
					break;

				case 'array':
					$arrParams[$k] = $this->string_escape(serialize($v));
					break;
					
				case 'double':
				case 'float': 
					$arrParams[$k] = preg_replace('#([-]?)([0-9]+)([\.,]?)([0-9]*)#', "\\1\\2.\\4", $v);
					break;

				default:
					$arrParams[$k] = ($v === NULL) ? 'NULL' : $v;
					break;
			}
		}

		return $arrParams;
	}
	
	public function escapeString($strString){
		return $this->string_escape($strString);
	}
		
	abstract protected function prepare_query($strQuery);
	abstract protected function string_escape($strString);
	abstract protected function limit_query($intOffset, $intRows);
	abstract protected function execute_query();
	abstract protected function get_error();
	abstract protected function get_errno();
	abstract protected function affected_rows();
	abstract protected function insert_id();
	abstract protected function createResult($resResult, $strQuery);
}

abstract class DatabaseResult {
	
	protected $resResult;
	protected $strQuery;
	private $intIndex = -1;
	private $intRowIndex = -1;
	private $blnDone = false;
	protected $arrCache = array();
	protected $arrRow = false;
	
	public function __construct($resResult, $strQuery) {
		if (!is_resource($resResult) && !is_object($resResult))
		{
			throw new DBALResultException('Invalid result resource', 0, $strQuery);
		}

		$this->resResult = $resResult;
		$this->strQuery = $strQuery;
	}
	
	public function __destruct() {
		$this->free();
	}
	
	/**
	 * Return a result parameter or a particular field of the current row
	 *
	 * Supported parameters:
	 * - query:     corresponding query string
	 * - numRows:   number of rows of the current result
	 * - numFields: fields of the current result
	 *
	 * Throw an exception on requests for unknown fields.
	 * @param string
	 * @return mixed
	 */
	public function __get($strKey)
	{
		switch ($strKey)
		{
			case 'query':
				return $this->strQuery;
				break;

			case 'numRows':
				return $this->num_rows();
				break;

			case 'numFields':
				return $this->num_fields();
				break;

			default:
				if (is_array($this->arrRow) && isset($this->arrRow[$strKey])) return $this->arrRow[$strKey];
				return null;
				break;
		}
	}


	/**
	 * Fetch the current row as enumerated array - does not use Cache
	 * @return array
	 */
	public function fetchRow()
	{
		$this->arrRow = $this->fetch_row();
		return $this->arrRow;
	}


	/**
	 * Fetch the current row as associative array - does not use Cache
	 * @return array
	 */
	public function fetchAssoc()
	{
		$this->arrRow = $this->fetch_assoc();
		return $this->arrRow;		
	}
	
	/**
	 * Fetch the current row as associative array
	 * @return array
	 */
	private function fetchAssocCache(){
		if (!isset($this->arrCache[++$this->intIndex]))
		{
			if (($arrRow = $this->fetch_assoc()) == false)
			{
				--$this->intIndex;
				return false;
			}
		
			$this->arrCache[$this->intIndex] = $arrRow;
		}
		
		return $this->arrCache[$this->intIndex];
	}


	/**
	 * Fetch a particular field of each row of the result
	 * @param string
	 * @return array
	 */
	public function fetchEach($strKey)
	{
		$arrReturn = array();

		if ($this->intIndex < 0)
		{
			$this->fetchAllAssoc();
		}

		foreach ($this->arrCache as $arrRow)
		{
			$arrReturn[] = $arrRow[$strKey];
		}

		return $arrReturn;
	}


	/**
	 * Fetch all rows as associative array
	 * @return array
	 */
	public function fetchAllAssoc()
	{
		do
		{
			$blnHasNext = $this->fetchAssocCache();
		}
		while ($blnHasNext);

		return $this->arrCache;
	}


	/**
	 * Get the column information and return it as array
	 * @param integer
	 * @return array
	 */
	public function fetchField($intOffset=0)
	{
		$arrFields = $this->fetch_field($intOffset);

		if (is_object($arrFields))
		{
			$arrFields = get_object_vars($arrFields);
		}

		return $arrFields;
	}	

	/**
	 * Go to the first row of the current result
	 * @return Database_Result
	 */
	public function first()
	{
		if (!$this->arrCache)
		{
			$this->arrCache[++$this->intRowIndex] = $this->fetchAssocCache();
		}

		$this->intIndex = 0;
		return $this;
	}


	/**
	 * Go to the next row of the current result
	 * @return Database_Result|boolean
	 */
	public function next()
	{
		if ($this->blnDone)
		{
			return false;
		}

		if (!isset($this->arrCache[++$this->intIndex]))
		{
			--$this->intIndex; // see #3762

			if (($arrRow = $this->fetchAssoc()) == false)
			{
				$this->blnDone = true;
				return false;
			}

			$this->arrCache[$this->intIndex] = $arrRow;
			++$this->intRowIndex;

			return $this;
		}

		return $this;
	}


	/**
	 * Go to the previous row of the current result
	 * @return Database_Result|boolean
	 */
	public function prev()
	{
		if ($this->intIndex == 0)
		{
			return false;
		}

		--$this->intIndex;
		return $this;
	}


	/**
	 * Go to the last row of the current result
	 * @return Database_Result|boolean
	 */
	public function last()
	{
		if (!$this->blnDone)
		{
			$this->arrCache = $this->fetchAllAssoc();
		}

		$this->blnDone = true;
		$this->intIndex = $this->intRowIndex = count($this->arrCache) - 1;

		return $this;
	}


	/**
	 * Return the current row as associative array
	 * @param boolean
	 * @return array
	 */
	public function row($blnFetchArray=false)
	{
		if ($this->intIndex < 0)
		{
			$this->first();
		}

		return $blnFetchArray ? array_values($this->arrCache[$this->intIndex]) : $this->arrCache[$this->intIndex];
	}


	/**
	 * Reset the current result
	 * @return Database_Result
	 */
	public function reset()
	{
		$this->intIndex = -1;
		$this->blnDone = false;
		return $this;
	}


	/**
	 * Abstract database driver methods
	 */
	abstract protected function fetch_row();
	abstract protected function fetch_assoc();
	abstract protected function num_rows();
	abstract protected function num_fields();
	abstract protected function fetch_field($intOffset);
}

?>