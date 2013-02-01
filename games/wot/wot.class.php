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

if(!class_exists('wot')) {
	class wot extends game_generic {
		public static $shortcuts = array('config');

		protected $this_game	= 'wot';
		protected $types		= array('classes', 'races');
		public $icons			= array('classes', 'classes_big', 'races', 'events');
		protected $classes		= array();
		protected $races		= array();
		protected $roles		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english', 'german');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= false;
		public $lang			= false;
		public $version			= '0.2';
		
		/**
		 * Load classes
		 */
		protected function load_classes($langs) {
			foreach($langs as $lang) {
				$this->load_lang_file($lang);
				$faction = $this->config->get('wot_faction');
				foreach($this->lang_file[$lang]['classes'] as $id => $fclass) {
					$this->classes[$lang][$id] = $fclass[$faction];
				}
			}
		}
		
		/**
		* Returns ImageTag with class-icon
		*
		* @param int $class_id
		* @param bool $big
		* @param bool $pathonly
		* @return html string
		*/
		public function decorate_classes($class_id, $big=false, $pathonly=false) {
		if($big AND !in_array('classes_big', $this->icons)) $big = false;
		$faction = ($class_id) ? $this->config->get('wot_faction')*8 : 0;
		$icon_path = $this->root_path.'games/'.$this->this_game.'/classes/'.($class_id+$faction).(($big) ? '_b.png' : '.png');
		if(is_file($icon_path)){
			return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt='' />";
		}
		return false;
		}

		/**
		* Initialises filters
		*
		* @param array $langs
		*/
		protected function load_filters($langs) {}

		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array();
			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_wot', wot::$shortcuts);
?>