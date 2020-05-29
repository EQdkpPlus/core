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
			'cancel'	=> array('process' => 'display'),
			'version_update' => array('process' => 'version_update'),
			'create' => array('process' => 'display_create'),
			'create_style' => array('process' => 'process_create', 'csrf'=>true),
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

	public function display_create(){
		$style_array = array();
		foreach(register('pdh')->get('styles', 'styles', array(0, true)) as $styleid=>$row){
			$style_array[$styleid] = $row['style_name'];
		}

		$this->tpl->assign_vars(array(
			'DD_STYLE_PARENT' => (new hdropdown('parent', array('options' => $style_array)))->output(),
		));

		$this->core->set_vars([
				'page_title'		=> $this->user->lang('create_style'),
				'template_file'		=> 'admin/manage_styles_create.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('extension_repo'), 'url'=>$this->root_path.'admin/manage_extensions.php'.$this->SID],
						['title'=>$this->user->lang('create_style'), 'url'=>' '],
				],
				'display'			=> true
		]);
	}

	public function process_create(){
		$intParent = $this->in->get('parent', 0);

		$strParentPath = $this->pdh->get('styles', 'templatepath', array($intParent));

		$strNewName = $this->in->get('style_name');
		$strNewNamePath = utf8_strtolower($strNewName);
		$strNewNamePath = preg_replace('/[^A-Za-z0-9\-\_]/', '', $strNewNamePath);

		$strBaseFolder = $this->root_path.'/templates/';
		if(is_dir($strBaseFolder.$strParentPath)){
			full_copy($strBaseFolder.$strParentPath, $strBaseFolder.$strNewNamePath);
			$this->pfh->rename( $strBaseFolder.$strNewNamePath.'/'.$strParentPath.'.css', $strBaseFolder.$strNewNamePath.'/'.$strNewNamePath.'.css');
			$this->pfh->rename( $strBaseFolder.$strNewNamePath.'/'.$strParentPath.'.js', $strBaseFolder.$strNewNamePath.'/'.$strNewNamePath.'.js');

			$strPackageXML = file_get_contents($strBaseFolder.$strNewNamePath.'/package.xml');
			$strNewPackageXML = preg_replace('/folder\>(.*)\<\/folder/', 'folder>'.$strNewNamePath.'</folder', $strPackageXML);
			$strNewPackageXML = preg_replace('/name\>(.*)\<\/name/', 'name>'.htmlentities($strNewName).'</name', $strNewPackageXML);
			$this->pfh->putContent($strBaseFolder.$strNewNamePath.'/package.xml', $strNewPackageXML);


			$strSettingsXML= file_get_contents($strBaseFolder.$strNewNamePath.'/settings.xml');
			$strNewSettingsXML = preg_replace('/template\_path\>(.*)\<\/template\_path/', 'template_path>'.$strNewNamePath.'</template_path', $strSettingsXML);

			$this->pfh->putContent($strBaseFolder.$strNewNamePath.'/settings.xml', $strNewSettingsXML);

			$intStyleID = $this->objStyles->install($strNewNamePath);

			redirect('admin/manage_styles.php'.$this->SID.'&edit=true&styleid='.$intStyleID);
		}


		die();
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

		$render_dd = (new hdropdown('renderer', array('options' => $arrRenderer, 'default' => 'side_by_side', 'tolang' => true, 'js' => 'onchange="this.form.submit();"')))->output();
		$render_dd->value = $render_dd->inpval();
		$this->tpl->assign_vars(array(
			'CONTENT'	=> $content,
			'FILENAME'	=> $strFilename,
			'RENDERER_DROPDOWN' => $render_dd,
			'ENCODED_FILENAME' => $this->in->get('diff', ''),
			'S_RENDERER' => $blnRenderer,
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('liveupdate_show_differences'),
			'template_file'		=> 'admin/diff_viewer.html',
			'header_format'		=> 'simple',
			'display'			=> true
		]);
	}

	public function edit_template(){
		if ($this->in->get('template_dd') != "" && $this->in->get('template_edit') != ""){
			$filename = base64_decode($this->in->get('template_dd'));

			$admin_folder = (substr($filename, 0, 6) == 'admin/') ? '/admin' : '';
			$filename = str_replace('admin/', '', $filename);
			
			//Sanitize Filename
			$filename = preg_replace('/[^A-Za-z0-9\._-]/', '', $filename);
			$extension = pathinfo($filename, PATHINFO_EXTENSION); 
			
			$storage_folder  = $this->pfh->FolderPath('templates/'.$this->style['template_path'].$admin_folder, 'eqdkp');
			
			$filename = "../../test.php";
			
			if(!isFilelinkInFolder($storage_folder.$filename, $storage_folder) || (!in_array($extension, array('html', 'js', 'css', 'tpl')))){
				message_die("Action not allowed");
			}
			
			
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

		redirect('admin/manage_styles.php'.$this->SID.'&edit=true&styleid='.$this->url_id.'&save=success');

		return;
	}

	private function get_data() {
		$portal_width = (strlen($this->in->get('portal_width'))) ? $this->in->get('portal_width').$this->in->get('dd_portal_width') : '';
		$column_left_width = (strlen($this->in->get('column_left_width'))) ? $this->in->get('column_left_width').$this->in->get('dd_column_left_width') : '';
		$column_right_width = (strlen($this->in->get('column_right_width'))) ? $this->in->get('column_right_width').$this->in->get('dd_column_right_width') : '';

		$data = array(
			'style_name'			=> $this->in->get('style_name', $this->style['style_name']),
			'style_version'			=> $this->in->get('style_version', $this->style['style_version']),
			'background_type'		=> $this->in->get('background_type', 0),
			'background_pos'		=> $this->in->get('background_pos'),
			'background_img'		=> $this->in->get('background_img'),
			'editor_theme'			=> $this->in->get('editor_theme'),
			'portal_width'			=> $portal_width,
			'column_left_width'		=> $column_left_width,
			'column_right_width'	=> $column_right_width,
			'attendees_columns'		=> $this->in->get('attendees_columns'),
			'logo_position'			=> $this->in->get('logo_position', 'center'),
			'favicon_img'			=> $this->in->get('favicon_img'),
			'banner_img'			=> $this->in->get('banner_img'),
			'additional_less'		=> $this->in->get('additional_less', '', 'raw'),
			'additional_fields'		=> serialize($this->in->getArray('add_links', 'string')),
		);

		$arrOptions = $this->objStyles->styleOptions();
		foreach($arrOptions as $key => $val){
			foreach($val as $name => $type)
			{
				$data[$name] = $this->in->get($name, '', 'raw');
			}
		}

		return $data;
	}

	public function edit(){
		// work-around for a known Chrome bug that causes the XSS auditor to incorrectly detect JavaScript inside a textarea
		@header('X-XSS-Protection: 0');

		if($this->in->get('save') == 'success') {
			$arrStyle = $this->pdh->get('styles', 'styles', array($this->url_id));
			$this->objStyles->deleteStyleCache($arrStyle['template_path']);

			$this->core->message( $this->user->lang('admin_update_style_success'), $this->user->lang('success'), 'green');
		}


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
				'ID'		=> $class_id,
				'CPICKER'	=> (new hcolorpicker('classc_'.$class_id, array('value' =>  $this->game->get_class_color($class_id, $this->url_id), 'id' => 'classc_'.$class_id)))->output(),
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

			$extension = pathinfo($filename, PATHINFO_EXTENSION);
			
			if(!in_array($extension, array('html', 'tpl', 'css', 'js'))){
				message_die("Extension not allowed.");
			}
			
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
				$file_ext = pathinfo($filename, PATHINFO_EXTENSION);
				
				if(!isFilelinkInFolder($filename, '') || (!in_array($file_ext, array('html', 'js', 'css', 'tpl')))){
					message_die("Action not allowed");
				}
				
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
			'F_ADD_STYLE'					=> 'manage_styles.php' . $this->SID.'&amp;update=true',
			'STYLE_ID'						=> $this->url_id,
			'DD_EDIT_TEMPLTES'				=> (new hdropdown('template_dd', array('options' => $files, 'value' => $this->in->get('template'), 'js' => 'onchange="this.form.template.value=this.value;this.form.action =\'manage_styles.php'.$this->SID.'&amp;edit=true&amp;styleid=' . $this->url_id.'\'; this.form.submit();"')))->output(),
			'TEMPLATE_CONTENT'				=> $this->jquery->CodeEditor('template_edit', ((isset($contents)) ? htmlentities($contents) : ''), $editor_type),

			// Form Values
			'STYLE_NAME'					=> $this->style['style_name'],
			'STYLE_CODE'					=> (isset($this->style['style_code'])) ? $this->style['style_code'] : '',
			'STYLE_AUTHOR'					=> $this->style['style_author'],
			'STYLE_CONTACT'					=> $this->style['style_contact'],
			'STYLE_VERSION'					=> $this->style['style_version'],
			'EDITOR_THEME'					=> (new hdropdown('editor_theme', array('options' => register('tinyMCE')->getAvailableSkins(), 'value' => $this->style['editor_theme'])))->output(),

			'FAVICON_IMG'					=> $this->style['favicon_img'],
			'BANNER_IMG'					=> $this->style['banner_img'],
			'BACKGROUND_IMG'				=> $this->style['background_img'],
			'CSS_FILE'						=> $this->style['css_file'],

			'STYLE_PORTAL_WIDTH'			=> (isset($this->style['portal_width'])) ? (int)$this->style['portal_width'] : 1100,
			'STYLE_COLUMN_LEFT_WIDTH'		=> (isset($this->style['column_left_width'])) ? (int)$this->style['column_left_width'] : 180,
			'STYLE_COLUMN_RIGHT_WIDTH'		=> (isset($this->style['column_right_width'])) ? (int)$this->style['column_right_width'] : 180,

			'STYLE_PORTAL_WIDTH_DISABLED'	=> ((!in_array('portal_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'STYLE_COLUMN_LEFT_DISABLED'	=> ((!in_array('portal_column_left_width', $arrUsedVariables) && !in_array('column_left_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'STYLE_COLUMN_RIGHT_DISABLED'	=> ((!in_array('portal_column_right_width', $arrUsedVariables) && !in_array('column_right_width', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'DD_PORTAL_WIDTH'				=> (new hdropdown('dd_portal_width', array('options' => $width_options, 'value' => ((strpos($this->style['portal_width'], '%') !== false) ? '%' : 'px'),  'disabled' => ((!in_array('portal_width', $arrUsedVariables)) ? true : false))))->output(),
			'DD_COLUMN_LEFT_WIDTH'			=> (new hdropdown('dd_column_left_width', array('options' => $width_options, 'value' => ((strpos($this->style['column_left_width'], '%') !== false) ? '%' : 'px'),  'disabled' => ((!in_array('portal_column_left_width', $arrUsedVariables) && !in_array('column_left_width', $arrUsedVariables)) ? true : false))))->output(),
			'DD_COLUMN_RIGHT_WIDTH'			=> (new hdropdown('dd_column_right_width', array('options' => $width_options, 'value' => ((strpos($this->style['column_right_width'], '%') !== false) ? '%' : 'px'),  'disabled' => ((!in_array('portal_column_right_width', $arrUsedVariables) && !in_array('column_right_width', $arrUsedVariables)) ? true : false))))->output(),

			'DD_ATTENDEE_COLUMNS'			=> (new hdropdown('attendees_columns', array('options' => $attendee_colums, 'value' => $this->style['attendees_columns'])))->output(),
			'DD_LOGO_POSITION'				=> (new hdropdown('logo_position', array('options' => $logo_positions, 'value' => $this->style['logo_position'])))->output(),

			'RADIO_BACKGROUND_IMAGE_TYPE'	=> (new hradio('background_type', array('options' => $this->user->lang("background_image_types"), 'value' => $this->style['background_type'], 'disabled' => ((!in_array('background_image', $arrUsedVariables)) ? true : false))))->output(),
			'RADIO_BACKGROUND_POSITION'		=> (new hradio('background_pos', array('options' => array('normal' => $this->user->lang('background_position_normal'), 'fixed' => $this->user->lang('background_position_fixed')), 'value' => $this->style['background_pos'], 'disabled' => ((!in_array('background_position', $arrUsedVariables) && !in_array('background_image_position', $arrUsedVariables)) ? true : false))))->output(),
			'BACKGROUND_IMG_DISABLED'		=> ((!in_array('background_image', $arrUsedVariables)) ? 'disabled="disabled"' : ''),
			'ADDITIONAL_LESS'				=> $this->style['additional_less'],

			// Language
			'L_TEMPLATE_WARNING'			=> sprintf($this->user->lang('template_warning'), $this->pfh->FileLink('templates', 'eqdkp').'/'.$this->style['template_path']),

			// Buttons
			'S_ADD' 						 => ( !$this->url_id ) ? true : false)
		);

		$arrOptions = $this->objStyles->styleOptions();
		foreach($arrOptions as $key => $val){
			$this->tpl->assign_block_vars('fieldset_row', array(
				'LEGEND' => $this->user->lang('stylesettings_heading_'.$key),
				'KEY'	=> $key,
			));

			$this->jquery->Collapse('#toggleColorsettings'.$key);

			foreach($val as $name=>$elem){
				$field = "";

				if($elem == 'color'){
					$field = (new hcolorpicker($name, array('value' =>  $this->style[$name], 'id' => $name, 'disabled' => ((!in_array($name, $arrUsedVariables)) ? true : false), 'size' => 14, 'showAlpha' => true, 'format' => 'rgb')))->output();
				} elseif($elem == 'decoration'){
					$field = (new hdropdown($name, array('options' => $text_decoration, 'value' => $this->style[$name], 'disabled' => ((!in_array($name, $arrUsedVariables)) ? true : false))))->output();
				} elseif($elem == 'font-family'){
					$field = (new htext($name, array('size' => 30, 'value' => sanitize($this->style[$name]), 'disabled' => ((!in_array($name, $arrUsedVariables)) ? true : false))))->output();
				} elseif($elem == 'size'){
					$field = (new htext($name, array('after_txt' => 'px', 'value' => sanitize($this->style[$name]), 'size' => 3, 'disabled' => ((!in_array($name, $arrUsedVariables)) ? true : false))))->output();
				}

				$this->tpl->assign_block_vars('fieldset_row.option_row', array(
					'NAME' => $this->user->lang('stylesettings_'.$name),
					'FIELD'=> $field,
					'HELP' => '@'.$this->objStyles->convertNameToLessVar($name),
				));
			}
		}

		//Additional Links
		$arrAdditionalLinks = array();
		foreach($arrUsedVariables as $val){
			if(strpos($val, 'add_link_') === 0){
				$arrAdditionalLinks[] = substr($val, 9);
			}
		}
		if(count($arrAdditionalLinks) > 0){
			$key = 'additional_links';
			$this->tpl->assign_block_vars('fieldset_row', array(
				'LEGEND' => $this->user->lang('stylesettings_heading_'.$key),
				'KEY'	=> $key,
			));
			$this->jquery->Collapse('#toggleColorsettings'.$key);

			foreach($arrAdditionalLinks as $val){
				$name = 'add_link_'.$key;
				$this->tpl->assign_block_vars('fieldset_row.option_row', array(
					'NAME' => ucfirst($val),
					'FIELD'=> (new htext('add_links['.$val.']', array('size' => 30, 'value' => $this->style['additional_fields'][$val])))->output(),
					'HELP' => '',
				));
			}
		}


		$this->jquery->Collapse('#toggleColorsettingsadditional_less', true);

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('styles_title'),
			'template_file'		=> 'admin/manage_styles.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('extension_repo'), 'url'=>$this->root_path.'admin/manage_extensions.php'.$this->SID],
				['title'=>$this->user->lang('style_localupdate'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function display(){
		redirect('admin/manage_extensions.php'.$this->SID, false, false, false);
	}

	private function get_used_variables($style_path){
		$arrOptions = $this->objStyles->styleOptions();
		$arrVariablesToLook = array('portal_width', 'portal_column_left_width', 'portal_column_right_width', 'background_image', 'background_position', 'background_image_position', 'column_left_width', 'column_right_width');
		foreach($arrOptions as $key => $val){
			foreach($val as $name => $type)
			{
				$arrVariablesToLook[] = $name;
			}
		}

		$arrFiles[] = $this->tpl->resolve_css_file($this->core->root_path . 'templates/'.$style_path.'/'.$style_path.'.css', $style_path);
		$arrFiles[] = $this->tpl->resolve_css_file($this->core->root_path . 'templates/'.$style_path.'/custom.css', $style_path);
		$arrFiles[] = $this->tpl->resolve_templatefile('index.tpl', $style_path);

		$arrVariables = array();
		foreach($arrFiles as $strFilename){
			if($strFilename && is_file($strFilename)){
				$strContent = file_get_contents($strFilename);

				foreach($arrVariablesToLook as $val){
					$myLess = '@'.$this->objStyles->convertNameToLessVar($val);
					$myTemplate = 'T_'.strtoupper($val);

					if(strpos($strContent, $myLess) !== false || strpos($strContent, $myTemplate) !== false){
						$arrVariables[] = $val;
					}
				}

				if(strpos($strContent, 'TEMPLATE_BACKGROUND') !== false){
					$arrVariables[] = 'background_image';
				}

				//Search for additional Links
				$arrFoundLinks = array();
				preg_match_all("/{LINK_(\w*)}/U", $strContent, $arrFoundLinks);
				foreach($arrFoundLinks[1] as $link){
					$arrVariables[] = 'add_link_'.strtolower($link);
				}
			}
		}

		$arrVariables = array_unique($arrVariables);

		return $arrVariables;
	}

}
registry::register('Manage_Styles');
