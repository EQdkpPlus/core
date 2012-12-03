<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       01.07.2009
 * Date:        $Date: 2009-05-17 16:10:37 +0200 (So, 17 Mai 2009) $
 * -----------------------------------------------------------------------
 * @author      $Author: hoofy_leon $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 4885 $
 *
 * $Id: filter.php 4885 2009-05-17 14:10:37Z sz3 $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

$english_array = array(
	'class' => array(
       0 => 'Unknown',
       1 => 'Artificer',
       2 => 'Bard',
       3 => 'Berserker',
       4 => 'Blacksmith',
       5 => 'Blood Mage',
       6 => 'Cleric',
       7 => 'Crafter',
       8 => 'Diplomat',
       9 => 'Disciple',
      10 => 'Dread Knight',
      11 => 'Druid',
      12 => 'Inquisitor',
      13 => 'Monk',
      14 => 'Necromancer',
      15 => 'Outfitter',
      16 => 'Paladin',
      17 => 'Psionicist',
      18 => 'Ranger',
      19 => 'Rogue',
      20 => 'Shaman',
      21 => 'Sorcerer',
      22 => 'Warrior',
	),
	'lang' => array(
		'vanguard' => 'Vanguard',
		'unknown' => 'Unknown',
		'cloth' => 'Cloth',
		'leather' => 'Leather',
		'chain' => 'Chain',
		'plate' => 'Plate',
	),
);
?>