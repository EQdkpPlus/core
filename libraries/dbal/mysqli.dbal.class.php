<?php 

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    System
 * @license    LGPL
 * @filesource
 */


/**
 * Class DB_Mysqli
 *
 * Driver class for MySQLi databases.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Driver
 */
class dbal_mysqli extends Database
{
	
	protected $strDbalName = 'mysql'; // equals php extension
	protected $strDbmsName = 'MySQL'; // user visible name
	
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
		$intPort = ($intPort !== false) ? $intPort : ini_get("mysqli.default_port");
		if($blnPersistent) $strHost = 'p:'.$strHost;
		
		@$this->resConnection = new mysqli($strHost, $strUser, $strPassword, $strDatabase, $intPort);
		if (@$this->resConnection->connect_error != ""){
			throw new DBALException(@$this->resConnection->connect_error);
		}
		@$this->resConnection->set_charset($this->strCharset);
		$this->strDatabase = $strDatabase;
	}


	/**
	 * Disconnect from the database
	 */
	protected function disconnect()
	{
		@$this->resConnection->close();
	}
	
	protected function get_client_version() {
		return @$this->resConnection->get_client_info();
	}
	
	protected function get_server_version(){
		return @$this->resConnection->get_server_info();
	}

	/**
	 * Return the last error message
	 * @return string
	 */
	protected function get_error()
	{
		return @$this->resConnection->error;
	}
	
	protected  function get_errno(){
		return @$this->resConnection->errno;
	}
	
	protected function get_connerror(){
		return @$this->resConnection->connect_error;
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
			return "FIND_IN_SET(" . $strKey . ", '" . $this->resConnection->real_escape_string($strSet) . "')";
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
		$intPort = (registry::get_const("dbport") !== null) ? registry::get_const("dbport") : ini_get("mysqli.default_port");
		if (is_object($this->resConnection)) @$this->resConnection->close();		
		@$this->resConnection = new mysqli(registry::get_const("dbhost"), registry::get_const("dbuser"), registry::get_const("dbpass"), $strDatabase, $intPort);
	}


	/**
	 * Begin a transaction
	 */
	protected function begin_transaction()
	{
		@$this->resConnection->query("SET AUTOCOMMIT=0");
		@$this->resConnection->query("BEGIN");
	}


	/**
	 * Commit a transaction
	 */
	protected function commit_transaction()
	{
		@$this->resConnection->query("COMMIT");
		@$this->resConnection->query("SET AUTOCOMMIT=1");
	}


	/**
	 * Rollback a transaction
	 */
	protected function rollback_transaction()
	{
		@$this->resConnection->query("ROLLBACK");
		@$this->resConnection->query("SET AUTOCOMMIT=1");
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

		@$this->resConnection->query("LOCK TABLES " . implode(', ', $arrLocks));
	}


	/**
	 * Unlock all tables
	 */
	protected function unlock_tables()
	{
		@$this->resConnection->query("UNLOCK TABLES");
	}


	/**
	 * Return the table size in bytes
	 * @param string
	 * @return integer
	 */
	protected function get_size_of($strTable)
	{
		$objStatus = @$this->resConnection->query("SHOW TABLE STATUS LIKE '" . $strTable . "'")
										  ->fetch_object();

		return ($objStatus->Data_length + $objStatus->Index_length);
	}
	
	protected function field_information($strTable){
		$objStatus = @$this->resConnection->query("SHOW TABLE STATUS LIKE '" . $strTable . "'")
		->fetch_object();
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
										  ->fetch_object();

		return $objStatus->Auto_increment;
	}


	/**
	 * Create a Database_Statement object
	 * @param resource
	 * @param boolean
	 * @return DB_Mysqli_Statement
	 */
	protected function createStatement($resConnection, $strTablePrefix, $strDebugPrefix, $blnDisableAutocommit)
	{
		return new DB_Mysqli_Statement($resConnection, $strTablePrefix, $strDebugPrefix, $blnDisableAutocommit);
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
 * Class DB_Mysqli_Statement
 *
 * Driver class for MySQLi databases.
 * @copyright  Leo Feyer 2005-2012
 * @author     Leo Feyer <http://www.contao.org>
 * @package    Driver
 */
class DB_Mysqli_Statement extends DatabaseStatement
{

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
		return "'" . $this->resConnection->real_escape_string($strString) . "'";
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
		
		$this->strQuery  = preg_replace("/([^\w]|^)__(\w)/", '$1'.$this->strTablePrefix.'$2', $this->strQuery);

		// Log the Query
		$this->objLogger->log($this->strDebugPrefix . 'sql_query', $this->strQuery);
		
		return @$this->resConnection->query($this->strQuery);
	}


	/**
	 * Return the last error message
	 * @return string
	 */
	protected function get_error()
	{
		return @$this->resConnection->error;
	}
	
	/**
	 * Return the last error code
	 * @return string
	 */
	protected function get_errno()
	{
		return @$this->resConnection->errno;
	}


	/**
	 * Return the number of affected rows
	 * @return integer
	 */
	protected function affected_rows()
	{
		return @$this->resConnection->affected_rows;
	}


	/**
	 * Return the last insert ID
	 * @return integer
	 */
	protected function insert_id()
	{
		return @$this->resConnection->insert_id;
	}


	/**
	 * Explain the current query
	 * @return array
	 */
	protected function explain_query()
	{
		return @$this->resConnection->query('EXPLAIN ' . $this->strQuery)->fetch_assoc();
	}

	/**
	 * Create a Database_Result object
	 * @param resource
	 * @param string
	 * @return DB_Mysqli_Result
	 */
	protected function createResult($resResult, $strQuery)
	{
		return new DB_Mysqli_Result($resResult, $strQuery);
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
class DB_Mysqli_Result extends DatabaseResult
{

	/**
	 * Fetch the current row as enumerated array
	 * @return array
	 */
	protected function fetch_row()
	{
		return @$this->resResult->fetch_row();
	}


	/**
	 * Fetch the current row as associative array
	 * @return array
	 */
	protected function fetch_assoc()
	{
		return @$this->resResult->fetch_assoc();
	}


	/**
	 * Return the number of rows of the current result
	 * @return integer
	 */
	protected function num_rows()
	{
		return @$this->resResult->num_rows;
	}


	/**
	 * Return the number of fields of the current result
	 * @return integer
	 */
	protected function num_fields()
	{
		return @$this->resResult->field_countmysql;
	}


	/**
	 * Get the column information
	 * @param integer
	 * @return object
	 */
	protected function fetch_field($intOffset)
	{
		return @$this->resResult->fetch_field_direct($intOffset);
	}


	/**
	 * Free the current result
	 */
	public function free()
	{
		if (is_object($this->resResult))
		{
			@$this->resResult->free();
		}
	}
	}

?>