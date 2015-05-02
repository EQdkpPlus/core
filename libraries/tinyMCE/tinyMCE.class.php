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

class tinyMCE extends gen_class {

	protected $tinymce_version = '4.1.7';
	protected $language	= 'en';
	protected $trigger	= array(
		'bbcode'	=> false,
		'normal'	=> false
	);
	protected $skin = 'lightgray';
	protected $theme = 'modern';
	
	public function __construct($nojsinclude=false){
		if(!$nojsinclude) $this->tpl->js_file($this->server_path.'libraries/tinyMCE/tinymce/jquery.tinymce.min.js');
		$this->language	= $this->user->lang('XML_LANG');
		$this->tpl->add_js('var tinymce_eqdkp_lightbox_thumbnailsize = '.(($this->config->get('thumbnail_defaultsize')) ? $this->config->get('thumbnail_defaultsize') : 400).';');
	}

	public function editor_bbcode($settings=false){
		if(!$this->trigger['bbcode']){
			
			//Language
			$lang = ( !$settings['language'] ) ? $this->language : $settings['language'];
			if (is_file($this->root_path.'libraries/tinyMCE/tinymce/langs/'.$lang.'.js')){
				$this->language	= ( !$settings['language'] ) ? $this->language : $settings['language'];
			} else $this->language = 'en';
			
			$arrHooks = (($this->hooks->isRegistered('tinymce_bbcode_setup')) ? $this->hooks->process('tinymce_bbcode_setup', array('js' => '', 'env' => $this->env), true): array());
			$strHooks = isset($arrHooks['js']) ? $arrHooks['js'] : '';
			$mention  = (isset($settings['mention']) && $settings['mention']) ? ' mention' : '';
			
			$this->tpl->add_js('
				function initialize_bbcode_editor(){
				$(".mceEditor_bbcode").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->server_path.'libraries/tinyMCE/tinymce/tinymce.min.js",

					// General options
					plugins: [
						"bbcode autolink link image charmap",
						"searchreplace visualblocks code fullscreen",
						"media paste textcolor'.$mention.'"
					],
					language : "'.$this->language.'",
					theme : "'.$this->theme.'",
					skin : "'.$this->skin.'",
					mentions: {
						source: function(query, process, delimiter){
							$.getJSON("'.$this->server_path.'libraries/tinyMCE/tinymce/plugins/mention/users.php", function (data) {
					          process(data);
					       });
						},
						insert: function(item) {
						    return "@\'" + item.name + "\'";
						}
					},
					
					setup: function(editor){
						'.$strHooks.'
					},

					// Theme options
					
					entity_encoding : "raw",
					add_unload_trigger : false,
					remove_linebreaks : false,
					inline_styles : false,
					convert_fonts_to_spans : false,
					force_p_newlines : false,
					menubar: false,
					relative_urls : false,
					remove_script_host : false,
					toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright |  bullist | forecolor | blockquote image link",
					statusbar : false,
				});
			}
			initialize_bbcode_editor();
			', 'docready');
			$this->trigger['bbcode'] = true;
		}
	}

	public function editor_normal($settings=false){
		if(!$this->trigger['normal']){
			//Language
			$lang = ( !$settings['language'] ) ? $this->language : $settings['language'];
			if (is_file($this->root_path.'libraries/tinyMCE/tinymce/langs/'.$lang.'.js')){
				$this->language	= ( !$settings['language'] ) ? $this->language : $settings['language'];
			} else $this->language = 'en';

			$autoresize		= (isset($settings['autoresize']) && $settings['autoresize']) ? ' autoresize' : '';
			$pageobjects	= (isset($settings['pageobjects']) && $settings['pageobjects']) ? ' eqdkp_pageobject' : '';
			$readmore		= (isset($settings['readmore']) && !$settings['readmore']) ? '' : ' eqdkp_pagebreak_readmore';
			$gallery 		= (isset($settings['gallery']) && $settings['gallery']) ? ' eqdkp_gallery' : '';
			$raidloot		= (isset($settings['raidloot']) && $settings['raidloot']) ? ' eqdkp_raidloot eqdkp_chars' : '';
			$relative_url	= (isset($settings['relative_urls']) && $settings['relative_urls'] == false) ? 'relative_urls : false,' : '';
			$removeHost		= (isset($settings['remove_host']) && $settings['remove_host'] == false) ? 'remove_script_host : false,' : 'remove_script_host : true, convert_urls : true,';
			
			$link_list = '';
			if (isset($settings['link_list'])){
				//Articles & Categories
				$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
				foreach($arrCategoryIDs as $cid){
					if (!$this->pdh->get('article_categories', 'published', array($cid))) continue;
					
					if ($cid != 1) $arrCategories[] = array('text' => $this->pdh->get('article_categories', 'name', array($cid)), 'id' => $cid);
					$arrArticles = $this->pdh->get('articles', 'id_list', array($cid));
					foreach($arrArticles as $articleID){
						if (!$this->pdh->get('articles', 'published', array($articleID))) continue;
						$arrItems[$cid][] = array('text' => $this->pdh->get('articles', 'title', array( $articleID)), 'id' => $articleID);
					}
				}

				$link_list = '
				link_list : [{text: "'.$this->user->lang('articles').'", value: "", menu: [';
					foreach($arrCategories as $val){
						$link_list .= '{text: "'.$val['text'].'", value: "{{category_url_plain::'.$val['id'].'}}", menu: [';
							if(!isset($arrItems[$val['id']])) $arrItems[$val['id']] = array();
							$link_list .= '{text: "'.$val['text'].'", value: "{{category_url_plain::'.$val['id'].'}}"},';
							foreach($arrItems[$val['id']] as $value){
								$link_list .= '{text: "'.$value['text'].'", value: "{{article_url_plain::'.$value['id'].'}}"},';
							}
							
						$link_list .= ']},';
					}
					//$link_list .= '{}';
				$link_list .= '
				]}],';
			}

			$arrHooks = (($this->hooks->isRegistered('tinymce_normal_setup')) ? $this->hooks->process('tinymce_normal_setup', array('js' => '', 'env' => $this->env), true): array());
			$strHooks = isset($arrHooks['js']) ? $arrHooks['js'] : '';
								
			$this->tpl->add_js('
				$(".mceEditor").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->server_path.'libraries/tinyMCE/tinymce/tinymce.min.js",
					document_base_url : "'.$this->env->link.'",
					// General options
					theme : "'.$this->theme.'",
					skin : "'.$this->skin.'",
					image_advtab: true,
					toolbar: "insertfile undo redo | styleselect | fullscreen | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media eqdkp_lightbox eqdkp_filebrowser | eqdkp_readmore eqdkp_pagebreak eqdkp_pageobject | forecolor emoticons | eqdkp_item eqdkp_gallery eqdkp_raidloot eqdkp_chars | custom_buttons",
					language : "'.$this->language.'",
					plugins: [
					 	"advlist autolink lists link image charmap preview anchor eqdkp_item eqdkp_lightbox eqdkp_filebrowser eqdkp_easyinsert",
						"searchreplace visualblocks code fullscreen",
						"media table contextmenu paste textcolor emoticons'.$autoresize.$pageobjects.$readmore.$gallery.$raidloot.'"
					],

					entity_encoding : "raw",
					rel_list: [{value:"", text: "" }, {value:"lightbox", text: "Lightbox" }, {value:"nofollow", text: "nofollow" }],
					extended_valid_elements : "p[class|id|style|data-sort|data-folder|data-id|title], script[type|lang|src]",
					setup: function(editor){
						'.$strHooks.'
					},
					'.$link_list.'
					file_browser_callback : function(field_name, url, type, win){
						var elfinder_url = "'.$this->env->link.'libraries/elfinder/elfinder.php'.$this->SID.'";    // use an absolute path!
						var cmsURL = elfinder_url;    // script URL - use an absolute path!
						if (cmsURL.indexOf("?") < 0) {
							//add the type as the only query parameter
							cmsURL = cmsURL + "?editor=tiny&type=" + type + "&field=" + field_name;
						}
						else {
							//add the type as an additional query parameter
							// (PHP session ID is now included if there is one at all)
							cmsURL = cmsURL + "&editor=tiny&type=" + type + "&field=" + field_name;
						}

						tinyMCE.activeEditor.windowManager.open({
							file : cmsURL,
							title : "File Browser",
							width : 900,
							height : 450,
							resizable : "yes",
							inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
							popup_css : false, // Disable TinyMCEs default popup CSS
							close_previous : "no"
						}, {
							window : win,
							input : field_name
						});
						return false;
					},
					
					'.$resizing.$relative_url.$removeHost.'

				});
						
			', 'docready');

			$this->trigger['normal'] = true;
		}
	}
	
	public function inline_editor_simple($selector, $settings=array()){
		if(!$this->trigger['inline_simple.'.$selector]){
			//Language
			$lang = ( !$settings['language'] ) ? $this->language : $settings['language'];
			if (is_file($this->root_path.'libraries/tinyMCE/tinymce/langs/'.$lang.'.js')){
				$this->language	= ( !$settings['language'] ) ? $this->language : $settings['language'];
			} else $this->language = 'en';
			
			$strSetup = (isset($settings['setup'])) ? $settings['setup'] : '';
			$strAutofocus = (isset($settings['autofocus']) && $settings['autofocus']) ? 'true' : 'false';
			$blnStart = (isset($settings['start_onload'])) ? $settings['start_onload'] : true;
			
			//Hooks
			$arrHooks = (($this->hooks->isRegistered('tinymce_inline_simple_setup')) ? $this->hooks->process('tinymce_inline_simple_setup', array('js' => '', 'selector' => $selector,  'env' => $this->env), true): array());
			$strHooks = isset($arrHooks['js']) ? $arrHooks['js'] : '';
			
			$tinyid = md5($selector);
			
			$this->tpl->add_js('
				function tinyinlinesimple_'.$tinyid.'() {
					$("'.$selector.'").tinymce({
						// Location of TinyMCE script
						script_url : "'.$this->server_path.'libraries/tinyMCE/tinymce/tinymce.min.js",
						document_base_url : "'.$this->env->link.'",
						
						// General options
						language : "'.$this->language.'",
						theme : "'.$this->theme.'",
						skin : "'.$this->skin.'",
						inline: true,
						toolbar: "undo redo",
						menubar: false,
						plugins: ["save"],
						setup: function(editor) {
							'.$strSetup.$strHooks.'
						},
						entity_encoding : "raw",
						relative_urls : false,
						remove_script_host : false,
						auto_focus: '.$strAutofocus.'
					});
				}
				'.(($blnStart) ? 'tinyinlinesimple_'.$tinyid.'()' : '').'
			', 'docready');
		
			$this->trigger['inline_simple.'.$selector] = true;
		}
	}
	
	public function inline_editor($selector, $settings=false, $blnStart=true){
		if(!$this->trigger['inline.'.$selector]){
			//Language
			$lang = ( !$settings['language'] ) ? $this->language : $settings['language'];
			if (is_file($this->root_path.'libraries/tinyMCE/tinymce/langs/'.$lang.'.js')){
				$this->language	= ( !$settings['language'] ) ? $this->language : $settings['language'];
			} else $this->language = 'en';
			
			$autoresize		= (isset($settings['autoresize']) && $settings['autoresize']) ? ' autoresize' : '';
			$pageobjects	= (isset($settings['pageobjects']) && $settings['pageobjects']) ? ' eqdkp_pageobject' : '';
			$readmore		= (isset($settings['readmore']) && !$settings['readmore']) ? '' : ' eqdkp_pagebreak_readmore';
			$gallery 		= (isset($settings['gallery']) && $settings['gallery']) ? ' eqdkp_gallery' : '';
			$raidloot		= (isset($settings['raidloot']) && $settings['raidloot']) ? ' eqdkp_raidloot eqdkp_chars' : '';
			$relative_url	= (isset($settings['relative_urls']) && $settings['relative_urls'] == false) ? 'relative_urls : false,' : '';
			$removeHost		= (isset($settings['remove_host']) && $settings['remove_host'] == false) ? 'remove_script_host : false,' : 'remove_script_host : true, convert_urls : true,';
			
			$strSetup = (isset($settings['setup'])) ? $settings['setup'] : '';
			$strAutofocus = (isset($settings['autofocus']) && $settings['autofocus']) ? 'true' : 'false';
			$blnStart = (isset($settings['start_onload'])) ? $settings['start_onload'] : true;
			
			$link_list = '';
			if (isset($settings['link_list'])){
				//Articles & Categories
				$arrCategoryIDs = $this->pdh->sort($this->pdh->get('article_categories', 'id_list', array()), 'article_categories', 'sort_id', 'asc');
				foreach($arrCategoryIDs as $cid){
					if (!$this->pdh->get('article_categories', 'published', array($cid))) continue;
						
					if ($cid != 1) $arrCategories[] = array('text' => $this->pdh->get('article_categories', 'name', array($cid)), 'id' => $cid);
					$arrArticles = $this->pdh->get('articles', 'id_list', array($cid));
					foreach($arrArticles as $articleID){
						if (!$this->pdh->get('articles', 'published', array($articleID))) continue;
						$arrItems[$cid][] = array('text' => $this->pdh->get('articles', 'title', array( $articleID)), 'id' => $articleID);
					}
				}
	
				$link_list = '
				link_list : [{text: "'.$this->user->lang('articles').'", value: "", menu: [';
				foreach($arrCategories as $val){
					$link_list .= '{text: "'.$val['text'].'", value: "{{category_url::'.$val['id'].'}}", menu: [';
					if(!isset($arrItems[$val['id']])) $arrItems[$val['id']] = array();
					$link_list .= '{text: "'.$val['text'].'", value: "{{category_url::'.$val['id'].'}}"},';
					foreach($arrItems[$val['id']] as $value){
						$link_list .= '{text: "'.$value['text'].'", value: "{{article_url::'.$value['id'].'}}"},';
					}

					$link_list .= ']},';
				}
				//$link_list .= '{}';
				$link_list .= '
				]}],';
			}
			
			$arrHooks = (($this->hooks->isRegistered('tinymce_inline_setup')) ? $this->hooks->process('tinymce_inline_setup', array('js' => '', 'selector' => $selector,  'env' => $this->env), true): array());
			$strHooks = isset($arrHooks['js']) ? $arrHooks['js'] : '';
			
			$tinyid = md5($selector);
				
			$this->tpl->add_js('
				function tinyinline_'.$tinyid.'(){	
				$("'.$selector.'").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->server_path.'libraries/tinyMCE/tinymce/tinymce.min.js",
					document_base_url : "'.$this->env->link.'",
					// General options
					inline: true,
					theme : "'.$this->theme.'",
					skin : "'.$this->skin.'",
					image_advtab: true,
					toolbar: "insertfile undo redo | styleselect | fullscreen | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media eqdkp_lightbox eqdkp_filebrowser | eqdkp_readmore eqdkp_pagebreak eqdkp_pageobject | forecolor emoticons | eqdkp_item eqdkp_gallery eqdkp_raidloot eqdkp_chars",
					language : "'.$this->language.'",
					 plugins: [
					 	"advlist autolink lists link image charmap preview anchor eqdkp_item eqdkp_lightbox eqdkp_filebrowser eqdkp_easyinsert",
						"searchreplace visualblocks code fullscreen",
						"save media table contextmenu paste textcolor emoticons'.$autoresize.$pageobjects.$readmore.$gallery.$raidloot.'"
					],
					entity_encoding : "raw",
					rel_list: [{value:"", text: "" }, {value:"lightbox", text: "Lightbox" }, {value:"nofollow", text: "nofollow" }],
					extended_valid_elements : "p[class|id|style|data-sort|data-folder|data-id|title], script[type|lang|src]",
					'.$link_list.'
					file_browser_callback : function(field_name, url, type, win){
						var elfinder_url = "'.$this->env->link.'libraries/elfinder/elfinder.php'.$this->SID.'";    // use an absolute path!
						var cmsURL = elfinder_url;    // script URL - use an absolute path!
						if (cmsURL.indexOf("?") < 0) {
							//add the type as the only query parameter
							cmsURL = cmsURL + "?editor=tiny&type=" + type + "&field=" + field_name;
						}
						else {
							//add the type as an additional query parameter
							// (PHP session ID is now included if there is one at all)
							cmsURL = cmsURL + "&editor=tiny&type=" + type + "&field=" + field_name;
						}
	
						tinyMCE.activeEditor.windowManager.open({
							file : cmsURL,
							title : "File Browser",
							width : 900,
							height : 450,
							resizable : "yes",
							inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
							popup_css : false, // Disable TinyMCEs default popup CSS
							close_previous : "no"
						}, {
							window : win,
							input : field_name
						});
						return false;
					},
					setup: function(editor) {	
						'.$strSetup.$strHooks.'
					},
					save_onsavecallback: function() {
					},
					auto_focus: '.$strAutofocus.',
			
					'.$resizing.$relative_url.$removeHost.'
	
				});
			}
			'.(($blnStart) ? 'tinyinline_'.$tinyid.'()' : '').'
			', 'docready');
	
			$this->trigger['inline.'.$selector] = true;
		}
	}

	public function textbox($input, $settings){
		$text_cols	= ( !$settings['textbox_cols'] ) ? "85" : $settings['textbox_cols'];
		$text_rows	= ( !$settings['textbox_rows'] ) ? "15" : $settings['textbox_rows'];
		$textbox	= ( !$settings['textbox_name'] ) ? "content" :$settings['textbox_name'];

		return '<textarea name="'.$textbox.'" class="mceEditor" cols="'.$text_cols.'" rows="'.$text_rows.'">'.$input.'</textarea>';
	}

	public function encode($input){
		return addslashes(htmlentities($input));
	}

	public function decode($input){
		return html_entity_decode(stripslashes($input));
	}
}

?>