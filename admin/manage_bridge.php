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

class Manage_Bridge extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'db', 'bridge', 'html',
			'crypt'	=> 'encrypt',
		);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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
		$arrPrefix = $this->get_prefix($this->in->get('notsamedb', 0));
		$arrPrefix = array_merge(array('' => ''), $arrPrefix );
		$arrKeys = array_keys($arrPrefix);
		$dropdown = $this->html->DropDown('db_prefix', $arrPrefix, $arrKeys[1], '', 'onchange="onchange_prefix()"');
		$this->config->set('cmsbridge_notsamedb', $this->in->get('notsamedb', 0));

		echo $dropdown;
		exit;

	}

	public function get_prefix($notsamedb = false){
		//Same Database
		if (!$notsamedb){
			$alltables = $this->db->get_tables();
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
		if ($this->in->get('host') != '' && $this->in->get('user') != '' && $this->in->get('pw') != '' && $this->in->get('name') != ''){
			$error = array();
			$this->db = dbal::factory(array('dbtype' => 'mysql', 'die_gracefully' => true));
			$result = $this->db->open($this->in->get('host'),$this->in->get('name'),$this->in->get('user'),$this->in->get('pw'));
			if ($result){
				//Schreibe die Daten in die Config
				$this->config->set('cmsbridge_host', $this->crypt->encrypt($this->in->get('host')));
				$this->config->set('cmsbridge_user', $this->crypt->encrypt($this->in->get('user')));
				$this->config->set('cmsbridge_password', $this->crypt->encrypt($this->in->get('pw', '', 'raw')));
				$this->config->set('cmsbridge_database', $this->crypt->encrypt($this->in->get('name')));
				$this->config->set('cmsbridge_notsamedb', 1);
				echo "true";
				die();
			} else {
				$this->config->del('cmsbridge_host');
				$this->config->del('cmsbridge_user');
				$this->config->del('cmsbridge_password');
				$this->config->del('cmsbridge_database');
				$this->config->del('cmsbridge_notsamedb');
			}

		}

		echo "false";
		exit;
	}

	public function ajax_check_usertable(){
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
		$groups = $this->bridge->get_user_groups(true);
		$out = '';
		foreach ($groups as $key=>$value){
			$out .= '<option value="'.$key.'">'.$value.'</option>';
		}

		echo $out;
		exit;
	}

	public function ajax_check_userlogin(){
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

			//Forum Integration
			$this->config->set('cmsbridge_url', $this->in->get('cms_url'));
			$this->config->set('cmsbridge_embedded', $this->in->get('cms_embedded'));
			if ($this->in->get('cms_show_link', 0) && strlen($this->in->get('cms_url'))){
				$this->config->set('cmsbridge_showlink', 1);
			} else {
				$this->config->set('cmsbridge_showlink', 0);
			}

			//Registration
			$this->config->set('cmsbridge_reg_url', $this->in->get('cms_reg_url'));
			$this->config->set('cmsbridge_reg_embedded', $this->in->get('cms_reg_embedded'));
			if ($this->in->get('cms_reg_redirect', 0) && strlen($this->in->get('cms_reg_url'))){
				$this->config->set('cmsbridge_reg_redirect', 1);
			} else {
				$this->config->set('cmsbridge_reg_redirect', 0);
			}
			
			$this->config->set('cmsbridge_onlycmsuserlogin', $this->in->get('cms_onlycmsuserlogin', 0));

			//Specific Bridge-Settings
			$settings = $this->bridge->get_settings();
			if (is_array($settings)){
				$save_array = array();
				foreach($settings as $name=>$confvars){
					$savetype = ($confvars['default']) ? $confvars['default'] : '';
					$tmp_get = (isset($confvars['serialized'])) ? serialize($this->in->getArray($name, $confvars['datatype'])) : $this->in->get($name, $savetype);
					$tmp_get = (isset($confvars['edecode'])) ? html_entity_decode($tmp_get) : $tmp_get;
					$save_array[$name] = $tmp_get;
				}
				$this->config->set($save_array);
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

			$this->config->del('cmsbridge_showlink');
			$this->config->del('cmsbridge_url');
			$this->config->del('cmsbridge_embedded');

			$this->config->del('cmsbridge_reg_redirect');
			$this->config->del('cmsbridge_reg_url');
			$this->config->del('cmsbridge_reg_embedded');
	}

	public function deactivate_bridge(){
		$this->bridge->deactivate_bridge();
	}


	// ---------------------------------------------------------
	// Display
	// ---------------------------------------------------------
	public function display() {
		if ($this->config->get('cmsbridge_active') == 1 && !$this->bridge->db){
			$this->bridge->deactivate_bridge();
			$this->core->message($this->user->lang('bridge_disabled_message'), $this->user->lang('error'), 'red');
		}
	
		if ($this->config->get('cmsbridge_active') != 1){
			$this->delete_settings();
		}

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
		);

		$arrSelectedGroups = ($this->config->get('cmsbridge_active') == 1) ? $this->bridge->get_user_groups(true) : array();

		//Bridge Settings
		$settings = $this->bridge->get_settings();
		if (is_array($settings)){
			foreach($settings as $name=>$confvars){
				// continue if hmode == true
				if(($this->HMODE && $confvars['not4hmode']) || (isset($confvars['disabled']) && $confvars['disabled']===true)){
					continue;
				}
				$no_lang = (isset($confvars['no_lang'])) ? true : false;

				$this->tpl->assign_block_vars('field', array(
					'NAME'		=> ($this->user->lang($confvars['name'])) ? $this->user->lang($confvars['name']) : $confvars['name'],
					'HELP'		=> ($this->user->lang($confvars['name'].'_help')) ? $this->user->lang($confvars['name'].'_help') : $confvars['help'],
					'FIELD'		=> $this->html->generateField($confvars, $name, $this->config->get($name), $no_lang),
				));
			}
		}
		$arrBridges = $this->bridge->get_available_bridges();
		ksort($arrBridges);

		$this->tpl->assign_vars(array(
			'MS_USERGROUPS'		=> $this->jquery->MultiSelect('usergroups', $arrSelectedGroups, explode(',', $this->config->get('cmsbridge_groups')), array('height' => 150, 'width' => 300)),
			'S_BRIDGE_ACTIVE'	=> ($this->config->get('cmsbridge_active') == 1) ? true : false,
			'S_BRIDGE_SETTINGS'	=> (is_array($settings) && count($settings) > 0) ? true : false,
			'DD_SYSTEMS'		=> $this->html->DropDown('cms_type',$arrBridges, $this->config->get('cmsbridge_type'), '', 'onchange="onchange_type()"'),
			'S_SAMEDB'			=> ($this->config->get('cmsbridge_notsamedb') == '0' && $this->config->get('cmsbridge_active') == 1) ? true : false,
			'S_NOTSAMEDB'		=> ($this->config->get('cmsbridge_notsamedb') == '1' && $this->config->get('cmsbridge_active') == 1) ? true : false,

			'DB_HOST'			=> ($this->crypt->decrypt($this->config->get('cmsbridge_host')) == '') ? $this->dbhost : $this->crypt->decrypt($this->config->get('cmsbridge_host')),
			'DB_USER'			=> ($this->crypt->decrypt($this->config->get('cmsbridge_user')) == '') ? $this->dbuser : $this->crypt->decrypt($this->config->get('cmsbridge_user')),
			'DB_PW'				=> ($this->crypt->decrypt($this->config->get('cmsbridge_password'))  == '') ? '' : $this->crypt->decrypt($this->config->get('cmsbridge_password')),
			'DB_DATABASE'		=> $this->crypt->decrypt($this->config->get('cmsbridge_database')),
			'DD_PREFIX'			=> $this->html->DropDown('db_prefix', $arrPrefix, $this->config->get('cmsbridge_prefix'), '', 'onchange="onchange_prefix()"'),
			'OWN_PREFIX'		=> (!in_array($this->config->get('cmsbridge_prefix'), $arrPrefix)) ? $this->config->get('cmsbridge_prefix') : '',
			'S_ACTIVATE_MESSAGE'=> ($this->in->get('activate') == 'true') ? true : false,
			'DD_EMBEDD_OPTIONS'	=> $this->html->DropDown('cms_embedded', $a_linkMode , $this->config->get('cmsbridge_embedded')),
			'CMS_URL'			=> $this->config->get('cmsbridge_url'),
			'S_CMS_LINK'		=> ($this->config->get('cmsbridge_showlink')) ? true : false,
			'S_CMS_REG_REDIRECT'=> ($this->config->get('cmsbridge_reg_redirect')) ? true : false,
			'CMS_REG_URL'		=> $this->config->get('cmsbridge_reg_url'),
			'DD_REG_EMBEDDED_OPTIONS' => $this->html->DropDown('cms_reg_embedded', $a_linkMode , $this->config->get('cmsbridge_reg_embedded')),
			'S_ONLYCMSUSERLOGIN'=> ((int)$this->config->get('cmsbridge_onlycmsuserlogin')) ? true : false,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('manage_bridge'),
			'template_file'		=> 'admin/manage_bridge.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Bridge', Manage_Bridge::__shortcuts());
registry::register('Manage_Bridge');
?>