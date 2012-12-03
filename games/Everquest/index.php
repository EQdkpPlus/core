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
  var $gamename = 'Everquest';
  var $maxlevel = 70;
  var $version  = '1.1';

  function do_it($install,$lang)
  {
    global $db;
    $classes = array(
      array('Unknown','Unknown',1,85),
      array('Warrior','Plate',1,85),
      array('Rogue','Chain',1,85),
      array('Monk','Leather',1,85),
      array('Ranger','Chain',1,85),
      array('Paladin','Plate',1,85),
      array('Shadow Knight','Plate',1,85),
      array('Bard','Plate',1,85),
      array('Beastlord','Leather',1,85),
      array('Shaman','Chain',1,85),
      array('Enchanter','Silk',1,85),
      array('Wizard','Silk',1,85),
      array('Necromancer','Silk',1,85),
      array('Magician','Silk',1,85),
      array('Berserker','Leather',1,85)
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
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
    	array_push($aq, "UPDATE __config SET config_value = 32 WHERE config_name='default_style' ;");
    	array_push($aq, "UPDATE __users SET user_style = '32' ;");
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
