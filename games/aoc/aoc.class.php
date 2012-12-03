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

if(!class_exists('aoc')){
	class aoc extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'aoc';
		protected $types		= array('classes', 'races', 'factions', 'filters');
		public $icons			= array('classes', 'races', 'events');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english', 'german');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= false;
		public $lang			= false;
		public $version			= '2.1';

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
				$this->filters[$lang][] = array('name' => '-----------', 'value' => false);
				foreach($names as $id => $name) {
					$this->filters[$lang][] = array('name' => $name, 'value' => 'class:'.$id);
				}
				$this->filters[$lang] = array_merge($this->filters[$lang], array(
					array('name' => '-----------', 'value' => false),
					array('name' => $this->glang('rogue', true, $lang), 'value' => 'class:1,2,3'),
					array('name' => $this->glang('soldier', true, $lang), 'value' => 'class:4,5,6'),
					array('name' => $this->glang('mage', true, $lang), 'value' => 'class:7,8,9'),
					array('name' => $this->glang('priest', true, $lang), 'value' => 'class:10,11,12'),
				));
			}
		}

		public function get_OnChangeInfos($install=false){
	
			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}#class
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_aoc', aoc::$shortcuts);
?>