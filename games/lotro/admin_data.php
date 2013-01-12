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

$realmnames = array(
	// EU Servers
	'Gwaihir',
	'Morthond',
	'Anduin',
	'Maiar',
	'Vanyar',
	'Belegaer',
	'Snowbourn',
	'Evernight',
	'Eldar',
	'Gilrain',
	'Laurelin',
	'Sirannon',
	'Estel',
	
	// US Servers
	'Arkenstone',
	'Brandywine',
	'Crickhollow',
	'Dwarrowdelf',
	'Elendilmir',
	'Firefoot',
	'Gladden',
	'Imladris',
	'Landroval',
	'Meneldor',
	'Nimrodel',
	'Riddermark',
	'Silverlode',
	'Vilya',
	'Windfola',
	
	// other Servers
	'Bullroarer',
);

$realmnames = array_unique($realmnames);

$settingsdata_admin = array(
	'game' => array(
		'lotrosettings' => array(
			'uc_faction'	=> array(
				'name'		=> 'uc_faction',
				'fieldtype'	=> 'dropdown',
				'size'		=> '1',
				'options'	=> $this->game->get('factions'),
				'default'	=> 'alliance'
			),
			'uc_server_loc'  => array(
				'name'		=> 'uc_server_loc',
				'fieldtype' => 'dropdown',
				'size'		=> '1',
				'options'	=> array('eu' => 'EU', 'us' => 'US'),
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