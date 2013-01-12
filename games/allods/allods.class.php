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


if(!defined('EQDKP_INC')){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('allods')) {
	class allods extends game_generic {
		public static $shortcuts = array('config');
		protected $this_game	= 'allods';
		protected $types		= array('classes', 'races', 'factions', 'filters');
		public $icons			= array('classes', 'classes_big', 'events', 'races');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english', 'german');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public $lang			= false;
		public $version			= '1.1';
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
					array('name' => $this->glang('plate'), 'value' => 'class:1,2,3'),
					array('name' => $this->glang('leather'), 'value' => 'class:1,2,3,6,7,8'),
					array('name' => $this->glang('cloth'), 'value' => 'class:3,4,5,6,7'),
				));
			}
		}
		
		/**
		* Returns Information to change the game
		*
		* @param bool $install
		* @return array
		*/

		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array(
				1 => '#a58a57',
				2 => '#00e1c8',
				3 => '#ffff50',
				4 => '#f12b47',
				5 => '#2f91ff',
				6 => '#ff8000',
				7 => '#ff80ff',
				8 => '#00c800',
			);

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_allods', allods::$shortcuts);
?>