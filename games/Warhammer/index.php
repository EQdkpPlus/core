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
 
 //array('Klasse', 'Rüstungsart',minlevel,maxlevel,id-optional),

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'Warhammer';
  var $maxlevel = 40;

   function do_it($db,$table_prefix,$install,$lang,$install=true)
   {
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
      	array('Unbekannt', '',0,40,),  
      	array('Eisenbrecher', '',0,40,),  
      	array('Maschinist', '',0,40,),  
      	array('Runenpriester', '',0,40,),  
      	array('Feuerzauberer', '',0,40,),  
      	array('Sigmarpriester', '',0,40,),  
      	array('Hexenjäger', '',0,40,),  
      	array('Erzmagier', '',0,40,),  
      	array('Schwertmeister', '',0,40,),  
      	array('Schattenkrieger', '-',0,40,),  
      	array('Weißer Löwe', '-',0,40,),  
      	array('Schwarzork', '-',0,40,),  
      	array('Goblin-Schamane', '-',0,40,),  
      	array('Squig-Treiber', '-',0,40,),  
      	array('Auserkorener', '-',0,40,),  
      	array('Magus', '-',0,40,),  
      	array('Zelot', '-',0,40,),  
      	array('Chaosbarbar', '-',0,40,),  
      	array('Hexenkriegerin', '-',0,40,),  
      	array('Jünger des Khaine', '-',0,40,),  
      	array('Zauberin', '-',0,40,) 
      );

      $races = array(
        'Zwerg',
        'Imperium',
		'Hochelfe',
		'Grünhaut',
		'Chaos',
		'Dunkelelfen'        
      );

      $factions = array(
        'Ordnung',
        'Zerstörung'
      );

    //Itemstats
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'armory' WHERE config_name = 'pk_is_prio_first' ;");
    }
    
    /*
    elseif($lang == 'ru')
    {
      $classes = array(
        array('Íåèçâåñòíî', 'Ëàòû',0,80,0),
        array('Ðàçáîéíèê', 'Êîæà',0,80,2),
        array('Îõîòíèê', 'Êîëü÷óãà',0,80,4),
        array('Æðåö', 'Òêàíü',0,80,6),
        array('Äðóèä', 'Êîæà',0,80,7),
        array('Øàìàí', 'Êîëü÷óãà',0,80,9),
        array('Êîëäóí', 'Òêàíü',0,80,10),
        array('Ìàã', 'Òêàíü',0,80,11),
        array('Âîèí', 'Ëàòû',0,80,12),
        array('Ïàëàäèí', 'Ëàòû',0,80,13),
        array('Todesritter', 'Platte',0,80,20)
        );

      $races = array(
        'Íåèçâåñòíî',
        'Ãíîì',
        '×åëîâåê',
        'Äâàðô',
        'Íî÷íîé ýëüô',
        'Òðîëü',
        'Íåæèòü',
        'Îðê',
        'Òàóðåí',
        'Äðåíåé',
        'Êðîâàâûé ýëüô'
      );

      $factions = array(
        'Àëüÿíñ',
        'Îðäà'
      );

    //Itemstats
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'wowhead' WHERE config_name = 'pk_is_prio_first' ;");
    }
    */
    else
    {
      $classes = array(
      	array('Unknown', '',0,40,),  
	    array('Ironbreaker', '-',0,40,),  
      	array('Engineer', '-',0,40,),  
      	array('Runepriest', '-',0,40,),  
      	array('Bright Wizard', '-',0,40,),  
      	array('Warrior Priest', '-',0,40,),  
      	array('Witch Hunter', '-',0,40,),  
      	array('Arch Mage', '-',0,40,),  
      	array('Swordmaster', '-',0,40,),  
      	array('Shadow Warrior', '-',0,40,),  
      	array('Whtie Lion', '-',0,40,),  
      	array('Black Orc', '-',0,40,),  
      	array('Goblin Shaman', '-',0,40,),  
      	array('Goblin Squig Herder', '-',0,40,),  
      	array('Chosen', '-',0,40,),  
      	array('Magus', '-',0,40,),  
      	array('Zealot', '-',0,40,),  
      	array('Marauder', '-',0,40,),  
      	array('Witch Elf', '-',0,40,),  
      	array('Disciple of Khaine', '-',0,40,),  
      	array('Sorcerer', '-',0,40,) 
      );

      $races = array(
        'Unknown',
        'Dwarf',
        'Empire',
        'Dwarf',
        'High Elf',
        'Greenskin',
        'Chaos',
        'Dark Elf'
      );

      $factions = array(
        'Order',
        'Destruction'
      );

    //Itemstats
    array_push($aq, "UPDATE ". $table_prefix ."plus_config SET config_value = 'wowhead' WHERE config_name = 'pk_is_prio_first' ;");
    }
    
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
