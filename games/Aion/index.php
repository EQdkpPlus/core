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
  var $gamename = 'Aion';
  var $maxlevel = 50;
  var $version  = '1.0';

  function do_it($install,$lang)
  {
    global $db;
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
        array('Unbekannt', '',0,50,0),
      	array('Templer', 'Platte',0,50),  
      	array('Gladiator', 'Platte',0,50),  
      	array('Assassine', 'Leder',0,50),  
      	array('Jäger', 'Leder',0,50),  
      	array('Zauberer', 'Stoff',0,50),  
      	array('Beschwörer', 'Stoff',0,50),  
      	array('Kleriker', 'Schwere Rüstung',0,50),  
      	array('Kantor', 'Schwere Rüstung',0,50)
      );

      $races = array(
        'Elyoss',
        'Asmodier'
      );

      $factions = array(
        'Member'
      );  
    }
    
    else
    {
      $classes = array(
        array('Unknown', '',0,50,0),
      	array('Templar', 'Plate',0,50),  
      	array('Gladiator', 'Plate',0,50),  
      	array('Assassin', 'Leather',0,50),  
      	array('Ranger', 'Leather',0,50),  
      	array('Sorcerer', 'Cloth',0,50),  
      	array('Spiritmaster', 'Cloth',0,50),  
      	array('Cleric', 'Mail',0,50),  
      	array('Chanter', 'Mail',0,50)  
      );

      $races = array(
        'Elyoss',
        'Asmodier'
      );

      $factions = array(
        'Member'
      );
    }
  
    // The Class colors
    $classColorsCSS = array(
          'Templar'      		=> '#4080FF',
          'Gladiator'    		=> '#4080FF',
          'Assassin'     		=> '#80FF00',
          'Ranger'    			=> '#80FF00',
          'Sorcerer'    		=> '#7d5ebc',
          'Spiritmaster'       	=> '#7d5ebc',
          'Cleric'     			=> '#FFFFFF',
          'Chanter'     		=> '#FFFFFF'
        );
  

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
