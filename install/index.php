<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * index.php
 * Began: Sun June 22 2003
 *
 * $Id: install.php 952 2007-11-29 20:41:43Z wallenium $
 ******************************/

// ---------------------------------------------------------
// Set up environment
// ---------------------------------------------------------
//
define('EQDKP_INC', true);
error_reporting(E_ALL);

$eqdkp_root_path = '../';

// Get the config file
if ( file_exists($eqdkp_root_path . 'config.php') )
{
    include_once($eqdkp_root_path . 'config.php');
}

include_once($eqdkp_root_path . 'includes/functions.php');
require_once($eqdkp_root_path . 'pluskernel/plusversion.php');
require_once($eqdkp_root_path . 'install/functions.php');


set_magic_quotes_runtime(0);
if ( !get_magic_quotes_gpc() )
{
    $_GET = slash_global_data($_GET);
    $_POST = slash_global_data($_POST);
}

// ---------------------------------------------------------
// Language
// ---------------------------------------------------------
if(isset($_GET['lang'])){
	SetLanguageCookie($_GET['lang']);
	header("Location: index.php");
}
$language = (isset($_COOKIE['eqdkpInstLanguage'])) ? $_COOKIE['eqdkpInstLanguage'] : 'english';

if( !include_once($eqdkp_root_path .'language/'.$language.'/lang_install.php') )
{
  die('Could not include the language files! Check to make sure that "' . $eqdkp_root_path . 'language/english/lang_install.php" exists!');
}

// ---------------------------------------------------------
// Template Wrap class
// ---------------------------------------------------------
if ( !include_once($eqdkp_root_path . 'includes/class_template.php') )
{
    die(sprintf($lang['error_template'],$eqdkp_root_path));
}

class Template_Wrap extends Template
{
    var $error_message   = array();           // Array of errors      @var $error_message
    var $install_message = array();           // Array of messages    @var $install_message
    var $header_inc      = false;             // Printed header?      @var $header_inc
    var $tail_inc        = false;             // Printed footer?      @var $tail_inc
    var $template_file   = '';                // Template filename    @var $template_file
    var $default_game 	 = '';				  // Defaultgame		  @var $default_game
    var $game_language 	 = '';				  // Defaultgame		  @var $default_game

    function template_wrap($template_file)
    {
      global $lang;
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
      global $lang;
        $this->set_filenames(array(
            'body' => 'install_message.html')
        );

        $this->assign_vars(array(
            'MSG_TITLE' => ( $title != '' ) ? $title : '&nbsp;',
            'MSG_TEXT'  => ( $text  != '' ) ? $text  : '&nbsp;',
            )
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
      global $lang;
        sort($this->install_message);
        reset($this->install_message);

        $install_message = implode('<br /><br />', $this->install_message);

        if ( $die )
        {
            $this->message_die($install_message, $lang['installation'].' ' . (( sizeof($this->install_message) == 1 ) ? $lang['note'] : $lang['notes']));
        }
        else
        {
            $this->assign_vars(array(
                'MSG_TITLE' => $lang['installation'].' ' . (( sizeof($this->install_message) == 1 ) ? $lang['note'] : $lang['notes']),
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
      global $lang;
        sort($this->error_message);
        reset($this->error_message);

        $error_message = implode('<br /><br />', $this->error_message);

        if ( $die )
        {
            $this->message_die($error_message, $lang['installation'].' ' . (( sizeof($this->error_message) == 1 ) ? $lang['error'] : $lang['errors']));
        }
        else
        {
            $this->assign_vars(array(
                'MSG_TITLE' => $lang['installation'].' ' . (( sizeof($this->error_message) == 1 ) ? $lang['error'] : $lang['errors']),
                'MSG_TEXT'  => $error_message)
            );
        }
    }

    function page_header()
    {
        global $STEP, $lang;

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
            'INSTALL_STEP'  => $STEP,
            'L_STEP'        => $lang['inst_step'],
            'L_HEADER'      => $lang['inst_header']
            )
        );

        $stepname = array(
          1 => $lang['stepname_1'],
          2 => $lang['stepname_2'],
          3 => $lang['stepname_3'],
          4 => $lang['stepname_4'],
          5 => $lang['stepname_5'],
          6 => $lang['stepname_6'],
        );

        // status indicator
        foreach($stepname as $stepno => $sstep){
          $this->assign_block_vars('step_row', array(
                'VALUE'     => $sstep,
  					    'SELECTED'  => ($stepno == $STEP) ? ' class="current"' : '',
  					    )
            );
        }
    }

    function page_tail()
    {
        global $DEFAULTS, $db, $lang;

        $this->assign_var('S_SHOW_BUTTON', true);

        if ( sizeof($this->install_message) > 0 )
        {
            $this->message_out(false);
        }

        if ( sizeof($this->error_message) > 0 )
        {
            $this->assign_var('S_SHOW_BUTTON', false);
            $this->error_message[0] = '<span style="font-weight: bold; font-size: 14px;" class="negative">'.$lang['notice'].'</span>';
            $this->error_out(false);
        }

        $this->assign_var('EQDKP_VERSION', EQDKPPLUS_VERSION);

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
    $tpl->message_die($lang['already_installed'], $lang['install_error']);
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
    'default_style' => '1',
    'table_prefix'  => 'eqdkp_',
    'dbal'          => 'mysql',
    'default_game'  => 'WoW',
    'default_lang'  => 'german'
);
$DBALS    = array(
    'mysql' => array(
        'label'       => 'MySQL',
        'structure'   => 'mysql',
        'comments'    => 'remove_remarks',
        'delim'       => ';',
        'delim_basic' => ';'
    )
);

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
    case 5:
        process_step5();
        break;
    case 6:
        process_step6();
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
    global $eqdkp_root_path, $DEFAULTS, $lang, $language;

    $tpl = new Template_Wrap('install_step1.html');

    //
    // Check to make sure config.php exists and is readable / writeable
    //
    $config_file = $eqdkp_root_path . 'config.php';
    if ( !file_exists($config_file) )
    {
        if ( !@touch($config_file) )
        {
            $tpl->error_append($lang['conf_not_write']);
        }
        else
        {
            $tpl->message_append($lang['conf_written']);
        }
    }
    else
    {
        if ( (!is_writeable($config_file)) || (!is_readable($config_file)) )
        {
            if ( !@chmod($config_file, 0666) )
            {
                $tpl->error_append($lang['conf_chmod']);
            }
            else
            {
                $tpl->message_append($lang['conf_writable']);
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
            $tpl->error_append($lang['templcache_notcreated']);
        }
        else
        {
            $tpl->message_append($lang['templatecache_created']);
        }
    }
    else
    {
        if ( !is_writeable($cache_directory) )
        {
            if ( !@chmod($cache_directory, 0777) )
            {
                $tpl->error_append($lang['templatecache_chmod']);
            }
            else
            {
                $tpl->message_append();
            }
        }
        // Cache directory exists and is writeable, we're good to go
    }
    clearstatcache();

    //
    // Server settings
    //
    // EQdkp versions
    $their_eqdkpp_version = $lang['inst_unknown'];
    $plusfile = @file('http://vcheck.eqdkp-plus.com/version/pluskernel');
    if (!$plusfile[0] )
    {
        $their_eqdkpp_version = $lang['connection_failed'];
    }
    else
    {
      $parse = explode('|' ,$plusfile[0]);
      if ( $parse[0])
      {
        $their_eqdkpp_version = $parse[0];
      }
    }

    // PHP Versions
    $our_php_version   = (( phpversion() >= '4.1.2' ) ? '<span class="positive">' : '<span class="negative">') . phpversion() . '</span>';
    $their_php_version = '4.1.2+';

    // Modules
    $our_mysql   = ( extension_loaded('mysql') ) ? '<span class="positive">'.$lang['inst_yes'].'</span>' : '<span class="negative">'.$lang['inst_no'].'</span>';
    $their_mysql = $lang['inst_yes'];
    $our_zlib    = ( extension_loaded('zlib') )  ? '<span class="positive">'.$lang['inst_yes'].'</span>' : '<span class="negative">'.$lang['inst_no'].'</span>';
    $their_zlib  = $lang['inst_no'];

    if(function_exists('curl_version'))
    {
    	$our_curl =	curl_version();
		if (is_array($our_curl))
		{
			$our_curl =  '<span class="positive">'.$lang['inst_yes'].' ('.$our_curl['version'].')</span>' ;
		}
		else
		{
			$our_curl =  '<span class="positive">'.$lang['inst_yes'].' ('.$our_curl.')</span>' ;
		}

    }
    else
    {
     $our_curl='<span class="negative">'.$lang['inst_no'].'</span>';
     $tpl->message_append('<b>'.$lang['lerror'].':</b> '.$lang['curl_notavailable']);

    }
    $their_curl  = $lang['inst_yes'];


    if(function_exists('fopen'))
    {
    	$our_fopen = '<span class="positive">'.$lang['inst_yes'].'</span>' ;
    }
    else
    {
     $our_fopen = '<span class="negative">'.$lang['inst_no'].'</span>' ;
     $tpl->message_append('<b>'.$lang['lerror'].':</b> '.$lang['fopen_notavailable']);
    }
    $their_fopen  = $lang['inst_yes'];

    if ( (phpversion() < '4.1.2') || (!extension_loaded('mysql')) )
    {
        $tpl->error_append('<span style="font-weight: bold; font-size: 14px;">'.$lang['minimal_requ_notfilled'].'</span>');
    }
    else
    {
        $tpl->message_append($lang['minimal_requ_filled']);
    }

    // installation language
    $lang_path = $eqdkp_root_path . 'language/';
    if ( $dir = @opendir($lang_path) )
    {
      while ( $file = @readdir($dir) )
      {
        $lang_folder = $lang_path . $file;
        if ( (!is_file($lang_folder)) && (is_file($lang_folder.'/lang_install.php')) && (!is_link($lang_folder)) && valid_folder($file) )
        {
          $tpl->assign_block_vars('lang_row', array(
              'VALUE'     => $file,
              'OPTION'    => ucfirst(strtolower($file)),
					    'SELECTED'  => option_selected(strtolower($file) == strtolower($language)),
					    )
          );
        }
      }
    }

    //
    // Output the page
    //
    $tpl->assign_vars(array(
        'OUR_EQDKPP_VERSION'  => EQDKPPLUS_VERSION,
        'THEIR_EQDKPP_VERSION'=> $their_eqdkpp_version,
        'OUR_PHP_VERSION'     => $our_php_version,
        'THEIR_PHP_VERSION'   => $their_php_version,
        'OUR_MYSQL'           => $our_mysql,
        'THEIR_MYSQL'         => $their_mysql,
        'OUR_ZLIB'            => $our_zlib,
        'THEIR_ZLIB'          => $their_zlib,
        'OUR_CURL'		        => $our_curl,
        'THEIR_CURL'          => $their_curl,
        'OUR_FOPEN'		        => $our_fopen,
        'THEIR_FOPEN'         => $their_fopen,

        'L_LANGUAGE_SELECT'   => $lang['language_selector'],
        'L_INSTALL_LANGUAGE'  => $lang['install_language'],
        'L_SERVER_VERSION'    => $lang['inst_latest'],
        'L_EQDKPPVERSION'     => $lang['inst_eqdkpv'],
        'L_EQDKPPNAME'        => $lang['eqdkp_name'],
        'L_INST_PHP'          => $lang['inst_php'],
        'L_VIEWPHPINFO'       => $lang['inst_view'],
        'L_VERSION'           => $lang['inst_version'],
        'L_REQUIRED'          => $lang['inst_required'],
        'L_AVAILABLE'         => $lang['inst_available'],
        'L_USING'             => $lang['inst_using'],
        'L_MYSQLMODULE'       => $lang['inst_mysqlmodule'],
        'L_CURLMODULE'        => $lang['inst_curlmodule'],
        'L_ZLIBMODULE'        => $lang['inst_zlibmodule'],
        'L_FOPEN'             => $lang['inst_fopen'],
        'L_INST_BUTTON1'      => $lang['inst_button1'],
        )
    );

    $tpl->page_header();
    $tpl->page_tail();
}

function process_step2()
{
    global $eqdkp_root_path, $DEFAULTS, $DBALS, $LOCALES, $lang;

    $tpl = new Template_Wrap('install_step2.html');

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

  // Output the page
    //
    $tpl->assign_vars(array(
        'DB_HOST'             => 'localhost',
        'TABLE_PREFIX'        => $DEFAULTS['table_prefix'],

        'L_DATABASE_CONF'     => $lang['inst_database_conf'],
        'L_DATABASE_TYPE'     => $lang['inst_dbtype'],
        'L_DATABASE_HOST'     => $lang['inst_dbhost'],
        'L_DATABASE_NAME'     => $lang['inst_dbname'],
        'L_DATABASE_USER'     => $lang['inst_dbuser'],
        'L_DATABASE_PW'       => $lang['inst_dbpass'],
        'L_DATABASE_PREFIX'   => $lang['inst_table_prefix'],
        'L_BUTTON_2'          => $lang['inst_button2'],
        )
    );

    $tpl->page_header();
    $tpl->page_tail();
}

function process_step3()
{
    global $eqdkp_root_path, $DEFAULTS, $DBALS, $LOCALES, $lang;

    $tpl = new Template_Wrap('install_step3.html');
    $dbtype       = post_or_db('dbtype');
    $dbhost       = post_or_db('dbhost');
    $dbname       = post_or_db('dbname');
    $dbuser       = post_or_db('dbuser');
    $dbpass       = post_or_db('dbpass');
    $table_prefix = post_or_db('table_prefix', $DEFAULTS);

    //
    // Database Check
    //
    $error = array();
    $connect_test = connect_check_db(true, $error, $DBALS[$dbtype], $table_prefix, $dbhost, $dbuser, $dbpass, $dbname, 80);
    if(count($error) > 0){
      $db_message = '';
      foreach($error as $amsg){
       $db_message .= $amsg.'<br/>';
      }
      $db_header = $lang['db_warning'];
    }else{
      $db_message = $lang['insinfo_dbready'];
      $db_header = $lang['db_information'];
    }

    // Output the page
    //
    // I require MySQL version 4.0.4 minimum.
    $server_version = mysql_get_server_info();
    $client_version = mysql_get_client_info();
    $tpl->message_append('<span style="font-weight: bold; font-size: 14px;" class="negative">'.$db_header.'</span><br /><br />'.$db_message);
    if ( (isset($server_version) && isset($client_version)) ) {
      $sql_infomssg = sprintf($lang['inst_mysqlinfo'], $server_version, $client_version);
    }else{
      $sql_infomssg = sprintf($lang['inst_failedversioninfo'], $dbname, $dbuser, $dbhost);
    }

    $tpl->assign_vars(array(
        'DB_HOST'      => 'localhost',
        'TABLE_PREFIX' => $DEFAULTS['table_prefix'],
        'DB_PASS'      => $dbpass,
        'DB_USER'      => $dbuser,
        'DB_HOST'      => $dbhost,
        'TABLE_PREFIX' => $table_prefix,
        'DB_NAME'      => $dbname,
        'DB_TYPE'      => $dbtype,
        'SQL_TITLE'    => $lang['inst_sqlheaderbox'],
        'SQL_TEXT'     => $sql_infomssg,
        'L_BUTTON3'    => $lang['inst_button3'],
        )
    );

    $tpl->page_header();
    $tpl->page_tail();
}

function process_step4()
{
    global $eqdkp_root_path, $DEFAULTS, $DBALS, $LOCALES, $lang;

    $tpl = new Template_Wrap('install_step4.html');
    $dbtype       = post_or_db('dbtype');
    $dbhost       = post_or_db('dbhost');
    $dbname       = post_or_db('dbname');
    $dbuser       = post_or_db('dbuser');
    $dbpass       = post_or_db('dbpass');
    $table_prefix = post_or_db('table_prefix', $DEFAULTS);

    //
    // Build the default language drop-down
    //
    $lang_path = $eqdkp_root_path . 'language/';
    if ( $dir = @opendir($lang_path) )
    {
      while ( $file = @readdir($dir) )
      {
        if ( (!is_file($lang_path . $file)) && (!is_link($lang_path . $file)) && valid_folder($file) )
        {
          $tpl->assign_block_vars('language_row', array(
              'VALUE'     => $file,
              'OPTION'    => ucfirst(strtolower($file)),
			  'SELECTED'  => option_selected(strtolower($file) == strtolower($lang['default_lang'])),
					    )
          );
        }
      }
    }

    foreach ( $LOCALES as $locale_type => $locale_desc )
    {
      $tpl->assign_block_vars('locale_row', array(
            'VALUE'    => $locale_desc['type'],
            'OPTION'   => $locale_type,
			'SELECTED' => option_selected(strtolower($locale_desc['type']) == strtolower($lang['default_locale'])),
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

    $server_path = str_replace('install/index.php', '', $_SERVER['PHP_SELF']);

    $tpl->message_append($lang['inst_checkifdbexists']);

    //
    // Standard Game
    //
    $games = array();
    if ( $dir = opendir($eqdkp_root_path . 'games/') )
    {
      while ( $d_plugin_code = @readdir($dir) )
      {
        $cwd = $eqdkp_root_path . 'games/'.$d_plugin_code.'/index.php'; // regenerate the link to the 'plugin'
        if((@is_file($cwd)) && valid_folder($d_plugin_code))
        {  // check if valid
          @array_push($games, $d_plugin_code);  // add to array
        }
      }
    }

    foreach ( $games as $game )
    {
      $tpl->assign_block_vars('game_row', array(
                  'VALUE'    => $game,
                  'SELECTED' => option_selected(strtolower($game) == strtolower($DEFAULTS['default_game'])),
                  'OPTION'   => $game
      ));
    }
    unset($games);

    // build game language
    $glanguagearray = array(
                        'en'  => 'English',
                        'de'  => 'German'
                        );
    foreach ( $glanguagearray as $gamelang=>$glangname){
      $tpl->assign_block_vars('gamelang_row', array(
                  'VALUE'    => $gamelang,
                  'SELECTED' => option_selected(strtolower($gamelang) == strtolower($lang['game_language'])),
                  'OPTION'   => $glangname
      ));
    }
    unset($glanguagearray);

    //
    // Output the page
    //
    $tpl->assign_vars(array(
        'SERVER_NAME'  => $server_name,
        'SERVER_PORT'  => $server_port,
        'SERVER_PATH'  => $server_path,
        'DB_PASS'      => $dbpass,
        'DB_USER'      => $dbuser,
        'DB_HOST'      => $dbhost,
        'TABLE_PREFIX' => $table_prefix,
        'DB_NAME'      => $dbname,
        'DB_TYPE'      => $dbtype,

        'L_LANG_CONF'  => $lang['inst_language_config'],
        'L_DEF_LANG'   => $lang['inst_default_lang'],
        'L_DEF_LOCA'   => $lang['inst_default_locale'],
        'L_GAME_CONF'  => $lang['inst_game_config'],
        'L_DEF_GAME'   => $lang['inst_default_game'],
        'L_SERVER_CONF'=> $lang['inst_server_config'],
        'L_SERVERNAME' => $lang['inst_server_name'],
        'L_SERVERPORT' => $lang['inst_server_port'],
        'L_SERVERPATH' => $lang['inst_server_path'],
        'L_BUTTON4'    => $lang['inst_button4'],
        )
    );


    $tpl->page_header();
    $tpl->page_tail();
}

function process_step5()
{
    global $eqdkp_root_path, $DEFAULTS, $DBALS, $LOCALES, $lang;

    $tpl = new Template_Wrap('install_step5.html');

    //
    // Get our posted data
    //

    $dbtype       				= post_or_db('dbtype');
    $dbhost       				= post_or_db('dbhost');
    $dbname       				= post_or_db('dbname');
    $dbuser       				= post_or_db('dbuser');
    $dbpass       				= post_or_db('dbpass');
    $default_locale       		= post_or_db('default_locale');
    $table_prefix 				= post_or_db('table_prefix', $DEFAULTS);
    $server_name  				= post_or_db('server_name');
    $server_port  				= post_or_db('server_port');
    $server_path  				= post_or_db('server_path');
    $default_game 				= post_or_db('default_game');
    $game_language				= post_or_db('game_language');
    $default_lang 				= post_or_db('default_lang', $DEFAULTS);

    define('CONFIG_TABLE', $table_prefix . 'config');
    define('USERS_TABLE',  $table_prefix . 'users');

    $dbal_file = $eqdkp_root_path . 'includes/db/' . $dbtype . '.php';
    if ( !file_exists($dbal_file) )
    {
        $tpl->message_die(sprintf($lang['inst_wrong_dbtype'],$dbtype, $dbal_file));
    }

    //
    // Database population
    //
    //define('DEBUG', 2);
    include_once($dbal_file);
    $db = new SQL_DB($dbhost, $dbname, $dbuser, $dbpass, false);

    // Check to make sure a connection was made
    if ( !is_resource($db->link_id) )
    {
      $tpl->message_die(sprintf($lang['inst_failedconhost'], $dbname, $dbuser, $dbhost));
    }

    $db_structure_file = $eqdkp_root_path . 'install/schemas/' . $dbtype . '_structure.sql';
    $db_data_file      = $eqdkp_root_path . 'install/schemas/' . $dbtype . '_data.sql';

    $remove_remarks_function = $DBALS[$dbtype]['comments'];

    // Parse structure file and create database tables
    $sql = @fread(@fopen($db_structure_file, 'r'), @filesize($db_structure_file));
    $sql = preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql);

    $sql = $remove_remarks_function($sql);
    $sql = parse_sql($sql, $DBALS[$dbtype]['delim']);

    $sql_count = count($sql);
    $i = 0;

    while ( $i < $sql_count )
    {

	if (isset($sql[$i]) && $sql[$i] != "")
	{
		if ( !($db->query($sql[$i]) ))
		{
        	$tpl->message_die(sprintf($lang['inst_sql_error'], $sql[$i]));
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
		if (isset($sql[$i]) && $sql[$i] != "")
		{
			if ( !($db->query($sql[$i]) ))
			{
	      		$tpl->message_die(sprintf($lang['inst_sql_error'], $sql[$i]));
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
    $config_file .= "?".">";

   	// Setup the Game
  	$default_game_file = $eqdkp_root_path . 'games/' . $default_game . '/index.php' ;
	if (file_exists($default_game_file))
	{
    	include($default_game_file);
    	include($eqdkp_root_path.'pluskernel/include/games.class.php');
    	$manage = new Manage_Game;
		$manage->do_it($db,$table_prefix,true,$game_language,true);
	}

    // Set our permissions to execute-only
    @umask(0111);

    if ( !$fp = @fopen('../config.php', 'w') )
    {
        $error_message  = $lang['inst_writerr_confile'].'<br /><pre>' . $config_file . '</pre>';
        $tpl->error_append($error_message);
    }
    else
    {
        @fputs($fp, $config_file);
        @fclose($fp);

        $tpl->message_append($lang['inst_confwritten']);
    }

    //
    // Output the page
    //
    $tpl->assign_vars(array(
        'L_ADMINACC'   => $lang['inst_administrator_config'],
        'L_ADM_USERN'  => $lang['inst_username'],
        'L_ADM_PW'     => $lang['inst_user_password'],
        'L_ADM_PW_CONF'=> $lang['inst_user_pw_confirm'],
        'L_ADM_EMAIL'  => $lang['inst_user_email'],
        'L_BUTTON5'    => $lang['inst_button5'],
        )
    );
    $tpl->page_header();
    $tpl->page_tail();
}

function process_step6()
{
    global $eqdkp_root_path, $DEFAULTS, $lang;

    $tpl = new Template_Wrap('install_step6.html');

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

    //define('DEBUG', 0);
    switch ( $dbtype )
    {
        case 'mysql':
            include_once($eqdkp_root_path . 'includes/db/mysql.php');
            break;
        default:
            include_once($eqdkp_root_path . 'includes/db/mysql.php');
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
    $config_file  = preg_replace('#\?'.'>$#', '', $config_file);
    $config_file .= 'define(\'EQDKP_INSTALLED\', true);' . "\n";
    $config_file .= '?'.'>';

    // Set our permissions to execute-only
    @umask(0111);

    if ( !$fp = @fopen('../config.php', 'w') )
    {
        $error_message  = $lang['inst_writerr_confile'].'<br /><pre>' . htmlspecialchars($config_file) . '</pre>';
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
        $tpl->message_append('<span style="font-weight: bold; font-size: 14px;" class="negative">'.$lang['notice'].'</span><br /><br />'.$lang['inst_passwordnotmatch']);
    }

    $tpl->message_append($lang['inst_admin_created']);

    $tpl->assign_vars(array(
        'L_LOGIN'      => $lang['login'],
        'L_USERNAME'   => $lang['username'],
        'L_PASSWORD'   => $lang['password'],
        'L_COOKIE_SET' => $lang['remember_password'],
        'L_LOGBUTTON'  => $lang['login_button'],
        'L_BUTTON5'    => $lang['inst_button5'],
        )
    );

    $tpl->page_header();
    $tpl->page_tail();
}

?>
