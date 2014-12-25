<?php
/*	Project:	EQdkp-Plus
 *	Package:	Dummy game package
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
 *
 *	This program is free software: you can redistribute it and/or modify
 *	it under the terms of the GNU Affero General Public License as published
 *	by the Free Software Foundation, either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This program is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU Affero General Public License for more details.
 *
 *	You should have received a copy of the GNU Affero General Public License
 *	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if(!class_exists('dummy')) {
	class dummy extends game_generic {#
		protected static $apiLevel	= 20;
		public $version				= '0.1.0';
		protected $this_game		= 'dummy';
		protected $types			= array();						// which information are stored?
		protected $classes			= array();
		protected $roles			= array();						// for each type there must be the according var
		protected $factions			= array();						// and the according function: load_$type
		protected $filters			= array();
		protected $realmlist		= array();
		protected $professions		= array();
		public $langs				= array('english', 'german');	// in which languages do we have information?

		protected $class_dependencies = array();
		public $default_roles		= array();
		protected $class_colors		= array();

		protected $glang			= array();
		protected $lang_file		= array();
		protected $path				= '';
		public $lang				= false;

		public function __construct() {
			parent::__construct();
		}

		public function install($install=false){
			//config-values
			$info['config'] = array();
			return $info;
		}
		
		public function load_filters($langs){
			return array();
		}

		public function profilefields(){
			// array with fields
		}

		public function admin_settings() {
			// array with admin fields
		}

	}#class
}
?>