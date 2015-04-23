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

if (!class_exists("jquery")) {
	class jquery extends gen_class {
		public static $shortcuts = array('puf'=> 'urlfetcher');

		private $tt_init				= '';
		private $ce_loaded				= false;
		private $language_set			= array();
		private $dyndd_counter			= 0;
		private $file_browser			= array();
		private $inits					= array(
			'colorpicker'		=> false,
			'starrating'		=> false,
			'formvalidation'	=> false,
			'fullcalendar'		=> false,
			'jqplot'			=> false,
			'spinner'			=> false,
			'multilang'			=> false,
		);
		
		/**
		* Construct of the jquery class
		*/
		public function __construct(){
			$this->path			= $this->server_path."libraries/jquery/";
			
			// Load the core css & js files
			$minified_or_not	= (DEBUG) ? '' : '.min';
			$this->tpl->css_file($this->path.'core/core'.$minified_or_not.'.css');
			$this->tpl->js_file($this->path.'core/core'.$minified_or_not.'.js', -100);

			// add the root_path to javascript
			$this->tpl->add_js("var mmocms_root_path = '".$this->server_path."';");
			$this->tpl->add_js("var mmocms_page = '".$this->env->current_page."';");
			$this->tpl->add_js("var mmocms_sid = '".$this->SID."';");
			$this->tpl->add_js("var mmocms_userid = ".$this->user->id.";");

			// jquery language file
			$langfile = '';
			$this->langfile('lang_jquery.js');

			// set the custom UI for jquery.ui
			$this->CustomUI($this->user->style['template_path']);

			// set the static html for notification
			$this->tpl->staticHTML('
				<div id="notify_container">
					<div id="default"  class="notify_default">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<h1>T{title}</h1>
						<p>T{text}</p>
					</div>

					<div id="error" class="notify_error">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<div style="float:left;margin:0 10px 0 0"><i class="fa fa-times fa-3x"></i></div>
						<h1>T{title}</h1>
						<p>T{text}</p>
					</div>

					<div id="success" class="notify_success">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<div style="float:left;margin:0 10px 0 0"><i class="fa fa-check fa-3x"></i></div>
						<h1>T{title}</h1>
						<p>T{text}</p>
					</div>
				</div>');
				$this->tpl->add_js('$("#notify_container").notify();', 'docready');
				$this->tpl->add_js('$(".lightbox").colorbox({rel:"lightbox", transition:"none", maxWidth:"90%", maxHeight:"90%"});', 'docready');
				$this->tpl->add_js('$("time.datetime").relativeTime();', 'docready');
				$this->tpl->add_js('$(".equalto").change(function(){
					field1	= $("#" + $(this).data("equalto")).val();
					field2	= $(this).val();
					console.log($(this).next("span.errormessage"));
					if(field1 != field2){
						$(this).next("span.errormessage").show();
					}else{
						$(this).next("span.errormessage").hide();
					}
				});', 'docready');
			$this->init_formvalidation();
			$this->init_spinner();
		}
		public function langfile($file){
			if ((isset($this->user->data['user_id'])) && ($this->user->is_signedin()) && (!empty($this->user->data['user_lang']))) {
				$langfile = $this->root_path.'language/'.$this->user->data['user_lang'].'/'.$file;
			}elseif(is_object($this->core)){
				$langfile = $this->root_path.'language/'.$this->config->get('default_lang').'/'.$file;
			}
			if(is_file($langfile)){
				$this->tpl->js_file($langfile);
			}
		}

		public function fullcalendar(){
			// include the calendar js/css.. css is included in base template dir, but can be overwritten by adding to template
			if(!$this->inits['fullcalendar']){
				$this->tpl->js_file($this->path."js/fullcalendar/fullcalendar.min.js");
				if(is_file($this->root_path.'templates/'.$this->user->style['template_path'].'/fullcalendar.css')){
					$this->tpl->css_file($this->root_path.'templates/'.$this->user->style['template_path'].'/fullcalendar.css');
				}else{
					$this->tpl->css_file($this->root_path.'templates/base_template/fullcalendar.css');
				}
				$this->tpl->css_file($this->root_path.'templates/fullcalendar.print.css', 'print');
			
				// now load the fullcalendar language file
				$this->langfile('lang_fullcalendar.js');
				$this->inits['fullcalendar']	= true;
			}
		}

		public function init_jqplot($mobile=false){
			// include the jqplot files
			if(!$this->inits['jqplot']){
				$this->tpl->js_file($this->path."js/jqplot/jquery.jqplot.min.js");
				$this->tpl->js_file($this->path."js/jqplot/jqplot.canvasAxisTickRenderer.min.js");
				$this->tpl->js_file($this->path."js/jqplot/jqplot.canvasTextRenderer.min.js");
				$this->tpl->js_file($this->path."js/jqplot/jqplot.categoryAxisRenderer.min.js");
				$this->tpl->js_file($this->path."js/jqplot/jqplot.dateAxisRenderer.min.js");
				$this->tpl->js_file($this->path."js/jqplot/jqplot.highlighter.min.js");
				$this->tpl->js_file($this->path."js/jqplot/jqplot.pieRenderer.min.js");
				if(is_file($this->root_path.'templates/'.$this->user->style['template_path'].'/jquery.jqplot.css')){
					$this->tpl->css_file($this->root_path.'templates/'.$this->user->style['template_path'].'/jquery.jqplot.css');
				}else{
					$this->tpl->css_file($this->path."js/jqplot/jquery.jqplot.css");
				}
				if($mobile){
					$this->tpl->js_file($this->path."js/jqplot/jqplot.mobile.min.js");
				}
				$this->inits['jqplot']	= true;
			}
		}
		
		public function init_multilang(){
			if(!$this->inits['multilang']){
				$this->tpl->add_js("
				$('body').on('click', '.multilang-switcher', function(){
					$(this).parent().find('.multilang-dropdown').toggle();
				})
				$('body').on('click', '.multilang-dropdown ul li', function(){
					console.log($(this));
					$(this).parent().find('li').removeClass('active');
					$(this).addClass('active');
					var langkey = $(this).data('key');
					var langname = 	$(this).data('lang');
					$(this).parent().parent().parent().find('.multilang-switcher span').html(langname);
					$(this).parent().parent().parent().find('input').hide();
					$(this).parent().parent().parent().find('textarea').hide();
					$(this).parent().parent().parent().find('.'+langkey).show();
					$(this).parent().parent().hide();
				})
				", "docready");
				
				$this->inits['multilang']	= true;
			}
		}

		/**
		* Code Editor
		*
		* @param $id		ID of the input field
		* @param $code		SourceCode to be highlighted
		* @param $type		html/xml/css/html_js
		* @param $options	Array with options
		* @return CHAR
		*/
		public function CodeEditor($id, $code, $type="html", $options=''){
			// Check if the JS file is loaded..
			if(!$this->ce_loaded){
				$this->tpl->js_file($this->path.'js/syntax/codemirror.js');
				$this->ce_loaded = true;

				// Load the line numbers
				if(!isset($options['no_css'])){
					$this->tpl->add_css("
						.CodeMirror-line-numbers {
							width: 2.2em;
							color: #aaa;
							background-color: #eee;
							text-align: right;
							padding-right: .3em;
							font-size: 10pt;
							font-family: monospace;
							padding-top: .4em;
						}
						.CodeMirror-wrapping{
							background: #f5f5f5;
						}
					");
				}
			}

			$path_to_syntax = $this->path."js/syntax/";

			// Switch the different types
			switch($type){
				case 'html':
					$js_file	= "'parsexml.js'";
					$css_file	= "'".$path_to_syntax."css/xmlcolors.css'";
				break;
				case 'xml':
					$js_file	= "'parsexml.js'";
					$css_file	= "'".$path_to_syntax."css/xmlcolors.css'";
				break;
				case 'css':
					$js_file	= "'parsecss.js'";
					$css_file	= "'".$path_to_syntax."css/csscolors.css'";
				break;
				case 'html_js':
					$js_file	= '["parsexml.js", "parsecss.js", "tokenizejavascript.js", "parsejavascript.js", "parsehtmlmixed.js"]';
					$css_file	= '["'.$path_to_syntax.'css/xmlcolors.css", "'.$path_to_syntax.'css/jscolors.css", "'.$path_to_syntax.'css/csscolors.css"]';
				break;	
			}

			// init the editor
			$this->tpl->add_js("
				var editor = CodeMirror.fromTextArea('".$id."', {
					height: '".((isset($options['textarea_height'])) ? $options['textarea_height'] : '350px')."',
					parserfile: ".$js_file.",
					stylesheet: ".$css_file.",
					path: '".$path_to_syntax."',
					continuousScanning: 500,
					lineNumbers: ".((isset($options['no_linenumbers'])) ? false : true)."
				});
			", 'eop');
			return '<textarea name="'.$id.'" class="'.$id.'" id="'.$id.'" cols="120" rows="30">'.$code.'</textarea>';
		}

		/**
		* Load custom UI css file
		*
		* @param $template		Template name
		*/
		public function CustomUI($template){
			$customui_css = $this->root_path.'templates/'.$template.'/jquery_tmpl.css';
			if(is_file($customui_css)){
				$this->tpl->css_file($customui_css);
			}
		}

		/**
		* Dialog
		*
		* @param $name		ID of the input field
		* @param $title		Title of the window
		* @param $options	Array with options
		* @param $type		confirm/url/alert/alert_indirect
		* @return CHAR
		*/
		public function Dialog($name, $title, $options, $type='url'){
			switch ($type){

				// Close window..
				case 'close':
					// not implemented yet
				break;

				// Confirm Windows..
				case 'confirm':	
					$jscode			= (isset($options['custom_js'])) ? $options['custom_js'] : "window.location ='".$options['url']."'";
					$cancel			= (isset($options['cancel_js'])) ? $options['cancel_js'] : '';
					$addit_jscode	= '';

					//
					if(isset($options['confirm_url']) && $options['confirm_url']){
						$cnfname	= (isset($options['confirm_name'])) ? $options['confirm_name'] : 'cnfrmarray[]';
						$cnfname 	= (isset($options['confirm_name']) && $options['confirm_name'] == '_class_') ? '.cb_select_class' : "input:checkbox[name=\"".$cnfname."\"]";
						$addit_jscode = "var selected = new Array();";
						if (isset($options['withid'])){
							$addit_jscode .= "selected.push(".$options['withid'].");";
						} else {
							$addit_jscode .= "$('".$cnfname.":checked').each(function(){
								selected.push($(this).val());
							});";
						}	
						$addit_jscode .= "
							$.ajax({
								url: '".$options['confirm_url']."',
								type: 'POST',
								data: {type: selected},
								success: function(data){
									".$name."_confirm.html(".$name."_confirm.html().replace(/<span style=\"display:none;\">#replacedata#<\/span>/g, data));
								}
							})
							";
					}
									
					// confirm JS Code
					$this->tpl->add_js("
						function ".$name."(".((isset($options['withid'])) ? $options['withid'] : '')."){
							".((isset($options['onlickjs'])) ? $options['onlickjs'] : '')."
							var ".$name."_confirm = 
								$('<div><\/div>')
								.html('<div class=\"confirmdialog\"><i class=\"fa fa-exclamation-triangle fa-2x\" style=\"float:left; margin:0 7px 24px 0;\"></i>".$this->sanitize($options['message'], false, true)."<\/div>')
								.dialog({
									title: '".$this->sanitize($title)."',
									resizable: false,
									height:".((isset($options['height'])) ? $options['height'] : '200').",
									modal: true,
									buttons: {
										\"".$this->sanitize(((isset($options['buttontxt'])) ? $options['buttontxt'] : $this->user->lang('cl_bttn_ok')))."\": function() {
											".$jscode."
											$( this ).dialog('close');
										},
										\"".$this->sanitize($this->user->lang('cancel'))."\": function() {
											".((isset($options['cancel_js'])) ? $options['cancel_js'] : '')."
											$( this ).dialog('close');
										}
									}
								});
								".$addit_jscode."
							}");
				break;

				// URL Windows...
				case 'url':
					$myclose = (isset($options['onclose'])) ? ", close: function(event, ui) { window.location.href = '".$options['onclose']."'; }" : '';
					$myclose		= (isset($options['onclosejs'])) ? ", close: function(event, ui) { ".$options['onclosejs']." }" : $myclose;
					$beforeclose	= (isset($options['beforeclose'])) ? ", beforeClose: function(event, ui) { ".$options['beforeclose']." }" : "";
					$this->tpl->add_js("
						function ".$name."(".((isset($options['withid'])) ? $options['withid'] : '')."){
							jQuery.FrameDialog.create({
								url: '".$options['url']."',
								title: '".$this->sanitize($title)."',
								buttons: ".((isset($options['buttons'])) ? $options['buttons'] : 'false').",
								height: ".((isset($options['height'])) ? $options['height'] : '300').",
								width: ".((isset($options['width'])) ? $options['width'] : '600').",
								modal: ".((isset($options['modal'])) ? $options['modal'] : 'false').",
								resizable: ".((isset($options['resize'])) ? $options['resize'] : 'true').",
								draggable: ".((isset($options['draggable'])) ? $options['draggable'] : 'true').$myclose.$beforeclose."
							})
						}");
				break;

				// Alert Message (direct)
				case 'alert':
					$jscod = "var ".$name."_alert = 
								$('<div></div>')
								.html('<p class=\"confirmdialog\"><i class=\"fa fa-exclamation-triangle fa-2x\" style=\"float:left; margin:0 7px 24px 0;\"></i>".$this->sanitize($options['message'])."</p>')
								.dialog({
								bgiframe: true,
								modal: true,
								height: ".((isset($options['height'])) ? $options['height'] : 	'150').",
								width: ".((isset($options['width'])) 	? $options['width'] : 	'300').",
								title: '".$this->sanitize($title)."',
								buttons: {
									Ok: function() {
										$(this).dialog('close');
									}
								}
							});
						";
					if(isset($options['outputonly'])){
						return $jscod;
					}else{
						$this->tpl->add_js("function show_".$name."(".((isset($options['withid'])) ? $options['withid'] : '')."){"
											.$jscod.
											"}");
					}
				break;
			}
		}

		/**
		* Slider
		*
		* @param $id		ID of the input field
		* @param $options	Options Array
		* @param $type		normal/range
		* @return HTML
		*/
		public function Slider($id, $options, $type='normal'){
			switch($type){
				case 'normal' :
					if (!isset($options['value'])) $options['value'] = 0;
					$this->tpl->add_js('$("#'.$id.'").slider({
						slide: function(event, ui) {
							console.log(ui);
								$("#'.$id.'-label").html(ui.value);
								$("#'.$id.'_0").val(ui.value);
						},
						value: '.(int)$options['value'].'
					});', 'docready');
					$class = (!empty($options['class'])) ? ' class="'.$options['class'].'"' : '';
					return '<label for="'.$id.'-label">'.$options['label'].': <span id="'.$id.'-label">'.$options['value'].'</span></label><div id="'.$id.'"'.$class.' style="width:'.((isset($options['width'])) ? $options['width'] : '100%').';"></div>
							<input type="hidden" id="'.$id.'_0" name="'.$options['name'].'" value="'.$options['value'].'" />';
				break;

				case 'range' :
					$this->tpl->add_js('
						$("#'.$id.'-sr").slider({
							range: true,
							min: '.$options['min'].',
							max: '.$options['max'].',
							values: ['.$options['value'][0].', '.$options['value'][1].'],
							slide: function(event, ui) {
								$("#'.$id.'-label").html(ui.values[0] + \' - \' + ui.values[1]);
								$("#'.$id.'_0").val(ui.values[0]);
								$("#'.$id.'_1").val(ui.values[1]);
							}
						});
						$("#'.$id.'-label").val($("#'.$id.'-sr").slider("values", 0) + \' - \' + $("#'.$id.'-sr").slider("values", 1));
						$("#'.$id.'_0").val($("#'.$id.'-sr").slider("values", 0));
						$("#'.$id.'_1").val($("#'.$id.'-sr").slider("values", 1));
				', 'docready');
				if(empty($options['name'])) $options['name'] = $id;
				$class = (!empty($options['class'])) ? ' class="'.$options['class'].'"' : '';
				$html = '<label for="'.$id.'-label">'.$options['label'].': <span id="'.$id.'-label">'.$options['value'][0].' - '.$options['value'][1].'</span></label>
									<input type="hidden" id="'.$id.'_0" name="'.$options['name'].'[]" value="'.$options['value'][0].'" />
									<input type="hidden" id="'.$id.'_1" name="'.$options['name'].'[]" value="'.$options['value'][1].'" />
									<div id="'.$id.'-sr"'.$class.' style="width:'.((isset($options['width'])) ? $options['width'] : '100%').';"></div>';
				return $html;
				break;
			}
		}

		/**
		* Check all Checkboxes
		*
		* @param $id			ID of the css class (must be unique)
		* @param $name			name of the checkboxes to be checked
		* @param $exid			exclude an ID being checked
		* @return false
		*/
		public function selectall_checkbox($id, $name, $exid=false){
			if($exid){
				$this->tpl->add_js('$("#'.$id.'").click(function(){
					var checked_status = this.checked;
					$("input[name=\''.$name.'\']").each(function(){
						if($(this).val() != \''.$exid.'\'){				
							$(this).prop(\'checked\', checked_status).trigger(\'change\');
						}
					});
				});', 'docready');
			}else{
				$this->tpl->add_js('$("#'.$id.'").click(function(){
					var checked_status = this.checked;
					$("input[name=\''.$name.'\']").each(function(){
						$(this).prop(\'checked\', checked_status).trigger(\'change\');
					});
				});', 'docready');
			}
		}
		
		/**
		* Autocomplete
		*
		* @param $id		ID of the input field
		* @param $myarray	Data Array
		* @return false
		*/
		public function Autocomplete($id, $myarray){

			if(is_array($myarray) && count($myarray) > 0){
				$myarray = str_replace('"', '', $myarray);	// clean the array, remove hyphens
				$js_array = $this->implode_wrapped('"','"', ",", $myarray);
				if (is_array($id)){					
					$ids = implode(',#', $id);
					$id = array_shift($id);
				} else {
					$ids = $id;
				}

				$this->tpl->add_js('
						var jquiac_'.$id.' = ['.$js_array.'];
						$("#'.$ids.'").autocomplete({
							source: jquiac_'.$id.'
						});
					', 'docready');
				return '['.$js_array.']';
			}
			return '[]';
		}
		
		public function AutocompleteMultiple($id, $myarray, $js_function){
			if(is_array($myarray) && count($myarray) > 0){
				if (is_array($id)){
					$ids = implode(',#', $id);
					$id = array_shift($id);
				} else {
					$ids = $id;
				}
				$this->tpl->add_js('
						var jquiac_'.$id.' = '.json_encode($myarray).';
						$("#'.$ids.'").autocomplete({
							source: jquiac_'.$id.',
							select: function(event, ui){
								'.$js_function.'
							},
							minLength:1
						});
				', 'docready');
			}
		}
		
		public function init_spinner(){
			if(!$this->inits['spinner']){
				$this->tpl->add_js("$('.core-spinner').each(function() {
										var self = $(this),
											min = self.data('min'),
											max = self.data('max'),
											step = self.data('step');
										$(this).spinner({
											min: min,
											max: max,
											step: step,
										});
									});", 'docready');
				$this->inits['spinner'] = true;
			}
		}
		
		/**
		* DropDown Menu
		*
		* @param $id			ID of the css class (must be unique)
		* @param $menuitems		Array with menu information
		* @param $imagepath		Where are the images?
		* @param $button		Show a button with name?
		* @return CHAR
		*/
		public function DropDownMenu($id, $menuitems ,$button=''){
			$dmmenu  = '<ul id="'.$id.'" class="sf-menu sf-ddm">
							<li><a href="#">'.$button.'</a>
						<ul>';
			foreach($menuitems as $key=>$value){
				if($value['perm']){
					$dmimg = (isset($value['icon']) && $value['icon']) ? '<i class="fa '.$value['icon'].' fa-fw fa-lg"></i>' : '';
					$dmmenu .= '<li><a href="'.$value['link'].'">'.$dmimg.'&nbsp;&nbsp;'.$value['name'].'</a></li>';
				}
			}
			$dmmenu .= '</ul>
					</li>
				</ul>';
			$this->tpl->add_js("$('#".$id."').superfish(); ", 'docready');
			return $dmmenu;
		}
		
		/**
		* DropDown Menu
		*
		* @param $id			ID of the css class (must be unique)
		* @param $menuitems		Array with menu information
		* @param $imagepath		Where are the images?
		* @param $button		Show a button with name?
		* @return CHAR
		*/
		// TODO: use icon instead of image
		public function ButtonDropDownMenu($id, $menuitems, $checkbox_listener=array(), $imagepath='', $button='', $buttonIcon=''){
			if (count($checkbox_listener)){
				foreach($checkbox_listener as $listener_id) {
					$this->tpl->add_js("
					$('".$listener_id."').on('change', function() {
						var count = 0;
						if ($('".$listener_id."').prop(\"multiple\")){
							$('".$listener_id." :selected').each(function(i, selected){ 
								count += 1;
							});
						} else {
							$('".$listener_id."').each(function(){
								if (this.checked){
									count += 1;
								}
							});
						}
						var menubutton = \"".$button."\";
						if (count > 0){
							menubutton = count + ' ' + menubutton;
						}
						$('#".$id." .sf-btn-name').html(menubutton);
					});
					", 'docready');
				}
			}
		
			$dmmenu  = '<ul id="'.$id.'" class="sf-menu sf-btn-ddm">
							<li>'.(($buttonIcon != '') ? '<img src="'.$this->root_path.$buttonIcon.'" alt="" style="float:left; margin-right:2px;"/> ' : '').'<a href="javascript:void(0);" class="sf-btn-name">'.$button.'</a>
						<ul><div class="clear"></div>';
			foreach($menuitems as $key=>$value){
				if($value['perm']){
					
					$dmimg = ($value['icon']) ? $this->core->icon_font($value['icon'], 'fa-lg', $this->root_path.$imagepath.'/') : '';
					switch($value['type']){
						case 'button': $dmmenu .= '<li><a href="javascript:void(0);" onclick="$(\''.$value['link'].'\').trigger(\'click\');">'.$dmimg.'&nbsp;&nbsp;'.$value['name'].'</a>'.((isset($value['append'])) ? $value['append'] : '').'</li>';
						break;
						
						default: $dmmenu .= '<li><a href="'.$value['link'].'">'.$dmimg.'&nbsp;&nbsp;'.$value['name'].'</a>'.((isset($value['append'])) ? $value['append'] : '').'</li>';
					}
					
				}
			}
			$dmmenu .= '</ul>
					</li>
				</ul>';
			$this->tpl->add_js("
				var focused_".$id." = null;
				$('#".$id."').superfish({
					onInit: function(){
						var ul = $(this);
				        var inputs = ul.find('input, select');
				        inputs.each(function(index, elt){
							$(elt).on('click', function(event){
					            focused_".$id." = $(elt);
					            event.stopPropagation();
					        });
					        $(document).on('click', function(event){
					            /*to allow people to choose to quit the menu*/
					            focused_".$id." = null;
					        });
					       
						})
					}, 
					onHide: function(){
						var ul = $(this);
						console.log(focused_".$id.");
						if(focused_".$id." != null){
						   this.stop(true, true);
						   //ul.css('display', 'block');
						   this.show();
						}
					}
			}); ", 'docready');
			return $dmmenu;
		}
		
		
		
		/**
		* SuckerFish horizontal Menu
		*
		* @param $array			Menu items array
		* @param $name			Name of the ul class
		* @param $mnuimagepth	Image path for menu images
		* @param $nodefimage	Do not use a default image
		* @return CHAR
		*/
		public function SuckerFishMenu($array, $name, $mnuimagepth, $nodefimage=false, $scndclass=''){
			$this->MenuConstruct_js($name);
			return $this->MenuConstruct_html($array, $name, $mnuimagepth, $nodefimage, $scndclass);
		}

		/**
		* Construct: HTML for the SuckerFish Menu
		*
		* @param $array			Menu items array
		* @param $name			Name of the ul class
		* @param $mnuimagepth	Image path for menu images
		* @param $nodefimage	Do not use a default image
		* @param $scndclass		Class of the menu
		* @return CHAR
		*/
		public function MenuConstruct_html($array, $name, $mnuimagepth, $nodefimage, $scndclass='sf-admin'){
			$hhm  = '<ul class="'.$name.' '.$scndclass.'">';

			// Header row
			if(is_array($array)){
				foreach($array as $k => $v){
					// Restart next loop if the element isn't an array we can use
					if ( !is_array($v) ){continue;}

					$header_row = '<li><a href="#">'.$this->core->icon_font((isset($v['icon'])) ? $v['icon'] : ((isset($v['img']) ? $v['img'] : (($nodefimage) ? '' : 'fa-puzzle-piece'))), 'fa-lg fa-fw', $mnuimagepth).' '.$v['name'].'</a>
										<ul>';

					// Generate the Menues
					$sub_rows = '';
					if(is_array($v)){
						foreach ( $v as $k2 => $row ){
							$admnsubmenu = ((isset($row['link']) && $row['text']) ? false : true);
							// Ignore the first element (header)
							if ( ($k2 == 'name' || $k2 == 'icon') &&  !$admnsubmenu){
								continue;
							}
							
							// the extension submenues
							if($admnsubmenu) {
								// build the icons
								$icon = $this->core->icon_font((isset($row['icon'])) ? $row['icon'] : ((isset($row['img']) ? $row['img'] : (($nodefimage) ? '' : 'fa-puzzle-piece'))), 'fa-lg fa-fw', $mnuimagepth);
								$plugin_header_row = '<li><a href="#">'.$icon.' '.((isset($row['name'])) ? $row['name'] : 'UNKNOWN').'</a>
													<ul>';
								// Submenu
								$plugin_sub_row = '';
								if(!isset($row['link']) && !isset($row['text'])){
									if(is_array($row)){
										foreach($row as $k3 => $row2){
											if ($k3 == 'name' || $k3 =='icon'){
												continue;
											}

											if ($row2['check'] == '' || ((is_array($row2['check'])) ? $this->user->check_auths($row2['check'][1], $row2['check'][0], false) : $this->user->check_auth($row2['check'], false))){
												$subsub_icon = $this->core->icon_font((isset($row2['icon'])) ? $row2['icon'] : ((isset($row2['img']) ? $row2['img'] : (($nodefimage) ? '' : ''))), 'fa-lg fa-fw', $mnuimagepth);
												$plugin_sub_row .= '<li><a href="'.$this->root_path.$row2['link'].'">';
												$plugin_sub_row .= $subsub_icon.' '.$row2['text'].'</a></li>';
											}
										}
									}
								}
								if(strlen($plugin_sub_row) > 0) $sub_rows .= $plugin_header_row.$plugin_sub_row.'</ul></li>';
							}else{
								if (($row['check'] == '' || ((is_array($row['check'])) ? $this->user->check_auths($row['check'][1], $row['check'][0], false) : $this->user->check_auth($row['check'], false))) && (!isset($row['check2']) || $row['check2'] == true)){
									$subicon	= $this->core->icon_font((isset($row['icon'])) ? $row['icon'] : ((isset($row['img']) ? $row['img'] : (($nodefimage) ? '' : ''))), 'fa-lg fa-fw', $mnuimagepth);
									$sub_rows .= '<li><a href="'.$this->root_path.$row['link'].'">';
									$sub_rows .= $subicon.' '.$row['text'].'</a></li>';
								}
							}
						}
					}
					
					if(strlen($sub_rows)) $hhm .= $header_row.$sub_rows.'</ul></li>';
				}
			}
			$hhm .= '</ul>';
			return $hhm;
		}

		/**
		* Construct: Javascript for the SuckerFish Menu
		*
		* @param $name		Name of the ul class
		* @return CHAR
		*/
		private function MenuConstruct_js($name){
			$this->tpl->add_js("
					jQuery('ul.".$name."').superfish({
						delay:		400,
						animation:	{opacity:'show',height:'show'},
						speed:		'fast'
					});
			", 'docready');
		}

		/**
		* Horizontal Accordion
		*
		* @param $name		Name/ID of the accordion (must be unique)
		* @param $list		Content array in the format: title => content
		* @return CHAR
		*/
		public function Accordion($name, $list, $options=''){
			$tmpopt = array();
			$tmpopt[] = 'heightStyle: "content"';
			if(isset($options['active'])){ $tmpopt[] = 'active: '.$options['active'];}
			if(isset($options['collapsible'])){ $tmpopt[] = 'collapsible: true';}
			if(isset($options['disabled'])){ $tmpopt[] = 'disabled: true';}
			if(isset($options['event'])){ $tmpopt[] = 'event: "'.$options['event'].'"';}

			$this->tpl->add_js("
					jQuery('#".$name."').accordion(".$this->gen_options($tmpopt).");
			", 'docready');
			$acccode   = '<div id="'.$name.'">';
			if(is_array($list)){
				foreach($list as $title=>$content){
					$acccode  .= '<h3>'.$title.'</h3>
								<div>'.$content.'</div>';
				}
			}
			$acccode  .= '</div>';
			return $acccode;
		}

				/**
		* notfication messages
		*
		* @param $msg		The Message to show
		* @param $options	Option List: life,sticky,speed, header,theme
		* @return CHAR
		*/
		public function notify($msg, $options){
			$parenttag	=(isset($options['parent']) && $options['parent'] == true) ? 'parent.' : '';
			$JSclick	= $JSoptions = array();

			$JSclick[]		= "text: '".$this->sanitize($msg, true)."'";
			if(is_array($options)){
				if(isset($options['header']) && !empty($options['header'])){	$JSclick[]		= "title: '".$options['header']."'";}
				
				// events (http://www.erichynds.com/blog/a-jquery-ui-growl-ubuntu-notification-widget)
				if(isset($options['beforeopen'])){	$JSoptions[]	= "beforeopen: function(e,instance){ ".$options['beforeopen']."}";}
				if(isset($options['open'])){		$JSoptions[]	= "open: function(e,instance){ ".$options['open']."}";}
				if(isset($options['close'])){		$JSoptions[]	= "close: function(e,instance){ ".$options['close']."}";}
				if(isset($options['click'])){		$JSoptions[]	= "click: function(e,instance){ ".$options['click']."}";}

				// the options of the notify
				if(isset($options['stack'])){	$JSoptions[]	= "stack: '".$options['stack']."'";}
				if(isset($options['custom'])){	$JSoptions[]	= "custom: true";}
			}

			// some fix variables
			$JSoptions[]	= 'expires: '.((isset($options['expires']) && (int)$options['expires'] > 0) ? $options['expires'] : 'false');
			$JSoptions[]	= 'speed: '.((isset($options['speed'])) ? $options['speed'] : 1000);
			$theme			= (isset($options['theme'])) ? $options['theme'] : 'default';

			// generate the output
			$this->tpl->add_js($parenttag.'$("#notify_container").notify("create", "'.$theme.'", '.$this->gen_options($JSclick).','.$this->gen_options($JSoptions).');', 'docready');
		}

		public function lightbox($id, $options){
			if(is_array($options)){
				if(isset($options['slideshow']) && $options['slideshow'] == true){	$jsoptions[]	= "slideshow: true";}
				if(isset($options['slideshowAuto'])){	$jsoptions[]	= "slideshowAuto: ".(($options['slideshowAuto'] == true) ? "true" : "false");}
				if(isset($options['transition'])){	$jsoptions[]	= "transition: '".$options['transition']."'";}
				if(isset($options['slideshowSpeed'])){	$jsoptions[]	= "slideshowSpeed:".$options['slideshowSpeed'];}
			}
			$jsoptions[] = 'rel:"'.$id.'"';
			$jsoptions[] = 'maxWidth:"90%"';
			$jsoptions[] = 'maxHeight:"90%"';
			
			$this->tpl->add_js('$(".lightbox_'.$id.'").colorbox('.$this->gen_options($jsoptions).');', 'docready');
		}

		/**
		* Date Picker
		*
		* @param $name		Name/ID of the calendar (must be unique)
		* @param $value		Value for the input field
		* @param $jscode	Javascript code of the input field
		* @param $options	Array with Options for calendar
		* @return CHAR
		*/
		public function Calendar($name, $value, $jscode='', $options=''){
			$mclass		= (isset($options['class'])) ? ' '.$options['class'] : '';
			$itemid		= (isset($options['id'])) ? $options['id'] : 'cal_'.$name;
			$myreadonly = (isset($options['readonly']) && $options['readonly']) ? ' readonly="readonly"' : '';
			$html		= '<input type="text" id="'.$itemid.'" name="'.$name.'" value="'.$value.'" size="15" '.$jscode.$mclass.$myreadonly.' />';
			$MySettings	= ''; $dpSettings = array();
			
			// Load default settings if no custom ones are defined..
			$options['format']		= (isset($options['format'])) ? $options['format'] : $this->time->translateformat2js($this->user->style['date_notime_short']);
			$options['cal_icons']	= (isset($options['cal_icons'])) ? $options['cal_icons'] : true;

			// Options
			if(isset($options['format']) && $options['format'] != ''){
				$dpSettings[] = "dateFormat: '".$options['format']."'";
			}
			$dpSettings[] = (isset($options['change_fields'])) ? 'changeMonth: true, changeYear: true' : 'changeMonth: false, changeYear: false';
			if($options['cal_icons']){
				$html = '<span class="input-icon-append">'.$html.'<i class="fa fa-calendar" onclick="$( \'#'.$itemid.'\' ).datepicker( \'show\' );"></i></span>';
			}
			if(isset($options['show_buttons'])){
				$dpSettings[] = "showButtonPanel: true";
			}
			if(isset($options['number_months']) && $options['number_months'] > '1'){
				$dpSettings[] = "numberOfMonths: ".$options['number_months'];
			}
			if(isset($options['year_range']) && $options['year_range'] != ''){
				$dpSettings[] = "yearRange: '".$options['year_range']."'";
			}
			if(isset($options['other_months'])){
				$dpSettings[] = "showOtherMonths: true";
			}
			if(isset($options['change_month'])){
				$dpSettings[] = "changeMonth: true";
			}
			if(isset($options['change_year'])){
				$dpSettings[] = "changeYear: true";
			}
			if(!isset($options['timeformat'])) $options['timeformat'] = $this->time->translateformat2js($this->user->style['time']);
			if(strpos($options['timeformat'], 's') !== false || isset($options['enablesecs'])) {
				$dpSettings[] = 'showSecond: true';
			}
			if(isset($options['onselect'])){
				$dpSettings[] = "onSelect: function(dateText, inst) { ".$options['onselect']." }";
			}
			
			if(isset($options['onclose'])){
				$dpSettings[] = "onClose: function( selectedDate ) { ".$options['onclose']." }";
			}

			if(count($dpSettings)>0){
				$MySettings = implode(", ", $dpSettings);
			}

			// JS Code Output
			if(isset($options['timepicker'])){
				$addisettings = array(
					"timeFormat:'".$options['timeformat']."'"
				);
				$functioncall = "datetimepicker({".$MySettings.",".implode(", ", $addisettings)."})";
				if(!isset($options['return_function'])) {
					$this->tpl->add_js("
						$('#".$itemid."').".$functioncall.";", 'docready');
				}
			}else{
				$functioncall = "datepicker({".$MySettings."})";
				if(!isset($options['return_function'])) {
					$this->tpl->add_js("
						$('#".$itemid."').".$functioncall.";", 'docready');
				}
			}
			/*if(!isset($options['return_function'])) {
				$this->tpl->add_js("
					$(\"img[class='ui-datepicker-trigger']\").each(function(){ 
					  $(this).after('<i class=\"fa fa-calendar ui-datepicker-trigger\"></i>');
					 // $(this).remove();
					 });
					
					", 'docready');
			}*/
			
			$this->setLanguage('datepicker', "$.datepicker.setDefaults($.datepicker.regional['{!language!}']);");
			$this->setLanguage('timepicker', "$.timepicker.setDefaults($.timepicker.regional['{!language!}']);");
			return (isset($options['return_function'])) ? $functioncall : $html;
		}

		/**
		* Tab Header
		*
		* @param $name			Name/ID of the tabulator (must be unique)
		* @param $cookie		Save the selection or not...
		* @param $taboptions	Options array
		* @return CHAR
		*/
		public function Tab_header($name, $cookie=false, $taboptions=false){
			$jsoptions = array();
			
			if($cookie){
				$jsoptions[] = "beforeActivate: function(e, ui) { localStorage.setItem('tabs.".$name."', ui.newTab.index()); },
									create: function (e, ui) {
										var tabID		= (window.location.hash) ? $('#' +  window.location.hash.replace('#', '')).index() : 0;
										var selectionId	= (window.location.hash && tabID > 0) ? tabID-1 : ((localStorage.getItem('tabs.".$name."') != null) ? localStorage.getItem('tabs.".$name."') : 0);
										$(this).tabs('option', 'active', selectionId);
									}";
			}
			$jsoptions[]	= 'fxSlide: '.(isset($taboptions['fxSlide'])) ? $taboptions['fxSlide'] : 'true';
			$jsoptions[]	= 'fxFade: '.(isset($taboptions['fxFade'])) ? $taboptions['fxFade'] : 'true';
			$jsoptions[]	= 'fxSpeed: '.(isset($taboptions['fxSpeed'])) ? $taboptions['fxSpeed'] : 'normal';
			if(isset($taboptions['show'])){
				$jsoptions[]	= 'show: function(event, ui) {'.$taboptions['show'].'}';
			}
			if(isset($taboptions['custom'])){
				$jsoptions[]	= $taboptions['custom'];
			}
			$this->tpl->add_js('$("#'.$name.'").tabs('.$this->gen_options($jsoptions).');', 'docready');
		}

		/**
		* Select a tab of an existing tab group
		*
		* @param $name			Name/ID of the tabulator (must be unique)
		* @param $selection		The Number of the tab to be selected (starts with 0)
		* @return CHAR
		*/
		public function Tab_Select($name, $selection){
			$this->tpl->add_js('$("#'.$name.'").tabs("option", "active", '.$selection.');', 'docready');
		}

		/**
		* Color Picker
		*
		* @param $name		Name/ID of the colorpicker field (must be unique)
		* @param $value		Value for the input field
		* @param $size		size of the field
		* @param $jscode	Optional JavaScript Code tags
		* @return CHAR
		*/
		public function colorpicker($id, $value, $name='', $size='14', $jscode=''){
			if(!$this->inits['colorpicker']) {
				$this->tpl->add_js('$(".colorpicker").spectrum({showInput: true, preferredFormat: "hex6"});', 'docready');
				$this->inits['colorpicker'] = true;
			}
			return '<input type="text" class="colorpicker" id="'.$id.'_input" name="'.(($name) ? $name : $id).'" value="'.$value.'" size="'.$size.'" '.$jscode.' />';
		}

		/**
		* Set Progress Bar Value
		*
		* @param $id		ID of the div (must be unique)
		* @param $value		Value between 0 and 100
		* @return CHAR
		*/
		function SetProgressbarValue($id, $value){
			$value = number_format($number, 2, '.', '');
			$this->tpl->add_js('$("#'.$id.'").progressbar({ value: '.$value.' });', 'docready');
		}

		/**
		* Progress Bar
		*
		* @param $id			ID of the div (must be unique)
		* @param $options		Array with options [completed, total, text, txtalign, directout]
		* @return CHAR
		*/
		public function progressbar($id, $value=0, $options=''){
			$html	= '';
			// options
			$value = (isset($options['completed']) && isset($options['total']) && $options['total'] >= $options['completed']) ? (($options['completed'] != 0) ? intval(($options['completed'] / $options['total']) * 100) : 0) : (($value >= 0 && $value <= 100) ? $value : '0');

			// format the number to fit the requirements for jquery.ui
			$value	= number_format($value, 2, '.', '');

			// the javascript
			$mjs	= '$("#'.$id.'").progressbar({ value: '.$value.' });';

			// direct output or use the template engine?
			if(isset($options['directout']) && $options['directout']){
				$html .= '<script type="text/javascript">$(function(){ '.$mjs.' });</script>';
			}else{
				$this->tpl->add_js($mjs, 'docready');
			}

			// generate percentage text if required
			$text					= (isset($options['text']) && strpos($options['text'], "%percentage%") === false) ? $options['text'] : str_replace("%percentage%", $value.'%', $options['text']);
			$text					= (isset($options['text']) && strpos($text, "%progress%") === false) ? $text : str_replace("%progress%", $options['completed'].'/'.$options['total'], $text);
			if (!isset($options['txtalign'])) $options['txtalign'] = 'center';

			// the HTML of the progressbar
			$html	.= '<div id="'.$id.'">'.((isset($options['text']) && $options['text']) ? '<span class="progressbar_label"'.(($options['txtalign']) ? ' style="text-align: '.$options['txtalign'].'"' : '').'>'.$text.'</span>' : '').'</div>';
			return $html;
		}

		/**
		* Init the formvalidation "hide-message-if-empty-inout" functionality
		*
		* @return JScode
		*/
		public function init_formvalidation(){
			if(!$this->inits['formvalidation']){
				$this->tpl->add_js("
				$('.fv_checkit input[required]').change(function(e) {
					var forminputvalue=$.trim($(this).val());
					//console.log('valid: '+$(this)[0].checkValidity());
					if(forminputvalue.length == 0){
						$(this).next('.fv_msg').hide();
						$(this).removeClass('fv_inp_invalid');
					}else{
						if (!$(this)[0].checkValidity()) {
							$(this).next('.fv_msg').show();
							$(this).addClass('fv_inp_invalid');
						}
					}
				}).trigger('change');", 'docready');
				$this->inits['formvalidation'] = true;
			}
		}

		/**
		* Star Rating Widget
		*
		* @param $name			name/id of the rating thing
		* @param $url			url for the ajax post request
		* @param $options		Options array
		* @return CHAR
		*/
		public function starrating($name, $url, $options=''){
			if(!$this->inits['starrating']){
				$this->starrating_js();
				$this->inits['starrating'] = true;
			}
			$tmpopt		= array();
			$tmpopt[]	= 'data-star-number="'.((isset($options['number']) && $options['number'] > 0) ? $options['number'] : 5).'"';
			$tmpopt[]	= 'data-star-score="'.((isset($options['score']) && $options['score'] > 0) ? $options['score'] : 0).'"';
			if(isset($options['readonly']) && $options['readonly']){
				$tmpopt[]	= 'data-star-readonly="true"';
			}

			return '<div class="starrating" data-star-url="'.$url.'" data-star-name="'.$name.'" '.implode(" ", $tmpopt).'></div>';
		}

		public function starrating_js(){
			//$lang_cancvote	= ($this->user->lang('lib_starrating_cancel')) ?$this->sanitize( $this->user->lang('lib_starrating_cancel')) : 'Cancel Rating';
			$tmpopt		= array();
			$tmpopt[] = 'starType: "i"';
			$tmpopt[] = 'cancelOff: "fa fa-times-circle-o"';
			$tmpopt[] = 'cancelOn: "fa fa-times-circle"';
			$tmpopt[] = 'starHalf: "fa fa-star-half"';
			$tmpopt[] = 'starOff: "fa fa-star-o"';
			$tmpopt[] = 'starOn: "fa fa-star"';
			$tmpopt[] = 'size: 16';
			$tmpopt[] = 'score: function() { return $(this).attr("data-star-score"); }';
			$tmpopt[] = 'number: function() { return $(this).attr("data-star-number"); }';
			$tmpopt[] = 'readOnly: function() { return $(this).attr("data-star-readonly") == "true"; }';
			$tmpopt[] = 'click: function(score, evt) {
			$.post($(this).attr("data-star-url"), {name: $(this).attr("data-star-name"), score: score}, function(data){
				$("#result").html(data);
			});
		  }';

			$this->tpl->add_js('$(".starrating").raty('.$this->gen_options($tmpopt).');', 'docready');
		}

		/**
		* MultiSelect with checkboxes & filter
		*
		* @param $name		Name/ID of the colorpicker field (must be unique)
		* @param $value		List as an array
		* @param $selected	selected items as string or array
		* @param $height	height of the popup
		* @param $width		width of the popup
		* @param $options	Array with options [id, preview_num, no_animation, sel_text, header, multiple]
		* @return CHAR
		*/
		public function MultiSelect($name, $list, $selected, $options=''){
			$myID		= (isset($options['id'])) ? $options['id'] : "dw_".$name;
			if(empty($options['height'])) $options['height'] = 200;
			if(empty($options['width'])) $options['width'] = 200;
			$tmpopt		= array();
			$tmpopt[] = 'height: '.$options['height'];
			$tmpopt[] = 'minWidth: '.$options['width'];
			$tmpopt[] = 'selectedList: '.((isset($options['preview_num']) && $options['preview_num'] > 0) ? $options['preview_num'] : '5');
			$tmpopt[] = 'multiple: '.((isset($options['multiple']) && !$options['multiple']) ? 'false' : 'true');
			if(isset($options['no_animation'])){	$tmpopt[] = 'show: "blind",hide: "blind"';}
			if(isset($options['header'])){			$tmpopt[] = 'header: "'.$options['header'].'"';}
			$todisable = (isset($options['todisable'])) ? ((is_array($options['todisable'])) ? $options['todisable'] : array($options['todisable'])) : array();
			$filterme = '';
			if(isset($options['filter'])){
				$filterme = '.multiselectfilter({'.'label: "'.$this->sanitize($this->user->lang('filter')).':"'.'})';
			}
			$javascript = (isset($options['javascript'])) ? $options['javascript'] : '';

			$this->tpl->add_js('$("#'.$myID.'").multiselect('.$this->gen_options($tmpopt).')'.$filterme.';', 'docready');
			$dropdown = "<select name='".$name."[]' id='".$myID."' multiple='multiple'".$javascript.">";
			$selected = (is_array($selected))? $selected : explode("|", $selected);
			if(is_array($list)){
				foreach ($list as $key => $value) {
					$selected_choice = (in_array($key, $selected)) ? ' selected="selected"' : '';
					$disabled = (in_array($key, $todisable)) ? ' disabled="disabled"' : '';
					$dropdown .= "<option value='".$key."'".$selected_choice.$disabled.">".$value."</option>";
				}
			}
			$dropdown .= "</select>";
			return $dropdown;
		}

		/**
		* RSS Feed Reader
		*
		* @param $name			ID of the css class (must be unique)
		* @param $url			URL to the Feed
		* @param $items			Amount of Feed items to show
		* @param $length		Preview text length
		* @param $backgr		Bakcground (true/false)
		* @return TimePicker JS Code
		*/
		public function rssFeeder($name, $url, $items='4', $length='80', $backgr=false){
			$backgr = ($backgr) ? $backgr : $this->user->style['tr_color1'];
			$tmpopt		= array();
			$tmpopt[] = 'targeturl: "'.$url.'"';
			$tmpopt[] = 'items: '.$items;
			$tmpopt[] = 'Maxlength: '.$length;
			$tmpopt[] = 'background: "'.$backgr.'"';
			$tmpopt[] = 'lang_readmore: "'.$this->sanitize($this->user->lang('lib_rss_readmore')).'"';
			$tmpopt[] = 'lang_loadingalt: "'.$this->sanitize($this->user->lang('lib_rss_loading')).'"';
			$tmpopt[] = 'lang_errorpage: "'.$this->sanitize($this->user->lang('lib_rss_error')).'"';
			$this->tpl->add_js('$("#'.$name.'").rssReader('.$this->gen_options($tmpopt).');', 'docready');
		}
		
		/**
		* Load the RSS Feed
		*
		* @param $url      URL of the Feed File
		* @return --
		*/
		public function loadRssFeed($url){
			header('content-type: application/xml');
			print $this->puf->fetch($url);
			exit;
		}

		/**
		* Single Time Picker
		*
		* @param $name			ID of the css class (must be unique)
		* @param $value			default value of the input
		* @param $hour			starttime: hours
		* @param $min			starttime: minutes
		* @param $sec			starttime: seconds
		* @param $enablesecs	use seconds			
		* @param $hourf			Format of the time: 24 or 12
		* @return TimePicker	JS Code
		*/
		public function timePicker($id, $name='', $value='', $enablesecs=false, $hourf=24){
			if(!$name) $name = 'input_'.$id;
			$tmpopt		= array();
			$tmpopt[] = 'hour: "'.($value-$value%3600)/3600 .'"';
			$tmpopt[] = 'minute: "'.($value%3600-($value%3600)%60)/60 .'"';
			$tmpopt[] = 'second: "'.($value%3600)%60 .'"';
			$tmpopt[] = 'showSecond: '.(($enablesecs) ? 'true' : 'false');
			$tmpopt[] = 'ampm: '.(($hourf == 12) ? 'true' : 'false');
			
			$this->tpl->add_js("$('#".$id."').timepicker(".$this->gen_options($tmpopt).");", 'docready');
			$this->setLanguage('timepicker', "$.timepicker.setDefaults($.timepicker.regional['{!language!}']);");
			return '<input name="'.$name.'" id="'.$id.'" value="'.$value.'" type="text" />';
		}

		/**
		* jqPlot: charts
		*
		* @param $type					The type of the chart (line, pie)
		* @param $id					ID of the css class (must be unique)
		* @param $data					Array with data
		* @param $options				The options array
		* @return HTML Code
		*/
		public function charts($type, $id, $data, $options=''){
			$this->init_jqplot();
			switch($type){
				case 'line':	return $this->charts_line($id, $data, $options);break;
				case 'multiline': return $this->charts_multiline($id, $data, $options); break;
				case 'pie':		return $this->charts_pie($id, $data, $options);break;
			}
		}

		/**
		* jqPlot: PieChart
		*
		* @param $id				ID of the css class (must be unique)
		* @param $data				Array with data
		* @param $options			The options array
		* @return TimePicker JS Code
		*/
		private function charts_pie($id, $data, $options=''){
			$js_array		= $this->Array2jsArray($data);

			$tmpopt		= array();
			$tmpopt[]	= "grid: {background: '".((isset($options['background'])) ? $options['background'] : '#f5f5f5')."', 
				borderColor: '".((isset($options['bordercolor'])) ? $options['bordercolor'] : '#999999')."', 
				borderWidth: ".((isset($options['border'])) ? $options['border'] : '2.0').", 
				shadow: ".((isset($options['shadow'])) ? 'true' : 'false'). "}";
			$tmpopt[]	= "seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{ 
				sliceMargin: ".((isset($options['piemargin']) && $options['piemargin'] > 0) ? $options['piemargin'] : 6).
				((isset($options['datalabels'])) ? ", showDataLabels: true, dataLabelNudge: 80, dataLabels: 'label'" : '')."}}";
			$tmpopt[]	= "legend:{show:".((isset($options['legend'])) ? 'true' : 'false').", escapeHtml:true}";
			if(isset($options['title'])){
				$tmpopt[]	= "title: '".$options['title']."'";
			}
			if(isset($options['color_array']) && is_array($options['color_array']) && count($options['color_array']) > 0){
				$tmpopt[]	= 'seriesColors: [ '.$this->implode_wrapped('"','"', ',', $options['color_array']).' ]';
			}
			$this->tpl->add_js("
				jqplotdata_".$id." = ".$js_array.";
				plot_".$id." = $.jqplot('".$id."', [jqplotdata_".$id."], ".$this->gen_options($tmpopt).");", 'docready');
			
			return '<div id="'.$id.'"></div>';
		}

		/**
		* jqPlot: LineChart
		*
		* @param $id					ID of the css class (must be unique)
		* @param $data					Array with data
		* @param $options				The options array
		* @return HTML Code
		*/
		private function charts_line($id, $data, $options=''){
			$js_array		= $this->Array2jsArray($data, false);

			// switch the renderers
			switch ($options['xrenderer']){
				case 'date':	$renderer = 'renderer:$.jqplot.DateAxisRenderer';break;
				default:		$renderer = 'renderer:$.jqplot.CategoryAxisRenderer';
			}

			$tmpopt		= array();
			if(isset($options['highlighter']) && $options['highlighter']){
				$tmpopt[]	= 'highlighter: { show: true }';
			}
			if(isset($options['title']) && $options['title']){
				$tmpopt[]	= "title: '".$options['title']."'";
			}
			$tmpopt[]		= "axes:{xaxis:{".(($options['autoscale_x']) ? 'autoscale:true' : $renderer)."},yaxis:{".(($options['autoscale_y']) ? 'autoscale:true' : '')."}}";
			$tmpopt[]		= "series:[{lineWidth:".((isset($options['lineWidth'])) ? $options['lineWidth'] : 4).", markerOptions:{style:'".((isset($options['markerStyle'])) ? $options['markerStyle'] : 'square')."'}}]";
			$this->tpl->add_js(" 
					plot_".$id." = $.jqplot('".$id."', [".$js_array."], ".$this->gen_options($tmpopt).");", 'docready');

			return '<div id="'.$id.'" style="'.(($options['height']) ? 'height:'.$options['height'].'px;' : '').' '.(($options['width']) ? 'width:'.$options['width'].'px;' : '').'"></div>';
		}
		
		/**
		 * jqPlot: MultiLineChart
		 *
		 * @param $id					ID of the css class (must be unique)
		 * @param $data					Array with data
		 * $data = array('series1' => array('name' => 'Name of Series1', 'lineWidth' => 4, 'markerStyle' => 'square', 'data' => array(array(0,1), array(1, 10), array(2, 15))), 'series2' => ..... );
		 * 
		 * 
		 * @param $options				The options array
		 * @return HTML Code
		 */
		private function charts_multiline($id, $data, $options=''){
			// switch the renderers
			switch ($options['xrenderer']){
				case 'date':	$renderer = 'renderer:$.jqplot.DateAxisRenderer';break;
				default:		$renderer = 'renderer:$.jqplot.CategoryAxisRenderer';
			}
		
			$tmpopt		= array();
			if(isset($options['highlighter']) && $options['highlighter']){
				$tmpopt[]	= 'highlighter: { show: true }';
			}
			if(isset($options['legend']) && $options['legend']){
				$tmpopt[]	= 'legend: { show: true, location:\''.((isset($options['legendPosition'])) ? $options['legendPosition'] : 'ne').'\' }';
			}
			
			if(isset($options['title']) && $options['title']){
				$tmpopt[]	= "title: '".$options['title']."'";
			}
			$arrData = array();
			$strSeriesOut = "series:[";
			$arrTmpSeries = array();
			foreach($data as $key => $seriendata){
				$arrTmpSeries[] = '{lineWidth:'.((isset($seriendata['lineWidth'])) ? $seriendata['lineWidth'] : 4).', markerOptions:{style:\''.((isset($seriendata['markerStyle'])) ? $seriendata['markerStyle'] : 'square')."'}, label:'".((isset($seriendata['name'])) ? $seriendata['name'] : "Series".$key)."'}";
				$arrData[] = $seriendata['data'];
			}
			$strSeriesOut .= implode(', ', $arrTmpSeries)."]";
			$tmpopt[] = $strSeriesOut;
			
			$tmpopt[]		= "axes:{xaxis:{".(($options['autoscale_x']) ? 'autoscale:true' : $renderer)."},yaxis:{".(($options['autoscale_y']) ? 'autoscale:true' : '')."}}";
			
			$this->tpl->add_js("
					plot_".$id." = $.jqplot('".$id."', ".json_encode($arrData).", ".$this->gen_options($tmpopt).");", 'docready');
		
			return '<div id="'.$id.'" style="'.(($options['height']) ? 'height:'.$options['height'].'px;' : '').' '.(($options['width']) ? 'width:'.$options['width'].'px;' : '').'"></div>';
		}
		

		/**
		* ToolTip
		* 
		* @param $help		Text to show
		* @param $name		class/id name
		* @return Tooltip
		*/
		public function qtip($name, $content, $options=array()){
			if(!isset($this->tt_init[$name])){
				$content			= (isset($options['contfunc'])) ? '{ text: function(api) { '.$content.' } }' : '"'.$content.'"';
				$viewport		= (isset($options['custom_viewport'])) ? $options['custom_viewport'] : '$(window)';
				$adjust_pos		= (isset($options['position_adjustment'])) ? $options['position_adjustment'] : 'shift none';
				$extra_class	= (isset($options['classes'])) ? 'classes: "'.$options['classes'].'",' : '';
				$my				= (isset($options['my'])) ? $options['my'] : 'top center';
				$at				= (isset($options['at'])) ? $options['at'] : 'bottom center';
				$width			= (isset($options['width'])) ? 'width: '.$options['width'].',' : '';
				$this->tpl->add_js('$("'.$name.'").qtip({
					content: '.$content.',
					position: {
						adjust: {
							method: "'.$adjust_pos.'"
						},
						viewport: '.$viewport.',
						at: "'.$at.'",
						my: "'.$my.'"
					},
					show: {
						event: "mouseenter"
					},
					style: {
						tip: {
							corner: true
						},'.$width.$extra_class.'
						widget: true
					}
				});', 'docready');
				$this->tt_init[$name] = true;
			}
		}

		/**
		* Collapsable div
		* 
		* @param $help		Text to show
		* @param $hide		Hide the collapsable div on load?
		* @param $persist	Save open/closed status?
		* @return Tooltip
		*/
		public function Collapse($id, $hide=false, $persist=true){
			$this->tpl->add_js("$('".$id."').jcollapser({ state: '".(($hide) ? 'inactive' : 'active')."', persistence:	".(($persist) ? 'true' : 'false').", cookiepath: '".$this->server_path."' });", 'docready');
		}

		/**
		* Binding DropDowns: Select in first, changes the input of second DD
		* 
		* @param $id1			The ID of the first (parent) dropdown
		* @param $id2			The ID of the second (child) dropdown
		* @param $array1		The array for the first (parent) dropdown
		* @param $array2		The array for the second (child) dropdown
		* @param $selected1		Value of the first (parent) dropdown
		* @param $selected2		Value of the second (child) dropdown
		* @return array with two dropdowns (parent & child)
		*/
		public function json_dropdown($id1, $id2, $array1, $jsonname, $selected1, $selected2=''){
			$this->tpl->add_js("$('body').on('change', '.{$id1}{$this->dyndd_counter}', function() {
				$('.{$id2}{$this->dyndd_counter} option').remove();
				if($(this).val() > 0){
					mydata	= {$jsonname}[$(this).val()];
					if(typeof mydata != 'undefined'){
						$.each(mydata, function(i, val) {
							$('.{$id2}{$this->dyndd_counter}').append($('<option></option>').val(i).html(val));
						});
					}
				}
			}); 
			$('.{$id1}{$this->dyndd_counter}').trigger('change');
			", 'docready');

			$output	= array(
				new hdropdown($id1, array('options' => $array1, 'value' => $selected1, 'id' => $id1.$this->dyndd_counter, 'class' => $id1.$this->dyndd_counter)),
				new hdropdown($id2, array('options' => $array2, 'id' => $id2.$this->dyndd_counter, 'class' => $id2.$this->dyndd_counter))
			);
			
			$this->dyndd_counter++;
			return $output;
		}
		
		/**
		 * Binding DropDowns: Select in parent, changes the input of child DD
		 * Sets the necessary js-code, does not provide html-code!
		 *
		 * @string 			$id1		The ID of the first (parent) dropdown
		 * @array/@string 	$id2		The ID (or array of IDs) of the second (child) dropdown(s)
		 * @string 			$url		The URL to the ajax call, see "dd_create_ajax"
		 * @string			$add_posts	additional post-variables to pass to the url
		 */
		public function js_dd_ajax($id1, $id2, $url, $add_posts='') {
			$change_js = "$('#".$id1."').change(function() {";
			$js = '';
			$child_js = '';
			// if we only have one child, put it in array for convenience
			if(!is_array($id2)) $id2 = array($id2);
			foreach($id2 as $key => $id) {
				$child_js .= "$('#".$id."').find('option').remove();";
				$child_js .= "$('#".$id."').append(data).trigger('change');";
			}
			$js .= "$.post('".$url."',{requestid:$('#".$id1."').val()".$add_posts."},function(data){".$child_js."});";
			// initialize on page-load
			$this->tpl->add_js($js, 'docready');
			// update on selection change
			$this->tpl->add_js($change_js.$js.'});', 'docready');
		}

		/**
		* Binding DropDowns: Select in first, changes the input of second DD
		* 
		* @param $id1			The ID of the first (parent) dropdown
		* @param $id2			The ID of the second (child) dropdown
		* @param $array1		The array for the first (parent) dropdown
		* @param $array2		The array for the second (child) dropdown
		* @param $selected1		Value of the first (parent) dropdown
		* @param $selected2		Value of the second (child) dropdown
		* @param $url			The URL to the ajax call, see "dd_create_ajax"
		* @return array with two dropdowns (parent & child)
		*/
		public function dd_ajax_request($id1, $id2, $array1, $array2, $selected1, $url, $add_posts='', $selected2=''){

			$jscode  = "$('#".$id1.$this->dyndd_counter."').change(function() {";
			if(is_array($id2)){
				$jscode_p = '';
				foreach($id2 as $ids2){
					$ids2		= preg_replace("~[^A-Za-z0-9]~", "", $ids2);
					$jscode		.= "$('#".$ids2.$this->dyndd_counter."').find('option').remove();";
					$jscode_p	.= "$('#".$ids2.$this->dyndd_counter."').append(data);";
				}
				$jscode .= "$.post('".$url."', { requestid: $(this).val()".$add_posts." } , function(data){ ".$jscode_p." });";
			}else{
				$jscode .= "$('#".$id2.$this->dyndd_counter."').find('option').remove();";
				$jscode .= "$.post('".$url."', { requestid: $(this).val()".$add_posts." } , function(data){ $('#".$id2.$this->dyndd_counter."').append(data) });";
			}
			$jscode .= "});";
			$this->tpl->add_js($jscode, 'docready');

			$output[] = new hdropdown($id1, array('options' => $array1, 'value' => $selected1, 'id' => $id1.$this->dyndd_counter));
			if(is_array($id2)){
				$jscode2 = '';
				$jscode2_p = '';
				$jscode2_c = 0;
				foreach($id2 as $ids2){
					$fieldname	= $ids2;
					$ids2		= preg_replace("~[^A-Za-z0-9]~", "", $ids2);
					$output[] = new hdropdown($fieldname, array('options' => $array2, 'id' => $ids2.$this->dyndd_counter));

					// Load the input of the selection
					$jscode2	.= "$('#".$ids2.$this->dyndd_counter."').find('option').remove();";
					$jscode2_p	.= "$('#".$ids2.$this->dyndd_counter."').append(data);";
					if(isset($selected2[$jscode2_c])){
						$jscode2_p	.= "$('#".$ids2.$this->dyndd_counter."').val('".$selected2[$jscode2_c]."');";
					}
					$jscode2_c++;
				}
				$jscode2	.= "$.post('".$url."', { requestid: $('#".$id1.$this->dyndd_counter."').val()".$add_posts." } , function(data){ ".$jscode2_p." });";
			}else{
				$output[] = new hdropdown($id2, array('options' => $array2, 'id' => $id2.$this->dyndd_counter));
				
				$jscode2 = "$('#".$id2.$this->dyndd_counter."').find('option').remove();
						$.post('".$url."', { requestid: $('#".$id1.$this->dyndd_counter."').val()".$add_posts." } , function(data){ $('#".$id2.$this->dyndd_counter."').append(data) });";
			}
			$this->tpl->add_js($jscode2, 'docready');
			$this->dyndd_counter++;

			return $output;
		}

		/**
		* Build the ajax Recall for the DropDown Selectables
		* 
		* @param $cstlst		Array of the binding dropbox
		* @return dropdown options
		*/
		public function dd_create_ajax($cstlist, $options=''){
			$output = '';
			if(is_array($cstlist)){
				foreach ($cstlist as $uid => $uname){
					$showname	= (isset($options['format'])) ? $options['format']($uname) : $uname;
					$svalue		= (isset($options['noid'])) ? $uname : $uid;
					$selected	= (isset($options['selected']) && $options['selected'] == $svalue) ? ' selected=selected' : '';
					$output 	.= "<option value='$svalue'$selected>$showname</option>";
				}
			}
			return $output;
		}

		/**
		* Image uploader
		* 
		* @param $type				The type of the filebrowser
		* @param $inputid			The ID of the input
		* @param $imgname			The image name
		* @param $imgpath			The image path
		* @param $options			The options array
		* @param $storageFolder		Storage folder
		* @return void
		*/
		public function imageUploader($type, $inputid, $imgname, $imgpath, $options='', $storageFolder=false){
			$this->fileBrowser($type, 'image', $storageFolder);
			
			$default_img_svg	= str_replace('.png', '.svg', $options['noimgfile']);
			$imgpreview			= (isset($imgname) && is_file($imgpath.$imgname)) ? $imgpath.$imgname : $this->root_path.((isset($options['noimgfile'])) ? ((file_exists($this->root_path.$default_img_svg)) ? $default_img_svg : $options['noimgfile']) : 'images/global/default-image.svg');
			list($previmgwidth, $previmgheight, $previmgtype, $previmgattr) = getimagesize($imgpreview);
			
			$imgprevheight	= (isset($options['prevheight'])) ? $options['prevheight'] : '120';
			$imgpreview = str_replace($this->root_path, $this->server_path, $imgpreview);
			
			// the output
			$out	= '<div id="image_'.$inputid.'" class="imageuploader_image">';
			if ($previmgheight > $imgprevheight){
				$out .= '<a href="'.$imgpreview.'" class="lightbox previewurl"><img class="previewimage" src="'.$imgpreview.'" alt="'.$this->user->lang('imageuploader_preview').'" style="max-height:'.$imgprevheight.'px"/></a>';
			} else {
				$out .= '<img src="'.$imgpreview.'" class="previewimage" alt="'.$this->user->lang('imageuploader_preview').'" style="max-height:'.$imgprevheight.'px"/>';
			}	
			$out .=	'</div><button class="mainoption" type="button" id="iubutton_'.$inputid.'_edit" onclick="elfinder_'.$type.'(\''.$inputid.'\');"><i class="fa fa-pencil-square-o"></i>'.$this->user->lang('imageuploader_editbutton').'</button>';
			if(isset($options['deletelink']) && (isset($imgname) && is_file($imgpath.$imgname))){
				$out .= '<button class="mainoption" value="" type="button" id="iubutton_'.$inputid.'_delete"><i class="fa fa-trash-o"></i> '.$this->user->lang('delete').'</button>';
				$this->tpl->add_js("$('#iubutton_".$inputid."_delete').click(function(){ location.href='".$options['deletelink']."' });", 'docready');
			}
			return $out;
		}
		
		/**
		* FileBrowser
		* 
		* @param $type		user / all
		* @param $filter	none / image
		* @return void
		*/
		public function fileBrowser($type = 'user', $filter = 'none', $storageFolder = false, $options=array()){
			$type = ($type == 'user') ? 'user' : 'all';
			
			if (!isset($this->file_browser[$type])){
				$strStorageFolder = ($storageFolder) ? '&sf='.urlencode($this->encrypt->encrypt($storageFolder)) : '';
				
				$myclose = (isset($options['onclose'])) ? ", close: function(event, ui) { window.location.href = '".$options['onclose']."'; }" : '';
				$myclose = (isset($options['onclosejs'])) ? ", close: function(event, ui) { ".$options['onclosejs']." }" : $myclose;
				
				$this->tpl->add_js("function elfinder_".$type."(fieldid){
					jQuery.FrameDialog.create({
						url: '".$this->server_path."libraries/elfinder/elfinder".(($type == 'user') ? '.useravatars' : '').".php".$this->SID."&type=".$filter.$strStorageFolder."&field='+fieldid,
						title: '".((isset($options['title'])) ? $this->sanitize($options['title']) : $this->sanitize($this->user->lang('imageuploader_wintitle')))."',
						height: 500,
						width: 840,
						modal: false,
						resizable: true,
						draggable: true,
						buttons: false".$myclose."
					})
				}");
				
				$this->file_browser[$type] = true;
			}
		}

		/**
		* Toolbar
		* 
		* @param $id			The ID of the toolbar
		* @param $arrItems		The item elements
		* @param $options		The options array
		* @return void
		*/
		public function toolbar($id, $arrItems, $options=array()){
			$position = (!isset($options['position'])) ? 'top' : $options['position'];
			$hideOnClick = (!isset($options['hideOnClick'])) ? true : $options['hideOnClick'];
			$toolbar_id = $id.'-toolbar';
			
			$this->tpl->add_js(
				"$('.".$toolbar_id."').toolbar({
					content: '#".$toolbar_id."-options', 
					position: '".$position."',
					hideOnClick: ".(($hideOnClick) ? 'true' :  'false')."
				});
				$('.".$toolbar_id."').on('toolbarItemClick',
					function( event ) {
						$('.".$toolbar_id."').toolbar('hide');
					}
				);",
			'docready');
			
			$strItems = '';
			$intItems = 0;
			foreach($arrItems as $key => $value){
				if (isset($value['check'])){
					if (!$this->user->check_auth($value['check'], false)) continue;
				}
				$strItems .= '<a href="'.((isset($value['href'])) ? $value['href'] : '#').'" '.((isset($value['js'])) ? $value['js'] : '').' title="'.((isset($value['title'])) ? $value['title'] : '').'"><i class="fa '.$value['icon'].'"></i></a>';
				$intItems++;
			}
			
			$this->tpl->staticHTML(
				'<div id="'.$toolbar_id.'-options"  style="display: none;">'.$strItems.'
				</div>
				'
			);
			
			return array('id' => $toolbar_id, 'items' => $intItems);
		}

		/**
		* Convert a PHP Array to JS Array
		* 
		* @param $formid		Id of the Form to validate
		* @return Tooltip
		*/
		// DEPRECATED - use json_encode instead?
		private function Array2jsArray($array, $jq_array=false, $trenner='"'){
			$last_item = max(array_keys($array));
			if($jq_array){
				$js_array = 'messages: {';
				foreach($array as $ids=>$values){
					$js_array .= $values['name'].': '.$trenner.addslashes($values['value']).$trenner;
					if($last_item != $ids){
						$js_array .= ',';
					}
				}
				$js_array .= '}';
			}else{
				$js_array = '['; $color_array = array();
				foreach($array as $ids=>$values){
					$js_array .= "['".$values['name']."', ".$values['value']."]";
					if(isset($values['color'])){
						$color_array[] = $values['color'];
					}
					if($last_item != $ids){
						$js_array .= ',';
					}
				}
				$js_array .= ']';
			}
			return $js_array;
		}
		
		private function gen_options($array){
			$set_comma = false;
			$output  = "{";
			if(is_array($array) && count($array) > 0){
				foreach($array as $values){
					if($values != ''){
						if($set_comma){
							$output .= ',';
						}
						$set_comma = true;
						$output .= $values;
					}
				}
			}
			$output .= "}";
			return $output;
		}

		/**
		* Implode wrapped version
		*
		* @param $before	before the value
		* @param $after		after the value
		* @param $glue		semicolon or other divorce-signs
		* @param $array		the data array
		* @return sanatized text
		*/
		public function implode_wrapped($before, $after, $glue, $array){
			$output = '';
			foreach($array as $item){
				$output .= $before . $item . $after . $glue;
			}
			return substr($output, 0, -strlen($glue));
		}

		/**
		* Set Language loader if requiredr
		*
		* @param $name			Name of the lamng handler to be loaded
		* @param $initname		the code to be initialized..
		* @return CHAR
		*/
		private function setLanguage($name, $initname){
			if(!isset($this->language_set[$name]) && !empty($name)){
				$MyLanguage	= ($this->user->lang('XML_LANG') && count($this->user->lang('XML_LANG')) < 3) ? $this->user->lang('XML_LANG') : '';
				$this->tpl->add_js(str_replace('{!language!}', $MyLanguage, $initname), 'docready');
				$this->language_set[$name] = true;
			}
		}

		/**
		* Clean up Text for usage in JS Outputs
		*
		* @param $mssg			The text to sanatize
		* @return sanatized text
		*/
		public function sanitize($mssg, $rmlb=false, $in_js=false){
			$mssg = html_entity_decode($mssg, ENT_COMPAT, 'UTF-8');
			$mssg = str_replace('&#039;', "'", $mssg);
			$mssg = str_replace('"', "'", $mssg);
			if($rmlb){
				$mssg = str_replace(array("\n", "\r"), '', $mssg);
			}
			return addslashes($mssg);
		}
	}
}
?>