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

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class ManagePageLayouts extends page_generic {
	private $multi_pools = array();

	public function __construct(){
		$this->user->check_auth('a_tables_man');
		$handler = array(
			'save' => array('process' => 'save_layout', 'csrf'=>true),
			'add_preset' => array('process' => 'add_preset', 'csrf'=>true),
			'add_layout' => array('process' => 'add_layout', 'csrf'=>true),
			'set_current_layout' => array('process' => 'set_current_layout', 'csrf'=>true),
			'del_pre' => array('process' => 'delete_preset', 'csrf'=>true),
			'del' => array('process' => 'delete', 'csrf'=>true)
		);
		parent::__construct(false, $handler);
		$this->process();
	}

	public function save_layout(){
		$layout_name = $this->in->get('filename', '');
		if(!$this->pdh->user_layout_exists($layout_name)) $this->display(array('text' => $this->user->lang('layout_not_exists'), 'title' => $this->user->lang('error'), 'color' => 'red'));

		$layout_def = $this->pdh->get_eqdkp_layout($layout_name);
		$layout_def['data']['description'] = addslashes($this->in->get('description', 'No description given.'));
		$page_list = $this->pdh->get_page_list();

		registry::load('form');

		//general options
		if (is_array($layout_def['options']) && !empty($layout_def['options'])){

			foreach ($layout_def['options'] as $key=>$value) {
				$name = 'params_'.$key;

				$layout_def['options'][$key]['value'] = form::value($name, $value);
			}
		}

		//substitutions
		if (is_array($layout_def['substitutions']) && !empty($layout_def['substitutions'])){
			foreach ($layout_def['substitutions'] as $key=>$value){
				$name = 'subs_'.$key;
				$layout_def['substitutions'][$key]['value'] = form::value($name, $value);
			}
		}

		//leaderboard
		$layout_def['pages']['listmembers']['listmembers_leaderboard']['maxpercolumn'] = $this->in->get('lb_maxpercolumn', 5);
		$layout_def['pages']['listmembers']['listmembers_leaderboard']['maxperrow'] = $this->in->get('lb_maxperrow', 5);
		$layout_def['pages']['listmembers']['listmembers_leaderboard']['sort_direction'] = $this->in->get('lb_sortdir', 'asc');
		$layout_def['pages']['listmembers']['listmembers_leaderboard']['column_type'] = $this->in->get('lb_columns', 'classid');
		$layout_def['pages']['listmembers']['listmembers_leaderboard']['columns'] = ($this->in->get('lb_columns') == 'classid') ? $this->in->getArray('lb_classes', 'int') : $this->in->getArray('lb_roles', 'int');
		$layout_def['pages']['listmembers']['listmembers_leaderboard']['default_pool'] = $this->in->get('lb_default_pool', 1);

		//roster
		$this->config->set('roster_classorrole', $this->in->get('roster_classorrole', 'class'));
		$this->config->set('roster_show_twinks', $this->in->get('roster_show_twinks', 0));
		$this->config->set('roster_show_hidden', $this->in->get('roster_show_hidden', 0));

		foreach($page_list as $page){
			foreach($layout_def['pages'][$page] as $page_object => $options){
				if(substr($page_object,0,4) == 'hptt'){
					$prefix = $page.':'.$page_object;
					$layout_def['pages'][$page][$page_object]['show_numbers'] = ($this->in->get($prefix.':numbers', 0)) ? true : false;
					$layout_def['pages'][$page][$page_object]['table_sort_dir'] = $this->in->get($prefix.':table_sort_dir', 'desc');
					$layout_def['pages'][$page][$page_object]['table_sort_col'] = 0;
					if($this->in->get($prefix.':default_pool', -1) >= 0) $layout_def['pages'][$page][$page_object]['default_pool'] = $this->in->get($prefix.':default_pool');
					if($this->in->get($prefix.':default_pool_ov', -1) >= 0) $layout_def['pages'][$page][$page_object]['default_pool_ov'] = $this->in->get($prefix.':default_pool_ov');
					$layout_def['pages'][$page][$page_object]['table_presets'] = array();
					$presets = $this->in->getArray($prefix.':td_add', 'string');
					$def_sort_column_id = 0;
					foreach($presets as $preset => $td_add){
						$layout_def['pages'][$page][$page_object]['table_presets'][] = array(
							'name' => $preset,
							'sort' => ($this->in->get($prefix.':sortable:'.$preset, 0)) ? true : false,
							'th_add' => html_entity_decode($this->in->get($prefix.':th_add:'.$preset, '')),
							'td_add' => html_entity_decode($this->in->get($prefix.':td_add:'.$preset, ''))
						);
						if($preset == $this->in->get($prefix.':default_sort', '')){
							$layout_def['pages'][$page][$page_object]['table_sort_col'] = $def_sort_column_id;
						}
						$def_sort_column_id++;
					}
				}
			}
		}

		$this->pdh->save_layout($layout_name, $layout_def);
		//did we change the current layout? => flush cache
		if($layout_name == $this->config->get('eqdkp_layout')){
			$this->pdc->flush();
			$this->pdh->init_eqdkp_layout($layout_name);
		}

		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' => $this->user->lang('lm_save_suc'), 'color' => 'green');
		$this->display($messages);
	}

	public function add_preset() {
		$strModul = $this->in->get('pdh_r_module');
		$strTag = $this->in->get('pdh_method');

		$arrParams = $this->in->getArray('param', 'string');
		$arrCaption = $this->in->getArray('caption', 'string');

		$strPresetName = $strTag.'_'.substr(randomID(), 0, 8);

		if ($strModul !="" && $strTag != ""){
			$strColumnTitle = $this->in->get('lang');

			$ps[0] = $strModul;
			$ps[1] = $strTag;

			foreach($arrParams as $param){
				if ($param === 'true') $param = true;
				if ($param === 'false') $param = false;

				$ps[2][] = (is_numeric($param)) ? intval($param) : $param;
			}

			foreach($arrCaption as $caption){
				$ps[3][] = (is_numeric($caption)) ? intval($caption) : $caption;
			}


			$this->pdh->update_user_preset($strPresetName, $ps, $strColumnTitle);
			$this->pdc->flush();
		}

		$this->display(false);
	}

	public function delete_preset(){
		$delete_preset = $this->in->get('del_pre', '');
		if($this->pdh->delete_user_preset($delete_preset)) {
			$this->pdc->flush();
		}
		$this->display(false, '1');
	}


	public function set_current_layout(){
		$new_layout = $this->in->get('current_layout', '');
		$layouts = $this->pdh->get_layout_list(true, true);
		$current_layout = $this->config->get('eqdkp_layout');

		if(in_array($new_layout, $layouts) && ($new_layout != $current_layout) ){
			$this->config->set('eqdkp_layout', $new_layout);
			$this->pdc->flush();
		}
		$messages[] = array('title' => $this->user->lang('save_suc'), 'text' =>  $this->user->lang('save_suc'), 'color' => 'green');
		// Check if the layout has changed
		$this->display($messages);
	}

	public function delete() {
		if($this->in->get('layout') != ''){
			$layout = $this->in->get('layout', '');
			$user_layouts = $this->pdh->get_layout_list(false, true);
			$current_layout = $this->config->get('eqdkp_layout');

			if(in_array($layout, $user_layouts) && $layout != $current_layout){
				$storage_folder  = $this->pfh->FolderPath('layouts', 'eqdkp');
				if (file_exists($storage_folder.$layout.'.esys.php')){
					$this->pfh->Delete($storage_folder.$layout.'.esys.php');
				}
				$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => $this->user->lang('lm_del_suc'), 'color' => 'green');
			}else{
				$messages[] = array('title' => $this->user->lang('del_no_suc'), 'text' => $this->user->lang('lm_del_error'), 'color' => 'red');
			}
		}
		$this->display($messages);
	}

	public function add_layout(){
		if($this->in->exists('new_layout_name') && $this->in->get('new_layout_name') != ''){
			$layout = $this->in->get('new_layout_name');
			if($this->pdh->layout_exists($layout)){
				$messages[] = array('title' => $this->user->lang('add_no_suc'), 'text' => $this->user->lang('lm_layout_exists'), 'color' => 'red');
				$this->display($messages);
			} else {
				//Create new layout
				$source_layout = $this->in->get('new_layout_source');
				$layout_desc = $this->in->get('new_layout_desc');
				$sl = $this->pdh->get_eqdkp_layout($source_layout);
				$sl['data']['description'] = $layout_desc;
				$this->pdh->save_layout($layout, $sl);
				$this->edit(false, $layout);
			}
		} else {
			$this->display();
		}
	}

	public function display($message = false, $tab = '0', $readd_xml = ''){
		if($message){
			$this->core->messages($message);
		}
		$current_layout = $this->config->get('eqdkp_layout');
		$this->pdh->auto_update_layout($current_layout);
		$intLayouts = 0;
		foreach($this->pdh->get_layout_list(true, false) as $layout){
			$intLayouts++;
			$this->tpl->assign_block_vars('layouts_row', array(
				'NAME'     => $layout,
				'DESC'    => $this->pdh->get_eqdkp_layout_description($layout),
				'IS_CURRENT' => ($layout == $current_layout) ? 'checked="checked"' : '',
			));
		}

		foreach($this->pdh->get_layout_list(false, true) as $layout){
			$intLayouts++;
			$this->tpl->assign_block_vars('user_layouts_row', array(
				'NAME'     => $layout,
				'DESC'    => $this->pdh->get_eqdkp_layout_description($layout),
				'IS_CURRENT' => ($layout == $current_layout) ? 'checked="checked"' : '',
			));
		}

		foreach($this->pdh->get_layout_list(true, true) as $layout) {
			$layout_options[$layout] = $layout;
		}

		$user_presets = $this->pdh->get_user_presets();
		if (is_array($user_presets)){
			foreach($user_presets as $key=>$value){
				$this->tpl->assign_block_vars('preset_row', array (
					'NAME' => $key,
					'MODULE' => $value[0],
					'TAG' => $value[1],
					'APARAM'	=> implode(', ', $value[2]),
					'DPARAM'	=> implode(', ', $value[3]),
				));
			}
		}

		$arrReadModules = $this->pdh->get_read_modules();
		$arrModuleDD = array('' => '');
		foreach($arrReadModules as $strReadModule => $val){
			$arrModuleDD[$strReadModule] = $strReadModule;
		}


		$arrMethods = array('' => '');
		if ($this->in->get('pdh_r_module') != ""){
			$object = register('pdh_r_'.$this->in->get('pdh_r_module'));
			$methods = get_class_methods($object);
			foreach($methods as $strMethodName){
				if (strpos($strMethodName, 'get_') === 0){
					$strMethodName_withoutget = substr($strMethodName, 4);
					if (strpos($strMethodName_withoutget, 'caption_') === 0) continue;

					if (strpos($strMethodName_withoutget, 'html_') === 0){
						continue;
					} else {
						if (!isset($arrMethods['html_'.$strMethodName_withoutget])) {
							$arrMethods[$strMethodName_withoutget] = $strMethodName_withoutget;
						}
					}
				}

			}
		}

		if ($this->in->get('pdh_r_module') != "" && $this->in->get('pdh_method') != "" && $this->in->get('pdh_r_module') == $this->in->get('pdh_r_old')){
			$className = 'pdh_r_'.$this->in->get('pdh_r_module');
			$methodName = 'get_'.$this->in->get('pdh_method');
			if (method_exists($className, $methodName)){
				$r = new ReflectionMethod($className, $methodName);
				$params = $r->getParameters();

				foreach ($params as $param) {
					try{
						$default = $param->getDefaultValue();
						if ($default === true) $default = 'true';
						if ($default === false) $default = 'false';
					}catch(ReflectionException $e){
						$default = '';
					}

					$this->tpl->assign_block_vars('param_row', array(
						'NAME' 			=> $param->getName(),
						'IS_OPTIONAL'	=> $param->isOptional(),
						'DEFAULT'		=> $default,
					));
				}
			}

			//Caption
			$strCaptionMethod = str_replace(array('html_', 'get_'), array('', 'get_caption_'), $methodName);

			if (method_exists($className, $strCaptionMethod)){
				$r = new ReflectionMethod($className, $strCaptionMethod);
				$params = $r->getParameters();

				foreach ($params as $param) {
					try{
						$default = $param->getDefaultValue();
						if ($default === true) $default = 'true';
						if ($default === false) $default = 'false';
					}catch(ReflectionException $e){
						$default = '';
					}

					$this->tpl->assign_block_vars('caption_row', array(
							'NAME' 			=> $param->getName(),
							'IS_OPTIONAL'	=> $param->isOptional(),
							'DEFAULT'		=> $default,
					));
				}
			}
		}

		$this->tpl->assign_vars(array (
			'NEW_PRESET_XML'			=> $readd_xml,
			'LAYOUT_DROPDOWN'			=> (new hdropdown('new_layout_source', array('options' => $layout_options, 'value' => $this->config->get('eqdkp_layout'))))->output(),
			'JS_LM_TABS'				=> $this->jquery->Tab_header('lm_tabs', true),
			'CSRF_DEL_TOKEN'			=> $this->CSRFGetToken('del'),
			'CSRF_DELPRESET_TOKEN'		=> $this->CSRFGetToken('del_pre'),

			'PDH_R_DD'					=> (new hdropdown('pdh_r_module', array('options' => $arrModuleDD, 'value' => $this->in->get('pdh_r_module'), 'js'=>'onchange="this.form.submit()"')))->output(),
			'PDH_METHODS_DD'			=> (new hdropdown('pdh_method', array('options' => $arrMethods, 'value' => $this->in->get('pdh_method'), 'js'=>'onchange="this.form.submit()"')))->output(),
			'PDH_R_OLD'					=> (new hhidden('pdh_r_old', array('value' => $this->in->get('pdh_r_module'))))->output(),
			'LAYOUT_COUNT'				=> $intLayouts,
			'USERPRESET_COUNT'			=> count($user_presets),
		));

		//$this->jquery->Tab_Select('lm_tabs', $tab);

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('lm_title'),
			'template_file'		=> 'admin/manage_pagelayouts.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('page_manager'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function edit($messages = false, $name = false){
		// The JavaScript Magic...
		$this->tpl->add_js( '
	// Return a helper with preserved width of cells
	var fixHelper = function(e, ui) {
		ui.children().each(function() {
			$(this).width($(this).width());
		});
		return ui;
	};

	$(".sortingtable tbody").sortable({
		helper: fixHelper,
		cancel: \'.not-sortable, .th_add, .td_add\',
	});

	// Delete the row
	$(document).on("click", ".del_me", function(){
		var mydelid		= jQuery.trim($(this).parent().parent().find(".delete_id").text());
		var mypreset	= jQuery.trim($(this).parent().parent().find(".presetname").text());
		$("#dp"+mydelid).prop("disabled", false);
		$("#button_"+mydelid).prop("disabled", false);
		$("#dp"+mydelid).append("<option value=\'"+mypreset+"\'>"+mypreset+"</option>");
		var body = $(this).parent().parent().parent();
		$(this).parent().parent().remove();
		if(body.children().length == 1) {
			body.find(".del_me").css("display", "none");
		}
	});

	$(".add_row_button").click(function() {
		var key = parseInt($(this).attr("id").substr(7));

		//reenable deletion
		$("#table_"+key+" > tbody").find(".del_me").removeAttr("style");

		var row = $("#table_"+key+" > tbody > tr:last").clone();
		var prefix		= jQuery.trim($(".prefix_id", row).text());
		var id			= jQuery.trim($(".delete_id", row).text());
		var selected	= jQuery.trim($("#dp"+id).val());
		var name		= jQuery.trim($("#dp"+id+ " :selected").text());


		// The name of the field
		$(".presetname", row).empty();
		$(".presetname", row).append(name);

		// The value fields
		$(".sortable", row).attr("name", prefix+"[sortable]["+selected+"]");
		$("label.sortable input", row).attr("name", prefix+"[sortable]["+selected+"]");
		$(".default_sort", row).val(selected);
		$(".td_add", row).attr("name", prefix+"[td_add]["+selected+"]");
		$(".th_add", row).attr("name", prefix+"[th_add]["+selected+"]");

		// Remove the option in select
		$("#dp"+id+" option[value=\'"+selected+"\']").remove();

		// Disable if no selection available
		if($("#dp"+id+" option").length == 0){
			$("#dp"+id).prop("disabled", true);
			$("#button_"+id).prop("disabled", true);
		}
		$("#table_"+key+" > tbody > tr:last").after(row);
	});

	$(".sortingtable tr:odd").addClass("row1");
	$(".sortingtable tr:even").addClass("row2");
	$("#lb_columns").change(function(){
		if($(this).val() == "classid") {
			$("#leaderboard_classid").removeAttr("style");
			$("#leaderboard_defaultrole").css("display", "none");
		} else {
			$("#leaderboard_classid").css("display", "none");
			$("#leaderboard_defaultrole").removeAttr("style");
		}
	});', 'docready');

		$layout_name = ($name) ? $name : $this->in->get('layout');
		if(!$this->pdh->user_layout_exists($layout_name)) $layout_name = $this->pdh->make_editable($layout_name);
		if(!$layout_name) $this->display(array('title' => $this->user->lang('error'), 'text' => $this->user->lang('layout_not_exists'), 'color' => 'red'));

		$this->pdh->auto_update_layout($layout_name);
		$layout_def = $this->pdh->get_eqdkp_layout($layout_name);

		//Tabs
		$this->jquery->Tab_header('plus_pm_pages_tabs');
		//Get all defined pages from current system file
		$pages = $this->pdh->get_page_list();

		$page_id = 1;
		$page_object_id = 1;

		$page_tabs = '';

		$table_sort_dirs['asc'] = $this->user->lang('lm_sort_asc');
		$table_sort_dirs['desc'] = $this->user->lang('lm_sort_desc');

		registry::load('form');

		//general options
		if (is_array($layout_def['options']) && !empty($layout_def['options'])){
			foreach ($layout_def['options'] as $key=>$value){
				$name = 'params_'.$key;
				$this->tpl->assign_block_vars('param_row', array(
					'NAME'	=> $value['lang'],
					'FIELD'	=> form::field($name, $value),
				));
			}
		}

		//substitutions
		if (is_array($layout_def['substitutions']) && !empty($layout_def['substitutions'])){
			foreach ($layout_def['substitutions'] as $key=>$value){
				$name = 'subs_'.$key;
				$this->tpl->assign_block_vars('subs_row', array(
					'NAME'	=> $value['lang'],
					'FIELD'	=> form::field($name, $value),
				));
			}
		}

		//iterate through all pages
		foreach($pages as $page) {
			$this->tpl->assign_block_vars('page_list', array(
				'ID' => md5($page),
				'NAME' => $this->user->lang('lm_page_'.$page, true),
				'ADMIN' => (strpos($page, 'admin') !== false) ? true : false
			));

			//get page settings
			$page_settings = $layout_def['pages'][$page];

			//default values
			if (!$page_settings){
				$page_settings = $this->pdh->get_page_settings($page);
			}

			$this->tpl->assign_block_vars('page_row', array(
				'ID' => md5($page),
				'S_LEADERBORD'	=> ($page == 'listmembers') ? true : false,
				'S_ROSTER'		=> ($page == 'roster') ? true : false,
			));

			//Leaderbord-Settings
			if ($page == 'listmembers'){
				$column_type = (isset($page_settings['listmembers_leaderboard']['column_type'])) ? $page_settings['listmembers_leaderboard']['column_type'] : 'classid';
				$this->tpl->assign_vars(array(
					'LB_MAXPERCOLUMN'	=> $page_settings['listmembers_leaderboard']['maxpercolumn'],
					'LB_MAXPERROW'	=> $page_settings['listmembers_leaderboard']['maxperrow'],
					'LB_SORTDIR'	=> (new hdropdown('lb_sortdir', array('options' => $table_sort_dirs, 'value' => $page_settings['listmembers_leaderboard']['sort_direction'])))->output(),
					'LB_COLUMN_DD'	=> (new hdropdown('lb_columns', array('options' => array('classid' => $this->user->lang('class'), 'defaultrole' => $this->user->lang('role')), 'value' => $column_type)))->output(),
					'LB_POOL_DD'	=> (new hdropdown('lb_default_pool', array('options' => $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list'))), 'value' => $page_settings['listmembers_leaderboard']['default_pool'])))->output(),
					'CLASSDISPLAY'	=> ($column_type == 'classid') ? '' : 'style="display: none;"',
					'ROLEDISPLAY'	=> ($column_type == 'defaultrole') ? '' : 'style="display: none;"',
				));
				if($page_settings['listmembers_leaderboard']['column_type'] == 'classid') {
					$classes = $page_settings['listmembers_leaderboard']['columns'];
					$roles = $this->pdh->get('roles', 'id_list');
				} else {
					$classes = array_keys($this->game->get_primary_classes(array('id_0')));
					$roles = $page_settings['listmembers_leaderboard']['columns'];
				}
				$arrGameClasses = array_keys($this->game->get('classes', 'id_0'));
				$arrDiff = array_diff($arrGameClasses, $classes);
				foreach($arrDiff as $val){
					array_push($classes, $val);
				}

				foreach($classes as $class){
					$this->tpl->assign_block_vars('page_row.class_row', array(
						'CLASS'	=> $class,
						'NAME'	=>	$this->game->decorate('primary', $class).' '.$this->game->get_name('primary', $class),
					));
				}
				foreach($roles as $role){
					$this->tpl->assign_block_vars('page_row.role_row', array(
						'ROLE'	=> $role,
						'NAME'	=>	$this->game->decorate('roles', $role).' '.$this->pdh->get('roles', 'name', array($role)),
					));
				}
			}

			//Roster-Settings
			if ($page == 'roster'){
				$this->tpl->assign_vars(array(
					'ROSTER_DD'			 => (new hdropdown('roster_classorrole', array('options' => array('class' => $this->user->lang('class'), 'role' => $this->user->lang('role'), 'raidgroup' => $this->user->lang('raidevent_raid_groupsmenu'), 'rank' => $this->user->lang('rank'), 'none' => $this->user->lang('none')), 'value' => $this->config->get('roster_classorrole'))))->output(),
					'ROSTER_SHOW_TWINKS' => ($this->config->get('roster_show_twinks')) ? ' checked="checked"' : '',
					'ROSTER_SHOW_HIDDEN' => ($this->config->get('roster_show_hidden')) ? ' checked="checked"' : '',
				));
			}

			//SK Startlist
			/*
			if ($page == 'listmembers' && ($layout_def['base_layout'] == 'sk' || $layout_def['base_layout'] == 'sk_bottom' || $layout_def['base_layout'] == 'sk_fixed')){

			}
			*/

			//iterate through defined objects
			foreach($page_settings as $page_object => $options) {
				$add_setts = array();
				//for now only show html pdh tag tables (hptt)
				if(substr($page_object,0,4) == 'hptt'){
					$potential_presets = array_keys($this->pdh->get_preset_list($options['table_main_sub'], $options['table_subs'], array_keys($layout_def['subs'])));
					$pps = array();
					foreach($potential_presets as $id => $pset){
						$pps[$pset] = ($this->pdh->get_preset_description($pset)) ? $this->pdh->get_preset_description($pset, true) : $pset;
					}

					foreach($options['table_presets'] as $column_id => $column_options){
						$preset = $column_options['name'];
						unset($pps[$preset]);
					}
					if(!isset($options['table_sort_dir'])) $options['table_sort_dir'] = 'desc';
					if($page_object == 'hptt_listmembers_memberlist_overview') {
						$this->init_multipools();
						if(!isset($options['default_pool'])) $options['default_pool'] = 0;
						if(!isset($options['default_pool_ov'])) $options['default_pool_ov'] = 1;
						$add_setts[] = array(
							'LANG'	=> $this->user->lang('lm_default_pool'),
							'FIELD'	=> (new hdropdown($page.'['.$page_object.'][default_pool]', array('options' => $this->multi_pools, 'value' => $options['default_pool'])))->output(),
						);
						$arrOvPools = $this->multi_pools;
						unset($arrOvPools[0]);
						$add_setts[] = array(
							'LANG'	=> $this->user->lang('lm_default_pool_ov'),
							'FIELD'	=> (new hdropdown($page.'['.$page_object.'][default_pool_ov]', array('options' => $arrOvPools, 'value' => $options['default_pool_ov'])))->output(),
						);
					}
					$this->tpl->assign_block_vars('page_row.page_object_row', array(
						'TABLE_TITLE'		=> $this->user->lang('lm_'.$page_object),
						'ID'				=> $page_object_id,
						'NAME'				=> $page_object,
						'DROPDOWN'			=> (new hdropdown('dp'.$page_object_id, array('options' => $pps, 'id' => 'dp'.$page_object_id, 'disabled' => ((count($pps) == 0) ? true : false))))->output(),
						'PREFIX'			=> $page.'['.$page_object.']',
						'NUMBERS'			=> (new hradio($page.'['.$page_object.'][numbers]', array('value' => $options['show_numbers'])))->output(),
						'TABLE_SORT_DIR'	=> (new hdropdown($page.'['.$page_object.'][table_sort_dir]', array('options' => $table_sort_dirs, 'value' => $options['table_sort_dir'], 'id' => $page.'_'.$page_object.'_sort_dir')))->output(),
						'DISABLED'			=> (count($pps) == 0) ? 'disabled="disabled"' : '',
						'S_ADD_SETTS'		=> (count($add_setts) > 0) ? true : false,
					));
					foreach($options['table_presets'] as $column_id => $column_options){
						$preset = $column_options['name'];
						$this->tpl->assign_block_vars('page_row.page_object_row.preset_row', array(
							'NAME'			=> ($this->pdh->get_preset_description($preset)) ? $this->pdh->get_preset_description($preset, true) : $preset,
							'SORTABLE'		=> (new hradio($page.'['.$page_object.'][sortable]['.$preset.']', array('value' => $column_options['sort'], 'class' => 'sortable')))->output(),
							'CODE'			=> $preset,
							'DEFAULT_SORT'	=> (isset($options['table_sort_col']) && $options['table_sort_col'] == $column_id) ? 'checked="checked"' : '',
							'TH_ADD'		=> sanitize($column_options['th_add']),
							'TD_ADD'		=> sanitize($column_options['td_add']),
							'ID'			=> $page_object_id,
						));
					}
					if(count($add_setts)) {
						foreach($add_setts as $sett) {
							$this->tpl->assign_block_vars('page_row.page_object_row.add_setts', $sett);
						}
					}
					$page_object_id++;
				}
			}
		}

		$this->tpl->assign_vars(array (
			'FILENAME'				=> $layout_name,
			'DESCRIPTION'			=> $this->pdh->get_eqdkp_layout_description($layout_name),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('lm_title'),
			'template_file'		=> 'admin/manage_pagelayouts_edit.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('page_manager'), 'url'=>$this->root_path.'admin/manage_pagelayouts.php'.$this->SID],
				['title'=>$this->user->lang('lm_title'), 'url'=>$this->root_path.'admin/manage_pagelayouts.php'.$this->SID],
				['title'=>$layout_name, 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	private function init_multipools() {
		if(!count($this->multi_pools)) {
			$this->multi_pools = $this->pdh->aget('multidkp', 'name', 0, array($this->pdh->get('multidkp', 'id_list')));
			$this->multi_pools[0] = 'AAAAA'; //to force 0 at first position
			asort($this->multi_pools);
			$this->multi_pools[0] = $this->user->lang('overview');
		}
	}
}
registry::register('ManagePageLayouts');
