<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
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
  var $gamename = 'Everquest2';
  var $maxlevel = 99;

   function do_it($db,$table_prefix,$install,$lang)
   {
    $classes = array(
      array('Unknown','Unknown',0,0),
      array('Assassin', 'Medium',70,99),
      array('Berserker', 'Heavy',70,99),
      array('Brigand', 'Medium',70,99),
      array('Bruiser', 'Light',70,99),
      array('Coercer', 'VeryLight',70,99),
      array('Conjuror', 'VeryLight',70,99),
      array('Defiler', 'Medium',70,99),
      array('Dirge', 'Medium',70,99),
      array('Fury', 'Light',70,99),
      array('Guardian', 'Heavy',70,99),
      array('Illusionist', 'VeryLight',70,99),
      array('Inquisitor', 'Heavy',70,99),
      array('Monk', 'Light',70,99),
      array('Mystic', 'Medium',70,99),
      array('Necromancer', 'VeryLight',70,99),
      array('Paladin', 'Heavy',70,99),
      array('Ranger', 'Medium',70,99),
      array('Shadowknight', 'Heavy',70,99),
      array('Swashbuckler', 'Medium',70,99),
      array('Templar', 'Heavy',70,99),
      array('Troubador', 'Medium',70,99),
      array('Warden', 'Light',70,99),
      array('Warlock', 'VeryLight',70,99),
      array('Wizard', 'VeryLight',70,99),

    );

    $races = array(
      'Sarnak',
      'Gnome',
      'Human',
      'Barbarian',
      'Dwarf',
      'High Elf',
      'Dark Elf',
      'Wood Elf',
      'Half Elf',
      'Kerra',
      'Troll',
      'Ogre',
      'Froglok',
      'Erudite',
      'Iksar',
      'Ratonga',
      'Halfling',
      'Arasai',
      'Fae'
    );

    $factions = array(
      'Good',
      'Evil',
      'Neutral'
    );

    //lets do some tweak on the templates dependent on the game
    $aq =  array();
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    #if(isStyleInstalled(32))
    #{
    	array_push($aq, "UPDATE ". $table_prefix ."config SET config_value = 32 WHERE config_name='default_style' ;");
    	array_push($aq, "UPDATE ". $table_prefix ."users SET user_style = '32' ;");
    #}


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
  	   $redir = "admin/settings.php";
  	   redirect($redir);
  	 }
   }
}

?>
