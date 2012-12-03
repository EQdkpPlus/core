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
        1 => 'Assassin',
        2 => 'Barbarian',
        3 => 'Bear Shaman',
        4 => 'Conqueror',
        5 => 'Dark Templar',
        6 => 'Demonologist',
        7 => 'Guardian',
    	8 => 'Herald of Xotli',
      	9 => 'Necromancer',
       10 => 'Priest of Mitra',
       11 => 'Ranger',
       12 => 'Tempest of Set'
	),
	'race' => array(
		'Unknown',
		'Aquilonian',
		'Cimmerian',
		'Stygian',
		'Khitan'
	),
	'faction' => array(
		'Good',
		'Evil'
	),
	'lang' => array(
		'aoc' => 'Age of Conan',
		'rogue' => 'Rogue',
		'soldier' => 'Soldier',
		'mage' => 'Mage',
		'priest' => 'Priest',
	),
);
?>