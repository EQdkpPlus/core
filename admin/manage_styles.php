<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Styles extends page_generic{
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'core', 'config', 'html', 'pfh', 'objStyles'=> 'styles');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
			'use_db_vars'		=> true,
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

			if ($filename == $this->style['template_path'].'.css'){
				$this->tpl->parse_cssfile($this->style['template_path'], $this->style);
			}

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
		
		$this->tpl->assign_vars(array(
			'CONTENT'	=> $content,
			'FILENAME'	=> $strFilename,
			'RENDERER_DROPDOWN' => $this->html->DropDown('renderer', $arrRenderer, $this->in->get('renderer', 'side_by_side'), '', 'onchange="this.form.submit();"'),
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
			$this->pfh->putContent($storage_folder.$filename, $this->in->get('template_edit', '', 'raw'));

			//Create new parsed css file
			if ($filename == $this->style['template_path'].'.css'){
				$this->tpl->parse_cssfile($this->style['template_path'], $this->style);
			}

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
		$this->core->message( $this->user->lang('admin_update_style_success'), $this->user->lang('success'), 'green');
		$this->pdh->process_hook_queue();
		
		$this->style = $this->pdh->get('styles', 'styles', array($this->url_id));
		$this->tpl->parse_cssfile($this->style['template_path'], $this->style);
		
	}

	private function get_data() {
		$portal_width = (strlen($this->in->get('portal_width'))) ? $this->in->get('portal_width').$this->in->get('dd_portal_width') : '';
		$column_left_width = (strlen($this->in->get('column_left_width'))) ? $this->in->get('column_left_width').$this->in->get('dd_column_left_width') : '';
		$column_right_width = (strlen($this->in->get('column_right_width'))) ? $this->in->get('column_right_width').$this->in->get('dd_column_right_width') : '';
		
		if (!$this->in->get('use_db_vars', 0)){
			$data = array(
				'portal_width'			=> $portal_width,
				'column_left_width'		=> $column_left_width,
				'column_right_width'	=> $column_right_width,

				'attendees_columns'		=> $this->in->get('attendees_columns'),
				'logo_position'			=> $this->in->get('logo_position', 'center'),
				'background_img'		=> $this->in->get('background_img'),
				'css_file'				=> $this->in->get('css_file'),
			);
			return $data;
		}
		
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

			'portal_width'			=> $portal_width,
			'column_left_width'		=> $column_left_width,
			'column_right_width'	=> $column_right_width,

			'attendees_columns'		=> $this->in->get('attendees_columns'),
			'logo_position'			=> $this->in->get('logo_position', 'center'),
			'background_img'		=> $this->in->get('background_img'),
			'css_file'				=> $this->in->get('css_file'),
			'use_db_vars'			=> $this->in->get('use_db_vars')
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

		// Attendee columns
		for ($i = 1; $i < 11; $i++){
			$attendee_colums[$i] = $i;
		}

		// Class Colors
		foreach($this->game->get('classes') as $class_id => $class_name){
			$this->tpl->assign_block_vars('classes', array(
				'NAME'		=> $class_name,
				'CPICKER'	=> $this->jquery->colorpicker('classc_'.$class_id, str_replace('#', '', $this->game->get_class_color($class_id, $this->url_id))),
			));
		}

		//First: the base templates
		$arrBaseTemplates = $this->objStyles->scan_templates($this->core->root_path . 'templates/base_template');
		//Now the files from the template
		$arrTemplates = $this->objStyles->scan_templates($this->core->root_path . 'templates/'.$this->style['template_path']);
		$arrTemplates = array_merge($arrBaseTemplates, $arrTemplates);
		
		foreach ($arrTemplates as $path => $name){
			$files[base64_encode($path)] = $path;
		}

		//Read an spezific template-file to edit
		$editor_type = 'html_js';
		if ($this->in->get('template') != "" && !is_numeric(base64_decode($this->in->get('template')))){
			$filename = base64_decode($this->in->get('template'));

			if (file_exists($this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').$filename)){
				$filename = $this->pfh->FolderPath('templates/'.$this->style['template_path'], 'eqdkp').$filename;
			} elseif (file_exists($this->core->root_path . 'templates/'.$this->style['template_path'].'/'.$filename)){
				$filename = $this->core->root_path . 'templates/'.$this->style['template_path'].'/'.$filename;
			} else {
				$filename = $this->core->root_path . 'templates/base_template/'.$filename;
			}

			if (file_exists($filename)){
				$contents = file_get_contents($filename);
				$file_ext = pathinfo($filename, PATHINFO_EXTENSION);
				$editor_type = ($file_ext == 'css') ? 'css' : 'html_js';
				$select_tab = ($this->style['use_db_vars']) ? 3 : 2;
			}
		}

		$this->confirm_delete($this->user->lang('confirm_delete_style'));
		$this->jquery->Tab_header('style_tabs');
		if(isset($select_tab) && $select_tab > 0){ $this->jquery->Tab_Select('style_tabs', $select_tab); }

		$this->tpl->assign_vars(array(
			// Form vars
			'F_ADD_STYLE'			=> 'manage_styles.php' . $this->SID.'&amp;update=true',
			'STYLE_ID'				=> $this->url_id,
			'DD_EDIT_TEMPLTES'		=> $this->html->DropDown('template_dd', $files, $this->in->get('template'), '', 'onchange="this.form.template.value=this.value;this.form.action =\'manage_styles.php'.$this->SID.'&amp;edit=true&amp;styleid=' . $this->url_id.'\'; this.form.submit();"', 'input'),
			'TEMPLATE_CONTENT'		=> $this->jquery->CodeEditor('template_edit', ((isset($contents)) ? htmlentities($contents) : ''), $editor_type),
			'S_USE_DBVARS'			=> ($this->style['use_db_vars']) ? true : false,

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

			'TABLE_BORDER_WIDTH'	=> $this->style['table_border_width'],
			'TABLE_BORDER_STYLE'	=> $this->style['table_border_style'],
			'INPUT_BORDER_WIDTH'	=> $this->style['input_border_width'],
			'INPUT_BORDER_STYLE'	=> $this->style['input_border_style'],
			'BACKGROUND_IMG'		=> $this->style['background_img'],
			'CSS_FILE'				=> $this->style['css_file'],
			'STYLE_PORTAL_WIDTH'	=> (isset($this->style['portal_width'])) ? (int)$this->style['portal_width'] : 1100,
			'STYLE_COLUMN_LEFT_WIDTH'	=> (isset($this->style['column_left_width'])) ? (int)$this->style['column_left_width'] : 180,
			'STYLE_COLUMN_RIGHT_WIDTH'	=> (isset($this->style['column_right_width'])) ? (int)$this->style['column_right_width'] : 180,
			'DD_PORTAL_WIDTH'		=> $this->html->DropDown('dd_portal_width', $width_options, ((strpos($this->style['portal_width'], '%') !== false) ? '%' : 'px'), '', '', 'input'),
			'DD_COLUMN_LEFT_WIDTH'	=> $this->html->DropDown('dd_column_left_width', $width_options, ((strpos($this->style['column_left_width'], '%') !== false) ? '%' : 'px'), '', '', 'input'),
			'DD_COLUMN_RIGHT_WIDTH'	=> $this->html->DropDown('dd_column_right_width', $width_options, ((strpos($this->style['column_right_width'], '%') !== false) ? '%' : 'px'), '', '', 'input'),

			'DD_LINK_STYLE'			=> $this->html->DropDown('body_link_style', $text_decoration, $this->style['body_link_style'], '', '', 'input'),
			'DD_HLINK_STYLE'		=> $this->html->DropDown('body_hlink_style', $text_decoration, $this->style['body_hlink_style'], '', '', 'input'),
			'DD_HEAD_LINK_STYLE'	=> $this->html->DropDown('header_link_style', $text_decoration, $this->style['header_link_style'], '', '', 'input'),
			'DD_HEAD_HLINK_STYLE'	=> $this->html->DropDown('header_hlink_style', $text_decoration, $this->style['header_hlink_style'], '', '', 'input'),
			'DD_TABLE_BORDERSTYLE'	=> $this->html->DropDown('table_border_style', $border_style, $this->style['table_border_style'], '', '', 'input'),
			'DD_INPUT_BORDERSTYLE'	=> $this->html->DropDown('input_border_style', $border_style, $this->style['input_border_style'], '', '', 'input'),
			'DD_ATTENDEE_COLUMNS'	=> $this->html->DropDown('attendees_columns', $attendee_colums, $this->style['attendees_columns'], '', '', 'input'),
			'DD_LOGO_POSITION'		=> $this->html->DropDown('logo_position', $logo_positions, $this->style['logo_position'], '', '', 'input'),
			// Color pickers
			'CP_BODY_BG'			=> $this->jquery->colorpicker('body_background', $this->style['body_background']),
			'CP_FONTCOLOR1'			=> $this->jquery->colorpicker('fontcolor1', $this->style['fontcolor1']),
			'CP_FONTCOLOR2'			=> $this->jquery->colorpicker('fontcolor2', $this->style['fontcolor2']),
			'CP_FONTCOLOR3'			=> $this->jquery->colorpicker('fontcolor3', $this->style['fontcolor3']),
			'CP_FONTCOLOR_NEG'		=> $this->jquery->colorpicker('fontcolor_neg', $this->style['fontcolor_neg']),
			'CP_FONTCOLOR_POS'		=> $this->jquery->colorpicker('fontcolor_pos', $this->style['fontcolor_pos']),
			'CP_BODY_LINK'			=> $this->jquery->colorpicker('body_link', $this->style['body_link']),
			'CP_BODY_HLINK'			=> $this->jquery->colorpicker('body_hlink', $this->style['body_hlink']),
			'CP_HEADER_LINK'		=> $this->jquery->colorpicker('header_link', $this->style['header_link']),
			'CP_HEADER_HLINK'		=> $this->jquery->colorpicker('header_hlink', $this->style['header_hlink']),

			'CP_TR_COLOR1'			=> $this->jquery->colorpicker('tr_color1', $this->style['tr_color1']),
			'CP_TR_COLOR2'			=> $this->jquery->colorpicker('tr_color2', $this->style['tr_color2']),
			'CP_TH_COLOR1'			=> $this->jquery->colorpicker('th_color1', $this->style['th_color1']),
			'CP_TABLE_BORDER'		=> $this->jquery->colorpicker('table_border_color', $this->style['table_border_color']),

			'CP_INPUT_COLOR'		=> $this->jquery->colorpicker('input_color', $this->style['input_color']),
			'CP_INPUT_BORDER'		=> $this->jquery->colorpicker('input_border_color', $this->style['input_border_color']),

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
		redirect('admin/manage_extensions.php'.$this->SID);
	}

}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Styles', Manage_Styles::__shortcuts());
registry::register('Manage_Styles');
?>