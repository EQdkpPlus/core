<?php
/****************************** 
* EQdkp * Copyright 2002-2003 
* Licensed under the GNU GPL.  See COPYING for full terms. 
* ------------------ 
* dbal.php 
* begin: Tue December 17 2002 
*  
* $Id$ 
* 
 ******************************/

if ( !defined('EQDKP_INC') )
{
     die('Do not access this file directly.');
}

switch ( $dbtype )
{
    case 'mysql':
        include_once($eqdkp_root_path . 'dbal/mysql.php');
        break;
    default:
        include_once($eqdkp_root_path . 'dbal/mysql.php');
        break;
}
// Instantiate the class, which connects in the constructor
$db = new SQL_DB($dbhost, $dbname, $dbuser, $dbpass, false);
if ( !$db->link_id )
{
    message_die('Could not connect to the database.');
}
?>
