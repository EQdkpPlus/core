<?php
 /*
 * Project:		eqdkpPLUS Libraries: jQuery
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:jQuery
 * @version		$Rev$
 * 
 * $Id$
 */

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

if (!class_exists("jquery")) {
	class jquery extends gen_class {
		public static $shortcuts = array('tpl', 'user', 'core', 'time', 'pfh', 'html', 'config', 'puf'=> 'urlfetcher','encrypt'	=> 'encrypt');

		private $tt_init				= '';
		private $ce_loaded				= false;
		private $datepicker_defaults	= false;
		private $dyndd_counter			= 0;
		
		/**
		* Construct of the jquery class
		*/
		public function __construct(){
			$this->path			= $this->root_path."libraries/jquery/";
			
			// Load the core css & js files
			$this->tpl->css_file($this->path.'core/core.css');
			$this->tpl->js_file($this->path.'core/core.js');

			// add the root_path to javascript
			$this->tpl->add_js("var mmocms_root_path = '".$this->root_path."';");

			// jquery language file
			$langfile = '';
			if ((isset($this->user->data['user_id'])) && ($this->user->is_signedin()) && (!empty($this->user->data['user_lang']))) {
				$langfile = $this->root_path.'language/'.$this->user->data['user_lang'].'/jquery.ui.l18n.js';
			}elseif(is_object($this->core)){
				$langfile = $this->root_path.'language/'.$this->config->get('default_lang').'/jquery.ui.l18n.js';
			}
			if(is_file($langfile)){
				$this->tpl->js_file($langfile);
			}
			// set the custom UI for jquery.ui
			$this->CustomUI($this->user->style['template_path']);
			
			// set the static html for notification
			$this->tpl->staticHTML('
				<div id="notify_container">
					<div id="default">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<h1>#{title}</h1>
						<p>#{text}</p>
					</div>

					<div id="error" class="notify_error">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<div style="float:left;margin:0 10px 0 0"><img src="'.$this->root_path.'images/global/false.png" alt="error"/></div>
						<h1>#{title}</h1>
						<p>#{text}</p>
					</div>

					<div id="success" class="notify_success">
						<a class="ui-notify-close ui-notify-cross" href="#">x</a>
						<div style="float:left;margin:0 10px 0 0"><img src="'.$this->root_path.'images/global/ok.png" alt="success"/></div>
						<h1>#{title}</h1>
						<p>#{text}</p>
					</div>
				</div>');
				$this->tpl->add_js('$("#notify_container").notify();', 'docready');
				
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
								.html('<div class=\"confirmdialog\"><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 200px 0;\"><\/span>".$this->sanitize($options['message'], false, true)."<\/div>')
								.dialog({
									title: '".$title."',
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
								title: '".$title."',
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
								.html('<p class=\"confirmdialog\"><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin:0 7px 20px 0;\"></span>".$this->sanitize($options['message'])."</p>')
								.dialog({
								bgiframe: true,
								modal: true,
								height: ".((isset($options['height'])) ? $options['height'] : 	'150').",
								width: ".((isset($options['width'])) 	? $options['width'] : 	'300').",
								title: '".$title."',
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
					$this->tpl->add_js('$("#'.$id.'").slider();', 'docready');
					return '<div id="'.$id.'"></div>';
				break;

				case 'range' :
					$this->tpl->add_js('
						$("#'.$id.'-sr").slider({
							range: true,
							min: '.$options['min'].',
							max: '.$options['max'].',
							values: ['.$options['values'][0].', '.$options['values'][1].'],
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
				$html = '<label for="'.$id.'-label">'.$options['label'].': <span id="'.$id.'-label">'.$options['values'][0].' - '.$options['values'][1].'</span></label>
									<input type="hidden" id="'.$id.'_0" name="'.$id.'[]" value="'.$options['values'][0].'" />
									<input type="hidden" id="'.$id.'_1" name="'.$id.'[]" value="'.$options['values'][1].'" />
									<div id="'.$id.'-sr" style="width:'.((isset($options['width'])) ? $options['width'] : '100%').';"></div>';
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
							this.checked = checked_status;
						}
					});
				});', 'docready');
			}else{
				$this->tpl->add_js('$("#'.$id.'").click(function(){
					var checked_status = this.checked;
					$("input[name=\''.$name.'\']").each(function(){
						this.checked = checked_status;
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
			}
		}
		
		/**
		* Spinner
		*
		* @param $id			ID of the css class (must be unique)
		* @param $options		Options Array
		* @return false
		*/
		public function spinner($id, $options=''){
			$tmpopt = array();
			if(isset($options['step'])){ $tmpopt[] = 'step: '.$options['step'];}
			if(isset($options['max'])){ $tmpopt[] = 'max: '.$options['max'];}
			if(isset($options['min'])){ $tmpopt[] = 'min: '.$options['min'];}
			if(isset($options['value'])){ $tmpopt[] = 'value: '.$options['value'];}
			if(isset($options['numberformat'])){ $tmpopt[] = 'numberformat: '.$options['numberformat'];}
			if(isset($options['incremental'])){ $tmpopt[] = 'incremental: true';}
			if(isset($options['change'])) { $tmpopt[] = 'change: function( event, ui ) {'.$options['change'].'}';}
			$selector = (isset($options['multiselector'])) ? '' : '#';
			$this->tpl->add_js('$("'.$selector.$id.'").spinner('.$this->gen_options($tmpopt).');', 'docready');
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
		public function DropDownMenu($id, $menuitems, $imagepath ,$button=''){
			$dmmenu  = '<ul id="'.$id.'" class="sf-menu sf-ddm">
							<li><a href="#">'.$button.'</a>
						<ul>';
			foreach($menuitems as $key=>$value){
				if($value['perm']){
					$dmimg = ($value['img']) ? '<img src="'.$this->root_path.$imagepath.'/'.$value['img'].'" alt="" />' : '';
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
		* SuckerFish horizontal Menu
		*
		* @param $array			Menu items array
		* @param $name			Name of the ul class
		* @param $mnuimagepth	Image path for menu images
		* @param $nodefimage	Do not use a default image
		* @return CHAR
		*/
		public function SuckerFishMenu($array, $name, $mnuimagepth, $nodefimage=false){
			$this->MenuConstruct_js($name);
			return $this->MenuConstruct_html($array, $name, $mnuimagepth, $nodefimage);
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
		private function MenuConstruct_html($array, $name, $mnuimagepth, $nodefimage, $scndclass='sf-admin'){
			$hhm  = '<ul class="'.$name.' '.$scndclass.'">';

			// Header row
			if(is_array($array)){
				foreach($array as $k => $v){
					// Restart next loop if the element isn't an array we can use
					if ( !is_array($v) ){continue;}
					$header_row = '<li><a href="#"><img src="'.((!isset($v['icon'])) ? (($nodefimage) ? '' : $mnuimagepth.'plugin.png') : $mnuimagepth.$v['icon']).'" alt="img" /> '.$v['name'].'</a>
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
							if($admnsubmenu) {
								$plugin_header_row = '<li><a href="#"><img src="'.((!isset($row['icon'])) ? (($nodefimage) ? '' : $mnuimagepth.'plugin.png') : $mnuimagepth.$row['icon']).'" alt="img" /> '.((isset($row['name'])) ? $row['name'] : 'UNKNOWN').'</a>
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
												$plugin_sub_row .= '<li><a href="'.$this->root_path.$row2['link'].'">';
												$plugin_sub_row .= (isset($row2['icon'])) ? '<img src="'.$mnuimagepth.$row2['icon'].'" alt="img" /> ' : '';
												$plugin_sub_row .= $row2['text'].'</a></li>';
											}
										}
									}
								}
								if(strlen($plugin_sub_row) > 0) $sub_rows .= $plugin_header_row.$plugin_sub_row.'</ul></li>';
							}else{
								if (($row['check'] == '' || ((is_array($row['check'])) ? $this->user->check_auths($row['check'][1], $row['check'][0], false) : $this->user->check_auth($row['check'], false))) && (!isset($row['check2']) || $row['check2'] == true)){
									$sub_rows .= '<li><a href="'.$this->root_path.$row['link'].'">';
									$sub_rows .= ($row['icon']) ? '<img src="'.$mnuimagepth.$row['icon'].'" alt="img" /> ' : '';
									$sub_rows .= $row['text'].'</a></li>';
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
					jQuery('ul.".$name."').supersubs({
						minWidth:	14,
						maxWidth:	40,
						extraWidth:	2
					}).superfish({
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
		public function Accordion($name, $list){
			$this->tpl->add_js("
					jQuery('#".$name."').accordion({
						header: '.title',
						autoHeight: false
					});
			", 'docready');
			$acccode   = '<div id="'.$name.'">';
			if(is_array($list)){
				foreach($list as $title=>$content){
					$acccode  .= '<div>
									<div class="title">'.$title.'</div>
									<div class="content">'.$content.'</div>
								</div>';
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
			$jsoptions	= array();

			$jsoptions[] = "text: '".$this->sanitize($msg, true)."'";
			if(is_array($options)){
				if(isset($options['header'])){	$jsoptions[]	= "title: '".$options['header']."'";}
				if(isset($options['custom'])){	$jsoptions[]	= "custom: true";}
				if(isset($options['stack'])){	$jsoptions[]	= "stack: '".$options['stack']."'";}
				if(isset($options['click'])){	$jsoptions[]	= "click: function(e,instance){ ".$options['click']."}";}
			}

			// some fix variables
			$expiresval	= (isset($options['expires'])) ? 'true' : 'false';
			$speedval	= (isset($options['speed'])) ? $options['speed'] : 1000;
			$theme		= (isset($options['theme'])) ? $options['theme'] : 'default';

			// generate the output
			$this->tpl->add_js($parenttag.'$("#notify_container").notify("create", "'.$theme.'", '.$this->gen_options($jsoptions).',{expires: '.$expiresval.', speed: '.$speedval.'});', 'docready');
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
			
			//we need to use a fixed format if PHP 5.3 isnt in use
			if(!function_exists('date_create_from_format')) {
				$options['format'] = $this->time->translateformat2js('Y-m-d');
				$options['timeformat'] = $this->time->translateformat2js('H:i');
			}
			// Load default settings if no custom ones are defined..
			$options['format']		= (isset($options['format'])) ? $options['format'] : $this->time->translateformat2js($this->user->style['date_notime_short']);
			$options['cal_icons']	= (isset($options['cal_icons'])) ? $options['cal_icons'] : true;

			// Options
			if(isset($options['format']) && $options['format'] != ''){
				$dpSettings[] = "dateFormat: '".$options['format']."'";
			}
			$dpSettings[] = (isset($options['change_fields'])) ? 'changeMonth: true, changeYear: true' : 'changeMonth: false, changeYear: false';

			if($options['cal_icons']){
				$dpSettings[] = "showOn: 'button', buttonImage: '".$this->path."core/images/calendar.png', buttonImageOnly: true";
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
			if(!isset($options['timeformat'])) $options['timeformat'] = $this->time->translateformat2js($this->user->style['time']);
			if(strpos($options['timeformat'], 's') !== false || isset($options['enablesecs'])) {
				$dpSettings[] = 'showSecond: true';
			}
			if(isset($options['onselect'])){
				$dpSettings[] = "onSelect: function(dateText, inst) { ".$options['onselect']." }";
			}

			if(count($dpSettings)>0){
				$MySettings = implode(", ", $dpSettings);
			}

			// JS Code Output
			if(isset($options['timepicker'])){
				$addisettings = array(
					"timeOnlyTitle: '".$this->sanitize($this->user->lang('timepicker_title'))."'",
					"timeText: '".$this->sanitize($this->user->lang('timepicker_time'))."'",
					"hourText: '".$this->sanitize($this->user->lang('timepicker_hour'))."'",
					"minuteText: '".$this->sanitize($this->user->lang('timepicker_minute'))."'",
					"secondText: '".$this->sanitize($this->user->lang('timepicker_second'))."'",
					"currentText: '".$this->sanitize($this->user->lang('timepicker_nowbutton'))."'",
					"timeFormat:'".$options['timeformat']."'",
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
			if(!$this->datepicker_defaults) {
				$MyLanguage	= ($this->user->lang('XML_LANG') && count($this->user->lang('XML_LANG')) < 3) ? $this->user->lang('XML_LANG') : '';
				$this->tpl->add_js("
					$.datepicker.setDefaults($.datepicker.regional['".$MyLanguage."']);", 'docready');
				$this->datepicker_defaults = true;
			}
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
			$mycookie		= ($cookie) ? ', selected: ($.cookie("cookie'.$name.'") || 0), select: function(e, ui) { $.cookie("cookie'.$name.'", ui.index, { expires: 30 }) } ' : '';
			$taboptions		= ($taboptions) ? $taboptions : '{ fxSlide: true, fxFade: true, fxSpeed: \'normal\' '.$mycookie.'}';
			$this->tpl->add_js('$("#'.$name.'").tabs('.$taboptions.');', 'docready');
		}

		/**
		* Select a tab of an existing tab group
		*
		* @param $name			Name/ID of the tabulator (must be unique)
		* @param $selection		The Number of the tab to be selected (starts with 0)
		* @return CHAR
		*/
		public function Tab_Select($name, $selection){
			$this->tpl->add_js('$("#'.$name.'").tabs(\'option\', \'selected\', '.$selection.');', 'docready');
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
			//$this->tpl->add_js("$('#".$id."').gccolor();", 'docready');
			$this->tpl->add_js("$('#".$id."').ColorPicker({
				color: '#".$value."',
				onShow: function (colpkr) { $(colpkr).fadeIn(500);return false;},
				onHide: function (colpkr) { $(colpkr).fadeOut(500);return false;},
				onChange: function (hsb, hex, rgb) { $('#".$id." div').css('backgroundColor', '#' + hex);$('#".$id."_input').val(hex);}
			});", 'docready');
			return '<div id="'.$id.'" class="colorSelector"><div style="background-color: #'.$value.'"></div></div><input type="hidden" id="'.$id.'_input" name="'.(($name) ? $name : $id).'" value="'.$value.'" size="'.$size.'" '.$jscode.' />';
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
		* @param $value			Value between 0 and 100
		* @param $text			Text to be shown in the progressbar
		* @param $textalign		Alignment of the text in the processbar
		* @param $directout		Directly output the code
		* @return CHAR
		*/
		public function ProgressBar($id, $value, $text='', $textalign='center', $directout=false){
			$value	= ($value >= 0 && $value <= 100) ? $value : '0';
			$value = number_format($value, 2, '.', '');
			if($text){
				$mcss = '.ui-progressbar { position:relative; }
						.'.$id.'_label { position: absolute; width: 90%; text-align: '.$textalign.'; line-height: 1.9em; left:5%; right:5%;}';
				if($directout){
					$mhtml = '<style type="text/css">'.$mcss.'</style>';
				}else{
					$this->tpl->add_css($mcss);
				}
			}
			$mjs	= '$("#'.$id.'").progressbar({
							value: '.$value.'
						});';
			if($directout){
				$mhtml .= '<script type="text/javascript">$(function(){ '.$mjs.' });</script>';
			}else{
				$this->tpl->add_js($mjs, 'docready');
			}
			if($text){
				$html  = '<div id="'.$id.'"><span class="'.$id.'_label">'.$text.'</span></div>';
			}else{
				$html  = '<div id="'.$id.'"></div>';
			}
			return (($directout) ? $mhtml: '').$html;
		}

		/**
		* Star Rating Widget
		*
		* @param $name			name/id of the rating thing
		* @param $array			array with rating infos
		* @param $post			URL for $_P.O.S.T
		* @param $value			amount of stars to be selected by default
		* @param $disabled		disable the possibility to vote
		* @param $onevote		Let the user only vote once
		* @param $halfstars		Use half stars
		* @return CHAR
		*/
		public function StarRating($name, $array, $post, $value='', $disabled=false, $onevote=true, $halfstars=false){
			$lang_cancvote	= ($this->user->lang('lib_starrating_cancel')) ?$this->sanitize( $this->user->lang('lib_starrating_cancel')) : 'Cancel Rating';

			// Generate the JS Code
			$tmpopt		= array();
			$tmpopt[]	= (!$onevote) ? 'cancelValue: 99' : '';
			$tmpopt[]	= (!$onevote) ? 'cancelTitle: "Cancel Rating"' : '';
			$tmpopt[]	= 'callback: function(ui, type, value){
							$.post("'.$post.'", {'.$name.': value}, function(data){
								$("#ajax_response_'.$name.'").html(data);
							});
						}';
			$tmpopt[]	= ($disabled) ? 'disabled: true' : '';
			$tmpopt[]	= ($onevote) ? 'oneVoteOnly: true' : '';
			$tmpopt[]	= ($halfstars) ? 'split: 2' : '';
			$this->tpl->add_js('$("#'.$name.'_form").children().not(":radio").hide();
								$("#'.$name.'_form").stars('.$this->gen_options($tmpopt).');', 'docready');

			// Generate the HTML Code
			$html= '<form id="'.$name.'_form" action="'.$post.'" method="post">';
			foreach($array as $no=>$element){
				$select_me	= ($no == $value) ? 'checked="checked"' : '';
				$html  .= '<input type="radio" name="'.$name.'" value="'.$no.'" title="'.$element.'" id="'.$name.$no.'" '.$select_me.' /> <label for="'.$name.$no.'">'.$element.'</label><br />';
			}
			$html .= '<input type="submit" value="Rate it!" />
						</form>';
			$html .= '<p id="ajax_response_'.$name.'"></p>';
			return $html;
		}

		/**
		* Set the Value of the StarRating
		*
		* @param $name		Name/ID of the colorpicker field (must be unique)
		* @param $value		Value to be set
		*/
		public function StarRatingValue($name, $value){
			$this->tpl->add_js('$("#'.$name.'_form").stars("select", parseInt('.$value.'));');
		}

		/**
		* MultiSelect with checkboxes & filter
		*
		* @param $name		Name/ID of the colorpicker field (must be unique)
		* @param $value		List as an array
		* @param $selected	selected items as string or array
		* @param $height	height of the popup
		* @param $width		width of the popup
		* @param $options	Array with options [id, selections, no_animation, sel_text, header, single_select]
		* @return CHAR
		*/
		public function MultiSelect($name, $list, $selected, $options=''){
			$myID		= (isset($options['id'])) ? $options['id'] : "dw_".$name;
			if(empty($options['height'])) $options['height'] = 200;
			if(empty($options['width'])) $options['width'] = 200;
			$tmpopt		= array();
			$tmpopt[] = 'height: '.$options['height'];
			$tmpopt[] = 'minWidth: '.$options['width'];
			$tmpopt[] = 'checkAllText: "'.$this->sanitize($this->user->lang('cl_ms_checkall')).'"';
			$tmpopt[] = 'uncheckAllText: "'.$this->sanitize($this->user->lang('cl_ms_uncheckall')).'"';
			$tmpopt[] = 'noneSelectedText: "'.$this->sanitize($this->user->lang('cl_ms_noneselected')).'"';
			$tmpopt[] = 'selectedList: '.((isset($options['selections']) && $options['selections'] > 0) ? $options['selections'] : '5');
			$tmpopt[] = 'selectedText: "'.((isset($options['sel_text'])) ? $options['sel_text'] : $this->sanitize($this->user->lang('cl_ms_selection'))).'"';
			$tmpopt[] = 'multiple: '.((isset($options['single_select'])) ? 'false' : 'true');
			if(isset($options['selections'])){		$tmpopt[] = 'selectedList: '.$options['selections'];}
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
			if($list){
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
			$backgr = ($backgr) ? $backgr : '#'.$this->user->style['tr_color1'];
			$tmpopt		= array();
			$tmpopt[] = 'targeturl: "'.$url.'"';
			$tmpopt[] = 'items: '.$items;
			$tmpopt[] = 'Maxlength: '.$length;
			$tmpopt[] = 'loadingImg: "'.$this->path.'core/images/35-1.gif"';
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
		public function timePicker($name, $value='', $hour=0, $min=0, $sec=0, $enablesecs=false, $hourf=24){
			$tmpopt		= array();
			$tmpopt[] = 'hour: "'.$min.'"';
			$tmpopt[] = 'minute: "'.$hour.'"';;
			$tmpopt[] = 'second: "'.$sec.'"';
			$tmpopt[] = 'showSecond: '.(($enablesecs) ? 'true' : 'false');
			$tmpopt[] = 'ampm: '.(($hourf == 12) ? 'true' : 'false');
			
			$this->tpl->add_js("$('#id_".$name."').timepicker(".$this->gen_options($tmpopt).");", 'docready');
			return '<input name="'.$name.'" id="id_'.$name.'" value="'.$value.'" type="text" />';
		}

		/**
		* jqPlot: PieChart
		*
		* @param $id				ID of the css class (must be unique)
		* @param $data				Array with data
		* @param $title				Title of the chart
		* @param $options			The options array
		* @param $piemargin			With of the margin
		* @param $showlegend		true or false
		* @return TimePicker JS Code
		*/
		public function PieChart($id, $data, $title='', $options='', $piemargin=6, $showlegend=true, $showLabels = false){
			$js_array = $this->Array2jsArray($data);
			$own_colors = (isset($options['color_array']) && is_array($options['color_array']) && count($options['color_array']) > 0) ? 'seriesColors: [ '.$this->implode_wrapped('"','"', ',', $options['color_array']).' ],' : '';
			$show_legend = ($showlegend) ? 'true' : 'false';
			$mytitle = ($title) ? "title: '".$title."'," : '';
			$labels = ($showLabels) ? ", showDataLabels: true, dataLabelNudge: 80, dataLabels: 'label'" : '';
			$this->tpl->add_js("
				jqplotdata_".$id." = ".$js_array.";
				
				plot_".$id." = $.jqplot('".$id."', [jqplotdata_".$id."], {
								  ".$mytitle."
								  ".$own_colors."
								  grid: {background: '".((isset($options['background'])) ? $options['background'] : '#f5f5f5')."',borderColor: '".((isset($options['bordercolor'])) ? $options['bordercolor'] : '#999999')."', borderWidth: ".((isset($options['border'])) ? $options['border'] : '2.0').", shadow: ".((isset($options['shadow'])) ? 'true' : 'false')."},
								  seriesDefaults:{renderer:$.jqplot.PieRenderer, rendererOptions:{sliceMargin:".$piemargin.$labels."}}, 
								  legend:{show:".$show_legend.", escapeHtml:true}
								});", 'docready');
			
			return '<div id="'.$id.'"></div>';
		}
		
		/**
		* jqPlot: LineChart
		*
		* @param $id					ID of the css class (must be unique)
		* @param $data					Array with data
		* @param $title					Title of the chart
		* @param $height				The height of the Chart-div
		* @param $width					The widtj of the Chart-div
		* @param $options				The options array
		* @param $autoscale_xaxis		Turn on/off autoscaling for the xaxis (true/false)
		* @param $autoscale_yaxis		Turn on/off autoscaling for the yaxis (true/false)
		* @return HTML Code
		*/		
		public function LineChart($id, $data, $title='', $height='', $width='', $options='', $autoscale_xaxis=true, $autoscale_yaxis=true, $xrenderer=false){
			$js_array = $this->Array2jsArray($data, false);
			$mytitle = ($title) ? "title: '".$title."'," : '';

			switch ($xrenderer){
				case 'date':	$renderer = 'renderer:$.jqplot.DateAxisRenderer';break;
				default:		$renderer = 'renderer:$.jqplot.CategoryAxisRenderer';
			}

			$autoscale_xaxis = ($autoscale_xaxis) ? 'autoscale:true' : $renderer;
			$autoscale_yaxis = ($autoscale_yaxis) ? 'autoscale:true' : '';

			$this->tpl->add_js(" 
					line1 = ".$js_array.";
					plot_".$id." = $.jqplot('".$id."', [line1], {
					".$mytitle."
					axes:{xaxis:{".$autoscale_xaxis."},yaxis:{".$autoscale_yaxis."}},
					series:[{lineWidth:".((isset($options['lineWidth'])) ? $options['lineWidth'] : '4').", markerOptions:{style:'".((isset($options['markerStyle'])) ? $options['markerStyle'] : 'square')."'}}]
					});", 'docready');

			return '<div id="'.$id.'" style="'.(($height) ? 'height:'.$height.'px;' : '').' '.(($width) ? 'width:'.$width.'px;' : '').'"></div>';
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
				$content = (isset($options['contfunc'])) ? '{ text: function(api) { '.$content.' } }' : '"'.$content.'"';
				$extra_class = (isset($options['classes'])) ? ' '.$options['classes'] : '';
				$my		= (isset($options['my'])) ? $options['my'] : 'top center';
				$at		= (isset($options['at'])) ? $options['at'] : 'bottom center';
				$width	= (isset($options['width'])) ? 'width: '.$options['width'].',' : '';
				$this->tpl->add_js('$("'.$name.'").qtip({
					content: '.$content.',
					position: {
						at: "'.$at.'",
						my: "'.$my.'"
					},
					show: {
						event: "mouseenter"
					},
					style: {
						classes: "ui-tooltip-shadow'.$extra_class.'",
						tip: {
							corner: true
						},'.$width.'
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
			$this->tpl->add_js("$('#{$id1}{$this->dyndd_counter}').change(function() {
				$('#{$id2}{$this->dyndd_counter} option').remove();
				if($(this).val() > 0){
					mydata	= {$jsonname}[$(this).val()];
					if(typeof mydata != 'undefined'){
						$.each(mydata, function(i, val) {
							var opt = $('<option />');
							opt.appendTo($('#{$id2}{$this->dyndd_counter}')).text(val).val(i);
						});
					}
				}
			}).change();", 'docready');

			$output	= array(
				$this->html->DropDown($id1, $array1, $selected1, '','', 'input', $id1.$this->dyndd_counter),
				$this->html->DropDown($id2, $array2, '', '', '', 'input', $id2.$this->dyndd_counter)
			);
			
			$this->dyndd_counter++;
			return $output;
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

			$output[] = $this->html->DropDown($id1, $array1, $selected1, '','', 'input', $id1.$this->dyndd_counter);
			if(is_array($id2)){
				$jscode2 = '';
				$jscode2_p = '';
				$jscode2_c = 0;
				foreach($id2 as $ids2){
					$fieldname	= $ids2;
					$ids2		= preg_replace("~[^A-Za-z0-9]~", "", $ids2);
					$output[] = $this->html->DropDown($fieldname, $array2, '', '', '', 'input', $ids2.$this->dyndd_counter);

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
				$output[] = $this->html->DropDown($id2, $array2, '', '', '', 'input', $id2.$this->dyndd_counter);
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
		* Validate the Form
		* 
		* @param $formid		Id of the Form to validate
		* @return Tooltip
		*/
		public function Validate($formid, $mssgarray=false, $customcode=''){
			$messages	= (is_array($mssgarray)) ? ', '.$this->Array2jsArray($mssgarray, true) : '';
			$customcode	= ($customcode) ? ','.$customcode : '';
			$this->tpl->add_js("
				var validator_".$formid." = '';
					$(document).ready(function() {
						validator_".$formid." =$('#".$formid."').validate({
						ignore: '.ignore_validation'".$messages.$customcode."
					});
				});
			");
		}

		/**
		* Validate the Form
		* 
		* @param $formid		Id of the Form to validate
		* @return Tooltip
		*/
		public function ValidateTab($formid, $mssgarray=false){
			$messages = (is_array($mssgarray)) ? ', '.$this->Array2jsArray($mssgarray, true, "'") : '';
			$this->tpl->add_js('
					$("#'.$formid.'").validate({
						invalidHandler: function(form, validator) {
							var errors = validator.numberOfInvalids();
							if (errors) {
								var invalidPanels = $(validator.invalidElements()).closest(".ui-tabs-panel", form);
								if (invalidPanels.size() > 0) {
									$.each($.unique(invalidPanels.get()), function(){
										$(this).siblings(".ui-tabs-nav")
											.find("a[href=\'#" + this.id + "\']").parent().not(".ui-tabs-selected")
											.addClass("ui-state-error")
											.show("pulsate",{times: 3});
									});
								}
							}
						},
						unhighlight: function(element, errorClass, validClass) {
							$(element).removeClass(errorClass);
							$(element.form).find("label[for=" + element.id + "]").removeClass(errorClass);
							var $panel = $(element).closest(".ui-tabs-panel", element.form);
							if ($panel.size() > 0) {
								var removeerror = true;
								$panel.find("." + errorClass).each(function(index) {
									if($(this).text()){
										removeerror = false;
									}
								});
								if (removeerror === true && $panel.find("." + errorClass + ":visible").length === 0) {
									$panel.siblings(".ui-tabs-nav").find("a[href=\'#" + $panel[0].id + "\']")
										.parent().removeClass("ui-state-error");
								}
							}
						},
						ignore: ".ignore_validation"'.$messages.'
					});
			', 'docready');
		}

		/**
		* Reset the Form Validation
		* 
		* @param $formid		Id of the Form to validate
		* @return Tooltip
		*/
		public function ResetValidate($formid){
			$this->tpl->add_js("
				function reset_validator_".$formid."(){
					validator_".$formid.".resetForm();
				}
			");
		}

		public function imageUploader($id, $inputid, $imgname, $imgpath, $options=''){
			$img2beremoved = (isset($imgname) && is_file($imgpath.$imgname)) ? $this->encrypt->encrypt($imgpath.$imgname) : false;
			$this->tpl->add_js("
				$('#iuForm_".$id."').ajaxForm({
					beforeSubmit: function(a,f,o) {
						$('#image_".$id."').fadeOut();
					},
					success: function(data) {
						//console.log(data);
						var jsondata = $.parseJSON(data);
						if(!jsondata.error){
							// show the new image
							$('#image_".$id." img').load(function() {
								$('#image_".$id."').fadeIn('slow');
							}).attr('src', '".$this->pfh->FolderPath('imageupload', 'eqdkp')."'+jsondata.file+'?' + new Date().getTime());
							
							// remove the old image
							if($('#".$inputid."').val() != ''){
								$.post('".$this->root_path."exchange.php?out=imageupload_del', { 
									data: '".$img2beremoved."'
								});

							}
							
							// set the new image name to the inout field & close dialog
							$('#".$inputid."').val(jsondata.file)
							$('#iud_".$id."').dialog('close');
						}else{
							alert(jsondata.error);
						}
					}
				});
				$('#iubutton_".$id."_edit').click( function(){
					$('#iud_".$id."').dialog('open');
				});
				$('#ful_".$id."').fileinput();
				$('#iud_".$id."').dialog({
					height: 140,
					autoOpen: false,
					width: 400,
					modal: true,
					resizable: false,
					draggable: false,
					buttons: {
						'".$this->sanitize($this->user->lang('cancel'))."': function() {
							$( this ).dialog('close');
						},
						'".$this->sanitize($this->user->lang('imageuploader_buttondo'))."': function() {
							 $('#iuForm_".$id."').submit();
						}
					}
				});", 'docready');
			
			// The static Output directly after page body
			$imgwidth		= (isset($options['width'])) ? $options['width'] : 3000;
			$imgheight		= (isset($options['height'])) ? $options['height'] : 3000;
			$imgfilesize	= (isset($options['filesize'])) ? $options['filesize'] : 500000;
			$imgpreview		= (isset($imgname) && is_file($imgpath.$imgname)) ? $imgpath.$imgname : $this->root_path.((isset($options['noimgfile'])) ? $options['noimgfile'] : 'images/no_pic.png');
			list($previmgwidth, $previmgheight, $previmgtype, $previmgattr) = getimagesize($imgpreview);
			
			$imgprevheight	= (isset($options['prevheight'])) ? $options['prevheight'] : '120';

			$this->tpl->staticHTML('<div style="display:none;" id="iud_'.$id.'" title="'.$this->user->lang('imageuploader_wintitle').'">
					<form id="iuForm_'.$id.'" action="'.$this->root_path.'exchange.php?out=imageupload&amp;imgheight='.$imgheight.'&amp;imgwidth='.$imgwidth.'&amp;filesize='.$imgfilesize.'" method="post" enctype="multipart/form-data">
						'.$this->user->lang('imageuploader_file').': <input name="uploadfile" type="file" id="ful_'.$id.'" />
					</form>
				</div>');
			
			// the output
			$out	= '<div id="image_'.$id.'" class="imageuploader_image">';
			if ($previmgheight > $imgprevheight){
				$out .= '<a href="'.$imgpreview.'" rel="lightbox"><img src="'.$imgpreview.'" alt="'.$this->user->lang('imageuploader_preview').'" height="'.$imgprevheight.'"/></a>';
			} else {
				$out .= '<img src="'.$imgpreview.'" alt="'.$this->user->lang('imageuploader_preview').'" height="'.$imgprevheight.'"/>';
			}	
			$out .=	'</div>
					<input value="'.$this->user->lang('imageuploader_editbutton').'" type="button" id="iubutton_'.$id.'_edit" class="mainoption bi_edit" />';
			if(isset($options['deletelink']) && (isset($imgname) && is_file($imgpath.$imgname))){
				$out .= '<input value="" type="button" id="iubutton_'.$id.'_delete" class="mainoption bi_delete novalue" />';
				$this->tpl->add_js("$('#iubutton_".$id."_delete').click(function(){ location.href='".$options['deletelink']."' });", 'docready');
			}
			return $out;
		}

		public function MoveUploadedImage($tmpfile, $folderpath){
			$newfile	= explode('__', $tmpfile);
			if(isset($newfile[1]) && $newfile[1] != ''){
				$filename	= md5(rand().'_'.$newfile[1]).'.'.end(explode(".", $tmpfile));
				$this->pfh->FileMove($this->pfh->FolderPath('imageupload', 'eqdkp').$tmpfile, $folderpath.$filename);
				return $filename;
			}else{
				return $tmpfile;
			}
		}

		/**
		* Convert a PHP Array to JS Array
		* 
		* @param $formid		Id of the Form to validate
		* @return Tooltip
		*/
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
				$output  = "{";
				if(is_array($array) && count($array) > 0){
					foreach($array as $values){
						if($values != ''){
							$output .= $values.",";
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
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_jquery', jquery::$shortcuts);
?>