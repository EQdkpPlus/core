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
 
 //array('Klasse', 'Rüstungsart',minlevel,maxlevel,id-optional),

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'Warhammer';
  var $maxlevel = 40;
  var $version  = '1.2';

  function do_it($install,$lang)
  {
    global $db;
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
      	array('Unknown', '',0,40,0),  
      	array('Eisenbrecher', '',0,40,1),  
      	array('Maschinist', '',0,40,2),  
      	array('Runenpriester', '',0,40,3),  
      	array('Feuerzauberer', '',0,40,4),  
      	array('Sigmarpriester', '',0,40,5),  
      	array('Hexenjäger', '',0,40,6),  
      	array('Erzmagier', '',0,40,7),  
      	array('Schwertmeister', '',0,40,8),  
      	array('Schattenkrieger', '-',0,40,9),  
      	array('Weißer Löwe', '-',0,40,10),  
      	array('Schwarzork', '-',0,40,11),  
      	array('Goblin-Schamane', '-',0,40,12),  
      	array('Squig-Treiber', '-',0,40,13),  
      	array('Auserkorener', '-',0,40,14),  
      	array('Magus', '-',0,40,15),  
      	array('Zelot', '-',0,40,16),  
      	array('Chaosbarbar', '-',0,40,17),  
      	array('Hexenkriegerin', '-',0,40,18),  
      	array('Jünger des Khaine', '-',0,40,19),  
      	array('Zauberin', '-',0,40,20),
      	array('Schwarzer Gardist', '-',0,40,21),
      	array('Ritter des Sonnenordens', '-',0,40,22),
      	array('Ork-Spalta', '-',0,40,23),
      	array('Hammerträger', '-',0,40,24)
      );

      $races = array(
        'Unbekannt',
        'Zwerge',
        'Imperium',
    		'Hochelfen',
    		'Grünhäute',
    		'Chaos',
    		'Dunkelelfen'        
      );

      $factions = array(
        'Ordnung',
        'Zerstörung'
      );

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = 'armory' WHERE config_name = 'pk_is_prio_first' ;");
    }
    else
    {
      $classes = array(
      	array('Unknown', '',0,40,0),  
        array('Ironbreaker', '-',0,40,1),  
      	array('Engineer', '-',0,40,2),  
      	array('Rune Priest', '-',0,40,3),  
      	array('Bright Wizard', '-',0,40,4),  
      	array('Warrior Priest', '-',0,40,5),  
      	array('Witch Hunter', '-',0,40,6),  
      	array('Archmage', '-',0,40,7),  
      	array('Swordmaster', '-',0,40,8),  
      	array('Shadow Warrior', '-',0,40,9),  
      	array('White Lion', '-',0,40,10),  
      	array('Black Orc', '-',0,40,11),  
      	array('Goblin Shaman', '-',0,40,12),  
      	array('Squig Herder', '-',0,40,13),  
      	array('Chosen', '-',0,40,14),  
      	array('Magus', '-',0,40,15),  
      	array('Zealot', '-',0,40,16),  
      	array('Marauder', '-',0,40,17),  
      	array('Witch Elf', '-',0,40,18),  
      	array('Disciple of Khaine', '-',0,40,19),  
      	array('Sorceress', '-',0,40,20),
      	array('Black Guard', '-',0,40,21),
      	array('Knight of the Sun Order', '-',0,40,22),
      	array('Orc-Choppa', '-',0,40,23),
      	array('Hammerer', '-',0,40,24)
      );

      $races = array(
        'Unknown',
        'Dwarfs',
        'Empire',
        'Dwarf',
        'High Elves',
        'Greenskins',
        'Chaos',
        'Dark Elves'
      );

      $factions = array(
        'Order',
        'Destruction'
      );

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = 'wowhead' WHERE config_name = 'pk_is_prio_first' ;");
    }
    
    //lets do some tweak on the templates dependent on the game
    $aq =  array();
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
    }

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");

    // this is the fix stuff.. instert the new information
    // into the database. moved it to a new class, its easier to
    // handle    
    $gmanager = new GameManagerPlus();
    $game_config = array(
      'classes'       => $classes,
      'races'         => $races,
      'factions'      => $factions,
      'class_colors'  => false,
      'max_level'     => $this->maxlevel,
      'add_sql'       => $aq,
      'version'       => $this->version,
    );
    
    $gmanager->ChangeGame($this->gamename, $game_config, $lang);
   }
}

?>
