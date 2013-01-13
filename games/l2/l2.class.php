<?php

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('l2')) {
	class l2 extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'l2';
		protected $types		= array('classes', 'races', 'factions', 'filters');
		public $icons			= array('classes', 'classes_big', 'events', 'races');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public $lang			= false;
		public $version			= '0.1';		

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

		/**
		* Returns Information to change the game
		*
		* @param bool $install
		* @return array
		*/
		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array(
				1 => '#B5B171',
				2 => '#608269',
				3 => '#A6D1FB',
				4 => '#E2B824',
				5 => '#825F7F',
				6 => '#E05102',
				7 => '#FEFDFF',
				8 => '#E23B30',
				
			);
			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}
}
?>