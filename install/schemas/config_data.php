<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date: 2010-07-01 13:31:49 +0200 (Thu, 01 Jul 2010) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2010 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 8269 $
 * 
 * $Id: config.class.php 8269 2010-07-01 11:31:49Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
    header('HTTP/1.0 404 Not Found');exit;
}

define('EQDKPPLUS_VERSION',		'0.7.0.18');

function get_inst_config($server_path, $default_lang, $default_locale, $default_game, $game_language){
	$default_config = array(
		'server_path'					=> ($server_path) ? $server_path : '/eqdkp/',
		'default_lang'					=> ($default_lang) ? $default_lang : 'english',
		'default_locale'				=> ($default_locale) ? $default_locale : 'en_US',
		'cookie_name'					=> "eqdkp_".GenerateCookieHash(),
		'default_game'					=> ($default_game) ? $default_game : 'wow',
		'game_language'					=> ($game_language) ? $game_language : 'de',
		'default_style'					=> '1',
		'default_alimit'				=> '100',
		'default_elimit'				=> '100',
		'default_ilimit'				=> '100',
		'default_nlimit'				=> '10',
		'default_rlimit'				=> '100',
		'guildtag'						=> 'My Guild',
		'dkp_name'						=> 'DKP',
		'hide_inactive'					=> '0',
		'inactive_period'				=> '99',
		'active_point_adj'				=> '0.00',
		'inactive_point_adj'			=> '0.00',
		'start_page'					=> 'viewnews.php',
		'cookie_path'					=> '/',
		'session_length'				=> '3600',
		'session_cleanup'				=> '0',
		'session_last_cleanup'			=> '0',
		'enable_gzip'					=> '0',
		'account_activation'			=> '1',
		'eqdkp_start'					=> time(),
		'plus_version'					=> EQDKPPLUS_VERSION,
		'default_style_overwrite'		=> '0',
		'enable_newscategories'			=> '0',
		'upload_allowed_extensions'		=> 'zip,rar,jpg,bmp,gif,png',
		'pk_updatecheck'				=> '1',
		'pk_enable_captcha'				=> '0',
		'pk_is_icon_loc'				=> 'http://www.buffed.de/images/wow/32/',
		'pk_is_icon_ext'				=> '.png',
		'pk_attendance90'				=> '1',
		'pk_lastraid'					=> '1',
		'pk_class_color'				=> '1',
		'pk_newsloot_limit'				=> 'all',
		'pk_lightbox_enabled'			=> '1',
		'pk_debug'						=> '0',
		'pk_maintenance_mode'			=> '1',
		'pk_enable_comments'			=> '1',
	);
	return $default_config;
}
?>