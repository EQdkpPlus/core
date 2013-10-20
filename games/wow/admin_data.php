<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$settingsdata_admin = array(
	'game' => array(
		'wowsettings' => array(
			'uc_faction'	=> array(
				'name'		=> 'uc_faction',
				'fieldtype'	=> 'dropdown',
				'size'		=> '1',
				'options'	=> registry::register('game')->get('factions'),
				'default'	=> 'alliance'
			),
			'uc_server_loc'	=> array(
				'name'		=> 'uc_server_loc',
				'fieldtype' => 'dropdown',
				'size'		=> '1',
				'options'	=> array('eu' => 'EU', 'us' => 'US', 'tw' => 'TW', 'kr' => 'KR', 'cn' => 'CN'),
			),
			'uc_data_lang'	=> array(
				'name'		=> 'uc_data_lang',
				'fieldtype' => 'dropdown',
				'size'		=> '1',
				'options'	=> array(
								'en_US' => 'English',
								'es_MX' => 'Mexican',
								'pt_BR' => 'Brasil',
								'en_GB' => 'English (GB)',
								'es_ES' => 'Spanish',
								'fr_FR' => 'French',
								'ru_RU' => 'Russian',
								'de_DE'	=> 'German',
								'pt_PT'	=> 'Portuguese',
								'ko_KR'	=> 'Korean',
								'zh_TW'	=> 'Taiwanese',
								'zh_CN'	=> 'Chinese'
							),
			),
			'uc_servername'	=> array(
				'name'		=> 'uc_servername',
				'fieldtype'	=> 'autocomplete',
				'size'		=> '21',
				'edecode'	=> true,
				'options'	=> registry::register('game')->get('realmlist'),
			)
		)
	)
);

?>