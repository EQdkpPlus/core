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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'core/html/hdropdown.class.php');

/*
 * available options
 * name			(string) 	name of the field
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		(string)	selected option
 * class		(string)	class for the field
 * js			(string)	extra js which shall be injected into the field
 * options		(array)		dropdown-list
 * dependency	(array)		array containing IDs of other inputs fields to disable, format: array(opt1_key => array(id1,id2,...), opt2_key => array(id5,id6,...))
 * tolang		(boolean)	whether to put the vals of the list into language
 * disabled		(boolean)	disabled field
 * todisable	(array)		if not empty: array containing the elements which shall be disabled
 * text_after	(string)	Text added after the Multiselect
 * text_before	(string)	Text added before the Multiselect
 */
class hcountry extends hdropdown {

	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);

		$root_path = registry::get_const('root_path');
		$cfile = $root_path.'core/country_states.php';
		if (file_exists($cfile)){
			include($cfile);
			$this->options = $country_array;
		}
	}

}
