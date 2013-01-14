<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-12 18:27:09 +0100 (Sa, 12 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12812 $
 * 
 * $Id: ro2.class.php 12812 2013-01-12 17:27:09Z godmod $
 */


if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('ro2')) {
	class ro2 extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'ro2';
		protected $types		= array('classes', 'races', 'filters');
		public $icons			= array('classes', 'classes_big', 'events', 'races');
		protected $classes		= array();
		protected $races		= array();
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
				$this->filters[$lang] = array_merge($this->filters[$lang], array(
					array('name' => '-----------', 'value' => false),
					array('name' => $this->glang('tank', true, $lang), 'value' => 'class:4'),
					array('name' => $this->glang('damage_dealer', true, $lang), 'value' => 'class:2,3,5'),
					array('name' => $this->glang('healer', true, $lang), 'value' => 'class:1'),
					
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
			$info['class_color'] = array(
				1	=> '#1FCA1F',
				2	=> '#D41188',
				3	=> '#19ECFF',
				4	=> '#C70909',
				5	=> '#FFE719',

				);

			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_ro2', ro2::$shortcuts);
?>