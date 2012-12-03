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
$eqdkp_root_path = './';
include_once($eqdkp_root_path . 'common.php');

class listusers extends page_generic {
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

		$user_avatar = $this->pfh->FolderPath('user_avatars','eqdkp').$custom['user_avatar'];
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
			'USER_BIRTHDAY'		=> ($privacy['priv_bday'] == 1) ? $row['birthday'].' ('.$this->time->age($row['birthday']).')': $this->time->age($row['birthday']),
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
					'CLASS'			=> $this->game->decorate('classes', array($member['classid'])).' '.$this->game->get_name('classes', $member['classid']),
					'RACE'			=> $this->game->decorate('races', array($member['raceid'])).' '.$this->game->get_name('races', $member['raceid']),
					'RANK'			=> sanitize($member['rankname']),
					'TYPE'			=> sanitize($member['twink']),
				);
				$this->tpl->assign_block_vars('char_row', $member_array );
			}
		}

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->root_path."email.php".$this->SID."&user=".$row['user_id'], 'width'=>'660', 'height'=>'450'));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('user_list'),
			'template_file'		=> 'userprofile.html',
			'display'			=> true)
		);
	}

	public function display(){
		include_once($this->root_path.'core/country_states.php');

		$is_user		= ($this->user->is_signedin()) ? true : false;
		$is_admin		= ($this->user->check_group(2, false) || $this->user->check_group(3, false));
		$usergroups		= $this->pdh->get('user_groups', 'id_list', array(true));

		$special_user	= unserialize(stripslashes($this->config->get('special_user')));
		$special_user = (!$special_user) ? array() : $special_user;
		unset($usergroups[0]); //Guestgroup

		foreach ($usergroups as $group){
			$user_in_group = $this->pdh->get('user_groups_users', 'user_list', array($group));

			if (is_array($user_in_group) && count($user_in_group) > 0){
				$this->tpl->assign_block_vars('group_row', array(
					'NAME'		=> $this->pdh->get('user_groups', 'name', array($group)),
					'ID'		=> $group,
					'FOOTCOUNT'	=> sprintf($this->user->lang('user_group_footcount'), count($user_in_group)),
				));
				$this->tpl->add_js('	$("#selall_usergr_'.$group.'").click(function(){
					var checked_status = this.checked;
					$(".cellphonebox_'.$group.'").each(function(){
						this.checked = checked_status;
					});
				});', 'docready');

				foreach ($user_in_group as $usr){
					if (!in_array($usr, $special_user)){
						$row					= $this->pdh->get('user', 'data', array($usr));
						$privacy				= unserialize($row['privacy_settings']);
						$custom					= unserialize($row['custom_fields']);

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

						//IRC
						if ($row['irq'] != ""){
							$irc_parts			= explode('@',$row['irq']);
							$data['irq']		= '<a href="irc://'.((isset($irc_parts[1])) ? $irc_parts[1] : 'irc.de.quakenet.org').'/'.str_replace('#', '', $irc_parts[0]).'" > '.$this->html->Tooltip('IRC: '.$row['irq'], '<img src="'.$this->root_path.'images/glyphs/irc.png" alt="IRC" />').'</a>';
						} else {$data['irq'] = '';}

						$user_data = array(
							'userid'			=> sanitize($row['user_id']),
							'username'			=> sanitize($row['username']),
							'firstandlastname'	=> (($row['first_name'] != '') ? sanitize($row['first_name']).' ' : '').(($row['last_name'] != '') ? sanitize($row['last_name']) : ''),
							'country'			=> sanitize($row['country']),
							'country_flag'		=> ($row['country'] != '') ? $this->html->Tooltip(ucfirst(strtolower($country_array[$row['country']])), '<img src="'.$this->root_path.'images/flags/'.strtolower($row['country']).'.png" alt="'.$row['country'].'" />') : '',
							'email'				=> ($perm && ($this->user->is_signedin()) && strlen($row['user_email'])) ? '<img src="'.$this->root_path.'images/glyphs/email.png" alt="email" /><a href="javascript:usermailer('.$row['user_id'].');">'.$this->user->lang('adduser_send_mail').'</a>' : '',
							'registered'		=> ($row['user_registered'] > 0) ? $this->time->user_date($row['user_registered'], true) : '',
							'icq'				=> ($perm && strlen($row['icq'])) ? '<a href="http://www.icq.com/people/'.$row['icq'].'" target="_blank">'.$this->html->Tooltip('ICQ: '.$row['icq'], '<img src="http://status.icq.com/online.gif?icq='.$row['icq'].'&amp;img=5" alt="icq" />').'</a>' : '',
							'skype'				=> ($perm && strlen($row['skype'])) ? '<a href="skype:'.$row['skype'].'?add">'.$this->html->Tooltip('Skype: '.$row['skype'], '<img src="'.$this->root_path.'images/glyphs/skype.png" alt="Skype" />').'</a>' : '',
							'msn'				=> ($perm && strlen($row['msn'])) ? '<a href="http://members.msn.com/?mem='.$row['msn'].'" target="_blank">'.$this->html->Tooltip('MSN: '.$row['msn'], '<img src="'.$this->root_path.'images/glyphs/msn.png" alt="msn" />').'</a> ' : '',
							'irq'				=> ($perm && strlen($data['irq'])) ? $data['irq'] : '',
							'twitter'			=> ($perm && isset($custom['twitter']) && strlen($custom['twitter'])) ? '<a href="http://twitter.com/'.$custom['twitter'].'" target="_blank">'.$this->html->Tooltip('Twitter: '.$custom['twitter'], '<img src="'.$this->root_path.'images/logos/twitter_icon_16.png" alt="Twitter" />').'</a>' : '',
							'facebook'			=> ($perm && isset($custom['facebook']) && strlen($custom['facebook'])) ? '<a href="http://facebook.com/'.((is_numeric($custom['facebook'])) ? 'profile.php?id='.$custom['facebook'] : $custom['facebook']).'" target="_blank">'.$this->html->Tooltip('Facebook: '.$custom['facebook'], '<img src="'.$this->root_path.'images/logos/facebook_icon_16.png" alt="Facebook" />').'</a>' : '',
							'cellphone'			=> ($phone_perm && strlen($row['cellphone'])) ? $this->html->Tooltip($this->user->lang('adduser_cellphone').': '.$row['cellphone'], '<img src="'.$this->root_path.'images/glyphs/phone_cell.png" alt="Cell" />') : '',
							'phone'				=> ($phone_perm && strlen($row['phone'])) ? $this->html->Tooltip($this->user->lang('adduser_phone').': '.$row['phone'], '<img src="'.$this->root_path.'images/glyphs/phone.png" alt="Phone" />') : '',
							'cellphone_checkbox' => (strlen($row['cellphone']) > 1 && isset($privacy['priv_nosms']) && (int)$privacy['priv_nosms'] != 1 && (int)$this->config->get('pk_sms_enable') == 1 && $this->user->check_auth('a_sms_send', false)) ? '<input type="checkbox" name="sendto['.$row['user_id'].']" value="'.$this->crypt->encrypt($row['cellphone'].';'.$row['username']).'" class="cellphonebox_'.$group.'" />' : '',
							'charakter_count'	=> ($members = $this->pdh->get('member', 'connection_id', array($row['user_id']))) ? count($members) : 0,
						);
						$this->tpl->assign_block_vars('group_row.row_users', $user_data );
					}
				}
			}
		} //foreach user-group

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

		$this->jquery->Dialog('usermailer', $this->user->lang('adduser_send_mail'), array('url'=>$this->root_path."email.php".$this->SID."&user='+userid+'", 'width'=>'660', 'height'=>'450', 'withid'=>'userid'));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('user_list'),
			'template_file'		=> 'listusers.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_listusers', listusers::__shortcuts());
registry::register('listusers');
?>