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
 * name			(string) 	name of the field
 * id			(string)	id of the field, defaults to a clean form of name if not set
 * value		(array)		array containing the selected values
 * class		(string)	class for the field
 * disabled		(boolean)	disabled field
 * js			(string)	extra js which shall be injected into the field
 * options		(array)		list of all available options
 * todisable	(array)		list of all options which shall not be selectable
 * tolang		(boolean)	apply language function on values of option-array
 * text_after	(string)	Text added after the Multiselect
 * text_before	(string)	Text added before the Multiselect
 *
 * additional options for jquery->multiselect
 * height 		(int)		height of the dropdown in px
 * width 		(int)		width of the dropdown in px
 * preview_num	(int)		number of selected options to be displayed in a comma seperated list in collapsed state
 * no_animation (boolean)	disable collapse animation?
 * header
 * filter
 */
class hiconselect extends html {

	protected static $type = 'dropdown';

	public $name				= '';
	public $disabled			= false;

	public $multiple			= true;
	public $width				= 200;
	public $height				= 200;
	public $preview_num			= 5;
	public $datatype			= 'string';
	public $tolang				= false;
	public $text_after			= "";
	public $text_before			= "";
	public $returnJS			= false;
	public $iconsource			= 'files';
	private $origID				= false;

	private $jq_options = array('height', 'width', 'preview_num', 'multiple', 'no_animation', 'header', 'filter', 'clickfunc', 'selectedtext', 'withmax', 'minselectvalue');
	private $out				= '';

	public function _construct() {
		if(empty($this->id)) $this->id = $this->cleanid($this->name);
	}

	public function output() {
		$dropdown = "";
		$this->out = '';

		if(strlen($this->text_before)) $dropdown = $this->text_before;
		$dropdown .= '<select name="'.$this->name.'" id="'.$this->id.'"';
		if(!empty($this->class)) $dropdown .= ' class="'.$this->class.'"';
		if($this->disabled) $dropdown .= ' disabled="disabled"';
		if(!empty($this->js)) $dropdown.= ' '.$this->js;
		$dropdown .= '>';
		if(!is_array($this->todisable)) $this->todisable = array($this->todisable);
		if($this->iconsource == 'eventicons'){
			$arrEvents		= $this->pdh->get('event', 'events');
			$arrIcons		= array();
			$arrIcons[0] = array(
				'name'	=> $this->user->lang('calendar_event_icon_none'),
				'icon'	=> '',
			);
			foreach($arrEvents as $eventid=>$eventdata){
				$arrIcons[$eventdata['icon']] = array(
					'name'	=> $eventdata['icon'],
					'icon'	=> $this->game->decorate('events', $eventid, array(), 0, true),
				);
			}
		}elseif($this->iconsource == 'files'){
			$arrIcons		= array();
			$arrIcons[0]	= array(
				'name'	=> $this->user->lang('calendar_event_icon_none'),
				'icon'	=> '',
			);
			$events_folder	= $this->pfh->FolderPath('event_icons', 'files');
			$link_eventsicon= $this->pfh->FolderPath('event_icons', 'files', 'absolute');
			$files			= sdir($events_folder);
			$arrImages		= array('png', 'jpeg', 'jpg', 'gif');
			foreach($files as $file) {
				$strExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
				if(!in_array($strExtension, $arrImages)) continue;
				$arrIcons[$file] = array(
					'name'	=> $file,
					'icon'	=> $link_eventsicon.'/'.$file,
				);
			}

			$events_folder = $this->root_path.'games/'.$this->config->get('default_game').'/icons/events';
			if (is_dir($events_folder)){
				$files = sdir($events_folder);
				foreach($files as $file) {
					$strExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
					if(!in_array($strExtension, $arrImages)) continue;
					$arrIcons[$file] = array(
						'name'	=> $file,
						'icon'	=> $this->env->buildlink().'games/'.$this->config->get('default_game').'/icons/events/'.$file,
					);
				}
			}
		}else{
			$arrIcons = $this->options;
		}

		if(is_array($arrIcons) && count($arrIcons) > 0){
			foreach ($arrIcons as $key => $value) {
				if($this->tolang) $value = ($this->user->lang($value['name'], false, false)) ? $this->user->lang($value['name']) : (($this->game->glang($value['name'])) ? $this->game->glang($value['name']) : $value['name']);
				$disabled = (($key === 0 && in_array($key, $this->todisable, true)) || ($key !== 0 && in_array($key, $this->todisable))) ? ' disabled="disabled"' : '';
				$selected_choice = (!empty($this->value) && ($this->value == 'all' || (is_array($this->value) && in_array($key, $this->value)) || (!is_array($this->value) && $this->value == $key))) ? 'selected="selected"' : '';
				$dropdown .= "<option data-image-src='".$value['icon']."' value='".$key."' ".$selected_choice.$disabled.">  ".$value['name']."</option>";
			}
		} else {
			$dropdown .= "<option value=''></option>";
		}
		$dropdown .= "</select>";
		$options = array('id' => $this->id);
		foreach($this->jq_options as $opt) $options[$opt] = $this->$opt;

		$options['multiple'] = false;
		$this->jquery->MultiSelect($this->name, array(), array(), $options, $this->returnJS);
		$jsout = ($this->returnJS) ? '<script>'.$this->jquery->get_jscode('multiselect', $this->id).'</script>' : '';
		if(strlen($this->text_after)) $dropdown .= $this->text_after;
		$this->out = $jsout.$dropdown;

		return $this->out;
	}

	public function _inpval() {
		return $this->in->getArray($this->name, $this->datatype);
	}
}
