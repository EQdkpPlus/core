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

class Manage_Game
{
  var $gamename = 'WoW';
  var $maxlevel = 80;
  var $version  = '3.0';

  function do_it($install,$lang)
  {
    global $db;
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
      	array('Druide', 'Leder',0,80,7),  
      	array('Hexenmeister', 'Stoff',0,80,10),
      	array('Jдger', 'Schwere Rьstung',0,80,4),
      	array('Krieger', 'Platte',0,80,12),
      	array('Magier', 'Stoff',0,80,11),
      	array('Paladin', 'Platte',0,80,13),
      	array('Priester', 'Stoff',0,80,6),
      	array('Schamane', 'Schwere Rьstung',0,80,9),
      	array('Schurke', 'Leder',0,80,2),   	
      	array('Todesritter', 'Platte',0,80,20),
      	array('Unknown', 'Platte',0,80,0)
      );

      $races = array(
        'Unknown',
        'Gnom',
        'Mensch',
        'Zwerg',
        'Nachtelf',
        'Troll',
        'Untoter',
        'Ork',
        'Taure',
        'Draenei',
        'Blutelf'
      );

      $factions = array(
        'Allianz',
        'Horde'
      );


    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = 'armory' WHERE config_name = 'pk_is_prio_first' ;");
    }
    elseif($lang == 'ru')
    {
      $classes = array(
        array('Неизвестно', 'Латы',0,80,0),
        array('Разбойник', 'Кожа',0,80,2),
        array('Охотник', 'Кольчуга',0,80,4),
        array('Жрец', 'Ткань',0,80,6),
        array('Друид', 'Кожа',0,80,7),
        array('Шаман', 'Кольчуга',0,80,9),
        array('Колдун', 'Ткань',0,80,10),
        array('Маг', 'Ткань',0,80,11),
        array('Воин', 'Латы',0,80,12),
        array('Паладин', 'Латы',0,80,13),
        array('Todesritter', 'Platte',0,80,20)
        );

      $races = array(
        'Неизвестно',
        'Гном',
        'Человек',
        'Дварф',
        'Ночной эльф',
        'Троль',
        'Нежить',
        'Орк',
        'Таурен',
        'Дреней',
        'Кровавый эльф'
      );

      $factions = array(
        'Альянс',
        'Орда'
      );

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = 'wowhead' WHERE config_name = 'pk_is_prio_first' ;");
    }
    else
    {
      $classes = array(
        array('Death Knight', 'Plate',0,80,20),        
      	array('Druid', 'Leather',0,80,7),
      	array('Hunter', 'Mail',0,80,4),
      	array('Mage', 'Cloth',0,80,11),
      	array('Paladin', 'Plate',0,80,13),
      	array('Priest', 'Cloth',0,80,6),      	
      	array('Rogue', 'Leather',0,80,2),
      	array('Shaman', 'Mail',0,80,9),      	
      	array('Unknown', 'Plate',0,80,0),
        array('Warlock', 'Cloth',0,80,10),        
        array('Warrior', 'Plate',0,80,12)    
      );

      $races = array(
        'Unknown',
        'Gnome',
        'Human',
        'Dwarf',
        'Night Elf',
        'Troll',
        'Undead',
        'Orc',
        'Tauren',
        'Draenei',
        'Blood Elf'
      );

      $factions = array(
        'Alliance',
        'Horde'
      );

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = 'wowhead' WHERE config_name = 'pk_is_prio_first' ;");
    }
  
    // The Class colors
    $classColorsCSS = array(
          'Druid'      		=> '#FF7C0A',
          'Warlock'    		=> '#9382C9',
          'Hunter'     		=> '#AAD372',
          'Warrior'    		=> '#C69B6D',
          'Paladin'    		=> '#F48CBA',
          'Mage'       		=> '#68CCEF',
          'Priest'     		=> '#FFFFFF',
          'Shaman'     		=> '#1a3caa',
          'Rogue'      		=> '#FFF468',
          'Death Knight'  => '#C41F3B',
        );
  
	#Classes Fix
    #Hunter
    array_push($aq, "UPDATE __members SET 	member_class_id = 4 WHERE member_class_id='3' ;");
    #Schaman
    array_push($aq, "UPDATE __members SET 	member_class_id = 9 WHERE 	member_class_id='8' ;");
    #Warrior
    array_push($aq, "UPDATE __members SET 	member_class_id = 12 WHERE 	member_class_id='1' ;");
    #Paladin
    array_push($aq, "UPDATE __members SET 	member_class_id = 13 WHERE 	member_class_id='5' ;");

    //lets do some tweak on the templates dependent on the game
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 14  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_wow.gif' WHERE style_id = 15  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_wow.gif' WHERE style_id = 16  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='bc_header3.gif' WHERE (style_id > 16) and (style_id < 30)  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 30  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='bc_header3.gif' WHERE style_id = 31  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='bc_header3.gif' WHERE style_id = 32  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 33  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='wowlogo3.png' WHERE style_id = 35  ;");

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
	    #Set Itemstats
	   	array_push($aq, "UPDATE __plus_config SET config_value = '1' WHERE config_name = 'pk_itemstats' ;");
	    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");
    }

    // this is the fix stuff.. instert the new information
    // into the database. moved it to a new class, its easier to
    // handle
    $gmanager = new GameManagerPlus();
    $game_config = array(
      'classes'       => $classes,
      'races'         => $races,
      'factions'      => $factions,
      'class_colors'  => $classColorsCSS,
      'max_level'     => $this->maxlevel,
      'add_sql'       => $aq,
      'version'       => $this->version,
    );
    
    $gmanager->ChangeGame($this->gamename, $game_config, $lang);
   }
}

?>
