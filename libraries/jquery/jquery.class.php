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

if (!class_exists("jquery")) {
	class jquery extends gen_class {
		public static $shortcuts = array('puf'=> 'urlfetcher');

		private $ce_loaded				= false;
		private $language_set			= array();
		private $dyndd_counter			= 0;
		private $file_browser			= array();
		private $returnJScache			= false;
		private $inits					= array(
			'colorpicker'		=> false,
			'starrating'		=> false,
			'formvalidation'	=> false,
			'fullcalendar'		=> false,
			'jqplot'			=> false,
			'spinner'			=> false,
			'multilang'			=> false,
			'monthpicker'		=> false,
			'geomap'			=> false,
			'qtip'				=> array(),
			'depr_suckerfish'	=> false,		// DEPRECATED
		);

		/**
		* Construct of the jquery class
		*/
		public function __construct(){
			$this->path			= $this->server_path."libraries/jquery/";

			// Load the core css & js files
			$this->tpl->css_file($this->path.'core/core.css');
			$this->tpl->js_file($this->path.'core/core'.((DEBUG) ? '' : '.min').'.js', 'direct', -100);
			if(DEBUG){
				$this->tpl->js_file($this->path.'core/jquery-migrate.js', 'direct', -99);
			}

			// add a few variables to javascript (head tag)
			$this->tpl->add_js("var mmocms_root_path = '".$this->server_path."';", 'head_top');
			$this->tpl->add_js("var mmocms_page = '".sanitize($this->env->current_page)."';", 'head_top');
			$this->tpl->add_js("var mmocms_controller_path = '".$this->controller_path."';", 'head_top');
			$this->tpl->add_js("var mmocms_seo_extension = '".$this->routing->getSeoExtension()."';", 'head_top');
			$this->tpl->add_js("var mmocms_sid = '".$this->SID."';", 'head_top');
			$this->tpl->add_js("var mmocms_userid = ".$this->user->id.";", 'head_top');
			$this->tpl->add_js("var mmocms_user_timezone = '".$this->time->date("P")."';", 'head_top');
			$this->tpl->add_js("var mmocms_user_dateformat_long = '".$this->time->translateformat2momentjs((isset($this->user->style['date_notime_long'])) ? $this->user->style['date_notime_long'] : (($this->config->get('default_date_long')) ? $this->config->get('default_date_long') : $this->user->lang('style_date_long')))."';", 'head_top');
			$this->tpl->add_js("var mmocms_user_dateformat_short = '".$this->time->translateformat2momentjs((isset($this->user->style['date_notime_short'])) ? $this->user->style['date_notime_short'] : (($this->config->get('default_date_short')) ? $this->config->get('default_date_short') : $this->user->lang('style_date_short')))."';", 'head_top');
			$this->tpl->add_js("var mmocms_user_timeformat = '".$this->time->translateformat2momentjs((isset($this->user->style['time'])) ? $this->user->style['time'] : (($this->config->get('default_date_time')) ? $this->config->get('default_date_time') : $this->user->lang('style_time')))."';", 'head_top');
			$this->tpl->add_js("var mmocms_user_timestamp = '".$this->time->date("m/d/Y H:i:s")."';", 'head_top');
			$this->tpl->add_js("var mmocms_user_timestamp_atom = '".$this->time->date(DATE_ATOM)."';", 'head_top');

			// jquery language file
			$langfile = '';
			$this->langfile('lang_jquery.js');

			// set the custom UI for jquery.ui
			$this->CustomUI((isset($this->user->style['template_path'])) ? $this->user->style['template_path'] : 'eqdkp_modern');

			// new toast notifications
			$this->init_toast();

			$this->tpl->add_js('$(".lightbox, a[rel=\'lightbox\']").colorbox({rel:"lightbox", transition:"none", maxWidth:"90%", maxHeight:"90%"});', 'docready');
			$this->tpl->add_js('$().relativeTime("time.datetime");', 'docready');
			$this->tpl->add_js('$(".equalto").on("change", function(){
					field1	= $("#" + $(this).data("equalto")).val();
					field2	= $(this).val();
					if(field1 != field2){
						$(this).next("span.errormessage").show();
					}else{
						$(this).next("span.errormessage").hide();
					}
				});', 'docready');
			$this->tpl->add_js("function JQisLocalStorageNameSupported() {
					var testKey = 'test', storage = window.sessionStorage;
					try {
						storage.setItem(testKey, '1');
						storage.removeItem(testKey);
						return true;
					}catch (error){
						return false;
					}
				}", 'head');
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
				}elseif(is_file($this->root_path.'templates/'.$this->user->style['template_path'].'/fullcalendar.min.css')){
					$this->tpl->css_file($this->root_path.'templates/'.$this->user->style['template_path'].'/fullcalendar.min.css');
				}else{
					$this->tpl->css_file($this->root_path.'templates/base_template/fullcalendar.min.css');
				}
				$this->tpl->css_file($this->root_path.'templates/fullcalendar.print.min.css', 'print');

				// now load the fullcalendar language file
				$this->tpl->js_file($this->path."js/fullcalendar/locale-all.js");
				$this->inits['fullcalendar']	= true;
			}
		}

		public function monthpicker(){
			// include the calendar js/css.. css is included in base template dir, but can be overwritten by adding to template
			if(!$this->inits['monthpicker']){
				$this->tpl->js_file($this->path."js/monthpicker/MonthPicker.min.js");
				$this->tpl->css_file($this->path."js/monthpicker/MonthPicker.min.css");
				$this->inits['monthpicker']	= true;
			}
		}

		public function get_jscode($module='all', $id=0){
			if($module == 'all'){
				return $this->returnJScache;
			}else{
				return (isset($this->returnJScache[$module][$id])) ? $this->returnJScache[$module][$id] : false;
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

		public function init_geomap(){
			if(!$this->inits['geomap']){
				$this->tpl->css_file($this->path."js/leaflet/leaflet.css");
				$this->tpl->js_file($this->path."js/leaflet/leaflet.js");
				$this->inits['geomap']	= true;
			}
		}

		public function init_toast(){
			$this->tpl->add_js("
			function system_message(text, type, sticky){
				var sticky = sticky || 3000;
				switch (type) {
					case 'error':
						mssgheading = '".$this->user->lang('error')."';
						mssgicon = 'error';
					break;
					case 'success':
						mssgheading = '".$this->user->lang('success')."';
						mssgicon = 'success';
					break;
					case 'warning':
						mssgheading = '".$this->user->lang('warning')."';
						mssgicon = 'warning';
					break;
					case 'info':
						mssgheading = '".$this->user->lang('information')."';
						mssgicon = 'info';
					break;
					default:
						mssgheading = false;
						mssgicon = false;
				}

				custom_message(text, {headertxt:mssgheading, icon: mssgicon, sticky: sticky})
			}

			function custom_message(text, options){
				headertxt		= (options.hasOwnProperty('headertxt')) ? options.headertxt : false;
				mssgicon		= (options.hasOwnProperty('icon')) ? options.icon : false;
				mssgposition	= (options.hasOwnProperty('position')) ? options.position : 'top-right';
				mssgstack		= (options.hasOwnProperty('stack')) ? options.stack : 5;
				mssgclosebutton = (options.hasOwnProperty('closebutton')) ? options.closebutton : true;
				mssgsticky		= (options.hasOwnProperty('sticky') && options.sticky) ? options.sticky : 3000;
				mssgparent		= (options.hasOwnProperty('parent') && options.parent) ? true : false;

				if(mssgparent){
					$.toast({
						heading:				headertxt,
						text:					text,
						icon:					mssgicon,
						position:			mssgposition,
						stack:				mssgstack,
						allowToastClose:	mssgclosebutton,
						hideAfter:			mssgsticky
					}).parent();
				}else{
					$.toast({
						heading:				headertxt,
						text:					text,
						icon:					mssgicon,
						position:			mssgposition,
						stack:				mssgstack,
						allowToastClose:	mssgclosebutton,
						hideAfter:			mssgsticky
					});
				}
			}");
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
		public function CodeEditor($id, $code, $type="html", $options=array()){
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
				$this->pdl->deprecated('jquery_tmpl.css');
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
					$myclose = (isset($options['onclose'])) ? ", close: function(event, ui) {
						setTimeout(function(){document.location.href = '".$options['onclose']."';},250);
					}" : '';
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
		public function Slider($id, $options, $type='normal', $returnJS=false){
			switch($type){
				case 'normal' :
					if (!isset($options['value'])) $options['value'] = 0;
					$this->returnJScache['slider'][$id] = '$("#'.$id.'").slider({
						slide: function(event, ui) {
							console.log(ui);
								$("#'.$id.'-label").html(ui.value);
								$("#'.$id.'_0").val(ui.value);
						},
						value: '.(int)$options['value'].'
					});';
					if(!$returnJS) { $this->tpl->add_js($this->returnJScache['slider'][$id], 'docready'); }
					$class = (!empty($options['class'])) ? ' class="'.$options['class'].'"' : '';
					return '<label for="'.$id.'-label">'.$options['label'].': <span id="'.$id.'-label">'.$options['value'].'</span></label><div id="'.$id.'"'.$class.' style="width:'.((isset($options['width'])) ? $options['width'] : '100%').';"></div>
							<input type="hidden" id="'.$id.'_0" name="'.$options['name'].'" value="'.$options['value'].'" />';
				break;

				case 'range' :
					$this->returnJScache['slider'][$id] = '
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
				';
				if(!$returnJS) { $this->tpl->add_js($this->returnJScache['slider'][$id], 'docready'); }
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
		public function Autocomplete($id, $myarray, $returnJS=false){
			if (is_array($id)){
				$ids = implode(',#', $id);
				$id = array_shift($id);
			} else {
				$ids = $id;
			}

			if(is_array($myarray) && count($myarray) > 0){
				foreach($myarray as $k => $v){
					$myarray[$k] = $this->sanitize($v, true);
				}
				$js_array = $this->implode_wrapped('"','"', ",", $myarray);

				$this->returnJScache['autocomplete'][$id] = 'var jquiac_'.$id.' = ['.$js_array.'];
						$("#'.$ids.'").autocomplete({
							source: jquiac_'.$id.'
						});';
				if(!$returnJS){
					$this->tpl->add_js($this->returnJScache['autocomplete'][$id], 'docready');
				}
				return '['.$js_array.']';
			}else{
				$this->returnJScache['autocomplete'][$id] = '
						$("#'.$ids.'").autocomplete({
							source: "'.$myarray.'"
						});';
				if(!$returnJS){
					$this->tpl->add_js($this->returnJScache['autocomplete'][$id], 'docready');
				}
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
		* Horizontal Accordion
		*
		* @param $name		Name/ID of the accordion (must be unique)
		* @param $list		Content array in the format: title => content
		* @return CHAR
		*/
		public function Accordion($name, $list, $options=array()){
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

		public function lightbox($id, $options){
			if(is_array($options)){
				if(isset($options['slideshow']) && $options['slideshow'] == true){	$jsoptions[]	= "slideshow: true";}
				if(isset($options['slideshowAuto'])){	$jsoptions[]	= "slideshowAuto: ".(($options['slideshowAuto'] == true) ? "true" : "false");}
				if(isset($options['transition'])){	$jsoptions[]	= "transition: '".$options['transition']."'";}
				if(isset($options['slideshowSpeed'])){	$jsoptions[]	= "slideshowSpeed:".$options['slideshowSpeed'];}
				if(isset($options['type'])){	$jsoptions[]	= $options['type'].": true";}
				if(isset($options['title_function'])){	$jsoptions[]	= "title: function(){".$options['title_function']."}";}
				if(isset($options['oncomplete'])){	$jsoptions[]	= "onComplete: function(){".$options['oncomplete']."}";}
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
		public function Calendar($name, $value, $jscode='', $options=array(), $returnJS=false){
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
					$this->returnJScache['calendar'][$name] = "$('#".$itemid."').".$functioncall.";";

				}
			}else{
				$functioncall = "datepicker({".$MySettings."})";
				if(!isset($options['return_function'])) {
					$this->returnJScache['calendar'][$name] =  "$('#".$itemid."').".$functioncall.";";
				}
			}

			if(!$returnJS){
				$this->tpl->add_js($this->returnJScache['calendar'][$name], 'docready');
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
		public function Tab_header($name, $cookie=false, $taboptions=array()){
			$jsoptions = array();

			if($cookie){
				$jsoptions[] = "beforeActivate: function(e, ui) { if(JQisLocalStorageNameSupported()){ localStorage.setItem('tabs.".$name."', ui.newTab.index());console.log('session saved');}else{console.log('session not saved');} },
									create: function (e, ui) {
										var tabID		= (window.location.hash) ? $('#' +  window.location.hash.replace('#', '')).index() : 0;
										var selectionId	= (window.location.hash && tabID > 0) ? tabID-1 : ((JQisLocalStorageNameSupported()) ? ((localStorage.getItem('tabs.".$name."') != null) ? localStorage.getItem('tabs.".$name."') : 0) : 0);
										$(this).tabs('option', 'active', selectionId);
									}";
			}
			$jsoptions[]	= 'fxSlide: '.((isset($taboptions['fxSlide'])) ? $taboptions['fxSlide'] : 'true');
			$jsoptions[]	= 'fxFade: '.((isset($taboptions['fxFade'])) ? $taboptions['fxFade'] : 'true');
			#$jsoptions[]	= 'fxSpeed: '.((isset($taboptions['fxSpeed'])) ? $taboptions['fxSpeed'] : 'normal');  // this is currently not working
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
		public function colorpicker($id, $value, $name='', $size='14', $jscode='', $options=array(), $returnJS=false){
			if(count($options) === 0){
				if(!$this->inits['colorpicker']) {
					$this->returnJScache['colorpicker'][$id] = '$(".colorpicker").spectrum({showInput: true, preferredFormat: "hex6"});';
					if(!$returnJS){ $this->tpl->add_js($this->returnJScache['colorpicker'][$id], 'docready'); }
					$this->inits['colorpicker'] = true;
				}
				return '<input type="text" class="colorpicker" id="'.$id.'_input" name="'.(($name) ? $name : $id).'" value="'.$value.'" size="'.$size.'" '.$jscode.' />';
			} else {
				$jsoptions[] = 'showInput: true';
				$jsoptions[] = 'preferredFormat: "'.((isset($options['format'])) ? $options['format'] : 'hex6').'"';
				if(isset($options['showAlpha'])) $jsoptions[] = 'showAlpha: true';
				if(isset($options['group'])){
					if(!isset($this->inits['colorpicker_'.$options['group']])){
						$this->returnJScache['colorpicker'][$id] = '$(".colorpicker_group_'.$options['group'].'").spectrum('.$this->gen_options($jsoptions).');';
						if(!$returnJS){ $this->tpl->add_js($this->returnJScache['colorpicker'][$id], 'docready'); }
						$this->inits['colorpicker_'.$options['group']] = true;
					}
				} else {
					$this->returnJScache['colorpicker'][$id] = '$(".colorpicker_'.$id.'").spectrum('.$this->gen_options($jsoptions).');';
					if(!$returnJS){ $this->tpl->add_js($this->returnJScache['colorpicker'][$id], 'docready'); }
				}
				return '<input type="text" class="colorpicker_group_'.$options['group'].' colorpicker_'.$id.'" id="'.$id.'_input" name="'.(($name) ? $name : $id).'" value="'.$value.'" size="'.$size.'" '.$jscode.' />';
			}
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
		public function progressbar($id, $value=0, $options=array()){
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
				$this->tpl->add_js('
				$(".fv_checkit").each(function(){ this.noValidate = true; })

				$(".fv_checkit").on("submit", function(e) {
				var self = this;
				$(this).addClass("fv_checked");
				if( $(".fv_checkit").find(".ui-tabs").length ){
					var tabhighlight = { };
					$(".fv_checkit input[required], .fv_checkit input[pattern]").each(function( index, node ) {
						tabs = $(this).parentsUntil(".fv_checkit .ui-tabs");
						tabhighlight[$(tabs[(tabs.length - 1)]).attr("id")] = "valid";
					});
					$(".fv_checkit input[required]:invalid, .fv_checkit input[pattern]:invalid").each(function( index, node ) {
						tabs = $(this).parentsUntil(".fv_checkit .ui-tabs");
						tabhighlight[$(tabs[(tabs.length - 1)]).attr("id")] = "invalid";
					});
					$(this).find(".fv_hint_tab").each(function(){ $(this).remove(); });
					for (var key in tabhighlight) {
						if (tabhighlight.hasOwnProperty(key)) {
							var val = tabhighlight[key]
							tabLI = $("li a[href=\"#"+key+"\"]").parents("li").find("a")
							if(tabLI.find(".fv_hint_tab").text() == "" && val == "invalid"){
								currenttxt = tabLI.text()
								tabLI.html(currenttxt+" <span class=\"fv_hint_tab bubble-red\">!</span>")
							}
						}
					}
				}

				// the existing form validation
				$(".fv_checkit input[required], .fv_checkit input[pattern]").each(function( index, node ) {
					if($(this).is(":invalid")){
						if(typeof $(this).data("fv-message") !== "undefined" && !$(this).next(".fv_msg").length){
							$(this).after("<span class=\"fv_msg\">"+$(this).data("fv-message")+"</span>");
						}
					}
				});

				return (($(self).find("input:invalid").length > 0) ? false : true);
			});', 'docready');
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
		public function starrating($name, $url, $options=array()){
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
		public function MultiSelect($name, $list, $selected, $options=array(), $returnJS=false){
			$myID		= (isset($options['id'])) ? $options['id'] : "dw_".$name;
			if(empty($options['height'])) $options['height'] = 200;
			if(empty($options['width'])) $options['width'] = 200;
			$tmpopt		= array();
			$tmpopt[] = 'height: '.$options['height'];
			$tmpopt[] = 'minWidth: '.$options['width'];
			$tmpopt[] = 'selectedList: '.((isset($options['preview_num']) && $options['preview_num'] > 0) ? $options['preview_num'] : '5');
			$tmpopt[] = 'multiple: '.((isset($options['multiple']) && !$options['multiple']) ? 'false' : 'true');
			if(isset($options['no_animation'])){	$tmpopt[] = 'show: "blind",hide: "blind"';}
			if(isset($options['clickfunc'])){	$tmpopt[] = 'click: function(e){ '.$options['clickfunc'].' }';}
			if(isset($options['header'])){			$tmpopt[] = 'header: "'.$this->sanitize($options['header']).'"';}
			if(isset($options['appendTo'])){			$tmpopt[] = 'appendTo: "'.$this->sanitize($options['appendTo']).'"';}
			if(isset($options['withmax'])){			$tmpopt[] = 'selectedText: "'.$this->sanitize($this->user->lang('jquery_multiselect_selectedtxt')).'"';}
			if(isset($options['selectedtext']) && !isset($options['withmax'])){	$tmpopt[] = 'selectedText: "'.$this->sanitize($options['selectedtext']).'"';}
			if(!isset($options['clickfunc']) && isset($options['minselectvalue']) && $options['minselectvalue'] > 0){
				$tmpopt[] = 'click: function(e){
					if( $(this).multiselect("widget").find("input:checked").length < '.$options['minselectvalue'].' ){
						return false;
					}
				}';
			}

			$todisable = (isset($options['todisable'])) ? ((is_array($options['todisable'])) ? $options['todisable'] : array($options['todisable'])) : array();
			$filterme = '';
			if(isset($options['filter'])){
				$filterme = '.multiselectfilter({'.'label: "'.$this->sanitize($this->user->lang('filter')).':"'.'})';
			}
			$javascript = (isset($options['javascript'])) ? $options['javascript'] : '';

			$this->returnJScache['multiselect'][$myID] = '$("#'.$myID.'").multiselect('.$this->gen_options($tmpopt).')'.$filterme.';';
			if(!isset($options['clickfunc']) && isset($options['minselectvalue']) && $options['minselectvalue'] > 0){
				$this->returnJScache['multiselect'][$myID] .= '
					$("#'.$myID.'").on("multiselectuncheckall", function(event, ui) {
						if( $(this).multiselect("widget").find("input:checked").length < '.$options['minselectvalue'].' ){
							$(this).val("1");
							$(this).multiselect("refresh");
						}
					});';
			}

			if(!$returnJS) {$this->tpl->add_js($this->returnJScache['multiselect'][$myID], 'docready'); }

			$dropdown = "<select name='".$name."[]' id='".$myID."' multiple='multiple'".$javascript.">";
			$selected = (is_array($selected))? $selected : explode("|", $selected);

			if(is_array($list)){
				foreach ($list as $key => $value) {
					if(is_array($value)){
						$dropdown .= '<optgroup label="'.$key.'">';

						foreach ($value as $key2 => $value2) {
							$selected_choice = (in_array($key2, $selected)) ? ' selected="selected"' : '';
							$disabled = (in_array($key2, $todisable)) ? ' disabled="disabled"' : '';
							$dropdown .= "<option value='".$key2."'".$selected_choice.$disabled.">".$value2."</option>";
						}

						$dropdown .= '</optgroup>';
					} else {
						$selected_choice = (in_array($key, $selected)) ? ' selected="selected"' : '';
						$disabled = (in_array($key, $todisable)) ? ' disabled="disabled"' : '';
						$dropdown .= "<option value='".$key."'".$selected_choice.$disabled.">".$value."</option>";
					}
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
			$backgr = ($backgr) ? $backgr : $this->user->style['table_tr_background_color1'];
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
		public function timePicker($id, $name='', $value=0, $enablesecs=false, $hourf=24, $returnJS=false){
			if(strlen($value) == 5){
				$value = $this->time->fromformat($value, 'H:i');
			}

			if(!$name) $name = 'input_'.$id;
			$tmpopt		= array();
			$tmpopt[] = 'hour: "'.($value-$value%3600)/3600 .'"';
			$tmpopt[] = 'minute: "'.($value%3600-($value%3600)%60)/60 .'"';
			$tmpopt[] = 'second: "'.($value%3600)%60 .'"';
			$tmpopt[] = 'showSecond: '.(($enablesecs) ? 'true' : 'false');
			$tmpopt[] = 'ampm: '.(($hourf == 12) ? 'true' : 'false');

			$this->returnJScache['timepicker'][$id] = "$('#".$id."').timepicker(".$this->gen_options($tmpopt).");";
			if(!$returnJS){ $this->tpl->add_js($this->returnJScache['timepicker'][$id], 'docready'); }
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
		public function charts($type, $id, $data, $options=array()){
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
		private function charts_pie($id, $data, $options=array()){
			$js_array		= $this->Array2jsArray($data);

			$tmpopt		= array();
			$tmpopt[]	= "grid: {background: '".((isset($options['background'])) ? $options['background'] : '#f5f5f5')."',
				borderColor: '".((isset($options['bordercolor'])) ? $options['bordercolor'] : '#999999')."',
				borderWidth: ".((isset($options['border'])) ? $options['border'] : '2.0').",
				shadow: ".((isset($options['shadow'])) ? 'true' : 'false'). "}";
			$tmpopt[]	= "seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{
				sliceMargin: ".((isset($options['piemargin']) && $options['piemargin'] > 0) ? $options['piemargin'] : 6).
				((isset($options['datalabels'])) ? ", showDataLabels: true, dataLabelNudge: 80, dataLabels: 'label'" : '')."}}";
			$tmpopt[]	= "legend:{show:".((isset($options['legend'])) ? 'true' : 'false').", escapeHtml:true, rendererOptions: {numberColumns: ".((isset($options['numberColumns'])) ? $options['numberColumns'] : '0')."}}";
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
		private function charts_line($id, $data, $options=array()){
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
		private function charts_multiline($id, $data, $options=array()){
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
			$varname	= str_replace(".", "", $name);
			if(!isset($this->inits['qtip'][$varname])){
				$viewport		= (isset($options['custom_viewport'])) ? $options['custom_viewport'] : '$(window)';
				$adjust_pos		= (isset($options['position_adjustment'])) ? $options['position_adjustment'] : 'shift none';
				$extra_class	= (isset($options['classes'])) ? 'classes: "'.$options['classes'].'",' : '';
				$my				= (isset($options['my'])) ? $options['my'] : 'top center';
				$at				= (isset($options['at'])) ? $options['at'] : 'bottom center';
				$width			= (isset($options['width'])) ? 'width: '.$options['width'].',' : '';
				if(isset($options['sticky'])){
					$content			= (isset($options['contfunc'])) ? 'function(api) { '.$content.' }' : '"'.$content.'"';
					$this->tpl->add_js('$("'.$name.'").qtip({
						content: {
							text: '.$content.',
							title: {
								text: function(api) { return $(this).attr("data-coretiphead") },
								button: true
							}
						},
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
						hide: false,
						style: {
							tip: {
								corner: true
							},'.$width.$extra_class.'
							widget: true
						}
					});', 'docready');
				}else{
					$content			= (isset($options['contfunc'])) ? '{ text: function(api) { '.$content.' } }' : '"'.$content.'"';
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
				}
				$this->inits['qtip'][$varname] = true;
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
			$this->tpl->add_js("$('".$id."').jcollapser({ state: '".(($hide) ? 'inactive' : 'active')."', persistence:	".(($persist) ? 'true' : 'false')." });", 'docready');
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
				(new hdropdown($id1, array('options' => $array1, 'value' => $selected1, 'id' => $id1.$this->dyndd_counter, 'class' => $id1.$this->dyndd_counter)))->output(),
				(new hdropdown($id2, array('options' => array(), 'id' => $id2.$this->dyndd_counter, 'class' => $id2.$this->dyndd_counter)))->output()
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
		public function js_dd_ajax($id1, $id2, $url, $add_posts='', $add_ids=array()) {
			$change_js = "$('#".$id1."').change(function() {";
			$js = '';
			$child_js = '';
			// if we only have one child, put it in array for convenience
			if(!is_array($id2)) $id2 = array($id2);
			$arrAdditionalValues = array();
			foreach($id2 as $key => $id) {
				if(isset($add_ids[$id])) $arrAdditionalValues = array_merge($arrAdditionalValues, $add_ids[$id]);
				$child_js .= "$('#".$id."').find('option').remove();";
				$child_js .= "$('#".$id."').append(data).trigger('change');";
			}

			$addValues = "";
			if(count($arrAdditionalValues)){
				$addValues .= ', parents: '.json_encode($arrAdditionalValues);
				foreach($arrAdditionalValues as $myid){
					$addValues .= ', '.$myid.": $('#".$myid."').val()";
				}
			}

			$js .= "$.post('".$url."',{requestid:$('#".$id1."').val()".$addValues.$add_posts."},function(data){".$child_js."});";
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

			$output[] = (new hdropdown($id1, array('options' => $array1, 'value' => $selected1, 'id' => $id1.$this->dyndd_counter)))->output();
			if(is_array($id2)){
				$jscode2 = '';
				$jscode2_p = '';
				$jscode2_c = 0;
				foreach($id2 as $ids2){
					$fieldname	= $ids2;
					$ids2		= preg_replace("~[^A-Za-z0-9]~", "", $ids2);
					$output[] = (new hdropdown($fieldname, array('options' => $array2, 'id' => $ids2.$this->dyndd_counter)))->output();

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
				$output[] = (new hdropdown($id2, array('options' => $array2, 'id' => $id2.$this->dyndd_counter)))->output();

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
		public function dd_create_ajax($cstlist, $options=array()){
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
		public function imageUploader($type, $inputid, $imgname, $imgpath, $options=array(), $storageFolder=false){
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

		public function placepicker($id, $returnJS=false){
			return $this->Autocomplete($id, $this->env->link.'exchange.php?out=placepicker', $returnJS);
		}

		public function geomaps($id, $arrMarkers=array()){
			$this->init_geomap();

			// We use markers, build a custom map used in usermaps plugin
			if(is_array($arrMarkers) && count($arrMarkers) > 0){
				$markersJS = '';
				foreach($arrMarkers as $markerUserID=>$markerdata){
					$latlangfrinit = $markerdata['lat'].', '.$markerdata['lng'];
					$markersJS .= '
					L.marker(
						['.$markerdata['lat'].', '.$markerdata['lng'].'],
						{title: "'.$this->sanitize($markerdata['title']).'", autoPan: true, layer: "markers"}
					).addTo(map),';
				}

				// now, init the map itsself
				$this->tpl->add_js("var map = L.map('".$id."_map').setView([".$latlangfrinit."], 13);
					L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
						attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
					}).addTo(map);
					var featureGroup = L.featureGroup(["
						.$markersJS.
					']).addTo(map);
					map.fitBounds(featureGroup.getBounds());',
					"docready");

			// we will use the address field as used in the calendar events
			}else{
				$this->tpl->add_js("
					var map = L.map('".$id."_map', {
						center: [$('#".$id."-datacontainer').data('latitude'), $('#".$id."-datacontainer').data('longitude')],
						zoom: 13
					});

					L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
						attribution: '&copy; <a href=\"https://www.openstreetmap.org/copyright\">OpenStreetMap</a> contributors'
					}).addTo(map);

					L.marker(
						[$('#".$id."-datacontainer').data('latitude'), $('#".$id."-datacontainer').data('longitude')]
					).addTo(map);");
			}
			return '<div class="map_frame" id="mapframe_'.$id.'"><div id="'.$id.'_map"></div></div>';
		}

		// placeholder for old calls, will be removed in the future
		public function googlemaps($id, $arrMarkers=array()){
			return $this->geomaps($id, $arrMarkers);
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
				$MyLanguage	= ($this->user->lang('XML_LANG') && strlen($this->user->lang('XML_LANG')) < 3) ? $this->user->lang('XML_LANG') : '';
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
		public function sanitize($mssg, $rmlb=false){
			$mssg = html_entity_decode($mssg, ENT_COMPAT, 'UTF-8');
			$mssg = str_replace('&#039;', "'", $mssg);
			$mssg = str_replace('"', "'", $mssg);
			if($rmlb){
				$mssg = str_replace(array("\n", "\r"), '', $mssg);
			}
			return addslashes($mssg);
		}

		/****************************************************************************
		/* To be replaced
		/****************************************************************************/

		/**
		* Convert a PHP Array to JS Array
		*
		* @param $formid		Id of the Form to validate
		* @return Tooltip
		*/
		// TODO - use json_encode instead?
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
	}
}