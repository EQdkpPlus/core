<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       08.03.2011
 * Date:        $Date: 2012-08-30 00:58:21 +0200 (Thu, 30 Aug 2012) $
 * -----------------------------------------------------------------------
 * @author      $Author: godmod $
 * @copyright   2006-2008 Corgan - Stefan Knaak | Wallenium & the EQdkp-Plus Developer Team
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev: 11999 $
 * 
 * $Id: ac2.class.php 11999 2012-08-29 22:58:21Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('ac2')) {
	class ac2 extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'ac2';
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
		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array(
				1      	=> '#FFFFFF',
				2    	=> '#FFFFFF',
				3     	=> '#FFFFFF',
				4    	=> '#FFFFFF',
				5      	=> '#FFFFFF',
				6    	=> '#FFFFFF',
				7     	=> '#FFFFFF',
				8    	=> '#FFFFFF',
				9      	=> '#FFFFFF',
				10    	=> '#FFFFFF',
				11     	=> '#FFFFFF',
				12    	=> '#FFFFFF',
				13      => '#FFFFFF',
				14    	=> '#FFFFFF',
				15     	=> '#FFFFFF',
				16    	=> '#FFFFFF',
				17      => '#FFFFFF',
				18    	=> '#FFFFFF',
				19     	=> '#FFFFFF',
				20    	=> '#FFFFFF',
				21      => '#FFFFFF',
				22    	=> '#FFFFFF',
				
			);
			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ac2', ac2::$shortcuts);
?>