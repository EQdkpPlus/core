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
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'core', 'config', 'html', 'pm', 'portal');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array(
			'save' => array('process' => 'save', 'csrf'=>true),
			'settings' => array('process' => 'ajax_load_settings'),
			'save_sett' => array('process' => 'save_settings', 'csrf'=>true),
			'duplicate'	=> array('process' => 'duplicate'),
			'id' => array('process' => 'edit'),
		);
		parent::__construct(false, $handler);

		// Check user permission
		$this->user->check_auth('a_extensions_man');
		$this->process();
	}

	private function get_settings($id, $save=false) {
		$module = $this->portal->get_module($this->pdh->get('portal', 'path', array($id)), $this->pdh->get('portal', 'plugin', array($id)));
		$module->set_id($id);
		$data = $module->get_settings();
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

	public function ajax_load_settings(){
		$id = $this->in->get('id', 0);
		//get old settings
		$old_settings = $this->get_settings($id);
		//save settings
		$this->save_settings(false);
		//get new settings
		$new_settings = $this->get_settings($id);
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

	public function save_settings($displayPage = true) {
		if($id = $this->in->get('id')){
			$this->pdh->put('portal', 'update', array($id, array('collapsable' => $this->in->get('collapsable', 0), 'visibility' => serialize($this->in->getArray('visibility', 'int')))));
			$save_array = $this->get_settings($id, true);
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

	public function duplicate(){
		$path = $this->pdh->get('portal', 'path', array($this->in->get('selected_id', 0)));
		if(!$path) $this->display();
		$plugin = $this->pdh->get('portal', 'plugin', array($this->in->get('selected_id')));
		$name = (is_object($obj)) ? $obj->get_data('name') : $path;
		$this->core->message(sprintf($this->user->lang('portal_duplicated_success'), $name), $this->user->lang('success'), 'green');
		$this->portal->install($path, $plugin, true);
		$this->display();
	}

	public function delete() {
		$path = $this->pdh->get('portal', 'path', array($this->in->get('selected_id', 0)));
		if(!$this->pdh->get('portal', 'child', array($this->in->get('selected_id',0))) || !$path) $this->display();
		$plugin = $this->pdh->get('portal', 'plugin', array($this->in->get('selected_id')));
		$obj = $this->portal->get_module($path, $plugin);
		$name = (is_object($obj)) ? $obj->get_data('name') : $path;
		$this->core->message(sprintf($this->user->lang('portal_delete_success'), $name), $this->user->lang('success'), 'green');
		$this->portal->uninstall($path, $plugin, $this->in->get('selected_id', 0));
		$this->display();
	}

	public function edit($id=false) {
		if($id || $id = $this->in->get('id', 0)) {
			$this->get_settings($id);
			// User groups
			$drpdwn_rights = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
			$drpdwn_rights[0] = $this->user->lang('cl_all');
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
				'FIELD'	=> $this->html->Checkbox('collapsable', '', $this->pdh->get('portal', 'collapsable', array($id))),
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

	public function save() {
		$sort_data = $this->in->getArray('sort', 'int');
		$sort = array();
		foreach($sort_data as $num => $data) {
			foreach($data as $id => $one) {
				$sort[$id] = $num;
			}
		}
		$retu = array();
		foreach($this->in->getArray('pos', 'string') as $id => $posi) {
			if($posi == 'disabled')	$retu[] = $this->pdh->put('portal', 'disable_enable', array($id));
			else $retu[] = $this->pdh->put('portal', 'update', array($id, array('enabled' => 1, 'position' => $posi, 'number' => $sort[$id])));
		}
		if(!in_array(false, $retu, true)) {
			$this->core->message($this->user->lang('portal_saved'), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('portal_not_saved'), $this->user->lang('error'), 'red');
		}
		$this->pdh->process_hook_queue();
		$this->portal->reset();
		$this->display();
	}

	public function display() {
		$this->add_js();

		// Install the Plugins if required
		$portal_module = $this->portal->get_all_modules();

		$filter = array();
		if($this->in->exists('fvisibility') && $this->in->get('fvisibility', 0) !== 0) $filter['visibility'] = $this->in->get('fvisibility', 0);

		$modules = $this->pdh->aget('portal', 'path', 0, array($this->pdh->sort($this->pdh->get('portal', 'id_list', array($filter)), 'portal', 'number')), true);
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
			$contact = (strpos($portalinfos['contact'], '@')=== false) ? $portalinfos['contact'] : 'mailto:'.$portalinfos['contact'];
			$data['desc'] = ($this->user->lang($path.'_desc')) ? $this->user->lang($path.'_desc') : $portalinfos['description'];
			$data['desc'] .= "<br />".$this->user->lang('portalplugin_version').": ".$portalinfos['version']."<br />".$this->user->lang('portalplugin_author').": ".$portalinfos['author'];
			$pdata = $this->pdh->get('portal', 'portal', array($id));
			$data['class'] = '';
			foreach ($portal_module[$path]->get_positions() as $value) {
				$data['class'] .= 'P'.$value.' ';
			}

			$data['tpl_posi'] = ($pdata['enabled']) ? $pdata['position'] : 'disabled';
			$icon = ($pdata['plugin']) ? (($this->pm->get_data($pdata['plugin'], 'icon')) ? $this->pm->get_data($pdata['plugin'], 'icon') : $this->root_path.'images/admin/plugin.png') : $this->root_path.'images/global/info.png';
			$data['desc'] = $this->html->ToolTip($data['desc'], '<img src="'.$icon.'" alt="p" />');
			$data['multiple'] = ($portal_module[$path]->get_multiple() && !$pdata['child']) ? true : false;
			if ($portal_module[$path]->get_multiple()){
				$portal_module[$path]->set_id($id);
				$portal_module[$path]->output();
				$data['header'] = ' ('.$portal_module[$path]->get_header().')';
			} else {
				$data['header'] = '';
			}
			$data['child'] = $pdata['child'];
			if($pdata['enabled']) {
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
			$this->tpl->assign_block_vars($data['tpl_posi'].'_row', array(
				'NAME'			=> $data['name'].$data['header'],
				'CLASS'			=> $data['class'],
				'ID'			=> $id,
				'POS'			=> $data['tpl_posi'],
				'INFO'			=> $data['desc'],
				'S_MULTIPLE'	=> $data['multiple'],
				'S_CHILD'		=> $data['child'],
			));
		}
		$this->portal->init_portalsettings();
		$this->confirm_delete($this->user->lang('portal_delete_warn'), 'manage_portal.php'.$this->SID.'&del=true', true, array('function' => 'delete_portal'));

		$filter_rights = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		$filter_rights[0] = $this->user->lang('portalplugin_filter3_all');
		ksort($filter_rights);

		$this->tpl->assign_var('PERM_FILTER', $this->html->DropDown('fvisibility', $filter_rights , $this->in->get('fvisibility', 0),'','onchange="javascript:form.submit();"'));
		$this->jquery->Dialog('portalsettings', $this->user->lang('portalplugin_winname'), array('url'=>$this->root_path."admin/manage_portal.php".$this->SID."&simple_head=true&reload=1&id='+moduleid+'", 'width'=>'660', 'height'=>'400', 'withid'=>'moduleid', 'onclose' => 'manage_portal.php'.$this->SID));
		$this->tpl->add_css(".portal_disabled { float:left; margin-left: 4px; width:230px; min-height: 16px;}");
		$this->tpl->add_js('$(".equalHeights").equalHeights();', 'docready');
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('portalplugin_management'),
			'template_file'		=> 'admin/manage_portal.html',
			'display'			=> true)
		);
	}

	private function add_js() {
		$this->tpl->add_js(
'				$("#left1, #left2, #middle, #right, #bottom, #disabled").sortable({
					connectWith: \'.connectedSortable\',
					cancel: \'.not-sortable, tbody .not-sortable,.not-sortable tbody, .th_add, .td_add\',
					cursor: \'pointer\',
				 	start: function(event, ui){
						var classI = $(ui.item).attr("class");
						classI = classI.toString();

						if (classI.indexOf("Pleft1") == -1){
							$("#left1").addClass("red");
						} else {
							$("#left1").addClass("green");
						};
						if (classI.indexOf("Pleft2") == -1){
							$("#left2").addClass("red");
						} else {
							$("#left2").addClass("green");
						};
						if (classI.indexOf("Pmiddle") == -1){
							$("#middle").addClass("red");
						} else {
							$("#middle").addClass("green");
						};
						if (classI.indexOf("Pright") == -1){
							$("#right").addClass("red");
						} else {
							$("#right").addClass("green");
						};
						if (classI.indexOf("Pbottom") == -1){
							$("#bottom").addClass("red");
						} else {
							$("#bottom").addClass("green");
						};
						$("#disabled").addClass("green");
					},
					stop: function(event, ui){
						$("#left1, #left2, #middle, #right, #bottom").removeClass("red");
						$("#left1, #left2, #middle, #right, #bottom, #disabled").removeClass("green");
					},

					receive: function(event, ui){
						var classI = $(ui.item).attr("class").toString();
						var pos = $(ui.item).parents().attr("id");
						if (pos != "disabled" && classI.indexOf("P"+pos) == -1){$(ui.sender).sortable(\'cancel\');return;};
						if (pos == "disabled"){$(ui.item).addClass("portal_disabled");}else{$(ui.item).removeClass("portal_disabled");};

						var id = $(ui.item).attr("id");
						$("#block_"+id).val(pos);
					}
				}).disableSelection();', 'docready');
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Portal', Manage_Portal::__shortcuts());
registry::register('Manage_Portal');
?>