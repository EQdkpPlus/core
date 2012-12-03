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
  var $maxlevel = 60;
  var $version  = '1.1';

  function do_it($install,$lang)
  {
    global $db;
    if($lang == 'de')
    {
      $classes = array(
        array('Unknown', 'leichte Rüstung',0,60),
        array('Barde', 'mittlere Rüstung',0,60),
        array('Hauptmann', 'schwere Rüstung',0,60),
        array('Jäger', 'mittlere Rüstung',0,60),
        array('Kundiger', 'leichte Rüstung',0,60),
        array('Schurke', 'mittlere Rüstung',0,60),
        array('Wächter', 'schwere Rüstung',0,60),
        array('Waffenmeister', 'schwere Rüstung',0,60),
        array('Runenbewahrer', 'leichte Rüstung',0,60),
        array('Hüter', 'mittlere Rüstung',0,60)
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
        array('Unknown', 'Light Armour',0,60),
        array('Minstrel', 'Medium Armour',0,60),
        array('Captain', 'Heavy Armour',0,60),
        array('Hunter', 'Medium Armour',0,60),
        array('Lore-master', 'Light Armour',0,60),
        array('Burglar', 'Medium Armour',0,60),
        array('Guardian', 'Heavy Armour',0,60),
        array('Champion', 'Heavy Armour',0,60),
        array('Runekeeper', 'Light Armour',0,60),
        array('Warden', 'Medium Armour',0,60)
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
    
    // The Class colors
    $classColorsCSS = array(
          'Minstrel'      => '#FFCC33',
          'Captain'    		=> '#0033CC',
          'Hunter'     		=> '#006600',
          'Lore-master'   => '#00CCFF',
          'Burglar'    		=> '#444444',
          'Guardian'      => '#990000',
          'Champion'      => '#CC3300',
          //'Runekeeper'    => '#1a3caa',
          //'Warden'        => '#FFF468',
        );
    
    //lets do some tweak on the templates dependent on the game
    $aq =  array();

    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    array_push($aq, "UPDATE __style_config SET logo_path='lotro_header_01.gif' WHERE style_id=31 or style_id=32  ;" );

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
    }

    //Itemstats
    array_push($aq, "UPDATE __plus_config SET config_value = '1' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = 'allakhazam' WHERE config_name = 'pk_is_prio_first' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '' WHERE config_name = 'pk_is_prio_second' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '' WHERE config_name = 'pk_is_prio_third' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '' WHERE config_name = 'pk_is_prio_fourth' ;");

#    array_push($aq, "UPDATE __plus_config SET config_value = 'http://lotro.allakhazam.com/images/icons/ItemIcons/' WHERE config_name = 'pk_is_icon_loc' ;");
#    array_push($aq, "UPDATE __plus_config SET config_value = '.jpg' WHERE config_name = 'pk_is_icon_ext' ;");


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
