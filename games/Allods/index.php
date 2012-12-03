<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * DAoC.php
 * Began: Fri May 13 2005
 *
 * $Id: index.php 3575 2009-01-14 11:12:58Z wallenium $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'Allods';
  var $maxlevel = 40;
  var $version  = '0.1';

function do_it($install,$lang)
{
  global $db;
	if($lang == 'de')
	{
		$classes = array(
		array('Unknown','Unknown',0,40),
		
		array('Krieger','Platte',0,40),
		array('Paladin','Platte',0,40),
		array('Heiler','Platte',0,40),
		array('Beschwörer','Stoff',0,40),
		array('Magier','Stoff',0,40),
		array('Behüter','Leder',0,40),
		array('Psioniker','Leder',0,40),
		array('Späher','Leder',0,40),
		
		
		);
				
		$races = array(
		'Unknown',
		'Gibberlings',
		'Elfen',
		'Kanians',
		'Xadaganians',
		'Orks',
		'Arisen'
		);
		
		$factions = array(
		'Die Liga',
		'Das Imperium'
		);
	}else
	{
		$classes = array(
		array('Unknown','Unknown',0,40),		
		array('Warrior','Plate',0,40),
		array('Paladin','Plate',0,40),
		array('Healer','Plate',0,40),
		array('Summoner','Cloth',0,40),
		array('Mage','Cloth',0,40),
		array('Warden','Leather',0,40),
		array('Psionicist','Leather',0,40),
		array('Scout','Leather',0,40),
		);
		
		$races = array(
		'Unknown',
		'Gibberlings',
		'Elves',
		'Kanians',
		'Xadaganians',
		'Orcs',
		'Arisen'
		);
		
		$factions = array(
		'The League',
		'The Empire'
		);
	}
    
		
		// The Class colors
    $classColorsCSS = array(
														
        'Warrior' => '#aa2222',
        'Paladin' => '#eeaa00',
		'Healer' => '#00aaaa',
		'Summoner'=> '#00aa55',
		'Mage'=> '#4466bb',		
		'Warden'=> '#55aa00',
		'Psionicist'=> '#8800aa',
		'Scout'=> '#aa5500',

     );
		
    //lets do some tweak on the templates dependent on the game
    $aq =  array();

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
      'class_colors'  => $classColorsCSS,
      'max_level'     => $this->maxlevel,
      'add_sql'       => $aq,
      'version'       => $this->version,
    );
    
    $gmanager->ChangeGame($this->gamename, $game_config, $lang);
   }
}

?>
