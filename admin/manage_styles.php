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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Styles extends page_generic{
	public static $shortcuts = array('objStyles'=> 'styles');

	public $style = array();

	public function __construct(){
		$this->user->check_auth('a_extensions_man');
		$handler = array(
			'version_update' => array('process' => 'version_update'),
			'template_edit_button' => array('process' => 'edit_template', 'csrf'=>true),
			'template_reset_button' => array('process' => 'reset_template', 'csrf'=>true),

			'mode' 		=> array('process' => 'edit'),
			'update'	=> array('process' => 'update'),
			'export_changed_files'	=> array('process' => 'exportChangedFiles'),
			'diff'		=> array('process' => 'diff_viewer'),
		);
		parent::__construct(false, $handler, false, null, '', 'styleid');

		// Variables
		$defaults = array(
			'attendees_columns'	=> 8,
		);

		// Build the style array
		if ( $this->url_id ){
			$this->classcolor	= $this->pdh->get('class_colors', 'class_colors', array($this->url_id));
			$this->style		= $this->pdh->get('styles', 'styles', array($this->url_id));
			if(!is_array($this->style)){
				$this->core->message($this->user->lang('error_invalid_style'), $this->user->lang('error'), 'red');
				$this->display();
				return;
			}
		}

		$this->process();
	}

	public function exportChangedFiles() {
		$this->objStyles->exportChangedFiles($this->url_id);
	}

	public function reset_template(){
		if ($this->in->get('template_dd') != "" ){

			$filename = base64_decode($this->in->get('template_dd'));
			$admin_folder = (substr($filename, 0, 6) == 'admin/') ? '/admin' : '';
			$storage_folder  = $this->pfh->FolderPath('templates/'.$this->style['template_path'].$admin_folder, 'eqdkp');

			$this->pfh->Delete($storage_folder.$filename);
			$this->edit();
			return;
		}
	}
	
	public function diff_viewer(){
		$strFilename = base64_decode($this->in->get('diff', ''));
		if (!$strFilename) return;
		$arrRenderer = array('side_by_side'=>'side_by_side', 'inline'=>'inline', 'unified'=>'unified', 'raw'=>'raw');
		$content = '';
		$blnRenderer = true;
		
		switch($this->in->get('type')){
			/*
			//Show the new file
			case 'new': {
				$new_file = file_get_contents($this->root_path.'templates/'.$this->style['template_path'].'/'.$strFilename);
				$content = '<div class="showfile">'.nl2br(htmlspecialchars($new_file)).'</div>';
				$blnRenderer = false;
			}
			break;
			
			//Show modified file
			case 'mod': {
				$mod_file = file_get_contents($this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').'/'.$strFilename);
				$content = '<div class="showfile">'.nl2br(htmlspecialchars($mod_file)).'</div>';
				$blnRenderer = false;
			}
			break;
			*/
			
			//Default: show merged diff
			default: {
				$strRenderer = $this->in->get('renderer', 'side_by_side');

				if ($strFilename){
				
					require_once($this->root_path.'libraries/diff/diff.php');
					require_once($this->root_path.'libraries/diff/engine.php');
					require_once($this->root_path.'libraries/diff/renderer.php');
					
					$this->tpl->css_file($this->root_path.'libraries/diff/diff.css');
					
					$mod_file = file_get_contents($this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').'/'.$strFilename);
					$new_file = file_get_contents($this->root_path.'templates/'.$this->style['template_path'].'/'.$strFilename);

					$diff = new diff($new_file, $mod_file, true);
					if (in_array($strRenderer, $arrRenderer)){
						$render_class = 'diff_renderer_'.$strRenderer;
					} else {
						$render_class = 'diff_renderer_side_by_side';
					}
					
					$renderer = new $render_class();
						
					$content = $renderer->get_diff_content($diff);
					
				}
			}
		}
		
		$render_dd = new hdropdown('renderer', array('options' => $arrRenderer, 'default' => 'side_by_side', 'tolang' => true, 'js' => 'onchange="this.form.submit();"'));
		$render_dd->value = $render_dd->inpval();
		$this->tpl->assign_vars(array(
			'CONTENT'	=> $content,
			'FILENAME'	=> $strFilename,
			'RENDERER_DROPDOWN' => $render_dd,
			'ENCODED_FILENAME' => $this->in->get('diff', ''),
			'S_RENDERER' => $blnRenderer,
		));
	
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('liveupdate_show_differences'),
			'template_file'		=> 'admin/diff_viewer.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}

	public function edit_template(){
		if ($this->in->get('template_dd') != "" && $this->in->get('template_edit') != ""){
			$filename = base64_decode($this->in->get('template_dd'));

			$admin_folder = (substr($filename, 0, 6) == 'admin/') ? '/admin' : '';
			$filename = str_replace('admin/', '', $filename);
			
			$storage_folder  = $this->pfh->FolderPath('templates/'.$this->style['template_path'].$admin_folder, 'eqdkp');
			$this->pfh->FilePath($storage_folder.$filename);
			$this->pfh->putContent($storage_folder.$filename, $this->in->get('template_edit', '', 'raw'));

			$this->core->message( $this->user->lang('edit_template_suc'), $this->user->lang('save_suc'), 'green');

			$this->tpl->delete_cache($this->style['template_path']);
		} else {
			$this->core->message( $this->user->lang('edit_template_nosuc'), $this->user->lang('save_nosuc'), 'red');
		}

		$this->edit();
	}


	// ---------------------------------------------------------
	// Process Update
	// ---------------------------------------------------------
	public function update(){
		// add the class colors to the database
		$this->objStyles->ClassColorManagement($this->url_id, false);
		$this->pdh->put('styles', 'update_style', array($this->url_id, $this->get_data()));
		$this->pdh->process_hook_queue();
		$this->style = $this->pdh->get('styles', 'styles', array($this->url_id));		
		
		//Delete Template Cache
		$this->objStyles->deleteStyleCache($this->style['template_path']);
		
		$this->core->message( $this->user->lang('admin_update_style_success'), $this->user->lang('success'), 'green');
	}

	private function get_data() {
		$portal_width = (strlen($this->in->get('portal_width'))) ? $this->in->get('portal_width').$this->in->get('dd_portal_width') : '';
		$column_left_width = (strlen($this->in->get('column_left_width'))) ? $this->in->get('column_left_width').$this->in->get('dd_column_left_width') : '';
		$column_right_width = (strlen($this->in->get('column_right_width'))) ? $this->in->get('column_right_width').$this->in->get('dd_column_right_width') : '';
				
		$data = array(
			'style_name'			=> $this->in->get('style_name', $this->style['style_name']),
			'style_version'			=> $this->in->get('style_version', $this->style['style_version']),
			'body_background'		=> $this->in->get('body_background', $this->style['body_background']),
			'body_link'				=> $this->in->get('body_link', $this->style['body_link']),
			'body_link_style'		=> $this->in->get('body_link_style', $this->style['body_link_style']),
			'body_hlink'			=> $this->in->get('body_hlink', $this->style['body_hlink']),
			'body_hlink_style'		=> $this->in->get('body_hlink_style', $this->style['body_hlink_style']),
			'header_link'			=> $this->in->get('header_link', $this->style['header_link']),
			'header_link_style'		=> $this->in->get('header_link_style', $this->style['header_link_style']),
			'header_hlink'			=> $this->in->get('header_hlink', $this->style['header_hlink']),
			'header_hlink_style'	=> $this->in->get('header_hlink_style', $this->style['header_hlink_style']),
			'tr_color1'				=> $this->in->get('tr_color1', $this->style['tr_color1']),
			'tr_color2'				=> $this->in->get('tr_color2', $this->style['tr_color2']),
			'th_color1'				=> $this->in->get('th_color1', $this->style['th_color']),
			'fontface1'				=> $this->in->get('fontface1', $this->style['fontface1']),
			'fontface2'				=> $this->in->get('fontface2', $this->style['fontface2']),
			'fontface3'				=> $this->in->get('fontface3', $this->style['fontface3']),
			'fontsize1'				=> $this->in->get('fontsize1', $this->style['fontsize1']),
			'fontsize2'				=> $this->in->get('fontsize2', $this->style['fontsize2']),
			'fontsize3'				=> $this->in->get('fontsize3', NULL),
			'fontcolor1'			=> $this->in->get('fontcolor1'),
			'fontcolor2'			=> $this->in->get('fontcolor2'),
			'fontcolor3'			=> $this->in->get('fontcolor3'),
			'fontcolor_neg'			=> $this->in->get('fontcolor_neg'),
			'fontcolor_pos'			=> $this->in->get('fontcolor_pos'),
			'table_border_width'	=> $this->in->get('table_border_width', NULL),
			'table_border_color'	=> $this->in->get('table_border_color'),
			'table_border_style'	=> $this->in->get('table_border_style'),
			'input_color'			=> $this->in->get('input_color'),
			'input_border_width'	=> $this->in->get('input_border_width', NULL),
			'input_border_color'	=> $this->in->get('input_border_color'),
			'input_border_style'	=> $this->in->get('input_border_style'),
				
			'background_type'		=> $this->in->get('background_type', 0),
			'background_pos'		=> $this->in->get('background_pos'),
			'background_img'		=> $this->in->get('background_img'),
				
			'portal_width'			=> $portal_width,
			'column_left_width'		=> $column_left_width,
			'column_right_width'	=> $column_right_width,

			'attendees_columns'		=> $this->in->get('attendees_columns'),
			'logo_position'			=> $this->in->get('logo_position', 'center'),
			
			'css_file'				=> $this->in->get('css_file'),
		);
		return $data;
	}

	public function edit(){
		$text_decoration = array(
			'none'			=> 'none',
			'underline'		=> 'underline',
			'overline'		=> 'overline',
			'line-through'	=> 'line-through',
			'blink'			=> 'blink'
		);
		$border_style = array(
			'none'		=> 'none',
			'hidden'	=> 'hidden',
			'dotted'	=> 'dotted',
			'dashed'	=> 'dashed',
			'solid'		=> 'solid',
			'double'	=> 'double',
			'groove'	=> 'groove',
			'ridge'		=> 'ridge',
			'inset'		=> 'inset',
			'outset'	=> 'outset'
		);

		$width_options = array(
			'px'	=> 'px',
			'%'		=> '%',
		);

		$logo_positions = array(
			'center'=>	$this->user->lang('logo_position_center'),
			'right'	=>	$this->user->lang('portalplugin_right'),
			'left'	=>	$this->user->lang('portalplugin_left'),
			'none'	=>	$this->user->lang('info_opt_ml_0'),
		);
		
		$arrUsedVariables = $this->get_used_variables($this->style['template_path']);		
		
		// Attendee columns
		for ($i = 1; $i < 11; $i++){
			$attendee_colums[$i] = $i;
		}

		// Class Colors
		$arrClasses = $this->game->get_primary_classes();
		foreach($arrClasses as $class_id => $class_name){
			$this->tpl->assign_block_vars('classes', array(
				'NAME'		=> $class_name,
				'CPICKER'	=> $this->jquery->colorpicker('classc_'.$class_id, $this->game->get_class_color($class_id, $this->url_id)),
			));
		}

		//First: the base templates
		$arrBaseTemplates = $this->objStyles->scan_templates($this->core->root_path . 'templates/base_template/');
		//Now the files from the template
		$arrTemplates = $this->objStyles->scan_templates($this->core->root_path . 'templates/'.$this->style['template_path'].'/');
		$arrTemplates = array_merge($arrBaseTemplates, $arrTemplates);
		
		//Scan Plugin Templates
		$arrPlugins = $this->pm->get_plugins();
		foreach($arrPlugins as $strPlugin){
			$pluginTemplatePath = $this->pm->get_data($strPlugin, 'template_path');
			//First: base_template
			$arrPluginBaseTemplates = $this->objStyles->scan_templates($this->core->root_path.$pluginTemplatePath.'base_template', $this->core->root_path);
			$arrPluginTemplates = $this->objStyles->scan_templates($this->core->root_path.$pluginTemplatePath.$this->style['template_path'], $this->core->root_path);
			$arrPluginTemplates = array_merge($arrPluginBaseTemplates, $arrPluginTemplates);
			
			$arrPluginTemplatesCleaned = array();
			foreach($arrPluginTemplates as $key => $val){
				$strKey = str_replace('templates/base_template/', '', $key);
				$strKey = str_replace('templates/'.$this->style['template_path'], '', $strKey);
				$arrPluginTemplatesCleaned[$strKey] = $val;
			}
			
			$arrTemplates = array_merge($arrTemplates, $arrPluginTemplatesCleaned);
		}
		
		$files[""] = "";
		foreach ($arrTemplates as $path => $name){
			$files[base64_encode($path)] = $path;
		}
		

		//Read an spezific template-file to edit
		$editor_type = 'html_js';
		if ($this->in->get('template') != "" && !is_numeric(base64_decode($this->in->get('template')))){
			$filename = base64_decode($this->in->get('template'));

			if(substr($filename, 0, 8) === 'plugins/'){
				$realFilename = str_replace("plugins/", "", $filename);
				$intFirstSlash = (strpos($realFilename, '/'));
				$strPluginName = substr($realFilename, 0, $intFirstSlash);
				$realFilename = str_replace($strPluginName.'/', "", $realFilename);

				$data_path = $this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').$filename;
				$template_path = $this->core->root_path.'templates/'.$this->style['template_path'].'/'.$filename;
				$plugin_path = $this->core->root_path.'plugins/'.$strPluginName.'/templates/'.$this->style['template_path'].'/'.$realFilename;
				$base_template_path =  $this->core->root_path.'plugins/'.$strPluginName.'/templates/base_template/'.$realFilename;
				
				if(file_exists($data_path)){
					$filename = $data_path;
				} elseif(file_exists($template_path)){
					$filename = $template_path;
				} elseif(file_exists($plugin_path)){
					$filename = $plugin_path;
				} else {
					$filename = $base_template_path;
				}
				
			} else {
				if (file_exists($this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').$filename)){
					$filename = $this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').$filename;
				} elseif (file_exists($this->core->root_path . 'templates/'.$this->style['template_path'].'/'.$filename)){
					$filename = $this->core->root_path . 'templates/'.$this->style['template_path'].'/'.$filename;
				} else {
					$filename = $this->core->root_path . 'templates/base_template/'.$filename;
				}
				
			}

			if (file_exists($filename)){
				$contents = file_get_contents($filename);
				$file_ext = pathinfo($filename, PATHINFO_EXTENSION);
				$editor_type = ($file_ext == 'css') ? 'css' : 'html_js';
				$select_tab = 3;
			}
		}

		$this->confirm_delete($this->user->lang('confirm_delete_style'));
		$this->jquery->Tab_header('style_tabs', true);
		if(isset($select_tab) && $select_tab > 0){ $this->jquery->Tab_Select('style_tabs', $select_tab); }
		
		$this->jquery->fileBrowser('admin', 'image');

		$this->tpl->assign_vars(array(
			// Form vars
			'F_ADD_STYLE'			=> 'manage_styles.php' . $this->SID.'&amp;update=true',
			'STYLE_ID'				=> $this->url_id,
			'DD_EDIT_TEMPLTES'		=> new hdropdown('template_dd', array('options' => $files, 'value' => $this->in->get('template'), 'js' => 'onchange="this.form.template.value=this.value;this.form.action =\'manage_styles.php'.$this->SID.'&amp;edit=true&amp;styleid=' . $this->url_id.'\'; this.form.submit();"')),
			'TEMPLATE_CONTENT'		=> $this->jquery->CodeEditor('template_edit', ((isset($contents)) ? htmlentities($contents) : ''), $editor_type),

			// Form Values
			'STYLE_NAME'			=> $this->style['style_name'],
			'STYLE_CODE'			=> (isset($this->style['style_code'])) ? $this->style['style_code'] : '',
			'STYLE_AUTHOR'			=> $this->style['style_author'],
			'STYLE_CONTACT'			=> $this->style['style_contact'],
			'STYLE_VERSION'			=> $this->style['style_version'],
			'FONTFACE1'				=> $this->style['fontface1'],
			'FONTFACE2'				=> $this->style['fontface2'],
			'FONTFACE3'				=> $this->style['fontface3'],
			'FONTSIZE1'				=> $this->style['fontsize1'],
			'FONTSIZE2'				=> $this->style['fontsize2'],
			'FONTSIZE3'				=> $this->style['fontsize3'],
			
			'FONTFACE1_DISABLED'	=> ((!in_array('fontface1', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'FONTFACE2_DISABLED'	=> ((!in_array('fontface2', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'FONTFACE3_DISABLED'	=> ((!in_array('fontface3', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'FONTSIZE1_DISABLED'	=> ((!in_array('fontsize1', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'FONTSIZE2_DISABLED'	=> ((!in_array('fontsize2', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'FONTSIZE3_DISABLED'	=> ((!in_array('fontsize3', $arrUsedVariables)) ? 'disabled="disabled"' : ''),

			'TABLE_BORDER_WIDTH'	=> $this->style['table_border_width'],
			'INPUT_BORDER_WIDTH'	=> $this->style['input_border_width'],

			'TABLE_BORDER_WIDTH_DISABLED' 	=> ((!in_array('table_border_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'INPUT_BORDER_WIDTH_DISABLED'	=> ((!in_array('input_border_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''), 
				
			'BACKGROUND_IMG'		=> $this->style['background_img'],
			'CSS_FILE'				=> $this->style['css_file'],
			
			'STYLE_PORTAL_WIDTH'	=> (isset($this->style['portal_width'])) ? (int)$this->style['portal_width'] : 1100,
			'STYLE_COLUMN_LEFT_WIDTH'	=> (isset($this->style['column_left_width'])) ? (int)$this->style['column_left_width'] : 180,
			'STYLE_COLUMN_RIGHT_WIDTH'	=> (isset($this->style['column_right_width'])) ? (int)$this->style['column_right_width'] : 180,
			'STYLE_PORTAL_WIDTH_DISABLED' => ((!in_array('portal_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'STYLE_COLUMN_LEFT_DISABLED' => ((!in_array('column_left_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'STYLE_COLUMN_RIGHT_DISABLED' => ((!in_array('column_right_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),	
			'DD_PORTAL_WIDTH'		=> new hdropdown('dd_portal_width', array('options' => $width_options, 'value' => ((strpos($this->style['portal_width'], '%') !== false) ? '%' : 'px'),  'disabled' => ((!in_array('portal_width', $arrUsedVariables)) ? true : false))),
			'DD_COLUMN_LEFT_WIDTH'	=> new hdropdown('dd_column_left_width', array('options' => $width_options, 'value' => ((strpos($this->style['column_left_width'], '%') !== false) ? '%' : 'px'),  'disabled' => ((!in_array('column_left_width', $arrUsedVariables)) ? true : false))),
			'DD_COLUMN_RIGHT_WIDTH'	=>new hdropdown('dd_column_right_width', array('options' => $width_options, 'value' => ((strpos($this->style['column_right_width'], '%') !== false) ? '%' : 'px'),  'disabled' => ((!in_array('column_right_width', $arrUsedVariables)) ? true : false))),

			'DD_LINK_STYLE'			=> new hdropdown('body_link_style', array('options' => $text_decoration, 'value' => $this->style['body_link_style'], 'disabled' => ((!in_array('body_link_style', $arrUsedVariables)) ? true : false))),	
			'DD_HLINK_STYLE'		=> new hdropdown('body_hlink_style', array('options' => $text_decoration, 'value' => $this->style['body_hlink_style'], 'disabled' => ((!in_array('body_hlink_style', $arrUsedVariables)) ? true : false))),
			'DD_HEAD_LINK_STYLE'	=> new hdropdown('header_link_style', array('options' => $text_decoration, 'value' => $this->style['header_link_style'], 'disabled' => ((!in_array('header_link_style', $arrUsedVariables)) ? true : false))),
			'DD_HEAD_HLINK_STYLE'	=> new hdropdown('header_hlink_style', array('options' => $text_decoration, 'value' => $this->style['header_hlink_style'], 'disabled' => ((!in_array('header_hlink_style', $arrUsedVariables)) ? true : false))),
			'DD_TABLE_BORDERSTYLE'	=> new hdropdown('table_border_style', array('options' => $border_style, 'value' => $this->style['table_border_style'], 'disabled' => ((!in_array('table_border_style', $arrUsedVariables)) ? true : false))),
			'DD_INPUT_BORDERSTYLE'	=> new hdropdown('input_border_style', array('options' => $border_style, 'value' => $this->style['input_border_style'], 'disabled' => ((!in_array('input_border_style', $arrUsedVariables)) ? true : false))),
			
				'DD_ATTENDEE_COLUMNS'	=> new hdropdown('attendees_columns', array('options' => $attendee_colums, 'value' => $this->style['attendees_columns'])),
			'DD_LOGO_POSITION'		=> new hdropdown('logo_position', array('options' => $logo_positions, 'value' => $this->style['logo_position'])),

			'RADIO_BACKGROUND_IMAGE_TYPE' => new hradio('background_type', array('options' => $this->user->lang("background_image_types"), 'value' => $this->style['background_type'], 'disabled' => ((!in_array('background_type', $arrUsedVariables)) ? true : false))),
			'RADIO_BACKGROUND_POSITION' => new hradio('background_pos', array('options' => array('normal' => $this->user->lang('background_position_normal'), 'fixed' => $this->user->lang('background_position_fixed')), 'value' => $this->style['background_pos'], 'disabled' => ((!in_array('background_pos', $arrUsedVariables)) ? true : false))),
			'BACKGROUND_IMG_DISABLED' => ((!in_array('background_img', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
	
			// Color pickers
			'CP_BODY_BG'			=> $this->jquery->colorpicker('body_background', $this->style['body_background'], false, 14, ((!in_array('body_background', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_FONTCOLOR1'			=> $this->jquery->colorpicker('fontcolor1', $this->style['fontcolor1'], false, 14, ((!in_array('fontcolor1', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_FONTCOLOR2'			=> $this->jquery->colorpicker('fontcolor2', $this->style['fontcolor2'], false, 14, ((!in_array('fontcolor2', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_FONTCOLOR3'			=> $this->jquery->colorpicker('fontcolor3', $this->style['fontcolor3'], false, 14, ((!in_array('fontcolor3', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_FONTCOLOR_NEG'		=> $this->jquery->colorpicker('fontcolor_neg', $this->style['fontcolor_neg'], false, 14, ((!in_array('fontcolor_neg', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_FONTCOLOR_POS'		=> $this->jquery->colorpicker('fontcolor_pos', $this->style['fontcolor_pos'], false, 14, ((!in_array('fontcolor_pos', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_BODY_LINK'			=> $this->jquery->colorpicker('body_link', $this->style['body_link'], false, 14, ((!in_array('body_link', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_BODY_HLINK'			=> $this->jquery->colorpicker('body_hlink', $this->style['body_hlink'], false, 14, ((!in_array('body_hlink', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_HEADER_LINK'		=> $this->jquery->colorpicker('header_link', $this->style['header_link'], false, 14, ((!in_array('header_link', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_HEADER_HLINK'		=> $this->jquery->colorpicker('header_hlink', $this->style['header_hlink'], false, 14, ((!in_array('header_hlink', $arrUsedVariables)) ? 'disabled="disabled"' : '')),

			'CP_TR_COLOR1'			=> $this->jquery->colorpicker('tr_color1', $this->style['tr_color1'], false, 14, ((!in_array('tr_color1', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_TR_COLOR2'			=> $this->jquery->colorpicker('tr_color2', $this->style['tr_color2'], false, 14, ((!in_array('tr_color2', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_TH_COLOR1'			=> $this->jquery->colorpicker('th_color1', $this->style['th_color1'], false, 14, ((!in_array('th_color1', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_TABLE_BORDER'		=> $this->jquery->colorpicker('table_border_color', $this->style['table_border_color'], false, 14, ((!in_array('table_border_color', $arrUsedVariables)) ? 'disabled="disabled"' : '')),

			'CP_INPUT_COLOR'		=> $this->jquery->colorpicker('input_color', $this->style['input_color'], false, 14, ((!in_array('input_color', $arrUsedVariables)) ? 'disabled="disabled"' : '')),
			'CP_INPUT_BORDER'		=> $this->jquery->colorpicker('input_border_color', $this->style['input_border_color'], false, 14, ((!in_array('input_border_color', $arrUsedVariables)) ? 'disabled="disabled"' : '')),

			// Language
			'L_TEMPLATE_WARNING'	=> sprintf($this->user->lang('template_warning'), $this->pfh->FileLink('templates', 'eqdkp').'/'.$this->style['template_path']),

			// Buttons
			'S_ADD' 				 => ( !$this->url_id ) ? true : false)
		);

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('styles_title'),
			'template_file'		=> 'admin/manage_styles.html',
			'display'			=> true)
		);
	}

	public function display(){
		redirect('admin/manage_extensions.php'.$this->SID, false, false, false);
	}
	
	private function get_used_variables($style_path){
		$var_mapping = array(
				'fontface1' 			=> "T_FONTFACE1",
				'fontface2' 			=> "T_FONTFACE2",
				'fontface3' 			=> "T_FONTFACE3",
				'fontsize1' 			=> "T_FONTSIZE1",
				'fontsize2' 			=> "T_FONTSIZE2",
				'fontsize3' 			=> "T_FONTSIZE3",
				'fontcolor1' 			=> "T_FONTCOLOR1",
				'fontcolor2' 			=> "T_FONTCOLOR2",
				'fontcolor3' 			=> "T_FONTCOLOR3",
				'fontcolor_neg' 		=> 'T_FONTCOLOR_NEG',
				'fontcolor_pos' 		=> 'T_FONTCOLOR_POS',
				'background_type' 		=> 'TEMPLATE_BACKGROUND',
				'background_img' 		=> 'TEMPLATE_BACKGROUND',
				'background_pos' 		=> 'T_BACKGROUND_POSITION',
				'body_background' 		=> 'T_BODY_BACKGROUND',
				'table_border_width' 	=> 'T_TABLE_BORDER_WIDTH',
				'table_border_color' 	=> 'T_TABLE_BORDER_COLOR',
				'table_border_style' 	=> 'T_TABLE_BORDER_STYLE',
				'body_link_style' 		=> 'T_BODY_LINK_STYLE',
				'body_link' 			=> 'T_BODY_LINK',
				'body_hlink_style' 		=> 'T_BODY_HLINK_STYLE',
				'body_hlink' 			=> 'T_BODY_HLINK',
				'header_link_style' 	=> 'T_HEADER_LINK_STYLE',
				'header_link' 			=> 'T_HEADER_LINK',
				'header_hlink_style' 	=> 'T_HEADER_HLINK_STYLE',
				'header_hlink' 			=> 'T_HEADER_HLINK',
				'th_color1' 			=> 'T_TH_COLOR1',
				'tr_color1' 			=> 'T_TR_COLOR1',
				'tr_color2' 			=> 'T_TR_COLOR2',
				'input_color' 			=> 'T_INPUT_BACKGROUND',
				'input_border_width' 	=> 'T_INPUT_BORDER_WIDTH',
				'input_border_color' 	=> 'T_INPUT_BORDER_COLOR',
				'input_border_style' 	=> 'T_INPUT_BORDER_STYLE',
				'portal_width' 			=> 'T_PORTAL_WIDTH',
				'column_left_width' 	=> 'T_COLUMN_LEFT_WIDTH',
				'column_right_width' 	=> 'T_COLUMN_RIGHT_WIDTH',
		);
		
		$arrFiles[] = $this->tpl->resolve_css_file($this->core->root_path . 'templates/'.$style_path.'/'.$style_path.'.css', $style_path);
		$arrFiles[] = $this->tpl->resolve_css_file($this->core->root_path . 'templates/'.$style_path.'/custom.css', $style_path);
		$arrFiles[] = $this->tpl->resolve_templatefile('index.tpl', $style_path);
		
		$arrVariables = array();
		foreach($arrFiles as $strFilename){
			if($strFilename && is_file($strFilename)){
				$strContent = file_get_contents($strFilename);
				
				foreach($var_mapping as $key => $val){
					if(strpos($strContent, $val) !== false){
						$arrVariables[] = $key;
					}
				}
			}
		}
		$arrVariables = array_unique($arrVariables);
		return $arrVariables;
	}

}
registry::register('Manage_Styles');
?>