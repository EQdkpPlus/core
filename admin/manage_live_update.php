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

class Manage_Live_Update extends page_generic {
	public static $shortcuts = array('repo' => 'repository');

	public function __construct(){
		$this->user->check_auth('a_maintenance');

		$handler = array(
			'refresh'=> array('process' => 'process_refresh'),
			'show' 	=> array('process' => 'handle_steps'),
			'step'	=> array('process' => 'handle_ajax_steps', 'csrf'=>true),
			'diff'	=> array('process' => 'show_diff'),
		);
		parent::__construct(false, $handler, array(), null, '');

		$this->repo->InitLifeupdate($this->getNewVersion());

		$this->steps = array(
			0	=> array('show'	=> true,
						'function'	=> 'process_show_step0',
						'label'	=> 'EQdkp Plus '.$this->user->lang('liveupdate'),
						),
			1	=> array('function'	=> 'process_step1', 'label'	=> $this->user->lang('liveupdate_step1')),
			2	=> array('function'	=> 'process_step2', 'label'	=> $this->user->lang('liveupdate_step2')),
			3	=> array('function'	=> 'process_step3', 'label'	=> $this->user->lang('liveupdate_step3')),
			4	=> array('function'	=> 'process_step4', 'label'	=> $this->user->lang('liveupdate_step4')),
			5	=> array('show'	=> true, 'function'	=> 'process_show_step5', 'label'	=> $this->user->lang('liveupdate_step5')),
			6	=> array('function'	=> 'process_step6', 'label'	=> $this->user->lang('liveupdate_step6')),
			7	=> array('function'	=> 'process_step7', 'label'	=> $this->user->lang('liveupdate_step7')),
			8	=> array('function'	=> 'process_step8', 'label'	=> $this->user->lang('liveupdate_step8')),
			9	=> array('show'	=> true, 'function'	=> 'process_show_step9', 'label'	=> $this->user->lang('liveupdate_step9')),
			10	=> array('function'	=> 'process_step10', 'label'	=> $this->user->lang('liveupdate_step10')),
			11	=> array('function'	=> 'process_step11', 'label'	=> $this->user->lang('liveupdate_step11')),
			//12	=> array('function'	=> 'process_step12', 'label'	=> $this->user->lang('liveupdate_step12')),
		);

		$this->process();
	}

	public function handle_steps(){
		@set_time_limit(0);

		$show = (int)$this->in->get('show', 0);
		if (isset($this->steps[$show]) && $this->steps[$show]['show'] == true){
			$function = $this->steps[$show]['function'];
			$this->$function();
		}

		$this->tpl->assign_vars(array(
			'S_SHOW'	=> true,
			'S_SHOW_'.strtoupper($show)	=> true,
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('liveupdate'),
			'template_file'		=> 'admin/manage_live_update.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('liveupdate'), 'url'=>' '],
			],
			'display'			=> true
		]);

	}

	private function bring_steps_to_template($start = 0, $blnStartOnload = false, $showFrom = false){
		$last_step = max(array_keys($this->steps));
		$showFrom  = ($showFrom ) ? $showFrom : $start;
		foreach ($this->steps as $id	=> $value){
			if ($id < $showFrom) continue;
			$this->tpl->add_js(
				"function lu_step".$id."(chunk){
					if(chunk === undefined){
						set_progress_bar_value(".((($id-1) < 0) ? 0 : $id-1).", '".$this->jquery->sanitize($value['label'])."...');
						chunkval = '';
					} else {
						chunkval = '&chunk='+chunk;
					}

					$.get('manage_live_update.php".$this->SID."&step=".$id."&link_hash=".$this->CSRFGetToken('step')."'+chunkval, function(data) {
					  if ($.trim(data) == 'true'){
						".(($id == $last_step) ? "set_progress_bar_value(".$last_step.", '".$this->jquery->sanitize($this->user->lang('liveupdate_step_end'))."'); window.location='manage_live_update.php".$this->SID."&finished=true';" : ((isset($this->steps[$id+1]['show']) && $this->steps[$id+1]['show'] == true) ? 'window.location.href="manage_live_update.php'.$this->SID.'&show='.($id+1).'"' : 'lu_step'.($id+1).'();'))."
					  }else {
						var mydata = $.trim(data);
						if(mydata.substr(0, 7) == 'chunked'){
							var mypars = mydata.split(':');
							lu_step".$id."(mypars[1]);
						} else {
							update_error(data);
						}
					  }
					});
				}"
			);
		}

		$this->tpl->add_js('
			var totalSteps = '.$last_step.';
			$("#nl_progressbar").progressbar({
				value: 0
			});

			function set_progress_bar_value(recentNumber, labeltext){
				percent = Math.round((recentNumber / totalSteps) * 100);
				$("#nl_progressbar").progressbar("destroy");

				$("#nl_progressbar").progressbar({
					value: percent
				});

				$("#nl_progressbar_label").html(labeltext);
			}

			function update_error(data){
				$("#lu_error").show();
				$("#lu_error_label").html(\'<b>'.$this->jquery->sanitize($this->user->lang('liveupdate_step_error')).'</b>\' + data);
				$(".lu_loading_indicator").hide();
				$(".lu_dontclose_info").hide();
			}
		');

		if ($blnStartOnload){
			$this->tpl->add_js('
			lu_step'.$start.'();
			', 'docready');

			$this->tpl->assign_vars(array(
				'S_STEP'	=> true,
				'S_SHOW'	=> false,
			));

			$this->core->set_vars([
				'page_title'		=> $this->user->lang('liveupdate'),
				'template_file'		=> 'admin/manage_live_update.html',
				'page_path'			=> [
					['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
					['title'=>$this->user->lang('liveupdate'), 'url'=>' '],
				],
				'display'			=> true
			]);
		} else {
			$this->tpl->add_js('
			set_progress_bar_value('.($start-1).', "'.$this->steps[$start]['label'].'...");
			', 'docready');
		}


	}

	public function handle_ajax_steps(){
		$step = (int)$this->in->get('step', 0);
		if (isset($this->steps[$step]) && !isset($this->steps[$step]['show'])){
			$function = $this->steps[$step]['function'];
			$this->$function();
		}
		exit;
	}

	public function process_show_step0(){
		$this->bring_steps_to_template(1, true);
	}

	//Get Download_Link
	public function process_step1(){
		$downloadLink = $this->repo->getCoreUpdateDownloadLink();
		if($downloadLink && isset($downloadLink['info']) && $downloadLink['info'] == 'new core available'){
			//Reset Repo, fetch again, and then bring a message to the user
			$this->repo->CheckforPackages(true);
			echo  $this->user->lang('liveupdate_step1_error_new_core');
		} elseif($downloadLink && $downloadLink['status'] == 1){
			$this->config->set('download_link', $this->encrypt->encrypt($downloadLink['link']), 'live_update');
			$this->config->set('download_hash', $this->encrypt->encrypt($downloadLink['hash']), 'live_update');
			$this->config->set('download_signature', $this->encrypt->encrypt($downloadLink['signature']), 'live_update');
			//Set release note to global namespace to prevent deletion
			$this->config->set('release_note', $downloadLink['note']);
			$new_version = str_replace('.', '', $this->getNewVersion());
			$this->config->set('download_newversion', $this->encrypt->encrypt($new_version), 'live_update');
			echo "true";
		} else {
			echo $this->user->lang('liveupdate_step1_error_'.$downloadLink['error']);
		}

		exit;
	}

	//Download Package
	public function process_step2(){

		$downloadLink = $this->encrypt->decrypt($this->config->get('download_link', 'live_update'));
		$new_version = str_replace('.', '', $this->getNewVersion());

		$destFolder = $this->pfh->FolderPath('update_to_'.$new_version,'live_update');
		$filename = 'lu_'.$new_version.'.zip';
		$this->pfh->secure_folder('','live_update');
		$this->repo->downloadPackage($downloadLink, $destFolder, $filename);

		if ($this->repo->verifyPackage($destFolder.$filename, $this->encrypt->decrypt($this->config->get('download_hash', 'live_update')), $this->encrypt->decrypt($this->config->get('download_signature', 'live_update')))){
			echo "true";
		} else {
			echo $this->user->lang('liveupdate_step2_error');
		}

		exit;
	}

	//Unpack Package
	public function process_step3(){
		$new_version = str_replace('.', '', $this->getNewVersion());

		$destFolder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$srcFolder = $this->pfh->FolderPath('update_to_'.$new_version,'live_update');
		$filename = 'lu_'.$new_version.'.zip';

		$archive = registry::register('zip', array($srcFolder.$filename));

		$intChunkSize = 100;
		$intFiles = $archive->getFileNumber();

		$intCurrentChunk = $this->in->get('chunk', 0);

		$from = $intCurrentChunk*$intChunkSize;
		$to = $from+$intChunkSize;

		if($from > $intFiles) {
			//Finished
			echo "true";
			exit;
		}

		if($to > $intFiles) $to = $intFiles;

		$my_extract = $archive->extract($destFolder, false, $from, $to);

		if(!$my_extract) {
			$this->pfh->Delete($srcFolder.$filename);
			echo $this->user->lang('liveupdate_step3_error');
			exit;
		}

		echo "chunked:".($intCurrentChunk+1).":".ceil($intFiles/$intChunkSize).":".$intFiles;
		exit;
	}

	//Compare Files
	public function process_step4(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$xmlfile = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update').'package.xml';

		$arrChanged = array();
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile, 'changed');
		if($arrFiles && count($arrFiles) > 0){
			foreach ($arrFiles as $file){
				if (is_file($this->root_path.$file['name']) && md5_file($this->root_path.$file['name']) != $file['md5_old'] && md5_file($this->root_path.$file['name']) != $file['md5']){
					$arrChanged[] = $file['name'];
				}
			}
		}

		if (count($arrChanged) > 0){

			$this->config->set('conflicted_files', $this->encrypt->encrypt(serialize($arrChanged)), 'live_update');
		}

		echo "true";
		exit;
	}

	private function download_conflicted_files(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$zipfile = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update').'conflicted_files.zip';
		$archive = registry::register('zip', array($zipfile));



		//Conflicted Files
		$arrConflictedFiles = unserialize($this->encrypt->decrypt($this->config->get('conflicted_files', 'live_update')));
		foreach ($arrConflictedFiles as $file){
			$arrFiles[] = $this->root_path.$file;
		}

		$archive->add($arrFiles, $this->root_path);
		$archive->create();

		if (file_exists($zipfile)){
			header('Content-Type: application/octet-stream');
			header('Content-Length: '.$this->pfh->FileSize($zipfile));
			header('Content-Disposition: attachment; filename="'.sanitize('conflicted_files_'.$new_version.'.zip').'"');
			header('Content-Transfer-Encoding: binary');
			readfile($zipfile);
			exit;
		}

	}

	//Show conflicted/deleted Files - Step 5
	public function process_show_step5(){
		if ($this->in->exists('submit')){
			$this->bring_steps_to_template(6, true);
			return;
		} elseif ($this->in->exists('download')){
			$this->download_conflicted_files();
			return;
		}


		$stop = false;

		//Conflicted Files
		$arrConflictedFiles = unserialize($this->encrypt->decrypt($this->config->get('conflicted_files', 'live_update')));
		if ($arrConflictedFiles && is_array($arrConflictedFiles) && count($arrConflictedFiles) > 0){
			$stop = true;

			$this->jquery->Dialog('liveupdate_diff', $this->user->lang('liveupdate_show_differences'), array('url'=>$this->root_path.'admin/manage_live_update.php'.$this->SID.'&diff=\'+file+\'', 'withid'=>'file', 'height'=> '700', 'width'=>'900'));

			$this->tpl->assign_vars(array(
				'S_CONFLICTED_FILES'	=> true,
			));

			foreach($arrConflictedFiles as $file){
				$this->tpl->assign_block_vars('conflicted_row', array(
					'FILENAME'	=> sanitize($file),
					'ENCODED_FILENAME' => base64_encode($file),
				));
			}
		}

		//Removed Files
		$new_version = str_replace('.', '', $this->getNewVersion());
		$xmlfile = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update').'package.xml';
		$arrRemovedFiles = $this->repo->getFilelistFromPackageFile($xmlfile, 'removed');
		if ($arrRemovedFiles && is_array($arrRemovedFiles) && count($arrRemovedFiles) > 0){
			$stop = true;
			$this->tpl->assign_vars(array(
				'S_REMOVED_FILES'	=> true,
			));
			foreach($arrRemovedFiles as $file){
				$this->tpl->assign_block_vars('removed_row', array(
					'FILENAME'	=> $file['name'],
					'ENCODED_FILENAME' => base64_encode($file['name']),
				));
			}
		}

		$this->jquery->Dialog('confirm_conflicted', '', array('url' => 'manage_live_update.php'.$this->SID.'&show=5&submit', 'message'=>$this->user->lang('liveupdate_conflicted_confirm')), 'confirm');

		if ($stop) {
			$this->bring_steps_to_template(5, false);
		} else {
			//Nothing to display, let's continue with next step
			$this->bring_steps_to_template(6, true);
		}

	}

	//Backup files that will be replaced
	public function process_step6(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$zipfile = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update').'backup_changed_files.zip';
		$archive = registry::register('zip', array($zipfile));
		$xmlfile = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update').'package.xml';
		$arrChangedFiles = $this->repo->getFilelistFromPackageFile($xmlfile, 'changed');
		foreach($arrChangedFiles as $file){
			$arrFiles[] = $this->root_path.$file['name'];
		}

		 $archive->add($arrFiles, $this->root_path);
		 $blnResult = $archive->create();
		if (!$blnResult){
			echo $this->user->lang('liveupdate_step6_error');
		} else {
			echo "true";
		}

		exit;
	}

	//Copy new files
	public function process_step7(){
		@set_time_limit(0);

		$new_version = str_replace('.', '', $this->getNewVersion());
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$xmlfile = $tmp_folder.'package.xml';
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile);
		$strLog = '';
		foreach($arrFiles as $file){
			if (file_exists($this->root_path.$file['name'])){
				$strLog .= 'Replaced File '.$file['name']."\r\n";
			} else {
				$strLog .= 'Added File '.$file['name']."\r\n";
			}
			$this->pfh->copy($tmp_folder.$file['name'],$this->root_path.$file['name']);
		}
		//Reset Opcache, for PHP7
		if(function_exists('opcache_reset')){
			opcache_reset();
		}
		if ($strLog != "") register('logs')->add('liveupdate_copied_files', array('Copied Files' => $strLog), '', $new_version);
		echo "true";
		exit;
	}

	//Check if all new files have been copied to the right place
	public function process_step8(){

		$new_version = str_replace('.', '', $this->getNewVersion());
		$strLogFile = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update').'/missing.log';

		$new_version = $this->encrypt->decrypt($this->config->get('download_newversion', 'live_update'));

		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$xmlfile = $tmp_folder.'package.xml';
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile);

		$arrMissingFiles = array();

		$this->pfh->putContent($strLogFile, "");

		foreach($arrFiles as $file){
			if (($file['type'] == 'changed' || $file['type'] == 'new') && md5_file($this->root_path.$file['name']) != $file['md5']){
				$arrMissingFiles[] = $file['name'];
			}
		}

		$this->pfh->putContent($strLogFile, $this->encrypt->encrypt(serialize($arrMissingFiles)));

		echo "true";
		exit;
	}

	private function download_missing_files(){
		$new_version = $this->encrypt->decrypt($this->config->get('download_newversion', 'live_update'));
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version,'live_update');
		$zipfile = $tmp_folder.'missing_files.zip';
		$archive = registry::register('zip', array($zipfile));

		//Missing Files
		$new_version = str_replace('.', '', $this->getNewVersion());
		$strLogFile = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update').'/missing.log';

		$arrFiles = array();
		$arrConflictedFiles = unserialize($this->encrypt->decrypt(file_get_contents($strLogFile)));
		foreach ($arrConflictedFiles as $file){
			if (file_exists($tmp_folder.'tmp/'.$file)) {
				$arrFiles[] = $tmp_folder.'tmp/'.$file;
			}
		}
		$archive->add($arrFiles, $tmp_folder.'tmp/');
		$archive->create();

		if (file_exists($zipfile)){
			header('Content-Type: application/octet-stream');
			header('Content-Length: '.$this->pfh->FileSize($zipfile));
			header('Content-Disposition: attachment; filename="'.sanitize('missing_files_'.$new_version.'.zip').'"');
			header('Content-Transfer-Encoding: binary');
			readfile($zipfile);
			exit;
		}

	}

	//Continue with steps
	public function process_show_step9(){
		if ($this->in->exists('download')){
			$this->download_missing_files();
			return;
		} elseif($this->in->exists('continue')){
			$this->bring_steps_to_template(10, true);
			return;
		}

		$arrMissingFiles = unserialize($this->encrypt->decrypt($this->config->get('missing_files', 'live_update')));
		if ($arrMissingFiles && count($arrMissingFiles) > 0){
			$this->bring_steps_to_template(9, false, 8);
			$intMyCookie = (int)$this->in->getEQdkpCookie('lu_step9', 0);
			$intMyCookie = $intMyCookie+1;
			set_cookie('lu_step9', $intMyCookie, time()+3600);

			foreach ($arrMissingFiles as $file){
				$this->tpl->assign_block_vars('missing_row', array(
					'FILENAME'	=> $file,
				));
			}

			if($intMyCookie > 4){
				$this->jquery->Dialog('confirm_conflicted', '', array('url' => 'manage_live_update.php'.$this->SID.'&show=9&continue', 'message'=>$this->user->lang('liveupdate_skip_confirm')), 'confirm');

				$this->tpl->assign_vars(array(
					'S_SHOW_CONTINUE_BTN' => true,
				));
			}

		} else {
			//Nothing to display, let's continue
			$this->bring_steps_to_template(10, true);
		}

	}

	//Delete removed files
	public function process_step10(){
		$new_version = $this->encrypt->decrypt($this->config->get('download_newversion', 'live_update'));
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$xmlfile = $tmp_folder.'package.xml';
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile, 'removed');
		if (is_array($arrFiles)){
			$strLog = '';
			foreach ($arrFiles as $file){
				if (file_exists($this->root_path.$file['name'])){
					$this->pfh->Delete($this->root_path.$file['name']);
					$strLog .= 'Deleted File '.$file['name']."\r\n";
				}
			}

			if ($strLog != "") register('logs')->add('liveupdate_deleted_files', $strLog);
		}

		echo "true";
		exit;
	}

	//Delete Installationfiles
	public function process_step11(){
		$new_version = $this->encrypt->decrypt($this->config->get('download_newversion', 'live_update'));
		$folder = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update');
		$this->pfh->Delete('update_to_'.$new_version, 'live_update');
		$this->config->del('live_update');

		//Delete Cookie
		set_cookie('lu_step9', 0, time()-3600);

		//Reset Opcache, for PHP7
		if(function_exists('opcache_reset')){
			opcache_reset();
		}

		//Reset Repository
		$this->pdh->put('repository', 'reset', array());
		$this->pdh->process_hook_queue();

		echo "true";
		exit;
	}

	//Update Version-Number
	/*
	public function process_step12(){
		$this->config->set('plus_version', $this->getNewVersion());
		echo "true";
		exit;
	}
	*/

	public function process_refresh(){
		$this->repo->CheckforPackages(true);
		redirect('admin/manage_live_update.php'.$this->SID);
	}

	private function getNewVersion($returnData = false){
		$updates = $this->repo->UpdatesAvailable(true);
		if ($updates){
			if ($returnData) return $this->repo->updates['pluskernel'];
			return $this->repo->updates['pluskernel']['version_int'];
		}

		return false;
	}

	public function display(){
		$updates = NULL;

		if($this->in->get('finished') == 'true'){
			$blnReleaseNote = ($this->config->get('release_note') && strlen($this->config->get('release_note'))) ? true : false;

			if(!$blnReleaseNote && registry::register('config')->get('pk_maintenance_mode')){
				redirect('maintenance/index.php'.$this->SID, false, false, false);
			}
			$this->tpl->assign_vars(array(
					'S_FINISHED' 	=> true,
					'S_RELEASE_NOTE' => $blnReleaseNote,
					'STR_RELEASE_NOTE' => str_replace('\r\n', "<br/>", nl2br($this->bbcode->toHTML($this->config->get('release_note')))),
			));

			$this->config->del('release_note');
		}

		if ($this->getNewVersion()){
			$updates = $this->getNewVersion(true);
			$this->tpl->assign_vars(array(
				'S_NEW_VERSION'	=> true,
				'NEW_VERSION'	=> $updates['version'],
				'CHANGELOG'		=> nl2br($updates['changelog']),
				'RELEASE_DATE'	=> $updates['release'],
				'S_NO_UPDATE_PACKAGE' => ($updates['dep_php'] == '9.9') ? true : false,
			));
		}

		//Check some Requirements for LiveUpdate itself
		$blnRequirements = true;
		$strRequirementsNote = '<br />';
		if(!class_exists("ZipArchive")) {
			$blnRequirements = false;
			$strRequirementsNote .= ' - ZipArchive-Class<br/>';
		}

		//Check new Core Requirements
		$mixResult = $this->repo->checkRequirementsForNewCore($updates['bugtracker_url'], $updates);
		if($mixResult !== true && is_array($mixResult)){
			$blnRequirements = false;
			foreach($mixResult as $val){
				$strRequirementsNote .= ' - '.$val.'<br />';
			}
		}


		$this->tpl->assign_vars(array(
			'S_START'			=> true,
			'S_RELEASE_CHANNEL' => ($this->repo->getChannel() != 'stable') ? true : false,
			//'S_UPDATE_BUTTON'	=> ($this->repo->getChannel() != 'stable' || DEBUG > 1),
			'RECENT_VERSION' 	=> VERSION_EXT,
			'RELEASE_CHANNEL' 	=> ucfirst($this->repo->getChannel()),
			'S_REQUIREMENTS'	=> $blnRequirements,
			'REQUIREMENTS_NOTE'	=> $strRequirementsNote,
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('liveupdate'),
			'template_file'		=> 'admin/manage_live_update.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('liveupdate'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function show_diff(){
		$strFilename = base64_decode($this->in->get('diff', ''));
		if (!$strFilename) return;
		$arrRenderer = array('side_by_side'=>'side_by_side', 'inline'=>'inline', 'unified'=>'unified', 'raw'=>'raw');
		$content = '';
		$blnRenderer = true;

		switch($this->in->get('type')){
			//Show the given file
			case 'showfile': {
				$old_file = file_get_contents($this->root_path.$strFilename);
				$content = '<div class="showfile">'.nl2br(htmlspecialchars($old_file)).'</div>';
				$blnRenderer = false;
			}
			break;

			//Default: show diff
			default: {
				$strRenderer = $this->in->get('renderer', 'side_by_side');

				if ($strFilename){

					require_once($this->root_path.'libraries/diff/diff.php');
					require_once($this->root_path.'libraries/diff/engine.php');
					require_once($this->root_path.'libraries/diff/renderer.php');

					$this->tpl->css_file($this->root_path.'libraries/diff/diff.css');

					$old_file = file_get_contents($this->root_path.$strFilename);
					$new_version = str_replace('.', '', $this->getNewVersion());
					$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
					$new_file = file_get_contents($tmp_folder.$strFilename);

					$diff = new diff($old_file, $new_file, true);
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
			'RENDERER_DROPDOWN' => (new hdropdown('renderer', array('options' => $arrRenderer, 'value' => $this->in->get('renderer', 'side_by_side'), 'js' => 'onchange="this.form.submit();"', 'tolang' => true)))->output(),
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
}
registry::register('Manage_Live_Update');
