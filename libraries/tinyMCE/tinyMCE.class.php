<?php
 /*
 * Project:		eqdkpPLUS Libraries: TinyMCE
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2008
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		libraries:TinyMCE
 * @version		$Rev$
 * 
 * $Id$
 */
 
if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
} 

class tinyMCE extends gen_class {
	public static $shortcuts = array('tpl', 'user', 'env' => 'environment', 'pdh', 'config');

	protected $tinymce_version = '3.5.6';
	protected $language	= 'en';
	protected $trigger	= array(
		'bbcode'	=> false,
		'normal'	=> false
	);
	
	public function __construct($nojsinclude=false){
		if(!$nojsinclude) $this->tpl->js_file($this->server_path.'libraries/tinyMCE/tinymce/jquery.tinymce.min.js');
		$this->language	= $this->user->lang('XML_LANG');
		$this->tpl->add_js('var tinymce_eqdkp_lightbox_thumbnailsize = '.(($this->config->get('thumbnail_defaultsize')) ? $this->config->get('thumbnail_defaultsize') : 400).';');
	}

	public function editor_bbcode($settings=false){
		if(!$this->trigger['bbcode']){
			$this->language	= ( !$settings['language'] ) ? $this->language : $settings['language'];

			$this->tpl->add_js('
				$(".mceEditor_bbcode").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->server_path.'libraries/tinyMCE/tinymce/tinymce.min.js",

					// General options
					plugins: [
        "bbcode autolink link image charmap",
        "searchreplace visualblocks code fullscreen",
        "media table contextmenu paste textcolor paste"
    ],
					//language : "'.$this->language.'",
					theme : "modern",

					// Theme options
					
					entity_encoding : "raw",
					add_unload_trigger : false,
					remove_linebreaks : false,
					inline_styles : false,
					convert_fonts_to_spans : false,
					force_p_newlines : false,
					menubar: false,
					toolbar: "undo redo | bold italic underline | alignleft aligncenter alignright | forecolor | quote image link",
					statusbar : false,
				});
			', 'docready');
			$this->trigger['bbcode'] = true;
		}
	}

	public function editor_normal($settings=false){
		if(!$this->trigger['normal']){
			$this->language	= (isset($settings['language'])) ? $settings['language'] : $this->language;
			$autoresize		= (isset($settings['autoresize'])) ? ' autoresize' : '';
			$resizing		= (isset($settings['autoresize'])) ? 'theme_advanced_resizing : true,' : '';
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
			
			$this->tpl->add_js('
				$(".mceEditor").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->server_path.'libraries/tinyMCE/tinymce/tinymce.min.js",
					document_base_url : "'.$this->env->link.'",
					// General options
					theme: "modern",
					toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image eqdkp_lightbox eqdkp_filebrowser | preview media fullpage | forecolor emoticons | eqdkp_item",
					//language : "'.$this->language.'",
					 plugins: [
        "advlist autolink lists link image charmap preview anchor eqdkp_item eqdkp_lightbox eqdkp_filebrowser",
        "searchreplace visualblocks code fullscreen",
        "media table contextmenu paste textcolor emoticons'.$autoresize.'"
    ],

					entity_encoding : "raw",
					rel_list: [{value:"lightbox", text: "Lightbox" }],
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