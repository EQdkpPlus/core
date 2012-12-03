<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:	     	http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2009
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2009 GodMod
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Upload extends EQdkp_Admin
{
		
    function Upload()
    {
        global $db, $core, $user, $tpl, $pm, $timekeeper;
        global $SID;

        parent::eqdkp_admin();

        $this->assoc_buttons(array(
            'upload' => array(
                'name'    => 'upload',
                'process' => 'process_upload',
                'check'   => 'a_files_man'),
						'folder' => array(
                'name'    => 'create_folder',
                'process' => 'process_folder',
                'check'   => 'a_files_man'),

						
						'delete' => array(
                'name'    => 'action',
								'value'		=> 'delete',
                'process' => 'process_delete',
                'check'   => 'a_files_man'),
						'move' => array(
                'name'    => 'action',
								'value'		=> 'move',
                'process' => 'process_move',
                'check'   => 'a_files_man'),

            'form' => array(
                'name'    => '',
                'process' => 'display_form',
                'check'   => 'a_files_man'))
        );

    }

    function error_check()
    {
        return false;
    }
		
		
		function process_delete(){
				global $db, $in, $user, $core, $timekeeper, $SID, $pcache, $uploader;

				$uploader->delete();
				
				$this->display_form();
		}
		
		function process_move(){
				global $db, $in, $user, $core, $timekeeper, $SID, $pcache, $uploader;
				
				$uploader->move();
				$this->display_form();
		}
		
		function process_folder(){
			global $db, $in, $user, $core, $timekeeper, $SID, $pcache, $uploader;
			$uploader->create_folder();
			$this->display_form();
		}
		
		function process_upload(){
			global $db, $in, $user, $core, $timekeeper, $SID, $pcache, $uploader;

			$result = $uploader->upload('upload', $in->get('folder', ''));
			if ($result){
				$core->message(sprintf($user->lang['upload_success'], $result), $user->lang['success'], 'green');
			} 
			
			$this->display_form();
		}
		
		
		function display_form(){
			global $db, $core, $user, $tpl, $pm, $jquery, $SID, $game, $html, $in, $pcache, $eqdkp_root_path, $uploader;
				
			$file_tree = $uploader->file_tree($pcache->FolderPath('files', 'eqdkp'), 'javascript:insertFile(\'[link]\');', array(), true, false, false, true);
			
			$folder = $uploader->file_tree($pcache->FolderPath('files','eqdkp'), '', array(), true, true, true);
			$dropdown['/'] = 'files';
			foreach ($folder as $key => $value){
				$dropdown[str_replace($pcache->FolderPath('files','eqdkp').'/', "", $key)] = '&nbsp;&nbsp;'.$value;
			}

			$action = array(
				'move'	=> $user->lang['move_files'],
				'delete'	=> $user->lang['delete']
			);	
			
				$tpl->assign_vars(array(
					'FILE_TREE'			=> $file_tree,
					'S_FILE_TREE'		=> true,
					'REPLACE'				=> $pcache->FolderPath('files', 'eqdkp'),
					'DKP_URL'				=> $core->buildLink(),
					'FOLDER_DD'			=> $html->Dropdown('folder', $dropdown, array()),
					'SRC_FOLDER_DD'	=> $html->Dropdown('src_folder', $dropdown, array()),
					'DEST_DD'	=> $html->Dropdown('dest_folder', $dropdown, array()),
					'ACTION_DD'			=> $html->DropDown('action', $action, 'move', '', 'onChange="check_action_dropdown()"', 'input', 'action_drpdwn'),
					'L_UPLOAD_FILE'	=> $user->lang['upload_file'],
					'L_SELECT_FILE'	=> $user->lang['select_file'],
					'L_SELECT_DEST_FOLDER'	=> $user->lang['select_dest_folder'],
					'L_DELETE_MARKED'	=> $user->lang['delete_selected'],
					'L_UPLOAD'			=> $user->lang['upload_file'],
					'L_ADD_FOLDER'	=> $user->lang['add_folder'],
					'L_FILE_MANAGER'	=> $user->lang['file_manager'],
					'L_FOLDER_NAME'	=> $user->lang['folder_name'],
					'L_TO'					=> $user->lang['move_to'],
					'L_GO'					=> $user->lang['go'],
					'L_MARKED'		=>  $user->lang['selected_files'],
				));
				
			$tpl->add_js(
				"function check_action_dropdown(){
		
	var action = document.getElementById('action_drpdwn').value;

	if (action == \"move\"){
			
		document.getElementById('target_dd').style.display = \"inline\";
			
	} else {
			
		document.getElementById('target_dd').style.display = \"none\";
			
	}
	}"
			);
			$core->set_vars(array(
            'page_title'    => $user->lang['file_manager'],
            'template_file' => 'admin/upload.html',
            'header_format'	=> 'simple',
						'display'       => true,
						
						)
			);
		}

}

$upload = new Upload;
$upload->process();
?>