<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * mysql.php
 * begin: Tue December 17 2002
 *
 * $Id: mysql.php 62 2007-05-15 18:42:34Z osr-corgan $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

/**
* SQL_DB class, MySQL version
* Abstracts MySQL database functions
*/

define('DBTYPE', 'mysql');

class SQL_DB
{
    var $link_id     = 0;                   // Connection link ID       @var link_id
    var $query_id    = 0;                   // Query ID                 @var query_id
    var $record      = array();             // Record                   @var record
    var $record_set  = array();             // Record set               @var record_set
    var $query_count = 0;                   // Query count              @var query_count
    var $queries     = array();             // Queries                  @var queries
    var $error_die   = true;                // Die on errors?           @var error_die

    /**
    * Constructor
    *
    * Connects to a MySQL database
    *
    * @param $dbhost Database server
    * @param $dbname Database name
    * @param $dbuser Database username
    * @param $dbpass Database password
    * @param $pconnect Use persistent connection
    * @return mixed Link ID / false
    */
    function sql_db($dbhost, $dbname, $dbuser, $dbpass='', $pconnect = false)
    {
        $this->pconnect = $pconnect;
        $this->dbhost = $dbhost;
        $this->dbname = $dbname;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;

        if ( $this->pconnect )
        {
            if ( empty($this->dbpass) )
            {
                $this->link_id = @mysql_pconnect($this->dbhost, $this->dbuser);
            }
            else
            {
                $this->link_id = @mysql_pconnect($this->dbhost, $this->dbuser, $this->dbpass);
            }
        }
        else
        {
            if ( empty($this->dbpass) )
            {
                $this->link_id = @mysql_connect($this->dbhost, $this->dbuser);
            }
            else
            {
                $this->link_id = @mysql_connect($this->dbhost, $this->dbuser, $this->dbpass);
            }
        }

        if ( (is_resource($this->link_id)) && (!is_null($this->link_id)) && ($this->dbname != '') )
        {
            if ( !@mysql_select_db($this->dbname, $this->link_id) )
            {
                @mysql_close($this->link_id);
                $this->link_id = false;
            }
            return $this->link_id;
        }
        else
        {
            return false;
        }
    }

    /**
    * Closes MySQL connection
    *
    * @return bool
    */
    function close_db()
    {
        if ( $this->link_id )
        {
            if ( $this->query_id )
            {
                @mysql_free_result($this->query_id);
            }
            return @mysql_close($this->link_id);
        }
        else
        {
            return false;
        }
    }

    /**
    * Get error information
    *
    * @return array Error information
    */
    function error()
    {
        $result['message'] = @mysql_error();
        $result['code'] = @mysql_errno();

        return $result;
    }

    /**
    * Basic query function
    *
    * @param $query Query string
    * @return mixed Query ID / Error string / Bool
    */
    function query($query)
    {
        // Remove pre-existing query resources
        unset($this->query_id);

        //$query = preg_replace('/;.*$/', '', $query);

        if ( $query != '' )
        {
            $this->query_count++;
            $this->query_id = @mysql_query($query, $this->link_id);
        }
        if ( !empty($this->query_id) )
        {
            $this->queries[$this->query_count] = $query;

            unset($this->record[$this->query_id]);
            unset($this->record_set[$this->query_id]);
            return $this->query_id;
        }
        else
        {
            if ( DEBUG )
            {
                $error = $this->error();
                $message  = 'SQL query error<br /><br />';
                $message .= 'Query: '.$query.'<br />';
                $message .= 'Message: '.$error['message'].'<br />';
                $message .= 'Code: '.$error['code'];

                if ( $this->error_die )
                {
                    #message_die($message);
                }

                return $message;
            }

            return false;
        }
    }

    /**
    * Return the first record (single column) in a query result
    *
    * @param $query Query string
    */
    function query_first($query)
    {
        $this->query($query);
        $record = $this->fetch_record($this->query_id);
        $this->free_result($this->query_id);

        return $record[0];
    }

    /**
    * Build query
    * Ikonboard -> phpBB -> EQdkp
    *
    * @param $query
    * @param $array Array of field => value pairs
    */
    function build_query($query, $array = false)
    {
        if ( !is_array($array) )
        {
            return false;
        }

        $fields = array();
        $values = array();

        if ( $query == 'INSERT' )
        {
            foreach ( $array as $field => $value )
            {
                $fields[] = $field;

                if ( is_null($value) )
                {
                    $values[] = 'NULL';
                }
                elseif ( is_string($value) )
                {
                    $values[] = "'" . $this->escape($value) . "'";
                }
                else
                {
                    $values[] = ( is_bool($value) ) ? intval($value) : $value;
                }
            }

            $query = ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
        }
        elseif ( $query == 'UPDATE' )
        {
            foreach ( $array as $field => $value )
            {
                if ( is_null($value) )
                {
                    $values[] = "$field = NULL";
                }
                elseif ( is_string($value) )
                {
                    $values[] = "$field = '" . $this->escape($value) . "'";
                }
                else
                {
                    $values[] = ( is_bool($value) ) ? "$field = " . intval($value) : "$field = $value";
                }
            }

            $query = implode(', ', $values);
        }

        return $query;
    }

    /**
    * Fetch a record
    *
    * @param $query_id Query ID
    * @return mixed Record / false
    */
    function fetch_record($query_id = 0)
    {
        if ( !$query_id )
        {
            $query_id = $this->query_id;
        }

        if ( $query_id )
        {
            $this->record[$query_id] = @mysql_fetch_array($query_id);
            return $this->record[$query_id];
        }
        else
        {
            return false;
        }
    }

    /**
    * Fetch a record set
    *
    * @param $query_id Query ID
    * @return mixed Record Set / false
    */
    function fetch_record_set($query_id = 0)
    {
        if ( !$query_id )
        {
            $query_id = $this->query_id;
        }
        if ( $query_id )
        {
            unset($this->record_set[$query_id]);
            unset($this->record[$query_id]);
            while ( $this->record_set[$query_id] = @mysql_fetch_array($query_id) )
            {
                $result[] = $this->record_set[$query_id];
            }
            return $result;
        }
        else
        {
            return false;
        }
    }

    /**
    * Find the number of returned rows
    *
    * @param $query_id Query ID
    * @return mixed Number of rows / false
    */
    function num_rows($query_id = 0)
    {
        if ( !$query_id )
        {
            $query_id = $this->query_id;
        }

        if ( $query_id )
        {
            $result = @mysql_num_rows($query_id);
            return $result;
        }
        else
        {
            return false;
        }
    }

    /**
    * Finds out the number of rows affected by a query
    *
    * @return mixed Affected Rows / false
    */
    function affected_rows()
    {
        return ( $this->link_id ) ? @mysql_affected_rows($this->link_id) : false;
    }

    /**
    * Find the ID of the row that was just inserted
    *
    * @return mixed Last ID / false
    */
    function insert_id()
    {
        if ( $this->link_id )
        {
            $result = @mysql_insert_id($this->link_id);
            return $result;
        }
        else
        {
            return false;
        }
    }

    /**
    * Free result data
    *
    * @param $query_id Query ID
    * @return bool
    */
    function free_result($query_id = 0)
    {
        if ( !$query_id )
        {
            $query_id = $this->query_id;
        }

        if ( $query_id )
        {
            unset($this->record[$query_id]);
            unset($this->record_set[$query_id]);

            @mysql_free_result($query_id);

            return true;
        }
        else
        {
            return false;
        }
    }

    /**
    * Remove quote escape
    *
    * @param $string
    * @return string
    */
    function escape($string)
    {
        $string = str_replace("'", "''",    $string);
        $string = str_replace('\\', '\\\\', $string);

        return $string;
    }

    /**
    * Set the error_die var
    *
    * @param $setting
    */
    function error_die($setting = true)
    {
        $this->error_die = $setting;
    }
}
?>
