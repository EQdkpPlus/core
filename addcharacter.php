<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2006
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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class addcharacter extends page_generic {
	private $data = array();

	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'game', 'config', 'core', 'html');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct() {
		$handler = array(
			'add'	=> array('process' => 'add', 'check' => 'u_member_add', 'csrf'=>true),
			'edit'	=> array('process' => 'edit', 'check' => 'u_member_man', 'csrf'=>true),
		);

		// Permissions
		$this->user->check_auths(array('u_member_man', 'u_member_add'), 'OR');
		
		// Check if the user is logged in
		if (!$this->user->is_signedin()){
			message_die($this->user->lang('uc_not_loggedin'));
		}
		
		$this->data['rank_id'] = $this->pdh->get('rank', 'default', array());
		
		parent::__construct('u_member_', $handler, array(), null, '', 'editid');
		$this->process();
	}
		
	//Add a new character
	public function add(){
		$data = array(
			'name'		=> $this->in->get('member_name'),
			'lvl'		=> $this->in->get('member_level', 0),
			'classid'	=> $this->in->get('member_class_id', 0),
			'raceid'	=> $this->in->get('member_race_id', 0),				
			'notes'		=> htmlspecialchars($this->in->get('notes'), ENT_QUOTES),
			'picture'	=> $this->in->get('picture'),
		);
		$this->data = array(
			'level'	=> $data['lvl'],
			'class_id'=> $data['classid'],
			'race_id'=> $data['raceid'],
		);
		
		if ($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false)){
			$data['mainid'] = $this->in->get('main_id', 0);
		}
		
		$data = array_merge($this->in->getArray('profilefields', 'string'), $data);
		$this->data = array_merge($this->data, $data);
		if (strlen($this->in->get('member_name'))){
			$this->pdh->put('member', 'addorupdate_member', array($this->url_id, $data, $this->in->get('overtakeuser')));
		
			$this->pdh->process_hook_queue();
			$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
			return true;
		} else {
			$this->core->message($this->user->lang('missing_values').$this->user->lang('name'), $this->user->lang('error'), 'red', true);
			return false;
		}
	}

	public function edit(){
		$data = array(
			'lvl'		=> $this->in->get('member_level', 0),
			'classid'	=> $this->in->get('member_class_id', 0),
			'raceid'	=> $this->in->get('member_race_id', 0),
			'notes'		=> htmlspecialchars($this->in->get('notes'), ENT_QUOTES),
			'picture'	=> $this->in->get('picture'),
		);
		//Admin only things
		if ($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false) && strlen($this->in->get('member_name'))){
			$data['name'] 	= $this->in->get('member_name');
			$data['rankid']	= $this->in->get('rank_id', $this->pdh->get('rank', 'default', array()));
			$data['status'] = $this->in->get('status', 0);
			$data['mainid'] = $this->in->get('main_id', 0);
		}

		$data = array_merge($this->in->getArray('profilefields', 'string'), $data);
		$this->pdh->put('member', 'addorupdate_member', array($this->url_id, $data, $this->in->get('overtakeuser')));
		
		//Transfer character history
		if ($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false) && ($this->url_id != $this->in->get('history_receiver', 0)) && $this->in->get('history_receiver', 0) > 0){
			$this->pdh->put('member', 'trans_member', array($this->url_id, $this->in->get('history_receiver', 0)));
		}
		$this->pdh->process_hook_queue();
		$this->tpl->add_js('jQuery.FrameDialog.closeDialog();');
	}

	public function display(){
		// Read the Data
		$member_data	= ($this->url_id > 0) ? $this->pdh->get('member', 'array', array($this->url_id)) : $this->data;
		$userid_real	= ($this->url_id > 0) ? $this->pdh->get('member', 'userid', array($this->url_id)) : $this->user->data['user_id'];

		// test
		if($this->in->get('ajax')){
			echo($this->game->callfunc('gameprofile_'.$this->in->get('ajax'), array($this->in->get('requestid'))));
			exit;
		}

		// Static fields
		$static_fields = array();
		if($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false)){
			$maincharsel	= $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list')));
			asort($maincharsel);
			if (!$this->url_id){
				$maincharsel[0] = $this->user->lang('mainchar');
			}
			$static_fields['main_id']	= array(
				'fieldtype'		=> 'dropdown',
				'category'		=> 'character',
				'name'			=> 'main_id',
				'options'		=> $maincharsel,
				'language'		=> $this->user->lang('mainchar'),
				'directfield'	=> true,
				'visible'		=> true
			);
		}

		$static_fields = array_merge($static_fields, array(
			'member_race_id'	=> array(
				'fieldtype'		=> 'dropdown',
				'category'		=> 'character',
				'name'			=> 'race_id',
				'options'		=> $this->game->get('races'),
				'language'		=> $this->user->lang('race'),
				'directfield'	=> true,
				'visible'		=> true
			),
			'member_class_id'	=> array(
				'fieldtype'		=> 'dropdown',
				'category'		=> 'character',
				'name'			=> 'class_id',
				'options'		=> $this->game->get('classes', array('id_0')),
				'language'		=> $this->user->lang('class'),
				'directfield'	=> true,
				'visible'		=> true
			),
			'member_level'	=> array(
				'fieldtype'		=> 'int',
				'category'		=> 'character',
				'name'			=> 'level',
				'language'		=> $this->user->lang('level'),
				'directfield'	=> true,
				'size'			=> 4,
				'visible'		=> true,
				'undeletable'	=> 1
			),
		));

		if($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false)){
			$tmpranks		= $this->pdh->aget('rank', 'name', 0, array($this->pdh->get('rank', 'id_list')));
			$static_fields['rank_id']	= array(
				'fieldtype'		=> 'dropdown',
				'category'		=> 'character',
				'name'			=> 'rank_id',
				'options'		=> $tmpranks,
				'language'		=> $this->user->lang('rank'),
				'directfield'	=> true,
				'visible'		=> true,
			);
		}

		// Dynamic Fields
		$this->jquery->Tab_header('addchar_tab');
		$categorynames			= $this->pdh->get('profile_fields', 'categories');
		$categorynames			= (is_array($categorynames)) ? $categorynames : array('character');
		$changed_profilefields	= $this->game->callfunc('changeprofilefields');
		if(is_array($categorynames)){
			foreach($categorynames as $catname){
				if($catname != 'character'){
					$this->tpl->assign_block_vars('cmrow', array(
						'NAME'	=> ($this->game->glang('uc_cat_'.$catname)) ? $this->game->glang('uc_cat_'.$catname) : $this->user->lang('uc_cat_'.$catname),
						'ID'	=> $catname
						)
					);
				}
				$profilefields = array_merge($static_fields, $this->pdh->get('profile_fields', 'fields'), $changed_profilefields);
				foreach($profilefields as $name=>$confvars){
					if($confvars['category'] == $catname){
						$ccfield = $this->html->generateField($confvars, ((isset($confvars['directfield']) && $confvars['directfield']) ? $name : 'profilefields['.$name.']'), (isset($confvars['name']) && (isset($member_data[$confvars['name']])) ? $member_data[$confvars['name']] : $member_data[$name]));
						if($ccfield && $confvars['visible']){
							$dynwhereto = ($confvars['category'] == 'character') ? 'character_row' : 'cmrow.tabs';
							$this->tpl->assign_block_vars($dynwhereto, array(
								'NAME'		=> ($this->game->glang($confvars['language'])) ? $this->game->glang($confvars['language']) : $confvars['language'],
								'FIELD'		=> $ccfield,
								'HELP'		=> ($this->game->glang($confvars['language'].'_help')) ? $this->game->glang($confvars['language'].'_help') : '',
							));
						}
					}
				}
			}
		}

		$arrHistoryReceivers = $this->pdh->aget('member', 'name', 0, array($this->pdh->get('member', 'id_list')));
		asort($arrHistoryReceivers);
		$this->jquery->Validate('addchar', array(
			array('name' => 'member_name', 'value' => '<br/>'.$this->user->lang('fv_required_name'))
		));
		$this->jquery->ResetValidate('addchar');
		$this->tpl->assign_vars(array(
			// Permissions
			'U_IS_EDIT'				=> ($this->url_id > 0) ? true : false,
			'USER_CAN_CONNECT'		=> ($this->user->check_auth('u_member_conn', false)) ? true : false,
			'ADMINMODE'				=> ($this->in->get('adminmode', 0) && $this->user->check_auth('a_members_man', false)),

			// Data
			'STATUS'				=> (isset($member_data['status'])) ? $member_data['status'] : '',
			'MEMBER_ID'				=> ($this->url_id > 0) ? $this->url_id : 0,
			'MEMBER_NAME'			=> ((isset($member_data['name'])) ? $member_data['name'] : ''),
			'NOTES'					=> stripslashes(((isset($member_data['notes'])) ? $member_data['notes'] : '')),
			'DD_HISTORY_RECEIVER'	=> $this->html->DropDown('history_receiver', $arrHistoryReceivers, $this->url_id),
			'MEMBER_PICTURE'		=> '<input type="hidden" value="'.$member_data['picture'].'" name="picture"/>',
		));

		$this->core->set_vars(array(
			'page_title'		=> '',
			'template_file'		=> 'addcharacter.html',
			'header_format'		=> 'simple',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_addcharacter', addcharacter::__shortcuts());
registry::register('addcharacter');
?>