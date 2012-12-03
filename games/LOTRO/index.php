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
  var $maxlevel = 50;

   function do_it($db,$table_prefix,$install,$lang)
   {
    if($lang == 'de')
    {
      $classes = array(
        array('Unknown', 'leichte Rüstung',0,50),
        array('Barde', 'mittlere Rüstung ',0,50),
        array('Hauptmann', 'schwere Rüstung',0,50),
        array('Jäger', 'mittlere Rüstung',0,50),
        array('Kundiger', 'leichte Rüstung',0,50),
        array('Schurke', 'mittlere Rüstung',0,50),
        array('Wächter', 'schwere Rüstung',0,50),
        array('Waffenmeister', 'schwere Rüstung',0,50)
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
    }else{
      $classes = array(
        array('Unknown', 'Light Armour',0,50),
        array('Minstrel', 'Medium Armour ',0,50),
        array('Captain', 'Heavy Armour',0,50),
        array('Hunter', 'Medium Armour',0,50),
        array('Lore-master', 'Light Armour',0,50),
        array('Burglar', 'Medium Armour',0,50),
        array('Guardian', 'Heavy Armour',0,50),
        array('Champion', 'Heavy Armour',0,50)
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
