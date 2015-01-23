<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
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

include_once(registry::get_const('root_path').'core/html/html.aclass.php');

/*
 * available options
 * name			(string) 	name of the field
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		
 * label		(string)	displayed text next to the slider
 * range		(boolean)	a double slider (range of two values) or a single slider (one value)?
 * min			(int)		minimum value of the field
 * max			(int)		maximum value of the field
 * width		(string)	width in (px) for the slider
 */
class hslider extends html {

	protected static $type = 'slider';
	
	public $name = '';
	public $range = true;
	private $options = array('min', 'max', 'value', 'width', 'label', 'name');
	private $out = '';
	
	protected function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$options = array();
		foreach($this->options as $opt) $options[$opt] = $this->$opt;
		$this->out = $this->jquery->Slider($this->id, $options, ($this->range) ? 'range' : 'normal');
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return ($this->range) ? $this->in->getArray($this->name, 'int') : $this->in->get($this->name, 0);
	}
}
?>