<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2008-12-24 01:09:59 +0100 (Mi, 24 Dez 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: osr-corgan $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 3515 $
 * 
 * $Id: index.php 3515 2008-12-24 00:09:59Z osr-corgan $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'shakesfidget';
  var $maxlevel = 999;
  var $version  = '1.0';

  function do_it($install,$lang)
  {
    global $db;
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
        array('Unknown', '',0,999,0),
      	array('Krieger', '',0,999),  
      	array('Magier', '',0,999),  
      	array('Kundschafter', '',0,50),  
      );

      $races = array(
        'Menschen',
        'Elfen',
        'Zwerge',
        'Gnome',
        'Orks',
        'Dunkelelfen',
        'Goblins',
        'Dämonen'
      );

      $factions = array(
        'Member'
      );  
    }
    
    else
    {
      $classes = array(
        array('Unknown', '',0,999,0),
      	array('Warrior', '',0,999),  
      	array('Mage', '',0,999),  
      	array('Scout', '',0,999),  
      );

      $races = array(
        'Human',
        'Elf',
        'Dwarf',
        'Gnome',
        'Orcs',
        'Dark Elf',
        'Goblin',
        'Demon',
      );

      $factions = array(
        'Member'
      );
    }
  
    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
	    #Set Itemstats
	   	array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_itemstats' ;");
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
