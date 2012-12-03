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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'ffxi';
  var $maxlevel = 75;
  var $version  = '1.0';

  function do_it($install,$lang)
  {
   	$aq =  array();
    
   	if($lang == 'de')
    {
      $classes = array(
        array('Unknown', 'Unknown',0,75,0),
		array('Bard', 'Support',0,75,1),
        array('Beastmaster', 'Damage Dealer',0,75,2),
        array('Blackmage', 'Support',0,75,3),
        array('Bluemage', 'Damage Dealer',0,75,4),
        array('Corsair', 'Support',0,75,5),
        array('Dancer', 'Support',0,75,6),
        array('Dark Knight', 'Damage Dealer',0,75,7),
        array('Dragoon', 'Damage Dealer',0,75,8),
        array('Monk', 'Damage Dealer',0,75,9),
		array('Ninja', 'Tank',0,75,10),
		array('Paladin', 'Tank',0,75,11),
		array('Puppetmaster', 'Damage Dealer',0,75,12),
		array('Ranger', 'Damage Dealer',0,75,13),
		array('Redmage', 'Support',0,75,14),
		array('Samurai', 'Damage Dealer',0,75,15),
		array('Scholar', 'Support',0,75,16),
		array('Summoner', 'Support',0,75,17),
		array('Thief', 'Damage Dealer',0,75,18),
		array('Warrior', 'Damage Dealer',0,75,19),
        array('Whitemage', 'Support',0,75,20)
      );

      $races = array(
        'Unknown',
        'Elvaan M',
        'Elvaan F',
		'Galka',
        'Hume M',
		'Hume F',
        'Mithra',
        'Tarutaru M',
		'Tarutaru F',
      );

      $factions = array(
        'Bastok',
        'SanDoria',
        'Windurst',
      );


    }
    else
    {
      $classes = array(
        array('Unknown', 'Unknown',0,75,0),
		array('Bard', 'Support',0,75,1),
        array('Beastmaster', 'Damage Dealer',0,75,2),
        array('Blackmage', 'Support',0,75,3),
        array('Bluemage', 'Damage Dealer',0,75,4),
        array('Corsair', 'Support',0,75,5),
        array('Dancer', 'Support',0,75,6),
        array('Dark Knight', 'Damage Dealer',0,75,7),
        array('Dragoon', 'Damage Dealer',0,75,8),
        array('Monk', 'Damage Dealer',0,75,9),
		array('Ninja', 'Tank',0,75,10),
		array('Paladin', 'Tank',0,75,11),
		array('Puppetmaster', 'Damage Dealer',0,75,12),
		array('Ranger', 'Damage Dealer',0,75,13),
		array('Redmage', 'Support',0,75,14),
		array('Samurai', 'Damage Dealer',0,75,15),
		array('Scholar', 'Support',0,75,16),
		array('Summoner', 'Support',0,75,17),
		array('Thief', 'Damage Dealer',0,75,18),
		array('Warrior', 'Damage Dealer',0,75,19),
        array('Whitemage', 'Support',0,75,20)
      );

      $races = array(
        'Unknown',
        'Elvaan M',
        'Elvaan F',
		'Galka',
        'Hume M',
		'Hume F',
        'Mithra',
        'Tarutaru M',
		'Tarutaru F',
      );

      $factions = array(
        'Bastok',
        'SanDoria',
        'Windurst',
      );

	}
  
    // The Class colors
    $classColorsCSS = array(
        'Unknown'		=> '#808080',
	  	'Bard'			=> '#800000',
        'Beastmaster'	=> '#804040',
        'Blackmage'		=> '#68578E',
        'Bluemage'		=> '#0472EF',
        'Corsair'		=> '#BF4040',
        'Dancer'		=> '#FF80FF',
        'Dark Knight'	=> '#5b5955',
        'Dragoon'		=> '#671AFF',
        'Monk'			=> '#B37802',
	    'Ninja'			=> '#FFFF00',
	    'Paladin'		=> '#ADE1E5',
	    'Puppetmaster'	=> '#EBD35F',
	    'Ranger'		=> '#408000',
	    'Redmage'		=> '#FF0000',
	    'Samurai'		=> '#6700A2',
	    'Scholar'		=> '#775504',
	    'Summoner'		=> '#0F7D7D',
	    'Thief'			=> '#00BF00',
	    'Warrior'		=> '#2C77B9',
        'Whitemage'		=> '#E2D6EC',
        );
  

    //lets do some tweak on the templates dependent on the game
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 14  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_wow.gif' WHERE style_id = 15  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='logo_wow.gif' WHERE style_id = 16  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='bc_header3.gif' WHERE (style_id > 16) and (style_id < 30)  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 30  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='bc_header3.gif' WHERE style_id = 31  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='bc_header3.gif' WHERE style_id = 32  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_wow.gif' WHERE style_id = 33  ;");
    array_push($aq, "UPDATE __style_config SET logo_path='wowlogo3.png' WHERE style_id = 35  ;");

    //Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
    if($install)
    {
	    #Set the Users Template
	   	array_push($aq, "UPDATE __config SET config_value = 36 WHERE config_name='default_style' ;");
	   	array_push($aq, "UPDATE __users SET user_style = '36' ;");

	    #Set Itemstats
	   	array_push($aq, "UPDATE __plus_config SET config_value = '1' WHERE config_name = 'pk_itemstats' ;");
	    array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");
    }

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
