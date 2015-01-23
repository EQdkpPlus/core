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
 * class		(string)	class for the field
 * readonly		(boolean)	field readonly?
 * js			(string)	extra js which shall be injected into the field
 * imageuploader(boolean)	make an imageuploader out of the field?
 * 		additional options for the imageuplaoder-field according to $imgoptions
 */
class hhidden extends html {

	protected static $type = 'hidden';
	
	public $name = '';
	
	public $imageuploader = false;
	public $imgup_type = 'all';
	public $storageFolder = false;
	
	private $imgoptions = array('prevheight', 'deletelink', 'noimgfile');
	private $out = '';
	
	public function _construct() {
		$out = '<input type="'.self::$type.'" name="'.$this->name.'" ';
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
		$out .= 'id="'.$this->id.'" ';
		if(!empty($this->value)) $out .= 'value="'.$this->value.'" ';
		if(!empty($this->class)) $out .= 'class="'.$this->class.'" ';
		if($this->readonly) $out .= 'readonly="readonly" ';
		if(!empty($this->js)) $out.= $this->js.' ';
		$imgup = '';
		
		if($this->imageuploader) {
			$imgopts = array();
			foreach($this->imgoptions as $opt) $imgopts[$opt] = $this->$opt;
			$imgup = $this->jquery->imageUploader($this->imgup_type, $this->id, $this->value, $this->imgpath, $imgopts, $this->storageFolder);
		}
		$this->out = $out.' />'.$imgup;
	}
	
	public function _toString() {
		return $this->out;
	}
	
	public function _inpval() {
		return $this->in->get($this->name, '');
	}
}
?>