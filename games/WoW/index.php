<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * DAoC.php
 * Began: Fri May 13 2005
 *
 * $Id$
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'WoW';
  var $maxlevel = 70;

   function do_it($db,$table_prefix,$install,$lang)
   {
   	$aq =  array();
    if($lang == 'de')
    {
      $classes = array(
        array('Unknown', 'Platte',0,70,0),
        array('Schurke', 'Leder',0,70,2),
        array('Jäger', 'Schwere Rüstung',0,70,4),
        array('Priester', 'Stoff',0,70,6),
        array('Druide', 'Leder',0,70,7),
        array('Schamane', 'Schwere Rüstung',0,70,9),
        array('Hexenmeister', 'Stoff',0,70,10),
        array('Magier', 'Stoff',0,70,11),
        array('Krieger', 'Platte',0,70,12),
        array('Paladin', 'Platte',0,70,13)
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
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'armory' WHERE config_name = 'pk_is_prio_first' ;");
    }else
    {
      $classes = array(
        array('Unknown', 'Plate',0,70,0),
        array('Rogue', 'Leather',0,70,2),
        array('Hunter', 'Mail',0,70,4),
        array('Priest', 'Silk',0,70,6),
        array('Druid', 'Leather',0,70,7),
        array('Shaman', 'Mail',0,70,9),
        array('Warlock', 'Silk',0,70,10),
        array('Mage', 'Silk',0,70,11),
        array('Warrior', 'Mail',0,70,12),
        array('Paladin', 'Mail',0,39,13)
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
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'wowhead' WHERE config_name = 'pk_is_prio_first' ;");
    }


    //lets do some tweak on the templates dependent on the game
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 14  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_wow.gif' WHERE style_id = 15  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_wow.gif' WHERE style_id = 16  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='bc_header3.gif' WHERE (style_id > 16) and (style_id < 30)  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 30  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='bc_header3.gif' WHERE style_id = 31  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='bc_header3.gif' WHERE style_id = 32  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='bc_header3.gif' WHERE style_id = 33  ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='bc_header3.gif' WHERE style_id = 35  ;");

    array_push($aq, "UPDATE ". $table_prefix ."config SET config_value = 35 WHERE config_name='default_style' ;");
    array_push($aq, "UPDATE ". $table_prefix ."users SET user_style = '35' ;");

    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '1' WHERE config_name = 'pk_itemstats' ;");

    // this is the fix stuff.. instert the new information
    // into the database. moved it to a new class, its easier to
    // handle
    $gmanager = new GameManagerPlus($table_prefix, $db);
    $gmanager->ChangeGame($this->gamename, $classes, $races, $factions, $this->maxlevel,$aq,$lang);

     if (!$install)
     {
  	   $redir = "admin/config.php";
  	   redirect($redir);
  	 }
   }
}

?>
