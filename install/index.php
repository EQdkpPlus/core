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

// ---------------------------------------------------------
// Set up environment
// ---------------------------------------------------------
//
define('EQDKP_INC', true);
$eqdkp_root_path = '../';

ini_set("display_errors", 0);
// auf die benutzerdefinierte Fehlerbehandlung umstellen
require_once($eqdkp_root_path.'core/plus_debug_logger.class.php');
$pdl = new plus_debug_logger();
set_error_handler(array($pdl,'myErrorHandler'));
register_shutdown_function(array($pdl, "catch_fatals"));
$pdl->set_debug_level(4);
$pdl->register_type("install_error", null, null, array(4));

// Get the config file
if ( file_exists($eqdkp_root_path . 'config.php') ){
	include_once($eqdkp_root_path . 'config.php');
}

// Requirements
require($eqdkp_root_path . 'core/config.class.php');
require($eqdkp_root_path . 'core/core.php');
include_once($eqdkp_root_path . 'core/core.functions.php');
require_once($eqdkp_root_path . 'core/input.class.php');
require_once($eqdkp_root_path . 'install/functions.php');
require_once($eqdkp_root_path . 'libraries/libraries.php');
require_once($eqdkp_root_path . 'core/file_handler/file_handler.class.php');
require_once($eqdkp_root_path . 'core/plus.class.php');

// Init required classes
$libloader		= new libraries();
$pcache			= new file_handler;
$in				= new Input();
$html			= new myHTML('pluskernel');
$plus			= new plus();

// Set the plus Version for install
include_once($eqdkp_root_path . 'install/schemas/config_data.php');

set_magic_quotes_runtime(0);
if ( !get_magic_quotes_gpc() ){
	$_GET	= slash_global_data($_GET);
	$_POST	= slash_global_data($_POST);
}

// ---------------------------------------------------------
// Language
// ---------------------------------------------------------
if(isset($_GET['lang'])){
	SetLanguageCookie($_GET['lang']);
	echo "<script>parent.window.location.href = 'index.php';</script>";
}
$language = (isset($_COOKIE['eqdkpInstLanguage'])) ? $_COOKIE['eqdkpInstLanguage'] : 'english';

if( !include_once($eqdkp_root_path .'language/'.$language.'/lang_install.php') ){
	die('Could not include the language files! Check to make sure that "' . $eqdkp_root_path . 'language/english/lang_install.php" exists!');
}
$inst_lang = $lang;

// ---------------------------------------------------------
// Template Wrap class
// ---------------------------------------------------------
if ( !include_once($eqdkp_root_path . 'gplcore/template.class.php') ){
	die(sprintf($inst_lang['error_template'],$eqdkp_root_path));
}

class Template_Wrap extends Template
{
	var $error_message		= array();			// Array of errors      @var $error_message
	var $install_message	= array();			// Array of messages    @var $install_message
	var $header_inc			= false;			// Printed header?      @var $header_inc
	var $tail_inc			= false;			// Printed footer?      @var $tail_inc
	var $template_file		= '';				// Template filename    @var $template_file
	var $default_game		= '';				// Defaultgame		  @var $default_game
	var $game_language		= '';				// Defaultgame		  @var $default_game
	var $icon_error			= '<img src="../templates/install/images/file_conflict.gif" height="16" align="absmiddle">&nbsp;';
	var $icon_ok			= '<img src="../templates/install/images/file_up_to_date.gif" height="16" align="absmiddle">&nbsp;';

	function __construct($template_file){
		global $inst_lang;
		$this->template_file = $template_file;

		$this->set_template('install', 'install');

		$this->assign_vars(array(
			'TYEAR'			=> date('Y', time()),
			'MSG_TITLE'		=> '',
			'MSG_TEXT'		=> '',
			'L_BUTTON_BACK'	=> $inst_lang['inst_button_back'])
		);

		$this->set_filenames(array(
			'body' => $this->template_file)
		);
	}
	
	function StatusIcon($mystat= 'ok'){
		return ($mystat=='ok') ? $this->icon_ok : $this->icon_error;
	}
	
	function message_die($text = '', $title = ''){
		global $inst_lang;
		$this->set_filenames(array(
			'body' => 'install_message.html')
		);

		$this->assign_vars(array(
			'MSG_TITLE'	=> ( $title != '' ) ? $title : '&nbsp;',
			'MSG_TEXT'	=> ( $text  != '' ) ? $text  : '&nbsp;',
		));

		if ( !$this->header_inc ){
			$this->page_header();
		}

		$this->page_tail();
	}

	function message_append($message){
		$this->install_message[ sizeof($this->install_message) + 1 ] = $message;
	}

	function message_out($die = false){
		global $inst_lang;
		sort($this->install_message);
		reset($this->install_message);

		$install_message = implode('<br /><br />', $this->install_message);
				
		if ( $die ){
			$this->message_die($install_message, (( sizeof($this->install_message) == 1 ) ? $inst_lang['installation_message'] : $inst_lang['installation_messages']));
		}else{
			$this->assign_vars(array(
				'MSG_TITLE'	=> (( sizeof($this->install_message) == 1 ) ? $inst_lang['installation_message'] : $inst_lang['installation_messages']),
				'MSG_TEXT'	=> $install_message)
			);
		}
	}

	function error_append($error){
		global $pdl;
		$pdl->log("install_error", $error);
	}

	function error_out($die = false){
		global $inst_lang, $pdl;

		$error_message = $pdl->get_html_log(4);
		$error_count = $pdl->get_log_size(4);

		$error_message = str_replace("install_error:", '<b>'.$inst_lang['install_error'].':</b>', $error_message);

		if ( $die ){
			$this->message_die($error_message, (( $error_count == 1 ) ? $inst_lang['error'] : $inst_lang['errors']));
		}else{
			$this->assign_vars(array(
				'MSG_TITLE'	=> (( $error_count == 1 ) ? $inst_lang['error'] : $inst_lang['errors']),
				'MSG_TEXT'	=> $error_message)
			);
		}
	}

	function page_header(){
		global $STEP, $inst_lang;

		$this->header_inc = true;

		$this->assign_vars(array(
			'INSTALL_STEP'	=> $STEP,
			'L_STEP'		=> ($STEP != 0) ? ' - '.$inst_lang['inst_step'].' '.$STEP : '',
			'L_HEADER'		=> $inst_lang['inst_header'],
		));

		$stepname = array(
			0	=> $inst_lang['stepname_0'],
			1	=> $inst_lang['stepname_1'],
			2	=> $inst_lang['stepname_2'],
			3	=> $inst_lang['stepname_3'],
			4	=> $inst_lang['stepname_4'],
			5	=> $inst_lang['stepname_5'],
			6	=> $inst_lang['stepname_6'],
			7	=> $inst_lang['stepname_7'],
		);

		// status indicator
		foreach($stepname as $stepno => $sstep){
			$this->assign_block_vars('step_row', array(
				'VALUE'		=> $sstep,
				'SELECTED'	=> ($stepno == $STEP) ? ' class="current"' : '',
			));
		}
	}

	function page_tail(){
		global $DEFAULTS, $db, $inst_lang, $pdl, $plus;

		$this->assign_var('S_SHOW_BUTTON', true);

		if ( sizeof($this->install_message) > 0 ){
			$this->message_out(false);
		}

		if($pdl->get_log_size(4) > 0){
			$this->assign_var('S_SHOW_BUTTON', false);
			$this->error_message[0] = "<img src='../images/false.png' alt='error' style='float:left; padding: 10px;'/>";
			$this->error_out(false);
		}

		$this->assign_var('EQDKP_VERSION', EQDKPPLUS_VERSION);

		if ( is_object($db) ){
			$db->close_db();
		}
		
		// Pass JS Code to template..
		if(!$this->tplout_set['js_code']){
			// JS in header...
			if(is_array($this->tpl_output['js_code'])){
				$imploded_jscode = implode("\n", $this->tpl_output['js_code']); 
				$this->assign_var('JS_CODE', (($debug) ? $imploded_jscode : $imploded_jscode));
				$this->tplout_set['js_code'] = true;
			}
			if(is_array($this->tpl_output['js_code_eop'])){
				$imploded_jscodeeop = implode("\n", $this->tpl_output['js_code_eop']); 
				$this->assign_var('JS_CODE_EOP', (($debug) ? $imploded_jscodeeop : $imploded_jscodeeop));
				$this->tplout_set['js_code'] = true;
			}
		}
				
		// Pass CSS Code to template..
		if(!$this->tplout_set['css_code']){
			if(is_array($this->tpl_output['css_code'])){
				$imploded_css = implode("\n", $this->tpl_output['css_code']);
				$this->assign_var('CSS_CODE', (($debug) ? $imploded_css : $imploded_css));
				$this->tplout_set['css_code'] = true;
			}
		}
				
		// Load the CSS Files..
		if(!$this->tplout_set['css_file']){
			if(is_array($this->tpl_output['css_file'])){
				$this->assign_var('CSS_FILES', $plus->implode_wrapped("<link rel='stylesheet' href='", "' type='text/css' />", "\n", $this->tpl_output['css_file']));
				$this->tplout_set['css_file'] = true;
			}
		}
	
		// Load the JS Files..
		if(!$this->tplout_set['js_file']){
			if(is_array($this->tpl_output['js_file'])){
				$this->assign_var('JS_FILES', $plus->implode_wrapped("<script type='text/javascript' src='../", "'></script>", "\n", $this->tpl_output['js_file']));
			}
			$this->tplout_set['js_file'] = true;
		}
			
		$this->display('header');
		$this->display('body');
		$this->display('footer');
		$this->destroy();
		exit;
	}
	
}

$STEP = ( isset($_GET['install_step']) ) ? $_GET['install_step'] : '0';
$STEP = ( isset($_POST['install_step']) ) ? $_POST['install_step'] : $STEP;
// Retry a step
if ( isset($_POST['retry']) && $_POST['install_step']){
	$STEP =  $_POST['install_step'] - 1;
}

// If EQdkp is already installed, don't let them install it again
if ( defined('EQDKP_INSTALLED') ){
	$tpl = new Template_Wrap('install_message.html');
	$tpl->message_die($inst_lang['already_installed'], $inst_lang['install_error']);
	exit();
}

// View phpinfo() if requested
if ( (isset($_GET['mode'])) && ($_GET['mode'] == 'phpinfo') ){
	phpinfo();
	exit;
}

// System defaults / available database abstraction layers
$DEFAULTS = array(
	'default_style'	=> '1',
	'table_prefix'	=> 'eqdkp07_',
	'dbal'			=> 'mysql',
	'default_game'	=> 'wow',
	'default_lang'	=> 'german'
);
$DBALS    = array(
	'mysql'			=> array(
	'label'			=> 'MySQL',
	'structure'		=> 'mysql',
	'comments'		=> 'remove_remarks',
	'delim'			=> ';',
	'delim_basic'	=> ';'
));

// ---------------------------------------------------------
// Figure out which step we are at the moment..
// ---------------------------------------------------------
switch ($STEP){
	case 0:
		process_step0();break;
	case 1:
		process_step1();break;
	case 2:
		process_step2();break;
	case 3:
		process_step3();break;
	case 4:
		process_step4();break;
	case 5:
		process_step5();break;
	case 6:
		process_step6();break;
	case 7:
		process_step7();break;
	case 8:
		process_step8();break;
	default:
		process_step1();break;
}

// ---------------------------------------------------------
// And do it
// ---------------------------------------------------------
function process_step0(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $inst_lang, $tpl, $html, $language;

	$tpl = new Template_Wrap('install_step0.html');

	// installation language
	if($dir = @opendir($eqdkp_root_path.'language/')){
		while ( $file = @readdir($dir) ){
			if ((!is_file($eqdkp_root_path.'language/'.$file)) && (!is_link($eqdkp_root_path.'language/'.$file)) && valid_folder($file)){
				include($eqdkp_root_path.'language/'.$file.'/lang_main.php');
				$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
				$language_array[$file]					= $lang_name_tp;
			}
		}
		$tpl->assign_var('LANG_DD', $html->DropDown('lang',$language_array, $language, '', 'onchange="javascript:form.submit();"'));
	}

	// Output the page
	$tpl->assign_vars(array(
		'L_WELCOME'				=> $inst_lang['welcome'],
		'L_LANGUAGE_SELECT'		=> $inst_lang['language_selector'],
		'L_INSTALL_LANGUAGE'	=> $inst_lang['install_language'],
		'L_LICENSE'				=> $inst_lang['license'],
		'L_LICENSE_TEXT'		=> $inst_lang['license_text'],
		'L_ACCEPT'				=> $inst_lang['accept'],
	));

	$tpl->page_header();
	$tpl->page_tail();
}

function process_step1(){
	global $eqdkp_root_path, $DEFAULTS, $inst_lang, $language, $html;

	$tpl = new Template_Wrap('install_step1.html');

	// EQdkp versions
	$their_eqdkpp_version = $inst_lang['inst_unknown'];
	$plusfile = @file('http://vcheck.eqdkp-plus.com/version/pluskernel');
	if (!$plusfile[0] ){
		$their_eqdkpp_version = $inst_lang['connection_failed'];
	}else{
		$parse = explode('|' ,$plusfile[0]);
		if ( $parse[0]){
			$their_eqdkpp_version = $parse[0];
		}
	}

	// PHP Versions
	$our_php_version		= (( phpversion() >= REQUIRED_PHP_VERSION ) ? '<span class="positive">' : '<span class="negative">') . phpversion() . '</span>';
	$our_php_status			= ( phpversion() >= REQUIRED_PHP_VERSION ) ? $tpl->StatusIcon('ok') : $tpl->StatusIcon('error');
	$their_php_version		= REQUIRED_PHP_VERSION.'+';

// Modules
	$our_mysql				= ( extension_loaded('mysql') ) ? '<span class="positive">'.$inst_lang['inst_yes'].'</span>' : '<span class="negative">'.$inst_lang['inst_no'].'</span>';
	$our_mysql_status   	= ( extension_loaded('mysql') ) ? $tpl->StatusIcon('ok') : $tpl->StatusIcon('error');
	$their_mysql			= $inst_lang['inst_yes'];

	$our_zlib				= ( extension_loaded('zlib') )  ? '<span class="positive">'.$inst_lang['inst_yes'].'</span>' : '<span class="negative">'.$inst_lang['inst_no'].'</span>';
	$our_zlib_status		= ( extension_loaded('zlib') ) ? $tpl->StatusIcon('ok') : $tpl->StatusIcon('error');
	$their_zlib				= $inst_lang['inst_no'];

	// Safe Mode
	$our_safemode			= (ini_get('safe_mode') == '1') ? '<span class="negative">'.$inst_lang['inst_yes'].'</span>' : '<span class="positive">'.$inst_lang['inst_no'].'</span>';
	$our_safemode_status	= ( ini_get('safe_mode') == '1' ) ? $tpl->StatusIcon('error') : $tpl->StatusIcon('ok');
	$their_safemode			= $inst_lang['inst_no'];
	if(ini_get('safe_mode') == '1'){
		$tpl->message_append($tpl->StatusIcon('error').'<b>'.$inst_lang['attention'].':</b> '.$inst_lang['safemode_on']);
	}

	if(function_exists('curl_version')){
		$our_curl =	curl_version();
		if (is_array($our_curl)){
			$our_curl			=  '<span class="positive">'.$inst_lang['inst_yes'].' ('.$our_curl['version'].')</span>' ;
			$our_curl_status	= $tpl->StatusIcon('ok');
		}else{
			$our_curl			=  '<span class="positive">'.$inst_lang['inst_yes'].' ('.$our_curl.')</span>' ;
			$our_curl_status	= $tpl->StatusIcon('ok');
		}
	}else{
		$our_curl='<span class="negative">'.$inst_lang['inst_no'].'</span>';
		$tpl->message_append($tpl->StatusIcon('error').'<b>'.$inst_lang['lerror'].':</b> '.$inst_lang['curl_notavailable']);
		$our_curl_status = $tpl->StatusIcon('error');
	}
	$their_curl  = $inst_lang['inst_yes'];

	if(function_exists('fopen')){
		$our_fopen			= '<span class="positive">'.$inst_lang['inst_yes'].'</span>' ;
		$our_fopen_status	= $tpl->StatusIcon('ok');
	}else {
		$our_fopen = '<span class="negative">'.$inst_lang['inst_no'].'</span>' ;
		$tpl->message_append($tpl->StatusIcon('error').'<b>'.$inst_lang['lerror'].':</b> '.$inst_lang['fopen_notavailable']);
		$our_curl_status = $tpl->StatusIcon('error');
	}
	$their_fopen  = $inst_lang['inst_yes'];
		
	//SOAP
	if(class_exists("SoapClient")){
		$our_soap			= '<span class="positive">'.$inst_lang['inst_yes'].'</span>' ;
		$our_soap_status	= $tpl->StatusIcon('ok');
	}else{
		$our_soap = '<span class="negative">'.$inst_lang['inst_no'].'</span>' ;
		$tpl->message_append($tpl->StatusIcon('error').'<b>'.$inst_lang['lerror'].':</b> '.$inst_lang['soap_notavailable']);
		 $our_soap_status = $tpl->StatusIcon('error');
	}
	$their_soap  = $inst_lang['inst_yes'];

	//Prevent Installation
	if ( (phpversion() < REQUIRED_PHP_VERSION) || (!extension_loaded('mysql')) || ini_get('safe_mode') == '1'){
		$tpl->error_append($inst_lang['minimal_requ_notfilled']);
	}else{
		$tpl->message_append($tpl->StatusIcon('ok').$inst_lang['minimal_requ_filled']);
	}
		
	//Other php-functions that are needed
	$other_php_functions = true;
	if (!function_exists('spl_autoload_register')){			
		$tpl->error_append($inst_lang['minimal_requ_notfilled'].'<br /><br />'.$tpl->StatusIcon('error').$inst_lang['spl_autoload_register_notavailable']);
		$other_php_functions = false;
	}

	//
	// Output the page
	//
	$tpl->assign_vars(array(
		'OUR_EQDKPP_VERSION'		=> EQDKPPLUS_VERSION,
		'THEIR_EQDKPP_VERSION'		=> (compareVersion($their_eqdkpp_version, EQDKPPLUS_VERSION) == 1) ? '<span style="color:red;font-weight:bold;">'.$their_eqdkpp_version.'</span>' : '<span style="color:green;font-weight:bold;">'.EQDKPPLUS_VERSION.'</span>',
		'OUR_PHP_VERSION'			=> $our_php_version,
		'THEIR_PHP_VERSION'			=> $their_php_version,
		'OUR_MYSQL'					=> $our_mysql,
		'THEIR_MYSQL'				=> $their_mysql,
		'OUR_ZLIB'					=> $our_zlib,
		'THEIR_ZLIB'				=> $their_zlib,
		'OUR_SAFEMODE'				=> $our_safemode,
		'THEIR_SAFEMODE'			=> $their_safemode,
		'OUR_CURL'					=> $our_curl,
		'THEIR_CURL'				=> $their_curl,
		'OUR_FOPEN'					=> $our_fopen,
		'THEIR_FOPEN'				=> $their_fopen,
		'OUR_SOAP'					=> $our_soap,
		'THEIR_SOAP'				=> $their_soap,
		'OUR_OTHER_FUNCTIONS'		=> ($other_php_functions) ? '<span class="positive">'.$inst_lang['inst_yes'].'</span>' : '<span class="negative">'.$inst_lang['inst_no'].'</span>',
		'THEIR_OTHER_FUNCTIONS'		=> $inst_lang['inst_yes'],
				
		'STATUS_PHP'				=> $our_php_status,
		'STATUS_MYSQL'				=> $our_mysql_status,
		'STATUS_ZLIB'				=> $our_zlib_status,
		'STATUS_SAFEMODE'			=> $our_safemode_status,
		'STATUS_CURL'				=> $our_curl_status,
		'STATUS_FOPEN'				=> $our_fopen_status,
		'STATUS_SOAP'				=> $our_soap_status,
		'STATURS_OTHER_FUNCTIONS'	=> $tpl->StatusIcon((!$other_php_functions) ? 'error': 'ok'),
				
		'L_WELCOME'					=> $inst_lang['welcome'],
		'L_LANGUAGE_SELECT'			=> $inst_lang['language_selector'],
		'L_INSTALL_LANGUAGE'		=> $inst_lang['install_language'],
		'L_SERVER_VERSION'			=> $inst_lang['inst_latest'],
		'L_EQDKPPVERSION'			=> $inst_lang['inst_eqdkpv'],
		'L_EQDKPPNAME'				=> $inst_lang['eqdkp_name'],
		'L_INST_PHP'				=> $inst_lang['inst_php'],
		'L_VIEWPHPINFO'				=> $inst_lang['inst_view'],
		'L_VERSION'					=> $inst_lang['inst_version'],
		'L_REQUIRED'				=> $inst_lang['inst_required'],
		'L_AVAILABLE'				=> $inst_lang['inst_available'],
		'L_ENABLED'					=> $inst_lang['inst_enabled'],
		'L_USING'					=> $inst_lang['inst_using'],
		'L_MYSQLMODULE'				=> $inst_lang['inst_mysqlmodule'],
		'L_CURLMODULE'				=> $inst_lang['inst_curlmodule'],
		'L_ZLIBMODULE'				=> $inst_lang['inst_zlibmodule'],
		'L_SAFEMODE'				=> $inst_lang['inst_safemode'],
		'L_OTHER_FUNCTIONS'			=> $inst_lang['inst_other_functions'],
		'L_FOPEN'					=> $inst_lang['inst_fopen'],
		'L_SOAP'					=> $inst_lang['inst_soap'],
		'L_INST_BUTTON1'			=> $inst_lang['inst_button1'],
		'L_INST_BUTTON_RETRY'		=> $inst_lang['inst_button_retry'],
	));

	$tpl->page_header();
	$tpl->page_tail();
}


function process_step2(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $inst_lang;

	$tpl = new Template_Wrap('install_step2.html');
	$tpl->assign_vars(array(
		'FTP_HOST'				=> '127.0.0.1',
		'FTP_PORT'				=> '21',
		'L_FTP_CONF'			=> $inst_lang['inst_ftp_conf'],
		'L_FTP_HOST'			=> $inst_lang['inst_ftphost'],
		'L_FTP_PORT'			=> $inst_lang['inst_ftpport'],
		'L_FTP_USER'			=> $inst_lang['inst_ftpuser'],
		'L_FTP_PW'				=> $inst_lang['inst_ftppass'],
		'L_FTP_ROOT'			=> $inst_lang['inst_ftppath'],
		'L_USE_FTP'				=> $inst_lang['inst_useftp'],
		'L_BUTTON_2'			=> $inst_lang['inst_button2'],
		'L_BUTTON_3'			=> $inst_lang['inst_button_jump'],
		'L_MANDOTARY'			=> ((ini_get('safe_mode') == '1') ? $inst_lang['inst_ftp_mandotary'] : $inst_lang['inst_ftp_not_mandotary']),
	));

	$tpl->page_header();
	$tpl->page_tail();
}

function process_step3(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $inst_lang, $pcache;

	$tpl = new Template_Wrap('install_step3.html');
		
	$ftphost		= post_or_db('ftphost');
	$ftpport		= post_or_db('ftpport');
	$ftpuser		= post_or_db('ftpuser');
	$ftppass		= post_or_db('ftppass');
	$ftproot		= post_or_db('ftproot');
	$useftp			= post_or_db('useftp');
	
	if(post_or_db('jump') == ""){	
		$connect = ftp_connect($ftphost,$ftpport, 5);
		$login = ftp_login($connect, $ftpuser, $ftppass);
			
		if ($connect){
			$tpl->message_append($inst_lang['inst_ftp_connection_success']);
		} else {
			if (ini_get('safe_mode') == '1'){
				$tpl->error_append($inst_lang['inst_ftp_connection_error']);
			} else {
				$tpl->message_append($inst_lang['inst_ftp_connection_error']);
			}		
		}
	
		if ($login){
			$tpl->message_append($inst_lang['inst_ftp_login_success']);
		}else{
			if (ini_get('safe_mode') == '1'){
				$tpl->error_append($inst_lang['inst_ftp_login_error']);
			} else {
				$tpl->message_append($inst_lang['inst_ftp_login_error']);
			}	
		}
		$useftp = ($login && $connect) ? $useftp : 0;
	} else {
		$useftp = 0;
	}

	// Check to make sure config.php exists and is readable / writeable
	$config_file = $eqdkp_root_path . 'config.php';
	if ( !file_exists($config_file) ){
		if ( !$pcache->CheckCreateFile($config_file, true) ){
			$tpl->error_append($inst_lang['conf_not_write']);
		}else{
			$tpl->message_append($tpl->StatusIcon('ok').$inst_lang['conf_written']);
		}
	}else{
		if ( ((!is_writeable($config_file)) || (!is_readable($config_file))) && !$useftp ){
			if ( !@chmod($config_file, 0666) ){
				$tpl->error_append($inst_lang['conf_chmod']);
			}else{
				$tpl->message_append($tpl->StatusIcon('ok').$inst_lang['conf_writable']);
			}
		}
		// config file exists and is writeable, we're good to go
	}
	clearstatcache();

	// Check to make sure the required chmod 777 folders are writeable
	$datafolder = $pcache->CheckCreateFolder($eqdkp_root_path.'data');
		
	if( !$datafolder){
		$tpl->error_append($tpl->StatusIcon('error').sprintf($inst_lang['templcache_notcreated'],'data'));
	}else{
		$tpl->message_append($tpl->StatusIcon('ok').sprintf($inst_lang['templcache_created'], 'data'));
		$exists = true;
	}
		
	if (!$pcache->CheckWrite()){
		$tpl->error_append($tpl->StatusIcon('error').sprintf($inst_lang['templcache_notwritable'], 'data'));
	} else {
		$tpl->message_append($tpl->StatusIcon('ok').sprintf($inst_lang['templatecache_ok'], 'data'));
		$write = true;
	}
		
	$exists = ($exists) ? '<strong style="color:green">'.$inst_lang['inst_found'].'</strong>'    : '<strong style="color:red">'.$inst_lang['inst_notfound'].'</strong>';
	$write  = ($write)  ? '<strong style="color:green">'.$inst_lang['inst_writable'].'</strong>' : (($exists) ? '<strong style="color:red">'.$inst_lang['inst_unwritable'].'</strong>' : '');
	clearstatcache();
		
	$tpl->assign_vars(array(
		'FTP_DATA'		=> base64_encode(serialize(array($ftphost, $ftpport, $ftpuser, $ftppass, (($useftp) ? 1 : 0), $ftproot))),
		'L_BUTTON_3'	=> $inst_lang['inst_button3'],	
		'S_NO_FTP'		=> post_or_db('jump') != "",
		'L_NO_FTP'		=> $inst_lang['inst_no_ftp'],
				
	));

	$tpl->page_header();
	$tpl->page_tail();
}


function process_step4(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $inst_lang;

	$tpl = new Template_Wrap('install_step4.html');

	// Build the database drop-down
	foreach ( $DBALS as $db_type => $db_options ){
		$tpl->assign_block_vars('dbal_row', array(
			'VALUE'		=> $db_type,
			'OPTION'	=> $db_options['label'])
		);
	}
		$ftpdata	= post_or_db('ftpdata');

	// Output the page
	$tpl->assign_vars(array(
		'DB_HOST'				=> 'localhost',
		'TABLE_PREFIX'			=> $DEFAULTS['table_prefix'],
		'FTP_DATA'				=> $ftpdata,
		
		'L_DATABASE_CONF'		=> $inst_lang['inst_database_conf'],
		'L_DATABASE_TYPE'		=> $inst_lang['inst_dbtype'],
		'L_DATABASE_HOST'		=> $inst_lang['inst_dbhost'],
		'L_DATABASE_NAME'		=> $inst_lang['inst_dbname'],
		'L_DATABASE_USER'		=> $inst_lang['inst_dbuser'],
		'L_DATABASE_PW'			=> $inst_lang['inst_dbpass'],
		'L_DATABASE_PREFIX'		=> $inst_lang['inst_table_prefix'],
		'L_BUTTON_2'			=> $inst_lang['inst_button4'],
	));

	$tpl->page_header();
	$tpl->page_tail();
}

function process_step5(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $inst_lang;

	$tpl = new Template_Wrap('install_step5.html');
	$dbtype			= post_or_db('dbtype');
	$dbhost			= post_or_db('dbhost');
	$dbname			= post_or_db('dbname');
	$dbuser			= post_or_db('dbuser');
	$dbpass			= post_or_db('dbpass');
	$table_prefix	= post_or_db('table_prefix', $DEFAULTS);
	$ftpdata		= post_or_db('ftpdata');
	
	// ********************************
	// Database Check
	// ********************************
	
	// check firs char of table_prefix, the check the res
	$first_char = substr($table_prefix,0,1);
	
	if (!preg_match('/^[a-zA-Z]+$/', $first_char)){
		$db_message	= $inst_lang['inst_error_prefix_inval']."<br /><br /><a href='index.php'>".$inst_lang['inst_redoit']."</a>";
		$tpl->error_append($db_message);
	}
	
	$error = array();
	$available_dbms = get_available_dbms(false, true);
	$dbms = ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
	require($eqdkp_root_path . 'gplcore/db/' . $available_dbms[$dbms]['DRIVER'] . '.php');
	$sql_db				= 'dbal_' . $available_dbms[$dbms]['DRIVER'];
	$db					= new $sql_db();
	$connect_test		= $db->check_connection(true, $error, $available_dbms[$dbms], $table_prefix, $dbhost, $dbuser, $dbpass, $dbname);
	$myError			= true;

	if(!$table_prefix){
		$db_message		= $inst_lang['inst_error_prefix']."<br /><br /><a href='index.php'>".$inst_lang['inst_redoit']."</a>";
		$db_header		= $inst_lang['db_warning'];
		$tpl->error_append($db_message);
	}elseif(count($error) > 0){
		$db_message = '';
		foreach($error as $amsg){
			$db_message	.= $amsg.'<br/>';
		}
		$db_message		.= "<a href='index.php'>".$inst_lang['inst_redoit']."</a>";
		$db_header		 = $inst_lang['db_warning'];
		$tpl->error_append($db_message);
	}else{
		$myError		= false;
		$db_message		= $inst_lang['insinfo_dbready'];
		$db_header		= $inst_lang['db_information'];
		$server_version	= mysql_get_server_info();
		$client_version	= mysql_get_client_info();
	}

	// Output the page
	if(!$myError){
		$tpl->message_append("<img src='../images/ok.png' alt='error' style='float:left; padding: 10px;'/>".$db_message);
		if ( (isset($server_version) && isset($client_version)) ) {
			$sql_infomssg = sprintf($inst_lang['inst_mysqlinfo'], $server_version, $client_version);
		}else{
			$sql_infomssg = sprintf($inst_lang['inst_failedversioninfo'], $dbname, $dbuser, $dbhost);
		}
	}

	$tpl->assign_vars(array(
		'DB_HOST'		=> 'localhost',
		'TABLE_PREFIX'	=> $DEFAULTS['table_prefix'],
		'DB_PASS'		=> $dbpass,
		'DB_USER'		=> $dbuser,
		'DB_HOST'		=> $dbhost,
		'FTP_DATA'		=> $ftpdata,
		'TABLE_PREFIX'	=> $table_prefix,
		'DB_NAME'		=> $dbname,
		'DB_TYPE'		=> $dbtype,
		'SQL_TITLE'		=> $inst_lang['inst_sqlheaderbox'],
		'SQL_TEXT'		=> (!$myError) ? $sql_infomssg : '',
		'SHOW_SQLINFO'	=> ($myError) ? false : true,
		'L_BUTTON3'		=> $inst_lang['inst_button5'],
	));

	$tpl->page_header();
	$tpl->page_tail();
}

function process_step6(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $core, $inst_lang, $html, $jquery, $tpl;

	$tpl			= new Template_Wrap('install_step6.html');
	$jquery			= new jquery();
	
	$dbtype			= post_or_db('dbtype');
	$dbhost			= post_or_db('dbhost');
	$dbname			= post_or_db('dbname');
	$dbuser			= post_or_db('dbuser');
	$dbpass			= post_or_db('dbpass');
	$table_prefix	= post_or_db('table_prefix', $DEFAULTS);
	$ftpdata		= post_or_db('ftpdata');
	
	$langfiles = $games = array();
	// this is for "stability reasons", remove it and you'll have a fatal!
	$core->config['default_game'] = $DEFAULTS['default_game'];		
	require_once($eqdkp_root_path . 'core/game.class.php');
	$game	  	= new Game();
	foreach($game->get_games() as $sgame){
		$games[$sgame] = $game->game_name($sgame);
		$langfiles[$sgame] = sdir($eqdkp_root_path . 'games/'.$sgame.'/language/', '*.php', '.php');
	}

	// check for the ajax...
	if($_GET['ajax'] == 'games'){
		echo $jquery->dd_create_ajax($langfiles[$_POST['requestid']], array('format'=>'ucfirst'));die();
	}

	// Build the default language  & Locales dropdowns
	if($dir = @opendir($eqdkp_root_path.'language/')){
		while ( $file = @readdir($dir) ){
			if ((!is_file($eqdkp_root_path.'language/'.$file)) && (!is_link($eqdkp_root_path.'language/'.$file)) && valid_folder($file)){
				include($eqdkp_root_path.'language/'.$file.'/lang_main.php');
				$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
				$language_array[$file]					= $lang_name_tp;
				$locale_array[$lang['ISO_LANG_SHORT']]	= $lang_name_tp;
			}
		}
		$tpl->assign_var('LANG_DD', $html->DropDown('default_lang', $language_array, $inst_lang['default_lang']));
		$tpl->assign_var('LOCA_DD', $html->DropDown('default_locale', $locale_array, $inst_lang['default_locale']));
	}
	
	// Other vars
	$server_path = str_replace('install/index.php', '', $_SERVER['PHP_SELF']);
	$tpl->message_append($tpl->StatusIcon('ok').$inst_lang['inst_checkifdbexists']);

	$game_array = $jquery->dd_ajax_request('default_game', 'game_language', $games, array('--------'), $DEFAULTS['default_game'], $inst_lang['game_language'], 'index.php?install_step=6&ajax=games');
	unset($games);unset($locale_array);unset($language_array);
		
	// Output the page
	$tpl->assign_vars(array(
		'SERVER_PATH'	=> $server_path,
		'DB_PASS'		=> $dbpass,
		'DB_USER'		=> $dbuser,
		'DB_HOST'		=> $dbhost,
		'TABLE_PREFIX'	=> $table_prefix,
		'DB_NAME'		=> $dbname,
		'DB_TYPE'		=> $dbtype,
		'FTP_DATA'		=> $ftpdata,
		'GAMELANG_DD'	=> $game_array[1],
		'GAME_DD'		=> $game_array[0],

		'L_LANG_CONF'	=> $inst_lang['inst_language_config'],
		'L_DEF_LANG'	=> $inst_lang['inst_default_lang'],
		'L_DEF_LOCA'	=> $inst_lang['inst_default_locale'],
		'L_GAME_CONF'	=> $inst_lang['inst_game_config'],
		'L_DEF_GAME'	=> $inst_lang['inst_default_game'],
		'L_SERVER_CONF'	=> $inst_lang['inst_server_config'],
		'L_SERVERPATH'	=> $inst_lang['inst_server_path'],
		'L_BUTTON4'		=> $inst_lang['inst_button6'],
	));

	$tpl->page_header();
	$tpl->page_tail();
}

function process_step7(){
	global $eqdkp_root_path, $DEFAULTS, $DBALS, $inst_lang, $db, $table_prefix, $pcache, $settings, $core;
	
	// init the core
	$tpl		= new Template_Wrap('install_step7.html');

	// Get our posted data
	$dbtype						= post_or_db('dbtype');
	$dbhost						= post_or_db('dbhost');
	$dbname						= post_or_db('dbname');
	$dbuser						= post_or_db('dbuser');
	$dbpass						= post_or_db('dbpass');
	$ftpdata					= post_or_db('ftpdata');
	$default_locale				= post_or_db('default_locale');
	$table_prefix				= post_or_db('table_prefix', $DEFAULTS);
	$server_path				= post_or_db('server_path');
	$default_game				= post_or_db('default_game');
	$game_language				= post_or_db('game_language');
	$default_lang				= post_or_db('default_lang', $DEFAULTS);

	// Database population
	// If we get here and the extension isn't loaded it should be safe to just go ahead and load it
	$available_dbms = get_available_dbms($dbtype);

	$dbms	= ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
	require($eqdkp_root_path . 'gplcore/db/' . $available_dbms[$dbms]['DRIVER'] . '.php');
	$sql_db	= 'dbal_' . $available_dbms[$dbms]['DRIVER'];
	$db		= new $sql_db();
	$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass, false);

	// init the core
	$settings		= new mmocms_config();
	$core			= new mmocms_core($eqdkp_root_path);

	// Check to make sure a connection was made
	if ( !is_resource($db->link_id) ){
		$tpl->message_die(sprintf($inst_lang['inst_failedconhost'], $dbname, $dbuser, $dbhost));
	}

	$db_structure_file			= $eqdkp_root_path . 'install/schemas/' . $available_dbms[$dbms]['SCHEMA'] . '_structure.sql';
	$db_data_file				= $eqdkp_root_path . 'install/schemas/' . $available_dbms[$dbms]['SCHEMA'] . '_data.sql';

	$remove_remarks_function	= $available_dbms[$dbms]['COMMENTS'];
	$delimiter					= $available_dbms[$dbms]['DELIM'];

	// Parse structure file and create database tables
	$sql	= @fread(@fopen($db_structure_file, 'r'), @filesize($db_structure_file));
	$sql	= preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql);

	$sql	= $remove_remarks_function($sql);
	$sql	= parse_sql($sql, $available_dbms[$dbms]['DELIM']);

	// TODO: No way to roll back changes if any particular query fails.
	$sql_count = count($sql);
	$i = 0;

	while ( $i < $sql_count ){
		if (isset($sql[$i]) && $sql[$i] != ""){
			if ( !($db->query($sql[$i]) )){
				$dberrorthing = $db->error();
				echo "Fehler";
				$tpl->message_die(sprintf($inst_lang['inst_sql_error'], $sql[$i], $dberrorthing['message'], $dberrorthing['code']));
			}
		}
		$i++;
	}
	unset($sql);

	// Parse the data file and populate the database tables
	$sql	= @fread(@fopen($db_data_file, 'r'), @filesize($db_data_file));
	$sql	= preg_replace('#eqdkp\_(\S+?)([\s\.,]|$)#', $table_prefix . '\\1\\2', $sql);

	$sql	= $remove_remarks_function($sql);
	$sql	= parse_sql($sql, $available_dbms[$dbms]['DELIM']);

	$sql_count	= count($sql);
	$i			= 0;

	while ( $i < $sql_count ){
		if (isset($sql[$i]) && $sql[$i] != ""){
			if ( !($db->query($sql[$i]) )){
				$dberrorthing = $db->error();
				$tpl->message_die(sprintf($inst_lang['inst_sql_error'], $sql[$i],$dberrorthing['message'], $dberrorthing['code']));
			}
		}
		$i++;
	}
	unset($sql);

	// Update some config settings
	$settings->install_set(get_inst_config($server_path, $default_lang, $default_locale, $default_game, $game_language));

	// Set default-auths for Admin
	$auth_default_query = $db->query("SELECT auth_id FROM __auth_options");
	while ($row = $db->fetch_record($auth_default_query)){
		$db->query("INSERT INTO __auth_users (auth_id, user_id, auth_setting) VALUES (".$db->escape($row['auth_id']).",1 ,'Y');");
	}
	$db->query("INSERT INTO __groups_users (group_id, user_id) VALUES (2,1);");
		
	// Set default-groups
	$db->query("INSERT INTO __groups_user (`groups_user_id`, `groups_user_name`, `groups_user_desc`, `groups_user_deletable`, `groups_user_default`, `groups_user_hide`) VALUES
			(1,'".$inst_lang['grp_guest']."','".$inst_lang['grp_guest_desc']."','0','0','1'),
			(2,'".$inst_lang['grp_super_admins']."','".$inst_lang['grp_super_admins_desc']."','0','0','0'),
			(3,'".$inst_lang['grp_admins']."','".$inst_lang['grp_admins_desc']."','0','0','0'),
			(4,'".$inst_lang['grp_member']."','".$inst_lang['grp_member_desc']."','0','1','0'),
			(5,'".$inst_lang['grp_officers']."','".$inst_lang['grp_officers_desc']."','1','0','0'),
			(6,'".$inst_lang['grp_writers']."','".$inst_lang['grp_writers_desc']."','1','0','0')
			");
		
		InsertGroupPermissions(1, array('u_event_list', 'u_event_view', 'u_item_list', 'u_item_view', 'u_raid_list', 'u_raid_view', 'u_member_view', 'u_member_list', 'u_infopages_view', 'u_userlist'));
		InsertGroupPermissions(3, false, array('a_backup', 'a_logs_del', 'a_maintenance', 'a_reset'));
		InsertGroupPermissions(4, array('u_event_list', 'u_event_view', 'u_item_list', 'u_item_view', 'u_raid_list', 'u_raid_view', 'u_member_view', 'u_member_list', 'u_member_man', 'u_member_add', 'u_member_conn', 'u_member_del', 'u_userlist', 'u_infopages_view'));
		InsertGroupPermissions(5, array('a_event_add', 'a_event_upd', 'a_event_del', 'a_item_add', 'a_item_upd', 'a_item_del', 'a_raid_add', 'a_raid_upd', 'a_raid_del', 'a_members_man'));
		InsertGroupPermissions(6, array('a_news_add', 'a_news_upd', 'a_news_del'));
		
		//Insert Welcome-News
		$db->query("INSERT into __news :params", array(
			'news_headline'			=> $inst_lang['welcome_news_titel'],
			'news_message'			=> $inst_lang['welcome_news'],
			'news_date'				=> time(),
			'user_id'				=> 1,
			'extended_message'		=> '',
			'nocomments'			=> 0,
			'news_permissions'		=> 0,
		));

	// Set the Game Details
	include($eqdkp_root_path.'core/game.class.php');
	$game = new Game(true);
	$game->ChangeGame($default_game, $game_language);

	// FTP-Data
	$ftp_data = unserialize(base64_decode($ftpdata));

	//Setup PDC
	$pdc_conf = array (
								'mode' => 'file',
    						'file' =>
    							array (
      							'dttl' => 86400,
      							'prefix' => $table_prefix,
    							)
							);
	$core->config_set('pdc', $pdc_conf);
	
	// Write the config file
	$config_file	 = "<?php\n\n";
	$config_file	.= "\$dbtype		= '{$dbtype}'; \n";
	$config_file	.= "\$dbhost		= '{$dbhost}'; \n";
	$config_file	.= "\$dbname		= '" . $dbname			. "'; \n";
	$config_file	.= "\$dbuser		= '" . $dbuser			. "'; \n";
	$config_file	.= "\$dbpass		= '" . $dbpass			. "'; \n";
	$config_file	.= "\$table_prefix	= '" . $table_prefix	. "';\n\n";
	$config_file	.= "\$ftphost		= '" . $ftp_data[0]		. "'; \n";
	$config_file	.= "\$ftpport		= '" . $ftp_data[1]		. "'; \n";
	$config_file	.= "\$ftpuser		= '" . $ftp_data[2]		. "'; \n";
	$config_file	.= "\$ftppass		= '" . $ftp_data[3]		. "'; \n";
	$config_file	.= "\$ftproot		= '" . $ftp_data[5]		. "'; \n";
	$config_file	.= "\$use_ftp		= " . $ftp_data[4]		. "; \n";
	$config_file	.= "?".">";

	// Set our permissions to execute-only
	@umask(0111);

	if ( !$fp = @fopen('../config.php', 'w') ){
		$error_message  = $inst_lang['inst_writerr_confile'].'<br /><textarea rows="10" cols="50">' . $config_file . '</textarea>';
		$tpl->error_append($error_message);
	}else{
		$pcache->putContent($config_file, '../config.php');
		$tpl->message_append($tpl->StatusIcon('ok').$inst_lang['inst_confwritten']);
	}

	// Output the page
	$tpl->assign_vars(array(
		'L_ADMINACC'	=> $inst_lang['inst_administrator_config'],
		'L_ADM_USERN'	=> $inst_lang['inst_username'],
		'L_ADM_PW'		=> $inst_lang['inst_user_password'],
		'L_ADM_PW_CONF'	=> $inst_lang['inst_user_pw_confirm'],
		'L_ADM_EMAIL'	=> $inst_lang['inst_user_email'],
		'L_BUTTON5'		=> $inst_lang['inst_button7'],
	));
	$tpl->page_header();
	$tpl->page_tail();
}

function process_step8(){
	global $eqdkp_root_path, $DEFAULTS, $inst_lang, $pcache, $core, $settings;
	
	// Remove the temporary folder
	$pcache->Delete($eqdkp_root_path.'data/d41d8cd98f00b204e9800998ecf8427e');
	
	$tpl	= new Template_Wrap('install_step8.html');

	// Get our posted data
	$username		= post_or_db('username');
	$user_password1	= post_or_db('user_password1');
	$user_password2	= post_or_db('user_password2');
	$user_email		= post_or_db('user_email');
		
	//check passwords
	$pw_check = true;
	if($user_password1 != $user_password2) {
		$pw_check = false;
	}
		
	$create_admin = true;
	if ($username == "" || $user_email == "" || $user_password1 == "" ) {
		$tpl->error_append($tpl->StatusIcon('error').$inst_lang['inst_admin_empty']);
		$create_admin = false;
	}	
		
	if ( !$pw_check ){
		$tpl->error_append($tpl->StatusIcon('error').$inst_lang['inst_passwordnotmatch']);
		$create_admin = false;
	}

	// Update admin account
	if ($create_admin) {
		include($eqdkp_root_path . 'config.php');

		$dbms = ( !isset($dbms) && isset($dbtype) ) ? $dbtype : $dbms;
		require($eqdkp_root_path . 'gplcore/db/' . $dbms . '.php');
		$db	= new $sql_db();
		$db->sql_connect($dbhost, $dbname, $dbuser, $dbpass, false);
		$settings	= new mmocms_config(false, $db);
		$core		= new mmocms_core($eqdkp_root_path);

		$salt = generate_salt();
		$password = ( $pw_check ) ? encrypt_password($user_password1, $salt) : encrypt_password('admin', $salt);
		$db->query("INSERT INTO __users :params", array(
					'user_id'		=> 1,
					'username'		=> $username,
					'user_password'	=> $password.':'.$salt,
					'user_lang'		=> $core->config['default_lang'],
					'user_email'	=> $user_email,
					'user_active'	=> '1',
					'rules'			=> 1,
					'user_style'	=> $DEFAULTS['default_style'],
			));
		$core->config_set(array('admin_email'=>$user_email));
			
		// Rewrite the config file to its final form
		$config_file	 = file($eqdkp_root_path . 'config.php');
		$config_file	 = implode("\n", $config_file);
		$config_file	 = preg_replace('#\?'.'>$#', '', $config_file);
		$config_file	.= 'define(\'EQDKP_INSTALLED\', true);' . "\n";
		$config_file	.= '?'.'>';
	
		// Set our permissions to execute-only
		@umask(0111);
	
		if ( !$fp = @fopen('../config.php', 'w') ){
			$error_message  = $inst_lang['inst_writerr_confile'].'<br /><pre>' . htmlspecialchars($config_file) . '</pre>';
			$tpl->error_append($error_message);
		}else{
			$pcache->putContent($config_file, '../config.php');
		}
		$tpl->message_append($tpl->StatusIcon('ok').$inst_lang['inst_admin_created']);
	}

	$tpl->assign_vars(array(
		'L_LOGIN'		=> $inst_lang['login'],
		'L_USERNAME'	=> $inst_lang['username'],
		'L_PASSWORD'	=> $inst_lang['password'],
		'L_COOKIE_SET'	=> $inst_lang['remember_password'],
		'L_LOGBUTTON'	=> $inst_lang['login_button'],
		'L_BUTTON5'		=> $inst_lang['inst_button8'],
	));

	$tpl->page_header();
	$tpl->page_tail();
}

?>