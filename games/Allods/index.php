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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class Manage_Game
{
	var $gamename	= 'Allods';
	var $maxlevel	= 47;
	var $version	= '1.0';

function do_it($install,$lang)
{
  global $db;
	if($lang == 'de')
	{
		$classes = array(
		array('Unknown','Unknown',0,47),
		
		array('Krieger','Platte',0,47),
		array('Paladin','Platte',0,47),
		array('Heiler','Platte',0,47),
		array('Beschwörer','Stoff',0,47),
		array('Magier','Stoff',0,47),
		array('Behüter','Leder',0,47),
		array('Psioniker','Leder',0,47),
		array('Späher','Leder',0,47),
		
		
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
		array('Unknown','Unknown',0,47),
		array('Warrior','Plate',0,47),
		array('Paladin','Plate',0,47),
		array('Priest','Plate',0,47),
		array('Necromancer','Cloth',0,47),
		array('Mage','Cloth',0,47),
		array('Druid','Leather',0,47),
		array('Psionic','Leather',0,47),
		array('Stalker','Leather',0,47),
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
		'Warrior'		=> '#a58a57',
		'Paladin'		=> '#00e1c8',
		'Priest'		=> '#ffff50',
		'Necromancer'	=> '#f12b47',
		'Mage'			=> '#2f91ff',
		'Druid'			=> '#ff8000',
		'Psionic'		=> '#ff80ff',
		'Stalker'		=> '#00c800',
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
