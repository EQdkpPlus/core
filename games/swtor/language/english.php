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
$english_array =  array(
	'classes' => array(
	#  id	name in(republic, imperium)
		0	=> array('Unknown', 'Unknown'),
		1	=> array('Vanguard', 'Powertech'),
        2	=> array('Commando', 'Mercenary'),
		3	=> array('Scoundrel', 'Operative'),
		4	=> array('Gunslinger', 'Sniper'),
		5	=> array('Sage', 'Sorcerer'),
		6	=> array('Shadow', 'Assassin'),
		7	=> array('Sentinel', 'Marauder'),
		8	=> array('Guardian', 'Juggernaut'),
	),
	'races' => array(
		'Unknown',
		'Human',
		'Rattataki',
		'Twi\'lek',
		'Chiss',
		'Sith Pureblood',
		'Miraluka',
		'Mirialan',
		'Zabrak',
		'Cyborg',
		'Cathar',
	),
	'factions' => array(
		'Republic',
		'Empire',
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
		'pk_tab_fs_swtorsettings'	=> 'SWToR Settings',
		'swtor_faction'				=> 'Faction',
		'swtor_faction_help'		=> 'The faction is used to hide classes of the opposing faction.',

		// Roles
		'role1'						=> 'Healer',
		'role2'						=> 'Tank',
		'role3'						=> 'Range-DD',
		'role4'						=> 'Melee',

		// Profile information
		'uc_gender'					=> 'Gender',
		'uc_male'					=> 'Male',
		'uc_female'					=> 'Female',
		'uc_guild'					=> 'Guild',
	),
);

?>