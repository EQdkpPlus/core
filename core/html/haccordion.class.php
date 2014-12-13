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
 * name			classname for the tooltip call
 * id			id for the div/span
 * content		content of the tooltip
 * label		text on which the tooltip shall be displayed
 * usediv		use a div or a span
 * additional options for qtip
 *		contfunc, name, my, mat, classes, width
 */

class haccordion extends html {
	protected static $type = 'accordion';
	
	public $name = '';
	public $content = '';
	public $label = '';
	public $usediv = false;
	
	private $contfunc = true;
	private $accordion_opts	= array('active', 'collapsible', 'disabled', 'event');
	private $out = '';

	public function _construct() {
		if(empty($this->name)) $this->name = unique_id();
		if(empty($this->id)) $this->id = unique_id();
		
		$acc_opts = array();
		foreach($this->accordion_opts as $opt) $acc_opts[$opt] = $this->$opt;

		$this->out = $this->jquery->Accordion($this->name, $this->options, $acc_opts);
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '');
	}
}
?>