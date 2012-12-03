<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2008
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 *
 * $Id$
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
  var $maxlevel = 55;
  var $version  = '1.2';

  function do_it($install,$lang)
  {
    global $db;
   	$aq =  array();

   	if($lang == 'de')
    {
      $classes = array(
      	array('Unknown', '',0,55,0),
      	array('Krieger', '',0,55,1),
      	array('Kundschafter', '',0,55,2) ,
      	array('Schurke', '',0,55,3) ,
      	array('Magier', '',0,55,4) ,
      	array('Priester', '',0,55,5),
      	array('Ritter', '',0,55,6),
      	array('Druide', '',0,55,7),
      	array('Bewahrer', '',0,55,8)
      );

      $races = array(
        'Mensch',
        'Elfen'
      );

      $factions = array(
        'Standard'
      );

    }
    else
    {
      $classes = array(
      	array('unknown', '',0,55,0),
      	array('Warrior', '',0,55,1),
      	array('Scout', '',0,55,2) ,
      	array('Rogue', '',0,55,3) ,
      	array('Mage', '',0,55,4) ,
      	array('Priest', '',0,55,5),
      	array('Knight', '',0,55,6),
      	array('Druid', '',0,55,7),
      	array('Warden', '',0,55,8)

      );

      $races = array(
        'Human',
        'Elves'
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
    array_push($aq, "UPDATE __plus_config SET config_value = '1' WHERE config_name = 'pk_itemstats' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = 'buffed' WHERE config_name = 'pk_is_webdb' ;");
    array_push($aq, "UPDATE __plus_config SET config_value = 'http://romdata.buffed.de/img/icons/rom/32/' WHERE config_name = 'pk_is_icon_loc' ;");

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