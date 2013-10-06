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
	'uc_faction'	=> array(
		'lang'		=> 'uc_faction',
		'type'		=> 'dropdown',
		'options'	=> $this->game->get('factions'),
		'default'	=> 'alliance'
	),
	'uc_server_loc'  => array(
		'lang'		=> 'uc_server_loc',
		'type' 		=> 'dropdown',
		'options'	=> array('eu' => 'EU', 'us' => 'US'),
	),
	// TODO: check if apostrophe is saved correctly
	'uc_servername'     => array(
		'lang'			=> 'uc_servername',
		'type'			=> 'text',
		'size'			=> '21',
		'autocomplete'	=> $realmnames,
	),
	'uc_lockserver'	=> array(
		'lang'		=> 'uc_lockserver',
		'type'		=> 'radio',
	)
);

?>