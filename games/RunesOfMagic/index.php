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
  var $gamename = 'RunesOfMagic';
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
      	array('Krieger', '',0,50,1), 
      	array('Kundschafter', '',0,50,2) ,
      	array('Schurke', '',0,50,3) ,
      	array('Magier', '',0,50,4) ,
      	array('Priester', '',0,50,5), 
      	array('Ritter', '',0,50,6) 
      );

      $races = array(
        'Mensch'
      );

      $factions = array(
        'Standard'
      );

    }
    else
    {
      $classes = array(
      	array('unknown', '',0,50,0),  
      	array('Warrior', '',0,50,1), 
      	array('Scout', '',0,50,2) ,
      	array('Rogue', '',0,50,3) ,
      	array('Mage', '',0,50,4) ,
      	array('Priest', '',0,50,5), 
      	array('Knight', '',0,50,6) 

      );

      $races = array(
        'Human'
      );

      $factions = array(
        'default'
      );

    }
    
    //lets do some tweak on the templates dependent on the game
    $aq =  array();
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
    }

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
    
    $gmanager->ChangeGame($this->gamename, $game_config, $lang);
   }
}

?>
