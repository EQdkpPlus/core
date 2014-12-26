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

class form extends gen_class {
	
	/**
	 *	the form_id is the identifier of the form, it should be unique and has otherwise no specific use
	 */
	public $form_id = '';
	
	public $use_tabs 		= false;
	public $use_fieldsets 	= false;
	public $use_dependency 	= false;
	public $validate		= false;
	public $assign2tpl 		= true;
	public $ajax_url		= '';
	
	/**
	 *	the language variables are build as follows:
	 *	- for tabs: $this->lang_prefix.'tab_'.$tabname
	 *	- for fieldsets: 
	 *		- $lang = $this->lang_prefix.'fs_'.$fieldsetname;
	 *		- $info = $this->lang_prefix.'fs_info_'.$fieldsetname;
	 *	- for fields:
	 *		- $lang = $this->lang_prefix.'f_'.$name;
	 *		- $help = $this->lang_prefix.'f_help_'.$name;
	 */
	public $lang_prefix 	= '';
	
	/**
	 *	structure: tab => array(fieldset => array(field => array(options)), field => array(options), field => array(options))
	 * 
	 *	the options of each field is an array containing the following elements
	 * 		- type: the type of the field to use (e.g. 'dropdown')
	 *		- any additional options for the chosen fieldtype
	 *		- optionally the following entries can be included
	 *			- 'text' 		=> 'text to put in front of the field'
	 *			- 'text2'		=> 'text to put behind the field'
	 *			- 'encrypt'		=> whether to encrypt the data of the field (encrypt on read, decrypt on output)
	 *			- 'lang'		=> if a custom language variable shall be used for the field
	 *			- 'dir_lang'	=> if a custom (direct) string shall be used instead of a language variable
	 *			- 'help'		=> if a custom help-language variable shall be used
	 *			- 'dir_help'	=> if a custom (direct) string shall be used instead of a language variable
	 */
	private $field_array 	= array();
	private $hidden			= '';
	
	// flags if dependency jquery stuff has been initialised
	private $jq_dropdown 	= false;
	private $jq_checkbox 	= false;
	private $jq_radio 		= false;
	
	// error flag (to redisplay form in case of wrong input)
	private $error			= false;
	
	/**
	 *	shortcut to create a field
	 *
	 *	@param string	$name:		name of the field
	 *	@param array	$options:	any options for the field
	 *	@return object/string		returns the html-field as object, which can automatically transform into the appropriate html
	 */
	public static function field($name, &$options) {
		// encryption
		if(!empty($options['encrypt'])) $options['value'] = register('encrypt')->decrypt($options['value']);
		if(empty($options['type'])) $options['type'] = '';
		$field_class = 'h'.$options['type'];
		// additional text around field?
		$text = (empty($options['text'])) ? '' : $options['text'];
		$text2 = (empty($options['text2'])) ? '' : $options['text2'];
		$field = (registry::class_exists('h'.$options['type'])) ?  new $field_class($name, $options) : '';
		// add the correct id into the options-array
		if(is_object($field)) $options['id'] = $field->id;
		return $text.$field.$text2;
	}
	
	/**
	 *	shortcut for the return value of a field
	 *
	 *	@param string	$name:		name of the field
	 *	@param array	$options:	any options for the field
	 *	@return mixed				returns the input-value of the field
	 */
	public static function value($name, $options, $lang_prefix=false) {
		if(empty($options['type'])) $options['type'] = '';
		$class_name = 'h'.$options['type'];
		if(!registry::class_exists($class_name)) return null;
		$class = new $class_name($name, $options);
		
		// choose language var
		if(!isset($options['lang'])) {
			$lang = ($lang_prefix) ? $lang_prefix.'f_'.$name : $name;
		} else {
			$lang = $options['lang'];
		}
		// direct language string?
		if(!empty($options['dir_lang'])) {
			$language = $options['dir_lang'];
		} else {
			$language = (register('user')->lang($lang, false, false)) ? register('user')->lang($lang) : ((register('game')->glang($lang)) ? register('game')->glang($lang) : $lang);
		}
		$class->_lang = $language;
		
		if(!empty($options['encrypt'])) return register('encrypt')->encrypt($class->inpval());
		return $class->inpval();
	}
	
	public function __construct($form_id) {
		$this->form_id = $form_id;
	}
	
	public function __get($name) {
		switch($name) {
			case 'error':
				return $this->error;
				
			default:
				return parent::__get($name);
		}
	}
	
	public function reset_fields() {
		$this->field_array = array();
	}
	
	/**
	 *	add a tab and put existing fieldsets and fields into it
	 *
	 *	@param string/array	$tab:		name of the tab, according to language variable "{lang_prefix}tab_{name}" OR array('name' => $name, 'lang' => $lang)
	 *	@param array		$fieldsets:	array with names of the fieldsets which shall be moved to this tab (from ungrouped)
	 *	@param array		$fields:	array with names of the fields which shall be moved to this tab (from ungrouped)
	 */
	public function add_tab($tab, $fieldsets=array(), $fields=array()) {
		if(is_array($tab)) {
			$tabname = $tab['name'];
			$this->field_array[$tabname]['_lang'] = $tab['lang'];
		} else {
			$tabname = $tab;
		}
		foreach($fieldsets as $fieldset) {
			if(isset($this->field_array[$fieldset])) {
				$this->field_array[$tabname][$fieldset] = $this->field_array['fs'][$fieldset];
				unset($this->field_array['fs'][$field]);
			}
		}
		foreach($fields as $field) {
			if(isset($this->field_array[$field])) {
				$this->field_array[$tabname]['f'][$field] = $this->field_array['f'][$field];
				unset($this->field_array['f'][$field]);
			}
		}
	}
	
	/**
	 *	add multiple tabs at once
	 *
	 *	@param array	$fieldarray:	 tabname => array(fieldsetname => array(fieldname => array(options)))
	 */
	public function add_tabs($fieldarray) {
		$this->field_array = array_merge($this->field_array, $fieldarray);
	}
	
	/**
	 *	group existing fields in a fieldset
	 *
	 *	@param string/array	$fieldset:	name of the fieldset, according to language variable "{lang_prefix}fs_{name}" OR array('name' => $name, 'lang' => $lang. (optional) 'info' => $info)
	 *	@param array		$fields:	array with names of the fields to be moved
	 *	@param string 		$tab:		name of the tab in which the fieldset shall be created (and the fields are currently located in)
	 */
	public function add_fieldset($fieldset, $fields=array(), $tab='') {
		if(is_array($fieldset)) {
			$fsname = $fieldset['name'];
			$this->field_array[$fsname]['_lang'] = $fieldset['lang'];
			if(!empty($fieldset['info'])) $this->field_array[$fsname]['_info'] = $fieldset['info'];
		} else {
			$fsname = $fieldset;
		}
		foreach($fields as $field) {
			if($tab) {
				$this->field_array[$tab][$fsname][$field] = $this->field_array[$tab]['f'][$field];
				unset($this->field_array[$tab]['f'][$field]);
			} else {
				$this->field_array['fs'][$fsname][$field] = $this->field_array['f'][$field];
				unset($this->field_array['f'][$field]);
			}
		}
	}
	
	/**
	 *	add one or more fieldsets including its fields in array format
	 *
	 *	@param array	$fieldarray:	fieldsetname => array(fieldname => array(options))
	 *	@param string	$tab:			name of the tab into which the fieldset shall be put
	 */
	public function add_fieldsets($fieldarray, $tab='') {
		if($tab)
			$this->field_array[$tab] = array_merge($this->field_array[$tab], $fieldarray);
		else
			$this->field_array['fs'] = array_merge($this->field_array, $fieldarray);
	}
	
	/**
	 *	add a single field to the form
	 *
	 *	@param string	$name:		name of the field, according to language variable "{lang_prefix}{name}"
	 *	@param array	$options:	argument-array for the field
	 *	@param string	$fieldset:	name of the fieldset where the field shall be placed
	 *	@param string	$tab:		name of the tab where the field shall be placed
	 */
	public function add_field($name, $options, $fieldset='', $tab='') {
		if($tab && $fieldset)
			$this->field_array[$tab][$fieldset][$name] = $options;
		elseif($tab)
			$this->field_array[$tab]['f'][$name] = $options;
		elseif($fieldset)
			$this->field_array['fs'][$fieldset][$name] = $options;
		else
			$this->field_array['f'][$name] = $options;
	}
	
	/*	add multiple fields in array format
	 *	@fieldarray (array):	fieldname => array(options)
	 *	@fieldset (string):		name of the fieldset where the fields shall be placed
	 *	@tab (string):			name of the tab where the fields shall be placed
	 */
	public function add_fields($fieldarray, $fieldset='', $tab='') {
		if($tab && $fieldset) {
			if(empty($this->field_array[$tab][$fieldset])) $this->field_array[$tab][$fieldset] = $fieldarray;
			else $this->field_array[$tab][$fieldset] = array_merge($this->field_array[$tab][$fieldset], $fieldarray);
		} elseif($tab) {
			if(empty($this->field_array[$tab]['f'])) $this->field_array[$tab]['f'] = $fieldarray;
			else $this->field_array[$tab]['f'] = array_merge($this->field_array[$tab], $fieldarray);
		} elseif($fieldset) {
			if(empty($this->field_array['fs'][$fieldset])) $this->field_array['fs'][$fieldset] = $fieldarray;
			else $this->field_array['fs'][$fieldset] = array_merge($this->field_array[$fieldset], $fieldarray);
		} else {
			if(empty($this->field_array['f'])) $this->field_array['f'] = $fieldarray;
			else $this->field_array['f'] = array_merge($this->field_array['f'], $fieldarray);
		}
	}
	
	/*	assign output to template variables
	 *	@values (array):	key => value, array containing the values with which to fill the formular
	 */
	public function output($values=array()) {
		$out = array();
		if($this->use_tabs) {
			if($this->assign2tpl) $this->jquery->Tab_header($this->lang_prefix.'tabs', true);
			foreach($this->field_array as $tabname => $tabdata) {
				if(strpos($tabname, '_') === 0) continue;
				if($this->assign2tpl) $this->tab2tpl($tabname, $tabdata);
				if($this->use_fieldsets) {
					foreach($tabdata as $fieldsetname => $fieldsetdata) {
						if(strpos($fieldsetname, '_') === 0) continue;
						if($this->assign2tpl) $this->fs2tpl($fieldsetname, $fieldsetdata, 'tabs.fieldsets');
						foreach($fieldsetdata as $name => $options) {
							if(!isset($values[$name])) $values[$name] = '';
							$out[$tabname][$fieldsetname][$name] = $this->f2tpl($name, $options, 'tabs.fieldsets.fields', $values[$name]);
						}
					}
				} else {
					foreach($tabdata['f'] as $name => $options) {
						if(!isset($values[$name])) $values[$name] = '';
						$out[$tabname][$name] = $this->f2tpl($name, $options, 'tabs.fields', $values[$name]);
					}
				}
			}
		} else {
			if($this->use_fieldsets) {
				foreach($this->field_array['fs'] as $fieldsetname => $fieldsetdata) {
					if(strpos($fieldsetname, '_') === 0) continue;
					if($this->assign2tpl) $this->fs2tpl($fieldsetname, $fieldsetdata, 'fieldsets');
					foreach($fieldsetdata as $name => $options) {
						if(!isset($values[$name])) $values[$name] = '';
						$out[$fieldsetname][$name] = $this->f2tpl($name, $options, 'fieldsets.fields', $values[$name]);
					}
				}
			} else {
				foreach($this->field_array['f'] as $name => $options) {
					if(!isset($values[$name])) $values[$name] = '';
					$out[$name] = $this->f2tpl($name, $options, 'fields', $values[$name]);
				}
			}
		}
		// initialise form validate
		if($this->validate) {
			$this->jquery->init_formvalidation();
			$this->form_class .= ' fv_checkit';
		}

		if($this->assign2tpl) 
			$this->tpl->assign_vars(array(
				'FORM_ID'	=> $this->form_id,
				'HIDDEN'	=> $this->hidden,
				'FORMCLASS'	=> $this->form_class,
			));
		else return $out;
	}
	
	/*	read input data according to form-fields
	 *	@return (array):	inputname => value
	 */
	public function return_values() {
		$values = array();
		foreach($this->field_array as $tabname => $fieldsets) {
			if(strpos($tabname, '_') === 0) continue;
			// extra handling for the only case of a 2-deep array
			if($tabname == 'f') {
				// variable fieldsets holds fields in this case
				foreach($fieldsets as $name => $options) {
					try {
						$values[$name] = self::value($name, $options, $this->lang_prefix);
					} catch (FormException $e) {
						$this->error = true;
						$this->core->message($e->getMessage(), $this->user->lang('fv_form_error'), 'red');
						$values[$name] = '';
					}
				}
				continue;
			}
			foreach($fieldsets as $fieldsetname => $fields) {
				if(strpos($fieldsetname, '_') === 0) continue;
				foreach($fields as $name => $options) {
					if (!is_array($options)) continue;
					
					try {
						$values[$name] = self::value($name, $options, $this->lang_prefix);
					} catch (FormException $e) {
						$this->error = true;
						$this->core->message($e->getMessage(), $this->user->lang('fv_form_error'), 'red');
						$values[$name] = '';
					}
				}
			}
		}
		return $values;
	}
	
	private function tab2tpl($tabname, $data) {
		$lang = (!empty($data['_lang'])) ? $data['_lang'] : $this->lang_prefix.'tab_'.$tabname;
		$this->tpl->assign_block_vars('tabs', array(
			'NAME'	=> $this->lang($lang),
			'ID'	=> $tabname
			)
		);
	}
	
	private function fs2tpl($fieldsetname, $data, $key) {
		$lang = (!empty($data['_lang'])) ? $data['_lang'] : $this->lang_prefix.'fs_'.$fieldsetname;
		$info = (!empty($data['_info'])) ? $data['_info'] : $this->lang_prefix.'fs_info_'.$fieldsetname;
		$this->tpl->assign_block_vars($key, array(
			'NAME'		=> $this->lang($lang),
			'INFO'		=> $this->lang($info, false),
			'ID'		=> 'fs_'.substr(md5($fieldsetname), 0, 10),
		));
	}
	
	private function f2tpl($name, $options, $key, $value) {
		// TODO: check 'disabled'
		
		if (!is_array($options)) return;
		
		// choose language var
		if(!isset($options['lang'])) {
			$lang = $this->lang_prefix.'f_'.$name;
			$help = $this->lang_prefix.'f_help_'.$name;
		} else {
			$lang = $options['lang'];
			if(!isset($options['help'])) $help = $lang.'_help';
		}
		if(isset($options['help'])) $help = $options['help'];
		// direct language string?
		if(!empty($options['dir_lang'])) {
			$language = $options['dir_lang'];
		} else {
			$language = $this->lang($lang);
		}
		// direct help string?
		if(!empty($options['dir_help'])) {
			$help_message = $options['dir_help'];
		} else {
			$help_message = $this->lang($help, false);
		}
		
		// fill in the field

		if(!empty($value) || $value === '0' || $value === 0) $options['value'] = $value;
		
		// create the field
		$field = self::field($name, $options);
		
		// dependency stuff - hide other elements depening on selection
		if(!empty($options['dependency'])) $this->jq_dep_init($options['type']);
		
		// ajax-reload for dropdown-options
		if(!empty($options['ajax_reload'])) {
			if(isset($options['ajax_reload']['multiple'])) {
				$ajax_reload = $options['ajax_reload']['multiple'];
			} else {
				$ajax_reload = array($options['ajax_reload']);
			}
			foreach($ajax_reload as $ajre) {
				if(strpos($ajre[1], '%URL%') !== false) {
					$ajre[1] = str_replace('%URL%', $this->ajax_url, $ajre[1]);
				}
				$this->jquery->js_dd_ajax($options['id'], $ajre[0], $ajre[1], (isset($ajre[2]) ? $ajre[2] : ''));
			}
		}
		
		if($this->assign2tpl) {
			if($options['type'] == 'hidden') {
				$this->hidden .= $field;
			} else {
				$this->tpl->assign_block_vars($key, array(
					'NAME'		=> $language,
					'HELP'		=> $help_message,
					'FIELD'		=> $field,
					'ID'		=> 'f_'.substr(md5($name), 0, 10),
					'S_REQUIRED'=> (isset($options['required']) && $options['required']) ? true : false,
				));
			}
		} else return array(
				'name'		=> $language,
				'help'		=> $help_message,
				'field'		=> $field,
				'type'		=> $options['type'],
			);
	}
	
	private function jq_dep_init($type) {
		if($this->{'jq_'.$type}) return;
		switch($type) {
			case 'dropdown':
				$js = "
	$('.form_change').change(function(){
		$.each($(this).find('option'), function(){
			var selected = this.selected;
			$.each($(this).data('form-change').split(','), function(index, value){
				if(value){
					if(selected){
						$('#".$this->form_id."').find('#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"]').removeAttr('disabled');
						$('#".$this->form_id."').find('dl:has(#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"])').show();
					}else{
						$('#".$this->form_id."').find('#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"]').attr('disabled', 'disabled');
						$('#".$this->form_id."').find('dl:has(#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"])').hide();
					}
				}
			});
		});
	}).trigger('change');";
				break;
				
			case 'checkbox':
			case 'radio':
				$js = "
	$('.form_change_checkbox, .form_change_radio').change(function(){
		$.each($('.form_change_checkbox > input, .form_change_radio > input'), function(){
			var checked = this.checked;
			$.each($(this).data('form-change').split(','), function(index, value){
				if(value){
					if(checked){
						$('#".$this->form_id."').find('#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"]').removeAttr('disabled');
						$('#".$this->form_id."').find('dl:has(#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"])').show();
					}else{
						$('#".$this->form_id."').find('#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"]').attr('disabled', 'disabled');
						$('#".$this->form_id."').find('dl:has(#'+value+',input[name=\"'+value+'\"],select[name=\"'+value+'\"],textarea[name=\"'+value+'\"])').hide();
					}
				}
			});
		});
	}).trigger('change');";
				break;
				
			default: return;
		}
		$this->{'jq_'.$type} = true;
		$this->tpl->add_js($js, 'docready');
	}
	
	private function lang($lang, $no_empty=true) {
		$fallback = ($no_empty) ? $lang : '';
		return ($this->user->lang($lang, false, false)) ? $this->user->lang($lang) : (($this->game->glang($lang)) ? $this->game->glang($lang) : $fallback);
	}
}

class FormException extends Exception {}
?>