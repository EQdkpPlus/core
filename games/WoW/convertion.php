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
        "Druide"          => "Druid",
        "Hexenmeister"    => "Warlock",
        "Jäger"           => "Hunter",
        "Krieger"         => "Warrior",
        "Magier"          => "Mage",
        "Paladin"         => "Paladin",
        "Priester"        => "Priest",
        "Schurke"         => "Rogue",
        "Schamane"        => "Shaman",        
        "Todesritter"     => "Death Knight"
  ),
  'french'  => array(
        "Druide"          => "Druid",
        "Démoniste"    => "Warlock",
        "Chasseur"           => "Hunter",
        "Guerrier"         => "Warrior",
        "Mage"          => "Mage",
        "Paladin"         => "Paladin",
        "Prêtre"        => "Priest",
        "Voleur"         => "Rogue",
        "Chaman"        => "Shaman",        
        "Chevalier de la mort"     => "Death Knight"
  ),
  'russian'  => array(
        "Ðàçáîéíèê"       => "Druid",
        "Îõîòíèê"           => "Warlock",
        "Æðåö"              => "Hunter",
        "Äðóèä"             => "Warrior",
        "Øàìàí"             => "Mage",
        "Êîëäóí"            => "Paladin",
        "Ìàã"               => "Priest",
        "Âîèí"              => "Rogue",
        "Ïàëàäèí"           => "Shaman",
        "Todesritter"     	=> "Death Knight",
  )
);

?>
