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
  var $gamename = 'Everquest2';
  var $maxlevel = 99;

   function do_it($db,$table_prefix,$install,$lang)
   {
    $classes = array(
      array('Unknown', 'Heavy',1,99),
      array('Fighter', 'Heavy',1,9),
      array('Scout', 'Medium',1,9),
      array('Mage', 'VeryLight',1,9),
      array('Priest', 'Heavy',1,9),
      array('Warrior', 'Heavy',10,19),
      array('Crusader', 'Heavy',10,19),
      array('Brawler', 'Light',10,19),
      array('Bruiser', 'Light',20,99),
      array('Monk', 'Light',20,99),
      array('Berserker', 'Heavy',20,99),
      array('Guardian', 'Heavy',20,99),
      array('Paladin', 'Heavy',20,99),
      array('Shadowknight', 'Heavy',20,99),
      array('Enchanter', 'VeryLight',10,19),
      array('Sorcerer', 'VeryLight',10,19),
      array('Summoner', 'VeryLight',10,19),
      array('Illusionist', 'VeryLight',20,99),
      array('Coercer', 'VeryLight',20,99),
      array('Wizard', 'VeryLight',20,99),
      array('Warlock', 'VeryLight',20,99),
      array('Necromancer', 'VeryLight',20,99),
      array('Conjuror', 'VeryLight',20,99),
      array('Cleric', 'Heavy',10,19),
      array('Druid', 'Light',10,19),
      array('Shaman', 'Medium',10,19),
      array('Templar', 'Heavy',20,99),
      array('Inquisitor', 'Heavy',20,99),
      array('Warden', 'Light',20,99),
      array('Fury', 'Light',20,99),
      array('Defiler', 'Medium',20,99),
      array('Mystic', 'Medium',20,99),
      array('Rogue', 'Medium',10,19),
      array('Bard', 'Medium',10,19),
      array('Predator', 'Medium',10,19),
      array('Swashbuckler', 'Medium',20,99),
      array('Brigand', 'Medium',20,99),
      array('Dirge', 'Medium',20,99),
      array('Troubador', 'Medium',20,99),
      array('Assassin', 'Medium',20,99),
      array('Ranger', 'Medium',20,99),
      array('Craftsmen', 'Heavy',1,99),
      array('Scholar', 'Heavy',1,99),
      array('Outfitter', 'Heavy',1,99),
      array('Provisioner', 'Heavy',1,99),
      array('Woodworker', 'Heavy',1,99),
      array('Carpenter', 'Heavy',1,99),
      array('Armorer', 'Heavy',1,99),
      array('Weaponsmith', 'Heavy',1,99),
      array('Tailor', 'Heavy',1,99),
      array('Jeweler', 'Heavy',1,99),
      array('Sage', 'Heavy',1,99),
      array('Alchemist', 'Heavy',1,99),

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
      'Kerra',
      'Troll',
      'Ogre',
      'Frog',
      'Erudite',
      'Iksar',
      'Ratonga',
      'Halfling'
    );

    $factions = array(
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
