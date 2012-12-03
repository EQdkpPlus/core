<?php
/**
 * Project:     EQdkp - Open Source Points System
 * License:     http://eqdkp.com/?p=license
 * -----------------------------------------------------------------------
 * File:        dbal.php
 * Began:       Tue Dec 17 2002
 * Date:        $Date: 2008-03-08 15:29:17 +0000 (Sat, 08 Mar 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: rspeicher $
 * @copyright   2002-2008 The EQdkp Project Team
 * @link        http://eqdkp.com/
 * @package     db
 * @version     $Rev: 516 $
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


    /**
     * Constructor
     */
    function dbal()
    {
        // Fill default sql layer based on the class being called.
        // This can be changed by the specified layer itself later if needed.
        $this->sql_layer = substr(get_class($this), 5);

        // Do not change this please! This variable is used to easy the use of it - and is hardcoded.
        $this->any_char = chr(0) . '%';
        $this->one_char = chr(0) . '_';
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
                // Fall through
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
                    
                    if ( is_null($value) )
                    {
                        $values[] = 'NULL';
                    }
                    elseif ( is_string($value) )
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
        global  $table_prefix, $dbname,$eqdkp_root_path ;
        
		if ($this->transaction)
		{
			$this->sql_transaction('rollback');
		}
		
        $message  = 'SQL ERROR<br /><br />';
        #$message .= 'Query: '   		. (($sql) ? sql_highlight($sql) : 'null') . '<br />';
        $message .= 'Query: '   		. (($sql) ? $sql : 'null') . '<br />';
        $message .= 'Message: ' 		. $error['message'] . '<br />';
        $message .= 'Code: '    		. $error['code'] . '<br />';
        $message .= 'Database : '    	. $dbname . '<br />';
        $message .= 'Table Prefix: '	. $table_prefix . '<br />';
        $message .= 'PHP: '			. phpversion() . ' | Mysql: '. mysql_get_client_info() . '<br /><br />';
        $message .= 'is your EQdkp updated? <a href="'.$eqdkp_root_path.'admin/update.php">click to check</a> <hr/> ';
        
        return $message;
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
?>