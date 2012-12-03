<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2003
 * Date:		$Date: 2010-08-11 01:26:21 +0200 (Wed, 11 Aug 2010) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 8532 $
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

// define debug to reduce errors:
define('DEBUG', 0);

/**
* Returns an array of available DBMS with some data, if a DBMS is specified it will only
* return data for that DBMS and will load its extension if necessary.
*/
function get_available_dbms($dbms = false, $return_unavailable = false){
	global $lang;

	$available_dbms = array(
		'mysql'		=> array(
			'LABEL'			=> 'MySQL',
			'SCHEMA'		=> 'mysql',
			'MODULE'		=> 'mysql', 
			'DELIM'			=> ';',
			'COMMENTS'		=> 'remove_remarks',
			'DRIVER'		=> 'mysql',
			'AVAILABLE'		=> true,
		),
	);

	if ($dbms){
		if (isset($available_dbms[$dbms])){
			$available_dbms = array($dbms => $available_dbms[$dbms]);
		}else{
			return array();
		}
	}

	// now perform some checks whether they are really available
	foreach ($available_dbms as $db_name => $db_ary){
		$dll = $db_ary['MODULE'];
		if (!@extension_loaded($dll)){
			if (!can_load_dll($dll)){
				if ($return_unavailable){
					$available_dbms[$db_name]['AVAILABLE'] = false;
				}else{
					unset($available_dbms[$db_name]);
				}
				continue;
			}
		}
		$any_db_support = true;
	}

	if ($return_unavailable){
		$available_dbms['ANY_DB_SUPPORT'] = $any_db_support;
	}
	return $available_dbms;
}

/**
* Parse multi-line SQL statements into a single line
*
* @param    string  $sql    SQL file contents
* @param    char    $delim  End-of-statement SQL delimiter
* @return   array
*/
function parse_sql($sql, $delim){
	global $lang;
	if ( $sql == '' ){
		die($lang['error_nostructure']);
	}

	$retval     = array();
	$statements = explode($delim, $sql);
	unset($sql);

	$linecount = count($statements);
	for ( $i = 0; $i < $linecount; $i++ ){
		if ( ($i != $linecount - 1) || (strlen($statements[$i]) > 0) ){
			$statements[$i] = trim($statements[$i]);
			$statements[$i] = str_replace("\r\n", '', $statements[$i]) . "\n";
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
function remove_remarks($sql){
	global $lang;
	if ( $sql == '' ){
		die($lang['error_nostructure']);
	}

	$retval = '';
	$lines  = explode("\n", $sql);
	unset($sql);

	foreach ( $lines as $line ){
		// Only parse this line if there's something on it, and we're not on the last line
		if ( strlen($line) > 0 ){
			// If '#' is the first character, strip the line
			$retval .= ( substr($line, 0, 1) != '#' ) ? $line . "\n" : "\n";
		}
	}
	unset($lines, $line);
	return $retval;
}

/**
* Applies addslashes() to the provided data
*
* @param    mixed   $data   Array of data or a single string
* @return   mixed           Array or string of data
*/
function slash_global_data(&$data){
	if ( is_array($data) ){
		foreach ( $data as $k => $v ){
			$data[$k] = ( is_array($v) ) ? slash_global_data($v) : addslashes($v);
		}
	}
	return $data;
}

function SetLanguageCookie($value){
	setcookie("eqdkpInstLanguage", $value, time()+(60*60*24*30*12), "");
}

function GenerateCookieHash(){
	$hash = md5(rand().rand().rand());
	$value = substr($hash, 4, 6);
	return $value;
}

function encrypt_password($string, $salt = ''){
		return hash('sha256', $salt . $string);
}

function generate_salt(){
	return substr(md5(uniqid('', true)), 0, 23);		 
}

function InsertGroupPermissions($group_id, $permissions = false, $without_permissions = false){
	global $db;
	 $sql = 'SELECT auth_id, auth_value, auth_default
			FROM __auth_options
			ORDER BY auth_id';
	$result = $db->query($sql);
	while ( $row = $db->fetch_record($result) ) {
		$auth_defaults[ $row['auth_value'] ] = $row['auth_value'];
		$auth_ids[$row['auth_value']] = $row['auth_id'];
	}
	
	if ($permissions != false){
		foreach ($permissions as $key=>$value){
			$db->query("INSERT __auth_groups (group_id, auth_id, auth_setting) VALUES (".$db->escape($group_id).", ".$db->escape($auth_ids[$value]).", 'Y')");
		}
	} else {
		foreach ($without_permissions as $key=>$value){
			$without_perms[$value] = $value;
		}

		foreach ($auth_defaults as $key=>$value){
			if (!isset($without_perms[$value])){	
				$db->query("INSERT __auth_groups (group_id, auth_id, auth_setting) VALUES (".$db->escape($group_id).", ".$db->escape($auth_ids[$value]).", 'Y')");
			}
		}
	}
}

?>