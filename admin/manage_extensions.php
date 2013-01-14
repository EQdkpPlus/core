<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
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
define('NO_MMODE_REDIRECT', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Extensions extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'pm', 'pfh', 'html', 'portal', 'game',
			'repo'		=> 'repository',
			'objStyles'	=> 'styles',
			'encrypt'	=> 'encrypt'
		);
		return array_merge(parent::$shortcuts, $shortcuts);
	}
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
			'hide_update_warning'	=> array('process' => 'hide_update_warning', 'csrf'=>true),
			'upload' => array('process' => 'process_upload', 'csrf'=>true),
		);
		parent::__construct(false, $handler);
		$this->code = $this->in->get('code', '');
		$this->process();
	}
	
	public function repo_info(){
		$extension = $this->pdh->get('repository', 'row', $this->in->get('info', 0));
		
		$this->tpl->assign_vars(array(
			'CATEGORY'			=> sanitize($extension['category']),
			'CODE'				=> sanitize($extension['plugin']),
			'NAME'				=> sanitize($extension['name']),
			'DATE'				=> $this->time->user_date($extension['date'], true),
			'AUTHOR'			=> sanitize($extension['author']),
			'DESCRIPTION'		=> nl2br(sanitize($extension['shortdesc'])),
			'VERSION'			=> sanitize($extension['version']),
			'LEVEL'				=> sanitize($extension['level']),
			'CHANGELOG'			=> nl2br(sanitize($extension['changelog'])),
			'RATING'			=> $this->jquery->StarRating('extension_'.md5($extension['plugin']), array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5), '', $extension['rating'], true),
		));
		
		$this->core->set_vars(array(
			'page_title'		=> 'Repo Info',
			'template_file'		=> 'admin/manage_extensions_repoinfo.html',
			'header_format'		=> 'simple',
			'display'			=> true
		));
	}

	public function process_upload(){
		$tempname		= $_FILES['extension']['tmp_name'];
		$name			= $_FILES['extension']['name'];
		$filetype		= $_FILES['extension']['type'];
		$upload_id		= md5(time().rand());
		
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
			$this->pfh->secure_folder('tmp/'.$upload_id, 'repository');
			$blnResult = $this->repo->unpackPackage($tempname, $this->pfh->FolderPath('tmp/'.$upload_id, 'repository'));
			if ($blnResult){
				$src_path = $extension_name = false;
				
				if (is_file($this->pfh->FolderPath('tmp/'.$upload_id, 'repository').'package.xml')){
					$xml = simplexml_load_file($this->pfh->FolderPath('tmp/'.$upload_id, 'repository').'package.xml');
					if ($xml && $xml->folder != ''){
						$extension_name = $xml->folder;
						$src_path = $this->pfh->FolderPath('tmp/'.$upload_id, 'repository');
						if (is_dir($this->pfh->FolderPath('tmp/'.$upload_id, 'repository').$extension_name)){
							$src_path = $this->pfh->FolderPath('tmp/'.$upload_id, 'repository').$extension_name;
						}
						
						$arrAttributes = $xml->attributes();
						
						switch ($arrAttributes['type']){
							case 'plugin':			$target = $this->root_path.'plugins';	$cat=1; 	break;
							case 'game':			$target = $this->root_path.'games';		$cat=7;		break;
							case 'template':		$target = $this->root_path.'templates';	$cat=1;		break;
							case 'portal':			$target = $this->root_path.'portal';	$cat=3;		break;
							default: $target = false;
						}
						
						$blnResult = $this->repo->full_copy($src_path, $target.'/'.$extension_name);
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

	//Hide Confirm Dialog before starting Update
	public function hide_update_warning(){
		if ((int)$this->in->get('hide', 0) == 0){
			$this->config->del('repo_hideupdatewarning');
		} else {
			$this->config->set('repo_hideupdatewarning', 1);
		}
		exit;
	}

	//Get Extension Download-Link
	public function process_step1(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
			$downloadLink = $this->repo->getExtensionDownloadLink($this->in->get('cat', 0), $this->code);
			if ($downloadLink && strlen($downloadLink['link'])){
				$this->config->set(md5($this->in->get('cat', 0).$this->code).'_link', $this->encrypt->encrypt($downloadLink['link']), 'repository');
				$this->config->set(md5($this->in->get('cat', 0).$this->code).'_hash', $this->encrypt->encrypt($downloadLink['hash']), 'repository');
				$this->config->set(md5($this->in->get('cat', 0).$this->code).'_signature', $this->encrypt->encrypt($downloadLink['signature']), 'repository');
				echo "true";
			} else {
				echo $this->user->lang('repo_step1_error');
			}
		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		exit;
	}

	//Download Package
	public function process_step2(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
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
			}

		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		exit;
	}

	//Unzip Package
	public function process_step3(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
			$destFolder = $this->pfh->FolderPath('tmp/'.md5($this->in->get('cat', 0).$this->code),'repository');
			$srcFolder = $this->pfh->FolderPath('','repository');
			$filename = 'repo_'.md5($this->in->get('cat', 0).$this->code).'.zip';

			if ($this->repo->unpackPackage($srcFolder.$filename, $destFolder)){
				echo "true";
			} else {
				$this->pfh->Delete('', 'repository');
				echo $this->user->lang('repo_step3_error');
			}

		} else {
			echo $this->user->lang('repo_unknown_error');
		}
		exit;
	}

	//Copy files
	public function process_step4(){
		if ($this->in->get('cat', 0) && strlen($this->code)){
			$srcFolder = $this->pfh->FolderPath('tmp/'.md5($this->in->get('cat', 0).$this->code),'repository');
			if (is_dir($srcFolder.$this->code)){
				$srcFolder .= $this->code;
			} elseif (is_dir($srcFolder.strtolower($this->code))){
				$srcFolder .= strtolower($this->code);
			}

			switch ((int)$this->in->get('cat', 0)){
				case 1:			$target = $this->root_path.'plugins/';	break;
				case 2:			$target = $this->root_path.'templates/';	break;
				case 3:			$target = $this->root_path.'portal/';	break;
				case 7:			$target = $this->root_path.'games/';	break;
			}

			if($target){
				$result = $this->repo->full_copy($srcFolder, $target.strtolower($this->code));
				if ($result){
					echo "true";
				} else {
					echo $this->user->lang('repo_step4_error');
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
		exit;
	}

	//Installing and Uninstalling Extensions
	public function mode() {
		switch((int)$this->in->get('cat', 0)){
			//Plugins
			case 1:		$modes = array('install', 'enable', 'uninstall', 'delete');
						if(!in_array($this->in->get('mode'), $modes)) return;
						$mode = $this->in->get('mode');
						$this->pm->search();
						$result = $this->pm->$mode($this->code);
						if($result) {
							$this->core->message(sprintf($this->user->lang('plugin_inst_message'), $this->code, $this->user->lang('plugin_inst_'.$mode)), $this->user->lang('success'), 'green');
						} else {
							$this->core->message(sprintf($this->user->lang('plugin_inst_errormsg'), $this->code, $this->user->lang('plugin_inst_'.$mode)), $this->user->lang('error'), 'red');
						}
						$this->pdh->process_hook_queue();
			break;

			//Templates
			case 2:		$modesWithParam = array('install', 'uninstall', 'enable', 'disable', 'reset', 'export', 'update', 'process_update');
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
						} else {
							return;
						}
						$this->pdh->process_hook_queue();
			break;
						
			//Portalmodules
			case 3:		$path = $this->in->get('selected_id');
						if(!$path) return;
						$idList = $this->pdh->get('portal', 'id_list', array(array('path' => $path)));
						$id = array_keys($idList);
						$plugin = $this->pdh->get('portal', 'plugin', array($idList[$id[0]]));
						$name = $this->pdh->get('portal', 'name', array($idList[$id[0]]));
						
						$this->portal->uninstall($path, $plugin);
						$this->portal->install($path, $plugin);
						$this->core->message(sprintf($this->user->lang('portal_reinstall_success'), $name), $this->user->lang('success'), 'green');
						$this->pdh->process_hook_queue();
			break;
		}
	}

	public function display(){
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
		
		$blnRequirements = $this->repo->checkRequirements();

		//Get Updates
		$urgendUpdates = $this->repo->BuildUpdateArray();
		$allUpdates = $this->repo->BuildUpdateArray(false);
		$arrUpdateCount = array(1=>array('red' => 0, 'yellow' => 0), 2=>array('red' => 0, 'yellow' => 0),3=>array('red' => 0, 'yellow' => 0),4=>array('red' => 0, 'yellow' => 0),7=>array('red' => 0, 'yellow' => 0));

		//=================================================================
		//Plugins

		$this->pm->search(); //search for new plugins
		$plugins_array = $this->pm->get_plugins(PLUGIN_ALL);
		//maybe plugins want to auto-install portalmodules
		$this->portal->get_all_modules();
		$plugin_count = count($plugins_array);
		$db_plugins = $this->pdh->get('plugins', 'id_list');

		foreach ( $plugins_array as $plugin_code ) {
			if ($plugin_code == 'pluskernel') continue;

			$contact			= $this->pm->get_data($plugin_code, 'contact');
			$version			= $this->pm->get_data($plugin_code, 'version');
			$description		= $this->pm->get_data($plugin_code, 'description');
			$long_description	= $this->pm->get_data($plugin_code, 'long_description');
			$manuallink			= $this->pm->get_data($plugin_code, 'manuallink');
			$homepagelink		= $this->pm->get_data($plugin_code, 'homepage');
			$author				= $this->pm->get_data($plugin_code, 'author');

			if($this->pm->check($plugin_code, PLUGIN_BROKEN)) {
				$this->tpl->assign_block_vars('plugins_row_broken', array(
					'NAME'			=> $plugin_code,
					'CODE'			=> $plugin_code,
					'DELETE'		=> (in_array($plugin_code, $db_plugins)) ? true : false,
					'DEL_LINK'		=> 'manage_extensions.php'.$this->SID.'&amp;cat=1&amp;mode=delete&amp;code='.$plugin_code.'&amp;link_hash='.$this->CSRFGetToken('mode'),
				));
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
				$link = ( $dep_all ) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=enable&amp;code=' . $plugin_code.'&amp;link_hash='.$this->CSRFGetToken('mode'). '" title="'.$this->user->lang('plug_enable_info').'">' . $this->user->lang('enable') . '</a>' : $this->user->lang('plug_dep_broken_deps');
				$row = 'yellow';
			} elseif ($this->pm->check($plugin_code, PLUGIN_INSTALLED)){
				if (isset($urgendUpdates[$plugin_code])){
					$row = 'red';
					$link = '<a href="javascript:repo_update(1, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
					if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
					$arrUpdateCount[1]['red'] ++;
				} else {
					$row = 'green';
					$link = ( $dep_all ) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=uninstall&amp;code=' . $plugin_code.'&amp;link_hash='.$this->CSRFGetToken('mode'). '">' . $this->user->lang('uninstall') . '</a>' : $this->user->lang('plug_dep_broken_deps');
				}
			} elseif(isset($allUpdates[$plugin_code])){
				$row = 'yellow';
				$link = '<a href="javascript:repo_update(1, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
				if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
				$arrUpdateCount[1]['yellow'] ++;
			} else {
				$row = 'grey';
				$link = ( $dep_all ) ? '<a href="manage_extensions.php' . $this->SID . '&amp;cat=1&amp;mode=install&amp;code=' . $plugin_code. '&amp;link_hash='.$this->CSRFGetToken('mode').'">' . $this->user->lang('install') . '</a>' : $this->user->lang('plug_dep_broken_deps');
			}

			$this->tpl->assign_block_vars('plugins_row_'.$row, array(
				'NAME'				=> (isset($arrExtensionListNamed[1][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[1][$plugin_code].')">'.$this->pm->get_data($plugin_code, 'name').'</a>' : $this->pm->get_data($plugin_code, 'name'),

				'VERSION'			=> ( !empty($version) ) ? $version : '&nbsp;',
				'CODE'				=> $plugin_code,
				'CONTACT'			=> ( !empty($contact) ) ? ( !empty($author) ) ? '<a href="mailto:' . $contact . '">' . $author . '</a>' : '<a href="mailto:' . $contact . '">' . $contact . '</a>'  : $author,
				'DESCRIPTION'		=> ( !empty($description) ) ? $description : '&nbsp;',

				'LONG_DESCRIPTION'	=> $this->html->ToolTip($long_description,$this->html->toggleIcons($long_description,'help.png','help_off.png','images/glyphs/',$this->user->lang('description'),false, false)),
				'HOMEPAGE'			=> $this->html->ToolTip($this->user->lang('homepage'),$this->html->toggleIcons($homepagelink,'browser.png','browser_off.png','images/glyphs/',$this->user->lang('homepage'),$homepagelink, false)),
				'MANUAL'			=> $this->html->ToolTip($this->user->lang('manual'),$this->html->toggleIcons($manuallink,'acroread.png','acroread_off.png','images/glyphs/',$this->user->lang('manual'),$manuallink, false)),
				'ACTION_LINK'		=> $link,
			));

			foreach($dep as $key => $depdata) {
				$tt = (isset($deptt[$key])) ? $deptt[$key] : $this->user->lang('plug_dep_'.$key);
				$this->tpl->assign_block_vars('plugins_row_'.$row.'.dep_row', array(
					'DEPENDENCY_STATUS' => $this->html->ToolTip($tt, $this->html->toggleIcons($depdata, 'status_green.gif','status_red.gif','images/glyphs/').' '.$this->user->lang('plug_dep_'.$key.'_short'))
				));
			}
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[1]) && is_array($arrExtensionList[1])){
			foreach ($arrExtensionList[1] as $id => $extension){
				if ($this->pm->search($extension['plugin']) || $extension['plugin'] == 'pluskernel') continue;
				$row = 'grey_repo';
				$dep['plusv']	= (version_compare($extension['dep_coreversion'], $this->config->get('plus_version'), '<='));
				$dep['games']	= 'skip';
				$dep['phpf']	= 'skip';
				$dl_link = ($blnRequirements) ? '<a href="javascript:repo_install(1, \''.sanitize($extension['plugin']).'\');" >'.$this->user->lang('backup_action_download').'</a>' : '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('backup_action_download').'</a>';
				$link = ($dep['plusv']) ? $dl_link : '';
				$this->tpl->assign_block_vars('plugins_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'CONTACT'			=> sanitize($extension['author']),
					'DESCRIPTION'		=> sanitize($extension['shortdesc']),
					'ACTION_LINK'		=> $link,
				));

				foreach($dep as $key => $depdata) {
					$tt = $this->user->lang('plug_dep_'.$key);

					$this->tpl->assign_block_vars('plugins_row_'.$row.'.dep_row', array(
						'DEPENDENCY_STATUS' => (($depdata === 'skip') ? '&nbsp;' : $this->html->ToolTip($tt, $this->html->toggleIcons($depdata, 'status_green.gif','status_red.gif','images/glyphs/').' '.$this->user->lang('plug_dep_'.$key.'_short'))),
					));
				}
			}
		}

		$badge = '';
		if ($arrUpdateCount[1]['red']){
			$badge = '<span class="update_available">'.$arrUpdateCount[1]['red'].'</span>';
		} elseif ($arrUpdateCount[1]['yellow']){
			$badge = '<span class="update_available_yellow">'.$arrUpdateCount[1]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'DEP_COUNT'	=> 3,
			'BADGE_1'	=> $badge,
		));

		//=================================================================
		//Templates

		$default_style = $this->config->get('default_style');
		$arrTemplates = $this->pdh->get('styles', 'styles');
		$arrLocalStyleUpdates = $this->objStyles->getLocalStyleUpdates();
		$arrUninstalledStyles = $this->objStyles->getUninstalledStyles();
		$arrStyles = array();

		foreach($arrUninstalledStyles as $key => $install_xml){
			$plugin_code = $key;
			if(isset($allUpdates[$plugin_code])){
				$row = 'yellow';
				$link = '<a href="javascript:repo_update(2, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
				if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
				$arrUpdateCount[2]['yellow'] ++;
			} else {
				$row = 'grey';
				$link = '<a href="manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=install&amp;code=' . $key. '&amp;link_hash='.$this->CSRFGetToken('mode').'">' . $this->user->lang('install') . '</a>';
			}
			
			$screenshot = '';
			if (file_exists($this->root_path.'templates/'.$plugin_code.'/screenshot.png' )){
				$screenshot = '<img src=\''.$this->root_path.'templates/'.$plugin_code.'/screenshot.png\' style=\'max-width:300px;\' alt="" />';
			} elseif(file_exists($this->root_path.'templates/'.$plugin_code.'/screenshot.jpg' )){
				$screenshot = '<img src=\''.$this->root_path.'templates/'.$plugin_code.'/screenshot.jpg\' style=\'max-width:300px;\' alt="" />';
			}

			$this->tpl->assign_block_vars('styles_row_'.$row, array(
				'NAME'			=> $this->html->ToolTip($screenshot, (($install_xml->name) ? $install_xml->name : stripslashes($key))),
				'VERSION'		=> $install_xml->version,
				'AUTHOR'		=> ($install_xml->authorEmail != "") ? '<a href="mailto:'.$install_xml->authorEmail.'">'.$install_xml->author.'</a>': $install_xml->author,
				'ACTION_LINK'	=> $link,
				'TEMPLATE'		=> $key,
			));
			$arrStyles[] = (($install_xml->name) ? $install_xml->name : stripslashes($key));
		}

		foreach($arrTemplates as $row){
			$screenshot = '';
			if (file_exists($this->root_path.'templates/'.$row['template_path'].'/screenshot.png' )){
				$screenshot = '<img src=\''.$this->root_path.'templates/'.$row['template_path'].'/screenshot.png\' style=\'max-width:300px;\' alt="" />';
			} elseif(file_exists($this->root_path.'templates/'.$row['template_path'].'/screenshot.jpg' )){
				$screenshot = '<img src=\''.$this->root_path.'templates/'.$row['template_path'].'/screenshot.jpg\' style=\'max-width:300px;\' alt="" />';
			}

			$plugin_code = $row['template_path'];
			if (isset($urgendUpdates[$plugin_code])){
				if (isset($arrLocalStyleUpdates[$plugin_code])){
					$rowname = 'red_local';
					$link = '<a href="manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=update&amp;code=' . $row['style_id']. '&amp;link_hash='.$this->CSRFGetToken('mode').'">'.$this->user->lang('uc_bttn_update').'</a>';
				} else {
					$rowname = 'red';
					$link = '<a href="javascript:repo_update(2, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
					if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
				}
				$arrUpdateCount[2]['red'] ++;
			} elseif(isset($allUpdates[$plugin_code])) {
				$rowname = 'yellow';
				$link = '<a href="javascript:repo_update(2, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
				if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
				$arrUpdateCount[2]['yellow'] ++;
			} else {
				$rowname = 'green';
				$link = ($row['style_id'] == $default_style) ? '' :'<a href="javascript:style_delete_warning('.$row['style_id'].');">' . $this->user->lang('uninstall') . '</a>';
			}

			$this->jquery->Dialog('style_preview', $this->user->lang('template_preview'), array('url'=>$this->root_path."viewnews.php".$this->SID."&style='+ styleid+'", 'width'=>'750', 'height'=>'520', 'modal'=>true, 'withid' => 'styleid'));

			$this->tpl->assign_block_vars('styles_row_'.$rowname, array(
				'ID'				=> $row['style_id'],
				'U_EDIT_STYLE'		=> 'manage_styles.php' . $this->SID . '&amp;edit=true&amp;styleid=' . $row['style_id'],
				'U_DOWNLOAD_STYLE'	=> 'manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=export&amp;code=' . $row['style_id'].'&amp;link_hash='.$this->CSRFGetToken('mode'),
				'ENABLE_ICON'		=> ($row['enabled'] == '1') ? 'green' : 'red',
				'ENABLE_ICON_INFO'	=> ($row['enabled'] == '1') ? $this->user->lang('style_enabled_info') : $this->user->lang('style_disabled_info'),
				'L_ENABLE'			=> ($row['enabled'] == '1') ? $this->user->lang('deactivate') : $this->user->lang('activate'),
				'ENABLE'			=> ($row['enabled'] == '1') ? 'disable' : 'enable',
				'U_ENABLE'			=> ($row['enabled'] == '1') ? 'manage_extensions.php' . $this->SID . '&amp;cat=2&amp;mode=disable&amp;code=' . $row['style_id'].'&amp;link_hash='.$this->CSRFGetToken('mode') : 'manage_extensions.php' . $this->SID . '&amp;mode=enable&amp;cat=2&amp;code=' . $row['style_id'].'&amp;link_hash='.$this->CSRFGetToken('mode'),
				'S_DEFAULT'			=> ($row['style_id'] == $default_style) ? true : false,
				'S_DEACTIVATED'		=> ($row['enabled'] != '1') ? true : false,
				'STANDARD'			=> ($row['style_id'] == $default_style) ? 'checked="checked"' : '',
				'VERSION'			=> $row['style_version'],
				'AUTHOR'			=> ($row['style_contact'] != "") ? '<a href="mailto:'.$row['style_contact'].'">'.$row['style_author'].'</a>': $row['style_author'],
				'NAME'				=> $this->html->ToolTip($screenshot, ((isset($arrExtensionListNamed[2][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[2][$plugin_code].')">'.$row['style_name'].'</a>' : $row['style_name'])),
				'TEMPLATE'			=> $row['template_path'],
				'USERS'				=> $row['users'],
				'ACTION_LINK'		=> $link,
			));
			
			$arrStyles[] = $plugin_code;
		}
		
		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[2]) && is_array($arrExtensionList[2])){
			foreach ($arrExtensionList[2] as $id => $extension){
				if (in_array($extension['plugin'], $arrStyles)) continue;
				$row = 'grey';

				$link = '<a href="javascript:repo_install(2, \''.sanitize($extension['plugin']).'\');" >'.$this->user->lang('backup_action_download').'</a>';
				if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('backup_action_download').'</a>';
				$this->tpl->assign_block_vars('styles_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'AUTHOR'			=> sanitize($extension['author']),
					'TEMPLATE'			=> sanitize($extension['plugin']),
					'DESCRIPTION'		=> sanitize($extension['shortdesc']),
					'ACTION_LINK'		=> $link,
				));

			}
		}
		
		
		$this->jquery->dialog('style_default_info', $this->user->lang('default_style'), array('message' => $this->user->lang('style_default_info').'<br /><br /><label><input type="radio" name="override" value="0" onchange="change_override(1);">'.$this->user->lang('yes').'</label>  <label><input type="radio" name="override" value="1" checked="checked" onchange="change_override(0);">'.$this->user->lang('no').'</label>', 'custom_js' => 'submit_form();', 'height' => 200), 'confirm');
		$this->jquery->dialog('style_reset_warning', $this->user->lang('reset_style'), array('message' => $this->user->lang('style_confirm_reset'), 'height' => 200, 'url' => $this->root_path.'admin/manage_extensions.php' . $this->SID . '&link_hash='.$this->CSRFGetToken('mode')."&cat=2&mode=reset&code='+ styleid+'", 'withid' => 'styleid'), 'confirm');
		$this->jquery->dialog('style_delete_warning', $this->user->lang('delete_style'), array('message' => $this->user->lang('confirm_delete_style'), 'height' => 200, 'url'=> $this->root_path.'admin/manage_extensions.php' . $this->SID . '&link_hash='.$this->CSRFGetToken('mode')."&cat=2&mode=uninstall&code='+ styleid+'", 'withid' => 'styleid'), 'confirm');

		$badge = '';
		if ($arrUpdateCount[2]['red']){
			$badge = '<span class="update_available">'.$arrUpdateCount[2]['red'].'</span>';
		} elseif ($arrUpdateCount[2]['yellow']){
			$badge = '<span class="update_available_yellow">'.$arrUpdateCount[2]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_2'	=> $badge,
		));

		//=================================================================
		//Portal Modules
		
		$arrTmpModules = array();
		
		if (isset($arrExtensionList[3]) && is_array($arrExtensionList[3])){
			foreach ($arrExtensionList[3] as $id => $extension){
				$arrTmpModules[$extension['plugin']] = $extension;
			}
		}

		$arrModules = $this->pdh->aget('portal', 'portal', 0, array($this->pdh->get('portal', 'id_list')));
		if (is_array($arrModules)){
			foreach($arrModules as $id => $value){
				$row = 'green';
				$link = '';
				$plugin_code = $value['path'];
				//Ignore Plugin Moduls in terms of repo-updates
				if (empty($value['plugin'])) {
					if (isset($urgendUpdates[$plugin_code])){
						$row = 'red';
						$link = '<a href="javascript:repo_update(3, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
						if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
						$arrUpdateCount[3]['red'] ++;
					}elseif(isset($allUpdates[$plugin_code])){
						$row = 'yellow';
						$link = '<a href="javascript:repo_update(3, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
						if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
						$arrUpdateCount[3]['yellow'] ++;
					}
				}
				//Add Reinstall Link if no update available
				if($row == 'green') {
					$link = '<img src="'.$this->root_path.'images/global/update.png" alt="'.$this->user->lang('reinstall').'" title="'.$this->user->lang('reinstall').'" onclick="javascript:reinstall_portal(\''.$plugin_code.'\')" style="cursor:pointer;" />';
				}

				$this->tpl->assign_block_vars('pm_row_'.$row, array(
					'NAME'				=> (isset($arrExtensionListNamed[3][$value['path']])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[3][$value['path']].')">'.$value['name'].'</a>' : $value['name'],
					'VERSION'			=> sanitize($value['version']),
					'CODE'				=> sanitize($value['path']),
					'CONTACT'			=> sanitize($value['autor']),
					'ACTION_LINK'		=> $link,
					'DESCRIPTION'		=> (isset($arrTmpModules[$value['path']])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[3][$value['path']].')">'.sanitize(cut_text($arrTmpModules[$value['path']]['shortdesc'], 100)).'</a>' : '',

				));
			}
			$this->confirm_delete($this->user->lang('portal_reinstall_warn'), 'manage_extensions.php'.$this->SID.'&cat=3', true, array('function' => 'reinstall_portal', 'handler' => 'mode'));
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[3]) && is_array($arrExtensionList[3])){
			foreach ($arrExtensionList[3] as $id => $extension){

				if ((is_array(search_in_array($extension['plugin'], $arrModules, true, 'path')))) continue;
				$row = 'grey';

				$link = '<a href="javascript:repo_install(3, \''.sanitize($extension['plugin']).'\');" >'.$this->user->lang('backup_action_download').'</a>';
				if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('backup_action_download').'</a>';
				$this->tpl->assign_block_vars('pm_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'CONTACT'			=> sanitize($extension['author']),
					'DESCRIPTION'		=> '<a href="javascript:repoinfo('.$id.')">'.sanitize(cut_text($extension['shortdesc'])).'</a>',
					'ACTION_LINK'		=> $link,
					'RATING'			=> $this->jquery->StarRating('extension_'.md5($extension['plugin']), array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5), '', $extension['rating'], true),
				));

			}
		}

		$badge = '';

		if ($arrUpdateCount[3]['red']){
			$badge = '<span class="update_available">'.count($arrUpdateCount[3]['red']).'</span>';
		} elseif ($arrUpdateCount[3]['yellow']){
			$badge = '<span class="update_available_yellow">'.count($arrUpdateCount[3]['yellow']).'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_3'	=> $badge,
		));

		//=================================================================
		//Games
		$arrGames = $this->game->get_games();
		$arrGameVersions = $this->game->get_versions();
		$arrTmpExtension = array();
		
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
						$link = '<a href="javascript:repo_update(7, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
						if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
						$arrUpdateCount[7]['red'] ++;
				}elseif(isset($allUpdates[$plugin_code])){
					$row = 'yellow';
					$link = '<a href="javascript:repo_update(7, \''.$plugin_code.'\');">'.$this->user->lang('uc_bttn_update').'</a>';
					if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('uc_bttn_update').'</a>';
					$arrUpdateCount[7]['yellow'] ++;
				} else {
						$row = 'green';
						$link = '';
				}

				$this->tpl->assign_block_vars('games_row_'.$row, array(
					'NAME'				=> (isset($arrExtensionListNamed[7][$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[7][$plugin_code].')">'.$this->game->game_name($plugin_code).'</a>' : $this->game->game_name($plugin_code),
					'VERSION'			=> $arrGameVersions[$plugin_code],
					'CODE'				=> sanitize($plugin_code),
					'CONTACT'			=> (isset($arrTmpExtension[$plugin_code])) ? $arrTmpExtension[$plugin_code]['author'] : '',
					'DESCRIPTION'		=> (isset($arrTmpExtension[$plugin_code])) ? '<a href="javascript:repoinfo('.$arrExtensionListNamed[7][$plugin_code].')">'.cut_text($arrTmpExtension[$plugin_code]['shortdesc'], 100).'</a>' : '',
					'RATING'			=> (isset($arrTmpExtension[$plugin_code])) ? $this->jquery->StarRating('extension_'.md5($arrTmpExtension[$plugin_code]['plugin']), array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5), '', $arrTmpExtension[$plugin_code]['rating'], true) : '',
					'ACTION_LINK'		=> $link,
				));
			}
		}

		//Now bring the Extensions from the REPO to template
		if (isset($arrExtensionList[7]) && is_array($arrExtensionList[7])){
			foreach ($arrExtensionList[7] as $id => $extension){
				if (in_array($extension['plugin'], $arrGames)) continue;
				$row = 'grey';

				$link = '<a href="javascript:repo_install(7, \''.sanitize($extension['plugin']).'\');" >'.$this->user->lang('backup_action_download').'</a>';
				if (!$blnRequirements) $link = '<a href="'.EQDKP_DOWNLOADS_URL.'" target="_blank">'.$this->user->lang('backup_action_download').'</a>';
				$this->tpl->assign_block_vars('games_row_'.$row, array(
					'NAME'				=> '<a href="javascript:repoinfo('.$id.')">'.$extension['name'].'</a>',
					'VERSION'			=> sanitize($extension['version']),
					'CODE'				=> sanitize($extension['plugin']),
					'CONTACT'			=> sanitize($extension['author']),
					'DESCRIPTION'		=> sanitize(cut_text($extension['shortdesc'], 100)),
					'ACTION_LINK'		=> $link,
					'RATING'			=> $this->jquery->StarRating('extension_'.md5($extension['plugin']), array(1=>1, 2=>2, 3=>3, 4=>4, 5=>5), '', $extension['rating'], true),
				));

			}
		}

		$badge = '';
		if ($arrUpdateCount[7]['red']){
			$badge = '<span class="update_available">'.$arrUpdateCount[7]['red'].'</span>';
		} elseif ($arrUpdateCount[7]['yellow']){
			$badge = '<span class="update_available_yellow">'.$arrUpdateCount[7]['yellow'].'</span>';
		}

		$this->tpl->assign_vars(array(
			'BADGE_7'	=> $badge,
		));

		//=================================================================
		//Common Output


		//Tabs
		$this->jquery->Tab_header('plus_plugins_tab', true);
		if ($this->in->exists('tab')){
			$this->jquery->Tab_Select('plus_plugins_tab', $this->in->get('tab',0));
		}
		
		$this->jquery->Dialog('update_confirm', '', array('custom_js'	=> 'repo_update_start(cat, extensioncode);', 'message'	=> $this->user->lang('repo_updatewarning').'<br /><br /><input type="checkbox" onclick="hide_update_warning(this.checked);" value="1" />'.$this->user->lang('repo_hide_updatewarning'), 'withid'	=> 'cat, extensioncode', 'width'=> 300, 'height'=>300), 'confirm');
		
		$this->jquery->Dialog('repoinfo', $this->user->lang('repo_extensioninfo'), array('url'=>$this->root_path."admin/manage_extensions.php".$this->SID."&info='+moduleid+'", 'width'=>'700', 'height'=>'600', 'withid'=>'moduleid'));

		foreach ($this->repo->DisplayCategories() as $key=>$category){
			$this->tpl->assign_vars(array(
				'L_CATEGORY_'.$key	=> $category,
			));
		}
		
		$this->tpl->assign_vars(array(
			'S_HIDE_UPDATEWARNING'	=> (int)$this->config->get('repo_hideupdatewarning'),
			'S_REQUIREMENTS' => $blnRequirements,
			'CSRF_MODE_TOKEN' => $this->CSRFGetToken('mode'),
			'CSRF_UPDATEWARNING_TOKEN' => $this->CSRFGetToken('hide_update_warning'),
		));

		$this->tpl->add_css('
			.ui-progressbar { position:relative; height:30px;}
			.nl_progressbar_label { position: absolute; width: 90%; text-align: center; line-height: 30px; left:5%; right:5%;}
		');

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('extensions'),
			'template_file'		=> 'admin/manage_extensions.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Extensions', Manage_Extensions::__shortcuts());
registry::register('Manage_Extensions');
?>