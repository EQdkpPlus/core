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

if(!class_exists('atlantica')) {
	class atlantica extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'atlantica';
		protected $types		= array('classes', 'races', 'filters');
		public $icons			= array('classes', 'classes_big', 'events');
		protected $classes		= array();
		protected $races		= array();
		protected $filters		= array();
		public $langs			= array('english');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
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
				$this->filters[$lang] = array(
					array('name' => '-----------', 'value' => false),
					array('name' => $this->glang('melee'), 'value' => 'class:1,5,7,9'),
					array('name' => $this->glang('ranged'), 'value' => 'class:2,3,4'),
					array('name' => $this->glang('magic'), 'value' => 'class:6,8'),
				);
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
		public function decorate_classes($class_id, $big=false, $pathonly=false){
			$icon_path = $this->root_path.'games/'.$this->game.'/classes/'.$class_id.(($big) ? '_b.png' : '.png');
			if($pathonly) {
				return $icon_path;
			}
			return "<img src='".$icon_path."' />";
		}

		public function get_OnChangeInfos($install=false){
			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install)
			#{
			#}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_atlantica', atlantica::$shortcuts);
?>