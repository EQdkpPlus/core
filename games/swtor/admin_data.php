<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2011-08-30 00:19:06 +0200 (Di, 30 Aug 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: hoofy $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 11104 $
 * 
 * $Id: admin_data.php 11104 2011-08-29 22:19:06Z hoofy $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$settingsdata_admin = array(
	'game' => array(
		'swtorsettings' => array(
			'swtor_faction'	=> array(
				'name'		=> 'swtor_faction',
				'fieldtype'	=> 'dropdown',
				'size'		=> '1',
				'options'	=> registry::register('game')->get('factions'),
				'default'	=> 0
			),
		)
	)
);

?>