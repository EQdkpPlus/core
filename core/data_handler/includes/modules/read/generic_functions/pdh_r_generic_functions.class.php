<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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

if(!defined('EQDKP_INC'))
{
	die('Do not access this file directly.');
}

if(!class_exists('pdh_r_generic_functions')){
	class pdh_r_generic_functions extends pdh_r_generic{
		public static $shortcuts = array();

		public $default_lang = 'english';
		

		public $hooks = array();

		public $presets = array(
				'genfunc_count' => array('count', array('%value%'), array()),
				'genfunc_colorize' => array('colorize', array('%value%'), array()),
				'genfunc_itemtooltip' => array('itemtooltip', array('%value%'), array()),
				'genfunc_shorten' => array('shorten', array('%value%', 320), array()),
				'genfunc_striptags' => array('striptags', array('%value%'), array()),
		);


		public function reset($affected_ids=array(), $strHook='', $arrAdditionalData=array()){
			
		}


		public function init(){
			
		}


		public function get_count($arrValue){
			return count($arrValue);
		}

		public function get_colorize($intValue){
			return color_item($intValue);
		}
		
		public function get_itemtooltip($strValue){
			infotooltip_js();
			return infotooltip($strValue);
		}
		
		public function get_shorten($strValue, $intCount=320){
			return truncate(strip_tags($strValue), $intCount, '...', false, true);
		}
		
		public function get_striptags($strValue){
			return strip_tags($strValue);
		}
	}
}
