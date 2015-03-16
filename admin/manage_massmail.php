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

define('EQDKP_INC', true);
define('IN_ADMIN', true);
$eqdkp_root_path = './../';
include_once($eqdkp_root_path . 'common.php');

class Manage_Massmail extends page_generic {
	public static $shortcuts = array('email'=>'MyMailer', 'crypt'=>'encrypt');
		
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
		//Latest Articles
		$arrArticles = $this->pdh->get('articles', 'id_list', array());
		$arrArticles = $this->pdh->limit($arrArticles, 0, 25);
		$arrArticles = $this->pdh->sort($arrArticles, 'articles', 'date', 'desc');
		foreach($arrArticles as $intArticleID){
			$strText = $this->pdh->get('articles',  'text', array($intArticleID));
			$arrContent = preg_split('#<hr(.*)id="system-readmore"(.*)\/>#iU', xhtml_entity_decode($strText));
			
			$strText = $this->bbcode->remove_embeddedMedia($this->bbcode->remove_shorttags($arrContent[0]));
			
			//Replace Image Gallery
			$arrGalleryObjects = array();
			preg_match_all('#<p(.*)class="system-gallery"(.*) data-sort="(.*)" data-folder="(.*)">(.*)</p>#iU', $strText, $arrGalleryObjects, PREG_PATTERN_ORDER);
			if (count($arrGalleryObjects[0])){
				include_once($this->root_path.'core/gallery.class.php');
				foreach($arrGalleryObjects[4] as $key=>$val){
					$strText = str_replace($arrGalleryObjects[0][$key], "", $strText);
				}
			}
			
			//Replace Raidloot
			$arrRaidlootObjects = array();
			preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)"(.*) data-chars="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
			if (count($arrRaidlootObjects[0])){
				include_once($this->root_path.'core/gallery.class.php');
				foreach($arrRaidlootObjects[3] as $key=>$val){
					$strText = str_replace($arrRaidlootObjects[0][$key], "", $strText);
				}
			}
			
			$arrNewsList[] = array(
					'date'		=> $this->pdh->get('articles', 'html_date', array($intArticleID)),
					'headline'	=> $this->pdh->get('articles', 'title', array($intArticleID)),
					'content'	=> '<b><u><a href="'.$this->user->removeSIDfromString($this->env->link.$this->pdh->get('articles',  'path', array($intArticleID))).'">'.unsanitize($this->pdh->get('articles', 'title', array($intArticleID))).'</a></u></b><br /> '.$strText,
			);
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
				$ahref_start = ((int)$this->pdh->get('calendar_events', 'calendartype', array($intRaidID)) == 1) ? '<a href="'.$this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($intRaidID)), $intRaidID, false, true).'">' : '';
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
			$arrReplace = array($this->pdh->get('calendar_events', 'name', array($event_id)), $this->pdh->get('calendar_events', 'html_date', array($event_id)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($event_id)), '<a href="'.$this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($event_id)), $event_id, false, true).'">'.$this->pdh->get('calendar_events', 'html_date', array($event_id)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($event_id)).': '.$this->pdh->get('calendar_events', 'name', array($event_id)).'</a>');

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

				$this->logs->add('action_massmail_sent', $log_action, '', $this->in->get('subject', ''));


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
		$editor = registry::register('tinyMCE');
		$editor->editor_normal(array('autoresize' => true, 'relative_urls'	=> false, 'remove_host' => false));

		$bnlEventId = ($this->in->get('event_id', 0) > 0) ? true : false;
		$eventid = (int)$this->in->get('event_id', 0);
		$body = $subject = '';
		if ($bnlEventId){
			$body .= '<p>&nbsp;</p><p><a href="'.$this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($eventid)), $eventid, false, true).'">'.$this->pdh->get('calendar_events', 'html_date', array($eventid)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($eventid)).': '.$this->pdh->get('calendar_events', 'name', array($eventid)).'</a></p>';
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
			'DD_TEMPLATE' => new hdropdown('templates', array('options' => $arrTempl, 'value' => $this->in->get('template', ''), 'js' => 'onchange="window.location=\'manage_massmail.php'.$this->SID.'&event_id='.$eventid.'&template=\'+this.value"')),
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('massmail_send'),
			'template_file'		=> 'admin/manage_massmail.html',
			'display'			=> true)
		);
	}
}
registry::register('Manage_massmail');
?>