<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2011
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

if(!class_exists('swtor')) {
	class swtor extends game_generic {

		protected $this_game	= 'swtor';
		protected $types		= array('classes', 'races', 'factions', 'roles');
		public $icons			= array('classes', 'classes_big', 'races', 'roles', 'events');
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
		public $version			= '1.2';
		
		/**
		 * Load classes
		 */
		protected function load_classes($langs) {
			foreach($langs as $lang) {
				$this->load_lang_file($lang);
				$faction = $this->config->get('swtor_faction');
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
		$faction = ($class_id) ? $this->config->get('swtor_faction')*8 : 0;
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
		
		public function profilefields() {
			$fields = array(
				'gender'	=> array(
					'type'			=> 'dropdown',
					'category'		=> 'character',
					'lang'			=> 'uc_gender',
					'options'		=> array('male' => 'uc_male', 'female' => 'uc_female'),
					'undeletable'	=> true,
					'visible'		=> true
				),
				'guild'	=> array(
					'type'			=> 'text',
					'category'		=> 'character',
					'lang'			=> 'uc_guild',
					'size'			=> 40,
					'undeletable'	=> true,
					'visible'		=> true
				)
			);
			return $fields;
		}

		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array();
			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
		
		public function admin_settings() {
			$admin_settings = array('swtor_faction'	=> array(
				'lang'		=> 'swtor_faction',
				'type'		=> 'dropdown',
				'size'		=> '1',
				'options'	=> $this->game->get('factions'),
				'default'	=> 0
			));
			return $admin_settings;
		}
	}
}
?>