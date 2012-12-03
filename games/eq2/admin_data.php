<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date: 2011-01-24 21:29:05 +0100 (Mo, 24 Jan 2011) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 9612 $
 * 
 * $Id: admin_data.php 9612 2011-01-24 20:29:05Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

$realmnames = array(
	'Valor', //EU Deutsch
	'Splitpaw', //EU English
	'Storms',	//EU Francais
	'Sebilis', //China
	'Test', //Public Test Server
	//US English
	'Butcherblock',
	'Nagafen',
	'Guk',
	'Freeport',
	'Everfrost',
	'Unrest',
	'Oasis',
	'Antonia Bayle',
	'Permafrost',
	'Crushbone',
	//Russia
	'Harla Dar',
	'Barren Sky',
	
);


$realmnames = array_unique($realmnames);

$settingsdata_admin = array(
	'game' => array(
		'eq2settings' => array(
			'uc_faction'	=> array(
				'name'		=> 'uc_faction',
				'fieldtype'	=> 'dropdown',
				'size'		=> '1',
				'options'	=> $this->game->get('factions'),
				'default'	=> 'alliance'
			),
			'uc_servername'     => array(
				'name'		=> 'uc_servername',
				'fieldtype'	=> 'autocomplete',
				'size'		=> '21',
				'edecode'	=> true,
				'options'	=> $realmnames,
			),
			'uc_lockserver'	=> array(
				'name'		=> 'uc_lockserver',
				'fieldtype'	=> 'checkbox',
				'size'		=> false,
				'options'	=> false,
			)
		)
	)
);

?>