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
  var $gamename = 'Vanguard-SoH';
  var $maxlevel = 50;

   function do_it($db,$table_prefix,$install,$lang)
   {
      $classes = array(
        array('Unknown', 'Unknown',0,50),
        array('Bard', 'Leather',0,50),
        array('Berserker', 'Leather',0,50),
        array('Blood Mage', 'Cloth',0,50),
        array('Cleric', 'Plate',0,50),
        array('Disciple', 'Cloth',0,50),
        array('Dread Knight', 'Plate',0,50),
        array('Druid', 'Leather',0,50),
        array('Inquisitor', 'Plate',0,50),
        array('Monk', 'Cloth',0,50),
        array('Necromancer', 'Cloth',0,50),
        array('Paladin', 'Plate',0,50),
        array('Psionicist', 'Cloth',0,50),
        array('Ranger', 'Chain',0,50),
        array('Rogue', 'Chain',0,50),
        array('Shaman', 'Chain',0,50),
        array('Sorcerer', 'Cloth',0,50),
        array('Warrior', 'Plate',0,50)
      );

      $races = array(
        'Unknown',
        'Barbarian',
        'Dark Elf',
        'Dwarf',
        'Elf',
        'Giant',
        'Gnome',
        'Goblin',
        'Half-Elf',
        'Halfling',
        'High Elf',
        'Human',
        'Kojani',
        'Kurashasa',
        'Lesser Giant',
        'Mordebi',
        'Orc',
        'Qaliathari',
        'Raki',
        'Thestran',
        'Varanjar',
        'Varathari',
        'Vulmane',
        'Wood Elf'
      );

      $factions = array(
        'Unknown',
        'Good',
        'Evil',
        'Neutral'
      );

    // this is the fix stuff.. instert the new information
    // into the database. moved it to a new class, its easier to
    // handle
    $gmanager = new GameManagerPlus($table_prefix, $db);
    $gmanager->ChangeGame($this->gamename, $classes, $races, $factions, $this->maxlevel,false,$lang);

     if (!$install)
     {
  	   $redir = "admin/config.php";
  	   redirect($redir);
  	 }
   }
}

?>
