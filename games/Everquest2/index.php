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

if ( !defined('EQDKP_INC') )
{
    header('HTTP/1.0 404 Not Found');
    exit;
}

class Manage_Game
{
  var $gamename = 'Everquest2';
  var $maxlevel = 80;
  var $version  = '1.0';

  function do_it($install,$lang)
  {
    global $db;
    if($lang == 'de')
	{
	  $classes = array(
        array('Beliebig','Beliebig',0,0),
        array('Assassine', 'Kette',80,80),
        array('Berserker', 'Platte',80,80),
        array('Brigant', 'Kette',80,80),
        array('Raufbold', 'Leder',80,80),
        array('Erzwinger', 'Stoff',80,80),
        array('Elementalist', 'Stoff',80,80),
        array('Schänder', 'Kette',80,80),
        array('Klagesänger', 'Kette',80,80),
        array('Furie', 'Leder',80,80),
        array('Wächter', 'Platte',80,80),
        array('Thaumaturgist', 'Stoff',80,80),
        array('Inquisitor', 'Platte',80,80),
        array('Mönch', 'Leder',80,80),
        array('Mystiker', 'Kette',80,80),
        array('Nekromant', 'Stoff',80,80),
        array('Paladin', 'Platte',80,80),
        array('Waldläufer', 'Kette',80,80),
        array('Schattenritter', 'Platte',80,80),
        array('Säbelrassler', 'Kette',80,80),
        array('Templer', 'Platte',80,80),
        array('Troubadour', 'Kette',80,80),
        array('Wärter', 'Leder',80,80),
        array('Hexenmeister', 'Stoff',80,80),
        array('Zauberer', 'Stoff',80,80),
      );

      $races = array(
        'Unbekannt',
        'Sarnak',
        'Gnom',
        'Mensch',
        'Barbar',
        'Zwerg',
        'Hochelf',
        'Dunkelelf',
        'Waldelf',
        'Halbelf',
        'Kerraner',
        'Troll',
        'Oger',
        'Froschlok',
        'Erudit',
        'Iksar',
        'Rattonga',
        'Halbling',
        'Arasai',
        'Fee'
      );

      $factions = array(
        'Gut',
        'Böse',
        'Neutral'
      );

      // The Class colors
      $classColorsCSS = array(
        'Beliebig'        => '#E1E1E1',
        'Assassine'       => '#E1E100',
        'Berserker'       => '#E10000',
        'Brigant'         => '#E1E100',
        'Raufbold'        => '#E10000',
        'Erzwinger'       => '#0000E1',
        'Elementalist'    => '#0000E1',
        'Schänder'        => '#00E100',
        'Klagesänger'     => '#E1E100',
        'Furie'           => '#00E100',
        'Wächter'         => '#E10000',
        'Thaumaturgist'   => '#0000E1',
        'Inquisitor'      => '#00E100',
        'Mönch'           => '#E10000',
        'Mystiker'        => '#00E100',
        'Nekromant'       => '#0000E1',
        'Paladin'         => '#E10000',
        'Waldläufer'      => '#E1E100',
        'Schattenritter'  => '#E10000',
        'Säbelrassler'    => '#E1E100',
        'Templer'         => '#00E100',
        'Troubadour'      => '#E1E100',
        'Wärter'          => '#00E100',
        'Hexenmeister'    => '#0000E1',
        'Zauberer'        => '#0000E1',
      );
    } else {
	  $classes = array(
        array('Unknown','Unknown',0,0),
        array('Assassin', 'Medium',80,80),
        array('Berserker', 'Heavy',80,80),
        array('Brigand', 'Medium',80,80),
        array('Bruiser', 'Light',80,80),
        array('Coercer', 'VeryLight',80,80),
        array('Conjuror', 'VeryLight',80,80),
        array('Defiler', 'Medium',80,80),
        array('Dirge', 'Medium',80,80),
        array('Fury', 'Light',80,80),
        array('Guardian', 'Heavy',80,80),
        array('Illusionist', 'VeryLight',80,80),
        array('Inquisitor', 'Heavy',80,80),
        array('Monk', 'Light',80,80),
        array('Mystic', 'Medium',80,80),
        array('Necromancer', 'VeryLight',80,80),
        array('Paladin', 'Heavy',80,80),
        array('Ranger', 'Medium',80,80),
        array('Shadowknight', 'Heavy',80,80),
        array('Swashbuckler', 'Medium',80,80),
        array('Templar', 'Heavy',80,80),
        array('Troubador', 'Medium',80,80),
        array('Warden', 'Light',80,80),
        array('Warlock', 'VeryLight',80,80),
        array('Wizard', 'VeryLight',80,80),
      );

      $races = array(
        'Unknown',
        'Sarnak',
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
        'Froglok',
        'Erudite',
        'Iksar',
        'Ratonga',
        'Halfling',
        'Arasai',
        'Fae'
      );

      $factions = array(
        'Good',
        'Evil',
        'Neutral'
      );

      // The Class colors
      $classColorsCSS = array(
        'Unknown'       => '#E1E1E1',
        'Assassin'      => '#E1E100',
        'Berserker'     => '#E10000',
        'Brigand'       => '#E1E100',
        'Bruiser'       => '#E10000',
        'Coercer'       => '#0000E1',
        'Conjuror'      => '#0000E1',
        'Defiler'       => '#00E100',
        'Dirge'         => '#E1E100',
        'Fury'          => '#00E100',
        'Guardian'      => '#E10000',
        'Illusionist'   => '#0000E1',
        'Inquisitor'    => '#00E100',
        'Monk'          => '#E10000',
        'Mystic'        => '#00E100',
        'Necromancer'   => '#0000E1',
        'Paladin'       => '#E10000',
        'Ranger'        => '#E1E100',
        'Shadowknight'  => '#E10000',
        'Swashbuckler'  => '#E1E100',
        'Templar'       => '#00E100',
        'Troubador'     => '#E1E100',
        'Warden'        => '#00E100',
        'Warlock'       => '#0000E1',
        'Wizard'        => '#0000E1',
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
	   	array_push($aq, "UPDATE __config SET config_value = 32 WHERE config_name='default_style' ;");
    	array_push($aq, "UPDATE __users SET user_style = '32' ;");
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
      'class_colors'  => $classColorsCSS,
      'max_level'     => $this->maxlevel,
      'add_sql'       => $aq,
      'version'       => $this->version,
    );
    
    $gmanager->ChangeGame($this->gamename, $game_config, $lang);
   }
}

?>
