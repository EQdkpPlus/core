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
		$shortcuts = array('user', 'tpl','in', 'pdh', 'game', 'config', 'core', 'html', 'time', 'pfh', 'crypt'=>'encrypt', 'jquery');
		return array_merge(parent::$shortcuts, $shortcuts);
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
		if($row['irq'] != ""){
			$irc_parts			= explode('@',$row['irq']);
			$data['irq']		= '<a href="irc://'.((isset($irc_parts[1])) ? $irc_parts[1] : 'irc.de.quakenet.org').'/'.str_replace('#', '', $irc_parts[0]).'" > <img alt="'.$row['irq'].'" title="'.$row['irq'].'" src="'.$this->root_path.'images/glyphs/irc.png" /> '.$row['irq'].'</a>';
		} else {
			$data['irq'] = '';
		}

		$user_avatar = $this->pdh->get('user', 'avatarimglink', array($row['user_id']));
		
		$age = ($this->time->age($row['birthday']) !== 0) ? $this->time->age($row['birthday']) : '';
		$user_data = array(
			'userid'			=> sanitize($row['user_id']),
			'USERNAME'			=> sanitize($row['username']),
			'firstandlastname'	=> (($row['first_name'] != '') ? sanitize($row['first_name']).' ' : '').(($row['last_name'] != '') ? sanitize($row['last_name']) : ''),
			'USER_COUNTRY'		=> ucfirst(strtolower($country_array[$row['country']])),
			'USER_COUNTRYFLAG'	=> ($row['country'] != '') ? '<img src="'.$this->root_path.'images/flags/'.strtolower($row['country']).'.png" alt="'.$row['country'].'" />' : '',
			'USER_MAIL'			=> ($perm && ($this->user->is_signedin()) && strlen($row['user_email'])) ? '<img src="'.$this->root_path.'images/glyphs/email.png" alt="email" /><a href="javascript:usermailer();">'.$this->user->lang('adduser_send_mail').'</a>' : '',
			'USER_REGISTERED'	=> ($row['user_registered'] > 0) ? $this->time->user_date($row['user_registered'], true) : '',
			'USER_ICQ'			=> ($perm && strlen($row['icq'])) ? '<a href="http://www.icq.com/people/'.$row['icq'].'" target="_blank"><img src="http://status.icq.com/online.gif?icq='.$row['icq'].'&amp;img=5" alt="icq" /> '.sanitize($row['icq']).'</a>' : '',
			'USER_SKYPE'		=> ($perm && strlen($row['skype'])) ? '<a href="skype:'.$row['skype'].'?add"><img src="'.$this->root_path.'images/glyphs/skype.png" alt="Skype" /> '.sanitize($row['skype']).'</a>' : '',
			'USER_MSN'			=> ($perm && strlen($row['msn'])) ? '<a href="http://members.msn.com/?mem='.$row['msn'].'" target="_blank"><img src="'.$this->root_path.'images/glyphs/msn.png" alt="msn" /> '.sanitize($row['msn']).'</a> ' : '',
			'USER_IRC'			=> ($perm && strlen($data['irq'])) ? $data['irq'] : '',
			'USER_TWITTER'		=> ($perm && strlen($custom['twitter'])) ? '<a href="http://twitter.com/'.$custom['twitter'].'" target="_blank"><img src="'.$this->root_path.'images/logos/twitter_icon_16.png" alt="Twitter" /> '.$custom['twitter'].'</a>' : '',
			'USER_FACEBOOK'		=> ($perm && strlen($custom['facebook'])) ? '<a href="http://facebook.com/'.((is_numeric($custom['facebook'])) ? 'profile.php?id='.$custom['facebook'] : $custom['facebook']).'" target="_blank"><img src="'.$this->root_path.'images/logos/facebook_icon_16.png" alt="Facebook" />'.sanitize($custom['facebook']).'</a>' : '',
			'USER_CELLPHONE'	=> ($phone_perm && strlen($row['cellphone'])) ? '<img src="'.$this->root_path.'images/glyphs/phone_cell.png" alt="Cell" /> '.sanitize($row['cellphone']) : '',
			'USER_PHONE'		=> ($phone_perm && strlen($row['phone'])) ? '<img src="'.$this->root_path.'images/glyphs/phone.png" alt="Phone" /> '.sanitize($row['phone']) : '',
			'USER_IMAGE'		=> (isset($custom['user_avatar']) && is_file($user_avatar)) ? $user_avatar : $this->root_path.'images/no_pic.png',
			'USER_BIRTHDAY'		=> ($privacy['priv_bday'] == 1) ? $this->time->user_date($row['birthday']).' ('.$age.')': $age,
			'USER_TOWN'			=> sanitize($row['town']),
			'USER_HARDWARE'		=> sanitize($custom['hardware']),
			'USER_WORK'			=> sanitize($custom['work']),
			'USER_INTERESTS'	=> sanitize($custom['interests']),
		);
		$this->tpl->assign_vars($user_data);

		$member_list = $this->pdh->maget('member', array('classid', 'raceid', 'rankname', 'twink', 'name', 'level'), 0, array($this->pdh->get('member', 'connection_id', array($user_id))));
		if (is_array($member_list)){
			foreach ($member_list as $mid => $member){
				$member_array = array(
					'NAME'			=> '<a href="'.$this->root_path.'viewcharacter.php'.$this->SID.'&amp;member_id='.$mid.'">'.sanitize($member['name'])."</a>",
					'LEVEL'			=> sanitize($member['level']),
					'CLASS'			=> $this->game->decorate('classes', array($member['classid'],false,$mid)).' '.$this->game->get_name('classes', $member['classid']),
					'RACE'			=> $this->game->decorate('races', array($member['raceid'],false,$mid)).' '.$this->game->get_name('races', $member['raceid']),
					'RANK'			=> sanitize($member['rankname']),
					'TYPE'			=> sanitize($member['twink']),
				);
				$this->tpl->assign_block_vars('char_row', $member_array );
			}
		}

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->root_path."email.php".$this->SID."&user=".$row['user_id'], 'width'=>'660', 'height'=>'450'));

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

}
?>