<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class upload extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'core', 'config', 'html', 'pfh', 'uploader'	=> 'uploader');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_files_man');
		$handler = array(
			'upload' => array('process' => 'upload'),
			'create_folder' => array('process' => 'folder'),
			'action' => array(
				array('process' => 'delete', 'value' => 'delete'),
				array('process' => 'move', 'value' => 'move'))
		);
		parent::__construct(false, $handler, false, null, 'user_id');
		$this->process();
	}

	public function delete(){
		$this->uploader->delete();
		$this->display();
	}

	public function move(){
		$this->uploader->move();
		$this->display();
	}

	public function folder(){
		$this->uploader->create_folder();
		$this->display();
	}
	
	public function upload(){
			$result = $this->uploader->upload('upload', $this->in->get('folder', ''));
		if ($result){
			$this->core->message(sprintf($this->user->lang('upload_success'), $result), $this->user->lang('success'), 'green');
		} else {
			$this->core->message($this->user->lang('upload_error'), $this->user->lang('error'), 'red');
		}
		$this->display();
	}

	public function display(){
		$file_tree = $this->uploader->file_tree($this->pfh->FolderPath('files', 'eqdkp'), 'javascript:insertFile(\'[link]\');', array(), true, false, false, true);

		$folder = $this->uploader->file_tree($this->pfh->FolderPath('files','eqdkp'), '', array(), true, true, true);
		$dropdown['/'] = 'files';
		foreach ($folder as $key => $value){
			$dropdown[str_replace($this->pfh->FolderPath('files','eqdkp').'/', "", $key)] = '&nbsp;&nbsp;'.$value;
		}
			$action = array(
			'move'		=> $this->user->lang('move_files'),
			'delete'	=> $this->user->lang('delete')
		);	

		$this->tpl->assign_vars(array(
			'FILE_TREE'				=> $file_tree,
			'S_FILE_TREE'			=> true,
			'REPLACE'				=> $this->pfh->FolderPath('files', 'eqdkp'),
			'DKP_URL'				=> $this->core->buildLink(),
			'FOLDER_DD'				=> $this->html->Dropdown('folder', $dropdown, array()),
			'SRC_FOLDER_DD'			=> $this->html->Dropdown('src_folder', $dropdown, array()),
			'DEST_DD'				=> $this->html->Dropdown('dest_folder', $dropdown, array()),
			'ACTION_DD'				=> $this->html->DropDown('action', $action, 'move', '', 'onchange="check_action_dropdown()"', 'input', 'action_drpdwn'),
		));
		$this->tpl->add_js(
			"function check_action_dropdown(){
				var action = document.getElementById('action_drpdwn').value;
				if (action == \"move\"){
					document.getElementById('target_dd').style.display = \"inline\";
				} else {
					document.getElementById('target_dd').style.display = \"none\";			
				}
			}
				function insertFile(name)	{
		name = replace_url(name);
		try {
			if (is_image(name)){			
				parent.$.cleditor( { replaceWith:'[img]'+name+'[/img]' } );
			} else {
				
				parent.$.cleditor( { replaceWith:'[url='+name+'][![Alt Text:!:'+name+']!][/url]' } );
			}

		} catch(e) {
			alert(\"No cleditor! Editor found\");
		}
	}
	
	function is_image(file_name) {
  // Die erlaubten Dateiendungen
  var image_extensions = new Array('jpg','gif','png');

  // Dateiendung der Datei
  var extension = file_name.split('.');
  extension = extension[extension.length - 1];
	extension = extension.toLowerCase();
  for (var k in image_extensions) {
    if (image_extensions[k] == extension) return true;
  }
  return false;
}

function replace_url(string){

	return string.replace(\"".$this->root_path."\",\"".$this->core->buildLink()."\");
}
			");
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('file_manager'),
			'template_file'		=> 'admin/upload.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_upload', upload::__shortcuts());
registry::register('upload');
?>