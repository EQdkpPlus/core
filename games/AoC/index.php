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

   function do_it($db,$table_prefix,$install,$lang)
   {
    if($lang == 'de'){
      $classes = array(
        array('Unknown','Unknown',0,0),
        
        // Roque
        array('Assassin','Chain',0,0),
        array('Barbar','Leather',0,0),
        array('Waldläufer','Chain',0,0),
        
        // warrior
        array('Eroberer','Platte',0,0),
        array('Wächter','Platte',0,0),
        array('Dunkler Templer','Platte',0,0),
        
        // Mage
        array('Dämonologe','Silk',0,0),
        array('Herold des Xotli','Silk',0,0),
        array('Nekromant','Silk',0,0),
        
        // Priest
        array('Mitrapriester','Leather',0,0),
        array('Bärenschamane','Chain',0,0),
        array('Vollstrecker Sets','Silk',0,0)
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
    }else{
      $classes = array(
        array('Unknown','Unknown',0,0),
        
        // Rogue
        array('Assassin','Chain',0,0),
        array('Barbarian','Leather',0,0),
        array('Ranger','Chain',0,0),
        
        // Warrior
        array('Conqueror','Plate',0,0),
        array('Dark Templar','Plate',0,0),
        array('Guardian','Plate',0,0),
        
        //Mage
        array('Demonologist','Silk',0,0),
        array('Herald of Xotli','Silk',0,0),
        array('Necromancer','Silk',0,0),
        
        //Priest
        array('Bear Shaman','Chain',0,0),
        array('Priest of Mitra','Leather',0,0),
        array('Tempest of Set','Silk',0,0)
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
    
    // The Class colors
    $classColorsCSS = array(
    );
    
    //lets do some tweak on the templates dependent on the game
    $aq =  array();
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE ". $table_prefix ."style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
    	array_push($aq, "UPDATE ". $table_prefix ."config SET config_value = 32 WHERE config_name='default_style' ;");
    	array_push($aq, "UPDATE ". $table_prefix ."users SET user_style = '32' ;");
    }

    //Itemstats
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '0' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");


    // this is the fix stuff.. instert the new information
    // into the database. moved it to a new class, its easier to
    // handle
    $gmanager = new GameManagerPlus($table_prefix, $db);
    $gmanager->ChangeGame($this->gamename, $classes, $races, $factions, $classColorsCSS, $this->maxlevel,$aq,$lang);

     if (!$install)
     {
  	   $redir = "admin/settings.php";
  	   redirect($redir);
  	 }
   }
}

?>
