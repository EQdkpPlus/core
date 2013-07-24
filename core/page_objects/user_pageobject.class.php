<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2013-03-25 17:40:09 +0100 (Mo, 25 Mrz 2013) $
 * -----------------------------------------------------------------------
 * @author		$Author: godmod $
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 13247 $
 *
 * $Id: listusers.php 13247 2013-03-25 16:40:09Z godmod $
 */

class user_pageobject extends pageobject {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl','in', 'pdh', 'game', 'config', 'core', 'html', 'time', 'pfh', 'crypt'=>'encrypt', 'jquery', 'comments', 'routing');
		return array_merge(parent::__shortcuts(), $shortcuts);
	}

	public function __construct() {
		$handler = array(
			'send'				=> array('process' => 'process_sendSMS'),
			'u'					=> array('process' => 'viewuser'),
		);
		$this->user->check_auth('u_userlist');
		parent::__construct(false, $handler, array());
		$this->process();
	}

	public function process_sendSMS(){
		if ($this->in->get('submit') != ''){
			$this->user->check_auth('a_sms_send');

			$objSMS			= register('sms', array($this->config->get('pk_sms_username'), $this->config->get('pk_sms_password')));

			$arrReceiver	= array();
			$strMessage		= ($this->in->get('text_area') != '')? $this->in->get('text_area') : '';
			$blnReturn		= false;

			if (is_array($this->in->getArray('sendto', 'string')) && strlen($strMessage)){
				$arrReceiverEmails = $this->in->getArray('sendto', 'string');

				foreach($arrReceiverEmails as $email){
					$arrReceiver[] = $this->crypt->decrypt($email);
				}

				$blnReturn = $objSMS->send($strMessage, $arrReceiver);
			}

			if ($blnReturn){
				$this->core->message($this->user->lang('sms_success'), $this->user->lang('success'), 'green');
			} else {
				$this->core->message($objSMS->getError(), $this->user->lang('error'), 'red');
			}

		}
		$this->display();
	}

	public function viewuser(){
		include_once($this->root_path.'core/country_states.php');

		$user_id 	= $this->in->get('u');
		$row		= $this->pdh->get('user', 'data', array($user_id));
		if (!$row){
			$this->display();
			return;
		}
		$is_user	=($this->user->is_signedin()) ? true : false;
		$is_admin	= ($this->user->check_group(2, false) || $this->user->check_group(3, false));
		$privacy	= unserialize($row['privacy_settings']);
		$custom		= unserialize($row['custom_fields']);

		//check the pricacy permissions. if we dont have the permission unset() the data array
		$perm = false;
		$privacy['priv_set'] = (isset($privacy['priv_set'])) ? $privacy['priv_set'] : -1;
		switch ((int)$privacy['priv_set']){
			case 0: // all
				$perm = true;
				break;
			case 1: // only user
				if($is_user){
					$perm = true;
				}
				break;
			case 2: // only admins
				if($is_admin){
					$perm = true;
				}
			break;
			default:
				if($is_user || $is_admin){
					$perm = true;
				};
		}

		$phone_perm = false;
		$privacy['priv_phone'] = (isset($privacy['priv_phone'])) ? $privacy['priv_phone'] : -1;
		switch ((int)$privacy['priv_phone']){
			case 0: // all
				// do nothing... everything fine
				$phone_perm = true;
				break;
			case 1: // only user
				if ($is_user) { $phone_perm = true;}
				break;
			case 2: // only admins
				if ($is_admin) { $phone_perm = true;}
				break;
			case 3: // nobody
				$phone_perm = false;
				break;
			default:
				if ($is_admin || $is_user){
					$phone_perm = true;
				}
		}
		
		//Gender
		switch($row['gender']){
			case '1' : $strGender = $this->user->lang('adduser_gender_m').', ';
			break;
			case '1' : $strGender = $this->user->lang('adduser_gender_m').', ';
			break;
			default: $strGender = "";
		}
		
		$this->jquery->Tab_header('userprofile_tabs', true);
		$this->tpl->assign_vars(array(
			'USER_PROFILE_ID' => $user_id,
			'USER_PROFILE_AVATAR' => ($this->user->is_signedin() && $this->pdh->get('user', 'avatarimglink', array($user_id))) ? $this->pfh->FileLink($this->pdh->get('user', 'avatarimglink', array($user_id)), false, 'absolute') : $this->server_path.'images/no_pic.png',
			'USER_PROFILE_USERNAME'	=> sanitize($row['username']),
			'USER_PROFILE_GENDER' => $strGender,
			'USER_PROFILE_REGISTERED'	=> $this->pdh->geth('user', 'regdate', array($user_id)),
			'USER_PROFILE_LAST_ACTIVITY' => $this->pdh->geth('user', 'last_visit', array($user_id)),
			'USER_PROFILE_USERGROUPS' => str_replace(', ', '', $this->pdh->geth('user', 'groups', array($user_id, true))),
		));
		
		//Wall Permissions
		$blnWallRead = false;
		if (!isset($privacy['priv_wall_posts_read'])) {
			if ($is_user) $blnWallRead = true;
		} else {		
			switch($privacy['priv_wall_posts_read']){
				case '0' : $blnWallRead = true;
				break;
				case '1' : if ($is_user)  $blnWallRead = true;
				break;
				case '2' : if (($user_id == $this->user->id) || $is_admin) $blnWallRead = true;
				break;
				default: if ($is_admin)  $blnWallRead = true;
			}
		}
		
		$blnWallWrite = false;
		if (!isset($privacy['priv_wall_posts_write'])) {
			if ($is_user)  $blnWallWrite = true;
		} else {
			switch($privacy['priv_wall_posts_write']){
				case '1' : if ($is_user)  $blnWallWrite = true;
				break;
				case '2' : if (($user_id == $this->user->id) || $is_admin) $blnWallWrite = true;
				break;
				default: if ($is_admin)  $blnWallWrite = true;
			}
		}
		
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
		$arrProfile = array();
		if ($row['first_name'] != "" || $row['last_name'] != "") $arrProfile['name'] = (($row['first_name'] != '') ? sanitize($row['first_name']).' ' : '').(($row['last_name'] != '') ? sanitize($row['last_name']) : '');
		$age = ($this->time->age($row['birthday']) !== 0) ? $this->time->age($row['birthday']) : '';
		if (strlen($age)) $arrProfile['age'] = ($privacy['priv_bday'] == 1) ? $this->time->user_date($row['birthday']).' ('.$age.')': $age;
		if ($row['town'] != "") $arrProfile['adduser_town'] = (($row['ZIP_code'] != "") ? sanitize($row['ZIP_code']).' ': '').sanitize($row['town']);
		if ($row['state'] != "") $arrProfile['adduser_state'] = sanitize($row['state']);
		if ($row['country'] != "") $arrProfile['adduser_country'] = '<img src="'.$this->server_path.'images/flags/'.strtolower($row['country']).'.png" alt="'.$row['country'].'" /> '.sanitize(ucfirst(strtolower($country_array[$row['country']])));
		
		foreach($arrProfile as $key => $val){
			$this->tpl->assign_block_vars('profile_personal_row', array(
				'NAME' => $this->user->lang($key),
				'TEXT' => $val,
			));
			$this->tpl->assign_var('USER_PROFILE_'.strtoupper($key), $val);
		}
		
		//Contact Information
		$arrContact = array();
		if ($perm && ($this->user->is_signedin()) && strlen($row['user_email'])) $arrContact['email_address'] = '<a href="javascript:usermailer();"><i class="icon-envelope icon-large"></i> '.$this->user->lang('adduser_send_mail').'</a>';
		if ($phone_perm && strlen($row['cellphone'])) $arrContact['adduser_cellphone'] = '<i class="icon-mobile-phone icon-large"></i> '.sanitize($row['cellphone']);
		if ($phone_perm && strlen($row['phone'])) $arrContact['adduser_phone'] = '<i class="icon-phone icon-large"></i> '.sanitize($row['phone']);
		if ($perm && strlen($row['icq'])) $arrContact['adduser_icq'] = '<a href="http://www.icq.com/people/'.sanitize($row['icq']).'" target="_blank"><img src="http://status.icq.com/online.gif?icq='.sanitize($row['icq']).'&amp;img=5" alt="icq" /> '.sanitize($row['icq']).'</a>';
		if ($perm && strlen($row['skype'])) $arrContact['adduser_skype'] = '<a href="skype:'.sanitize($row['skype']).'?add"><i class="icon-skype icon-large"></i> '.sanitize($row['skype']).'</a>';
		if ($perm && strlen($custom['twitter'])) $arrContact['adduser_twitter'] = '<a href="https://twitter.com/'.sanitize($custom['twitter']).'" target="_blank"><i class="icon-twitter icon-large"></i> '.sanitize($custom['twitter']).'</a>';
		if ($perm && strlen($custom['facebook'])) $arrContact['adduser_facebook'] = '<a href="https://facebook.com/'.((is_numeric($custom['facebook'])) ? 'profile.php?id='.sanitize($custom['facebook']) : sanitize($custom['facebook'])).'" target="_blank"><i class="icon-facebook icon-large"></i> '.sanitize($custom['facebook']).'</a>';
		if ($perm && strlen($custom['youtube'])) $arrContact['adduser_youtube'] = '<a href="https://www.youtube.com/user/'.sanitize($custom['youtube']).'" target="_blank"><i class="icon-youtube icon-large"></i> '.sanitize($custom['youtube']).'</a>';
		
		if($row['irq'] != ""){
			$irc_parts			= explode('@',$row['irq']);
			$data['irq']		= '<a href="irc://'.((isset($irc_parts[1])) ? $irc_parts[1] : 'irc.de.quakenet.org').'/'.str_replace('#', '', $irc_parts[0]).'" >'.$row['irq'].'</a>';
		} else {
			$data['irq'] = '';
		}
		if ($perm && strlen($data['irq'])) $arrContact['adduser_irq'] = $data['irq'];
		
		foreach($arrContact as $key => $val){
			$this->tpl->assign_block_vars('profile_contact_row', array(
				'NAME' => $this->user->lang($key),
				'TEXT' => $val,
			));
			$this->tpl->assign_var('USER_PROFILE_'.strtoupper($key), $val);
		}
		
		//Misc Profile Information
		$arrMisc = array();
		if (strlen($custom['hardware'])) $arrMisc['adduser_hardware'] = sanitize($custom['hardware']);
		if (strlen($custom['work'])) $arrMisc['adduser_work'] = sanitize($custom['work']);
		if (strlen($custom['interests'])) $arrMisc['adduser_interests'] = sanitize($custom['interests']);
		
		foreach($arrMisc as $key => $val){
			$this->tpl->assign_block_vars('profile_misc_row', array(
				'NAME' => $this->user->lang($key),
				'TEXT' => $val,
			));
			$this->tpl->assign_var('USER_PROFILE_'.strtoupper($key), $val);
		}
				
		$hptt_page_settings = array('name' => 'hptt_listmembers_memberlist_overview',
			'table_main_sub' => '%member_id%',
			'table_subs' => array('%member_id%', '%link_url%', '%link_url_suffix%', '%with_twink%'),
			'page_ref' => $this->strPath,
			'show_numbers' => false,
			'show_select_boxes' => false,
			'show_detail_twink' => false,
			'perm_detail_twink' => true,
			'table_sort_col' => 0,
			'table_sort_dir' => 'asc',
			'table_presets' => array(
				array('name' => 'mlink_decorated', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'mlevel', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'mrank', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'mtwink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'current_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'attendance_30_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'attendance_lt_all', 'sort' => true, 'th_add' => '', 'td_add' => ''),
		));

		$arrMemberList = ($this->pdh->get('member', 'mainchar', array($user_id))) ? array($this->pdh->get('member', 'mainchar', array($user_id))) : array();

		$hptt = $this->get_hptt($hptt_page_settings, $arrMemberList, $arrMemberList, array('%link_url%' => $this->routing->build('character', false, false, false), '%link_url_suffix%' => '', '%with_twink%' => false, '%use_controller%' => true), 'userprofile_'.$user_id);
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array(
			'S_PROFILE_PERSONAL_ROW' => count($arrProfile),
			'S_PROFILE_CONTACT_ROW' => count($arrContact),
			'S_PROFILE_MISC_ROW' 	=> count($arrMisc),
			'PROFILE_CHARS' 		=> $hptt->get_html_table($this->in->get('sort'), '', null, 1, sprintf($this->user->lang('listmembers_footcount'), count( $this->pdh->get('member', 'connection_id', array($user_id))))),
			'S_PROFILE_CHARACTERS'	=> count($arrMemberList),
		));


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
					array('name' => 'ibuyerlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ilink_itt', 'sort' => true, 'th_add' => '', 'td_add' => 'style="height:21px;"'),
					array('name' => 'iraidlink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ipoolname', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'ivalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
		);
		infotooltip_js();
		$view_list			= $this->pdh->get('item', 'itemids4userid', array($user_id));
		$hptt_page_settings	= $arrItemListSettings;
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->build('item', false, false, false), '%link_url_suffix%' => '', '%itt_lang%' => false, '%itt_direct%' => 0, '%onlyicon%' => 0, '%noicon%' => 0, '%raid_link_url%' => $this->routing->build('raid', false, false, false), '%raid_link_url_suffix%' => '', '%use_controller%' => true, '%member_link_url_suffix%' => '','%member_link_url%' => $this->routing->build('character', false, false, false)), 'userprofile_'.$user_id, 'isort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'ITEM_OUT'			=> $hptt->get_html_table($this->in->get('isort', ''), $this->vc_build_url('isort'), $this->in->get('istart', 0), $this->user->data['user_ilimit']),
			'ITEM_PAGINATION'	=> generate_pagination($this->vc_build_url('istart', true), count($view_list), $this->user->data['user_ilimit'], $this->in->get('istart', 0), 'istart')
		));
		
		// Individual Adjustment History
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
				array('name' => 'adj_members', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				array('name' => 'adj_reason', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => ''),
				array('name' => 'adj_value', 'sort' => true, 'th_add' => '', 'td_add' => 'nowrap="nowrap"'),
			),
		);
		
		$view_list = $this->pdh->get('adjustment', 'adjsofuser', array($user_id));
		$hptt_page_settings = $arrAdjListSettings;
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%raid_link_url%' => $this->routing->build('raid', false, false, false), '%raid_link_url_suffix%' => '', '%use_controller%' => true), 'userprofile_'.$user_id, 'asort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'ADJUSTMENT_OUT' 		=> $hptt->get_html_table($this->in->get('asort', ''), $this->vc_build_url('asort'), $this->in->get('astart', 0), $this->user->data['user_alimit']),
			'ADJUSTMENT_PAGINATION'	=> generate_pagination($this->vc_build_url('astart', true), count($view_list), $this->user->data['user_alimit'], $this->in->get('astart', 0), 'astart')
		));
		
		
		
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
					array('name' => 'rnote', 'sort' => true, 'th_add' => 'width="70%"', 'td_add' => 'nowrap="nowrap"'),
					array('name' => 'rvalue', 'sort' => true, 'th_add' => '', 'td_add' => ''),
				),
		);
		
		$view_list			= $this->pdh->get('raid', 'raidids4userid', array($user_id));
		$hptt_page_settings	= $arrRaidListSettings;
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => $this->routing->build('raid', false, false, false) , '%link_url_suffix%' => '', '%with_twink%' => true, '%use_controller%' => true), 'userprofile_'.$user_id, 'rsort');
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
					array('name' => 'eicon', 'sort' => false, 'th_add' => '', 'td_add' => ''),
					array('name' => 'elink', 'sort' => true, 'th_add' => '', 'td_add' => ''),
					array('name' => 'event_attendance', 'sort' => true, 'th_add' => '', 'td_add' => 'width="80%"'),
				),
		);
		$view_list = $this->pdh->get('event', 'id_list');
		$hptt_page_settings = $arrEventAttSettings;
		$hptt = $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%member_id%' => $this->pdh->get('member', 'mainchar', array($user_id)), '%link_url%' => $this->routing->build('event', false, false, false), '%link_url_suffix%' => '', '%with_twink%' => true, '%use_controller%' => true), 'userprofile_'.$user_id, 'esort');
		$hptt->setPageRef($this->strPath);
		$this->tpl->assign_vars(array (
			'EVENT_ATT_OUT' => $hptt->get_html_table($this->in->get('esort', ''), $this->vc_build_url('esort')),
		));
		
		
		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->server_path."email.php".$this->SID."&user=".$row['user_id'], 'width'=>'660', 'height'=>'450'));

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
			
		$hptt				= $this->get_hptt($hptt_page_settings, $view_list, $view_list, array('%link_url%' => 'listusers.php', '%link_url_suffix%' => '', '%use_controller%' => true));
		$hptt->setPageRef($this->strPath);
		//footer
		$user_count			= count($view_list);
		$footer_text		= sprintf($this->user->lang('listusers_footcount'), $user_count, $this->user->data['user_rlimit']);

		$this->tpl->assign_vars(array (
			'PAGE_OUT'			=> $hptt->get_html_table($sort, $pagination_suffix, $start, $this->user->data['user_rlimit'], $footer_text),
			'USER_PAGINATION'	=> generate_pagination('listusers.php'.$this->SID.$sort_suffix, $user_count, $this->user->data['user_rlimit'], $start),
		));

		if (((int)$this->config->get('pk_sms_enable') == 1) && $this->user->check_auth('a_sms_send', false)){
				if(strlen(($this->config->get('pk_sms_username'))) < 1 || strlen(($this->config->get('pk_sms_password')))){
					$sms_info = $this->user->lang('sms_info_account')." ".$this->user->lang('sms_info_account_link') ;
					if ($_HMODE) {$sms_info = $this->user->lang('sms_info_account')." ".$_HMODE_LINK;}
				}

				$this->tpl->assign_vars(array(
					'F_SMS'				=> true,
					'F_ACC_INFO'		=> $sms_info,
				));
		}

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->server_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));

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