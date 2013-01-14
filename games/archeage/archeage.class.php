<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-01-14 15:10:51 +0100 (Mo, 14 Jan 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: sionaa $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 12841 $
 * 
 * $Id: archeage.class.php 12841 2013-01-14 14:10:51Z sionaa $
 */


if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('archeage')) {
	class archeage extends game_generic {
		public static $shortcuts = array();
		protected $this_game	= 'archeage';
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
				1	=> '#C70909',
				2	=> '#1FCA1F',
				3	=> '#13AFDC',
				4	=> '#FFE719',
				5	=> '#D41188',
								
				);

			$info['aq'] = array();

			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			#if($install){
			#}
			return $info;
		}
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_archeage', archeage::$shortcuts);
?>