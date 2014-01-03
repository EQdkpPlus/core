<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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
$german_array = array(
	'classes' => array(
	#  id	name in(republic, imperium)
		0	=> array('Unbekannt', 'Unbekannt'),
		1	=> array('Frontkaempfer', 'Powertech'),
        2	=> array('Kommando', 'Soeldner'),
		3	=> array('Schurke', 'Saboteur'),
		4	=> array('Revolverheld', 'Scharfschuetze'),
		5	=> array('Gelehrter', 'Hexer'),
		6	=> array('Schatten', 'Attentaeter'),
		7	=> array('Waechter', 'Marodeur'),
		8	=> array('Hueter', 'Juggernaut'),

	),
	'races' => array(
		'Unknown',
		'Mensch',
		'Rattataki',
		'Twi\'lek',
		'Chiss',
		'Reinblut Sith',
		'Miraluka',
		'Mirialan',
		'Zabrak',
		'Cyborg',
		'Cathar',
	),
	'factions' => array(
		'Rebublik',
		'Imperium'
	),
	'roles' => array(
		1 => array(2, 3, 5),
		2 => array(1, 6, 8),
		3 => array(2, 4, 5),
		4 => array(1, 3, 6, 7, 8)
	),
	'lang' => array(
		'swtor'						=> 'Star Wars: The Old Republic',

		//Admin Settings
		'pk_tab_fs_swtorsettings'	=> 'SWToR Einstellungen',
		'swtor_faction'				=> 'Fraktion',
		'swtor_faction_help'		=> 'Die Fraktion dient dem Ausblenden der Klassen der jeweils anderen Fraktion.',

		// Roles
		'role1'						=> 'Heiler',
		'role2'						=> 'Tank',
		'role3'						=> 'DD Fernkampf',
		'role4'						=> 'DD Nahkampf',

		// Profile information
		'uc_gender'					=> 'Geschlecht',
		'uc_male'					=> 'Männlich',
		'uc_female'					=> 'Weiblich',
		'uc_guild'					=> 'Gilde',
	),
);

?>