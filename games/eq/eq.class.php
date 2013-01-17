<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('eq')) {
	class eq extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'eq';
		protected $types		= array('classes', 'races', 'factions', 'filters');
		public $icons			= array('classes', 'classes_big', 'races');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public  $lang			= false;
		public $version	= '2.1.2';

		/**
		* Returns Information to change the game
		*
		* @param bool $install
		* @return array
		*/
		public function get_OnChangeInfos($install=false){
		
		$info = array();
		
			return $info;
		}

		/**
		* Initialises filters
		*
		* @param array $langs
		*/
		protected function load_filters($langs){
			if(!$this->classes) {
				$this->load_type('classes', $langs);
			}
			foreach($langs as $lang) {
				$names = $this->classes[$this->lang];
				$this->filters[$lang] = array(
					array('name' => '-----------', 'value' => false),
					array('name' => $this->glang('plate', true, $lang), 'value' => array(1 => 'class', 8 => 'class', 11 => 'class', 13 => 'class', 15 => 'class')),
					array('name' => $this->glang('chain', true, $lang), 'value' => array(3 => 'class', 9 => 'class', 10 => 'class', 12 => 'class')),
					array('name' => $this->glang('leather', true, $lang), 'value' => array(2 => 'class', 6 => 'class', 16 => 'class')),
					array('name' => $this->glang('silk', true, $lang), 'value' => array(4 => 'class', 5 => 'class', 7 => 'class', 14 => 'class')),
				);
			}
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_eq', eq::$shortcuts);
?>