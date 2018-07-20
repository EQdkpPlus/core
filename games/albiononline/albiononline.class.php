<?php

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('albiononline')) {
	class albiononline extends game_generic {	
		public $version			= '0.1.0';
		protected $this_game	= 'Albiononline';
		protected $types		= array('specs', 'roles', 'filters');
		public $langs			= array('german');	
		protected static $apiLevel = 20;
					
		protected $class_dependencies = array(
array (
  'name' => 'spezialisierungen',
  'type' => 'specs',
  'primary' => true,
  'admin' => false,
  'decorate' => false,
  'colorize' => true,
  'roster' => true,
  'recruitment' => true,
  'parent' => false,
), 
			
		); //end $class_dependencies
		
		public $default_roles = array (
  1 => 
  array (
    0 => 2,
  ),
  2 => 
  array (
    0 => 13,
    1 => 18,
  ),
  3 => 
  array (
    0 => 1,
    1 => 3,
    2 => 4,
    3 => 5,
    4 => 6,
    5 => 12,
    6 => 14,
  ),
  4 => 
  array (
    0 => 6,
    1 => 7,
    2 => 8,
    3 => 9,
    4 => 10,
    5 => 11,
    6 => 13,
    7 => 15,
    8 => 16,
    9 => 17,
    10 => 18,
  ),
);
		
		protected $class_colors = array (
  1 => '#D47373',
  2 => '#69CCF0',
  3 => '#F58CBA',
  4 => '#FFFFFF',
  5 => '#0070DE',
);
		
		protected $glang		= array();
		protected $lang_file	= array();
		protected $path		= '';
		protected $filters		= array();
		public $lang			= false;
		//Primary Classtype
		protected $specs = false;
		
				
		/* Constructor */
		public function __construct() {
			parent::__construct();
		}
				
		/* Install or Game Change Information */
		public function install($install=false){
			$info = array();
			return $info;
		}

		/**
		* Initialises filters
		*
		* @param array $langs
		*/
		protected function load_filters($langs){
			if(!$this->specs) {
				$this->load_type('specs', $langs);
			}
			foreach($langs as $lang) {
				$names = $this->specs[$this->lang];
				$this->filters[$lang][] = array('name' => '-----------', 'value' => false);
				foreach($names as $id => $name) {
					$this->filters[$lang][] = array('name' => $name, 'value' => 'spezialisierungen:'.$id);
				}
			}
		}
					
	}#class
}
?>