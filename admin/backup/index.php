<?php
/*****************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * backup.php
 * Began: Mon May 23 2005
 *
 * $Id: index.php 6 2006-05-08 17:11:35Z tsigo $
 *
 *
 * class-1 MySQL Backup/Restore
 * (c) class-1 Web Design (http://www.class1web.co.uk), 2004
 * This file is part of class-1 MySQL Backup/Restore.
 *
 ******************************/

// First things first - a small amount of security
// This script is *so* hackable as to not be funny
// The possible benefit (IMO) outweighs the possible security
// issue with referrer hacking tho...

// Check that the visitor's referrer was a page within your own site.
// Copyright 2001 Tim Green
// http://www.dwfaq.com/snippets/snippet_details.asp?SnipID=51
$tg__ServerName=explode(".",getenv("SERVER_NAME"));
$tg__server=$tg__ServerName[count($tg__ServerName)-2].".".$tg__ServerName[count($tg__ServerName)-1];
$tg__Referred = getenv("HTTP_REFERER"); 

// Get host name from URL 
preg_match("/^(http:\/\/)?([^\/]+)/i", $tg__Referred, $tg__RefHost); 
$tg__Host = $tg__RefHost[2]; 
preg_match("/[^\.\/]+\.[^\.\/]+$/", $tg__Host, $tg__RefHost); 

if ($tg__RefHost[0] != $tg__server) { 
    // Change the location to your desired page
    header("Location: ./../../viewnews.php"); 
} 


// Now start the "stuff"

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../../';
include_once($eqdkp_root_path . 'common.php');
$config_exist = 0;


// Defines
DEFINE ("DB_SELECT_FORM", 1);
DEFINE ("POST_DB_SELECT_FORM", 2);
DEFINE ("POST_SELECT_TABLES_FORM", 3);
DEFINE ("RESTORE_DATA", 4);
DEFINE ("POST_CONFIG_FORM", 5);
DEFINE ("TITLE", "class-1 MySQL Backup/Restore");
DEFINE ("EQDKP_TITLE", "Modified for EQdkp");
DEFINE ("VERSION", "0.1e");

// Make sure we pick up variables passed via URL
foreach (array_keys($_GET) as $key) $$key = $_GET[$key];

// Start a session and get variables
session_start();
if ($_SESSION['db_selected']) {
	$db_selected = $_SESSION['db_selected'];
} else {
	$db_selected = "";
}

// Output HTML headers (except for the backup download "page")
if ($mode != POST_SELECT_TABLES_FORM) {
	echo "<html>\n";
	echo "<head>\n";
	echo "<title>".TITLE." ".EQDKP_TITLE."</title>\n";
	echo "<link rel=\"stylesheet\" type=\"text/css\" href=\"backup.css\" />\n";
	echo "</head>\n";
	echo "<body>\n";
}

if ( (isset($dbuser)) && (isset($dbpass)) && (isset($dbhost)) ) {

  $config_exist = 1;

}

// Output a form to request MySQL server details if config.inc doesn't exist
if (!$config_exist) {
	echo "<form method=\"post\" action=\"backup.php?mode=".POST_CONFIG_FORM."\">\n";
	
	echo "<table cellspacing=\"0\" cellpadding=\"3\">\n";
	echo "<tr><td class=\"title\" colspan=\"2\">".TITLE." ".EQDKP_TITLE." Setup</td></tr>\n";
	echo "<tr><td class=\"main_left\" colspan=\"2\">Enter your MySQL Server details below.</td></tr>\n";

	echo "<tr><td class=\"main_left\">Server:</td>\n";
	echo "<td class=\"main_left\"><input type=\"text\" name=\"dbhost\" value=\"$dbhost\" style=\"width:200px\" /></td></tr>\n";
	
	echo "<tr><td class=\"main_left\">Username:</td>\n";
	echo "<td class=\"main_left\"><input type=\"text\" name=\"dbuser\" value=\"$dbuser\" style=\"width:200px\" /></td></tr>\n";	

	echo "<tr><td class=\"main_left\">Password:</td>\n";
	echo "<td class=\"main_left\"><input type=\"password\" name=\"dbpass\" value=\"$dbpass\" style=\"width:200px\" /></td></tr>\n";	
	
	echo "<tr><td class=\"main_left\" colspan=\"2\"><br /><input type=\"submit\" value=\"Post Server Details\" /> <input type=\"reset\" value=\"Reset\">\n";
	
	echo "</td></tr>\n";
	
	echo "</table>\n";

	echo "</form>\n";
	echo "</body>\n";
	echo "</html>\n";
	exit();
}


// Connect to MySQL
$link = mysql_connect("$dbhost", "$dbuser", "$dbpass");
if (!$link) die("Failed to connect to MySQL - ".mysql_error());

// Select database if one is chosen
if ($_POST['db_selected']) $db_selected = $_POST['db_selected'];

if ($db_selected) {
	$result = mysql_selectdb($db_selected, $link);
	if (!$result) {
		$_SESSION = array();
		session_destroy();

		die("Failed to select database - ".mysql_error());
	}
}

// Include backup functions
include("backup.inc");

// Simple dialog box function
function dialog_box($title, $message)
{
	echo "<table cellspacing=\"0\" cellpadding=\"3\">\n";
	echo "<tr><td class=\"title\">$title</td></tr>\n";
	echo "<tr><td class=\"main_left\"><p>$message</p></td></tr>\n";
	echo "</table>\n";
}

// Handle URL actions
switch ($mode) {
	case POST_DB_SELECT_FORM: {
		// Get the selected database and store it in a session
		$db_selected = $_POST['db_selected'];
		$_SESSION['db_selected'] = $db_selected;
		
		// Get list of tables in the database and output form
		$result = mysql_list_tables($db_selected);
		$num_tables = mysql_num_rows($result);

		echo "<table cellspacing=\"0\" cellpadding=\"3\">\n";
		echo "<tr><td class=\"title\">Backup MySQL Data</td></tr>\n";
		echo "<tr><td class=\"main_left\"><p>Once you click select the selected tables will be backed up and a file download will start which will download the backup file to your computer.";

		echo "<p><b>Select MySQL Tables to backup</b></p>\n";
		
		echo "<form method=\"post\" action=\"backup.php?mode=".POST_SELECT_TABLES_FORM."&num_tables=$num_tables\">\n";

		for ($i = 0; $i < $num_tables; $i++) {
			$tablename = mysql_tablename($result, $i);
			
			$checkbox_string = sprintf("<input type=\"checkbox\" name=\"check_id%d\" /><input type=\"hidden\" name=\"tablename%d\" value=\"%s\" />&nbsp;%s<br />\n", $i, $i, $tablename, $tablename);
			echo "$checkbox_string";
		}

		echo "<p><b>Select MySQL details to backup</b></p>\n";

		echo "<input type=\"checkbox\" name=\"structure\" checked />&nbsp;Table Structure<br />\n";
		echo "<input type=\"checkbox\" name=\"data\" checked />&nbsp;Table Data<br />\n";
		
		echo "<br /><input type=\"submit\" value=\"Select\">\n";
		echo "<input type=\"reset\" value=\"Reset\">\n";
		echo "</form>\n";

		echo "</p></td></tr>\n";
		echo "</table>\n";
		
		break;
	}
	
	case POST_SELECT_TABLES_FORM: {
		$sql_string = "";
		$backup_structure = ($_POST['structure'] == "on") ? 1 : 0;
		$backup_data = ($_POST['data'] == "on") ? 1 : 0;

		if (!DEBUG) {
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=backup.sql");
		} else {
			echo "<pre>\n";
		}

		$j = 0;
		for ($i = 0; $i < $num_tables; $i++) {
			$check_id = sprintf("check_id%d", $i);
			$tablename = sprintf("tablename%d", $i);

			if ($_POST[$check_id]) {
				$tablename_array[$j] = $_POST[$tablename];
				$j++;
			}
		}

		$sql_string = backup_data($tablename_array, $backup_structure, $backup_data);
		
		echo "$sql_string";
		
		if (DEBUG) echo "</pre>\n";
		
		break;
	}
	
	case RESTORE_DATA: {
		dialog_box("Restore Complete", "The Restore is complete. Any errors or messages encountered are shown below. Any tables which were backed up can be deleted once you are happy with the restored tables.");

		echo "<p />";

		restore_data("./$filename", $restore_structure, $restore_data, $db_selected);

		unlink("./$filename");

		break;
	}

	case DB_SELECT_FORM: {
		// Get list of databases and output form
		$db_list = mysql_list_dbs($link);

		echo "<table cellspacing=\"0\" cellpadding=\"3\">\n";
		if ($action == "backup") {
			echo "<tr><td class=\"title\">Backup MySQL Data</td></tr>\n";
			echo "<tr><td class=\"main_left\"><p><b>Select database to backup from</b>";
		}
		if ($action == "restore") {
			echo "<tr><td class=\"title\">Restore MySQL Data</td></tr>\n";
			echo "<tr><td class=\"main_left\"><p><b>Select database to restore to</b>";
		}

		if ($action == "backup") echo "<form method=\"post\" action=\"backup.php?mode=".POST_DB_SELECT_FORM."\">\n";
		if ($action == "restore") {
			// Upload file
			$filename = $_FILES['filename']['name'];

			$ret_val = move_uploaded_file($_FILES['filename']['tmp_name'], "./$filename");

			if (!$ret_val) {
				echo "<br /><br />Could not upload file.\n";
				echo "</p></td></tr>\n";
				echo "</table>\n";

				break;
			}

			$restore_structure = ($_POST['structure'] == "on") ? 1 : 0;
			$restore_data = ($_POST['data'] == "on") ? 1 : 0;
		
			echo "<form method=\"post\" action=\"backup.php?mode=".RESTORE_DATA."&filename=$filename&restore_structure=$restore_structure&restore_data=$restore_data\">\n";
		}

		echo "<select name=\"db_selected\" style=\"width:200px\">\n";

		while ($row = mysql_fetch_object($db_list)) {
			$db_select_string = sprintf("<option>%s</option>\n", $row->Database);
			echo $db_select_string;
		}

		echo "</select>\n";
		echo "<input type=\"submit\" value=\"Select\">\n";
		echo "</form>\n";

		echo "</p></td></tr>\n";
		echo "</table>\n";
		
		break;
	}

	default: {
		echo "<h3><u>".TITLE." ".EQDKP_TITLE."</u></h3>";

		echo "<p>Choose an option below.</p>";

		dialog_box("Backup MySQL Data", "Click <a href=\"backup.php?mode=".DB_SELECT_FORM."&action=backup\">here</a> to backup MySQL data.");

		echo "<p />";

		dialog_box("Restore MySQL Data", "<b>Select a file</b><form method=\"post\" enctype=\"multipart/form-data\" action=\"backup.php?mode=".DB_SELECT_FORM."&action=restore\"><input type=\"file\" name=\"filename\" /><br /><br /><b>Select MySQL details to restore</b><br /><br /><input type=\"checkbox\" name=\"structure\" checked />&nbsp;Table Structure<br /><input type=\"checkbox\" name=\"data\" checked />&nbsp;Table Data<br /><br /><input type=\"submit\" value=\"Restore\" /></form><p />");

		echo "<p />";

	}
}

// Close MySQL link
mysql_close($link);

if ($mode != POST_SELECT_TABLES_FORM) {
	echo "<p><a href=\"backup.php\">Return to Start</a></p>\n";

	echo "<p>".TITLE." (v".VERSION.")<br />Powered by and copyright <a  onclick=\"window.open(this.href,'new');return false;\" href=\"http://www.class1web.co.uk\">class-1</a> Software, 2004.</p>\n";
	echo "<p>".EQDKP_TITLE." (v".EQDKP_VERSION.")<br />Copyright <a  onclick=\"window.open(this.href,'new');return false;\" href=\"http://www.eqdkp.com\">The EQdkp Project</a> 2005.</p>\n";

	echo "</body>\n";
	echo "</html>\n";
}

?>
