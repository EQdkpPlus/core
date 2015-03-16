<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2015 EQdkp-Plus Developer Team
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

class user_pageobject extends pageobject {
	public static $shortcuts = array('crypt'=>'encrypt');
	
	public function __construct() {
		$handler = array(
			'u'					=> array('process' => 'viewuser'),
		);
		$this->user->check_auth('u_userlist');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function viewuser(){
		include_once($this->root_path.'core/country_states.php');

		$user_id 	= $this->in->get('u');
		$row		= $this->pdh->get('user', 'data', array($user_id));
		if (!$row){
			$this->display();
			return;
		}

		$arrUserCustomFieldsData = $this->pdh->get('user', 'custom_fields', array($user_id));
		
		//Gender
		switch($row['gender']){
			case '1' : $strGender = $this->user->lang('gender_m').', ';
			break;
			case '2' : $strGender = $this->user->lang('gender_f').', ';
			break;
			default: $strGender = "";
		}
		
		$this->jquery->Tab_header('userprofile_tabs', true);
		
		$this->tpl->assign_vars(array(
			'USER_PROFILE_ID' => $user_id,
			'USER_PROFILE_AVATAR' => ($this->pdh->get('user', 'avatarimglink', array($user_id))) ? $this->pfh->FileLink($this->pdh->get('user', 'avatarimglink', array($user_id)), false, 'absolute') : $this->server_path.'images/global/avatar-default.svg',
			'USER_PROFILE_USERNAME'	=> sanitize($row['username']),
			'USER_PROFILE_GENDER' => $strGender,
			'USER_PROFILE_REGISTERED'	=> $this->pdh->geth('user', 'regdate', array($user_id)),
			'USER_PROFILE_LAST_ACTIVITY' => $this->pdh->geth('user', 'last_visit', array($user_id)),
			'USER_PROFILE_USERGROUPS' => str_replace(', ', '', $this->pdh->geth('user', 'groups', array($user_id, true))),
		));
		
		//Wall Permissions
		$blnWallRead = $this->pdh->get('user', 'check_privacy', array($user_id, 'priv_wall_posts_read'));
		$blnWallWrite = $this->pdh->get('user', 'check_privacy', array($user_id, 'priv_wall_posts_write'));

		//Wall
		$this->comments->SetVars(array(
			'attach_id'	=> $user_id,
			'page'		=> 'userwall',
			'auth'		=> 'a_users_man',
			'userauth' 	=> (($blnWallWrite) ? 'u_userlist' : 'a_something'),
			'replies'	=> true,
		));
		$this->tpl->assign_vars(array(
			'USER_WALL'			=> $this->comments->Show(),
			'S_SHOW_WALL'		=> $blnWallRead,
		));
		
		//Personal Profile Information
		$blnPersonal = $blnContact = false;
		
		if($this->pdh->get('user', 'check_privacy', array($user_id, 'priv_userprofile_age'))){
			$age = ($this->time->age($row['birthday']) !== 0) ? $this->time->age($row['birthday']) : '';
			if (strlen($age)) {
				$val = ($this->pdh->get('user', 'check_privacy', array($user_id, 'priv_bday'))) ? $this->time->user_date($row['birthday']).' ('.$age.')': $age;
				$this->tpl->assign_block_vars('profile_personal_row', array(
					'NAME' => $this->user->lang("user_sett_f_priv_userprofile_age"),
					'TEXT' => $val,
				));
				$this->tpl->assign_var('USER_PROFILE_AGE', $val);
				$blnPersonal = true;
			}
		}
		
		if($this->pdh->get('user', 'check_privacy', array($user_id, 'priv_userprofile_country'))){

			if (strlen($row['country'])) {
				$val = '<img src="'.$this->server_path.'images/flags/'.strtolower($row['country']).'.svg" alt="'.$row['country'].'" /> '.sanitize(ucfirst(strtolower($country_array[$row['country']])));
				$this->tpl->assign_block_vars('profile_personal_row', array(
						'NAME' => $this->user->lang("user_sett_f_priv_userprofile_country"),
						'TEXT' => $val,
				));
				$this->tpl->assign_var('USER_PROFILE_COUNTRY', $val);
				$blnPersonal = true;
			}
		}

		
		$arrProfileFields = $this->pdh->get('user_profilefields', 'usersettings_fields', array(true));
		foreach($arrProfileFields as $intFieldID){
			$blnPerm = $this->pdh->get('user', 'check_privacy', array($user_id, 'priv_userprofile_'.$intFieldID));
			if (!$blnPerm) continue;
			
			$val = $this->pdh->geth('user_profilefields', 'display_field', array($intFieldID, $user_id));

			if ($val == "") continue;
			
			$this->tpl->assign_block_vars('profile_personal_row', array(
					'NAME' => $this->pdh->geth('user_profilefields', 'name', array($intFieldID)),
					'TEXT' => $val,
			));
			$blnPersonal = true;
			$this->tpl->assign_var('USER_PROFILE_'.strtoupper($intFieldID), $val);
		}
		
		
		
		//Contact Information
		if ($this->pdh->get('user', 'check_privacy', array($user_id, 'priv_userprofile_email'))){
			$strEmail = $this->pdh->geth('user', 'email', array($user_id, true));
			if ($strEmail != ""){	
				$this->tpl->assign_block_vars('profile_contact_row', array(
						'NAME' => $this->user->lang("email_address"),
						'TEXT' => $strEmail,
				));
				$blnContact = true;
			}
		}
		
		$arrContactFields = $this->pdh->get('user_profilefields', 'contact_fields', array(true));
		foreach($arrContactFields as $intFieldID){
			$blnPerm = $this->pdh->get('user', 'check_privacy', array($user_id, 'priv_userprofile_'.$intFieldID));
			if (!$blnPerm) continue;
				
			$val = $this->pdh->geth('user_profilefields', 'display_field', array($intFieldID, $user_id));
		
			if ($val == "") continue;
				
			$this->tpl->assign_block_vars('profile_contact_row', array(
					'NAME' => $this->pdh->geth('user_profilefields', 'name', array($intFieldID)),
					'TEXT' => $val,
			));
			$blnPersonal = true;
			$this->tpl->assign_var('USER_PROFILE_'.strtoupper($intFieldID), $val);
		}
								
		$hptt_page_settings = $this->pdh->get_page_settings('userprofile', 'hptt_userprofile_memberlist_overview');

		$arrMemberList = ($this->pdh->get('member', 'mainchar', array($user_id))) ? array($this->pdh->get('member', 'mainchar', array($user_id))) : array();

		$hptt = $this->get_hptt($hptt_page_settings, $arrMemberList, $arrMemberList, array('%link_url%' => $this->routing->simpleBuild('character'), '%link_url_suffix%' => '', '%with_twink%' => false, '%use_controller%' => true), 'userprofile_'.$user_id);
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array(
			'S_PROFILE_PERSONAL_ROW' => $blnPersonal,
			'S_PROFILE_CONTACT_ROW' => $blnContact,

			'PROFILE_CHARS' 		=> $hptt->get_html_table($this->in->get('sort'), '', null, 1, sprintf($this->user->lang('listmembers_footcount'), count( $this->pdh->get('member', 'connection_id', array($user_id))))),
			'S_PROFILE_CHARACTERS'	=> count($arrMemberList),
		));
		
		//Custom Tabs
		$arrHooks = $this->hooks->process('userprofile_customtabs', array('user_id' => $user_id));
		if (is_array($arrHooks)){
			foreach ($arrHooks as $plugin => $value){
				$title = $value['title'];
				$id = substr(md5($title), 0, 9);
				$content = $value['content'];
				
				$this->tpl->assign_block_vars('custom_tabs', array(
					'ID'		=> $id,
					'NAME'		=> $title,
					'CONTENT'	=> $content,	
				));
			}
		}

		$this->jquery->Tab_header('userprofile_dkp_tabs', true);
		
		// Item History
		$arrItemListSettings = array(
			'name' => 'hptt_viewmember_itemlist',
				'table_main_sub' => '%item_id%',
				'table_subs' => array('%item_id%', '%link_url%', '%link_url_suffix%', '%raid_link_url%', '%raid_link_url_suffix%', '%itt_lang%', '%itt_direct%', '%onlyicon%', '%noicon%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'idate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ibuyerlink', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'iraidlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
		);
		if($this->config->get('disable_points')) unset($arrItemListSettings['table_presets'][5]);
		infotooltip_js();
		$view_list			= $this->pdh->get('item', 'itemids4userid', array($user_id));
		$hptt_page_settings	= $arrItemListSettings;
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('item'), '%link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true, '%member_link_url_suffix%' => '','%member_link_url%' => $this->routing->simpleBuild('character')), 'userprofile_'.$user_id, 'isort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'ITEM_OUT'			=> $hptt->get_html_table($this->in->get('isort', ''), $this->vc_build_url('isort'), $this->in->get('istart', 0), $this->user->data['user_ilimit']),
			'ITEM_PAGINATION'	=> generate_pagination($this->vc_build_url('istart', true), count($view_list), $this->user->data['user_ilimit'], $this->in->get('istart', 0), 'istart')
		));
		
		// Individual Adjustment History
		if(!$this->config->get('disable_points')){
			$arrAdjListSettings = array(
				'name' => 'hptt_viewmember_adjlist',
				'table_main_sub' => '%adjustment_id%',
				'table_subs' => array('%adjustment_id%', '%raid_link_url%', '%raid_link_url_suffix%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'adj_date', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'adj_members', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'adj_reason', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => ''),
					array('name' => 'adj_value', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
				),
			);

			$view_list = $this->pdh->get('adjustment', 'adjsofuser', array($user_id));
			$hptt_page_settings = $arrAdjListSettings;
			$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%raid_link_url%' => $this->routing->simpleBuild('raids'), '%raid_link_url_suffix%' => '', '%use_controller%' => true), 'userprofile_'.$user_id, 'asort');
			$hptt->setPageRef($this->strPath);
			$this->tpl->assign_vars(array (
				'ADJUSTMENT_OUT' 		=> $hptt->get_html_table($this->in->get('asort', ''), $this->vc_build_url('asort'), $this->in->get('astart', 0), $this->user->data['user_alimit']),
				'ADJUSTMENT_PAGINATION'	=> generate_pagination($this->vc_build_url('astart', true), count($view_list), $this->user->data['user_alimit'], $this->in->get('astart', 0), 'astart')
			));
		}
		
		
		
		// Raid Attendance
		$arrRaidListSettings = array(
			'name' => 'hptt_viewmember_raidlist',
				'table_main_sub' => '%raid_id%',
				'table_subs' => array('%raid_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'rdate', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
		);
		if($this->config->get('disable_points')) unset($arrRaidListSettings['table_presets'][3]);
		
		$view_list			= $this->pdh->get('raid', 'raidids4userid', array($user_id));
		$hptt_page_settings	= $arrRaidListSettings;
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->simpleBuild('raids') , '%link_url_suffix%' => '', '%with_twink%' => true, '%use_controller%' => true), 'userprofile_'.$user_id, 'rsort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'RAID_OUT'			=> $hptt->get_html_table($this->in->get('rsort', ''), $this->vc_build_url('rsort'), $this->in->get('rstart', 0), $this->user->data['user_rlimit']),
			'RAID_PAGINATION'	=> generate_pagination($this->vc_build_url('rstart', true), count($view_list), $this->user->data['user_rlimit'], $this->in->get('rstart', 0), 'rstart')
		));

		//Event-Attendance
		$arrEventAttSettings = array(
				'table_main_sub' => '%event_id%',
				'table_subs' => array('%event_id%', '%member_id%', '%link_url%', '%link_url_suffix%'),
				'page_ref' => 'viewcharacter.php',
				'show_numbers' => false,
				'show_select_boxes' => false,
				'show_detail_twink' => false,
				'table_sort_col' => 0,
				'table_sort_dir' => 'desc',
				'table_presets' => array(
					array('name' => 'eicon', 'sort' => false, 'th_add' => 'class="hiddenSmartphone"', 'td_add' => 'class="hiddenSmartphone"'),
					array('name' => 'elink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'event_attendance', 'sort' => true, 'th_add' => '', 'td_add' => 'width="80%"'),
				),
		);
		$view_list = $this->pdh->get('event', 'id_list');
		$hptt_page_settings = $arrEventAttSettings;
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%member_id%' => $this->pdh->get('member', 'mainchar', array($user_id)), '%link_url%' => $this->routing->simpleBuild('events'), '%link_url_suffix%' => '', '%with_twink%' => true, '%use_controller%' => true), 'userprofile_'.$user_id, 'esort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'EVENT_ATT_OUT' => $hptt->get_html_table($this->in->get('esort', ''), $this->vc_build_url('esort')),
		));
		
		
		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->server_path."email.php".$this->SID."&user=".$row['user_id'], 'width'=>'660', 'height'=>'450'));
	
		
		$this->tpl->add_meta('<link rel="canonical" href="'.$this->env->link.$this->routing->build('User', $row['username'], 'u'.$row['user_id'], false, true).'" />');
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('user').': '.sanitize($row['username']),
			'template_file'		=> 'userprofile.html',
			'display'			=> true)
		);
	}

	public function display(){
		//Sort
		$sort			= $this->in->get('sort');
		$sort_suffix	= '&amp;sort='.$sort;

		$start				= $this->in->get('start', 0);
		$pagination_suffix	= ($start) ? '&amp;start='.$start : '';
		
		$view_list = $this->pdh->get('user', 'id_list', array());

		//Output
		$hptt_page_settings	= $this->pdh->get_page_settings('listusers', 'hptt_listusers_userlist');
			
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'listusers.php', '%link_url_suffix%' => '', '%use_controller%' => true), $this->user->id);
		$hptt->setPageRef($this->strPath);
		//footer
		$user_count			= count($view_list);
		$footer_text		= sprintf($this->user->lang('listusers_footcount'), $user_count, $this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array (
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
			'USER_PAGINATION'	=> generate_pagination('listusers.php'.$this->SID.$sort_suffix, $user_count, $this->user->data['user_rlimit'], $start),
		));

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->server_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));
		$this->tpl->add_meta('<link rel="canonical" href="'.$this->env->link.$this->routing->build('User', false, false, false, true).'" />');
		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('user_list'),
			'template_file'		=> 'listusers.html',
			'display'			=> true)
		);
	}
	
		//Url building
	public function vc_build_url($exclude='', $with_base=false) {
		$base_url = $this->strPath.$this->SID;
		$url_params = array(
			'member_id'	=> $this->in->get('member_id', 0),
			'asort'		=> $this->in->get('asort', ''),
			'esort'		=> $this->in->get('esort', ''),
			'isort'		=> $this->in->get('isort', ''),
			'msort'		=> $this->in->get('msort', ''),
			'rsort'		=> $this->in->get('rsort', ''),
			'istart'	=> $this->in->get('istart', 0),
			'rstart'	=> $this->in->get('rstart', 0),
		);
		$url = ($with_base) ? $base_url : '';
		foreach($url_params as $key => $par) {
			if($key != $exclude && !empty($par)) $url .= '&amp;'.$key.'='.$par;
		}
		return $url;
	}
}
?>