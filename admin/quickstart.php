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
include_once($eqdkp_root_path.'common.php');

class QuickStartWizard extends page_generic {

	public static $shortcuts = array(
			'repo'		=> 'repository',
			'objStyles'	=> 'styles',
			'encrypt'	=> 'encrypt',
			'form'	=> array('form', array('core_settings'))
	);

	public function __construct(){
		$this->user->check_auth('a_maintenance');

		$handler = array(

		);
		parent::__construct(false, $handler,  false, null);
		$this->process();
	}


	//Step1 - select game
	public function step0(){
		$arrGames = array();

		$arrExtensionList = $this->repo->getExtensionList();
		if (is_array($arrExtensionList)){
			foreach($arrExtensionList[7] as $intID => $val){
				$arrGames[$val['plugin_id']] =  $val['name'];
				$arrJsonGames[$val['plugin_id']] = $val['plugin'];
			}
		}

		natsort($arrGames);

		$this->tpl->assign_vars(array(
				'DD_GAMES' => (new hdropdown('games', array('options' => $arrGames)))->output(),
				'JSON_GAMES' => json_encode($arrJsonGames),
		));
	}

	public function process_step0(){
		if(!$this->checkCSRF('process')) return false;

		//Game muss installiert werden
		$intPluginID = $this->in->get('games');

		$arrExtensionList = $this->repo->getExtensionList();
		if (is_array($arrExtensionList)){
			foreach($arrExtensionList[7] as $intID => $val){
				$arrGames[$val['plugin_id']] =  $val['plugin'];
			}
		}

		$strPluginname = $arrGames[$intPluginID];

		$this->game->installGame($strPluginname, $this->user->lang_name, 1);
		$this->pdc->flush();
		$objStyles = register('styles');
		$objStyles->delete_cache(false);
	}

	//Step2 - Gildenname, plus Spiel-Einstellungen, Gildenlogo
	public function step1(){
		$this->form->use_tabs = false;
		$this->form->use_fieldsets = false;

		$arrSettings = array(
				'guildtag'		=> array(
						'type'		=> 'text',
						'size'			=> 35
				),
				'custom_logo'	=> array(
						'type'			=> 'imageuploader',
						'imgpath'		=> $this->pfh->FolderPath('','files'),
						//'noimgfile'		=> "templates/".$this->user->style['template_path']."/images/logo.png",
						'returnFormat'	=> 'in_data',
						//'deletelink'	=> $this->root_path.'admin/manage_settings.php'.$this->SID.'&dellogo=true',
				),
		);

		$this->form->lang_prefix = 'core_sett_';

		$this->form->add_fields($arrSettings);

		// merge the game admin array to the existing one
		$settingsdata_admin = $this->game->admin_settings();
		if(is_array($settingsdata_admin) && !empty($settingsdata_admin)){
			$this->form->add_fields($settingsdata_admin);
		}

		// Importer API Key Wizzard
		$apikey_config		= $this->game->get_importers('apikey');
		if(($this->game->get_importAuth('a_members_man', 'guild_import') || $this->game->get_importAuth('a_members_man', 'char_mupdate')) && $apikey_config){
			if($apikey_config['status'] == 'required' || $apikey_config['status'] == 'optional'){
				if(isset($apikey_config['steps']) && is_array($apikey_config['steps']) && count($apikey_config['steps']) > 0){
					$appisetts	= array();
					foreach($apikey_config['steps'] as $title=>$val){
						$appisetts[$this->game->glang($title)]	= $this->game->glang($val);
					}

					// now, let us add the API-Key-Field to the last element of the array
					$apikeyform = '';
					$apikey_set	= false;
					foreach($apikey_config['form'] as $fieldname=>$fieldcontent){
						if($this->config->get($fieldname) != '') { $apikey_set = true; }
						$value = ($this->in->exists($fieldname)) ? $this->in->get($fieldname) : $this->config->get($fieldname);
						$apikeyform	.= '<br/>'.$this->game->glang($fieldname).': '.$this->form->field($fieldname, array_merge($fieldcontent, array('value'=>$value)));
					}

					end($appisetts);
					$key				= key($appisetts);
					reset($appisetts);
					$appisetts[$key]	= str_replace('{APIKEY_FORM}', $apikeyform, $appisetts[$key]);

					$this->form->add_field('settings_apikey', array(
							'type'		=> 'accordion',
							'options'	=> $appisetts,
							'active'	=> (($apikey_set) ? (count($appisetts)-1) : 0),
					));
				}
			}
		}

		$this->form->output();
	}

	public function process_step1(){
		if(!$this->checkCSRF('process')) return false;

		$this->form->use_tabs = false;
		$this->form->use_fieldsets = false;

		$arrSettings = array(
				'guildtag'		=> array(
						'type'		=> 'text',
						'size'			=> 35
				),
				'custom_logo'	=> array(
						'type'			=> 'imageuploader',
						'imgpath'		=> $this->pfh->FolderPath('','files'),
						//'noimgfile'		=> "templates/".$this->user->style['template_path']."/images/logo.png",
						'returnFormat'	=> 'in_data',
						//'deletelink'	=> $this->root_path.'admin/manage_settings.php'.$this->SID.'&dellogo=true',
				),
		);

		$this->form->lang_prefix = 'core_sett_';

		$this->form->add_fields($arrSettings);

		// merge the game admin array to the existing one
		$settingsdata_admin = $this->game->admin_settings();
		if(is_array($settingsdata_admin) && !empty($settingsdata_admin)){
			$this->form->add_fields($settingsdata_admin);
		}

		$arrValues = $this->form->return_values();

		$arrValues['game_importer_apikey'] = $this->in->get('game_importer_apikey');
		$arrValues['game_importer_clientid'] = $this->in->get('game_importer_clientid');
		$arrValues['game_importer_clientsecret'] = $this->in->get('game_importer_clientsecret');

		$this->config->set($arrValues);

		$this->config->set('main_title', $arrValues['guildtag']);

		$this->pdh->process_hook_queue();
	}

	//Step3 - Gildenimport falls vorhanden
	public function step2(){
		//siehe SEttings
		$this->form->reset_fields();

		$blnHasImport = false;

		if($this->game->get_importAuth('a_members_man', 'char_mupdate')){
			$this->jquery->Dialog('MassUpdateChars', $this->user->lang('uc_import_adm_update'), array('url'=>$this->game->get_importers('char_mupdate', true), 'width'=>'600', 'height'=>'450'));
			$blnHasImport = true;
		}
		if($this->game->get_importAuth('a_members_man', 'guild_import')){
			$this->jquery->Dialog('GuildImport', $this->user->lang('uc_import_guild_wh'), array('url'=>$this->game->get_importers('guild_import', true), 'width'=>'600', 'height'=>'450'));
			$blnHasImport = true;
		}

		if(!$blnHasImport) return false;

		// The importer settings
		if($this->game->get_importAuth('a_members_man', 'guild_import')){
			if(($this->game->get_importers('guild_imp_rsn') && $this->config->get('servername') == '') || $this->game->get_apikeyfield_requiered_and_empty()){
				$gimport_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_import').'" disabled="disabled" />';
			}else{
				$gimport_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_import').'" class="mainoption" onclick="javascript:GuildImport()" />';
			}
			$this->form->add_field('uc_import_guild', array(
					'lang'	=> 'uc_import_guild',
					'type'	=> 'direct',
					'text'	=> $gimport_out,
			));
		}

		if($this->game->get_importAuth('a_members_man', 'char_mupdate')){
			if(($this->game->get_importers('guild_imp_rsn') && $this->config->get('servername') == '')  || $this->game->get_apikeyfield_requiered_and_empty()){
				$cupdate_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_update').'" disabled="disabled" />';
			}else{
				$cupdate_out = '<input type="button" name="add" value="'.$this->user->lang('uc_bttn_update').'" class="mainoption" onclick="javascript:MassUpdateChars()" />';
			}

			$this->form->add_field('uc_update_all', array(
					'lang'	=> 'uc_update_all',
					'type'	=> 'direct',
					'text'	=> $cupdate_out,
			));
		}

		$this->form->output();
	}

	//Step4 - Select Style
	public function step3(){
		//Auflisten und Nachinstallieren, aber erst im letzten Step dann als default setzen

		$arrStyles = array();

		//First, the local styles
		$style_array = array();
		foreach(register('pdh')->get('styles', 'styles', array(0, false)) as $styleid=>$row){
			$screenshot = '';
			if (file_exists($this->root_path.'templates/'.$row['template_path'].'/screenshot.png' )){
				$screenshot = $this->root_path.'templates/'.$row['template_path'].'/screenshot.png';
			} elseif(file_exists($this->root_path.'templates/'.$row['template_path'].'/screenshot.jpg' )){
				$screenshot = $this->root_path.'templates/'.$row['template_path'].'/screenshot.jpg';
			}

			$this->tpl->assign_block_vars('style_row', array(
					'PLUGINID'	=> 0,
					'STYLE_ID'	=> $row['style_id'],
					'NAME'		=> $row['style_name'],
					'SCREENSHOT' => $screenshot,
					'IS_LOCAL'	=> true,
					'IS_CHECKED' => ($row['template_path'] == 'eqdkp_clean') ? ' checked="checked"' : '',
			));

			$style_array[$styleid] = $row['style_name'];
		}



		//Now the ones from the Extension List
		$arrExtensionList = $this->repo->getExtensionList();
		if (is_array($arrExtensionList)){
			foreach($arrExtensionList[2] as $intID => $val){

				$this->tpl->assign_block_vars('style_row', array(
						'PLUGINID'	=> $val['plugin_id'],
						'PLUGINPATH'=> $val['plugin'],
						'NAME'		=> $val['name'],
						'SCREENSHOT' => 'http://cdn1.eqdkp-plus.eu/repository/screenshot.php?extid='.$val['plugin_id'],
						'IS_REPO'	=> true,
				));

			}
		}
	}

	public function process_step3(){
		if(!$this->checkCSRF('process')) return false;

		if($this->in->get('local', 0)){
			$intStyleID = $this->in->get('local', 0);

			$this->config->set('styleid', $intStyleID, 'wizard');

			$this->config->set('default_style', $intStyleID);
			$this->pdh->put('user', 'update_userstyle', array($intStyleID));

		}elseif($this->in->get('extid', 0)){
			$intPluginCode = $this->in->get('extid', 0);
			$strPluginName = $this->in->get('code');

			$objStyles = register('styles');

			$intStyleID = $this->objStyles->install($strPluginName);

			$this->config->set('styleid', $intStyleID, 'wizard');
		}
		$this->pdh->process_hook_queue();
	}

	//Step5 - Select Plugins
	public function step4(){
		//Auflisten und Installieren, per checkbox.

		//Now the ones from the Extension List
		$arrExtensionList = $this->repo->getExtensionList();
		if (is_array($arrExtensionList)){
			$arrNames = array();
			foreach($arrExtensionList[1] as $intID => $val){
				$arrNames[$intID] = $val['name'];
			}

			natsort($arrNames);

			foreach($arrNames as $intID => $v){
				$val = $arrExtensionList[1][$intID];
				if($val['plugin'] == 'pluskernel') continue;

				$this->tpl->assign_block_vars('style_row', array(
						'PLUGINID'	=> $val['plugin_id'],
						'NAME'		=> sanitize($val['name']),
						'DESC'		=> sanitize($val['description']),
						'PLUGINPATH'=> sanitize($val['plugin']),
						'SCREENSHOT' => 'http://cdn1.eqdkp-plus.eu/repository/screenshot.php?extid='.$val['plugin_id'],
						'IS_REPO'	=> true,
				));

			}
		}
	}

	//Install Plugin
	public function process_step4(){
		if(!$this->checkCSRF('process')) return false;

		$code = $this->in->get('code');

		$this->pm->install($code);

		$this->pdh->process_hook_queue();
		exit; //because it is ajax
	}

	//Step6 - Select Portal Moduls, install, permission all, put to right and left
	public function step5(){
		//Auflisten und Installieren, per checkbox.

		//Now the ones from the Extension List
		$arrExtensionList = $this->repo->getExtensionList();
		if (is_array($arrExtensionList)){
			$arrNames = array();
			foreach($arrExtensionList[3] as $intID => $val){
				$arrNames[$intID] = $val['name'];
			}

			natsort($arrNames);

			foreach($arrNames as $intID => $v){
				$val = $arrExtensionList[3][$intID];

				$this->tpl->assign_block_vars('style_row', array(
						'PLUGINID'	=> $val['plugin_id'],
						'NAME'		=> sanitize($val['name']),
						'DESC'		=> sanitize($val['description']),
						'PLUGINPATH'=> sanitize($val['plugin']),
						'SCREENSHOT' => 'http://cdn1.eqdkp-plus.eu/repository/screenshot.php?extid='.$val['plugin_id'],
						'IS_REPO'	=> true,
				));

			}
		}
	}

	public function process_step5(){
		if(!$this->checkCSRF('process')) return false;

		$intCurrent = $this->in->get('current');
		$path = $this->in->get('code');

		$this->portal->get_all_modules();

		$idList = $this->pdh->get('portal', 'id_by_path', array($path));
		$plugin = $this->pdh->get('portal', 'plugin', array($idList[0]));
		$name = $this->pdh->get('portal', 'name', array($idList[0]));

		$this->portal->uninstall($path, $plugin);
		$intID = $this->portal->install($path, $plugin);

		//Set Permissions to all
		$this->config->set('visibility', 'a:1:{i:0;s:1:"0";}', 'pmod_'.$intID);

		//Set Position
		$arrBlockModules = $this->pdh->get('portal_layouts', 'modules', array(1));
		$strBlock = (($intCurrent % 2) == 0) ? 'right' : 'left';
		$arrBlockModules[$strBlock][] = $intID;

		$blnResult = $this->pdh->put('portal_layouts', 'update', array(1, 'Standard', array('left', 'middle', 'bottom', 'right'), $arrBlockModules, array()));

		$this->pdh->process_hook_queue();
		exit; //because it is ajax
	}

	public function step6(){
		//DKP System

		$this->pdh->auto_update_layout($current_layout);
		$intLayouts = 0;
		foreach($this->pdh->get_layout_list(true, false) as $layout){
			$intLayouts++;
			$this->tpl->assign_block_vars('style_row', array(
					'NAME'     => $layout,
					'DESC'    => $this->pdh->get_eqdkp_layout_description($layout),
					'IS_CURRENT' => ($layout == 'normal') ? 'checked="checked"' : '',
			));
		}
	}

	public function process_step6(){
		if(!$this->checkCSRF('process')) return false;

		$strPointLayout = $this->in->get("pointlayout");
		$this->config->set('eqdkp_layout', $strPointLayout);

		if($strPointLayout == 'nopoints'){
			$this->config->set('enable_points', 0);
		} else {
			$this->config->set('enable_points', 1);
		}
		$this->pdc->flush();
	}


	public function step7(){
		//finish

		//set default style
		$intStyleID = $this->config->get('styleid', 'wizard');

		$this->config->set('default_style', $intStyleID);
		$this->pdh->put('user', 'update_userstyle', array($intStyleID));

	}

	public function display(){
		$show = (int)$this->in->get('show', 0);
		$process = (int)$this->in->exists('process', 0);

		if($process){
			$function = 'process_step'.$show;
			if(method_exists($this, $function)){
				$this->$function();
			}
		}

		if($this->in->exists('show')) $show++ ;

		$function = 'step'.$show;
		$blnResult = $this->$function();
		//Go to the next step
		if($blnResult === false){
			$show++ ;
			$function = 'step'.$show;
			$blnResult = $this->$function();
		}

		$this->tpl->assign_vars(array(
				'S_SHOW_'.strtoupper($show)	=> true,
				'STEP' 				=> $show,
				'LINK_HASH' 		=> $this->CSRFGetToken('process'),
				'WIZARD_HEADLINE'	=> $this->user->lang('wizard_step'.$show),
				'WIZARD_INFOBOX'	=> $this->user->lang('wizard_step'.$show.'_info'),
		));


		$this->core->set_vars([
				'page_title'		=> $this->user->lang('wizard'),
				'template_file'		=> 'admin/quickstart.html',
				'page_path'			=> [
						['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
						['title'=>$this->user->lang('wizard'), 'url'=>' '],
				],
				'display'			=> true
		]);
	}
}
registry::register('QuickStartWizard');
