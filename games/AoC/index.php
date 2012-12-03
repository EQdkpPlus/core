<?php
/******************************
 * EQdkp
 * Copyright 2002-2003
 * Licensed under the GNU GPL.  See COPYING for full terms.
 * ------------------
 * DAoC.php
 * Began: Fri May 13 2005
 *
 * $Id: index.php 1864 2008-04-07 22:04:26Z wallenium $
 *
 ******************************/

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'AoC';
  var $maxlevel = false;
  var $version  = '1.0';

function do_it($install,$lang)
{
  global $db;
	if($lang == 'de')
	{
		$classes = array(
		array('Unknown','Unknown',0,80),
		
		// Schurke
		array('Assassin','Schurke',0,80),
		array('Barbar','Schurke',0,80),
		array('Waldläufer','Schurke',0,80),
		
		// Soldat
		array('Eroberer','Soldat',0,80),
		array('Dunkler Templer','Soldat',0,80),
		array('Wächter','Soldat',0,80),
		
		// Magier
		array('Dämonologe','Magier',0,80),
		array('Herold des Xotli','Magier',0,80),
		array('Nekromant','Magier',0,80),
		
		// Priester
		array('Bärenschamane','Priester',0,80),
		array('Mitrapriester','Priester',0,80),
		array('Vollstrecker Sets','Priester',0,80)
		);
		
		$races = array(
		'Unknown',
		'Aquilonier',
		'Cimmerier',
		'Stygier'
		);
		
		$factions = array(
		'Gut',
		'Böse'
		);
	}else
	{
		$classes = array(
		array('Unknown','Unknown',0,80),
		
		// Rogue
		array('Assassin','Rogue',0,80),
		array('Barbarian','Rogue',0,80),
		array('Ranger','Rogue',0,80),
		
		// Soldier
		array('Conqueror','Soldier',0,80),
		array('Dark Templar','Soldier',0,80),
		array('Guardian','Soldier',0,80),
		
		// Mage
		array('Demonologist','Mage',0,80),
		array('Herald of Xotli','Mage',0,80),
		array('Necromancer','Mage',0,80),
		
		// Priest
		array('Bear Shaman','Priest',0,80),
		array('Priest of Mitra','Priest',0,80),
		array('Tempest of Set','Priest',0,80)
		);
		
		$races = array(
		'Unknown',
		'Aquilonian',
		'Cimmerian',
		'Stygian'
		);
		
		$factions = array(
		'Good',
		'Evil'
		);
	}
    
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
      'class_colors'  => false,
      'max_level'     => $this->maxlevel,
      'add_sql'       => $aq,
      'version'       => $this->version,
    );
   }
}

?>
