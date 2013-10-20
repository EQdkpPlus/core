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
			'uc_faction'	=> array(
				'lang'		=> 'uc_faction',
				'type'		=> 'dropdown',
				'options'	=> registry::register('game')->get('factions'),
				'default'	=> 'alliance'
			),
			'uc_server_loc'	=> array(
				'lang'		=> 'uc_server_loc',
				'type' 		=> 'dropdown',
				'options'	=> array('eu' => 'EU', 'us' => 'US', 'tw' => 'TW', 'kr' => 'KR', 'cn' => 'CN'),
			),
			'uc_data_lang'	=> array(
				'lang'		=> 'uc_data_lang',
				'type' 		=> 'dropdown',
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
			// TODO: check if apostrophe is saved correctly
			'uc_servername'	=> array(
				'lang'			=> 'uc_servername',
				'type'			=> 'text',
				'size'			=> '21',
				'autocomplete'	=> registry::register('game')->get('realmlist'),
			)
);

?>