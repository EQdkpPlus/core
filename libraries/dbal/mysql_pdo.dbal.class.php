<?php

/**
 * Class DB_Mysqli
 *
 * Driver class for MySQLi databases.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Driver
 */
class dbal_mysql_pdo extends Database
{
	
	protected $strDbalName = 'mysql pdo';
	protected $strDbmsName = 'MySQL PDO'; // user visible name
	
	/**
	 * List tables query
	 * @var string
	 */
	protected $strListTables = "SHOW TABLES FROM `%s`";
	
	/**
	 * List fields query
	 * @var string
	 */
	protected $strListFields = "SHOW COLUMNS FROM `%s`";
	
	
	/**
	 * Connect to the database server and select the database
	 */
	public function connect($strHost, $strDatabase, $strUser, $strPassword, $intPort=false, $blnPersistent=false)
	{
		$strPort = ($intPort !== false) ? ';port='.$intPort : "";
		$arrOptions = array();
		if($blnPersistent) $arrOptions[PDO::ATTR_PERSISTENT] = true;
		if($strDatabase == "") throw new DBALException("Database Name missing");
		
		$arrOptions[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		
		// SSL Connection to MySQL
		if(defined('DB_SSL_USE') && DB_SSL_USE){
			$arrOptions[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = (defined('DB_SSL_VERIFY_SERVERCERT')) ? DB_SSL_VERIFY_SERVERCERT : true;
			$arrOptions[PDO::MYSQL_ATTR_SSL_KEY] = (defined('DB_SSL_KEY')) ? DB_SSL_KEY : '';
			$arrOptions[PDO::MYSQL_ATTR_SSL_CERT] = (defined('DB_SSL_CERT')) ? DB_SSL_CERT : '';
			$arrOptions[PDO::MYSQL_ATTR_SSL_CA] = (defined('DB_SSL_CA')) ? DB_SSL_CA : '';			
		}
		
		try {
			$this->resConnection = new PDO('mysql:host='.$strHost.';dbname='.$strDatabase.';charset='.$this->strCharset.$strPort, $strUser, $strPassword, $arrOptions);
		} catch (PDOException $e) {
			$strError =  $e->getMessage();
			throw new DBALException($strError);
		}
		
		$this->strDatabase = $strDatabase;
	}
	
	
	/**
	 * Disconnect from the database
	 */
	protected function disconnect()
	{
		$this->resConnection = null;
	}
	
	protected function get_client_version() {
		return @$this->resConnection->getAttribute(PDO::ATTR_CLIENT_VERSION);
	}
	
	protected function get_server_version(){
		return @$this->resConnection->getAttribute(PDO::ATTR_SERVER_VERSION);
	}
	
	/**
	 * Return the last error message
	 * @return string
	 */
	protected function get_error()
	{
		$arrError = $this->resConnection->errorInfo();
		return $arrError[2];
	}
	
	protected  function get_errno(){
		return 0;
		return @$this->resConnection->errorCode();
	}
	
	protected function get_connerror(){
		$arrError = $this->resConnection->errorInfo();
		return $arrError[2];
	}
	
	
	/**
	 * Auto-generate a FIND_IN_SET() statement
	 * @param string
	 * @param string
	 * @param boolean
	 * @return string
	 */
	protected function find_in_set($strKey, $strSet, $blnIsField=false)
	{
		if ($blnIsField)
		{
			return "FIND_IN_SET(" . $strKey . ", " . $strSet . ")";
		}
		else
		{
			return "FIND_IN_SET(" . $strKey . ", " . $this->resConnection->quote($strSet) . ")";
		}
	}
	
	
	/**
	 * Return a standardized array with field information
	 *
	 * Standardized format:
	 * - name:       field name (e.g. my_field)
	 * - type:       field type (e.g. "int" or "number")
	 * - length:     field length (e.g. 20)
	 * - precision:  precision of a float number (e.g. 5)
	 * - null:       NULL or NOT NULL
	 * - default:    default value (e.g. "default_value")
	 * - attributes: attributes (e.g. "unsigned")
	 * - index:      PRIMARY, UNIQUE or INDEX
	 * - extra:      extra information (e.g. auto_increment)
	 * - numeric:	 true/false
	 * @param string
	 * @return array
	 * @todo Support all kind of keys (e.g. FULLTEXT or FOREIGN).
	 */
	protected function list_fields($strTable)
	{
		$arrReturn = array();
		$arrFields = $this->query(sprintf($this->strListFields, $strTable))->fetchAllAssoc();
		
		foreach ($arrFields as $k=>$v)
		{
			$arrChunks = preg_split('/(\([^\)]+\))/', $v['Type'], -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
			
			$arrReturn[$k]['name'] = $v['Field'];
			$arrReturn[$k]['type'] = $arrChunks[0];
			
			if (isset($arrChunks[1]) && strlen($arrChunks[1]))
			{
				$arrChunks[1] = str_replace(array('(', ')'), array('', ''), $arrChunks[1]);
				$arrSubChunks = explode(',', $arrChunks[1]);
				
				$arrReturn[$k]['length'] = trim($arrSubChunks[0]);
				
				if (isset($arrSubChunks[1]) && strlen($arrSubChunks[1]))
				{
					$arrReturn[$k]['precision'] = trim($arrSubChunks[1]);
				}
			}
			
			if (isset($arrChunks[2]) && strlen($arrChunks[2]))
			{
				$arrReturn[$k]['attributes'] = trim($arrChunks[2]);
			}
			
			if (strlen($v['Key']))
			{
				switch ($v['Key'])
				{
					case 'PRI':
						$arrReturn[$k]['index'] = 'PRIMARY';
						break;
						
					case 'UNI':
						$arrReturn[$k]['index'] = 'UNIQUE';
						break;
						
					case 'MUL':
						// Ignore
						break;
						
					default:
						$arrReturn[$k]['index'] = 'KEY';
						break;
				}
			}
			
			$arrReturn[$k]['null'] = ($v['Null'] == 'YES') ? 'NULL' : 'NOT NULL';
			$arrReturn[$k]['default'] = $v['Default'];
			$arrReturn[$k]['extra'] = $v['Extra'];
			$arrNumeric = array("tinyint", "smallint", "mediumint", "int", "bigint", "bit", "float", "double", "decimal");
			$arrReturn[$k]['numeric'] = (in_array($arrReturn[$k]['type'], $arrNumeric));
		}
		
		$arrIndexes = $this->query("SHOW INDEXES FROM `$strTable`")->fetchAllAssoc();
		
		foreach ($arrIndexes as $arrIndex)
		{
			$arrReturn[$arrIndex['Key_name']]['name'] = $arrIndex['Key_name'];
			$arrReturn[$arrIndex['Key_name']]['type'] = 'index';
			$arrReturn[$arrIndex['Key_name']]['index_fields'][] = $arrIndex['Column_name'];
			$arrReturn[$arrIndex['Key_name']]['index'] = (($arrIndex['Non_unique'] == 0) ? 'UNIQUE' : 'KEY');
		}
		
		return $arrReturn;
	}
	
	
	/**
	 * Change the current database
	 * @param string
	 * @return boolean
	 */
	protected function set_database($strDatabase=false)
	{
		if ($strDatabase === false) $strDatabase = $this->strDatabase;
		if (is_object($this->resConnection)) $this->resConnection = null;
		
		$strPort = (registry::get_const("dbport") !== null) ? ';port='.registry::get_const("dbport") : "";
		
		try {
			$this->resConnection = new PDO('mysql:host='.registry::get_const("dbhost").';dbname='.$strDatabase.';charset='.$this->strCharset.$strPort, registry::get_const("dbuser"), registry::get_const("dbpass"));
		} catch (PDOException $e) {
			$strError =  $e->getMessage();
			throw new DBALException($strError);
		}
	}
	
	
	/**
	 * Begin a transaction
	 */
	protected function begin_transaction()
	{
		$this->resConnection->beginTransaction();
	}
	
	
	/**
	 * Commit a transaction
	 */
	protected function commit_transaction()
	{
		$this->resConnection->commit();
	}
	
	
	/**
	 * Rollback a transaction
	 */
	protected function rollback_transaction()
	{
		$this->resConnection->rollBack();
	}
	
	
	/**
	 * Lock one or more tables
	 * @param array
	 */
	protected function lock_tables($arrTables)
	{
		$arrLocks = array();
		
		foreach ($arrTables as $table=>$mode)
		{
			$arrLocks[] = $table .' '. $mode;
		}
		
		$this->resConnection->query("LOCK TABLES " . implode(', ', $arrLocks));
	}
	
	
	/**
	 * Unlock all tables
	 */
	protected function unlock_tables()
	{
		$this->resConnection->query("UNLOCK TABLES");
	}
	
	
	/**
	 * Return the table size in bytes
	 * @param string
	 * @return integer
	 */
	protected function get_size_of($strTable)
	{
		$objStatus = @$this->resConnection->query("SHOW TABLE STATUS LIKE '" . $strTable . "'")
		->fetch(PDO::FETCH_OBJ);
		return ($objStatus->Data_length + $objStatus->Index_length);
	}
	
	protected function field_information($strTable){
		$objStatus = @$this->resConnection->query("SHOW TABLE STATUS LIKE '" . $strTable . "'")
		->fetch(PDO::FETCH_OBJ);
		return array(
				'data_length'	=> $objStatus->Data_length,
				'index_length'	=> $objStatus->Index_length,
				'rows'			=> $objStatus->Rows,
				'collation'		=> $objStatus->Collation,
				'engine'		=> $objStatus->Engine,
				'auto_increment'=> $objStatus->Auto_increment,
		);
	}
	
	
	/**
	 * Return the next autoincrement ID of a table
	 * @param string
	 * @return integer
	 */
	protected function get_next_id($strTable)
	{
		$objStatus = @$this->resConnection->query("SHOW TABLE STATUS LIKE '" . $strTable . "'")
		->fetch(PDO::FETCH_OBJ);
		
		return $objStatus->Auto_increment;
	}
	
	
	/**
	 * Create a Database_Statement object
	 * @param resource
	 * @param boolean
	 * @return DB_Mysql_PDO_Statement
	 */
	protected function createStatement($resConnection, $strTablePrefix, $strDebugPrefix, $blnDisableAutocommit)
	{
		return new DB_Mysql_PDO_Statement($resConnection, $strTablePrefix, $strDebugPrefix, $blnDisableAutocommit);
	}
	
	protected function show_create_table($strTable){
		$objQuery = $this->query("SHOW CREATE TABLE ".$strTable);
		if ($objQuery) {
			$arrResult = $objQuery->fetchAssoc();
			return $arrResult['Create Table'];
		}
		
		return "";
	}
}


/**
 * Class DB_Mysql_PDO_Statement
 *
 * Driver class for MySQLi databases.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Driver
 */
class DB_Mysql_PDO_Statement extends DatabaseStatement
{
	
	protected $arrParams = array();
	protected $arrParamsList = array();
	/**
	 * Prepare a query and return it
	 * @param string
	 */
	protected function prepare_query($strQuery)
	{
		return $strQuery;
	}
	
	
	/**
	 * Escape a string
	 * @param string
	 * @return string
	 */
	protected function string_escape($strString)
	{
		return $this->resConnection->quote($strString);
	}
	
	
	/**
	 * Limit the current query
	 * @param integer
	 * @param integer
	 */
	protected function limit_query($intRows, $intOffset)
	{
		if (strncasecmp($this->strQuery, 'SELECT', 6) === 0)
		{
			$this->strQuery .= ' LIMIT ' . (int)$intOffset . ',' . (int)$intRows;
		}
		else
		{
			$this->strQuery .= ' LIMIT ' . (int)$intRows;
		}
	}
	
	
	/**
	 * Execute the current query
	 * @return resource
	 */
	protected function execute_query()
	{
		$this->strQuery  = $this->replaceTablePrefix();
		
		//Bring params to the correct order
		if(isset($this->arrParams['multiple'])){
			//First, bind the query
			try {
				$objStatement = $this->resConnection->prepare($this->strQuery);
			}catch(PDOException $e){
				$strError =  $e->getMessage();
				throw new DBALQueryException($strError);
			}

			
			foreach($this->arrParams['multiple'] as $arrSetParams){
				$arrParams = array();
				foreach($this->arrParamsList as $key => $val){
					if($val == '?') $arrParams[] = array_shift($this->arrParams['execute']);

					if($val == ':cond'){
						$arrSet = array_shift($this->arrParams['conditions']);
						foreach($arrSet as $v){
							$arrParams[] = $v;
						}
					}
					
					if($val == ':in') {
						foreach($this->arrParams['in'] as $v){
							$arrParams[] = $v;
						}
					}
					if($val == ':p') {
						foreach($arrSetParams as $v){
							$arrParams[] = $v;
						}
					}
				}
				
				// Log the Query
				$this->objLogger->log($this->strDebugPrefix . 'sql_query', $this->strQuery, $arrParams);
				
				//Now Execute
				try {
					$objStatement->execute($arrParams);
				}catch(PDOException $e){
					$strError =  $e->getMessage();
					throw new DBALQueryException($strError);
				}
			}
			
			
			//Now Return
			if (is_object($objStatement)) $this->resConnection->affectedRows = $objStatement->rowCount();
			if (is_object($objStatement) && $objStatement->columnCount() === 0) return true;
			return $objStatement;
			
		} else {
			$arrParams = array();
			
			foreach($this->arrParamsList as $key => $val){				
				if($val == '?') $arrParams[] = array_shift($this->arrParams['execute']);

				if($val == ':cond'){
					$arrSet = array_shift($this->arrParams['conditions']);
					if(is_array($arrSet)) {
						foreach($arrSet as $v){
							$arrParams[] = $v;
						}
					}
				}
				
				if($val == ':in') {
					foreach($this->arrParams['in'] as $v){
						$arrParams[] = $v;
					}
				}
				if($val == ':p') {
					foreach($this->arrParams['set'] as $v){
						$arrParams[] = $v;
					}
				}
			}
			
			// Log the Query
			$this->objLogger->log($this->strDebugPrefix . 'sql_query', $this->strQuery, $arrParams);
			
			try {
				$objStatement = $this->resConnection->prepare($this->strQuery);
				$objStatement->execute($arrParams);
			}catch(PDOException $e){
				$strError =  $e->getMessage();
				throw new DBALQueryException($strError);
			}
			
			if (is_object($objStatement)) $this->resConnection->affectedRows = $objStatement->rowCount();
			if (is_object($objStatement) && $objStatement->columnCount() === 0) return true;
			return $objStatement;
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
		
		
		$intWildcards = preg_match_all("/(\?|\:in|\:p)/", $this->strQuery, $arrWilcards);
		if($intWildcards){
			$this->arrParamsList = $arrWilcards[0];
		}
		
		// Auto-generate the SET/VALUES subpart
		if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || strncasecmp($this->strQuery, 'REPLACE', 7) === 0 || strncasecmp($this->strQuery, 'UPDATE', 6) === 0)
		{
			$this->strQuery = str_replace(':p', '%p', $this->strQuery);
		}
		
		return $this;
	}
	
	private function add_ticks($mixTables){
		if(is_array($mixTables)){
			foreach($mixTables as $key => $val){
				if($val[0] != "`") $mixTables[$key] = "`".$val."`";
			}
			
			return $mixTables;
			
		} else {
			if($mixTables[0] != "`") $mixTables = "`".$mixTables."`";
			return $mixTables;
		}
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
				$arrQuestions = array_fill(0, count($arrParams[$arrKeys[0]]), '?');
				$strQuery = sprintf('(%s) VALUES (%s)',
						implode(', ', $this->add_ticks(array_keys($arrParams[$arrKeys[0]]))),
						implode(', ', $arrQuestions));
			}

			foreach($arrParamsArray as $arrParams){
				//$arrParams = $this->escapeParams($arrParams);
				
				// INSERT / REPLACE
				if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || (strncasecmp($this->strQuery, 'REPLACE', 7) === 0))
				{
					$this->arrParams['multiple'][] = $arrParams;
				}
			}

		} else {
			//$arrParams = $this->escapeParams($arrParams);
			
			// INSERT / REPLACE
			if (strncasecmp($this->strQuery, 'INSERT', 6) === 0 || (strncasecmp($this->strQuery, 'REPLACE', 7) === 0))
			{
				$this->arrParams['set'] = array_values($arrParams);
				$arrQuestions = array_fill(0, count($arrParams), '?');
				$strQuery = sprintf('(%s) VALUES (%s)',
						implode(', ', $this->add_ticks(array_keys($arrParams))),
						implode(', ', $arrQuestions));
			}
			
			// UPDATE
			elseif (strncasecmp($this->strQuery, 'UPDATE', 6) === 0)
			{
				$arrSet = array();
				$this->arrParams['set'] = array_values($arrParams);
				
				$i=0;
				foreach ($arrParams as $k=>$v)
				{
					$vi = trim(substr($v, strlen($k)));
					$sign = substr($vi, 0, 1);
					
					if (strpos($v, $k) === 0 && in_array($sign, array('+', '-', '*'. '/'))){
						$arrSet[] = $this->add_ticks($k).' = '.$k.''.$sign.''.intval(trim(substr($vi, 1)));
						unset($this->arrParams['set'][$i]);
					} else {
						$arrSet[] = $this->add_ticks($k) . '=?';
					}
					$i++;
				}
				
				$strQuery = 'SET ' . implode(', ', $arrSet);				
			}
		}
		
		$this->strQuery = str_replace('%p', $strQuery, $this->strQuery);
		return $this;
	}
	
	/**
	 * Create an IN-Statement
	 *
	 * Usage example:
	 * $objStatement->prepare("UPDATE table SET a=4 WHERE id :in")->set(array(1, 3, 4));
	 * will be transformed into "UPDATE table SET a=4 WHERE id IN(1,3,4);".
	 * @param array
	 * @return Database_Statement
	 */
	public function in($arrParams){
		if (!count($arrParams))
		{
			throw new Exception('Empty param array');
		}
		//$arrParams = $this->escapeParams($arrParams, true);
		
		$this->arrParams['in'] = $arrParams;
		
		$arrQuestions = array_fill(0, count($arrParams), '?');
		
		$this->strQuery = str_replace(':in', "IN (".implode(',', $arrQuestions).")", $this->strQuery);
		
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
		
		$this->arrParams['execute'] = $arrParams;
		
		try {
			$objResult = $this->query();
			return $objResult;
		} catch(DBALQueryException $e){
			$this->error($e->getMessage(), $e->getQuery(), $e->getCode());
		}
		
		return false;
	}
	
	public function add_condition($strCondition, $arrParams){
		if(stripos($this->strQuery, 'where') === false){
			$condQuery = str_replace("?", " :cond", $strCondition);
			$this->strQuery.= ' WHERE '.$strCondition;
		} else {
			$this->strQuery.= ' AND '.$strCondition;
		}
		$arrWilcards = array();
		
		$condQuery = str_replace("?", " :cond", $strCondition);
		$intWildcards = preg_match_all("/(\:cond)/", $condQuery, $arrWilcards);
		
		if($intWildcards){
			$this->arrParamsList = array_merge($this->arrParamsList, $arrWilcards[0]);
		}
		
		if (isset($arrParams[0]) && is_array($arrParams[0]))
		{
			$arrParams = array_values($arrParams[0]);
		}
		
		$this->arrParams['conditions'][] = $arrParams;
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
	 * Return the last error message
	 * @return string
	 */
	protected function get_error()
	{
		$arrError = $this->resConnection->errorInfo();
		return $arrError[2];
	}
	
	/**
	 * Return the last error code
	 * @return string
	 */
	protected function get_errno()
	{
		return 0;
		return @$this->resConnection->errorCode();
	}
	
	
	/**
	 * Return the number of affected rows
	 * @return integer
	 */
	protected function affected_rows()
	{
		return @$this->resConnection->affectedRows;
	}
	
	
	/**
	 * Return the last insert ID
	 * @return integer
	 */
	protected function insert_id()
	{
		return @$this->resConnection->lastInsertId();
	}
	
	
	/**
	 * Explain the current query
	 * @return array
	 */
	protected function explain_query()
	{
		return $this->resConnection->query('EXPLAIN ' . $this->strQuery)->fetch(PDO::FETCH_ASSOC);
	}
	
	/**
	 * Create a Database_Result object
	 * @param resource
	 * @param string
	 * @return DB_Mysqli_Result
	 */
	protected function createResult($resResult, $strQuery)
	{
		return new DB_Mysql_PDO_Result($resResult, $strQuery);
	}
}


/**
 * Class DB_Mysqli_Result
 *
 * Driver class for MySQLi databases.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Driver
 */
class DB_Mysql_PDO_Result extends DatabaseResult
{
	
	/**
	 * Fetch the current row as enumerated array
	 * @return array
	 */
	protected function fetch_row()
	{
		return @$this->resResult->fetch(PDO::FETCH_NUM);
	}
	
	
	/**
	 * Fetch the current row as associative array
	 * @return array
	 */
	protected function fetch_assoc()
	{
		return @$this->resResult->fetch(PDO::FETCH_ASSOC);
	}
	
	
	/**
	 * Return the number of rows of the current result
	 * @return integer
	 */
	protected function num_rows()
	{
		return @$this->resResult->rowCount();
	}
	
	
	/**
	 * Return the number of fields of the current result
	 * @return integer
	 */
	protected function num_fields()
	{
		return @$this->resResult->columnCount();
	}
	
	
	/**
	 * Get the column information
	 * @param integer
	 * @return object
	 */
	protected function fetch_field($intOffset)
	{
		return @$this->resResult->getColumnMeta($intOffset);
	}
	
	
	/**
	 * Free the current result
	 */
	public function free()
	{
		if (is_object($this->resResult))
		{
			@$this->resResult = null;
		}
	}
}

?>