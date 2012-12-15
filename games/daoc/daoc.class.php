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

if(!class_exists('daoc')) {
	class daoc extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'daoc';
		protected $types		= array('classes', 'races', 'factions', 'filters');
		public $icons			= array('classes', 'classes_big', 'races', 'events');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= false;
		public $lang			= false;
		public $version			= '2.0';

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
			}
		}

		public function get_OnChangeInfos($install=false){
		
			$info['aq'] = array();
			
			/*
			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			if($install){
			}*/
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_daoc', daoc::$shortcuts);
?>