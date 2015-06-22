<?php
/*	Project:	EQdkp-Plus
 *	Package:	World of Warships game package
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

if(!class_exists('wows')) {
	class wows extends game_generic {
		protected static $apiLevel	= 20;
		public $version				= '0.3.1';
		protected $this_game		= 'wows';
		protected $types			= array('races','classes','ships','roles');
		protected $classes			= array();
		protected $races			= array();
		public $langs				= array('german');

		protected $class_dependencies = array(
			array(
				'name'		=> 'race',
				'type'		=> 'races',
				'admin' 	=> false,
				'decorate'	=> false,
				'parent'	=> false,
			),
			array(
				'name'		=> 'class',
				'type'		=> 'classes',
				'admin'		=> false,
				'primary'	=> true,
				'roster'	=> true,
				'recruitment' => true,
				'parent'	=> false,
			),

			array(
				'name'		=> 'ship',
				'type'		=> 'ships',
				'admin'		=> false,
				'decorate'	=> true,
				'primary'	=> true,
				'colorize'	=> true,
				'roster'	=> false,
				'recruitment' => true,
				'parent'	=> array(
					'class' => array(
						0 	=> array(0),	// Unknown
						// Zerstörer
						1 	=> array(10102,10103,10104,10105,10106,10107,10108,10109,10110,20102,20103,20104,20105,20106,20107,20108,20109,20110),
						// Kreuzer
						2 	=> array(10201,10202,10203,10204,10205,10206,10207,10208,10209,10210,20201,20202,20203,20204,20205,20206,20207,20208,20209,20210),	
						// Schlachtschiffe
						3 	=> array(10303,10304,10305,10306,10307,10308,10309,10310,20303,20304,20305,20306,20307,20308,20309,20310,41306),
						// Flugzeugträger
						4	=> array(10404,10405,10406,10407,10408,10409,20404,20405,20406,20407),
						// Premium
						5	=> array(11202,11207,21208,41306,51105,51203,51205),
					),
				),
			),
		);
		public $default_roles = array( 
			1 => array(1,2,3,4,5),
			2 => array(1,2,3,4,5),
			3 => array(1,2,3,4,5),
			4 => array(1,2,3,4,5)
		);
	
		protected $glang		= array();
		protected $lang_file	= array();
		protected $path			= '';
		public $lang			= false;
		
		public function profilefields(){
			$this->load_type('langusdestroyer', array($this->lang));
			$this->load_type('languscruiser', array($this->lang));
			$this->load_type('langusbattleship', array($this->lang));
			$this->load_type('languscarrier', array($this->lang));
			$this->load_type('langjpndestroyer', array($this->lang));
			$this->load_type('langjpncruiser', array($this->lang));
			$this->load_type('langjpnbattleship', array($this->lang));
			$this->load_type('langjpncarrier', array($this->lang));
			$fields = array(
				'usdestroyer'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'character',
					'lang'			=> 'uc_destroyer',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langusdestroyer[$this->lang],
					'sort'			=> 1,
				),
/*				'usdestroyer'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'usa',
					'lang'			=> 'uc_destroyer',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langusdestroyer[$this->lang],
					'sort'			=> 1,
				),
				*/
				'uscruiser'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'usa',
					'lang'			=> 'uc_cruiser',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->languscruiser[$this->lang],
					'sort'			=> 2,
				),
				'usbattleship'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'usa',
					'lang'			=> 'uc_battleship',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langusbattleship[$this->lang],
					'sort'			=> 3,
				),
				'uscarrier'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'usa',
					'lang'			=> 'uc_carrier',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->languscarrier[$this->lang],
					'sort'			=> 4,
				),
				'jpndestroyer'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'jpn',
					'lang'			=> 'uc_destroyer',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langjpndestroyer[$this->lang],
					'sort'			=> 1,
				),
				'jpncruiser'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'jpn',
					'lang'			=> 'uc_cruiser',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langjpncruiser[$this->lang],
					'sort'			=> 2,
				),
				'jpnbattleship'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'jpn',
					'lang'			=> 'uc_battleship',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langjpnbattleship[$this->lang],
					'sort'			=> 3,
				),
				'jpncarrier'	=> array(
					'type'			=> 'multiselect',
					'category'		=> 'jpn',
					'lang'			=> 'uc_carrier',
					'undeletable'	=> true,
					'visible'		=> true,
					'options'		=> $this->langjpncarrier[$this->lang],
					'sort'			=> 4,
				),
			);
			return $fields;
		}

		public function decorate_classes($class_id, $big=false, $pathonly=false) {
			if($big AND !in_array('classes_big', $this->icons)) $big = false;
			$faction = ($class_id) ? $this->config->get('wows_faction')*8 : 0;
			$icon_path = $this->root_path.'games/'.$this->this_game.'/classes/'.($class_id+$faction).(($big) ? '_b.png' : '.png');
			if(is_file($icon_path)){
				return ($pathonly) ? $icon_path : "<img src='".$icon_path."' alt='' />";
			}
			return false;
		}

		public function install($install=false){
			return array();
		}

		protected function load_filters($langs){
			return array();
		}
	}
}
?>