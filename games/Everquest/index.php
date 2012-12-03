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
  var $gamename = 'Everquest';
  var $maxlevel = 70;

   function do_it($db,$table_prefix,$install,$lang)
   {
    $classes = array(
      array('Unknown','Unknown',0,0),
      array('Warrior','Plate',0,0),
      array('Rogue','Chain',0,0),
      array('Monk','Leather',0,0),
      array('Ranger','Chain',0,0),
      array('Paladin','Plate',0,0),
      array('Shadow Knight','Plate',0,0),
      array('Bard','Plate',0,0),
      array('Beastlord','Leather',0,0),
      array('Shaman','Chain',0,0),
      array('Enchanter','Silk',0,0),
      array('Wizard','Silk',0,0),
      array('Necromancer','Silk',0,0),
      array('Magician','Silk',0,0),
      array('Berserker','Leather',0,0)
    );

    $races = array(
      'Unknown',
      'Gnome',
      'Human',
      'Barbarian',
      'Dwarf',
      'High Elf',
      'Dark Elf',
      'Wood Elf',
      'Half Elf',
      'Vah Shir',
      'Troll',
      'Ogre',
      'Frog',
      'Iksar',
      'Erudite',
      'Halfling'
    );

    $factions = array(
      'Good',
      'Evil'
    );

    //lets do some tweak on the templates dependent on the game
    $aq =  array();
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );
    array_push($aq, "UPDATE ". $table_prefix ."config SET config_value = 32 WHERE config_name='default_style' ;");
    array_push($aq, "UPDATE ". $table_prefix ."users SET user_style = '32' ;");

    //Itemstats
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '0' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");


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
