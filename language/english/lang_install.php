<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:				http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2002
 * -----------------------------------------------------------------------
 * @copyright   2006-2011 EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * 
 */
 
if (!defined('EQDKP_INC')) {
	die('You cannot access this file directly.');
}

//Language: English 0.7	
//Created by EQdkp Plus Translation Tool on  2011-09-03 18:07
//File: lang_install
//Source-Language: german07

$alang = array( 
"page_title" => "EQDKP-PLUS %s Installation",
"back" => "Save and back",
"continue" => "Proceed",
"language" => "Language",
"inst_finish" => "Complete installation",
"error" => "Error",
"warning" => "Warning",
"success" => "Success",
"yes" => "Yes",
"no" => "No",
"retry" => "Retry",
"skip" => "Skip",
'step_order_error' => 'Step-Order error: Step not found. Please ensure that all files are uploaded correctly. For further information please visit our forums at <a href="'.EQDKP_BOARD_URL.'">'.EQDKP_BOARD_URL.'</a>.',
"licence" => "Licence Agreement",
"php_check" => "Pre-installation Check",
"ftp_access" => "FTP Settings",
'encryptionkey' => 'Encryption Key',
"data_folder" => "Data Folder",
"db_access" => "Database Access",
"inst_settings" => "Settings",
"admin_user" => "Administrator Account",
"end" => "Complete the installation",
"accept" => "Accept & Start Installation",
'license_text'			=> '<b>EQdkp Plus is published under Creative Commons Licence "Attribution-NonCommercial-ShareAlike 3.0 Unported (CC BY-NC-SA 3.0)".</b><br /><br /> The full licence text can be found at https://creativecommons.org/licenses/by-nc-sa/3.0/legalcode.<br /><br />
	This is a human-readable summary of the Legal Code:<br /><br />
	<b>You are free:</b><ul><li><b>to Share</b> — to copy, distribute and transmit the work </li><li><b>to Remix</b> — to adapt the work </li></ul>
	<b>Under the following conditions:</b><ul><li><b>Attribution</b> — You must attribute the work in the manner specified by the author or licensor (but not in any way that suggests that they endorse you or your use of the work).</li><li><b>Noncommercial</b> — You may not use this work for commercial purposes. </li><li><b>Share Alike</b> — If you alter, transform, or build upon this work, you may distribute the resulting work only under the same or similar license to this one. </li></ul>
	<b>With the understanding that: </b><ul><li><b>Waiver</b> — Any of the above conditions can be waived if you get permission from the copyright holder. </li><li><b>Public Domain</b> — Where the work or any of its elements is in the public domain under applicable law, that status is in no way affected by the license. </li><li><b>Other Rights</b> — In no way are any of the following rights affected by the license: <ul><li>Your fair dealing or fair use rights, or other applicable copyright exceptions and limitations; </li><li>The author\'s moral rights; </li><li>Rights other persons may have either in the work itself or in how the work is used, such as publicity or privacy rights. </li></ul> </li><li><b>Notice</b> — For any reuse or distribution, you must make clear to others the license terms of this work. The best way to do this is with a link to this web page (https://creativecommons.org/licenses/by-nc-sa/3.0/). </li></ul>
',
"welcome"	=> "Welcome to the installer for EQdkp Plus. We have worked hard to make this process easy and fast. To get started simply accept our license agreement by clicking 'Accept & Start Installation' below.",
"table_pcheck_name" => "Name",
"table_pcheck_required" => "Required",
"table_pcheck_installed" => "Current",
"module_php" => "PHP version",
"module_mysql" => "MySQL database",
"module_zLib" => "zLib PHP module",
"module_safemode" => "PHP Safemode",
"module_curl" => "cURL PHP module",
"module_fopen" => "fopen PHP function",
"module_soap" => "SOAP PHP module",
"module_autoload" => "spl_autoload_register PHP function",
"module_hash" => "hash PHP function",
"safemode_warning" => "<strong>WARNING</strong><br/>The PHP Safe mode is active, you have to use the FTP mode of EQDKP-Plus!",
"phpcheck_success" => "The minimum requirements for the installation of EQDKP-Plus are met. The installation can proceed.",
"phpcheck_failed" => "The minimum requirements for the installation of EQDKP-Plus are not met.<br />A selection of suitable hosting companies can be found on our <a href=\"".EQDKP_PROJECT_URL."\" target=\"_blank\">website</a>",
"do_match_opt_failed" => "Some requirements are not met. EQDKP-Plus will work on this system; however, not all features will be available.",
"ftphost" => "FTP host",
"ftpport" => "FTP port",
"ftpuser" => "FTP username",
"ftppass" => "FTP password",
"ftproot" => "FTP base dir",
"ftproot_sub" => "(Path to the root directory of the FTP user)",
"useftp" => "Use FTP mode as file handler",
"useftp_sub" => "(You can change it later by editing the config.php)",
"safemode_ftpmustbeon" => "Since PHP safe mode is on, the FTP details must be completed to continue the installation.",
"ftp_connectionerror" => "The FTP connection could not be established. Please check the FTP host and the FTP port.",
"ftp_loginerror" => "The FTP login was not successful. Please check your FTP username and FTP password.",
"plain_config_nofile" => "The file <b>config.php</b> is not available and automatic creation failed. <br />Please create a blank text file with the name <b>config.php</b> and set the permissions with chmod 777",
"plain_config_nwrite" => "The <b>config.php</b> file is not writeable. <br /> Please set the correct permissions. <b>chmod 0777 config.php</b>.",
"plain_dataf_nwrite" => "The folder <b>./data/</b> is not writeable.<br /> Please set the correct permissions. <b>chmod -R 0777 data</​​b>.",
'plain_dataf_na'		=> "The folder <b>./data/</b> is not available.<br /> Please create this folder. <b>mkdir data</​​b>.",
"ftp_datawriteerror" => "The Data folder could not be written to. Is the FTP root path set  correctly?",
"ftp_info" => "To improve security and functionality, you can choose to allow an ftp account of your choosing to perform file interactions on the server. This reduces the need to use more open server permissions, and may be required on some hosting configurations. To use this optional setting please provide a ftp user with permissions to access your installation, and select the 'FTP Mode' tick box. If you are not using FTP Mode you may simply select proceed on this page.",
'ftp_tmpinstallwriteerror' => 'The folder <b>./data/97384261b8bbf966df16e5ad509922db/tmp/</b> is not writable.<br />To write the config-file, CHMOD 777 is required. This folder will be deleted after the installation process.',
	'ftp_tmpwriteerror' 	=> 'The folder <b>./data/%s/tmp/</b> is not writable.<br />Using FTP-Mode requires CHMOD 777 for this folder. This is the only folder needing writing permissions.',
"dbtype" => "Database type",
"dbhost" => "Database host",
"dbname" => "Database name",
"dbuser" => "Database username",
"dbpass" => "Database password",
"table_prefix" => "Prefix for EQDKP-Plus tables",
"test_db" => "Test database",
"prefix_error" => "No or invalid database prefix specified! Please enter a valid prefix.",
"INST_ERR_PREFIX" => "An EQdkp installation with that prefix already exists. Delete all tables with that prefix and repeat this step once you have used the \"Back\" button. Alternatively, you can choose a different prefix, e.g. if you want to install multiple sets of EQDKPlus data in a database.",
"INST_ERR_DB_CONNECT" => "Could not connect to the database, see error message below.",
"INST_ERR_DB_NO_ERROR" => "No error message given.",
"INST_ERR_DB_NO_MYSQLI" => "The version of MySQL installed on this machine is incompatible with the “MySQL with MySQLi Extension” option you have selected. Please try the “MySQL” option instead.",
"INST_ERR_DB_NO_NAME" => "No database name specified.",
"INST_ERR_PREFIX_INVALID" => "The table prefix you have specified is invalid for your database. Please try another, removing characters such as hyphen, apostrophe or forward- or back-slashes.",
"INST_ERR_PREFIX_TOO_LONG" => "The table prefix you have specified is too long. The maximum length is %d characters.",
"dbcheck_success" => "The database was checked. It found no errors or conflicts. The installation can be continued safely.",
"inst_db" => "Install database",
"lang_config" => "Language settings",
"default_lang" => "Default language",
"default_locale" => "Default localization",
"game_config" => "Game settings",
"default_game" => "Default game",
"server_config" => "Server settings",
"server_path" => "Script path",
"grp_guest" => "Guests",
"grp_super_admins" => "Super administrators",
"grp_admins" => "administrators",
"grp_officers" => "officers",
"grp_writers" => "Editors",
"grp_member" => "Members",
"grp_guest_desc" => "Guests are not signed in users",
"grp_super_admins_desc" => "Super administrators have all rights",
"grp_admins_desc" => "Administrators do not have all admin rights",
"grp_officers_desc" => "Officers are able to manage raids",
"grp_writers_desc" => "Editors are able to write and manage news",
"grp_member_desc" => "member",
"timezone" => "Timezone of the server",
"startday" => "First day of the week",
"sunday" => "Sunday",
"monday" => "Monday",
"time_format" => "H:i",
"date_long_format" => "j. F Y",
"date_short_format" => "d.m.y",
"welcome_news_title" => "Welcome to EQDKP-Plus",
"welcome_news" => '<p>The installation of your EQdkp Plus was completed successfully - you can now set it up according to your wishes.</p>
<p>You can find assistance to administration and general use in our <a href="'.EQDKP_WIKI_URL.'" target=\"_blank\">Wiki</a>.</p>
<p>For further support, please visit our <a href="'.EQDKP_BOARD_URL.'" target=\"_blank\">Forum</a>.</p>
<p>Have fun with EQdkp Plus! Your EQdkp Plus team</p>',
"role_healer" => "Healer",
"role_tank" => "Tank",
"role_range" => "Ranged DPS",
"role_melee" => "Melee DPS",
"create_user" => "Create Access",
"username" => "Administrator username",
"user_password" => "Administrator password",
"user_pw_confirm" => "Confirm the administrator password",
"user_email" => "Administrator email address",
"auto_login" => "Remember me (cookie)",
"user_required" => "Username, email and password are required fields",
"no_pw_match" => "The passwords do not match.",
"install_end_text" => "The installation can now be completed successfully.",
//Step: encryptionkey
'encryptkey_info'		=> 'The encryption key is part of the encryption process used to protect sensitive data in the database, such as your users email addresses. Even if your database is compromised, without the encryption key your data remains encoded and secure. Therefore please choose a secure key, and ensure that you store a safe copy. Nobody else can ever retrieve it for you if it becomes lost!',
'encryptkey'			=> 'Encryption-Key',
'encryptkey_help'		=> '(min. length 6 chars)',
'encryptkey_repeat'		=> 'Confirm the encryption Key',
'encryptkey_no_match'	=> 'The encryptions keys do not match',
'encryptkey_too_short'	=> 'The encryption key is too short. Minimum length is 6 chars.',

 );
$lang = (is_array($lang))? $lang : array();
$lang = array_merge($lang, $alang);
?>