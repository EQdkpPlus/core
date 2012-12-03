<?php
/**
 * Project:     EQdkp - Open Source Points System
 * License:     http://eqdkp.com/?p=license
 * -----------------------------------------------------------------------
 * File:        dbal.php
 * Began:       Tue Dec 17 2002
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2002-2008 The EQdkp Project Team
 * @link        http://eqdkp.com/
 * @package     db
 * @version     $Rev$
 */


if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

/**
 * Database Abstraction Layer
 *
 * @abstract
 */
class dbal
{
    var $link_id     = 0;                   // @var    int        $link_id         connection link ID for a database connection resource
    var $query_id    = 0;                   // @var    int        $query_id        query ID
    var $record      = array();             // @var    array      $record          Record
    var $record_set  = array();             // @var    array      $record_set      Record set
    var $queries     = array();             // @var    array      $queries         the result of all queries run
    var $error_die   = true;                // @var    bool       $error_die       die on errors (true) or allow the script to run (false)?

    /**
     * Current sql layer
     */
    var $sql_layer = '';

	// Transactions
	var $transaction       = false;
	var $transaction_count = 0;

    /**
     * Wildcards for matching any (%) or exactly one (_) character within LIKE expressions
     */
    var $any_char;
    var $one_char;

    var $debug_prefix = '';
    var $pdl = false;
    var $root_path = false;
    var $table_prefix = false;

    /**
     * Constructor
     */
    function dbal($debug_prefix = '', $pdl=false, $root_path=false, $table_prefix=false)
    {
    	if(!$root_path) {
    		global $eqdkp_root_path;
    	}
    	$this->root_path = $eqdkp_root_path;
    	if(!$pdl) {
    		global $pdl;
    	}
    	$this->pdl = $pdl;
    	if(!$table_prefix) {
    		global $table_prefix;
    	}
    	$this->table_prefix = $table_prefix;
        // Fill default sql layer based on the class being called.
        // This can be changed by the specified layer itself later if needed.
        $this->sql_layer = substr(get_class($this), 5);

        // Do not change this please! This variable is used to easy the use of it - and is hardcoded.
        $this->any_char = chr(0) . '%';
        $this->one_char = chr(0) . '_';

        //pdl error stuff
        $this->debug_prefix = $debug_prefix;
        if(!$pdl->type_known($this->debug_prefix."sql_error"))
          $pdl->register_type($this->debug_prefix."sql_error", array($this, 'pdl_pt_format_sql_error'), array($this, 'pdl_html_format_sql_error'), array(2,3), true);
        if(!$pdl->type_known($this->debug_prefix."sql_query"))
          $pdl->register_type($this->debug_prefix."sql_query", null, array($this, 'pdl_html_format_sql_query'), array(2,3));
    }
	
	/**
	 * Destructor
	 */
	public function __destruct() {
		$this->sql_close();
	}

    /**
     * pdl html format function for sql queries
     */
    function pdl_html_format_sql_query($log_entry){
      $text = '';
      $text = $this->sql_highlight(htmlentities(wordwrap($log_entry['args'][0],120,"\n",true)));
      return $text;
    }

    /**
     * pdl html format function for sql errors
     */
    function pdl_html_format_sql_error($log_entry){
      $text =  '<b>Query:</b>'   		  . $log_entry['args'][0] . '<br /><br />
                <b>Message:</b> ' 		. $log_entry['args'][1] . '<br /><br />
                <b>Code:</b>'    		  . $log_entry['args'][2] . '<br />
                <b>Database:</b>'   	. $log_entry['args'][3] . '<br />
                <b>Table Prefix:</b>'	. $log_entry['args'][4] . '<br />           
                <b>PHP:</b>'			. phpversion() . ' | Mysql: '. mysql_get_client_info() . '<br /><br />
                is your EQdkp updated? <a href="'.$this->root_path.'admin/update.php">click to check</a> ';
      return $text;
    }

        /**
     * pdl plaintext (logfile) format function for sql errors
     */
    function pdl_pt_format_sql_error($log_entry){ 
      return 'Qry: '.$log_entry['args'][0]."\tMsg: ".$log_entry['args'][1]."\tCode: ".$log_entry['args'][2]."\tDB: ".$log_entry['args'][3]."\tPrfx: ".$log_entry['args'][4]."\tTrace:\n".$log_entry['args'][5];
    }
    /**
		* Highlight certain keywords in a SQL query
		*
		* @param $sql Query string
		* @return string Highlighted string
		*/
    function sql_highlight($sql){

	    //shorten really long queries (e.g. gzipped cache updates)
	    if(strlen($sql) > 1000){
	      $sql = substr($sql, 0, 1000).' (...)';
	    }

	    // Make table names bold
	    $sql = preg_replace('/' . $this->table_prefix .'(\S+?)([\s\.,]|$)/', '<b>' . $this->table_prefix . "\\1\\2</b>", $sql);

	    // Non-passive keywords
	    $red_keywords = array('/(INSERT INTO)/','/(UPDATE\s+)/','/(DELETE FROM\s+)/', '/(CREATE TABLE)/', '/(IF (NOT)? EXISTS)/',
	                          '/(ALTER TABLE)/', '/(CHANGE)/');
	    $red_replace = array_fill(0, sizeof($red_keywords), '<span class="negative">\\1</span>');
	    $sql = preg_replace($red_keywords, $red_replace, $sql);

	    // Passive keywords
	    $green_keywords = array('/(SELECT)/','/(FROM)/','/(WHERE)/','/(LIMIT)/','/(ORDER BY)/','/(GROUP BY)/',
	                            '/(\s+AND\s+)/','/(\s+OR\s+)/','/(BETWEEN)/','/(DESC)/','/(LEFT JOIN)/',
	                            '/(LIKE)/', '/(SHOW TABLE STATUS)/', '/(SHOW)/');

	    $green_replace = array_fill(0, sizeof($green_keywords), '<span class="positive">\\1</span>');
	    $sql = preg_replace($green_keywords, $green_replace, $sql);

	    return $sql;
		}

    /**
     * Determine whether execution should halt on an error (true) or continue (false)
     */
    function error_die($setting = true)
    {
        $this->error_die = $setting;
    }

    /**
     * DBAL garbage collection, close sql connection
     */
    function sql_close()
    {
        return $this->_sql_close();
    }

    /**
     * Build query
     * Ikonboard -> phpBB -> EQdkp
     *
     * @param     string     $query        Type of query to build, either INSERT or UPDATE
     * @param     array      $array        Array of field => value pairs
     * @return    string                   A SQL string fragment
     */
    function sql_build_query($query, $array = false)
    {
        if ( !(is_array($array) && count($array) > 0) )
        {
            return false;
        }

        $fields = array();
        $values = array();

        switch ($query)
        {
            case 'REPLACE':
            case 'INSERT':
                // Returns a string in the form: (<field1>, <field2> ...) VALUES ('<value1>', <(int)value2>, ...)
                foreach ( $array as $field => $value )
                {
                    // Hack to prevent assigning $array directly from a fetch_record call
                    // injecting number-based indices into the built query
                    if ( is_numeric($field) )
                    {
                        continue;
                    }

                    $fields[] = $field;

                    if (is_null($value) )
                    {
                        $values[] = 'NULL';
                    }
                    elseif (is_int($value) )
                    {
                        $values[] = $value;
                    }
                    elseif (is_string($value) )
                    {
                        $values[] = "'" . $this->sql_escape($value) . "'";
                    }
                    else
                    {
                        $values[] = "'" . (( is_bool($value) ) ? intval($value) : $value) . "'";
                    }
                }

                $query = ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';

            break;

            case 'UPDATE':
                // Returns a string in the form: <field1> = '<value1>', <field2> = <(int)value2>, ...
                foreach ( $array as $field => $value )
                {
                    // Hack to prevent assigning $array directly from a fetch_record call
                    // injecting number-based indices into the built query
                    if ( is_numeric($field) )
                    {
                        continue;
                    }

                    if ( is_null($value) )
                    {
                        $values[] = "$field = NULL";
                    }
                    elseif ( is_string($value) )
                    {
                        $values[] = "$field = '" . $this->sql_escape($value) . "'";
                    }
                    else
                    {
                        $values[] = ( is_bool($value) ) ? "$field = '" . intval($value) . "'" : "{$field} = '{$value}'";
                    }
                }

                $query = implode(', ', $values);
            break;

            default:
                return false;
            break;
        }

        return $query;
    }

	/**
	* SQL Transaction
	*
	*/
	function sql_transaction($status = 'begin')
	{
		switch ($status)
		{
			case 'begin':
				// If we are within a transaction we will not open another one, but enclose the current one to not loose data (prevening auto commit)
				if ($this->transaction)
				{
					$this->transaction_count++;
					return true;
				}

				$result = $this->_sql_transaction('begin');

				if (!$result)
				{
					$this->sql_error();
				}

				$this->transaction = true;
			break;

			case 'commit':
				// If there was a previously opened transaction we do not commit yet... but count back the number of inner transactions
				if ($this->transaction && $this->transaction_count)
				{
					$this->transactions--;
					return true;
				}

				$result = $this->_sql_transaction('commit');

				if (!$result)
				{
					$this->sql_error();
				}

				$this->transaction = false;
				$this->transaction_count = 0;
			break;

			case 'rollback':
				$result = $this->_sql_transaction('rollback');
				$this->transaction = false;
				$this->transaction_count = 0;
			break;

			default:
				$result = $this->_sql_transaction($status);
			break;
		}

		return $result;
	}

    /**
     * display sql error page
     */
    function sql_error($sql = '')
    {
        $error = $this->_sql_error();
        global  $dbname, $user, $core;
        static $sys_message=false;
        static $maintenance=false;
        if(is_object($core)) {
        	$maintenance = $core->config['pk_maintenance_mode'];
        } else {
        	$maintenance = true;
        }

        if ($this->transaction)
        {
        	$this->sql_transaction('rollback');
        }

        if(is_object($user) && (!$sys_message) && !$maintenance && is_object($acl))
        {
        	if($user->check_auth('a_',false))
        	{
        		$core->message("<b>SQL Error</b> <br> <a href=".$PHP_SELF."?debug=3>Click for more infos</a>", 'Error', 'red');
        		$sys_message = true ;
        	}
        }
        $exception = new Exception();
        $this->pdl->log($this->debug_prefix."sql_error",$sql, $error['message'], $error['code'], $dbname, $this->table_prefix, $exception->getTraceAsString());
    }
    
    /**
		* Used to test whether we are able to connect to the database the user has specified
		* and identify any problems (eg there are already tables with the names we want to use
		* @param    array    $dbms should be of the format of an element of the array returned by {@link get_available_dbms get_available_dbms()}
		*                    necessary extensions should be loaded already
		*/
		function check_connection($error_connect, &$error, $dbms, $table_prefix, $dbhost, $dbuser, $dbpasswd, $dbname, $prefix_may_exist=false){
			global $eqdkp_root_path, $config, $lang;

			// Check that we actually have a database name before going any further.....
			if ($dbms['DRIVER'] != 'sqlite' && $dbms['DRIVER'] != 'oracle' && $dbname === ''){
				$error[] = $lang['INST_ERR_DB_NO_NAME'];
				return false;
			}
			
			// Check the prefix length to ensure that index names are not too long and does not contain invalid characters
			switch ($dbms['DRIVER']){
				case 'mysql':
				case 'mysqli':
					if (strpos($table_prefix, '-') !== false || strpos($table_prefix, '.') !== false || strpos($table_prefix, "'") !== false || strpos($table_prefix, '\\') !== false || strpos($table_prefix, '/') !== false){
						$error[] = $lang['INST_ERR_PREFIX_INVALID'];
						return false;
					}
					$prefix_length = 36;
				break;
			}
		
			if (strlen($table_prefix) > $prefix_length){
				$error[] = sprintf($lang['INST_ERR_PREFIX_TOO_LONG'], $prefix_length);
				return false;
			}
		
			// Try and connect ...
			$connect_test = $this->sql_connect($dbhost, $dbname, $dbuser, $dbpasswd, false);
			if ($connect_test === false || !is_resource($connect_test)){
				$db_error = $this->error();
				$error[]	= $lang['INST_ERR_DB_CONNECT'] . '<br />' . (($db_error['message']) ? $db_error['message'] : $lang['INST_ERR_DB_NO_ERROR']);
			}else{
				// Likely matches for an existing eqdkp installation
				if (!$prefix_may_exist){
					$temp_prefix			= strtolower($table_prefix);
					$table_ary				= array($temp_prefix . 'raids', $temp_prefix . 'raid_attendees', $temp_prefix . 'config', $temp_prefix . 'sessions', $temp_prefix . 'users');
					$tables						= $this->get_tables($this);
					$tables						= array_map('strtolower', $tables);
					$table_intersect	= array_intersect($tables, $table_ary);
		
					if (sizeof($table_intersect)){
						$error[] = $lang['INST_ERR_PREFIX'];
					}
				}
		
				// Make sure that the user has selected a sensible DBAL for the DBMS actually installed
				switch ($dbms['DRIVER']){
			case 'mysqli':
				if (version_compare(mysqli_get_server_info($db->db_connect_id), '4.1.3', '<')){
					$error[] = $lang['INST_ERR_DB_NO_MYSQLI'];
				}
			break;
		}
			}
		
			if ($error_connect && (!isset($error) || !sizeof($error))){
				return true;
			}
			return false;
		}


    /**
     * Implode an array of strings with a given delimiter after calling {@link escape} on each element
     *
     * @param     string     $delim        the delimiter to call implode() with
     * @param     array      $array        Array of strings to escape and join together
     * @return    array
     * @access    private
     */
    function _implode($delim, $array)
    {
        if ( !is_array($array) || count($array) == 0 )
        {
            return '';
        }

        foreach ( $array as $k => $v )
        {
            $array[$k] = $this->sql_escape($v);
        }

        return implode($delim, $array);
    }
	
	function get_tables($db){
	switch ($db->sql_layer){
		case 'mysql':
		case 'mysql4':
		case 'mysqli':
			$sql = 'SHOW TABLES';
		break;
	}
	$result = $db->query($sql);
	$tables = array();

	while ($row = $db->fetch_record($result)){
		$tables[] = current($row);
	}
	$db->free_result($result);
	return $tables;
}
		

/*
 * Deprecated Methods.
 *
 * These methods will disappear in a few versions' time. Please ensure your code uses the new method names!
 */

    // sql_build_query
    function build_query($query, $array = false)
    {
        return $this->sql_build_query($query, $array);
    }
}

// This variable holds the class name to use later
$sql_db = 'dbal_' . $dbms;
