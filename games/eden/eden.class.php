<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-14 19:35:01 +0100 (Mo, 14 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: sionaa $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12844 $
 * 
 * $Id: eden.class.php 12844 2013-01-14 18:35:01Z sionaa $
 */


if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('eden')) {
	class eden extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'eden';
		protected $types		= array('classes', 'races', 'filters');
		public $icons			= array('classes', 'classes_big', 'events', 'races');
		protected $classes		= array();
		protected $races		= array();
		protected $filters		= array();
		public $langs			= array('english', 'german');

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
					array('name' => $this->glang('tank', true, $lang), 'value' => 'class:7,12,15'),
					array('name' => $this->glang('healer', true, $lang), 'value' => 'class:1,3,11'),
					array('name' => $this->glang('support', true, $lang), 'value' => 'class:1,3,11'),
					array('name' => $this->glang('melee', true, $lang), 'value' => 'class:2,9,13'),
					array('name' => $this->glang('ranged', true, $lang), 'value' => 'class:4,5,6,8,10,14'),
					
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
				1	=> '#368BDE',
				2	=> '#F87430',
				3	=> '#368BDE',
				4	=> '#8DCC47',
				5	=> '#8DCC47',
				6	=> '#B85AF0',
				7	=> '#F8D25A',
				8	=> '#B85AF0',
				9	=> '#F87430',
				10	=> '#8DCC47',
				11	=> '#368BDE',
				12	=> '#F8D25A',
				13	=> '#F87430',
				14	=> '#B85AF0',
				15	=> '#F8D25A',
												
				);

			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_eden', eden::$shortcuts);
?>