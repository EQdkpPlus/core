<?php
 /*
 * Project:     EQdkp-Plus
 * License:     Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		    http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:       2007
 * Date:        $Date$
 * -----------------------------------------------------------------------
 * @author      $Author$
 * @copyright   2007-2008 sz3
 * @link        http://eqdkp-plus.com
 * @package     eqdkp-plus
 * @version     $Rev$
 * 
 * $Id$
 */

// EQdkp required files/vars
define('EQDKP_INC', true);
define('IN_ADMIN', true);

$eqdkp_root_path = './../';
include_once ($eqdkp_root_path . 'common.php');

class ManageProfileFields extends EQdkp_Admin{
	public function __construct(){
		global $core, $pdh, $in, $user;
		parent::eqdkp_admin();
		
		// Vars used to confirm deletion
        $confirm_text = $user->lang['confirm_del_profilefields'];
        $usernames = array();
        if ( isset($_POST['delete']) )
        {                    
            if ( isset($_POST['del']) )
            {
                $fields = $pdh->get('profile_fields', 'fields');
								$in_fields = $in->getArray('del', 'string');
								foreach ( $in_fields as $profile_id )
                {

										$headlines[] = $fields[$profile_id]['language'];
										$ids[] = $profile_id;
                }

                $lines = implode(', ', $headlines);
 								$news_ids = implode(', ', $ids);
                $confirm_text .= '<br /><br />' . $lines;
            }
            
        }

        $this->set_vars(array(
            'confirm_text'  => $confirm_text,
						'uri_parameter' => 'del_ids',
            'url_id'        => ( sizeof($headlines) > 0 ) ? $news_ids : '',
            'script_name'   => 'manage_profilefields.php' . $SID)
        );
		
		$this->assoc_params(array(
			'edit' => array(
				'name'		=> 'edit',
				'process'	=> 'process_edit',
				'check'		=> 'a_config_man'
      ),
			
      'enable' => array(
				'name'		=> 'enable',
				'process'	=> 'process_enable',
				'check'		=> 'a_config_man'
      ),
			'disable' => array(
				'name'		=> 'disable',
				'process'	=> 'process_disable',
				'check'		=> 'a_config_man'
      ),
			
		));

		$this->assoc_buttons(array(
			'add' => array(
				'name'		=> 'add',
				'process'	=> 'process_add',
				'check'		=> 'a_config_man'
      ),
			'new' => array(
				'name'		=> 'new',
				'process'	=> 'process_edit',
				'check'		=> 'a_config_man'
      ),
			'delete' => array(
				'name'		=> 'delete',
				'process'	=> 'process_delete',
				'check'		=> 'a_config_man'
      ),
      'form' => array(
          'name'      => '',
          'process'   => 'display_list',
          'check'     => 'a_config_man'
      )
		));
	}
	
	function process_enable(){
		global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $SID, $in;
		if ($in->get('enable') != ""){
			$result = $pdh->put('profile_fields', 'enable_field', array($in->get('enable')));
		}
		
		//Handle Result
		if ($result){
			$message = array('title' => $user->lang['success'], 'text' => sprintf($user->lang['pf_enable_suc'], $in->get('enable')), 'color' => 'green');
			$pdh->process_hook_queue();			
		} else {
			$message = array('title' => $user->lang['error'], 'text' => sprintf($user->lang['pf_enable_nosuc'], $in->get('enable')), 'color' => 'red');
		}
		$this->display_list($message);
		
	} //close function
	
	function process_disable(){
		global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $SID, $in;
		if ($in->get('disable') != ""){
			$result = $pdh->put('profile_fields', 'disable_field', array($in->get('disable')));
		}
		
		//Handle Result
		if ($result){
			$message = array('title' => $user->lang['success'], 'text' => sprintf($user->lang['pf_disable_suc'], $in->get('disable')), 'color' => 'green');
			$pdh->process_hook_queue();			
		} else {
			$message = array('title' => $user->lang['error'], 'text' => sprintf($user->lang['pf_disable_nosuc'], $in->get('disable')), 'color' => 'red');
		}
		$this->display_list($message);
	}
	
	function process_confirm(){
		global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $SID, $in;
		
		if ($in->get('del_ids')){
			$ids = explode(', ', $in->get('del_ids'));
			$result = $pdh->put('profile_fields', 'delete_fields', array($ids));
			$message = array('title' => $user->lang['success'], 'text' => $user->lang['pf_delete_suc'], 'color' => 'green');
		} else {
			$message = array('title' => $user->lang['error'], 'text' => $user->lang['pf_delete_nosuc'], 'color' => 'red');
		}
		$pdh->process_hook_queue();
		$this->display_list($message);
		
	}
	
	function process_add(){
		global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $SID, $in;
		if ($in->get('id') != ""){
		//Update
			$result = $pdh->put('profile_fields', 'update_field', array($in->get('id')));
		} else {		
		//Insert
			$result = $pdh->put('profile_fields', 'insert_field', array());
		}
		//Handle Result
		if ($result){
			$message = array('title' => $user->lang['success'], 'text' => $user->lang['pf_save_suc'], 'color' => 'green');
			$pdh->process_hook_queue();			
		} else {
			$message = array('title' => $user->lang['error'], 'text' => $user->lang['pf_save_nosuc'], 'color' => 'red');
		}
		$this->display_list($message);
	}
	
	function process_edit(){
		global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $SID, $in, $html, $game;
		
		$fields = $pdh->get('profile_fields', 'fields');
		  
		$field_data = $fields[$in->get('edit')];
			
			$types = array(
				'text'	=> 'Text',
				'int'		=> 'Integer',
				'dropdown' => 'Dropdown',
			);
			
			$categories = array(
				'profiler'	=> $game->glang('uc_cat_profiler'),
				'skills'		=> $game->glang('uc_cat_skills'),
				'character'	=> $game->glang('uc_cat_character'),
				'profession'=> $game->glang('uc_cat_profession'),
			);
			
			$tpl->assign_vars(array (
				'S_EDIT'						=> true,											 
				'L_MANAGE_PROFILEFIELDS'	=> $user->lang['manage_profilefields'],
				'L_NAME'     				=> $user->lang['name'],
				'L_TYPE'						=> $user->lang['type'],
				'L_CATEGORY'				=> $user->lang['pi_category'],
				'L_SIZE'						=> $user->lang['field_length'],
				'L_IMAGE'						=> sprintf($user->lang['profilefield_image'], $game->get_game()),
				'L_OPTIONS'					=> $user->lang['profilefield_optionen'],
				'L_ID'							=> $user->lang['ID'],
				'L_CANCEL'					=> $user->lang['cancel'],
				'L_SAVE'						=> $user->lang['save'],
				'F_PAGE_MANAGER'		=> 'manage_profilefields.php'.$SID,
				
				'ID'								=> ($fields[$in->get('edit')]) ? $in->get('edit') : '',
				'LANGUAGE'					=> $field_data['language'],
				'TYPE_DD'						=> $html->DropDown('type', $types, $field_data['fieldtype'], '', ' onChange="handle_fieldtypes(this.value);"'),
				'CATEGORY_DD'				=> $html->DropDown('category', $categories, $field_data['category']),
				'SIZE'							=> $field_data['size'],
				'IMAGE'							=> $field_data['image'],
				'S_SHOW_OPTIONS'		=> ($field_data['fieldtype'] == 'dropdown') ? '' : 'style="display:none;"',
			));
			
			if ($field_data['fieldtype'] == 'dropdown'){
				foreach ($field_data['options'] as $key => $value){
					$tpl->assign_block_vars('options_row', array(
						'ID'	=> $key,
						'LANGUAGE'	=> $value,
						
					));
				}
				
			}
				
			$core->set_vars(array (
					'page_title'    => $user->lang['manage_profilefields'],
					'template_file' => 'admin/manage_profilefields.html',
					'display'       => true
				)
			);
		
	}
	
  
  function display_list($message = false){
  global $core, $pdh, $tpl, $user, $jquery, $eqdkp_root_path, $pcache, $SID, $game;
  	if($message){
  		$core->messages($message);
  	}
    
		$fields = $pdh->get('profile_fields', 'fields');
		foreach ($fields as $key=>$value){
			$tpl->assign_block_vars('profile_row', array (
				'ROW_CLASS'	=> $core->switch_row_class(),
				'ID'				=> $key,
				'TYPE'			=> $value['fieldtype'],
				'CATEGORY'	=> $game->glang('uc_cat_'.$value['category']),
				'SIZE'			=> $value['size'],
				'VISIBLE'		=> $value['visible'],
				'LIST'			=> $value['list'],
				'NAME'			=> $value['language'],
				'ENABLED_ICON' => ($value['enabled'] == 1) ? 'green' : 'red',
				'ENABLE'		=> ($value['enabled'] == 1) ? 'disable' : 'enable',
				'L_ENABLE'		=> ($value['enabled'] == 1) ? $user->lang['deactivate'] : $user->lang['activate'],
				'U_EDIT'		=> 'manage_profilefields.php'.$SID.'&edit='.$key,
				'U_ENABLE'	=> 'manage_profilefields.php'.$SID.'&'.(($value['enabled'] == 1) ? 'disable' : 'enable').'='.$key,
				'S_UNDELETABLE'	=> $value['undeletable'],
			));
		}
    
    $tpl->assign_vars(array (
			'L_MANAGE_PROFILEFIELDS'	=> $user->lang['manage_profilefields'],
			'L_ENABLE'					=> $user->lang['activate'],
			'L_NAME'     				=> $user->lang['name'],
			'L_ID'							=> $user->lang['ID'],
			'L_TYPE'						=> $user->lang['type'],
			'L_CATEGORY'				=> $user->lang['pi_category'],
			'L_NEW_PROFILEFIELD'=> $user->lang['new_profilefield'],
			'L_DELETE'					=> $user->lang['delete_selected'],
			'L_ACTION'					=> $user->lang['action'],
			'FC_PROFILEFIELDS'	=> sprintf($user->lang['profilefields_footcount'], count($fields)),
    ));
    	
    $core->set_vars(array (
      	'page_title'    => $user->lang['manage_profilefields'],
      	'template_file' => 'admin/manage_profilefields.html',
        'display'       => true
    	)
    );
  }
  
 }

$manprofilefields = new ManageProfileFields();
$manprofilefields->process();
?>
