<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2002
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
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Massmail extends page_generic {
	public static function __shortcuts() {
		$shortcuts = array('user', 'tpl', 'in', 'pdh', 'jquery', 'core', 'config', 'pm', 'time', 'env', 'email'=>'MyMailer', 'crypt'=>'encrypt', 'html', 'time', 'logs', 'hooks');
		return array_merge(parent::$shortcuts, $shortcuts);
	}

	public function __construct(){
		$this->user->check_auth('a_users_massmail');
		$handler = array(
			'data'	=> array('process' => 'process_data'),
			'send'	=> array('process' => 'process_send', 'csrf' => true),
			'submit' => array('process' => 'submit', 'csrf' => true),
			);
		parent::__construct(false, $handler, array('user', 'name'), null, 'user_id[]');

		$this->process();
	}

	public function process_data(){
		//Latest News List
		$arrNews = $this->pdh->aget('news', 'news', 0, array($this->pdh->sort($this->pdh->get('news', 'id_list', array()), 'news', 'date', 'desc')));
		$arrNewsList = array();
		if (is_array($arrNews )) {
			foreach ($arrNews as $newsid => $value){
				$arrNewsList[] = array(
					'date'		=> $this->pdh->get('news', 'html_date', array($newsid)),
					'headline'	=> sanitize($value['news_headline']),
					'content'	=> '<b><u><a href="'.$this->env->link.'viewnews.php?id='.$newsid.'">'.sanitize($value['news_headline']).'</a></u></b><br /> '.sanitize($value['news_message']),
				);
			}
		}

		//Next Events List
		$arrRaidIDlist = $this->pdh->get('calendar_events', 'id_list', array(false, $this->time->time));
		$arrRaidIDlist = $this->pdh->sort($arrRaidIDlist, 'calendar_events', 'date', 'asc');
		if (is_array($arrRaidIDlist)) {
			$arrRaidIDlist = array_slice($arrRaidIDlist, 0, 15);
		}
		$arrRaidList = array();
		if (is_array($arrRaidIDlist)){
			foreach ($arrRaidIDlist as $intRaidID){
				$ahref_start = ((int)$this->pdh->get('calendar_events', 'calendartype', array($intRaidID)) == 1) ? '<a href="'.$this->env->link.'calendar/viewcalraid.php?eventid='.$intRaidID.'">' : '';
				$ahref_end	= ((int)$this->pdh->get('calendar_events', 'calendartype', array($intRaidID)) == 1) ? '</a>' : '';

				$arrRaidList[] = array(
					'date'		=> $this->pdh->get('calendar_events', 'html_date', array($intRaidID)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($intRaidID)),
					'headline'	=> $this->pdh->get('calendar_events', 'name', array($intRaidID)),
					'content'	=> $ahref_start.$this->pdh->get('calendar_events', 'html_date', array($intRaidID)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($intRaidID)).': '.$this->pdh->get('calendar_events', 'name', array($intRaidID)).$ahref_end,
				);
			}
		}

		$data = array(
			'latest_news'	=> array(
				'name'	=> $this->user->lang('news'),
				'list'	=> $arrNewsList,
			),
			'next_events' => array (
				'name'	=> $this->user->lang('massmail_next_events'),
				'list'	=> $arrRaidList,
			),
		);

		//Plugin Hooks
		$arrPluginsHooks = $this->hooks->process('massmail_content');
		if (is_array($arrPluginsHooks)){
			foreach ($arrPluginsHooks as $plugin => $value){
				if (is_array($value)){
					$data = array_merge($data, $value);
				}
			}
		}

		//Bring the content to template
		if (is_array($data)){
			foreach ($data as $key => $value){
				$this->tpl->assign_block_vars('type_row', array(
					'KEY'	=> 'd'.md5($key),
					'NAME'	=> $value['name'],
				));
				$this->jquery->selectall_checkbox('selall_d'.md5($key), 'cb_d'.md5($key).'[]', $this->user->data['user_id']);

				if (is_array($value['list'])){
					foreach ($value['list'] as $listid => $listvalue){
						$this->tpl->assign_block_vars('type_row.content_row', array(
							'DATE'		=> $listvalue['date'],
							'HEADLINE'	=> $listvalue['headline'],
							'CONTENT'	=> $listvalue['content'],
							'ID'		=> 'd'.md5($key).'_'.$listid,
						));
					}
				}
			}
		}

		$this->jquery->tab_header('massmail_content_tabs');

		$this->tpl->assign_vars(array(
			'S_DATA' => true,
		));
	}

	public function process_send(){
		$body = $this->crypt->decrypt($this->in->get('message'));
		$subject = $this->crypt->decrypt($this->in->get('subject'));
		$userid = $this->in->get('userid', 0);

		//Replace Placeholders
		$arrSearch = array('{DKP_NAME}', '{EQDKP_LINK}', '{DATE}', '{USERNAME}');
		$arrReplace = array($this->config->get('dkp_name'), $this->env->link, $this->time->user_date($this->time->time), $this->pdh->get('user', 'name', array($userid)));
		$body = str_replace($arrSearch, $arrReplace, $body);
		$subject = str_replace($arrSearch, $arrReplace, $subject);

		if ($this->in->get('event_id', 0) > 0){
			$event_id = $this->in->get('event_id', 0);
			$arrSearch = array('{EVENT_NAME}', '{EVENT_DATE}', '{EVENT_LINK}');
			$arrReplace = array($this->pdh->get('calendar_events', 'name', array($event_id)), $this->pdh->get('calendar_events', 'html_date', array($event_id)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($event_id)), '<a href="'.$this->env->link.'calendar/viewcalraid.php?eventid='.$event_id.'">'.$this->pdh->get('calendar_events', 'html_date', array($event_id)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($event_id)).': '.$this->pdh->get('calendar_events', 'name', array($event_id)).'</a>');

			$body = str_replace($arrSearch, $arrReplace, $body);
			$subject = str_replace($arrSearch, $arrReplace, $subject);
		}

		if (strlen($this->pdh->get('user', 'email', array($userid, true)))){
			$options = array(
				'template_type'		=> 'input',
			);

			//Set E-Mail-Options
			$this->email->SetOptions($options);
			
			$strEmail = $this->pdh->get('user', 'email', array($userid));
			$status = true;
			if (preg_match("/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/",$strEmail)){
				$status = $this->email->SendMailFromAdmin($strEmail, $subject, $body, '');
			}

			if (!$status){
				echo "error";
			}
		}

		echo 'true';
		exit;
	}

	public function submit(){
		if ($this->in->get('body', '', 'raw') != ""){
			$arrRecipients = array();
			if (count($this->in->getArray('usergroups', 'int')) > 0){
				foreach ($this->in->getArray('usergroups', 'int') as $key => $groupid){
					$arrGroupMembers = $this->pdh->get('user_groups_users', 'user_list', array($groupid));
					foreach($arrGroupMembers as $userid){
						$arrRecipients[] = (int)$userid;
					}
				}
			}

			if (count($this->in->getArray('user', 'int')) > 0){
				foreach ($this->in->getArray('user', 'int') as $key => $userid){
					$arrRecipients[] = (int)$userid;
				}
			}

			if (count($this->in->getArray('status', 'int')) > 0 && $this->in->get('event_id', 0) > 0){
				$eventid = $this->in->get('event_id', 0);
				foreach ($this->in->getArray('status', 'int') as $key => $statusid){

					$arrMembers = $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($eventid, $statusid));
					if (is_array($arrMembers)){
						foreach ($arrMembers as $memberid){
							$userID = (int)$this->pdh->get('member', 'userid', array($memberid));
							if ($userID != 0) $arrRecipients[] = (int)$this->pdh->get('member', 'userid', array($memberid));
						}
					}
				}

			}

			$arrRecipients = array_unique($arrRecipients);

			if (count($arrRecipients) > 0) {
				$this->tpl->assign_vars(array(
					'S_SEND'	=> true,
					'ENCR_MESSAGE'		=> $this->crypt->encrypt($this->in->get('body', '', 'raw')),
					'ENCR_SUBJECT'		=> $this->crypt->encrypt($this->in->get('subject', '')),
					'RECIPIENTS'		=> implode(',', $arrRecipients),
					'RECIPIENTS_COUNT'	=> count($arrRecipients),
					'L_MASSMAIL_SUCCESS'=> sprintf($this->user->lang('massmail_success'), count($arrRecipients)),
					'EVENT_ID'			=> $this->in->get('event_id', 0),
					'CSRF_SEND_TOKEN'	=> $this->CSRFGetToken('send'),
				));

				$strRecipientNames = '';
				foreach ($arrRecipients as $userid){
					if (strlen($this->pdh->get('user', 'email', array($userid, true)))){
						$strRecipientNames .= $this->pdh->get('user', 'name', array($userid)). ' - '.$this->pdh->get('user', 'email', array($userid)).'<br />';
					}
				}

				$log_action = array(
					'{L_adduser_send_mail_subject}'	=> $this->in->get('subject', ''),
					'{L_adduser_send_mail_body}'	=> $this->in->get('body', '', 'raw'),
					'{L_email_receiver}'			=> $strRecipientNames,
				);

				$this->logs->add('action_massmail_sent', $log_action);


			} else {
				$this->core->message($this->user->lang('massmail_norecipients'), $this->user->lang('error'), 'red');
			}

		} else {
			$this->core->message($this->user->lang('adduser_send_mail_error_fields'), $this->user->lang('error'), 'red');
			$this->display();
		}

		return true;
	}

	public function display(){
		$editor = registry::register('tinyMCE', array($this->root_path));
		$editor->editor_normal(array('autoresize' => true, 'relative_urls'	=> false, 'remove_host' => false));

		$bnlEventId = ($this->in->get('event_id', 0) > 0) ? true : false;
		$eventid = (int)$this->in->get('event_id', 0);
		$body = $subject = '';
		if ($bnlEventId){
			$body .= '<p>&nbsp;</p><p><a href="'.$this->env->link.'calendar/viewcalraid.php?eventid='.$eventid.'">'.$this->pdh->get('calendar_events', 'html_date', array($eventid)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($eventid)).': '.$this->pdh->get('calendar_events', 'name', array($eventid)).'</a></p>';
			$this->tpl->assign_vars(array(
				'DD_STATUS'	=> $this->jquery->MultiSelect('status', $this->user->lang('raidevent_raid_status'), $this->in->getArray('status', 'int'), array('width' => 400)),
			));
		}
		if ($this->in->get('template') != ""){
			$file = preg_replace('/[^a-zA-Z0-9 -]/', '', $this->in->get('template'));
			$strTemplate = file_get_contents($this->root_path.'language/'.$this->user->data['user_lang'].'/email/massmail_'.$file.'.html');
			$body = is_utf8($strTemplate) ? $strTemplate : utf8_encode($strTemplate);
			if (preg_match('#{SUBJECT}(.*?){/SUBJECT}#', $body, $matches)){
				$subject = $matches[1];
				$body = str_replace($matches[0], '', $body);
			}

		}

		$this->jquery->dialog('massmailContentDialog', $this->user->lang('massmail_add_content'), array('url' => 'manage_massmail.php'.$this->SID.'&data=true&simple_head=true', 'height' => 600, 'width' => 700));

		$arrUserGroups = $this->pdh->aget('user_groups', 'name', 0, array($this->pdh->get('user_groups', 'id_list')));
		unset($arrUserGroups[1]);

		//Load Template Files
		$arrTemplates = sdir($this->root_path.'language/'.$this->user->data['user_lang'].'/email', 'massmail_*.html');
		$arrTempl = array('' => $this->user->lang('massmail_select_template').'...');
		if (is_array($arrTemplates) && count($arrTemplates) > 0){
			foreach($arrTemplates as $file){

				$file = preg_replace('/[^a-zA-Z0-9 -]/', '', $file);
				$file = str_replace(array('massmail', 'html'), array('', ''), $file);
				$arrTempl[$file] = $file;
			}
		}

		$this->tpl->assign_vars(array(
			'DD_GROUPS'	=> $this->jquery->MultiSelect('usergroups', $arrUserGroups, $this->in->getArray('usergroups', 'int'), array('width' => 400, 'filter' => true)),
			'DD_USERS'	=> $this->jquery->MultiSelect('user', $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), $this->in->getArray('user', 'int'),  array('width' => 400, 'filter' => true)),
			'SUBJECT'	=> ($this->in->exists('subject')) ? $this->in->get('subject', '') : $subject,
			'BODY'		=> ($this->in->exists('body')) ? $this->in->get('body', '', 'raw') : $body,
			'EVENT_ID'	=> ($bnlEventId) ? '&amp;event_id='.$eventid : '',
			'S_EVENT_ID'=> $bnlEventId,
			'DD_TEMPLATE' => $this->html->DropDown('templates', $arrTempl, $this->in->get('template', ''), '', 'onchange="window.location=\'manage_massmail.php'.$this->SID.'&event_id='.$eventid.'&template=\'+this.value"'),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('massmail_send'),
			'template_file'		=> 'admin/manage_massmail.html',
			'display'			=> true)
		);
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_Manage_Massmail', Manage_Massmail::__shortcuts());
registry::register('Manage_massmail');
?>