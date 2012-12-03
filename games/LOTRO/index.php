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
  var $gamename = 'LOTRO';
  var $maxlevel = 50;

   function do_it($db,$table_prefix,$install,$lang)
   {
    if($lang == 'de')
    {
      $classes = array(
        array('Unknown', 'leichte Rüstung',0,50),
        array('Barde', 'mittlere Rüstung ',0,50),
        array('Hauptmann', 'schwere Rüstung',0,50),
        array('Jäger', 'mittlere Rüstung',0,50),
        array('Kundiger', 'leichte Rüstung',0,50),
        array('Schurke', 'mittlere Rüstung',0,50),
        array('Wächter', 'schwere Rüstung',0,50),
        array('Waffenmeister', 'schwere Rüstung',0,50)
      );

      $races = array(
        'Unknown',
        'Mensch',
        'Hobbit',
        'Elb',
        'Zwerg'
      );

      $factions = array(
        'Normal',
        'MonsterPlay'
      );
    }
    else
    {
      $classes = array(
        array('Unknown', 'Light Armour',0,50),
        array('Minstrel', 'Medium Armour ',0,50),
        array('Captain', 'Heavy Armour',0,50),
        array('Hunter', 'Medium Armour',0,50),
        array('Lore-master', 'Light Armour',0,50),
        array('Burglar', 'Medium Armour',0,50),
        array('Guardian', 'Heavy Armour',0,50),
        array('Champion', 'Heavy Armour',0,50)
      );

      $races = array(
        'Unknown',
        'Man',
        'Hobbit',
        'Elf',
        'Dwarf'
      );

      $factions = array(
        'Normal',
        'MonsterPlay'
      );
    }

    //lets do some tweak on the templates dependent on the game
    $aq =  array();

    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='lotro_header_01.gif' WHERE style_id=31 or style_id=32  ;" );

    #if(isStyleInstalled(32))
    #{
	    array_push($aq, "UPDATE ". $table_prefix ."config SET config_value = 32 WHERE config_name='default_style' ;");
    	array_push($aq, "UPDATE ". $table_prefix ."users SET user_style = '32' ;");
    #}

    //Itemstats
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '1' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'allakhazam' WHERE config_name = 'pk_is_prio_first' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '' WHERE config_name = 'pk_is_prio_second' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '' WHERE config_name = 'pk_is_prio_third' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '' WHERE config_name = 'pk_is_prio_fourth' ;");

#    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'http://lotro.allakhazam.com/images/icons/ItemIcons/' WHERE config_name = 'pk_is_icon_loc' ;");
#    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '.jpg' WHERE config_name = 'pk_is_icon_ext' ;");


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
