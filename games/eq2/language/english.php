<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
 * Date:        $Date: 2009-11-01 13:22:28 +0100 (So, 01 Nov 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: wallenium $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 6326 $
 *
 * $Id: english.php 6326 2009-11-01 12:22:28Z wallenium $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
$english_array = array(
	'class' => array(
         0 => 'Unknown',
         1 => 'Assassin',
         2 => 'Berserker',
         3 => 'Brigand',
         4 => 'Bruiser',
         5 => 'Coercer',
         6 => 'Conjuror',
         7 => 'Defiler',
         8 => 'Dirge',
         9 => 'Fury',
        10 => 'Guardian',
        11 => 'Illusionist',
        12 => 'Inquisitor',
        13 => 'Monk',
        14 => 'Mystic',
        15 => 'Necromancer',
        16 => 'Paladin',
        17 => 'Ranger',
        18 => 'Shadowknight',
        19 => 'Swashbuckler',
        20 => 'Templar',
        21 => 'Troubador',
        22 => 'Warden',
        23 => 'Warlock',
        24 => 'Wizard',
	),
	'race' => array(
        'Unknown',
        'Sarnak',
        'Gnome',
        'Human',
        'Barbarian',
        'Dwarf',
        'High Elf',
        'Dark Elf',
        'Wood Elf',
        'Half Elf',
        'Kerra',
        'Troll',
        'Ogre',
        'Froglok',
        'Erudite',
        'Iksar',
        'Ratonga',
        'Halfling',
        'Arasai',
        'Fae'
	),
	'faction' => array(
        'Good',
        'Evil',
        'Neutral'
	),
	'lang' => array(
		'eq2' => 'Everquest 2',
		'very_light' => 'Very Light',
		'light' => 'Light',
		'medium' => 'Medium',
		'heavy' => 'Heavy',
	),
);
?>