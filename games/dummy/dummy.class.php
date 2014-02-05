<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2010
 * Date:		$Date: 2013-04-22 15:47:04 +0200 (Mo, 22 Apr 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13327 $
 *
 * $Id: dummy.class.php 13327 2013-04-22 13:47:04Z godmod $
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('dummy')) {
	class dummy extends game_generic {
		protected $this_game	= 'dummy';
		protected $types		= array();	// which information are stored?
		public $icons			= array();	// which icons do we have?
		protected $classes		= array();
		protected $roles		= array();
		protected $races		= array();															// for each type there must be the according var
		protected $factions		= array();															// and the according function: load_$type
		protected $filters		= array();
		protected $realmlist	= array();
		protected $professions	= array();
		public $langs			= array('english', 'german');					// in which languages do we have information?
		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public $lang			= false;
		public $version			= '0.1.0';

		public function __construct() {
			parent::__construct();
		}	
		
		/**
		 * Returns Information to change the game
		 *
		 * @param bool $install
		 * @return array
		 */
		public function get_OnChangeInfos($install=false){
			//classcolors
			$info['class_color'] = array();

			//config-values
			$info['config'] = array();

			//lets do some tweak on the templates dependent on the game
			$info['aq'] = array();

			return $info;
		}
		
		public function load_filters($langs){
			return array();
		}

	}#class
}
?>