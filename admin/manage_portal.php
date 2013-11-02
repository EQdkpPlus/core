<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
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
$eqdkp_root_path = '../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Portal extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'core', 'config', 'html', 'pm', 'portal', 'env' => 'environment');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array(
			'save' => array('process' => 'save', 'csrf'=>true),			
			'save_block' => array('process' => 'save_block',  'csrf'=>true),
			'settings' => array('process' => 'ajax_load_settings'),
			'save_sett' => array('process' => 'save_settings', 'csrf'=>true),
			'duplicate'	=> array('process' => 'duplicate'),
			'id' => array('process' => 'edit'),
			'l'	=> array('process' => 'edit_portallayout'),
			'b'	=> array('process' => 'edit_portalblock'),
			'del_layouts' => array('process' => 'delete_portallayout',  'csrf'=>true),
			'del_blocks' => array('process' => 'delete_portalblock',  'csrf'=>true),
			
		);
		parent::__construct(false, $handler);

		// Check user permission
		$this->user->check_auth('a_extensions_man');
		$this->process();
	}
	
	public function save_block(){
		$intBlockID = $this->in->get('b', 0);
		if ($this->in->get('name') == '') $this->edit_portalblock();
		
		if ($intBlockID){
			$blnResult = $this->pdh->put('portal_blocks', 'update', array($intBlockID, $this->in->get('name'), $this->in->get('wide_content', 0)));
		} else {
			$blnResult = $this->pdh->put('portal_blocks', 'add', array($this->in->get('name'), $this->in->get('wide_content', 0)));
		}
		
		if ($blnResult){
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('success'), 'green');
			$this->pdh->process_hook_queue();
			$this->display();
		}
	}
	
	public function delete_portallayout(){
		$retu = array();
	
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
	
				$pos[] = stripslashes($this->pdh->get('portal_layouts', 'name', array($id)));
				$retu[$id] = $this->pdh->put('portal_layouts', 'delete', array($id));
			}
		}
	
		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
			$this->core->messages($messages);
		}
	
		$this->pdh->process_hook_queue();
	}
	
	public function delete_portalblock(){
		$retu = array();
	
		if(count($this->in->getArray('selected_ids', 'int')) > 0) {
			foreach($this->in->getArray('selected_ids','int') as $id) {
	
				$pos[] = stripslashes($this->pdh->get('portal_blocks', 'name', array($id)));
				$retu[$id] = $this->pdh->put('portal_blocks', 'delete', array($id));
			}
		}
	
		if(!empty($pos)) {
			$messages[] = array('title' => $this->user->lang('del_suc'), 'text' => implode(', ', $pos), 'color' => 'green');
			$this->core->messages($messages);
		}
	
		$this->pdh->process_hook_queue();
	}
	
	
	//Get Portal Module Settings
	private function get_settings($id, $save=false, $state='fetch_new') {
		$module = $this->portal->get_module($this->pdh->get('portal', 'path', array($id)), $this->pdh->get('portal', 'plugin', array($id)));
		$module->set_id($id);
		$data = $module->get_settings($state);
		if(!$data) return array();
		$save_array = array();
		$child = $this->pdh->get('portal', 'child', array($id));

		foreach($data as $confvars) {
			$options			= $confvars;
			$options['type']	= $confvars['property'];
			//is child?
			if ($child){
				$confvars['name'] = $confvars['name'].'_'.$id;
			}


			if ($this->config->get($confvars['name']) !==false){
				$options['value']	= $options['selected'] = $this->config->get($confvars['name']);
			} else {
				$options['selected'] = (isset($options['selected'])) ? $options['selected'] : ((isset($options['value'])) ? $options['value'] : '');
			}
			if($options['type'] == 'jq_multiselect' && $options['selected']) {
				$options['selected'] = (isset($options['selected']) && strlen($options['selected'])) ? unserialize($options['selected']) : array();
			}

			$ccfield			= $this->html->widget($options);
			if($ccfield) {
				if($save) {
					$save_array[$confvars['name']]	= $this->html->widget_return($options);
				} else {
					$this->tpl->assign_block_vars('config_row', array(
						'NAME'	=> $this->user->lang($confvars['language'], true),
						'FIELD'	=> $ccfield,
						'HELP'	=> (isset($confvars['help'])) ? $this->user->lang($confvars['help'], false, false) : '',
						'ID'	=> $confvars['name'],
					));
					$save_array[$confvars['name']] = array(
						'name' => $this->user->lang($confvars['language'], true),
						'field'=> $ccfield,
						'help' => (isset($confvars['help'])) ? $this->user->lang($confvars['help'], false, false) : '',
						'type' => $options['type'],
						'change' => (isset($options['change'])) ? true : false,
					);
				}
			}
		}
		return $save_array;
	}
	
	//Load Portal Module Settings
	public function ajax_load_settings(){
		$id = $this->in->get('id', 0);
		//get old settings
		$old_settings = $this->get_settings($id, false, 'fetch_old');
		//save settings
		$this->save_settings(false);
		//get new settings
		$new_settings = $this->get_settings($id, false, 'fetch_new');
		$out = array();
		$out['new'] = array_diff_key($new_settings, $old_settings);
		//get removed settings
		$out['removed'] = array_diff_key($old_settings, $new_settings);

		//search for changed settings
		foreach ($old_settings as $key => $value){
			if (isset($new_settings[$key]) && ($value['field'] != $new_settings[$key]['field'] || $value['name'] != $new_settings[$key]['name'])){
				$out['changed'][$key] = $new_settings[$key];
			}
		}
		//search new settings for javascript, if there is some, force reload;
		$arrForceReload = array('datepicker');
		foreach ($new_settings as $key => $value){
			if (in_array($value['type'], $arrForceReload)) $out['reload'] = 1;
		}

		echo json_encode($out);
		exit;
	}
	
	//Save Portal Module Settings
	public function save_settings($displayPage = true) {
		if($id = $this->in->get('id')){
			$this->pdh->put('portal', 'update', array($id, array('collapsable' => $this->in->get('collapsable', 0), 'visibility' => serialize($this->in->getArray('visibility', 'int')))));
			$save_array = $this->get_settings($id, true, 'save');
			if(count($save_array) > 0){
				$this->config->set($save_array);
			}
			$this->portal->reset_portal($this->pdh->get('portal', 'path', array($id)), $this->pdh->get('portal', 'plugin', array($id)));
			$this->pdh->process_hook_queue();
			if (!$displayPage ) return;
			$this->core->message($this->user->lang('save_suc'), $this->user->lang('success'), 'green');
			$this->edit($id);
		}
	}
	
	//Duplicate Portal Module
	public function duplicate(){
		$path = $this->pdh->get('portal', 'path', array($this->in->get('selected_id', 0)));
		if(!$path) $this->display();
		$plugin = $this->pdh->get('portal', 'plugin', array($this->in->get('selected_id')));
		$name = (is_object($obj)) ? $obj->get_data('name') : $path;
		$this->core->message(sprintf($this->user->lang('portal_duplicated_success'), $name), $this->user->lang('success'), 'green');
		$this->portal->install($path, $plugin, true);
		$this->edit_portallayout();
	}
	
	//Delete Portal Module
	public function delete() {
		$path = $this->pdh->get('portal', 'path', array($this->in->get('selected_id', 0)));
		if(!$this->pdh->get('portal', 'child', array($this->in->get('selected_id',0))) || !$path) $this->display();
		$plugin = $this->pdh->get('portal', 'plugin', array($this->in->get('selected_id')));
		$obj = $this->portal->get_module($path, $plugin);
		$name = (is_object($obj)) ? $obj->get_data('name') : $path;
		$this->core->message(sprintf($this->user->lang('portal_delete_success'), $name), $this->user->lang('success'), 'green');
		$this->portal->uninstall($path, $plugin, $this->in->get('selected_id', 0));
		$this->edit_portallayout();
	}
	
	//Edit Portal Module
	public function edit($id=false) {
		if($id || $id = $this->in->get('id', 0)) {
			$this->get_settings($id);
			// User groups
			$drpdwn_rights = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
			$drpdwn_rights[0] = $this->user->lang('cl_all');
			$drpdwn_rights[999999999] = $this->user->lang('viewing_wrapper');
			
			ksort($drpdwn_rights);
			$arrVisibilityOptions = array('no_lang' => true);
			if ($this->portal->get_module($this->pdh->get('portal', 'path', array($id)), $this->pdh->get('portal', 'plugin', array($id)))->LoadSettingsOnchangeVisibility){
				$arrVisibilityOptions['javascript'] = ' onchange="load_settings()"';
			}
			$this->tpl->assign_block_vars('config_row', array(
				'NAME'	=> $this->user->lang('portalplugin_rights'),
				'FIELD'	=> $this->jquery->MultiSelect('visibility', $drpdwn_rights, $this->pdh->get('portal', 'visibility', array($id)), $arrVisibilityOptions),
				'ID'	=> 'visibility',
			));
			//Collapsable
			$this->tpl->assign_block_vars('config_row', array(
				'NAME'	=> $this->user->lang('portal_collapsable'),
				'FIELD'	=> new hcheckbox('collapsable', array('value' => $this->pdh->get('portal', 'collapsable', array($id)))),
				'ID'	=> 'collapsable',
			));

		}		
		$this->tpl->assign_var('ACTION', $_SERVER['SCRIPT_NAME'].$this->SID.'&amp;id='.$id.'&amp;simple_head=simple');
		$this->tpl->assign_var('MODULE_ID', $id);
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('portalplugin_management'),
			'template_file'		=> 'admin/manage_portal_moduleconfig.html',
			'header_format'		=> $this->simple_head,
			'display'			=> true
		));
	}
	
	//Save Portal Layout
	public function save() {
		$intLayoutID = $this->in->get('l', 0);
		$strName = $this->in->get('name');
		$arrBlocks = $this->in->getArray('portal_blocks', 'string');
		
		$arrBlockModules = array();

		foreach($this->in->getArray('pos', 'string') as $intModuleID => $strBlock) {
			if ($strBlock == 'disabled') continue;
			$arrBlockModules[$strBlock][] = $intModuleID;
		}

		if ($intLayoutID){
			$blnResult = $this->pdh->put('portal_layouts', 'update', array($intLayoutID, $strName, $arrBlocks, $arrBlockModules));
		} else {
			$blnResult = $this->pdh->put('portal_layouts', 'add', array($strName, $arrBlocks, $arrBlockModules));	
		}
		
		if($blnResult) {
			$this->core->message($this->user->lang('portal_saved'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('portal_not_saved'), $this->user->lang('error'), 'red');
		}
		
		$this->pdh->process_hook_queue();
		$this->portal->reset();
		$this->edit_portallayout();
	}
	
	//Edit Portal Layout
	public function edit_portallayout() {
		$intLayoutID = $this->in->get('l', 0);
		$arrUsedBlocks = ($intLayoutID) ? $this->pdh->get('portal_layouts', 'blocks', array($intLayoutID)) : array('left', 'right', 'middle', 'bottom');
		$arrUsedBlockModules = ($intLayoutID) ? $this->pdh->get('portal_layouts', 'modules', array($intLayoutID)) : array();
		
		$arrSortIDs = array();
		foreach($arrUsedBlockModules as $strBlock => $arrModules){
			foreach($arrModules as $sortID => $intModuleID){
				$arrUsedModules[$intModuleID] = $strBlock;
				$arrSortIDs[$intModuleID] = $sortID;
			}
		}

		$arrBlocksIDList = $this->pdh->get('portal_blocks', 'id_list');
		
		// Install the Plugins if required
		$portal_module = $this->portal->get_all_modules();

		$filter = array();
		if($this->in->exists('fvisibility') && $this->in->get('fvisibility', 0) !== 0) $filter['visibility'] = $this->in->get('fvisibility', 0);
		$arrModuleIDs = $this->pdh->get('portal', 'id_list', array($filter));

		$arrSort = array();
		foreach($arrModuleIDs as $intModuleID){
			$arrSort[] = (isset($arrSortIDs[$intModuleID])) ? $arrSortIDs[$intModuleID] : 9999999;
		}
		
		array_multisort($arrSort, SORT_ASC, SORT_NUMERIC, $arrModuleIDs);
			
		$modules = $this->pdh->aget('portal', 'path', 0, array($arrModuleIDs), true);
		
		
		$arrModulesForOwnBlocks = array();
		foreach($modules as $id => &$data) {
			$path = $data['path'];
			if(!$portal_module[$path]) {
				unset($data);
				unset($modules[$id]);
				continue;
			}
			$this->portal->load_lang($path, $this->pdh->get('portal', 'plugin', array($id)));
			$portalinfos = $portal_module[$path]->get_data();
			$data['name'] = ($this->user->lang($path.'_name')) ? $this->user->lang($path.'_name') : $portalinfos['name'];
			$data['icon'] = $portalinfos['icon'];
			$contact = (strpos($portalinfos['contact'], '@')=== false) ? $portalinfos['contact'] : 'mailto:'.$portalinfos['contact'];
			$data['desc'] = ($this->user->lang($path.'_desc')) ? $this->user->lang($path.'_desc') : $portalinfos['description'];
			$data['desc'] .= "<br />".$this->user->lang('portalplugin_version').": ".$portalinfos['version']."<br />".$this->user->lang('portalplugin_author').": ".$portalinfos['author'];
			$pdata = $this->pdh->get('portal', 'portal', array($id));
			
			$data['tpl_posi'] = 'disabled';
			if (isset($arrUsedModules[$id])){
				if (in_array($arrUsedModules[$id], array('left', 'right', 'bottom', 'middle'))){
					$data['tpl_posi'] = $arrUsedModules[$id];
				} elseif(in_array(str_replace('block', '', $arrUsedModules[$id]), $arrBlocksIDList)) {
					$data['tpl_posi'] = 'later';
				}
			}

			// Build the icon for the portal modules...
			if($pdata['plugin']){
				$icon = ($this->pm->get_data($pdata['plugin'], 'icon')) ? $this->pm->get_data($pdata['plugin'], 'icon') : 'fa-puzzle-piece';
			}elseif(isset($data['icon'])){
				$icon = $data['icon'];
			}else{
				$icon = 'fa-info-circle';
			}
			
			// start the description text
			$data['desc']		= $this->html->ToolTip($data['desc'], $this->core->icon_font($icon, 'fa-lg'));
			$data['multiple']	= ($portal_module[$path]->get_multiple() && !$pdata['child']) ? true : false;
			if ($portal_module[$path]->get_multiple()){
				$portal_module[$path]->set_id($id);
				$portal_module[$path]->output();
				$data['header'] = ' ('.$portal_module[$path]->get_header().')';
			} else {
				$data['header'] = '';
			}
			$data['child'] = $pdata['child'];
			if($data['tpl_posi'] != 'later' && $data['tpl_posi'] != 'disabled') {
				$this->tpl->assign_block_vars($data['tpl_posi'].'_row', array(
					'NAME'			=> $data['name'].$data['header'],
					'CLASS'			=> $data['class'],
					'ID'			=> $id,
					'POS'			=> $data['tpl_posi'],
					'INFO'			=> $data['desc'],
					'S_MULTIPLE'	=> $data['multiple'],
					'S_CHILD'		=> $data['child'],
				));
				unset($data);
				unset($modules[$id]);
			}
		}
		unset($data); //important, strange results without this
		function my_sort($a, $b) {
			if ($a['name'] == $b['name']) {
				return 0;
			}
			return ($a['name'] < $b['name']) ? -1 : 1;
		}
		uasort($modules, 'my_sort');
		foreach($modules as $id => $data) {
			$tpl_data = array(
				'NAME'			=> $data['name'].$data['header'],
				'CLASS'			=> $data['class'],
				'ID'			=> $id,
				'POS'			=> $data['tpl_posi'],
				'INFO'			=> $data['desc'],
				'S_MULTIPLE'	=> $data['multiple'],
				'S_CHILD'		=> $data['child'],
			);
			
			if ($data['tpl_posi'] == 'later'){
				$tpl_data['POS'] = $arrUsedModules[$id];
				$arrModulesForOwnBlocks[$arrUsedModules[$id]][] = $tpl_data;
			} else {
				$this->tpl->assign_block_vars($data['tpl_posi'].'_row', $tpl_data);
			}
			
		}
		$this->portal->init_portalsettings();
		$this->confirm_delete($this->user->lang('portal_delete_warn'), 'manage_portal.php'.$this->SID.'&del=true&l='.$intLayoutID, true, array('function' => 'delete_portal'));

		$filter_rights = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		$filter_rights[0] = $this->user->lang('portalplugin_filter3_all');
		ksort($filter_rights);
		
		$arrBlockList = array(
			'left'		=> $this->user->lang('portalplugin_left'), 
			'middle'	=> $this->user->lang('portalplugin_middle'), 
			'bottom'	=> $this->user->lang('portalplugin_bottom'), 
			'right'		=> $this->user->lang('portalplugin_right'),
		);
		foreach($this->pdh->get('portal_blocks', 'id_list') as $intBlockID){
			$arrBlockList['block'.$intBlockID] = $this->pdh->get('portal_blocks', 'name', array($intBlockID));
		}
		
		$this->add_js(array_keys($arrBlockList));
		//Bring Blocks to template
		foreach($this->pdh->get('portal_blocks', 'id_list') as $intBlockID){
			$this->tpl->assign_block_vars('block_row', array(
				'ID'		=> $intBlockID,
				'NAME'		=> $this->pdh->get('portal_blocks', 'name', array($intBlockID)),
				'S_HIDDEN'	=> (!in_array('block'.$intBlockID, $arrUsedBlocks)),
			));
			
			if (isset($arrModulesForOwnBlocks['block'.$intBlockID])){
				foreach($arrModulesForOwnBlocks['block'.$intBlockID] as $tpl_data){
					$this->tpl->assign_block_vars('block_row.module_row', $tpl_data);
				}
			}
		}
		
		$this->tpl->assign_vars(array(
				'PERM_FILTER' 		=> new hdropdown('fvisibility', array('options' => $filter_rights, 'value' => $this->in->get('fvisibility', 0), 'js' => 'onchange="javascript:form.submit();"')),
				'NAME'				=> ($intLayoutID) ? $this->pdh->get('portal_layouts', 'name', array($intLayoutID)) : '',
				'MS_PORTAL_BLOCKS'	=> $this->jquery->MultiSelect('portal_blocks', $arrBlockList, (($intLayoutID) ? $this->pdh->get('portal_layouts', 'blocks', array($intLayoutID)) : array('left', 'right', 'middle', 'bottom')), array('width' => 300)),
				'S_RIGHT_HIDDEN'	=> (!in_array('right', $arrUsedBlocks)),
				'S_LEFT_HIDDEN'		=> (!in_array('left', $arrUsedBlocks)),
				'S_MIDDLE_HIDDEN'	=> (!in_array('middle', $arrUsedBlocks)),
				'S_BOTTOM_HIDDEN'	=> (!in_array('bottom', $arrUsedBlocks)),
				'LAYOUT_ID'			=> $intLayoutID,
				'EMBEDD_URL'		=> $this->env->link,
		));
		
		$this->jquery->Validate("portal_layout_form");
		
		$this->jquery->Dialog('portalsettings', $this->user->lang('portalplugin_winname'), array('url'=>$this->root_path."admin/manage_portal.php".$this->SID."&simple_head=true&reload=1&id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid', 'onclose' => 'manage_portal.php'.$this->SID.'&l='.$intLayoutID));
		$this->tpl->add_css(".portal_disabled { float:left; margin-left: 4px; width:230px; min-height: 16px;}");
		$this->tpl->add_js('$(".equalHeights").equalHeights();', 'docready');
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('portalplugin_management'),
			'template_file'		=> 'admin/manage_portal_layout.html',
			'display'			=> true)
		);
	}
	
	public function edit_portalblock(){
		$intBlockID = $this->in->get('b', 0);
		if ($intBlockID){
			$this->tpl->assign_vars(array(
					'NAME'					=> $this->pdh->get('portal_blocks', 'name', array($intBlockID)),
					'WIDE_CONTENT_CHECKED'	=> ($this->pdh->get('portal_blocks', 'wide_content', array($intBlockID))) ? 'checked="checked"' : '',
					'BLOCKID'				=> $intBlockID,
			));
		}
		
		$this->jquery->Validate("portal_block_form");
		
		$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('edit_portal_block'),
				'template_file'		=> 'admin/manage_portal_block.html',
				'display'			=> true)
		);
	}
	
	//Display Layout and Block List
	public function display(){
		$this->jquery->Tab_header('portal_tabs');
		
		//Portal Layouts
		$view_list = $this->pdh->get('portal_layouts', 'id_list', array());
		
		$hptt_page_settings = array(
				'name'				=> 'hptt_admin_manage_portal_layouts_list',
				'table_main_sub'	=> '%layout_id%',
				'page_ref'			=> 'manage_portal.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'asc',
				'table_sort_col'	=> 1,
				'table_presets'		=> array(
						array('name' => 'portal_layout_editicon','sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
						array('name' => 'portal_layout_name','sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'portal_layout_blocks','sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'portal_layout_usedby',		'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
		);
		
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_portal.php', '%link_url_suffix%' => '&amp;upd=true'));
		$sort_suffix = '?sort='.$this->in->get('sort');

		$this->confirm_delete($this->user->lang('confirm_delete_articles'));
		
		$this->tpl->assign_vars(array(
				'LAYOUT_LIST' 		=> $hptt->get_html_table($this->in->get('sort'),false,null,1,null,false, array('portal_layouts', 'checkbox_check')),
				'HPTT_LAYOUT_LIST_COLUMN_COUNT'	=> $hptt->get_column_count(),
		));
		
		//Portal blocks
		$view_list = $this->pdh->get('portal_blocks', 'id_list', array());
		
		$hptt_page_settings = array(
				'name'				=> 'hptt_admin_manage_portal_block_list',
				'table_main_sub'	=> '%block_id%',
				'page_ref'			=> 'manage_portal.php',
				'show_numbers'		=> false,
				'show_select_boxes'	=> true,
				'selectboxes_checkall'=>true,
				'show_detail_twink'	=> false,
				'table_sort_dir'	=> 'asc',
				'table_sort_col'	=> 1,
				'table_presets'		=> array(
						array('name' => 'portal_block_editicon','sort' => false, 'th_add' => 'width="20"', 'td_add' => ''),
						array('name' => 'portal_block_name','sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'portal_block_wide_content','sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'portal_block_templatevar','sort' => true, 'th_add' => '', 'td_add' => ''),
						array('name' => 'portal_block_usedby','sort' => true, 'th_add' => '', 'td_add' => ''),
				),
		);
		
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'manage_portal.php', '%link_url_suffix%' => '&amp;upd=true'));
		$sort_suffix = '?sort='.$this->in->get('sort');
		
		$this->tpl->assign_vars(array(
				'BLOCK_LIST' 					=> $hptt->get_html_table($this->in->get('sort')),
				'HPTT_BLOCK_LIST_COLUMN_COUNT'	=> $hptt->get_column_count(),
		));
		
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('portalplugin_management'),
			'template_file'		=> 'admin/manage_portal.html',
			'display'			=> true)
		);
	}

	private function add_js($arrBlocks) {
		$this->tpl->add_js(
'				$("#'.implode(',#', $arrBlocks).', #disabled").sortable({
					connectWith: \'.connectedSortable\',
					cancel: ".ui-state-disabled",
					cursor: \'pointer\',

					receive: function(event, ui){
						var classI = $(ui.item).attr("class").toString();
						var pos = $(ui.item).parents().attr("id");
			
						if (pos == "disabled"){$(ui.item).addClass("portal_disabled");}else{$(ui.item).removeClass("portal_disabled");};

						var id = $(ui.item).attr("id");
						$("#block_"+id).val(pos);
					},
					placeholder: "ui-state-highlight"
				}).disableSelection();', 'docready');
	}
}
registry::register('Manage_Portal');
?>