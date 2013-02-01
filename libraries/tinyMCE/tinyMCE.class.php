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
	public static $shortcuts = array('tpl', 'user', 'env' => 'environment');

	protected $tinymce_version = '3.5.6';
	protected $language	= 'en';
	protected $trigger	= array(
		'bbcode'	=> false,
		'normal'	=> false
	);
	
	public function __construct($rootpath=false, $nojsinclude=false){
		if($rootpath) $this->root_path = $rootpath;
		if(!$nojsinclude) $this->tpl->js_file($this->root_path.'libraries/tinyMCE/tiny_mce/jquery.tinymce.js');
		$this->language	= $this->user->lang('XML_LANG');
	}

	public function editor_bbcode($settings=false){
		if(!$this->trigger['bbcode']){
			$this->language	= ( !$settings['language'] ) ? $this->language : $settings['language'];

			$this->tpl->add_js('
				$(".mceEditor_bbcode").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->root_path.'libraries/tinyMCE/tiny_mce/tiny_mce.js",

					// General options
					plugins : "bbcode",
					//language : "'.$this->language.'",
					theme : "advanced",
					skin: "cirkuit",

					// Theme options
					theme_advanced_buttons1 : "bold,italic,underline,undo,redo,link,unlink,image,forecolor,styleselect,removeformat,cleanup,code",
					theme_advanced_buttons2 : "",
					theme_advanced_buttons3 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_styles : "Code=codeStyle;Quote=quoteStyle",
					entity_encoding : "raw",
					add_unload_trigger : false,
					remove_linebreaks : false,
					inline_styles : false,
					convert_fonts_to_spans : false
				});
			', 'docready');
			$this->trigger['bbcode'] = true;
		}
	}

	public function editor_normal($settings=false){
		if(!$this->trigger['normal']){
			$this->language	= (isset($settings['language'])) ? $settings['language'] : $this->language;
			$autoresize		= (isset($settings['autoresize'])) ? ',autoresize' : '';
			$resizing		= (isset($settings['autoresize'])) ? 'theme_advanced_resizing : true,' : '';
			$relative_url	= (isset($settings['relative_urls']) && $settings['relative_urls'] == false) ? 'relative_urls : false,' : '';
			$removeHost		= (isset($settings['remove_host']) && $settings['remove_host'] == false) ? 'remove_script_host : false,' : 'remove_script_host : true, convert_urls : true,';
			
			$this->tpl->add_js('
				$(".mceEditor").tinymce({
					// Location of TinyMCE script
					script_url : "'.$this->root_path.'libraries/tinyMCE/tiny_mce/tiny_mce.js",
					document_base_url : "'.$this->env->link.'",
					// General options
					theme : "advanced",
					skin: "cirkuit",
					//language : "'.$this->language.'",
					plugins : "table,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen,wordcount,eqdkp_uploader,eqdkp_lightbox,pages'.$autoresize.'",
			
					//extended_valid_elements : "img[class|!src|border:0|alt|title|width|height|style]",
					//invalid_elements : "strong,b,em,i",
	
					entity_encoding : "raw",
					
					// Theme options
					theme_advanced_buttons1_add : "fontselect,fontsizeselect,eqdkp_uploader,pages,eqdkp_item_code,eqdkp_embed_code,eqdkp_lightbox",
					theme_advanced_buttons2_add : "separator,insertdate,inserttime,preview,zoom,separator,forecolor,backcolor",
					theme_advanced_buttons2_add_before: "cut,copy,paste,separator,search,replace,separator",
					theme_advanced_buttons3_add_before : "tablecontrols,separator",
					theme_advanced_buttons3_add : "emotions,iespell,flash,advhr,separator,media",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					'.$resizing.$relative_url.$removeHost.'
										
					// Drop lists for link/image/media/template dialogs
					template_external_list_url : "lists/template_list.js",
					external_link_list_url : "lists/link_list.js",
					external_image_list_url : "lists/image_list.js",
					media_external_list_url : "lists/media_list.js",
			
					// Replace values for the template plugin
					template_replace_values : {
						username : "Some User",
						staffid : "991234"
					}
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

if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_tinyMCE', tinyMCE::$shortcuts);
?>