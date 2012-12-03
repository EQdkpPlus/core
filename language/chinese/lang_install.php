<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * file.php
 * Began: Day January 1 2003
 *
 * $Id: lang_install.php 1701 2008-03-16 15:04:04Z osr-corgan $
 *
 ******************************/

// Do not remove. Security Option!
if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

$lang = array(
'inst_header'               => 'Installation',

// ===========================================================
//	Per Language default settings
// ===========================================================
'game_language'             => 'en',
'default_lang'              => 'english',
'default_locale'            => 'en_EN',

// ===========================================================
//	Prepare Installation
// ===========================================================
'installation_message'      => 'Installation Note',
'installation_messages'     => 'Installation Notes',
'error'                     => 'Error',
'errors'                    => 'Errors',
'lerror'                    => 'ERROR',
'notice'                    => 'NOTICE',
'install_error'             => 'Installation Error',
'inst_step'                 => 'Step',
'error_nostructure'         => 'Could not obtain SQL structure/data',
'error_template'            => "Could not include '%s' includes/class_template.php - check to make sure that the file exists!",

// ===========================================================
//	Stepnames
// ===========================================================
'stepname_1'                => 'Information',
'stepname_2'                => 'Database',
'stepname_3'                => 'DB Check',
'stepname_4'                => 'Server info',
'stepname_5'                => 'Account',
'stepname_6'                => 'Finish',

// ===========================================================
//	Step 1: PHP / Mysql Environment
// ===========================================================
'language_selector'         => 'Please Select prefered language',
'install_language'          => 'Language of Installation',
'already_installed'         => 'EQdkp is already installed - remove the <b>install/</b> folder in this directory.',
'conf_not_write'            => 'The <b>config.php</b> file does not exist and could not be created in EQdkp\'s root folder.<br />
                                You must create an empty config.php file on your server before proceeding.',
'conf_written'              => 'The <b>config.php</b> file has been created in EQdkp\'s root folder<br />
                                Deleting this file will interfere with the operation of your EQdkp installation.',
'conf_chmod'                => 'The file <b>config.php</b> is not set to be readable/writeable and could not be changed automatically.
                                <br />Please change the permissions to 0666 manually by executing <b>chmod 0666 config.php</b> on your server.',
'conf_writable'             => '<b>config.php</b> has been set to be readable/writeable in order to let this installer write your configuration
                                file automatically.',
'templcache_created'        => "The directory '%1\$s' was created, removing this directory could interfere with the operation of your EQdkp installation.",
'templcache_notcreated'     => "The directory '%1\$s' could not be created, please create one manually in the templates directory.
                                <br />You can do this by changing to the EQdkp root directory and typing <b>mkdir -p %1\$s</b>",
'templcache_notwritable'    => "The directory '%1\$s' exists, but is not set to be writeable and could not be changed automatically.
                                <br />Please change the permissions to 0777 manually by executing <b>chmod 0777 %1\$s</b> on your server.",
'templatecache_ok'          => "The '%1\$s' directory has been set to be writeable in order to let EQDKP-PLUS create files in these folders.",
'cachefolder_out'           => "The folder '%1\$s' has been %2\$s and is %3\$s",
'connection_failed'         => 'Connection to EQdkp-PLUS.com failed.',
'curl_notavailable'         => 'cURL is not available. Itemstats possibly will not work correct.',
'fopen_notavailable'        => 'fopen is not available. Itemstats possibly will not work correct.',
'safemode_on'               => 'PHP Safe Mode is enabled. EQDKP-PLUS will not run in Safe Mode because of the Data write operations.',

'minimal_requ_notfilled'    => 'Sorry, your server does not meet the minimum requirements for EQdkp',
'minimal_requ_filled'       => 'EQdkp has scanned your server and determined that it meets the minimum requirements in order to install.',

'inst_unknown'              => 'Unknown',
'eqdkp_name'                => 'EQdkp PLUS',
'inst_eqdkpv'               => 'EQDKP Plus Version',
'inst_latest'               => 'Latest stable',

'inst_php'                  => 'PHP',
'inst_view'                 => 'View phpinfo()',
'inst_version'              => 'Version',
'inst_required'             => 'Required',
'inst_available'            => 'Available',
'inst_enabled'              => 'Enabled',
'inst_using'                => 'Using',
'inst_yes'                  => 'Yes',
'inst_no'                   => 'No',

'inst_mysqlmodule'          => 'MySQL Module',
'inst_zlibmodule'           => 'zLib Module',
'inst_curlmodule'           => 'cURL Module',
'inst_fopen'                => 'fopen',
'inst_safemode'             => 'Safe Mode',

'inst_php_modules'          => 'PHP Modules',
'inst_Supported'            => 'Supported',

'inst_found'                => 'Found',
'inst_writable'             => 'Writable',
'inst_notfound'             => 'Not Found',
'inst_unwritable'           => 'Unwritable',

'inst_button1'              => 'Start Install',

// ===========================================================
//	Step 2: Database
// ===========================================================
'inst_database_conf'        => 'Database Configuration',
'inst_dbtype'               => 'Database Type',
'inst_dbhost'               => 'Database Host',
'inst_dbname'               => 'Database Name',
'inst_dbuser'               => 'Database Username',
'inst_dbpass'               => 'Database Password',
'inst_table_prefix'         => 'Prefix for EQdkp tables',
'inst_button2'              => 'Test Database',

// ===========================================================
//	Step 3: Database cofirmation
// ===========================================================
'inst_error_nodbname'       => 'No Database name given!',
'inst_error_prefix'         => 'No Table prefix set! Please go back and enter a prefix.',
'inst_error_prefix_inval'   => 'Invalid prefix',
'inst_error_prefix_toolong' => 'prefix too long!',
'inserror_dbconnect'        => 'Failed to connect to database',
'insterror_no_mysql'        => 'The Database is not MySQL!',
'inst_redoit'               => 'Restart instalation',
'db_warning'                => 'Warning',
'db_information'            => 'Information',
'inst_sqlheaderbox'         => 'SQL Information',
'inst_mysqlinfo'            => "MySQL client <b>and</b> server version 4.0.4 or higher and InnoDB table support are required for EQdkp.<br>
                                <b><br>You are running server version <ul>%s</ul> and client version <ul>%s.</ul></b><br>
                                MySQL versions less than 4.0.4 will not work and are not supported. Versions less than 4.0.4<br>
                                will experience data corruption, and we will not provide support for these installations.<br><br>",
'inst_button3'              => 'Proceed',
'inst_button_back'          => 'Back',
'inst_sql_error'            => "Error! Failed to execute this SQL Statement: <br><br><ul>%1\$s</ul><br>Error: %2\$s [%3\$s]",
'insinfo_dbready'           => 'The Database connection was checked and no errors were found. You can proceed with the installation.',

// Errors
'INST_ERR'                  => 'Installation error',
'INST_ERR_PREFIX'           => 'The Database Prefix already exists. Please go back and use another one or your data will be flushed!',
'INST_ERR_DB_CONNECT'       => 'Could not connect to the database, see error message below.',
'INST_ERR_DB_NO_ERROR'      => 'No error message given.',
'INST_ERR_DB_NO_MYSQLI'     => 'The version of MySQL installed on this machine is incompatible with the “MySQL with MySQLi Extension” option you have selected. Please try the “MySQL” option instead.',
'INST_ERR_DB_NO_NAME'       => 'No database name specified.',
'INST_ERR_PREFIX_INVALID'   => 'The table prefix you have specified is invalid for your database. Please try another, removing characters such as hyphen, apostrophe or forward- or back-slashes.',
'INST_ERR_PREFIX_TOO_LONG'  => 'The table prefix you have specified is too long. The maximum length is %d characters.',

// ===========================================================
//	Step 4: Server
// ===========================================================
'inst_language_config'      => 'Language Configuration',
'inst_default_lang'         => 'Default Language',
'inst_default_locale'       => 'Default Locale',

'inst_game_config'          => 'Game Configuration',
'inst_default_game'         => 'Default Game',

'inst_server_config'        => 'Server Configuration',
'inst_server_name'          => 'Domain Name',
'inst_server_port'          => 'Server port',
'inst_server_path'          => 'Script path',

'inst_button4'              => 'Install Database',

// ===========================================================
//	Step 5: Accounts
// ===========================================================
'inst_administrator_config' => 'Administrator Account Configuration',
'inst_username'             => 'Administrator Username',
'inst_user_password'        => 'Administrator Password',
'inst_user_pw_confirm'      => 'Confrim Administrator Password',
'inst_user_email'           => 'Administrator Email Address',

'inst_button5'              => 'Install Accounts',

'inst_writerr_confile'      => 'The <b>config.php</b> file couldn\'t be opened for writing.  Paste the following in to config.php and save the
                                file to continue:',
'inst_confwritten'          => 'Your configuration file has been written with the initial values, but installation will not be complete until
                                you create an administrator account in the next step.',
'inst_checkifdbexists'      => 'Before proceeding, please verify that the database name you provided is already created and that the user you provided has permission to create tables in that database',
'inst_wrong_dbtype'         => "Unable to find the database abstraction layer for <b>%s</b>, check to make sure %s exists.",
'inst_failedconhost'        => "Failed to connect to database <b>%s</b> as <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Restart Installation</a>",
'inst_failedversioninfo'    => "Failed to get version information for database <b>%s</b> as <b>%s@%s</b>
                                <br /><br /><a href='index.php'>Restart Installation</a>",

// ===========================================================
//	Step 5: Finish
// ===========================================================
'login'                     => 'Login',
'username'                  => 'Username',
'password'                  => 'Password',
'remember_password'         => 'Remember me (cookie)',

'login_button'              => 'Login',

'inst_passwordnotmatch'     => 'Your passwords did not match, so it has been reset to <b>admin</b>.  You can change it by logging in and going to your account settings.',
'inst_admin_created'        => 'Your administrator account has been created, log in above to be taken to the EQdkp configuration page.',
);
?>
