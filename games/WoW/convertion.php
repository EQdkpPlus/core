<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date:  $
 * -----------------------------------------------------------------------
 * @author      $Author:  $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev:  $
 * 
 * $Id:  $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

// Convert the Classnames to english
$classconvert_array = array(
  'german'  => array(
        "Druide"          => "Druid",
        "Hexenmeister"    => "Warlock",
        "Jдger"           => "Hunter",
        "Krieger"         => "Warrior",
        "Magier"          => "Mage",
        "Paladin"         => "Paladin",
        "Priester"        => "Priest",
        "Schurke"         => "Rogue",
        "Schamane"        => "Shaman",        
        "Todesritter"     => "Death Knight"
  ),
  'russian'  => array(
        "Разбойник"       => "Druid",
        "Охотник"           => "Warlock",
        "Жрец"              => "Hunter",
        "Друид"             => "Warrior",
        "Шаман"             => "Mage",
        "Колдун"            => "Paladin",
        "Маг"               => "Priest",
        "Воин"              => "Rogue",
        "Паладин"           => "Shaman",
        "Todesritter"     	=> "Death Knight",
  )
);

?>
