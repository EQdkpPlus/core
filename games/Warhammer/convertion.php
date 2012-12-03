<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Convert the Classnames to english
$classconvert_array = array(
  'german'  => array(
        'Ork-Spalta'              => 'Orc-Choppa',
        'Hammerträger'            => 'Hammerer',
        'Schwarzer Gardist'       => 'Black Guard',
        'Ritter des Sonnenordens' => 'Knight of the Sun Order',
        'Eisenbrecher'            => 'Ironbreaker',
        'Hexenjäger'              => 'Witch Hunter',
        'Hexenkriegerin'          => 'Witch Elf',
        'Zaubererin'              => 'Sorceress',
        'Magus'                   => 'Magus',
        'Feuerzauberer'           => 'Bright Wizard',
        'Erzmagier'               => 'Archmage',
        'Jünger des Khaine'       => 'Disciple of Khaine',
        'Goblin Schamane'         => 'Gobbo Shaman',
        'Schwarzork'              => 'Black Orc',
        'Auserkorener'            => 'Chosen',
        'Chaosbarbar'             => 'Marauder',
        'Maschinist'              => 'Engineer',
        'Runenpriester'           => 'Rune Priest',
        'Sigmarpriester'          => 'Warrior Priest',
        'Schattenkrieger'         => 'Shadow Warrior',        
        'Weißer Löwe'             => 'White Lion',
        'Zelot'                   => 'Zealot',
        'Squig-Treiber'           => 'Squig Herder'
  )
);

?>
