<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       08.03.2011
 * Date:        $Date: 2011-03-08 01:06:10 +0100 (Tue, 08 Mar 2011) $
 * -----------------------------------------------------------------------
 * @author      $Author: crimvel $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 9627 $
 *
 * $Id: index.php 9627 2011-01-26 00:06:10Z wallenium $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class Manage_Game
{
	var $gamename	= 'tera';
	var $maxlevel	= 50;
	var $version	= '1.0';

	function do_it($install,$lang){
		global $db;
		$aq =  array();

		if($lang == 'de'){
			$classes = array(
				array('Unknown', '',0,50,0),
				array('Bogenschütze', '',0,50,1),
				array('Berserker', '',0,50,2),
				array('Lanzer', '',0,50,3),
				array('Mystiker', '',0,50,4),
				array('Priester', '',0,50,5),
				array('Zerstörer', '',0,50,6),
				array('Zauberer', '',0,50,7),
				array('Krieger', '',0,50,8),
			);

			$races = array(
				'Unknown',
				'Aman',
				'Baraka',
				'Castanics',
				'Hochelfen',
				'Menschen',
				'Popori',
				'Elin',
			);

			$factions = array(
				'Standard',
			);
		}else{
			$classes = array(
				array('Unknown', '',0,50,0),
				array('Archer', '',0,50,1),
				array('Berserker', '',0,50,2),
				array('Lancer', '',0,50,3),
				array('Mystic', '',0,50,4),
				array('Priest', '',0,50,5),
				array('Slayer', '',0,50,6),
				array('Sorcerer', '',0,50,7),
				array('Warrior', '',0,50,8),
			);
			$races = array(
				'Unknown',
				'Aman',
				'Baraka',
				'Castanics',
				'High Elves',
				'Humans',
				'Popori',
				'Elin',
			);

			$factions = array(
				'default',
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
			'class_colors'	=> false,
			'max_level'		=> $this->maxlevel,
			'add_sql'		=> $aq,
			'version'		=> $this->version,
		);
		$gmanager->ChangeGame($this->gamename, $game_config, $lang);
	}
}
?>