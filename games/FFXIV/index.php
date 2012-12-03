<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date: 2008-10-09 00:19:07 +0200 (Do, 09 Okt 2008) $
 * -----------------------------------------------------------------------
 * @author      $Author: osr-corgan $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 2786 $
 * 
 * $Id: index.php 2786 2008-10-08 22:19:07Z osr-corgan $
 */

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'FFXIV';
  var $maxlevel = 99;
  var $version  = '1.0';

  function do_it($install,$lang)
  {
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
        array('Unknown', 'Unknown',0,0,0),
        //array('All Jobs', 'Unknown',0,0,0),
        array('Archer', 'Disciple of War',0,0,2),
        array('Gladiator', 'Disciple of War',0,0,3),
        array('Lancer', 'Disciple of War',0,0,4),
        array('Marauder', 'Disciple of War',0,0,5),
        array('Pugilist', 'Disciple of War',0,0,6),
        array('Conjurer', 'Disciple of Magic',0,0,7),
        array('Thaumaturge', 'Disciple of Magic',0,0,8),
        array('Botanist', 'Disciple of the Land',0,0,9),
        array('Fisher', 'Disciple of the Land',0,0,10),
        array('Miner', 'Disciple of the Land',0,0,11),
        array('Alchemist', 'Disciple of the Hand',0,0,12),
        array('Armorer', 'Disciple of the Hand',0,0,13),
        array('Blacksmith', 'Disciple of the Hand',0,0,14),
        array('Carpenter', 'Disciple of the Hand',0,0,15),
        array('Culinarian', 'Disciple of the Hand',0,0,16),
        array('Goldsmith', 'Disciple of the Hand',0,0,17),
        array('Leatherworker', 'Disciple of the Hand',0,0,18),
        array('Weaver', 'Disciple of the Hand',0,0,19)
      );

      $races = array(
        'Unknown',
        'Elezen M',
        'Elezen F',
	   'Roegadyn',
        'Hyur M',
	   'Hyur F',
        'Miqote',
        'Lalafell M',
	   'Lalafell F',
      );

      $factions = array(
        'Gridania',
        'Limsa Lominsa',
        'Uldah',
      );


    }
    else
    {
      $classes = array(
        array('Unknown', 'Unknown',0,0,0),
        //array('All Jobs', 'Unknown',0,0,0),
        array('Archer', 'Disciple of War',0,0,2),
        array('Gladiator', 'Disciple of War',0,0,3),
        array('Lancer', 'Disciple of War',0,0,4),
        array('Marauder', 'Disciple of War',0,0,5),
        array('Pugilist', 'Disciple of War',0,0,6),
        array('Conjurer', 'Disciple of Magic',0,0,7),
        array('Thaumaturge', 'Disciple of Magic',0,0,8),
        array('Botanist', 'Disciple of the Land',0,0,9),
        array('Fisher', 'Disciple of the Land',0,0,10),
        array('Miner', 'Disciple of the Land',0,0,11),
        array('Alchemist', 'Disciple of the Hand',0,0,12),
        array('Armorer', 'Disciple of the Hand',0,0,13),
        array('Blacksmith', 'Disciple of the Hand',0,0,14),
        array('Carpenter', 'Disciple of the Hand',0,0,15),
        array('Culinarian', 'Disciple of the Hand',0,0,16),
        array('Goldsmith', 'Disciple of the Hand',0,0,17),
        array('Leatherworker', 'Disciple of the Hand',0,0,18),
        array('Weaver', 'Disciple of the Hand',0,0,19)
      );

      $races = array(
        'Unknown',
        'Elezen M',
        'Elezen F',
	   'Roegadyn',
        'Hyur M',
	   'Hyur F',
        'Miqote',
        'Lalafell M',
	   'Lalafell F',
      );

      $factions = array(
        'Gridania',
        'Limsa Lominsa',
        'Uldah',
      );

	}
  
    // The Class colors
    $classColorsCSS = array(
        'Unknown'		=> '#808080',
        'All Jobs'   		=> '#808080',
	   //'Bard'			=> '#800000',
        //'Beastmaster'  	=> '#804040',
        'Thaumaturge'		=> '#68578E',
        //'Bluemage'		=> '#0472EF',
        //'Corsair'		=> '#BF4040',
        //'Dancer'		=> '#FF80FF',
        'Marauder'	     => '#5b5955',
        'Lancer'		=> '#671AFF',
        'Pugilist'		=> '#B37802',
	   //'Ninja'			=> '#FFFF00',
	   'Gladiator'		=> '#ADE1E5',
	   //'Puppetmaster'	=> '#EBD35F',
	   'Archer'		=> '#408000',
	   'Conjurer'		=> '#FF0000',
	   //'Samurai'		=> '#6700A2',
	   //'Scholar'		=> '#775504',
	   //'Summoner'		=> '#0F7D7D',
	   //'Thief'			=> '#00BF00',
	   //'Warrior'		=> '#2C77B9',
        //'Whitemage'		=> '#E2D6EC',
        'Botanist'		=> '#808080',
        'Fisher'		     => '#808080',
        'Miner'		     => '#808080',
        'Alchemist'		=> '#808080',
        'Armorer'		=> '#808080',
        'Blacksmith'		=> '#808080',
        'Carpenter'		=> '#808080',
        'Culinarian'		=> '#808080',
        'Leatherworker'	=> '#808080',
        'Goldsmith'		=> '#808080',
        'Weaver'		=> '#808080',
        );
  

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
	    #Set the Users Template
	   	array_push($aq, "UPDATE __config SET config_value = 36 WHERE config_name='default_style' ;");
	   	array_push($aq, "UPDATE __users SET user_style = '36' ;");

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
