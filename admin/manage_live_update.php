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

class Manage_Live_Update extends page_generic {
		public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'jquery', 'core', 'config', 'pfh', 'repo' => 'repository', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_maintenance');
		$this->user->check_hostmode();
		$handler = array(
			'show' 	=> array('process' => 'handle_steps'),
			'step'	=> array('process' => 'handle_ajax_steps', 'csrf'=>true),
			'refresh'=> array('process' => 'process_refresh'),
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
		$show = (int)$this->in->get('show', 0);
		if (isset($this->steps[$show]) && $this->steps[$show]['show'] == true){
			$function = $this->steps[$show]['function'];
			$this->$function();
		}

		$this->tpl->assign_vars(array(
			'S_SHOW'	=> true,
			'S_SHOW_'.strtoupper($show)	=> true,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('liveupdate'),
			'template_file'		=> 'admin/manage_live_update.html',
			'display'			=> true)
		);

	}

	private function bring_steps_to_template($start = 0, $blnStartOnload = false, $showFrom = false){
		$last_step = max(array_keys($this->steps));
		$showFrom  = ($showFrom ) ? $showFrom : $start;
		foreach ($this->steps as $id	=> $value){
			if ($id < $showFrom) continue;
			$this->tpl->add_js(
				"function lu_step".$id."(){
					set_progress_bar_value(".((($id-1) < 0) ? 0 : $id-1).", '".$value['label']."...');

					$.get('manage_live_update.php".$this->SID."&step=".$id."&link_hash=".$this->CSRFGetToken('step')."', function(data) {
					  if ($.trim(data) == 'true'){
						//alert('Step".$id.' '.$value['label']." beendet');
						".(($id == $last_step) ? "set_progress_bar_value(".$last_step.", '".$this->user->lang('liveupdate_step_end')."'); window.location='manage_live_update.php".$this->SID."';" : ((isset($this->steps[$id+1]['show']) && $this->steps[$id+1]['show'] == true) ? 'window.location.href="manage_live_update.php'.$this->SID.'&show='.($id+1).'"' : 'lu_step'.($id+1).'();'))."
					  }else {
						update_error(data);
					  }
					});
				}"
			);
		}

		$this->tpl->add_js('
			var totalSteps = '.$last_step.';

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
				$("#lu_error_label").html("<b>'.$this->user->lang('liveupdate_step_error').'</b>" + data);
				$("#lu_loading_img").hide();
				$("#lu_dontclose").hide();
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

			$this->core->set_vars(array(
				'page_title'		=> $this->user->lang('liveupdate'),
				'template_file'		=> 'admin/manage_live_update.html',
				'display'			=> true)
			);
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

		$encrypt = register('encrypt');
		if ($downloadLink && strlen($downloadLink['link'])){
			$this->config->set('download_link', $encrypt->encrypt($downloadLink['link']), 'live_update');
			$this->config->set('download_hash', $encrypt->encrypt($downloadLink['hash']), 'live_update');
			$this->config->set('download_signature', $encrypt->encrypt($downloadLink['signature']), 'live_update');
			echo "true";
		} else {
			echo $this->user->lang('liveupdate_step1_error');
		}
		exit;
	}

	//Download Package
	public function process_step2(){
		$encrypt = register('encrypt');
		$downloadLink = $encrypt->decrypt($this->config->get('download_link', 'live_update'));
		$new_version = str_replace('.', '', $this->getNewVersion());

		$destFolder = $this->pfh->FolderPath('update_to_'.$new_version,'live_update');
		$filename = 'lu_'.$new_version.'.zip';
		$this->pfh->secure_folder('','live_update');
		$this->repo->downloadPackage($downloadLink, $destFolder, $filename);

		if ($this->repo->verifyPackage($destFolder.$filename, $encrypt->decrypt($this->config->get('download_hash', 'live_update')), $encrypt->decrypt($this->config->get('download_signature', 'live_update')))){
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

		if ($this->repo->unpackPackage($srcFolder.$filename, $destFolder)){
			echo "true";
		} else {
			echo $this->user->lang('liveupdate_step3_error');
		}
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
			$encrypt = register('encrypt');
			$this->config->set('conflicted_files', $encrypt->encrypt(serialize($arrChanged)), 'live_update');
		}

		echo "true";
		exit;
	}

	private function download_conflicted_files(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$zipfile = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update').'conflicted_files.zip';
		$archive = registry::register('zip', array($zipfile));

		$encrypt = register('encrypt');

		//Conflicted Files
		$arrConflictedFiles = unserialize($encrypt->decrypt($this->config->get('conflicted_files', 'live_update')));
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

		$encrypt = register('encrypt');
		$stop = false;

		//Conflicted Files
		$arrConflictedFiles = unserialize($encrypt->decrypt($this->config->get('conflicted_files', 'live_update')));
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
		$new_version = str_replace('.', '', $this->getNewVersion());
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$xmlfile = $tmp_folder.'package.xml';
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile);

		foreach($arrFiles as $file){
			$this->pfh->copy($tmp_folder.$file['name'],$this->root_path.$file['name']);
		}

		echo "true";

		exit;
	}

	//Check if all new files have been copied to the right place
	public function process_step8(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$xmlfile = $tmp_folder.'package.xml';
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile);

		$arrMissingFiles = array();

		foreach($arrFiles as $file){
			if (($file['type'] == 'changed' || $file['type'] == 'new') && md5_file($this->root_path.$file['name']) != $file['md5']){
				//Hm, thats bad, the hash in package.xml does not fit the hash of the file delivered
				if (md5_file($this->root_path.$file['name']) != md5_file($tmp_folder.$file['name'])){
					$arrMissingFiles[] = $file['name'];
				}
			}
		}
		$encrypt = register('encrypt');
		$this->config->set('missing_files', $encrypt->encrypt(serialize($arrMissingFiles)), 'live_update');

		echo "true";
		exit;
	}

	private function download_missing_files(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version,'live_update');
		$zipfile = $tmp_folder.'missing_files.zip';
		$archive = registry::register('zip', array($zipfile));

		//Missing Files
		$encrypt = register('encrypt');
		$arrConflictedFiles = unserialize($encrypt->decrypt($this->config->get('missing_files', 'live_update')));
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
		}
		$encrypt = register('encrypt');
		$arrMissingFiles = unserialize($encrypt->decrypt($this->config->get('missing_files', 'live_update')));
		if ($arrMissingFiles && count($arrMissingFiles) > 0){
			$this->bring_steps_to_template(9, false, 8);

			foreach ($arrMissingFiles as $file){
				$this->tpl->assign_block_vars('missing_row', array(
					'FILENAME'	=> $file,
				));
			}

		} else {
			//Nothing to display, let's continue
			$this->bring_steps_to_template(10, true);
		}

	}

	//Delete removed files
	public function process_step10(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$tmp_folder = $this->pfh->FolderPath('update_to_'.$new_version.'/tmp/','live_update');
		$xmlfile = $tmp_folder.'package.xml';
		$arrFiles = $this->repo->getFilelistFromPackageFile($xmlfile, 'removed');
		if (is_array($arrFiles)){
			foreach ($arrFiles as $file){
				$this->pfh->Delete($this->root_path.$file['name']);
			}
		}

		echo "true";
		exit;
	}

	//Delete Installationfiles
	public function process_step11(){
		$new_version = str_replace('.', '', $this->getNewVersion());
		$folder = $this->pfh->FolderPath('update_to_'.$new_version.'/','live_update');
		$this->pfh->Delete('update_to_'.$new_version, 'live_update');
		$this->config->del('live_update');
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
		if ($this->getNewVersion()){
			$updates = $this->getNewVersion(true);
			$this->tpl->assign_vars(array(
				'S_NEW_VERSION'	=> true,
				'NEW_VERSION'	=> $updates['version'],
				'CHANGELOG'		=> $updates['changelog'],
				'RELEASE_DATE'	=> $updates['release'],
			));
		}
		$this->tpl->assign_vars(array(
			'S_START'			=> true,
			'S_RELEASE_CHANNEL' => ($this->repo->getChannel() != 'stable') ? true : false,
			'RECENT_VERSION' 	=> VERSION_EXT,
			'RELEASE_CHANNEL' 	=> ucfirst($this->repo->getChannel()),
			'S_REQUIREMENTS'	=> ($updates != NULL && $updates['dep_php'] != '') ? version_compare(PHP_VERSION, $updates['dep_php'], '>=') : true,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('liveupdate'),
			'template_file'		=> 'admin/manage_live_update.html',
			'display'			=> true)
		);
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
			'RENDERER_DROPDOWN' => $this->html->DropDown('renderer', $arrRenderer, $this->in->get('renderer', 'side_by_side'), '', 'onchange="this.form.submit();"'),
			'ENCODED_FILENAME' => $this->in->get('diff', ''),
			'S_RENDERER' => $blnRenderer,
		));
	
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('liveupdate_show_differences'),
			'template_file'		=> 'admin/diff_viewer.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Live_Update', Manage_Live_Update::__shortcuts());
registry::register('Manage_Live_Update');
?>