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

include_once(registry::get_const('root_path').'core/html/html.aclass.php');

/*
 * available options
 * name			(string) 	name of the input
 * id			(string)	id of the input
 * value		(int) 		timestamp
 * class		(string)	class for the input
 * enablesecs	(boolean) 	wether seconds shall be used
 * hourf		(int) 		24 or 12 hour format
 * required		(boolean)	field required to be filled for form submission?
 */
class htimepicker extends html {

	protected static $type = 'timepicker';

	public $name				= '';
	public $enablesecs			= false;
	public $hourf				= 24;
	public $value				= '';
	public $required			= false;
	public $fvmessage			= false;
	public $returnJS			= false;
	public $returnDatetime		= true;

	private $out				= '';

	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}

	public function output() {
		$out = '<input type="text" name="'.$this->name.'" id="'.$this->id.'" value="'.$this->time->date("H:i", $this->value).'"';
		if(!empty($this->class)) $out .= ' class="'.$this->class.'"';
		if($this->required) $out .= ' required="required" data-fv-message="'.(($this->fvmessage) ? $this->fvmessage : registry::fetch('user')->lang('fv_required')).'"';
		$this->jquery->timePicker($this->id, $this->name, $this->value, $this->enablesecs, $this->hourf, $this->returnJS);
		$out .= ' />';
		$jsout = ($this->returnJS) ? '<script>'.$this->jquery->get_jscode('timepicker', $this->id).'</script>' : '';
		if($this->required) $out .= '<i class="fa fa-asterisk required small"></i>';
		return $jsout.$out;
	}

	/**
	 * Returns Time in Format H:i, in GMT
	 *
	 * @return string
	 */
	public function _inpval() {
		$strTimeInUserTime = $this->in->get($this->name, '');
		$intTimestamp = $this->time->convert_usertimestring_to_utc($strTimeInUserTime);
		return ($this->returnDatetime) ? date("H:i", $intTimestamp) : $intTimestamp;
	}
}
