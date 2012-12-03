<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       24.01.2011
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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class Manage_Game
{
	var $gamename	= 'Rift';
	var $maxlevel	= 50;
	var $version	= '1.1';

	function do_it($install,$lang){
		global $db;
		$aq =  array();

		if($lang == 'de'){
			$classes = array(
				array('Unknown', '',0,50,0),
				array('Krieger', '',0,50,1),
				array('Schurke', '',0,50,2),
				array('Magier', '',0,50,3),
				array('Geistlicher', '',0,50,4),
			);

			$races = array(
				'Unknown',
				'Zerge',
				'Hochelfen',
				'Mathosianer',
				'Bahmi',
				'Eth',
				'Kelari'
			);

			$factions = array(
				'Die Wächter',
				'Die Skeptiker'
			);
		}else{
			$classes = array(
				array('Unknown', '',0,50,0),
				array('Warrior', '',0,50,1),
				array('Rogue', '',0,50,2),
				array('Mage', '',0,50,3),
				array('Cleric', '',0,50,4),
			);
			$races = array(
				'Unknown',
				'Dwarves',
				'High Elves',
				'Mathosian',
				'Bahmi',
				'Eth',
				'Kelari'
			);

			$factions = array(
				'The Guardians',
				'The Defiant'
			);
		}

		//lets do some tweak on the templates dependent on the game
		$aq =  array();
			array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='bc_header3.gif' ;");
			array_push($aq, "UPDATE __style_config SET logo_path='/logo/logo_plus.gif' WHERE logo_path='/logo/logo_wow.gif' ;");
			array_push($aq, "UPDATE __style_config SET logo_path='logo_plus.gif' WHERE logo_path='logo_wow.gif' ;" );

		//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
		if($install){
		}

		$classColorsCSS = array(
          'Warrior'      	=> '#ff0000',
          'Rogue'    		=> '#800080',
          'Mage'     		=> '#ffff00',
          'Cleric'    		=> '#008000',
        );

		//Itemstats
		array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_itemstats' ;");
		array_push($aq, "UPDATE __plus_config SET config_value = '0' WHERE config_name = 'pk_is_autosearch' ;");

		// this is the fix stuff.. instert the new information
		// into the database. moved it to a new class, its easier to
		// handle
		$gmanager = new GameManagerPlus();
		$game_config = array(
			'classes'		=> $classes,
			'races'			=> $races,
			'factions'		=> $factions,
			'class_colors'  => $classColorsCSS,
			'max_level'		=> $this->maxlevel,
			'add_sql'		=> $aq,
			'version'		=> $this->version,
		);
		$gmanager->ChangeGame($this->gamename, $game_config, $lang);
	}
}
?>