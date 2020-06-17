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
define('NO_MMODE_REDIRECT', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Extensions extends page_generic {
	public static $shortcuts = array(
			'repo'		=> 'repository',
			'objStyles'	=> 'styles',
			'encrypt'	=> 'encrypt'
		);

	private $code = '';

	public function __construct(){
		$this->user->check_auth('a_extensions_man');

		$handler = array(
			'mode' => array('process' => 'mode', 'csrf'=>true),
			'info' => array('process' => 'repo_info'),
			'step' => array(
				array('value' => '1', 'process'	=> 'process_step1'),
				array('value' => '2', 'process'	=> 'process_step2'),
				array('value' => '3', 'process'	=> 'process_step3'),
				array('value' => '4', 'process'	=> 'process_step4'),
			),
			'upload' => array('process' => 'process_upload', 'csrf'=>true),
			'hide_update_warning'	=> array('process' => 'hide_update_warning', 'csrf'=>true),
		);
		parent::__construct(false, $handler);
		$this->code = $this->in->get('code', '');
		$this->code = preg_replace("/[^a-z0-9\.\_\-]/", "", strtolower($this->code));

		if(!$this->pdl->type_known("repository")) $this->pdl->register_type("repository", null, null, array(3,4), true);

		$this->process();
	}

	public function repo_info(){
		$extension = $this->pdh->get('repository', 'row', $this->in->get('info', 0));

		$this->tpl->assign_vars(array(
			'EXTID'				=> sanitize($extension['plugin_id']),
			'CATEGORY'			=> sanitize($extension['category']),
			'CODE'				=> sanitize($extension['plugin']),
			'NAME'				=> sanitize($extension['name']),
			'DATE'				=> $this->time->user_date($extension['date'], true),
			'AUTHOR'			=> sanitize($extension['author']),
			'DESCRIPTION'		=> nl2br(sanitize($extension['description'])),
			'VERSION'			=> sanitize($extension['version']),
			'LEVEL'				=> sanitize($extension['level']),
			'CHANGELOG'			=> nl2br(sanitize($extension['changelog'])),
			'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
			'RATING'			=> $this->jquery->starrating('extension_'.md5($extension['plugin']), $this->env->phpself, array('score' => $extension['rating'], 'readonly' => true)),
		));

		$this->core->set_vars([
			'page_title'		=> 'Repo Info',
			'template_file'		=> 'admin/manage_extensions_repoinfo.html',
			'header_format'		=> 'simple',
			'display'			=> true
		]);
	}

	//Hide Confirm Dialog before starting Update
	public function hide_update_warning(){
		if ((int)$this->in->get('hide', 0) == 0){
			$this->config->del('repo_hideupdatewarning');
		} else {
			$this->config->set('repo_hideupdatewarning', 1);
		}
		exit;
	}

	public function process_upload(){
		$tempname		= $_FILES['extension']['tmp_name'];
		$name			= $_FILES['extension']['name'];
		$filetype		= $_FILES['extension']['type'];
		$upload_id		= randomID();

		$mime_types = array(
				'zip'	=> 'application/zip',
		);

		// get the mine....
		$fileEnding		= pathinfo($name, PATHINFO_EXTENSION);
		$mime = false;
		if(function_exists('finfo_open') && function_exists('finfo_file') && function_exists('finfo_close')){
			$finfo			= finfo_open(FILEINFO_MIME);
			$mime			= finfo_file($finfo, $tempname);
			finfo_close($finfo);
		}elseif(function_exists('mime_content_type')){
			$mime			= mime_content_type( $tempname );
		}else{
			// try to get the extension... not really secure...

			if (array_key_exists($fileEnding, $mime_types)) {
				$mime			= $mime_types[$fileEnding];
			}
		}

		$mime = array_shift(preg_split('/[; ]/', $mime));
		$blnTypeAllowed = false;
		switch ($mime) {
			case 'application/zip':
			case 'application/x-zip': $blnTypeAllowed = true;
		}

		if (!strlen($tempname)){
			$this->core->message($this->user->lang('plugin_upload_error1'), $this->user->lang('error'), 'red');
		} elseif (!$blnTypeAllowed){
			$this->core->message(sprintf($this->user->lang('plugin_upload_error2'), $name, $mime), $this->user->lang('error'), 'red');
		} else {
			//Everything ok, lets unpack it
			$this->pfh->FolderPath('tmp', 'repository');
			$this->pfh->secure_folder('tmp', 'repository');
			$blnResult = $this->repo->unpackPackage($tempname, $this->pfh->FolderPath('tmp/'.$upload_id, 'repository'));
			if ($blnResult){
				$src_path = $extension_name = false;

				if (is_file($this->pfh->FolderPath('tmp/'.$upload_id, 'repository').'package.xml')){
					$xml = simplexml_load_file($this->pfh->FolderPath('tmp/'.$upload_id, 'repository').'package.xml');

					if ($xml && $xml->folder != ''){
						$extension_name = $xml->folder;
						$extension_name = preg_replace("/[^a-z0-9\.\_\-]/", "", strtolower($extension_name));

						//Subfolder detection
						$src_path = $this->pfh->FolderPath('tmp/'.$upload_id, 'repository');
						$arrSubfolder = scandir($src_path);

						$arrIgnore = array(".", "..", "package.xml", "settings.xml", "index.html");
						$arrDiff = array_diff($arrSubfolder, $arrIgnore);
						if(is_array($arrDiff) && count($arrDiff) === 1){
							foreach($arrDiff as $strSubfolder){
								$src_path .= $strSubfolder;
							}
						}

						$arrAttributes = $xml->attributes();

						switch ($arrAttributes['type']){
							case 'plugin':			$target = $this->root_path.'plugins';	$cat=1; 	break;
							case 'game':			$target = $this->root_path.'games';		$cat=7;		break;
							case 'template':		$target = $this->root_path.'templates';	$cat=2;		break;
							case 'portal':			$target = $this->root_path.'portal';	$cat=3;		break;
							case 'language':		$target = $this->root_path;				$cat=11;	break;
							default: $target = false;
						}

						//Delete custom files, because they should not be overwritten during update
						if($cat === 2){
							$this->pfh->Delete($src_path.'/custom.css');
							$this->pfh->Delete($src_path.'/custom.js');
						}

						if($target){

							$blnResult = $this->repo->full_copy($src_path, $target.'/'.$extension_name);

							//Copy package.xml file
							$this->pfh->copy($this->pfh->FolderPath('tmp/'.$upload_id, 'repository').'package.xml', $target.'/'.$extension_name.'/package.xml');

							$this->pfh->FolderPath('tmp/'.$upload_id, 'repository').'package.xml';
							}

						if (!$blnResult){
							$this->core->message($this->user->lang('plugin_package_error3'), $this->user->lang('error'), 'red');
						} else {
							$this->pm->search();
							redirect('admin/manage_extensions.php'.$this->SID.'&cat='.$cat.'&mode=install&code='.$extension_name.'&link_hash='.$this->CSRFGetToken('mode'));
						}

					} else {
						$this->core->message($this->user->lang('plugin_package_error2'), $this->user->lang('error'), 'red');
					}

				} else {
					$this->core->message($this->user->lang('plugin_package_error1'), $this->user->lang('error'), 'red');

				}

			} else {
				$this->core->message($this->user->lang('plugin_upload_error3'), $this->user->lang('error'), 'red');
			}
			$this->pfh->Delete('tmp/'.$upload_id, 'repository');
		}
	}

	//Get Extension Download-Link
	public function process_step1(){
		$intExtensionID = $this->in->get('extid', 0);
		$this->pdl->log('repository', '1. Get Download Link for Ext. '.$intExtensionID);

		//Check for Zip Extension
		if (!class_exists("ZipArchive")){
			echo $this->user->lang('repo_error_zip');
			$this->pdl->log('repository', '1. Error: Zip not available, Ext. '.$intExtensionID);
			return;
		}

		if ($this->in->get('cat', 0) && strlen($this->code)){
			$downloadLink = $this->repo->getExtensionDownloadLink($intExtensionID, $this->in->get('cat', 0), $this->code);
			if($downloadLink && $downloadLink['status'] == 1){
				$this->config->set(md5($this->in->get('cat', 0).$this->code).'_link', $this->encrypt->encrypt($downloadLink['link']), 'repository');
				$this->config->set(md5($this->in->get('cat', 0).$this->code).'_hash', $this->encrypt->encrypt($downloadLink['hash']), 'repository');
				$this->config->set(md5($this->in->get('cat', 0).$this->code).'_signature', $this->encrypt->encrypt($downloadLink['signature']), 'repository');
				echo "true";
			} else {
				echo $this->user->lang('repo_step1_error_'.$downloadLink['error']);
				$this->pdl->log('repository', '1. Error: No download link, Error Code '.$downloadLink['error'].', Ext. '.$this->code);
			}

		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		exit;
	}

	//Download Package
	public function process_step2(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
			@set_time_limit(0);
			@ignore_user_abort(true);
			$this->pdl->log('repository', '2. Download for Ext. '.$this->code);

			$downloadLink 		= $this->encrypt->decrypt($this->config->get(md5($this->in->get('cat', 0).$this->code).'_link', 'repository'));
			$downloadHash 		= $this->encrypt->decrypt($this->config->get(md5($this->in->get('cat', 0).$this->code).'_hash', 'repository'));
			$downloadSignature	= $this->encrypt->decrypt($this->config->get(md5($this->in->get('cat', 0).$this->code).'_signature', 'repository'));
			$filename = 'repo_'.md5($this->in->get('cat', 0).$this->code).'.zip';

			$destFolder = $this->pfh->FolderPath('','repository');
			$this->pfh->secure_folder('','repository');
			$this->repo->downloadPackage($downloadLink, $destFolder, $filename);

			if ($this->repo->verifyPackage($destFolder.$filename, $downloadHash, $downloadSignature, 'packages')){
				echo "true";
			} else {
				echo $this->user->lang('repo_step2_error');
				$this->pdl->log('repository', '2. Verification Error '.$this->code);
			}

		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		exit;
	}

	//Unzip Package
	public function process_step3(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
			$this->pdl->log('repository', '3. Unzip Package '.$this->code);

			@set_time_limit(0);
			@ignore_user_abort(true);

			$destFolder = $this->pfh->FolderPath('tmp/'.md5($this->in->get('cat', 0).$this->code),'repository');
			$srcFolder = $this->pfh->FolderPath('','repository');
			$filename = 'repo_'.md5($this->in->get('cat', 0).$this->code).'.zip';

			if ($this->repo->unpackPackage($srcFolder.$filename, $destFolder)){
				echo "true";
			} else {
				$this->pfh->Delete('', 'repository');
				echo $this->user->lang('repo_step3_error');
				$this->pdl->log('repository', '3. Unzip failed '.$this->code);
			}

		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		exit;
	}

	//Copy files
	public function process_step4(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
			@set_time_limit(0);
			@ignore_user_abort(true);
			$this->pdl->log('repository', '4. Copy files '.$this->code);

			$srcFolder = $origSrcFolder = $this->pfh->FolderPath('tmp/'.md5($this->in->get('cat', 0).$this->code),'repository');

			//Subfolder detection
			$arrSubfolder = scandir($srcFolder);

			$arrIgnore = array(".", "..", "package.xml", "index.html");
			$arrDiff = array_diff($arrSubfolder, $arrIgnore);
			if(is_array($arrDiff) && count($arrDiff) === 1){
				foreach($arrDiff as $strSubfolder){
					$srcFolder .= $strSubfolder;
				}
			}

			switch ((int)$this->in->get('cat', 0)){
				case 1:			$target = $this->root_path.'plugins/'.strtolower($this->code);	break;
				case 2:			$target = $this->root_path.'templates/'.strtolower($this->code);	break;
				case 3:			$target = $this->root_path.'portal/'.strtolower($this->code);	break;
				case 7:			$target = $this->root_path.'games/'.strtolower($this->code);	break;
				case 11:		$target = $this->root_path; break;
			}

			//Delete custom files, because they should not be overwritten during update
			if((int)$this->in->get('cat', 0) === 2){
				$this->pfh->Delete($srcFolder.'/custom.css');
				$this->pfh->Delete($srcFolder.'/custom.js');
			}

			$this->pdl->log('repository', '4. Target for '.$this->code.' is '.$target);
			if($target){
				if((int)$this->in->get('cat', 0) === 11){
					$result = $this->repo->installLanguage($srcFolder);
				} else {
					$result = $this->repo->full_copy($srcFolder, $target);
				}

				if((int)$this->in->get('cat', 0) === 2){
					//Copy package.xml file
					$this->pfh->copy($origSrcFolder.'/package.xml', $target.'/package.xml');
				}

				if ($result){
					echo "true";
				} else {
					echo $this->user->lang('repo_step4_error');
					$this->pdl->log('repository', '4. Fullcopy failed '.$this->code);
				}
			} else {
				echo $this->user->lang('repo_unknown_error');
			}

		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		//clean up
		$this->pfh->Delete('', 'repository');
		$this->config->del('repository');

		//Reset Opcache, for PHP7
		if(function_exists('opcache_reset')){
			opcache_reset();
		}

		exit;
	}

	//Installing and Uninstalling Extensions
	public function mode() {
		$arrMessage = array();
		switch((int)$this->in->get('cat', 0)){
			//Plugins
			case 1:		$modes = array('install', 'enable', 'uninstall', 'delete', 'remove');
						if($this->in->get('mode') == 'update'){
							$this->pm->plugin_update_check();
						} elseif(in_array($this->in->get('mode'), $modes)) {
							$mode = $this->in->get('mode');
							$this->pm->search();
							$result = $this->pm->$mode($this->code);
							if($result) {
								$arrMessage = array(sprintf($this->user->lang('plugin_inst_message'), $this->code, $this->user->lang('plugin_inst_'.$mode)), $this->user->lang('success'), 'green');
							} else {
								$arrMessage = array(sprintf($this->user->lang('plugin_inst_errormsg'), $this->code, $this->user->lang('plugin_inst_'.$mode)), $this->user->lang('error'), 'red');
							}
						}
			break;

			//Templates
			case 2:		$modesWithParam = array('install', 'uninstall', 'enable', 'disable', 'reset', 'export', 'update', 'process_update', 'remove');
						$modes = array('default_style', 'delete_cache');
						if(in_array($this->in->get('mode'), $modesWithParam)){
							$mode = $this->in->get('mode');
							$this->objStyles->$mode($this->code);
							if ($this->in->get('mode') == "update"){
								redirect('admin/manage_extensions.php'.$this->SID);
							}
						} elseif(in_array($this->in->get('mode'), $modes)){
							$mode = $this->in->get('mode');
							$this->objStyles->$mode();
						}
			break;

			//Portalmodules
			case 3:		$path = $this->in->get('selected_id');

						if($this->in->get('mode') == "update"){
							$this->pdh->process_hook_queue();
							$this->portal->get_all_modules();
						}elseif($this->in->get('mode') == "remove"){
							$this->portal->remove($this->code);
						} else {
							if ($path){
								$idList = $this->pdh->get('portal', 'id_by_path', array($path));
								$plugin = $this->pdh->get('portal', 'plugin', array($idList[0]));
								$name = $this->pdh->get('portal', 'name', array($idList[0]));

								$this->portal->uninstall($path, $plugin);
								$this->portal->install($path, $plugin);
								$arrMessage = array(sprintf($this->user->lang('portal_reinstall_success'), $name), $this->user->lang('success'), 'green');
							}
						}
			break;

			//Games
			case 7: 	if($this->in->get('mode') == "remove"){
							$plugin_code = preg_replace("/[^a-zA-Z0-9-_]/", "", $this->code);
							if($plugin_code != "") $this->pfh->Delete($this->root_path.'games/'.$plugin_code.'/');
						}
			break;

		}
		$this->pdh->process_hook_queue();
		$url_suffix = (($this->in->get('simple_head') != "") ? '&simple_head='.$this->in->get('simple_head') : '').(($this->in->get('show_only') != "") ? '&show_only='.$this->in->get('show_only') : '');
		$url = 'admin/manage_extensions.php'.$this->SID.'&mes='.rawurlencode(base64_encode(serialize($arrMessage))).$url_suffix;
		if($this->in->get('autoupd', 0)){
			$url .= '&autoupd=1&current='.$this->in->get('current', 0).'&try='.$this->in->get('try', 0).$url_suffix;
		}
		redirect($url);
	}

	public function display(){
		//Show error / success messages
		if($this->in->exists('mes')){
			$arrMessage = unserialize_noclasses(base64_decode($this->in->get('mes')));
			if(isset($arrMessage[0]) && $arrMessage[1]){
				$this->core->message($arrMessage[0],$arrMessage[1],$arrMessage[2]);
			}
		}

		$intShowOnly = ($this->in->get('show_only', '') != "") ? $this->in->get('show_only', '') : false;


		//Get Extensions
		$arrExtensionList = $this->repo->getExtensionList();
		$arrExtensionListNamed = array();
		if (is_array($arrExtensionList)){
			foreach($arrExtensionList as $catid => $extensions){
				if (is_array($extensions)){
					foreach($extensions as $id => $ext){
						if (!isset($arrExtensionListNamed[$catid])) $arrExtensionListNamed[$catid] = array();
						$arrExtensionListNamed[$catid][$ext['plugin']] = $id;
					}
				}
			}
		}

		//Get Updates
		$urgendUpdates = $this->repo->BuildUpdateArray();
		$allUpdates = $this->repo->BuildUpdateArray(false);
		$arrUpdateCount = array(1=>array('red' => 0, 'yellow' => 0), 2=>array('red' => 0, 'yellow' => 0),3=>array('red' => 0, 'yellow' => 0),4=>array('red' => 0, 'yellow' => 0),7=>array('red' => 0, 'yellow' => 0),11=>array('red' => 0, 'yellow' => 0));

		//=================================================================
		//Plugins

		$this->pm->search(); //search for new plugins
		$plugins_array = $this->pm->get_plugins(PLUGIN_ALL);
		//maybe plugins want to auto-install portalmodules
		$this->portal->get_all_modules();
		$db_plugins = $this->pdh->get('plugins', 'id_list');
		$plugin_count = 0;

		foreach ( $plugins_array as $plugin_code ) {
			if ($plugin_code == 'pluskernel') continue;

			$contact			= $this->pm->get_data($plugin_code, 'contact');
			$contact = (strlen($contact)) ? ((strpos($contact, '@')) ? 'mailto:'.$contact : $contact) : 'https://eqdkp-plus.eu';
			$version			= $this->pm->get_data($plugin_code, 'version');
			$description		= $this->pm->get_data($plugin_code, 'description');
			$long_description	= $this->pm->get_data($plugin_code, 'long_description');
			$manuallink			= $this->pm->get_data($plugin_code, 'manuallink');
			$homepagelink		= $this->pm->get_data($plugin_code, 'homepage');
			$author				= $this->pm->get_data($plugin_code, 'author');
			$bugtracker_url		= (isset($arrExtensionListNamed[1][$plugin_code])) ? sanitize($this->pdh->get('repository', 'bugtracker_url', array(1, $arrExtensionListNamed[1][$plugin_code]))) : '';

			if($this->pm->check($plugin_code, PLUGIN_BROKEN)) {
				//Delete it from database if plugin is broken - means it isn't there anymore.
				$this->pm->delete($plugin_code);
				/*
				$this->tpl->assign_block_vars('plugins_row_broken', array(
					'NAME'			=> $plugin_code,
					'CODE'			=> $plugin_code,
					'DELETE'		=> (in_array($plugin_code, $db_plugins)) ? true : false,
					'DEL_LINK'		=> 'manage_extensions.php'.$this->SID.'&amp;cat=1&amp;mode=delete&amp;code='.$plugin_code.'&amp;link_hash='.$this->CSRFGetToken('mode'),
				));
				*/
				continue;
			}
			//dependencies
			$dep['plusv']	= $this->pm->check_dependency($plugin_code, 'plus_version');
			$dep['games']	= $this->pm->check_dependency($plugin_code, 'games');
			$dep['phpf']	= $this->pm->check_dependency($plugin_code, 'php_functions');

			//show missing functions
			$deptt['phpf'] = $this->user->lang('plug_dep_phpf');
			$needed_functions = $this->pm->get_plugin($plugin_code)->get_dependency('php_functions');
			if( is_array($needed_functions) && (count($needed_functions) > 0) ){
				$deptt['phpf'] .= ':<br />';
				foreach($needed_functions as $function){
					$deptt['phpf'] .= (function_exists($function)) ? '<span class="positive">'.$function.'</span><br />' : '<span class="negative">'.$function.'</span><br />';
				}
			}

			$dep_all = $dep['plusv'] && $dep['games'] && $dep['phpf'];

			if($this->pm->check($plugin_code, PLUGIN_DISABLED)) {
				$link = ( $dep_all ) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=enable&amp;code=' . $plugin_code.'&amp;link_hash='.$this->CSRFGetToken('mode'). '" title="'.$this->user->lang('enable').'"><i class="fa fa-lg fa-toggle-off"></i></a>' : '<i class="fa fa-lg fa-exclamation-triangle" title="'.$this->user->lang('plug_dep_broken_deps').'"></i>';
				$row = 'yellow';
			} elseif ($this->pm->check($plugin_code, PLUGIN_INSTALLED)){
				if (isset($urgendUpdates[$plugin_code])){
					$row = 'red';
					$link = '<a href="javascript:repo_update('.$urgendUpdates[$plugin_code]['plugin_id'].', 1, \''.$plugin_code.'\');" class="needs_update" data-id="'.$urgendUpdates[$plugin_code]['plugin_id'].'" data-category="1" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-lg fa-refresh"></i>';
					$arrUpdateCount[1]['red'] ++;
				} else {
					$row = 'green';
					$link = ( $dep_all ) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=uninstall&amp;code=' . $plugin_code.'&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('uninstall').'"><i class="fa fa-lg fa-toggle-on"></i></a>' : '<i class="fa fa-lg fa-exclamation-triangle" title="'.$this->user->lang('plug_dep_broken_deps').'"></i>';
				}
			} elseif(isset($allUpdates[$plugin_code])){
				$row = 'yellow';
				$link = '<a href="javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].', 1, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="1" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-lg fa-refresh"></i></a>';
				$arrUpdateCount[1]['yellow'] ++;
				$link .= '&nbsp;&nbsp;&nbsp;<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=remove&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('delete').'"><i class="fa fa-lg fa-trash-o"></i></a>';
			} else {
				$row = 'grey';
				$link = ( $dep_all ) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=install&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('install').'"><i class="fa fa-lg fa-toggle-off"></i></a>' : '<i class="fa fa-lg fa-exclamation-triangle" title="'.$this->user->lang('plug_dep_broken_deps').'"></i>';
				$link .= '&nbsp;&nbsp;&nbsp;<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=remove&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('delete').'"><i class="fa fa-lg fa-trash-o"></i></a>';
			}
			$plugin_count++;


			$depout = "";
			foreach($dep as $key => $depdata) {
				$tt = (isset($deptt[$key])) ? $deptt[$key] : $this->user->lang('plug_dep_'.$key);
				if(!$depdata){
					$depout .= '<span class="coretip" data-coretip="'.$tt.'">'.$this->user->lang('plug_dep_'.$key.'_short').'</span> ';
				}
			}

			$this->tpl->assign_block_vars('plugins_row_'.$row, array(
				'NAME'				=> (isset($arrExtensionListNamed[1][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[1][$plugin_code].')">'.$this->pm->get_data($plugin_code, 'name').'</a>' : $this->pm->get_data($plugin_code, 'name'),

				'VERSION'			=> ( !empty($version) ) ? $version : '&nbsp;',
				'CODE'				=> $plugin_code,
				'CONTACT'			=> ( !empty($contact) ) ? ( !empty($author) ) ? '<a href="' . $contact . '">' . $author . '</a>' : '<a href="' . $contact . '">' . $contact . '</a>'  : $author,
				'DESCRIPTION'		=> ( !empty($description) ) ? $description : '&nbsp;',
				'LONG_DESCRIPTION'	=> $long_description,
				'HOMEPAGE_LINK'		=> ($homepagelink != '') ? $homepagelink : false,
				'HOMEPAGE'			=> $this->user->lang('homepage'),
				'MANUAL_LINK'		=> ($manuallink != '') ? $manuallink : false,
				'MANUAL'			=> $this->user->lang('manual'),
				'ACTION_LINK'		=> $link,
				'BUGTRACKER_URL'	=> $bugtracker_url,
				'DEPENDENCIES'		=> ($dep_all) ? '<i class="fa fa-lg fa-check icon-color-green"></i>' : '<i class="fa fa-lg fa-times icon-color-red"></i> '.$depout,
			));
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[1]) && is_array($arrExtensionList[1])){
			foreach ($arrExtensionList[1] as $id => $extension){
				if ($this->pm->search($extension['plugin']) || $extension['plugin'] == 'pluskernel') continue;
				$plugin_count++;
				$row = 'grey_repo';
				$dep['plusv']	= (version_compare($extension['dep_coreversion'], VERSION_INT, '<='));

				$depout = "";
				foreach($dep as $key => $depdata) {
					$tt = (isset($deptt[$key])) ? $deptt[$key] : $this->user->lang('plug_dep_'.$key);
					if(!$depdata){
						$depout .= '<span class="coretip" data-coretip="'.$tt.'">'.$this->user->lang('plug_dep_'.$key.'_short').'</span> ';
					}
				}

				$dl_link = '<a href="javascript:repo_install('.$extension['plugin_id'].', 1, \''.sanitize($extension['plugin']).'\');" ><i class="fa fa-toggle-off fa-lg" title="'.$this->user->lang('install').'"></i></a>';
				$link = ($dep['plusv']) ? $dl_link : '';
				$this->tpl->assign_block_vars('plugins_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'CONTACT'			=> sanitize($extension['author']),
					'DESCRIPTION'		=> sanitize($extension['description']),
					'ACTION_LINK'		=> $link,
					'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
					'DEPENDENCIES'		=> ($dep['plusv']) ? '<i class="fa fa-lg fa-check icon-color-green"></i>' : '<i class="fa fa-lg fa-times icon-color-red"></i> '.$depout,

				));
			}
		}

		$badge = '';
		if ($arrUpdateCount[1]['red']){
			$badge = '<span class="update_available">'.(int)$arrUpdateCount[1]['red'].'</span>';
		} elseif ($arrUpdateCount[1]['yellow']){
			$badge = '<span class="update_available_yellow">'.(int)$arrUpdateCount[1]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'DEP_COUNT'		=> 3,
			'BADGE_1'		=> $badge,
			'PLUGIN_COUNT'	=> $plugin_count,
		));

		//=================================================================
		//Templates

		$default_style = $this->config->get('default_style');
		$arrTemplates = $this->pdh->get('styles', 'styles');
		$arrLocalStyleUpdates = $this->objStyles->getLocalStyleUpdates();
		$arrUninstalledStyles = $this->objStyles->getUninstalledStyles();
		$arrStyles = array();
		$intStyles = 0;

		foreach($arrUninstalledStyles as $key => $install_xml){
			$plugin_code = $key;
			if(isset($allUpdates[$plugin_code])){
				$row = 'yellow';
				$link = '<a href="javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].',2, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="2" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-lg fa-refresh icon-color-yellow"></i></a>';
				$link .= '&nbsp;&nbsp;&nbsp;<a href="manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=remove&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('delete').'"><i class="fa fa-lg fa-trash-o"></i></a>';
				$arrUpdateCount[2]['yellow'] ++;
				$link_plain = 'javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].',2, \''.$plugin_code.'\');';
			} else {
				$row = 'grey';
				$link_plain = 'manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=install&amp;code=' . $key. '&amp;link_hash='.$this->CSRFGetToken('mode');
				$link = '<a href="manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=install&amp;code=' . $key. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="' . $this->user->lang('install') . '"><i class="fa fa-lg fa-toggle-off"></i></a>';
				$link .= '&nbsp;&nbsp;&nbsp;<a href="manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=remove&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('delete').'"><i class="fa fa-lg fa-trash-o"></i></a>';
			}

			$screenshot = '';
			if (file_exists($this->root_path.'templates/'.$plugin_code.'/screenshot.png' )){
				$screenshot = $this->root_path.'templates/'.$plugin_code.'/screenshot.png';
			} elseif(file_exists($this->root_path.'templates/'.$plugin_code.'/screenshot.jpg' )){
				$screenshot = $this->root_path.'templates/'.$plugin_code.'/screenshot.jpg';
			}

			$intStyles++;
			$this->tpl->assign_block_vars('styles_row_'.$row, array(
				'TT_CONTENT'	=> $screenshot,
				'ROWNAME'		=> 'style_'.$row,
				'TT_NAME'		=> ($install_xml->name) ? $install_xml->name : stripslashes($key),
				'VERSION'		=> $install_xml->version,
				'AUTHOR'		=> ($install_xml->authorEmail != "") ? '<a href="mailto:'.$install_xml->authorEmail.'">'.$install_xml->author.'</a>': $install_xml->author,
				'ACTION_LINK'	=> $link,
				'TEMPLATE'		=> $key,
				'USERS'			=> 0,
				'U_EDIT_STYLE'	=> $link_plain,
			));
			$arrStyles[] = (($install_xml->folder) ? $install_xml->folder : stripslashes($key));
		}

		foreach($arrTemplates as $row){
			$screenshot = '';
			if (file_exists($this->root_path.'templates/'.$row['template_path'].'/screenshot.png' )){
				$screenshot = $this->root_path.'templates/'.$row['template_path'].'/screenshot.png';
			} elseif(file_exists($this->root_path.'templates/'.$row['template_path'].'/screenshot.jpg' )){
				$screenshot = $this->root_path.'templates/'.$row['template_path'].'/screenshot.jpg';
			}

			$plugin_code = $row['template_path'];
			if (isset($urgendUpdates[$plugin_code])){
				if (isset($arrLocalStyleUpdates[$plugin_code])){
					$rowname = 'red_local';
					$link = '<a href="manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=update&amp;code=' . $row['style_id']. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-lg fa-refresh icon-color-red"></i></a>';
				} else {
					$rowname = 'red';
					$link = '<a href="javascript:repo_update('.$urgendUpdates[$plugin_code]['plugin_id'].', 2, \''.$plugin_code.'\');" class="needs_update" data-id="'.$urgendUpdates[$plugin_code]['plugin_id'].'" data-category="2" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-lg fa-refresh icon-color-red"></i></a>';
				}
				$arrUpdateCount[2]['red'] ++;
			} elseif(isset($allUpdates[$plugin_code])) {
				$rowname = 'yellow';
				$link = '<a href="javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].', 2, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="2" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-lg fa-refresh icon-color-yellow"></i></a>';
				$arrUpdateCount[2]['yellow'] ++;
			} else {
				$rowname = 'green';
				$link = ($row['style_id'] == $default_style) ? '' :'<a href="javascript:style_delete_warning('.$row['style_id'].');" title="' . $this->user->lang('uninstall') . '"><i class="fa fa-lg fa-toggle-on"></i></a>';
			}

			$this->jquery->Dialog('style_preview', $this->user->lang('template_preview'), array('url'=>$this->server_path."".$this->SID."&style='+ styleid+'", 'width'=>'750', 'height'=>'520', 'modal'=>true, 'withid' => 'styleid'));

			$intStyles++;

			$this->tpl->assign_block_vars('styles_row_'.$rowname, array(
				'ID'				=> $row['style_id'],
				'ROWNAME'			=> 'style_'.$rowname,
				'U_EDIT_STYLE'		=> 'manage_styles.php' . $this->SID . '&amp;edit=true&amp;styleid=' . $row['style_id'],
				'U_DOWNLOAD_STYLE'	=> 'manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=export&amp;code=' . $row['style_id'].'&amp;link_hash='.$this->CSRFGetToken('mode'),
				#'ENABLE_ICON'		=> ($row['enabled'] == '1') ? 'green' : 'red',
				#'ENABLE_ICON_INFO'	=> ($row['enabled'] == '1') ? $this->user->lang('style_enabled_info') : $this->user->lang('style_disabled_info'),
				'L_ENABLE'			=> ($row['enabled'] == '1') ? $this->user->lang('deactivate') : $this->user->lang('activate'),
				'ENABLE'			=> ($row['enabled'] == '1') ? 'fa fa-check-square-o fa-lg icon-color-green' : 'fa fa-square-o fa-lg icon-color-red',
				'U_ENABLE'			=> ($row['enabled'] == '1') ? 'manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=disable&amp;code=' . $row['style_id'].'&amp;link_hash='.$this->CSRFGetToken('mode') : 'manage_extensions.php' . $this->SID . '&amp;mode=enable&amp;cat=2&amp;code=' . $row['style_id'].'&amp;link_hash='.$this->CSRFGetToken('mode'),
				'S_DEFAULT'			=> ($row['style_id'] == $default_style) ? true : false,
				'S_DEACTIVATED'		=> ($row['enabled'] != '1') ? true : false,
				'STANDARD'			=> ($row['style_id'] == $default_style) ? 'checked="checked"' : '',
				'VERSION'			=> $row['style_version'],
				'AUTHOR'			=> ($row['style_contact'] != "") ? '<a href="mailto:'.$row['style_contact'].'">'.$row['style_author'].'</a>': $row['style_author'],
				'TT_CONTENT'		=> $screenshot,
				'TT_NAME'			=> (isset($arrExtensionListNamed[2][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[2][$plugin_code].')">'.$row['style_name'].'</a>' : $row['style_name'],
				'TEMPLATE'			=> $row['template_path'],
				'USERS'				=> $row['users'],
				'ACTION_LINK'		=> $link,
				'BUGTRACKER_URL'	=> (isset($arrExtensionListNamed[2][$plugin_code])) ? sanitize($this->pdh->get('repository', 'bugtracker_url', array(2, $arrExtensionListNamed[2][$plugin_code]))) : '',

			));

			$arrStyles[] = $plugin_code;
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[2]) && is_array($arrExtensionList[2])){
			foreach ($arrExtensionList[2] as $id => $extension){
				if (in_array($extension['plugin'], $arrStyles)) continue;
				$row = 'grey';

				$link = '<a href="javascript:repo_install('.$extension['plugin_id'].',2, \''.sanitize($extension['plugin']).'\');" ><i class="fa fa-toggle-off fa-lg" title="'.$this->user->lang('install').'"></i></a>';
				$intStyles++;
				$this->tpl->assign_block_vars('styles_row_'.$row, array(
					'TT_NAME'			=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'AUTHOR'			=> sanitize($extension['author']),
					'TEMPLATE'			=> sanitize($extension['plugin']),
					'DESCRIPTION'		=> sanitize($extension['description']),
					'ACTION_LINK'		=> $link,
					'ROWNAME'			=> 'style_'.$row,
					'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
					'TT_CONTENT'		=> 'http://cdn1.eqdkp-plus.eu/repository/screenshot.php?extid='.$extension['plugin_id'],
				));

			}
		}


		$this->jquery->dialog('style_default_info', $this->user->lang('default_style'), array('message' => $this->user->lang('style_default_info').'<br /><br /><label><input type="radio" name="override" value="0" onchange="change_override(1);">'.$this->user->lang('yes').'</label>  <label><input type="radio" name="override" value="1" checked="checked" onchange="change_override(0);">'.$this->user->lang('no').'</label>', 'custom_js' => 'submit_form();', 'height' => 300), 'confirm');
		$this->jquery->dialog('style_reset_warning', $this->user->lang('reset_style'), array('message' => $this->user->lang('style_confirm_reset'), 'height' => 300, 'url' => $this->root_path.'admin/manage_extensions.php' . $this->SID . '&link_hash='.$this->CSRFGetToken('mode')."&cat=2&mode=reset&code='+ styleid+'", 'withid' => 'styleid'), 'confirm');
		$this->jquery->dialog('style_delete_warning', $this->user->lang('delete_style'), array('message' => $this->user->lang('confirm_delete_style'), 'height' => 300, 'url'=> $this->root_path.'admin/manage_extensions.php' . $this->SID . '&link_hash='.$this->CSRFGetToken('mode')."&cat=2&mode=uninstall&code='+ styleid+'", 'withid' => 'styleid'), 'confirm');

		$badge = '';
		if ($arrUpdateCount[2]['red']){
			$badge = '<span class="update_available">'.(int)$arrUpdateCount[2]['red'].'</span>';
		} elseif ($arrUpdateCount[2]['yellow']){
			$badge = '<span class="update_available_yellow">'.(int)$arrUpdateCount[2]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_2'		=> $badge,
			'STYLE_COUNT'	=> $intStyles,
		));

		//=================================================================
		//Portal Modules

		$arrTmpModules = array();
		$intPortalModules = 0;

		if (isset($arrExtensionList[3]) && is_array($arrExtensionList[3])){
			foreach ($arrExtensionList[3] as $id => $extension){
				$arrTmpModules[$extension['plugin']] = $extension;
			}
		}

		$arrModules = $this->pdh->aget('portal', 'portal', 0, array($this->pdh->get('portal', 'id_list')));
		if (is_array($arrModules)){
			foreach($arrModules as $id => $value){
				if((int)$value['child'] === 1) continue;

				$row = 'green';
				$link = '';
				$plugin_code = $value['path'];
				if(!$plugin_code || $plugin_code == ""){
					$this->core->message("Could not initiate module ID ".$id, "Error", 'red');
					continue;
				}

				$class_name = $plugin_code.'_portal';
				if(!class_exists($class_name)) continue;
				$del_link = "";
				//Ignore Plugin Moduls in terms of repo-updates
				if (empty($value['plugin'])) {
					if (isset($urgendUpdates[$plugin_code])){
						$row = 'red';
						$link = '<a href="javascript:repo_update('.$urgendUpdates[$plugin_code]['plugin_id'].',3, \''.$plugin_code.'\');" class="needs_update" data-id="'.$urgendUpdates[$plugin_code]['plugin_id'].'" data-category="3" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg"></i></a>';
						$arrUpdateCount[3]['red'] ++;
					}elseif(isset($allUpdates[$plugin_code])){
						$row = 'yellow';
						$link = '<a href="javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].',3, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="3" data-code="'.$plugin_code.'"  title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg"></i></a>';
						$arrUpdateCount[3]['yellow'] ++;
					}

					$del_link = '<a href="manage_extensions.php' . $this->SID . '&amp;cat=3&amp;mode=remove&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('delete').'"><i class="fa fa-lg fa-trash-o"></i></a>';

				}
				//Add Reinstall Link if no update available
				$reinst_link = '<i class="fa fa-retweet fa-lg" title="'.$this->user->lang('reinstall').'" onclick="javascript:reinstall_portal(\''.$plugin_code.'\')" style="cursor:pointer;"></i>';

				$intPortalModules++;
				$contact = sanitize($class_name::get_data('contact'));
				$contact = (strlen($contact)) ? ((strpos($contact, '@')) ? 'mailto:'.$contact : $contact) : 'https://eqdkp-plus.eu';
				$author = sanitize($class_name::get_data('author'));

				$this->tpl->assign_block_vars('pm_row_'.$row, array(
					'NAME'				=> (isset($arrExtensionListNamed[3][$value['path']])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[3][$value['path']].')">'.$value['name'].'</a>' : $value['name'],
					'VERSION'			=> sanitize($value['version']),
					'CODE'				=> sanitize($value['path']),
					'CONTACT'			=> ( !empty($contact) ) ? ( !empty($author) ) ? '<a href="' . $contact . '">' . $author . '</a>' : '<a href="' . $contact . '">' . $contact . '</a>'  : $author,
					'ACTION_LINK'		=> $link,
					'REINSTALL_LINK'	=> ($row == 'green') ? $reinst_link : '',
					'DELETE_LINK'		=> $del_link,
					'DESCRIPTION'		=> (isset($arrTmpModules[$value['path']])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[3][$value['path']].')">'.sanitize(cut_text($arrTmpModules[$value['path']]['description'], 100)).'</a>' : '',
					'BUGTRACKER_URL'	=> (isset($arrExtensionListNamed[3][$plugin_code])) ? sanitize($this->pdh->get('repository', 'bugtracker_url', array(3, $arrExtensionListNamed[3][$plugin_code]))) : '',

				));

			}
			$this->confirm_delete($this->user->lang('portal_reinstall_warn'), 'manage_extensions.php'.$this->SID.'&cat=3', true, array('function' => 'reinstall_portal', 'handler' => 'mode'));
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[3]) && is_array($arrExtensionList[3])){
			foreach ($arrExtensionList[3] as $id => $extension){

				if ((is_array(search_in_array($extension['plugin'], $arrModules, true, 'path')))) continue;
				$row = 'grey';
				$intPortalModules++;

				$link = '<a href="javascript:repo_install('.$extension['plugin_id'].',3, \''.sanitize($extension['plugin']).'\');" title="'.$this->user->lang('install').'"><i class="fa fa-toggle-off fa-lg"></i></a>';
				$this->tpl->assign_block_vars('pm_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'CONTACT'			=> sanitize($extension['author']),
					'DESCRIPTION'		=> '<a href="javascript:repoinfo('.$id.')">'.sanitize(cut_text($extension['description'])).'</a>',
					'ACTION_LINK'		=> $link,
					'RATING'			=> $this->jquery->starrating('extension_'.md5($extension['plugin']), $this->env->phpself , array('score' => $extension['rating'], 'readonly' => true)),
					'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
				));

			}
		}

		$badge = '';

		if ($arrUpdateCount[3]['red']){
			$badge = '<span class="update_available">'.(int)$arrUpdateCount[3]['red'].'</span>';
		} elseif ($arrUpdateCount[3]['yellow']){
			$badge = '<span class="update_available_yellow">'.(int)$arrUpdateCount[3]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_3'		=> $badge,
			'PORTAL_COUNT'	=> $intPortalModules,
		));

		//=================================================================
		//Games
		$arrGames = $this->game->get_games();
		$arrGameVersions = $this->game->get_versions();
		$arrGameAuthors = $this->game->get_authors();
		$arrTmpExtension = array();
		$intGames = 0;

		if (isset($arrExtensionList[7]) && is_array($arrExtensionList[7])){
			foreach ($arrExtensionList[7] as $id => $extension){
				$arrTmpExtension[$extension['plugin']] = $extension;
			}
		}

		if (is_array($arrGames)){
			foreach($arrGames as $id => $value){
				$plugin_code = $value;
				if (isset($urgendUpdates[$plugin_code])){
						$row = 'red';
						$link = '<a href="javascript:repo_update('.$urgendUpdates[$plugin_code]['plugin_id'].',7, \''.$plugin_code.'\');" class="needs_update" data-id="'.$urgendUpdates[$plugin_code]['plugin_id'].'" data-category="7" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg"></i></a>';
						$arrUpdateCount[7]['red'] ++;
				}elseif(isset($allUpdates[$plugin_code])){
					$row = 'yellow';
					$link = '<a href="javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].',7, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="7" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg" ></i></a>';
					$arrUpdateCount[7]['yellow'] ++;
				} else {
						$row = 'green';
						$link = '';
				}

				$intGames++;
				$this->tpl->assign_block_vars('games_row_'.$row, array(
					'NAME'				=> (isset($arrExtensionListNamed[7][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[7][$plugin_code].')">'.$this->game->game_name($plugin_code).'</a>' : $this->game->game_name($plugin_code),
					'VERSION'			=> $arrGameVersions[$plugin_code],
					'CODE'				=> sanitize($plugin_code),
					'CONTACT'			=> (isset($arrTmpExtension[$plugin_code])) ? $arrTmpExtension[$plugin_code]['author'] : $arrGameAuthors[$plugin_code],
					'DESCRIPTION'		=> (isset($arrTmpExtension[$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[7][$plugin_code].')">'.cut_text($arrTmpExtension[$plugin_code]['description'], 100).'</a>' : '',
					'RATING'			=> (isset($arrTmpExtension[$plugin_code])) ? $this->jquery->starrating('extension_'.md5($arrTmpExtension[$plugin_code]['plugin']), $this->env->phpself , array('score' => $arrTmpExtension[$plugin_code]['rating'], 'readonly' => true)) : '',
					'ACTION_LINK'		=> $link,
					'DELETE_LINK'		=> ($plugin_code != $this->config->get('default_game')) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=7&amp;mode=remove&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'" title="'.$this->user->lang('delete').'"><i class="fa fa-lg fa-trash-o"></i></a>' : '',
					'BUGTRACKER_URL'	=> (isset($arrExtensionListNamed[7][$plugin_code])) ? sanitize($this->pdh->get('repository', 'bugtracker_url', array(7, $arrExtensionListNamed[7][$plugin_code]))) : '',
				));
			}
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[7]) && is_array($arrExtensionList[7])){
			foreach ($arrExtensionList[7] as $id => $extension){
				if (in_array($extension['plugin'], $arrGames)) continue;
				$row = 'grey';
				$intGames++;

				$link = '<a href="javascript:repo_install('.$extension['plugin_id'].',7, \''.sanitize($extension['plugin']).'\');" title="'.$this->user->lang('install').'"><i class="fa fa-toggle-off fa-lg"></i></a>';
				$this->tpl->assign_block_vars('games_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'CONTACT'			=> sanitize($extension['author']),
					'DESCRIPTION'		=> sanitize(cut_text($extension['description'], 100)),
					'ACTION_LINK'		=> $link,
					'RATING'			=> $this->jquery->starrating('extension_'.md5($extension['plugin']), $this->env->phpself , array('score' => $extension['rating'], 'readonly' => true)),
					'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
				));

			}
		}

		$badge = '';
		if ($arrUpdateCount[7]['red']){
			$badge = '<span class="update_available">'.(int)$arrUpdateCount[7]['red'].'</span>';
		} elseif ($arrUpdateCount[7]['yellow']){
			$badge = '<span class="update_available_yellow">'.(int)$arrUpdateCount[7]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_7'	=> $badge,
			'GAME_COUNT'=> $intGames,
		));

		//=================================================================
		//Languages

		$arrLanguages = $arrLanguageVersions = array();
		$intLanguages = 0;
		// Build language array
		if($dir = @opendir($this->core->root_path . 'language/')){
			while ( $file = @readdir($dir) ){
				if ((!is_file($this->root_path . 'language/' . $file)) && (!is_link($this->root_path . 'language/' . $file)) && valid_folder($file)){
					include($this->root_path.'language/'.$file.'/lang_main.php');
					$lang_name_tp = (($lang['ISO_LANG_NAME']) ? $lang['ISO_LANG_NAME'].' ('.$lang['ISO_LANG_SHORT'].')' : ucfirst($file));
					$arrLanguages[$file]		= $lang_name_tp;
					$arrLanguageVersions[$file] = $lang['LANG_VERSION'];
				}
			}
		}

		if (is_array($arrLanguages)){
			foreach($arrLanguages as $id => $value){
				$plugin_code = $id;
				if (isset($urgendUpdates[$plugin_code])){
						$row = 'red';
						$link = '<a href="javascript:repo_update('.$urgendUpdates[$plugin_code]['plugin_id'].',11, \''.$plugin_code.'\');" class="needs_update" data-id="'.$urgendUpdates[$plugin_code]['plugin_id'].'" data-category="11" data-code="'.$plugin_code.'" title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg"></i></a>';
						$arrUpdateCount[11]['red'] ++;
				}elseif(isset($allUpdates[$plugin_code])){
					$row = 'yellow';
					$link = '<a href="javascript:repo_update('.$allUpdates[$plugin_code]['plugin_id'].',11, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="11" data-code="'.$plugin_code.'"  title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg"></i></a>';
					$arrUpdateCount[11]['yellow'] ++;
				} else {
					$intExtensionID = (isset($arrExtensionListNamed[11][$plugin_code])) ? $arrExtensionListNamed[11][$plugin_code] : false;
						$row = 'green';
						$link = ($intExtensionID) ? '<a href="javascript:repo_update('.$arrExtensionList['11'][$intExtensionID]['plugin_id'].',11, \''.$plugin_code.'\');" class="needs_update" data-id="'.$allUpdates[$plugin_code]['plugin_id'].'" data-category="11" data-code="'.$plugin_code.'"  title="'.$this->user->lang('uc_bttn_update').'"><i class="fa fa-refresh fa-lg"></i></a>' : '';
				}

				$intLanguages++;
				$this->tpl->assign_block_vars('language_row_'.$row, array(
					'NAME'				=> (isset($arrExtensionListNamed[11][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[11][$plugin_code].')">'.$value.'</a>' : $value,
					'VERSION'			=> $arrLanguageVersions[$plugin_code],
					'ACTION_LINK'		=> $link,
					'BUGTRACKER_URL'	=> (isset($arrExtensionListNamed[11][$plugin_code])) ? sanitize($this->pdh->get('repository', 'bugtracker_url', array(11, $arrExtensionListNamed[11][$plugin_code]))) : '',
				));
			}
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[11]) && is_array($arrExtensionList[11])){
			foreach ($arrExtensionList[11] as $id => $extension){
				if (isset($arrLanguages[$extension['plugin']])) continue;
				$row = 'grey';

				$intLanguages++;
				$link = '<a href="javascript:repo_install('.$extension['plugin_id'].', 11, \''.sanitize($extension['plugin']).'\');" title="'.$this->user->lang('install').'"><i class="fa fa-toggle-off fa-lg"></i></a>';
				$this->tpl->assign_block_vars('language_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'ACTION_LINK'		=> $link,
					'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
				));

			}
		}

		$badge = '';
		if ($arrUpdateCount[11]['red']){
			$badge = '<span class="update_available">'.(int)$arrUpdateCount[11]['red'].'</span>';
		} elseif ($arrUpdateCount[11]['yellow']){
			$badge = '<span class="update_available_yellow">'.(int)$arrUpdateCount[11]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_11'		=> $badge,
			'LANGUAGE_COUNT' => $intLanguages,
		));
		//=================================================================
		//Search for extensions
		if($this->in->get('search') != ""){
			$arrSearchResults = $this->pdh->get('repository', 'search', array($this->in->get('search')));

			foreach($arrSearchResults as $intExtension => $extension){
				if ($extension['plugin'] == 'pluskernel') continue;
				//Check if the extension is already installed
				$blnInstallable = true;
				$strCategoryIcon = '';

				if($extension['category'] == 1){
					$strCategoryIcon = '<i class="fa fa-cogs"></i>';
					if ($this->pm->search($extension['plugin']))  $blnInstallable = false;
				} elseif($extension['category'] == 2){
					$strCategoryIcon = '<i class="fa fa-paint-brush"></i>';
					if (in_array($extension['plugin'], $arrStyles))  $blnInstallable = false;
				} elseif($extension['category'] == 3){
					$strCategoryIcon = '<i class="fa fa-columns"></i>';
					if ((is_array(search_in_array($extension['plugin'], $arrModules, true, 'path')))) $blnInstallable = false;
				} elseif($extension['category'] == 7){
					$strCategoryIcon = '<i class="fa fa-gamepad"></i>';
					if (in_array($extension['plugin'], $arrGames)) $blnInstallable = false;
				} elseif($extension['category'] == 11){
					$strCategoryIcon = '<i class="fa fa-globe"></i>';
					if (isset($arrLanguages[$extension['plugin']])) $blnInstallable = false;
				}

				$dep = array();
				$dep['plusv']	= (version_compare($extension['dep_coreversion'], VERSION_INT, '<='));

				$depout = "";
				foreach($dep as $key => $depdata) {
					$tt = (isset($deptt[$key])) ? $deptt[$key] : $this->user->lang('plug_dep_'.$key);
					if(!$depdata){
						$depout .= '<span class="coretip" data-coretip="'.$tt.'">'.$this->user->lang('plug_dep_'.$key.'_short').'</span> ';
					}
				}

				$dl_link = '<a href="javascript:repo_install('.$extension['plugin_id'].', '.$extension['category'].', \''.sanitize($extension['plugin']).'\');" ><i class="fa fa-toggle-off fa-lg" title="'.$this->user->lang('install').'"></i></a>';
				$link = ($dep['plusv']) ? $dl_link : '';
				$this->tpl->assign_block_vars('plugins_search_row', array(
						'NAME'				=> '<a href="javascript:repoinfo('.$intExtension.')">'.$extension['name'].'</a>',
						'VERSION'			=> sanitize($extension['version']),
						'CATEGORY'			=> $this->user->lang('pi_category_'.$extension['category']),
						'CATEGORY_ICON'		=> $strCategoryIcon,
						'CODE'				=> sanitize($extension['plugin']),
						'CONTACT'			=> sanitize($extension['author']),
						'DESCRIPTION'		=> sanitize($extension['description']),
						'ACTION_LINK'		=> ($blnInstallable) ? $link : '',
						'BUGTRACKER_URL'	=> sanitize($extension['bugtracker_url']),
						'DEPENDENCIES'		=> ($dep['plusv']) ? '<i class="fa fa-lg fa-check icon-color-green"></i>' : '<i class="fa fa-lg fa-times icon-color-red"></i> '.$depout,
				));
			}

			$this->tpl->assign_vars(array(
					'S_SHOW_EXT_SEARCH'		=> true,
					'SEARCH_COUNT'			=> count($arrSearchResults),
					'S_EXT_SEARCH_VAL'		=> sanitize($this->in->get('search')),
			));

		}

		//=================================================================
		//Common Output


		//Tabs
		$this->jquery->Tab_header('plus_plugins_tab', true);
		if ($this->in->exists('tab')){
			$this->jquery->Tab_Select('plus_plugins_tab', $this->in->get('tab',0));
		}
		if($this->in->get('search') != ""){
			$this->jquery->Tab_Select('plus_plugins_tab', 6);
		}

		$this->jquery->Dialog('update_confirm', '', array('custom_js'	=> 'repo_update_start(extid, cat, extensioncode);', 'message'	=> $this->user->lang('repo_updatewarning').'<br /><br /><input type="checkbox" onclick="hide_update_warning(this.checked);" value="1" />'.$this->user->lang('repo_hide_updatewarning'), 'withid'	=> 'extid, cat, extensioncode', 'width'=> 300, 'height'=>300), 'confirm');

		$this->jquery->Dialog('repoinfo', $this->user->lang('repo_extensioninfo'), array('url'=>$this->root_path."admin/manage_extensions.php".$this->SID."&info='+moduleid+'", 'width'=>'700', 'height'=>'600', 'withid'=>'moduleid'));

		foreach ($this->repo->DisplayCategories() as $key=>$category){
			$this->tpl->assign_vars(array(
				'L_CATEGORY_'.$key	=> $category,
				'S_SHOW_CAT_'.$key => (!$intShowOnly || $intShowOnly == $key) ? true : false,
			));
		}

		$this->tpl->assign_vars(array(
			'S_HIDE_UPDATEWARNING'		=> (int)$this->config->get('repo_hideupdatewarning'),
			'CSRF_MODE_TOKEN' 			=> $this->CSRFGetToken('mode'),
			'CSRF_UPDATEWARNING_TOKEN'	=> $this->CSRFGetToken('hide_update_warning'),
			'AUTOUPD_ON' 				=> $this->in->get('autoupd', 0) ? true : false,
			'AUTOUPD_CURRENT' 			=> $this->in->get('current', 0),
			'AUTOUPD_TRY'				=> $this->in->get('try', 0),
			'S_SHOW_CAT_UPLOAD'			=> ((!$intShowOnly || $intShowOnly == 'update') ? true : false),
			'S_SHOW_TABS'				=> (!$intShowOnly),
			'S_MANUAL_UPLOAD'			=> (class_exists("ZipArchive")) ? true : false,
			'ME_URL_SUFFIX'				=> (($this->in->get('simple_head') != "") ? '&simple_head='.$this->in->get('simple_head') : '').(($this->in->get('show_only') != "") ? '&show_only='.$this->in->get('show_only') : '')
		));

		$this->tpl->add_css('
			.ui-progressbar { position:relative; height:30px;}
			.nl_progressbar_label { position: absolute; width: 90%; text-align: center; line-height: 30px; left:5%; right:5%;}
		');

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('extensions'),
			'template_file'		=> 'admin/manage_extensions.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('extension_repo'), 'url'=>$this->root_path.'admin/manage_extensions.php'.$this->SID],
			],
			'display'			=> true
		]);
	}
}
registry::register('Manage_Extensions');
