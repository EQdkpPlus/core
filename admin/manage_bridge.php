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

class Manage_Bridge extends page_generic {
	public static $shortcuts = array('crypt' => 'encrypt');

	public function __construct(){
		$this->user->check_auth('a_config_man');
		$handler = array(
			'ajax' => array(
				array('process' => 'ajax_load_prefix', 'value' => 'prefix'),
				array('process' => 'ajax_check_database', 'value' => 'checkdb'),
				array('process' => 'ajax_check_usertable', 'value' => 'usertable'),
				array('process' => 'ajax_load_usergroups', 'value' => 'usergroups'),
				array('process' => 'ajax_check_userlogin', 'value' => 'checkuser'),
				array('process' => 'ajax_disable_bridge', 'value' => 'disable'),
				),
			'edit'	=> array('process' => 'edit', 'csrf'=>true),
		);
		parent::__construct(false, $handler, array('user', 'name'), null, 'user_id[]');

		$this->process();
	}

	public function ajax_disable_bridge(){
		if ($this->in->get('disable', 0)){
			$this->deactivate_bridge();
		}
		exit;

	}

	public function ajax_load_prefix(){
		header('content-type: text/html; charset=UTF-8');
		
		$arrPrefix = $this->get_prefix($this->in->get('notsamedb', 0));
		$arrPrefix = array_merge(array('' => ''), $arrPrefix );
		$arrKeys = array_keys($arrPrefix);
		$dropdown = new hdropdown('db_prefix', array('options' => $arrPrefix, 'value' => $arrKeys[1], 'js' => 'onchange="onchange_prefix()"'));
		$this->config->set('cmsbridge_notsamedb', $this->in->get('notsamedb', 0));

		echo $dropdown;
		exit;

	}

	public function get_prefix($notsamedb = false){
		//Same Database
		if (!$notsamedb){
			$alltables = $this->db->listTables();
			$tables		= array();
			foreach ($alltables as $name){
				if (strpos($name, '_') !== false){
					$prefix = substr($name, 0, strpos($name, '_')+1);
					$tables[$prefix] = $prefix;
				} elseif  (strpos($name, '.') !== false){
					$prefix = substr($name, 0, strpos($name, '.')+1);
					$tables[$prefix] = $prefix;
				}
			}

		} else {
			$tables = $this->bridge->get_prefix();
		}

		return $tables;
	}

	public function ajax_check_database(){
		header('content-type: text/html; charset=UTF-8');
		
		if ($this->in->get('host') != '' && $this->in->get('user') != '' && $this->in->get('pw') != '' && $this->in->get('name') != ''){
			$error = array();
			try {
				$db = dbal::factory(array('dbtype' => registry::get_const('dbtype')));
				$db->connect($this->in->get('host'),$this->in->get('name'),$this->in->get('user'),$this->in->get('pw'));
				//Schreibe die Daten in die Config
				$this->config->set('cmsbridge_host', $this->crypt->encrypt($this->in->get('host')));
				$this->config->set('cmsbridge_user', $this->crypt->encrypt($this->in->get('user')));
				$this->config->set('cmsbridge_password', $this->crypt->encrypt($this->in->get('pw', '', 'raw')));
				$this->config->set('cmsbridge_database', $this->crypt->encrypt($this->in->get('name')));
				$this->config->set('cmsbridge_notsamedb', 1);
				echo "true";
				exit;
				
			} catch(DBALException $e){
				$this->config->del('cmsbridge_host');
				$this->config->del('cmsbridge_user');
				$this->config->del('cmsbridge_password');
				$this->config->del('cmsbridge_database');
				$this->config->del('cmsbridge_notsamedb');
			}
		}
		sleep(1);
		echo "false";
		exit;
	}

	public function ajax_check_usertable(){
		sleep(1);
		header('content-type: text/html; charset=UTF-8');
		$prefix = $this->in->get('prefix');
		$this->config->set('cmsbridge_prefix', $prefix);

		$type = $this->in->get('type');
		$this->config->set('cmsbridge_type', $type);

		if ($this->bridge->check_user_group_table()){
			echo "true";
		} else {
			echo "false";
		}

		exit;
	}

	public function ajax_load_usergroups(){
		header('content-type: text/html; charset=UTF-8');
		$groups = $this->bridge->get_user_groups(true);
		$out = '';
		foreach ($groups as $key=>$value){
			$out .= '<option value="'.$key.'">'.$value.'</option>';
		}

		echo $out;
		exit;
	}

	public function ajax_check_userlogin(){
		header('content-type: text/html; charset=UTF-8');
		
		$groups = $this->in->get('groups', '');
		$groups = str_replace('|', ',', $groups);
		if ($groups != ''){
			$this->config->set('cmsbridge_groups', $groups);
			$this->config->set('cmsbridge_active', 1);

			if ($this->in->get('user') != '' && $this->in->get('pw') != "" && $this->bridge->login($this->in->get('user'), $this->in->get('pw'), false, false, false)){
				echo "true";
			} else {
				echo "false";
			}
		}	else {
			echo "false";
		}


		exit;
	}

	public function edit(){
		if (is_array($this->in->getArray('usergroups', 'int')) && count($this->in->getArray('usergroups', 'int')) > 0){
			$groups = implode(',', $this->in->getArray('usergroups', 'int'));
			$this->config->set('cmsbridge_groups', $groups);
			
			//Sync Usergroups
			$groups = implode(',', $this->in->getArray('sync_usergroups', 'int'));
			$this->config->set('cmsbridge_sync_groups', $groups);

			//Forum Integration
			$this->config->set('cmsbridge_url', $this->in->get('cms_url'));
			$this->config->set('cmsbridge_embedded', $this->in->get('cms_embedded'));

			//Registration
			$this->config->set('cmsbridge_reg_url', $this->in->get('cms_reg_url'));
			//Passwort Reset Page
			$this->config->set('cmsbridge_pwreset_url', $this->in->get('cms_pwreset_url'));
			
			$this->config->set('cmsbridge_onlycmsuserlogin', $this->in->get('cms_onlycmsuserlogin', 0));

			//Bridge Settings
			$settings = $this->bridge->get_settings();
			$form = register('form', array('bridge_settings'));
			
			if (is_array($settings)){
				$form->add_fields($settings);
				$arrValues = $form->return_values();
				$this->config->set($arrValues);
			}

		} else {
			$this->deactivate_bridge();
		}
	}

	public function delete_settings(){
			$this->config->del('cmsbridge_host');
			$this->config->del('cmsbridge_user');
			$this->config->del('cmsbridge_password');
			$this->config->del('cmsbridge_database');
			$this->config->del('cmsbridge_notsamedb');

			$this->config->del('cmsbridge_prefix');
			$this->config->del('cmsbridge_type');
			$this->config->del('cmsbridge_groups');

			$this->config->del('cmsbridge_url');
			$this->config->del('cmsbridge_reg_url');
			$this->config->del('cmsbridge_pwreset_url');
			$this->config->del('cmsbridge_embedded');
	}

	public function deactivate_bridge(){
		$this->bridge->deactivate_bridge();
	}


	// ---------------------------------------------------------
	// Display
	// ---------------------------------------------------------
	public function display() {
		if ($this->config->get('cmsbridge_active') == 1 && !$this->bridge->status){
			$this->bridge->deactivate_bridge();
			$this->core->message($this->user->lang('bridge_disabled_message'), $this->user->lang('error'), 'red');
		}
	
		if ($this->config->get('cmsbridge_active') != 1){
			$this->delete_settings();
		}

		registry::load("form");
		
		$arrPrefix = $this->get_prefix($this->config->get('cmsbridge_notsamedb'));
		$arrPrefix = array_merge(array('' => ''), $arrPrefix );
		$arrKeys = array_keys($arrPrefix);

		if ($this->in->get('edit') == 'true'){
			$this->core->message($this->user->lang('pk_succ_saved'), $this->user->lang('pk_save_title'), 'green');
		}

		$a_linkMode= array(
			'0'				=> $this->user->lang('pk_set_link_type_self'),
			'1'				=> $this->user->lang('pk_set_link_type_link'),
			'2'				=> $this->user->lang('pk_set_link_type_iframe'),
			'4'				=> $this->user->lang('pk_set_link_type_D_iframe_womenues'),
			'5'				=> $this->user->lang('pk_set_link_type_D_iframe_woblocks'),
		);

		$arrSelectedGroups = ($this->config->get('cmsbridge_active') == 1) ? $this->bridge->get_user_groups(true) : array();

		//Bridge Settings
		$settings = $this->bridge->get_settings();
		$form = register('form', array('bridge_settings'));
		
		if (is_array($settings)){
			$form->add_fields($settings);
			
			//Build Settings Array
			foreach($settings as $name=>$confvars){
				$arrValues[$name] = $this->config->get($name);
			}
			$form->output($arrValues);
		}
		$arrBridges = $this->bridge->get_available_bridges();
		ksort($arrBridges);
		
		$arrSyncFields = $this->bridge->get_available_sync_fields();

		$this->tpl->assign_vars(array(
			'MS_USERGROUPS'		=> $this->jquery->MultiSelect('usergroups', $arrSelectedGroups, explode(',', $this->config->get('cmsbridge_groups')), array('height' => 170, 'width' => 300)),
			'S_BRIDGE_ACTIVE'	=> ($this->config->get('cmsbridge_active') == 1) ? true : false,
			'S_PROFILEFIELDS_INFO' => ($this->config->get('cmsbridge_active') == 1 && count($arrSyncFields)) ? true : false,
			'S_BRIDGE_SETTINGS'	=> (is_array($settings) && count($settings) > 0) ? true : false,
			'DD_SYSTEMS'		=> new hdropdown('cms_type', array('options' => $arrBridges, 'value' => $this->config->get('cmsbridge_type'), 'js' => 'onchange="onchange_type()"')),
			'S_SAMEDB'			=> ($this->config->get('cmsbridge_notsamedb') == '0' && $this->config->get('cmsbridge_active') == 1) ? true : false,
			'S_NOTSAMEDB'		=> ($this->config->get('cmsbridge_notsamedb') == '1' && $this->config->get('cmsbridge_active') == 1) ? true : false,

			'DB_HOST'			=> ($this->crypt->decrypt($this->config->get('cmsbridge_host')) == '') ? $this->dbhost : $this->crypt->decrypt($this->config->get('cmsbridge_host')),
			'DB_USER'			=> ($this->crypt->decrypt($this->config->get('cmsbridge_user')) == '') ? $this->dbuser : $this->crypt->decrypt($this->config->get('cmsbridge_user')),
			'DB_PW'				=> ($this->crypt->decrypt($this->config->get('cmsbridge_password'))  == '') ? '' : $this->crypt->decrypt($this->config->get('cmsbridge_password')),
			'DB_DATABASE'		=> $this->crypt->decrypt($this->config->get('cmsbridge_database')),
			'DD_PREFIX'			=> new hdropdown('db_prefix', array('options' => $arrPrefix, 'value' => $this->config->get('cmsbridge_prefix'), 'js' => 'onchange="onchange_prefix()"')),
			'OWN_PREFIX'		=> (!in_array($this->config->get('cmsbridge_prefix'), $arrPrefix)) ? $this->config->get('cmsbridge_prefix') : '',
			'S_ACTIVATE_MESSAGE'=> ($this->in->get('activate') == 'true') ? true : false,
			'DD_EMBEDD_OPTIONS'	=> new hdropdown('cms_embedded', array('options' => $a_linkMode, 'value' => $this->config->get('cmsbridge_embedded'))),
			'CMS_URL'			=> $this->config->get('cmsbridge_url'),
			'CMS_PWRESET_URL'	=> $this->config->get('cmsbridge_pwreset_url'),
			'CMS_REG_URL'		=> $this->config->get('cmsbridge_reg_url'),
			'S_ONLYCMSUSERLOGIN'=> ((int)$this->config->get('cmsbridge_onlycmsuserlogin')) ? true : false,
			'MS_SYNC_USERGROUPS'=> $this->jquery->MultiSelect('sync_usergroups', $arrSelectedGroups, explode(',', $this->config->get('cmsbridge_sync_groups')), array('height' => 170, 'width' => 300)),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_bridge'),
			'template_file'		=> 'admin/manage_bridge.html',
			'display'			=> true)
		);
	}
}
registry::register('Manage_Bridge');
?>