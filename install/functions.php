<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * functions.php
 * begin: 30.11.2007
 *
 * $Id: $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
// define debug to reduce errors:
define('DEBUG', 0);

/**
* Returns an array of available DBMS with some data, if a DBMS is specified it will only
* return data for that DBMS and will load its extension if necessary.
*/
function get_available_dbms($dbms = false, $return_unavailable = false)
{
    global $lang;

    $available_dbms = array(
        'mysql'       => array(
            'LABEL'         => 'MySQL',
            'SCHEMA'        => 'mysql',
            'MODULE'        => 'mysql', 
            'DELIM'         => ';',
            'COMMENTS'      => 'remove_remarks',
            'DRIVER'        => 'mysql',
            'AVAILABLE'     => true,
        ),
    );

    if ($dbms)
    {
        if (isset($available_dbms[$dbms]))
        {
            $available_dbms = array($dbms => $available_dbms[$dbms]);
        }
        else
        {
            return array();
        }
    }

    // now perform some checks whether they are really available
    foreach ($available_dbms as $db_name => $db_ary)
    {
        $dll = $db_ary['MODULE'];

        if (!@extension_loaded($dll))
        {
            if (!can_load_dll($dll))
            {
                if ($return_unavailable)
                {
                    $available_dbms[$db_name]['AVAILABLE'] = false;
                }
                else
                {
                    unset($available_dbms[$db_name]);
                }
                continue;
            }
        }
        $any_db_support = true;
    }

    if ($return_unavailable)
    {
        $available_dbms['ANY_DB_SUPPORT'] = $any_db_support;
    }
    return $available_dbms;
}

/**
* Used to test whether we are able to connect to the database the user has specified
* and identify any problems (eg there are already tables with the names we want to use
* @param    array    $dbms should be of the format of an element of the array returned by {@link get_available_dbms get_available_dbms()}
*                    necessary extensions should be loaded already
*/
function connect_check_db($error_connect, &$error, $dbms, $table_prefix, $dbhost, $dbuser, $dbpasswd, $dbname, $dbport, $prefix_may_exist = false, $load_dbal = true, $unicode_check = true)
{
    global $eqdkp_root_path, $config, $lang;


    if ($load_dbal)
    {
        // Include the DB layer
        include_once($eqdkp_root_path . 'includes/db/' . $dbms['DRIVER'] . '.php');
    }

    // Instantiate it and set return on error true
    // Note to self: If general dbal class made, and each subclass has prefix, add here.
    $sql_db = 'dbal_' . $dbms['DRIVER'];
    $db = new $sql_db();
    $db->error_die(false);

    // Check that we actually have a database name before going any further.....
    if ($dbms['DRIVER'] != 'sqlite' && $dbms['DRIVER'] != 'oracle' && $dbname === '')
    {
        $error[] = $lang['INST_ERR_DB_NO_NAME'];
        return false;
    }

    // Make sure we don't have a daft user who thinks having the SQLite database in the forum directory is a good idea
/*    if ($dbms['DRIVER'] == 'sqlite' && stripos(eqdkp_realpath($dbhost), eqdkp_realpath('../')) === 0)
    {
        $error[] = $lang['INST_ERR_DB_FORUM_PATH'];
        return false;
    }
*/
    // Check the prefix length to ensure that index names are not too long and does not contain invalid characters
    switch ($dbms['DRIVER'])
    {
        case 'mysql':
        case 'mysqli':
            if (strpos($table_prefix, '-') !== false || strpos($table_prefix, '.') !== false || strpos($table_prefix, "'") !== false || strpos($table_prefix, '\\') !== false || strpos($table_prefix, '/') !== false)
            {
                $error[] = $lang['INST_ERR_PREFIX_INVALID'];
                return false;
            }

            $prefix_length = 36;
        break;
    }

    if (strlen($table_prefix) > $prefix_length)
    {
        $error[] = sprintf($lang['INST_ERR_PREFIX_TOO_LONG'], $prefix_length);
        return false;
    }

    // Try and connect ...
    // NOTE: EQdkp's sql_connect function returns a resource (a link ID) if the connection was successful.
    $connect_test = $db->sql_connect($dbhost, $dbname, $dbuser, $dbpasswd, false);
	
    if ($connect_test === false || !is_resource($connect_test))
    {
        $db_error = $db->error();
        $error[] = $lang['INST_ERR_DB_CONNECT'] . '<br />' . (($db_error['message']) ? $db_error['message'] : $lang['INST_ERR_DB_NO_ERROR']);
    }
    else
    {
        // Likely matches for an existing eqdkp installation
        if (!$prefix_may_exist)
        {
            $temp_prefix = strtolower($table_prefix);
            $table_ary = array($temp_prefix . 'raids', $temp_prefix . 'raid_attendees', $temp_prefix . 'config', $temp_prefix . 'sessions', $temp_prefix . 'users');

            $tables = get_tables($db);
            $tables = array_map('strtolower', $tables);
            $table_intersect = array_intersect($tables, $table_ary);

            if (sizeof($table_intersect))
            {
                $error[] = $lang['INST_ERR_PREFIX'];
            }
        }

        // Make sure that the user has selected a sensible DBAL for the DBMS actually installed
        switch ($dbms['DRIVER'])
        {
            case 'mysqli':
                if (version_compare(mysqli_get_server_info($db->db_connect_id), '4.1.3', '<'))
                {
                    $error[] = $lang['INST_ERR_DB_NO_MYSQLI'];
                }
            break;
        }

    }

    if ($error_connect && (!isset($error) || !sizeof($error)))
    {
        return true;
    }
    return false;
}

/**
* Get tables of a database
*/
function get_tables($db)
{
    switch ($db->sql_layer)
    {
        case 'mysql':
        case 'mysql4':
        case 'mysqli':
            $sql = 'SHOW TABLES';
        break;
    }

    $result = $db->query($sql);

    $tables = array();

    while ($row = $db->fetch_record($result))
    {
        $tables[] = current($row);
    }

    $db->free_result($result);

    return $tables;
}

/**
* Parse multi-line SQL statements into a single line
*
* @param    string  $sql    SQL file contents
* @param    char    $delim  End-of-statement SQL delimiter
* @return   array
*/
function parse_sql($sql, $delim)
{
  global $lang;
    if ( $sql == '' )
    {
        die($lang['error_nostructure']);
    }

    $retval     = array();
    $statements = explode($delim, $sql);
    unset($sql);

    $linecount = count($statements);
    for ( $i = 0; $i < $linecount; $i++ )
    {
        if ( ($i != $linecount - 1) || (strlen($statements[$i]) > 0) )
        {
            $statements[$i] = trim($statements[$i]);
            $statements[$i] = str_replace("\r\n", '', $statements[$i]) . "\n";

            // Remove 2 or more spaces
            $statements[$i] = preg_replace('#\s{2,}#', ' ', $statements[$i]);

            $retval[] = trim($statements[$i]);
        }
    }
    unset($statements);

    return $retval;
}

/**
* Removes comments from a SQL data file
*
* @param    string  $sql    SQL file contents
* @return   string
*/
function remove_remarks($sql)
{
  global $lang;
    if ( $sql == '' )
    {
        die($lang['error_nostructure']);
    }

    $retval = '';
    $lines  = explode("\n", $sql);
    unset($sql);

    foreach ( $lines as $line )
    {
        // Only parse this line if there's something on it, and we're not on the last line
        if ( strlen($line) > 0 )
        {
            // If '#' is the first character, strip the line
            $retval .= ( substr($line, 0, 1) != '#' ) ? $line . "\n" : "\n";
        }
    }
    unset($lines, $line);

    return $retval;
}

/**
* Set $config_name to $config_value in CONFIG_TABLE
*
* @param    mixed   $config_name    Config name, or associative array of name => value pairs
* @param    string  $config_value
* @return   bool
*/
function config_set($config_name, $config_value='', $db = null)
{
    if ( is_null($db) )
    {
        global $db;
    }

    if ( is_object($db) )
    {
        if ( is_array($config_name) )
        {
            foreach ( $config_name as $d_name => $d_value )
            {
                config_set($d_name, $d_value);
            }
        }
        else
        {
            if ( $config_value == '' )
            {
                return false;
            }

            $sql = 'UPDATE ' . CONFIG_TABLE . "
                    SET config_value='" . strip_tags(htmlspecialchars($config_value)) . "'
                    WHERE config_name='" . $config_name . "'";
            $db->query($sql);

            return true;
        }
    }

    return false;
}

/**
* Applies addslashes() to the provided data
*
* @param    mixed   $data   Array of data or a single string
* @return   mixed           Array or string of data
*/
function slash_global_data(&$data)
{
    if ( is_array($data) )
    {
        foreach ( $data as $k => $v )
        {
            $data[$k] = ( is_array($v) ) ? slash_global_data($v) : addslashes($v);
        }
    }
    return $data;
}

function SetLanguageCookie($value){
	setcookie("eqdkpInstLanguage", $value, time()+(60*60*24*30*12), "");
}

?>
