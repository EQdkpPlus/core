<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
 * Date:        $Date: 2009-07-16 14:52:28 +0200 (Do, 16 Jul 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 5280 $
 *
 * $Id: english.php 5280 2009-07-16 12:52:28Z hoofy_leon $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}
$english_array = array(
	'class' => array(
    	0 => 'Unknown',
        1 => 'Bard',
        2 => 'Beastlord',
        3 => 'Berserker',
        4 => 'Enchanter',
        5 => 'Magician',
    	6 => 'Monk',
    	7 => 'Necromancer',
      	8 => 'Paladin',
      	9 => 'Ranger',
       10 => 'Rogue',
       11 => 'Shadow Knight',
       12 => 'Shaman',
       13 => 'Warrior',
       14 => 'Wizard'
	),
	'race' => array(
	      'Unknown',
	      'Gnome',
	      'Human',
	      'Barbarian',
	      'Dwarf',
	      'High Elf',
	      'Dark Elf',
	      'Wood Elf',
	      'Half Elf',
	      'Vah Shir',
	      'Troll',
	      'Ogre',
	      'Frog',
	      'Iksar',
	      'Erudite',
	      'Halfling'
    ),
    'faction' => array(
      		'Good',
      		'Evil'
    ),
    'lang' => array(
		'eq' => 'Everquest',
		'plate' => 'Plate',
		'silk' => 'Silk',
		'leather' => 'Leather',
		'chain' => 'Chain',
	),
);

?>