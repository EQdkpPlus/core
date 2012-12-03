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
	   1 => 'Bard',
       2 => 'Beastmaster',
       3 => 'Blackmage',
       4 => 'Bluemage',
       5 => 'Corsair',
       6 => 'Dancer',
       7 => 'Dark Knight',
       8 => 'Dragoon',
       9 => 'Monk',
	  10 => 'Ninja',
	  11 => 'Paladin',
	  12 => 'Puppetmaster',
	  13 => 'Ranger',
	  14 => 'Redmage',
	  15 => 'Samurai',
	  16 => 'Scholar',
	  17 => 'Summoner',
	  18 => 'Thief',
	  19 => 'Warrior',
      20 => 'Whitemage',
	),
	'race' => array(
        'Unknown',
        'Elvaan M',
        'Elvaan F',
		'Galka',
        'Hume M',
		'Hume F',
        'Mithra',
        'Tarutaru M',
		'Tarutaru F',
	),
	'faction' => array(
        'Bastok',
        'SanDoria',
        'Windurst',
	),
	'lang' => array(
		'ffxi' => 'Final Fantasy XI',
		'tank' => 'Tank',
		'support' => 'Support',
		'damage_dealer' => 'Damage Dealer',
	),
);
?>