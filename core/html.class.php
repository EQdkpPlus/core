<?php
 /*
 * Project:		eqdkpPLUS Libraries: myHTML
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:myHTML
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("html")) {
	class html extends gen_class {
		public static $shortcuts = array('user', 'jquery', 'pm', 'in', 'game', 'time', 'editor'=>'tinyMCE', 'crypt'=>'encrypt', 'tpl');
		
		private $blnDepJS = false;

		/**
		* Generate Widget
		* 
		* @param $options	Options for the fields
		*/	
		public function widget($options = array()){
			//backward compatibility
			$type = (isset($options['fieldtype'])) ? $options['fieldtype'] : ((isset($options['type'])) ? $options['type'] : '');
			if(!isset($options['value'])) {
				if(isset($options['default'])) $options['value'] = $options['default'];
				elseif($type == 'int') $options['value'] = '0';
			}
			$options['javascript']	= (isset($options['javascript'])) ? $options['javascript'] : '';
			
			//dependencies
			if (isset($options['dependency'])){
				if (!$this->blnDepJS){
					
					$this->tpl->add_js(
						'function dep_check_value(name, target, value){
							if ($("#"+target).val() == value){
								$("input[name="+name+"],textarea[name="+name+"],select[name="+name+"]").removeAttr("disabled");
							} else {
								$("input[name="+name+"],textarea[name="+name+"],select[name="+name+"]").attr("disabled", "disabled");
							}
						}
						
						function dep_check_cb(name, target){
							if ($("#"+target).attr("checked") == "checked"){
								$("input[name="+name+"],textarea[name="+name+"],select[name="+name+"]").removeAttr("disabled");
							} else {
								$("input[name="+name+"],textarea[name="+name+"],select[name="+name+"]").attr("disabled", "disabled");
							}
						}
						
						'	
					, 'docready');
										
					$this->blnDepJS = true;
				}
			
			
				if (is_array($options['dependency'])){
					//It's a dropdown
					$this->tpl->add_js('
					$(document).on("change", "#'.$options['dependency'][0].'", function () {
							dep_check_value("'.$options['name'].'", "'.$options['dependency'][0].'", "'.$options['dependency'][1].'");
						});	
						dep_check_value("'.$options['name'].'", "'.$options['dependency'][0].'", "'.$options['dependency'][1].'");
					', 'docready');
					
					
				} else {
					//It's a checkbox
					$this->tpl->add_js('$(document).on("change", "#'.$options['dependency'].'", function () {
							dep_check_cb("'.$options['name'].'", "'.$options['dependency'].'");
						});		
						dep_check_cb("'.$options['name'].'", "'.$options['dependency'].'");
					', 'docready');
				}
			}
			
			//clean id
			if(empty($options['id'])) $options['id'] = $this->cleanid($options['name']);

			switch ($type){
				//Callback-Function
				case 'function':
					$plugin		= $options['plugin'];
					$objPlugin	= $this->pm->get_plugin($plugin);
					$function	= $options['function'];
					$ccfield	= $objPlugin->$function($options, false);
				break;
				
				//Plaintext
				case 'plaintext':
					$ccfield = '';
				break;

				// Checkboxes
				case 'checkbox':
					$ccfield = $this->CheckBox($options['name'], ((isset($options['text'])) ? $options['text'] : ''), ((isset($options['selected'])) ? $options['selected'] : ''), ((isset($options['value'])) ? $options['value'] : ''), ((isset($options['class'])) ? $options['class'] : ''), $options['javascript'], ((isset($options['disabled'])) ? true : false));
				break;

				// Checkbox
				case 'boolean':
					$ccfield = $this->CheckBox(@$options['name'], $this->is_set($options['text']), (isset($options['value'])) ? '1' : false, '', '', $options['javascript']);
				break;

				//Spinner
				case 'spinner':
					if(!isset($options['id'])) $options['id'] = 'spin_'.uniqid();
					$this->jquery->spinner($options['id'], $options);
					$options['class'] = (isset($options['class'])) ? $options['class'] : '';

				// Text
				case 'text':
				case 'int':
					$readonly = (isset($options['readonly'])) ? $options['readonly'] : false;
					$encrypt = (isset($options['encrypt'])) ? true : false;
					$ccfield = $this->TextField(@$options['name'], ((isset($options['size'])) ? $options['size'] : ''), ((isset($options['value'])) ? $options['value'] : ''), 'text', ((isset($options['id'])) ? $options['id'] : ''), ((isset($options['class'])) ? $options['class'] : 'input'), $readonly, $encrypt, $options['javascript']);
				break;
				

				// Dropdown
				case 'dropdown':
					// make it translatable...
					$tmpdrdwnrry = array();
					foreach($options['options'] as $ddid=>$ddname){
						$tmplangdd = '';
						if(isset($ddname) && !is_array($ddname) && !isset($options['no_lang'])){
							$tmplangdd = ($this->user->lang($ddname)) ? $this->user->lang($ddname) : $ddname;
						}else{
							$tmplangdd = $ddname;
						}
						$tmpdrdwnrry[$ddid] = ($tmplangdd) ? $tmplangdd : $ddname;	
					}
					$options['readonly']	= (isset($options['readonly'])) ? $options['readonly'] : false;
					$options['id']			= (isset($options['id'])) ? $options['id'] : '';
					if(!isset($options['selected']) && isset($options['value'])) $options['selected'] = $options['value'];
					$ccfield = $this->DropDown($options['name'], $tmpdrdwnrry, $options['selected'], '', $options['javascript'], 'input', $options['id'],array(), $options['readonly']);
				break;

				case 'ajax_dropdown':
					$ccfield = '';
				break;

				// Autocomplete
				case 'autocomplete':
					$this->jquery->Autocomplete('id'.$options['name'], $options['options']);
					$ccfield = $this->TextField($options['name'], $options['size'], $options['selected'], 'text', 'id'.$options['name']);
				break;

				// Slider
				case 'slider':
					$ccfield = $this->jquery->Slider($options['name'], array('label' => $options['label'], 'values' => $options['value'], 'min'=>$options['min'], 'max'=>$options['max'], 'width'=> $options['width']), (($options['format']) ? $options['format'] : 'range'));
				break;

				// Timepicler
				case 'timepicker':
					if(!isset($options['sec'])) $options['sec'] = 0;
					if(!isset($options['enable_sec'])) $options['enable_sec'] = false;
					$ccfield = $this->jquery->timePicker($options['name'], $options['value'], $options['hour'], $options['min'], $options['sec'], $options['enable_sec'], (isset($options['hour_format'])) ? $options['hour_format'] : '24');
				break;
				
				case 'colorpicker':
					$ccfield = $this->jquery->colorpicker($options['name'], $options['value']);
				break;

				// Datepicker
				case 'datepicker':
					$options['options']['readonly'] = (isset($options['readonly'])) ? $options['readonly'] : false;
					if(is_numeric($options['value'])) $options['value'] = $this->time->date($this->time->calendarformat($options['options']), $options['value']);
					if(isset($options['options']['format'])) $options['options']['format'] = $this->time->translateformat2js($options['options']['format']);
					if(isset($options['options']['timeformat'])) $options['options']['timeformat'] = $this->time->translateformat2js($options['options']['timeformat']);
					$ccfield = $this->jquery->Calendar($options['name'], $options['value'], '', ((isset($options['options'])) ? $options['options'] : array()));
				break;

				// Hidden Field
				case 'hidden':
					$ccfield = $this->TextField($options['name'], ((isset($options['size'])) ? $options['size'] : ''), $options['value'], 'hidden');
				break;

				// Password field
				case 'password':
					$encrypt = (isset($options['encrypt'])) ? true : false;
					$readonly = (isset($options['readonly'])) ? $options['readonly'] : false;
					$ccfield = $this->TextField($options['name'], $options['size'], $options['value'], 'password', $options['id'], 'input', $readonly, $encrypt, $options['javascript']);
				break;

				// jQuery Multiselect
				case 'jq_multiselect':
					$tmpdrdwnrry = array();
					foreach($options['options'] as $ddid=>$ddname){
						$tmplangdd = $ddname;
						if(isset($ddname) && !is_array($ddname) && !isset($options['no_lang'])){
							$tmplangdd = $this->user->lang($ddname, true, false);
						}
						$tmpdrdwnrry[$ddid] = $tmplangdd;	
					}
					if(empty($options['selected'])) $options['selected'] = array();
					$ccfield = $this->jquery->MultiSelect($options['name'], $tmpdrdwnrry, $options['selected'], $options);
				break;

				// HTML MultiSelect
				case 'multiselect':
					// make it translatable...
					$tmpdrdwnrry = array();
					foreach($options['options'] as $ddid=>$ddname){
						$tmplangdd = '';
						if(isset($ddname) && !is_array($ddname)){
							$tmplangdd = ($this->user->lang($ddname)) ? $this->user->lang($ddname) : $ddname;
						}else{
							$tmplangdd = $ddname;
						}
						$tmpdrdwnrry[$ddid] = ($tmplangdd) ? $tmplangdd : $ddname;	
					}
					$ccfield = $this->MultiSelect($options['name'], $tmpdrdwnrry, $options['selected'], '', '', 8);
				break;

				//Textarea
				case 'textarea':
					$encrypt = (isset($options['encrypt'])) ? true : false;
					$ccfield = $this->TextArea($options['name'],  $options['rows'], $options['cols'],$options['value'], false, 'input', $encrypt);
				break;

				//BBCode-Editor
				case 'bbcodeeditor':
					$ccfield = $this->TextArea($options['name'], $options['rows'], $options['cols'], $options['value'], $options['name'].'_bbcode', 'mceEditor_bbcode');
					$this->editor->editor_bbcode();
				break;

				// Radio Button
				case 'radio':
					$ccfield = $this->RadioBox($options['name'], $options['options'], ((isset($options['selected'])) ? $options['selected'] : ''));
				break;

				// Direct Output
				case 'direct':
					$ccfield = $options['direct'];
				break;
				
				case 'imageuploader':
					$fileuploadid	= 'file_imageupl_'.$this->cleanid($options['name']);
					$imgvalue		= (isset($options['value'])) ? $options['value'] : '';
					$ccfield = $this->TextField($options['name'], 10, $imgvalue, $type = 'hidden', $fileuploadid).$this->jquery->imageUploader('imageupl_'.$this->cleanid($options['name']), $fileuploadid, $imgvalue, $options['imgpath'], $options['options']);
				break;

				default: $ccfield = '';
			}
			return $ccfield;
		}

		public function widget_return($options = array()){
			//backward compatibility
			$type = (isset($options['fieldtype'])) ? $options['fieldtype'] : $options['type'];

			if (isset($options['default'])){
				return $this->in->get($options['name'], $options['default']);
			}

			switch ($type){
				//Callback-Function
				case 'function':
					$plugin = $options['plugin'];
					$objPlugin = $this->pm->get_plugin($plugin);
					$function = $options['function'];
					//maybe error-catching needed if $objPlugin is no object!
					return (is_object($objPlugin)) ? $objPlugin->$function($in->get($options['name']), true) : NULL;
				
				case 'int':
				case 'checkbox':
				case 'spinner':
					return $this->in->get($options['name'], 0);

				case 'boolean':
					return (($this->in->get($options['name'], 0) == '1') ? true : false);

				case 'datepicker':
					return $this->time->fromformat($this->in->get($options['name'], ''), $this->time->calendarformat($options['options']));
					
				case 'text':
				case 'textarea':
				case 'bbcodeeditor':
				case 'dropdown':
				case 'autocomplete':
				case 'hidden':
				case 'password':
				case 'radio':
				case 'timepicker':
				case 'colorpicker':
					$value = $this->in->get($options['name'], '', ((isset($options['codeinput']) && $options['codeinput']) ? 'raw' : ''));
					if (isset($options['encrypt'])){
						$value = $this->crypt->encrypt($value);
					}
					return $value;

				case 'sortable':
				case 'multiselect':
				case 'jq_multiselect':
					$in_array = $this->in->getArray($options['name'], 'string');
					if (is_array($in_array)){
						return serialize($in_array);
					} else {
						return array();
					}
				
				case 'imageuploader':
					return $this->jquery->MoveUploadedImage($this->in->get($options['name'], ''), $options['imgpath']);			
			}
		}

		private function cleanid($input){
			if(strpos($input, '[') === false && strpos($input, ']') === false) return $input;
			$out = str_replace(array('[', ']'), array('_', ''), $input);
			return 'clid_'.$out;
		}

		public function generateField($confvars, $name, $value, $no_lang=false){
			$confvars['name']		= $name;
			$confvars['value']		= $confvars['selected'] = (isset($confvars['serialized'])) ? unserialize($value) : $value;

			if(isset($confvars['fieldtype'])){
				switch($confvars['fieldtype']){
					// Dropdown - load glang for dropdown-options
					case 'dropdown':
					case 'radio':
					case 'multiselect':
						if($no_lang) break;
						$drpdownfields = array();
						foreach($confvars['options'] as $namee=>$valuee){
							if($valuee AND is_string($valuee)) {
								$drpdownfields[$namee]	= ($this->user->lang($valuee, false, false)) ? $this->user->lang($valuee) : (($this->game->glang($valuee, false, false, true)) ? $this->game->glang($valuee) : $valuee);
							} else {
								$drpdownfields[$namee] = $valuee;
							}
						}
						$confvars['options'] = $drpdownfields;
						$confvars['no_lang'] = true; //we only need to translate things once
					break;
				}
			}
			
			return $this->widget($confvars);
		}

		private function is_set($ddd){
			return (isset($ddd)) ? $ddd : '';
		}

		/**
		* DropDown
		* 
		* @param $name				fieldname
		* @param $list				Array with dropdown items
		* @param $selected			selected, true or not
		* @param $text				Label of the Dropdown
		* @param $javascr			custom additions, such as onclick...
		* @param $class				css class
		* @param $customid			ID of field
		* @param $todisable			array with keys to disable (readonly)
		* @param $disable_select	disable whole select
		* @return Dropdown
		*/
		public function DropDown($name, $list, $selected, $text='', $javascr = '', $class = 'input', $customid='', $todisable=array(), $disable_select=false){
			$dropdown = ( $text != '' ) ?  $text.": " : '';
			$dropdown  .= "<select size='1' ".$javascr." name='".$name."' ".(($customid) ? "id='".$customid."'" : "id='".$this->cleanid($name)."'")." class='".$class."' ".(($disable_select) ? 'disabled="disabled"' : '').">";
			$todisable = (is_array($todisable)) ? $todisable : array($todisable);
			if(is_array($list) && count($list) > 0){
				foreach ($list as $key => $value) {
					if(is_array($value)){
						$dropdown .= "<optgroup label='".$key."'>";
						foreach ($value as $key2 => $value2) {
							$selected_choice = ($key2 == $selected) ? ' selected="selected"' : '';
							$disabled = (isset($todisable[$key]) && is_array($todisable[$key]) && ($key === 0 && in_array($key, $todisable, true)) || ($key !== 0 && in_array($key, $todisable))) ? ' disabled="disabled"' : '';
							$dropdown .= "<option value='".$key2."'".$selected_choice.$disabled.">".$value2."</option>";
						}
						$dropdown .= "</optgroup>";
					}else{
						$disabled = (($key === 0 && in_array($key, $todisable, true)) || ($key !== 0 && in_array($key, $todisable))) ? ' disabled="disabled"' : '';
						$selected_choice = (($key == $selected)) ? 'selected="selected"' : '';
						$dropdown .= "<option value='".$key."' ".$selected_choice.$disabled.">".$value."</option>";
					}
				}
			}else{
				$dropdown .= "<option value=''></option>";
			}
			$dropdown .= "</select>";
			return $dropdown;
			
		}

		/**
		* Multiselect
		* 
		* @param $name			fieldname
		* @param $list			Array with dropdown items
		* @param $selected		selected, true or not
		* @param $text			Label of the Dropdown
		* @param $javascr		custom additions, such as onclick...
		* @param $class			css class
		* @param $size			Size of the Multiselect
		* @return Dropdown
		*/
		public function MultiSelect($name, $list, $selected, $javascr = '', $class = '', $size=4){
			$rclass	= ($class) ? "class='".$class."'" : "";
			$dropdown = "<select size='".$size."' ".$javascr." name='".$name."[]' ".$rclass." multiple='multiple'>";
			$selected = (is_array($selected)) ? $selected : explode("|", $selected);
			if($list){
				foreach ($list as $key => $value) {
					$selected_choice = (in_array($key, $selected)) ? 'selected="selected"' : '';
					$dropdown .= "<option value='".$key."' ".$selected_choice.">".$value."</option>";
				}
			}
			$dropdown .= "</select>";
			return $dropdown;
		}

		/**
		* Radio Box
		* 
		* @param $name			fieldname
		* @param $list			Array with dropdown items
		* @param $selected		selected, true or not
		* @param $class			css class
		* @return Dropdown
		*/
		public function RadioBox($name, $list, $selected, $class = 'input'){
			$radiobox  = '';
			if(!is_array($list)){
				$list = array (
					'0'   => $this->user->lang('cl_off'),
					'1'   => $this->user->lang('cl_on')
				);
			}
			foreach ($list as $key => $value) {
				$selected_choice = ((string)$key == (string)$selected) ? 'checked="checked"' : '';
				$radiobox .='<label><input type="radio" name="'.$name.'" value="'.$key.'" '.$selected_choice.' class="'.$class.'"/>'.$value.'</label>&nbsp;';
			}
			return $radiobox;
		}

		/**
		* CheckBox
		* 
		* @param $name			fieldname
		* @param $langname		Field Label
		* @param $options		Checked or not
		* @param $help			Help + Text
		* @param $value			Value of checkbox
		* @param $notable		Table or not?
		* @return Dropdown
		*/
		public function CheckBox($name, $langname, $options, $value='1', $mclass='', $js='', $disabled=false, $id=''){
			$is_checked		= ( $options == '1' ) ? 'checked="checked"' : '';
			$is_disabled	= ($disabled) ? 'disabled="disabled"' : '';
			$is_class		= ($mclass) ? " class='".$mclass."'" : '';
			if(empty($id)) $id = $this->cleanid($name);
			return "<input type='checkbox' name='".$name."' id='".$id."' value='".(($value) ? $value : 1)."' ".$is_checked.$is_disabled.$is_class." ".$js." /> ".(($langname) ? $langname : '');
		}
		
		/**
		* Return if a checkbox is checked
		* 
		* @param $value			the value to be checked
		* @return checked or not
		*/
		public function isChecked($value){
			return ( $value == '1' ) ? ' checked="checked"' : '';
		}

		/**
		* Text Area
		* 
		* @param $name			fieldname
		* @param $rows			Text Area Rows
		* @param $cols			Text Area Cols
		* @param $value			Value of checkbox
		* @param $test			Field Lable
		* @param $help			Help Tooltip Text
		* @param $type			Type of the Text
		* @param $notable		Table or not?
		* @return TextArea
		*/
		public function TextArea($name, $rows, $cols, $value = '', $id=false, $class='input', $encrypt=false, $javascript = ''){
			if ($encrypt){
				$value = $this->crypt->decrypt($value);
			}
			return " <textarea name='".$name."' rows='".$rows."' cols='".$cols."' class='".$class."' id='".(($id) ? $id : $name)."' ".$javascript.">".$value."</textarea>";
		}

		/**
		* Text Field
		* 
		* @param $name			fieldname
		* @param $size			Size
		* @param $value			Value of checkbox
		* @param $text			Field Lable
		* @param $help			Help Tooltip Text
		* @param $type			Type of the Text
		* @param $notable		Table or not?
		* @param $id			Field ID
		* @return TextField
		*/
		public function TextField($name, $size, $value = '', $type = 'text', $id='', $class='input', $readonly = false, $encrypt = false, $javascript = ''){
			$myID = ($id) ? ' id="'.$id.'"' : '';
			$myreadonly = ($readonly) ? ' readonly="readonly"' : '';
			if ($encrypt){
				$value = $this->crypt->decrypt($value);
			}
			return " <input type='".$type."' name='".$name."' size='".$size."' value=\"".$value."\" class='".$class."' ".$myID.$myreadonly." ".$javascript." />";
		}

		public function ToolTip($content, $wraptext, $title='', $options = array()){
			if (!isset($options['contfunc'])) { $options['contfunc'] = true; }
			$name = (!isset($options['name'])) ? 'eqdkpttip' : $options['name'];
			$this->jquery->qtip('.'.$name, 'return $(".'.$name.'_c", this).html();', $options);
			if(isset($options['usediv']) && $options['usediv']){
				return '<div class="'.$name.'"><div class="'.$name.'_c" style="display:none;">'.$content.'</div>'.$wraptext.'</div>';
			}else{
				return '<span class="'.$name.'"><span class="'.$name.'_c" style="display:none;">'.$content.'</span>'.$wraptext.'</span>';
			}
			return ;
		}

		public function bar($value, $max, $widht, $text, $color=false){
			if(strpos($widht, '%') !== false && strpos($widht, 'px')) $widht .= 'px';
			if ($color){
				$html = "<div class='plus_bar_$color' style='width: ".$widht.";'><p>$max</p></div>";
			}else{
				$html = "<div class='plus_bar-container' style='width: ".$widht.";'>
						<b style='width: ".round((($max > 0) ? ($value / $max) * 100 : 0))."%;'></b>
						<span style='width: ".$widht."px;'>".$text." ".$value."/".$max."</span>
					</div>";
			}
			return $html;
		}

		public function bars($a_values, $width){
			if(isset($a_values) && is_array($a_values)){
				$out = '<div class="plus_multibar" style="width: '.$width.'px;">';
				foreach ($a_values as $bar){
					$out .= '<div class="plus_multibar-container" style="width: '.$width.'px;">
								<b style="width: '.round((($bar['max'] > 0) ? ($bar['value'] / $bar['max']) * 100 : 0)).'%;"></b>
								<span style="width: '.$width.'px;">'.((isset($bar['text']) && $bar['text']) ? $bar['text'] : '').' '.$bar['value'].'/'.$bar['max'].'</span>
							</div>';
				}
				$out .= '</div>';
				return $out;
			}
		}

		/**
		* Toggle Icons
		* 
		* @param $value			on or off?
		* @param $icon_on		image for on
		* @param $icon_off		image for off
		* @param $path			path for images
		* @param $iconAltText	alt text for the images
		* @param $url			url
		* @return Tooltip
		*/
		public function toggleIcons($value, $icon_on, $icon_off, $path, $iconAltText='', $url='', $notitle=false){
			$mytitle = ($notitle) ? ' title="'.$iconAltText.'"' : '';
			$icon = (!empty($value) || $value === true)? $icon_on : $icon_off;
			$ret_val =	'<img src="'.$this->root_path.$path.$icon.'" alt="'.$iconAltText.$mytitle.'" />';

			if (!empty($url)){
				$ret_val = '<a href="'.$url.'">'.$ret_val.'</a>';
			}
			return $ret_val;
		}
	}
} 
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_html', html::$shortcuts);
?>