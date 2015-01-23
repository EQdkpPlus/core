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

class htooltip extends html {
	protected static $type = 'none';
	
	public $name = '';
	public $content = '';
	public $label = '';
	public $usediv = false;
	
	private $contfunc = true;
	private $all_opts = array('contfunc', 'name', 'my', 'at', 'classes', 'width', 'class');
	private $out = '';

	public function _construct() {
		if(empty($this->name)) $this->name = unique_id();
		if(empty($this->class)) $this->class = "";
		if(empty($this->id)) $this->id = unique_id();
		$options = array();
		foreach($this->all_opts as $opt) {
			$options[$opt] = $this->$opt;
		}
		$this->jquery->qtip('.'.$this->name, 'return $(".'.$this->name.'_c", this).html();', $options);
		if(isset($this->usediv) && $this->usediv){
			$this->out = '<div class="'.$this->name.' '.$this->class.'" id="'.$this->id.'"><div class="'.$this->name.'_c" style="display:none;">'.$this->content.'</div>'.$this->label.'</div>';
		}else{
			$this->out = '<span class="'.$this->name.' '.$this->class.'" id="'.$this->id.'"><span class="'.$this->name.'_c" style="display:none;">'.$this->content.'</span>'.$this->label.'</span>';
		}
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return '';
	}
}
?>