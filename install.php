<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * install.php
 * Began: Sun June 22 2003
 *
 * Last Change:
 * $Rev::                      $:
 * $Author:: 											$:
 * $Date:: 												$:
 * $HeadURL::											$:
 * $Id$
 ******************************/

// ---------------------------------------------------------
// Set up environment
// ---------------------------------------------------------
// 
define('EQDKP_INC', true);
error_reporting(E_ALL);

set_magic_quotes_runtime(0);
if ( !get_magic_quotes_gpc() )
{
    $_GET = slash_global_data($_GET);
    $_POST = slash_global_data($_POST);
}

$eqdkp_root_path = './';

// Get the config file
if ( file_exists($eqdkp_root_path . 'config.php') )
{
    include_once($eqdkp_root_path . 'config.php');
}

// ---------------------------------------------------------
// Template Wrap class
// ---------------------------------------------------------
if ( !include_once($eqdkp_root_path . 'includes/class_template.php') )
{
    die('Could not include ' . $eqdkp_root_path . 'includes/class_template.php - check to make sure that the file exists!');
}


class Template_Wrap extends Template
{
    var $error_message   = array();           // Array of errors      @var $error_message
    var $install_message = array();           // Array of messages    @var $install_message
    var $header_inc      = false;             // Printed header?      @var $header_inc
    var $tail_inc        = false;             // Printed footer?      @var $tail_inc
    var $template_file   = '';                // Template filename    @var $template_file

    function template_wrap($template_file)
    {
        $this->template_file = $template_file;

        $this->set_template('install');

        $this->assign_vars(array(
            'MSG_TITLE' => '',
            'MSG_TEXT'  => '')
        );

        $this->set_filenames(array(
            'body' => $this->template_file)
        );
    }

    function message_die($text = '', $title = '')
    {
        $this->set_filenames(array(
            'body' => 'install_message.html')
        );

        $this->assign_vars(array(
            'MSG_TITLE' => ( $title != '' ) ? $title : '&nbsp;',
            'MSG_TEXT'  => ( $text  != '' ) ? $text  : '&nbsp;')
        );

        if ( !$this->header_inc )
        {
            $this->page_header();
        }

        $this->page_tail();
    }

    function message_append($message)
    {
        $this->install_message[ sizeof($this->install_message) + 1 ] = $message;
    }

    function message_out($die = false)
    {
        sort($this->install_message);
        reset($this->install_message);

        $install_message = implode('<br /><br />', $this->install_message);

        if ( $die )
        {
            $this->message_die($install_message, 'Installation ' . (( sizeof($this->install_message) == 1 ) ? 'Note' : 'Notes'));
        }
        else
        {
            $this->assign_vars(array(
                'MSG_TITLE' => 'Installation ' . (( sizeof($this->install_message) == 1 ) ? 'Note' : 'Notes'),
                'MSG_TEXT'  => $install_message)
            );
        }
    }

    function error_append($error)
    {
        $this->error_message[ (sizeof($this->error_message) + 1) ] = $error;
    }

    function error_out($die = false)
    {
        sort($this->error_message);
        reset($this->error_message);

        $error_message = implode('<br /><br />', $this->error_message);

        if ( $die )
        {
            $this->message_die($error_message, 'Installation ' . (( sizeof($this->error_message) == 1 ) ? 'Error' : 'Errors'));
        }
        else
        {
            $this->assign_vars(array(
                'MSG_TITLE' => 'Installation ' . (( sizeof($this->error_message) == 1 ) ? 'Error' : 'Errors'),
                'MSG_TEXT'  => $error_message)
            );
        }
    }

    function page_header()
    {
        global $STEP;

        $this->header_inc = true;

        /*
        $now = gmdate('D, d M Y H:i:s', time()) . ' GMT';
        @header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        @header('Last-Modified: ' . $now);
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Content-Type: text/html; charset=iso-8859-1');
        */

        $this->assign_vars(array(
            'INSTALL_STEP' => $STEP)
        );
    }

    function page_tail()
    {
        global $DEFAULTS, $db;

        $this->assign_var('S_SHOW_BUTTON', true);

        if ( sizeof($this->install_message) > 0 )
        {
            $this->message_out(false);
        }

        if ( sizeof($this->error_message) > 0 )
        {
            $this->assign_var('S_SHOW_BUTTON', false);
            $this->error_message[0] = '<span style="font-weight: bold; font-size: 14px;" class="negative">NOTICE</span>';
            $this->error_out(false);
        }

        $this->assign_var('EQDKP_VERSION', $DEFAULTS['version']);

        if ( is_object($db) )
        {
            $db->close_db();
        }

        $this->display('body');
        $this->destroy();

        exit;
    }
}

$STEP = ( isset($_POST['install_step']) ) ? $_POST['install_step'] : '1';

// If EQdkp is already installed, don't let them install it again
if ( defined('EQDKP_INSTALLED') )
{
    $tpl = new Template_Wrap('install_message.html');
    $tpl->message_die('EQdkp is already installed - remove the <b>install.php</b> file in this directory.', 'Installation Error');
    exit();
}

// View phpinfo() if requested
if ( (isset($HTTP_GET_VARS['mode'])) && ($HTTP_GET_VARS['mode'] == 'phpinfo') )
{
    phpinfo();
    exit;
}

// System defaults / available database abstraction layers
$DEFAULTS = array(
    'version'       => '1.3.2',
    'default_lang'  => 'german',
    'default_style' => '1',
    'table_prefix'  => 'eqdkp_',
    'dbal'          => 'mysql'
);
$DBALS    = array(
    'mysql' => array(
        'label'       => 'MySQL 4.x',
        'structure'   => 'mysql',
        'comments'    => 'remove_remarks',
        'delim'       => ';',
        'delim_basic' => ';'
    )
);
//$STEP = ( isset($_POST['install_step']) ) ? $_POST['install_step'] : '1';

$LOCALES = array(
	'English' => array(
		'label'	=> 'English',
		'type'	=> 'en_US'
		),
	'German'  => array(
		'label' => 'German',
		'type'	=> 'de_DE'
		),
	'French'  => array(
		'label'	=> 'French',
		'type'	=> 'fr_FR'
		)
	);

// ---------------------------------------------------------
// Figure out what we're doing...
// ---------------------------------------------------------
switch ( $STEP )
{
    case 1:
        process_step1();
        break;
    case 2:
        process_step2();
        break;
    case 3:
        process_step3();
        break;
    case 4:
        process_step4();
        break;
    default:
        process_step1();
        break;
}

// ---------------------------------------------------------
// And do it
// ---------------------------------------------------------
function process_step1()
{
    global $eqdkp_root_path, $DEFAULTS;

    $tpl = new Template_Wrap('install_step1.html');

    //
    // Check to make sure config.php exists and is readable / writeable
    //
    $config_file = $eqdkp_root_path . 'config.php';
    if ( !file_exists($config_file) )
    {
        if ( !@touch($config_file) )
        {
            $tpl->error_append('The <b>config.php</b> file does not exist and could not be created in EQdkp\'s root folder.<br />
                                You must create an empty config.php file on your server before proceeding.');
        }
        else
        {
            $tpl->message_append('The <b>config.php</b> file has been created in EQdkp\'s root folder<br />
                                  Deleting this file will interfere with the operation of your EQdkp installation.');
        }
    }
    else
    {
        if ( (!is_writeable($config_file)) || (!is_readable($config_file)) )
        {
            if ( !@chmod($config_file, 0666) )
            {
                $tpl->error_append('The file <b>config.php</b> is not set to be readable/writeable and could not be changed automatically.
                                    <br />Please change the permissions to 0666 manually by executing <b>chmod 0666 config.php</b> on your server.');
            }
            else
            {
                $tpl->message_append('<b>config.php</b> has been set to be readable/writeable in order to let this installer write your configuration
                                      file automatically.');
            }
        }
        // config file exists and is writeable, we're good to go
    }
    clearstatcache();

    //
    // Check to make sure templates/cache exists and is writeable
    //
    $cache_directory = $eqdkp_root_path . 'templates/cache';
    if ( !file_exists($cache_directory) )
    {
        if ( !@mkdir($cache_directory, 0777) )
        {
            $tpl->error_append('The templates cache directory could not be created, please create one manuallly in the templates directory.
                                <br />You can do this by changing to the EQdkp root directory and typing <b>mkdir -p templates/cache/</b>');
        }
        else
        {
            $tpl->message_append('A templates cache directory was created in your templates directory, removing this directory could interfere
                                  with the operation of your EQdkp installation.');
        }
    }
    else
    {
        if ( !is_writeable($cache_directory) )
        {
            if ( !@chmod($cache_directory, 0777) )
            {
                $tpl->error_append('The templates cache directory exists, but is not set to be writeable and could not be changed automatically.
                                    <br />Please change the permissions to 0777 manually by executing <b>chmod 0777 templates/cache</b> on your server.');
            }
            else
            {
                $tpl->message_append('The templates cache directory ahs been set to be writeable in order to let the Templating engine create cached
                                      versions of the compiled templates and speed up the displaying of EQdkp pages.');
            }
        }
        // Cache directory exists and is writeable, we're good to go
    }
    clearstatcache();

    //
    // Server settings
    //
    // EQdkp versions
    $our_eqdkp_version   = $DEFAULTS['version'];
    $their_eqdkp_version = 'Unknown';
    $sh = @fsockopen('eqdkp.com', 80, $errno, $error, 5);
    if ( !$sh )
    {
        $their_eqdkp_version = 'Connection to EQdkp.com failed.';
    }
    else
    {
        @fputs($sh, "GET /version.php HTTP/1.1\r\nHost: eqdkp.com\r\nConnection: close\r\n\r\n");
        while ( !@feof($sh) )
        {
            $content = @fgets($sh, 512);
            if ( preg_match('#<version>(.+)</version>#i', $content, $version) )
            {
                $their_eqdkp_version = $version[1];
                break;
            }
        }
    }
    @fclose($sh);

    // PHP Versions
    $our_php_version   = (( phpversion() >= '4.1.2' ) ? '<span class="positive">' : '<span class="negative">') . phpversion() . '</span>';
    $their_php_version = '4.1.2+';

    // Modules
    $our_mysql   = ( extension_loaded('mysql') ) ? '<span class="positive">Yes</span>' : '<span class="negative">No</span>';
    $their_mysql = 'Yes';
    $our_zlib    = ( extension_loaded('zlib') )  ? '<span class="positive">Yes</span>' : '<span class="negative">No</span>';
    $their_zlib  = 'No';

    if(function_exists('curl_version'))
    {
    	$their_curl =	curl_version();
    	if(isset($their_curl['version']))
    	{
    		$their_curl =  '<span class="positive">YES ('.$their_curl['version'].')</span>' ;
    	}
    }
    else
    {
     $their_curl='<span class="negative">No</span>';
     $tpl->message_append('<b>ERROR:</b> cURL is not available. Itemstats possibly will not work correct.');

    }


    if(function_exists('fopen'))
    {
    	$their_fopen = '<span class="positive">YES</span>' ;
    }
    else
    {
     $their_fopen = '<span class="negative">NO</span>' ;
     $tpl->message_append('<b>ERROR:</b> fopen is not available. Itemstats possibly will not work correct.');
    }



    if ( (phpversion() < '4.1.2') || (!extension_loaded('mysql')) )
    {
        $tpl->error_append('<span style="font-weight: bold; font-size: 14px;">Sorry, your server does not meet the minimum requirements for EQdkp</span>');
    }
    else
    {
        $tpl->message_append('EQdkp has scanned your server and determined that it meets the minimum requirements in order to install.');
    }

    //
    // Output the page
    //
    $tpl->assign_vars(array(
        'OUR_EQDKP_VERSION'   => $our_eqdkp_version,
        'THEIR_EQDKP_VERSION' => $their_eqdkp_version,
        'OUR_PHP_VERSION'     => $our_php_version,
        'THEIR_PHP_VERSION'   => $their_php_version,
        'OUR_MYSQL'           => $our_mysql,
        'THEIR_MYSQL'         => $their_mysql,
        'OUR_ZLIB'            => $our_zlib,
        'THEIR_CURL'		      => $their_curl,
        'THEIR_FOPEN'		      => $their_fopen,
        'THEIR_ZLIB'          => $their_zlib)
    );

    $tpl->page_header();
    $tpl->page_tail();
}


function process_step2()
{


    global $eqdkp_root_path, $DEFAULTS, $DBALS, $LOCALES;

    $tpl = new Template_Wrap('install_step2.html');

    //
    // Build the default language drop-down
    //

    if ($_SERVER["HTTP_ACCEPT_LANGUAGE"])
    {
			$langarray = explode(",",$_SERVER["HTTP_ACCEPT_LANGUAGE"]);
			$lang = str_replace("-","_",$langarray[0]);
		}
		else
		{
			$lang="en_us";
		}

    $lang_path = $eqdkp_root_path . 'language/';
    if ( $dir = @opendir($lang_path) )
    {
        while ( $file = @readdir($dir) )
        {
            if ( (!is_file($lang_path . $file)) && (!is_link($lang_path . $file)) && ($file != '.') && ($file != '..') && ($file != 'CVS') )
            {
            		if($file == 'english' and $lang=="en_us")
            		{
            			$selected = 'selected="selected"' ;
            		}
            		else
            		{
            			$selected = '' ;
            		}

            		if($file == 'german' and $lang=="de_de")
            		{
            			$selected = 'selected="selected"' ;
            		}
            		else
            		{
            			$selected = '' ;
            		}

                $tpl->assign_block_vars('language_row', array(
                    'VALUE'  => $file,
                    'OPTION' => ucfirst(strtolower($file)),
					'SELECTED' => ( $DEFAULTS['default_lang'] == ucfirst(strtolower($file)) ? 'selected="selected"' : '')
					)
                );
            }
        }
    }

    //
    // Build the database drop-down
    //
    foreach ( $DBALS as $db_type => $db_options )
    {
        $tpl->assign_block_vars('dbal_row', array(
            'VALUE'  => $db_type,
            'OPTION' => $db_options['label'])
        );
    }

    foreach ( $LOCALES as $locale_type => $locale_desc )
    {

   		if($locale_type == 'English' and $lang=="en_us")
  		{
  			$selected = 'selected="selected"' ;
  		}
  		else
  		{
  			$selected = '' ;
  		}

  		if($locale_type == 'German' and $lang=="de_de")
  		{
  			$selected = 'selected="selected"' ;
  		}
  		else
  		{
  			$selected = '' ;
  		}

        $tpl->assign_block_vars('locale_row', array(
            'VALUE'  => $locale_desc['type'],
            'OPTION'   => $locale_type,
			'SELECTED' => $DEFAULTS['default_lang'] == $locale_type ? 'selected="selected"' : '',
			)
        );
    }
    //
    // Determine server settings
    //
    $server_name = ( !empty($_SERVER['HTTP_HOST']) ) ? $_SERVER['HTTP_HOST'] : $_ENV['HTTP_HOST'];

    if ( (!empty($_SERVER['SERVER_PORT'])) || (!empty($_ENV['SERVER_PORT'])) )
    {
        $server_port = ( !empty($_SERVER['SERVER_PORT']) ) ? $_SERVER['SERVER_PORT'] : $_ENV['SERVER_PORT'];
    }
    else
    {
        $server_port = '80';
    }

    $server_path = str_replace('install.php', '', $_SERVER['PHP_SELF']);

    $tpl->message_append('Before proceeding, please verify that the database name you provided is already created and that the user you provided has permission to create tables in that database');

    //
    // Output the page
    //
    $tpl->assign_vars(array(
        'DB_HOST'      => 'localhost',
        'TABLE_PREFIX' => $DEFAULTS['table_prefix'],
        'SERVER_NAME'  => $server_name,
        'SERVER_PORT'  => $server_port,
        'SERVER_PATH'  => $server_path)
    );

    $tpl->page_header();
    $tpl->page_tail();
}

function process_step3()
{
    global $eqdkp_root_path, $DEFAULTS, $DBALS, $LOCALES;

    $tpl = new Template_Wrap('install_step3.html');

    //
    // Get our posted data
    //
    $default_lang = post_or_db('default_lang', $DEFAULTS);
    $dbtype       = post_or_db('dbtype');
    $dbhost       = post_or_db('dbhost');
    $dbname       = post_or_db('dbname');
    $dbuser       = post_or_db('dbuser');
    $dbpass       = post_or_db('dbpass');
    $default_locale       = post_or_db('default_locale');
    $table_prefix = post_or_db('table_prefix', $DEFAULTS);
    $server_name  = post_or_db('server_name');
    $server_port  = post_or_db('server_port');
    $server_path  = post_or_db('server_path');

    define('CONFIG_TABLE', $table_prefix . 'config');
    define('USERS_TABLE',  $table_prefix . 'users');

    $dbal_file = $eqdkp_root_path . 'dbal/' . $dbtype . '.php';
    if ( !file_exists($dbal_file) )
    {
        $tpl->message_die('Unable to find the database abstraction layer for <b>' . $dbtype . '</b>, check to make sure ' . $dbal_file . ' exists.');
    }

    //
    // Database population
    //
    define('DEBUG', 2);
    include_once($dbal_file);
    $db = new SQL_DB($dbhost, $dbname, $dbuser, $dbpass, false);

    // Check to make sure a connection was made
    if ( !is_resource($db->link_id) )
    {
        $tpl->message_die('Failed to connect to database <b>' . $dbname . '</b> as <b>' . $dbuser . '@' . $dbhost . '</b>
                           <br /><br /><a href="install.php">Restart Installation</a>');
    }

    $db_structure_file = $eqdkp_root_path . 'dbal/structure/' . $dbtype . '_structure.sql';
    $db_data_file      = $eqdkp_root_path . 'dbal/structure/' . $dbtype . '_data.sql';

    $remove_remarks_function = $DBALS[$dbtype]['comments'];

    // I require MySQL version 4.0.4 minimum.
    $server_version = mysql_get_server_info();
    $client_version = mysql_get_client_info();

    if ( (isset($server_version) && isset($client_version)) ) {

        $tpl->message_append('MySQL client <b>and</b> server version 4.0.4 or higher and InnoDB table support are required for EQdkp.<br>
                              <b><br>You are running server version <ul>' . $server_version . '</ul> and client version <ul>' . $client_version . '.</ul></b><br>
                              MySQL versions less than 4.0.4 will not work and are not supported. Versions less than 4.0.4<br>
                              will experience data corruption, and we will not provide support for these installations.<br><br>');
    }
    else
    {
           		$tpl->message_die('Failed to get version information for database <b>' . $dbname . '</b> as <b>' . $dbuser . '@' . $dbhost . '</b>
                           <br /><br /><a href="install.php">Restart Installation</a>');
    }

    // Parse structure file and create database tables
    $sql = @fread(@fopen($db_structure_file, 'r'), @filesize($db_structure_file));
    $sql = preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql);

    $sql = $remove_remarks_function($sql);
    $sql = parse_sql($sql, $DBALS[$dbtype]['delim']);

    $sql_count = count($sql);
    $i = 0;

    while ( $i < $sql_count )
    {

	if (isset($sql[$i]) && $sql[$i] != "") {

		if ( !($db->query($sql[$i]) )) {
           		$tpl->message_die('Failed to connect to database <b>' . $dbname . '</b> as <b>' . $dbuser . '@' . $dbhost . '</b>
                           <br /><br /><a href="install.php">Restart Installation</a>');

		}
	}

        $i++;

    }
    unset($sql);

    // Parse the data file and populate the database tables
    $sql = @fread(@fopen($db_data_file, 'r'), @filesize($db_data_file));
    $sql = preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql);

    $sql = $remove_remarks_function($sql);
    $sql = parse_sql($sql, $DBALS[$dbtype]['delim']);

    $sql_count = count($sql);
    $i = 0;

    while ( $i < $sql_count )
    {

	if (isset($sql[$i]) && $sql[$i] != "") {

		if ( !($db->query($sql[$i]) )) {
           		$tpl->message_die('Failed to connect to database <b>' . $dbname . '</b> as <b>' . $dbuser . '@' . $dbhost . '</b>
                           <br /><br /><a href="install.php">Restart Installation</a>');

		}
	}

        $i++;

    }

    unset($sql);
    //
    // Update some config settings
    //
    $db->query('UPDATE ' . CONFIG_TABLE . " SET config_name='eqdkp_start' WHERE config_name='" . $table_prefix . "start'");

    $db->query("UPDATE " . CONFIG_TABLE . " SET config_value='".$server_name."' WHERE config_name='server_name'");
    $db->query("UPDATE " . CONFIG_TABLE . " SET config_value='".$server_port."' WHERE config_name='server_port'");
    $db->query("UPDATE " . CONFIG_TABLE . " SET config_value='".$server_path."' WHERE config_name='server_path'");
    $db->query("UPDATE " . CONFIG_TABLE . " SET config_value='".$default_lang."' WHERE config_name='default_lang'");
    $db->query("UPDATE " . CONFIG_TABLE . " SET config_value='".$default_locale."' WHERE config_name='default_locale'");

    //
    // Write the config file
    //

    $config_file  = "";
    $config_file .= "<?php\n\n";
    $config_file .= "\$dbtype       = '" . $dbtype        . "'; \n";
    $config_file .= "\$dbhost       = '" . $dbhost        . "'; \n";
    $config_file .= "\$dbname       = '" . $dbname        . "'; \n";
    $config_file .= "\$dbuser       = '" . $dbuser        . "'; \n";
    $config_file .= "\$dbpass       = '" . $dbpass        . "'; \n";
    $config_file .= "\$ns           = '" . $server_name   . "'; \n";
    $config_file .= "\$debug        = '0';                      \n";
    $config_file .= "\$table_prefix = '" . $table_prefix  . "';\n\n";
    $config_file .= "?>";

    // Set our permissions to execute-only
    @umask(0111);

    if ( !$fp = @fopen('config.php', 'w') )
    {
        $error_message  = 'The <b>config.php</b> file couldn\'t be opened for writing.  Paste the following in to config.php and save the
                           file to continue:<br /><pre>' . $config_file . '</pre>';
        $tpl->error_append($error_message);
    }
    else
    {
        @fputs($fp, $config_file);
        @fclose($fp);

        $tpl->message_append('Your configuration file has been written with the initial values, but installation will not be complete until
                              you create an administrator account in the next step.');
    }

    //
    // Output the page
    //
    $tpl->page_header();
    $tpl->page_tail();
}

function process_step4()
{
    global $eqdkp_root_path, $DEFAULTS;

    $tpl = new Template_Wrap('install_step4.html');

    //
    // Get our posted data
    //
    $username       = post_or_db('username');
    $user_password1 = post_or_db('user_password1');
    $user_password2 = post_or_db('user_password2');
    $user_email     = post_or_db('user_email');

    //
    // Update admin account
    //
    include($eqdkp_root_path . 'config.php');
    define('CONFIG_TABLE', $table_prefix . 'config');
    define('USERS_TABLE',  $table_prefix . 'users');
    define('STYLES_TABLE', $table_prefix . 'styles');

    define('DEBUG', 0);
    switch ( $dbtype )
    {
        case 'mysql':
            include_once($eqdkp_root_path . 'dbal/mysql.php');
            break;
        default:
            include_once($eqdkp_root_path . 'dbal/mysql.php');
            break;
    }

    $db = new SQL_DB($dbhost, $dbname, $dbuser, $dbpass, false);

    $sql = 'SELECT config_value FROM ' . CONFIG_TABLE . " WHERE config_name='default_lang'";
    $default_lang = $db->query_first($sql);

    $query = $db->build_query('UPDATE', array(
        'username'      => $username,
        'user_password' => ( $user_password1 == $user_password2 ) ? md5($user_password1) : md5('admin'),
        'user_lang'     => $default_lang,
        'user_email'    => $user_email,
        'user_active'   => '1')
    );
    $db->query('UPDATE ' . USERS_TABLE . ' SET ' . $query . " WHERE user_id='1'");

    $db->query("UPDATE " . CONFIG_TABLE . " SET config_value='".$user_email."' WHERE config_name='admin_email'");


    //
    // Rewrite the config file to its final form
    //
    $config_file  = file($eqdkp_root_path . 'config.php');
    $config_file  = implode("\n", $config_file);
    $config_file  = preg_replace('#\?>$#', '', $config_file);
    $config_file .= 'define(\'EQDKP_INSTALLED\', true);' . "\n";
    $config_file .= '?>';

    // Set our permissions to execute-only
    @umask(0111);

    if ( !$fp = @fopen('config.php', 'w') )
    {
        $error_message  = 'The <b>config.php</b> file couldn\'t be opened for writing.  Paste the following in to config.php and save the
                           file to continue:<br /><pre>' . htmlspecialchars($config_file) . '</pre>';
        $tpl->error_append($error_message);
    }
    else
    {
        @fputs($fp, $config_file, strlen($config_file));
        @fclose($fp);
    }

    //
    // Print out the login form
    //
    if ( $user_password1 != $user_password2 )
    {
        $tpl->message_append('<span style="font-weight: bold; font-size: 14px;" class="negative">NOTICE</span><br /><br />Your passwords did
                              not match, so it has been reset to <b>admin</b>.  You can change it by logging in and going to your account settings.');
    }

    $tpl->message_append('Your administrator account has been created, log in above to be taken to the EQdkp configuration page.');

    $tpl->page_header();
    $tpl->page_tail();
}

// ---------------------------------------------------------
// Functions!
// ---------------------------------------------------------
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
* Checks if a POST field value exists;
* If it does, we use that one, otherwise we use the optional database field value,
* or return a null string if $db_row contains no data
*
* @param    string  $post_field POST field name
* @param    array   $db_row     Array of DB values
* @param    string  $db_field   DB field name
* @return   string
*/
function post_or_db($post_field, $db_row = array(), $db_field = '')
{
    if ( @sizeof($db_row) > 0 )
    {
        if ( $db_field == '' )
        {
            $db_field = $post_field;
        }

        $db_value = $db_row[$db_field];
    }
    else
    {
        $db_value = '';
    }

    return ( (isset($_POST[$post_field])) || (!empty($_POST[$post_field])) ) ? $_POST[$post_field] : $db_value;
}

/**
* Removes comments from a SQL data file
*
* @param    string  $sql    SQL file contents
* @return   string
*/
function remove_remarks($sql)
{
    if ( $sql == '' )
    {
        die('Could not obtain SQL structure/data');
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
* Parse multi-line SQL statements into a single line
*
* @param    string  $sql    SQL file contents
* @param    char    $delim  End-of-statement SQL delimiter
* @return   array
*/
function parse_sql($sql, $delim)
{
    if ( $sql == '' )
    {
        die('Could not obtain SQL structure/data');
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
?>
