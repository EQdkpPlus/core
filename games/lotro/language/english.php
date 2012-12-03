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
            1 => 'Minstrel',
            2 => 'Captain',
            3 => 'Hunter',
            4 => 'Lore-master',
            5 => 'Burglar',
            6 => 'Guardian',
            7 => 'Champion',
            8 => 'Runekeeper',
            9 => 'Warden',
		),
      	'race' => array(
			'Unknown',
        	'Man',
        	'Hobbit',
        	'Elf',
        	'Dwarf'
      	),
		'faction' => array(
			'Normal',
			'MonsterPlay'
		),
		'lang' => array(
			'lotro' => 'Lord of the Rings Online',
			'heavy' => 'Heavy Armour',
			'medium' => 'Medium Armour',
			'light' => 'Light Armour',
		),
);
?>