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

if(!class_exists('diablo3')) {
	class diablo3 extends game_generic {
		protected $this_game	= 'diablo3';
		protected $types		= array('classes');
		public $icons			= array('classes', 'classes_big');
		protected $classes		= array();
		protected $races		= array();
		protected $factions		= array();
		protected $filters		= array();
		public $langs			= array('english', 'german');

		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public $lang			= false;
		public $version			= '1.0';

		/**
		* Initialises filters
		*
		* @param array $langs
		*/
		protected function load_filters($langs){
			
		}

		public function get_OnChangeInfos($install=false){
			//classcolors
			/*
			
			$info['class_color'] = array(
				1 => '#80FF00',
				2 => '#FFFFFF',
				3 => '#FFFFFF',
				4 => '#4080FF',
				5 => '#80FF00',
				6 => '#7d5ebc',
				7 => '#7d5ebc',
				8 => '#4080FF',
			);

			
			//Do this SQL Query NOT if the Eqdkp is installed -> only @ the first install
			if($install){
			}*/
			return $info;
		}
	}
}
?>