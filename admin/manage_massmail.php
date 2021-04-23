<?php
/*	Project:	EQdkp-Plus
 *	Package:	EQdkp-plus
 *	Link:		http://eqdkp-plus.eu
 *
 *	Copyright (C) 2006-2016 EQdkp-Plus Developer Team
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
			'load_template'	=> array('process' => 'process_load_template'),
			'send'	=> array('process' => 'process_send', 'csrf' => true),
			'submit' => array('process' => 'submit', 'csrf' => true),
			'add_template' => array('process' => 'add_template', 'csrf' => true),
			'save_template' => array('process' => 'save_template', 'csrf' => true),
			'delete_template' => array('process' => 'delete_template', 'csrf' => true),
			);
		parent::__construct(false, $handler, array('user', 'name'), null, 'user_id[]');

		$this->process();
	}

	public function process_load_template(){
		$strTemplatename = $this->in->get('template');
		$strTemplatename = preg_replace("/[^a-zA-Z0-9_-]/", "", $strTemplatename);

		$strLanguageFile = $this->root_path.'language/'.$this->user->data['user_lang'].'/email/massmail_'.$strTemplatename.'.html';
		$strDataFile =  $this->pfh->FolderPath('massmails', 'eqdkp').'massmail_'.$strTemplatename.'.html';
		if(is_file($strDataFile)){
			$strContent = file_get_contents($strDataFile);
		}elseif(is_file($strLanguageFile)){
			$strContent = file_get_contents($strLanguageFile);
		} else {
			$strContent = "";
		}

		$body = is_utf8($strContent) ? $strContent : utf8_encode($strContent);
		if (preg_match('#{SUBJECT}(.*?){/SUBJECT}#', $body, $matches)){
			$subject = $matches[1];
			$body = str_replace($matches[0], '', $body);
		}

		header('Content-type: text/html; charset=utf-8');
		echo 'ok|;|;|;'.$subject.'|;|;|;'.$body;

		exit;
	}

	public function delete_template(){
		$strTemplatename = $this->in->get('template');
		$strTemplatename = preg_replace("/[^a-zA-Z0-9_-]/", "", $strTemplatename);

		$strDataFile =  $this->pfh->FolderPath('massmails', 'eqdkp').'massmail_'.$strTemplatename.'.html';
		if(is_file($strDataFile)){
			$this->pfh->Delete($strDataFile);
		}
		$strLanguageFile = $this->root_path.'language/'.$this->user->data['user_lang'].'/email/massmail_'.$strTemplatename.'.html';
		if(is_file($strLanguageFile)){
			$this->pfh->Delete($strLanguageFile);
		}

		$this->display(true);
	}

	public function add_template(){
		$strTemplatename = $this->in->get('templatename');
		$strTemplatename = preg_replace("/[^a-zA-Z0-9_-]/", "", $strTemplatename);
		$strSubject = $this->in->get('subject');
		$strBody = $this->in->get('message', '', 'raw');
		$strOut = $strBody;
		if($strSubject != ""){
			$strOut = "{SUBJECT}".$strSubject."{/SUBJECT}".$strOut;
		}

		$strFilename = $this->pfh->FolderPath('massmails', 'eqdkp').'massmail_'.$strTemplatename.'.html';
		$this->pfh->putContent($strFilename, $strOut);

		//Get all Templates, create new Dropdown, select Template
		$arrTemplates = sdir($this->root_path.'language/'.$this->user->data['user_lang'].'/email', 'massmail_*.html');
		$arrTempl = array('' => $this->user->lang('massmail_select_template').'...');
		if (is_array($arrTemplates) && count($arrTemplates) > 0){
			foreach($arrTemplates as $file){
				$file = preg_replace('/[^a-zA-Z0-9 -]/', '', $file);
				$file = str_replace(array('massmail', 'html'), array('', ''), $file);
				$arrTempl[$file] = $file;
			}
		}

		$arrTemplates = sdir($this->pfh->FolderPath('massmails', 'eqdkp'), 'massmail_*.html');
		if (is_array($arrTemplates) && count($arrTemplates) > 0){
			foreach($arrTemplates as $file){
				$file = preg_replace('/[^a-zA-Z0-9 -]/', '', $file);
				$file = str_replace(array('massmail', 'html'), array('', ''), $file);
				$arrTempl[$file] = $file;
			}
		}

		$dd = (new hdropdown('template', array('options' => $arrTempl, 'value' => $strTemplatename, 'js' => 'onchange="load_template()"')))->output();

		header('Content-type: text/html; charset=utf-8');
		echo 'ok||'.$strTemplatename.'||'.$dd;

		exit;
	}

	public function save_template(){
		$strTemplatename = $this->in->get('templatename');
		$strTemplatename = preg_replace("/[^a-zA-Z0-9_]/", "", $strTemplatename);
		$strSubject = $this->in->get('subject');
		$strBody = $this->in->get('message', '', 'raw');
		$strOut = $strBody;
		if($strSubject != ""){
			$strOut = "{SUBJECT}".$strSubject."{/SUBJECT}".$strOut;
		}

		$strFilename = $this->pfh->FolderPath('massmails', 'eqdkp').'massmail_'.$strTemplatename.'.html';
		$this->pfh->putContent($strFilename, $strOut);

		header('Content-type: text/html; charset=utf-8');
		echo "ok";

		exit;
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
				foreach($arrGalleryObjects[4] as $key=>$val){
					$strText = str_replace($arrGalleryObjects[0][$key], "", $strText);
				}
			}

			//Replace Raidloot
			$arrRaidlootObjects = array();
			preg_match_all('#<p(.*)class="system-raidloot"(.*) data-id="(.*)"(.*) data-chars="(.*)">(.*)</p>#iU', $strText, $arrRaidlootObjects, PREG_PATTERN_ORDER);
			if (count($arrRaidlootObjects[0])){
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

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('massmail_send'),
			'template_file'		=> 'admin/manage_massmail_data.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('massmail'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}

	public function process_send(){
		$body = $this->crypt->decrypt($this->in->get('message'));
		$subject = $this->crypt->decrypt($this->in->get('subject'));
		$userid = $this->in->get('userid', 0);

		header('Content-type: application/json; charset=utf-8');

		if($userid === 0){
			echo json_encode(array('status' => 'end', 'name' => ''));
			exit;
		}

		//Replace Placeholders
		$arrSearch = array('{DKP_NAME}', '{EQDKP_LINK}', '{DATE}', '{USERNAME}');
		$arrReplace = array($this->config->get('dkp_name'), $this->env->link, $this->time->user_date($this->time->time), $this->pdh->get('user', 'name', array($userid)));
		$body = str_replace($arrSearch, $arrReplace, $body);

		$subject = str_replace($arrSearch, $arrReplace, $subject);

		if ($this->in->get('event_id', 0) > 0){
			$event_id = $this->in->get('event_id', 0);
			$arrSearch = array('{EVENT_NAME}', '{EVENT_DATE}', '{EVENT_LINK}', '{EVENT_URL}');
			$arrReplace = array($this->pdh->get('calendar_events', 'name', array($event_id)), $this->pdh->get('calendar_events', 'html_date', array($event_id)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($event_id)), '<a href="'.$this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($event_id)), $event_id, false, true).'">'.$this->pdh->get('calendar_events', 'html_date', array($event_id)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($event_id)).': '.$this->pdh->get('calendar_events', 'name', array($event_id)).'</a>', $this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($event_id)), $event_id, false, true));

			$body = str_replace($arrSearch, $arrReplace, $body);
			$subject = str_replace($arrSearch, $arrReplace, $subject);
		}

		$result = $this->messenger->sendMessage($this->in->get('method', 'email'), $userid, $subject, $body);

		if($result){
			echo json_encode(array('status' => 'ok', 'name' => $this->pdh->get('user', 'name', array($userid))));
			exit;
		}

		echo json_encode(array('status' => 'error', 'name' => $this->pdh->get('user', 'name', array($userid))));
		exit;
	}

	public function submit(){
		if ($this->in->get('body', '', 'raw') != ""){
			$arrRecipients = array();

			$arrAllUsers = $this->pdh->get('user', 'id_list');

			//Usergroups
			if (count($this->in->getArray('usergroups', 'int')) > 0){
				foreach ($this->in->getArray('usergroups', 'int') as $key => $groupid){
					$arrGroupMembers = $this->pdh->get('user_groups_users', 'user_list', array($groupid));
					foreach($arrGroupMembers as $userid){
						if(!in_array((int)$userid, $arrAllUsers)) continue;

						$arrRecipients[] = (int)$userid;
					}
				}
			}

			//Normal User IDs
			if (count($this->in->getArray('user', 'int')) > 0){
				foreach ($this->in->getArray('user', 'int') as $key => $userid){
					if(!in_array((int)$userid, $arrAllUsers)) continue;

					$arrRecipients[] = (int)$userid;
				}
			}

			//Status for Calenderevent ID
			if (count($this->in->getArray('status', 'int')) > 0 && $this->in->get('event_id', 0) > 0){
				$eventid = $this->in->get('event_id', 0);
				foreach ($this->in->getArray('status', 'int') as $key => $statusid){
					//Have Raidgroups been selected?
					$arrRaidgroups = $this->in->getArray('raidgroups', 'int');
					if (count($arrRaidgroups) > 0){
						$arrMembers = array();
						foreach($arrRaidgroups as $intRaidgroupID){
							$arrMembers = array_merge($arrMembers, $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($eventid, $statusid, $intRaidgroupID)));
						}

					} else {
						$arrMembers = $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($eventid, $statusid));
					}

					if (is_array($arrMembers)){
						foreach ($arrMembers as $memberid){
							$userID = (int)$this->pdh->get('member', 'userid', array($memberid));
							if(!in_array($userID, $arrAllUsers)) continue;

							if ($userID != 0) $arrRecipients[] = (int)$this->pdh->get('member', 'userid', array($memberid));
						}
					}
				}
			} elseif (count($this->in->getArray('raidgroups', 'int')) > 0 && $this->in->get('event_id', 0) > 0){
				//Calenderevent Raidgroups for EventID

				$eventid = $this->in->get('event_id', 0);
				$arrStatus = $this->user->lang('raidevent_raid_status');
				foreach ($this->in->getArray('raidgroups', 'int') as $key => $intRaidgroupID){
					$arrMembers = array();
					foreach($arrStatus as $statusid => $statusname){
						$arrMembers = array_merge($arrMembers, $this->pdh->get('calendar_raids_attendees', 'attendee_stats', array($eventid, $statusid, $intRaidgroupID)));
					}

					if (is_array($arrMembers)){
						foreach ($arrMembers as $memberid){
							$userID = (int)$this->pdh->get('member', 'userid', array($memberid));
							if(!in_array($userID, $arrAllUsers)) continue;

							if ($userID != 0) $arrRecipients[] = (int)$this->pdh->get('member', 'userid', array($memberid));
						}
					}
				}
			}



			$arrRecipients = array_unique($arrRecipients);

			if (count($arrRecipients) > 0) {
				$this->tpl->assign_vars(array(
					'S_SEND'			=> true,
					'ENCR_MESSAGE'		=> $this->crypt->encrypt($this->in->get('body', '', 'raw')),
					'ENCR_SUBJECT'		=> $this->crypt->encrypt($this->in->get('subject', '')),
					'METHOD'			=> $this->in->get('method', 'email'),
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
				$this->display();
			}

		} else {
			$this->core->message($this->user->lang('adduser_send_mail_error_fields'), $this->user->lang('error'), 'red');
			$this->display();
		}

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('massmail_send'),
			'template_file'		=> 'admin/manage_massmail_send.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('massmail'), 'url'=>' '],
			],
			'display'			=> true
		]);

		return true;
	}

	public function display($blnIgnoreTemplate=false){
		$editor = registry::register('tinyMCE');
		$editor->editor_normal(array('autoresize' => true, 'relative_urls'	=> false, 'remove_host' => false));

		$bnlEventId = ($this->in->get('event_id', 0) > 0) ? true : false;
		$eventid = (int)$this->in->get('event_id', 0);
		$body = $subject = '';
		if ($bnlEventId){
			$body .= '<p>&nbsp;</p><p><a href="'.$this->env->link.$this->routing->build('calendarevent', $this->pdh->get('calendar_events', 'name', array($eventid)), $eventid, false, true).'">'.$this->pdh->get('calendar_events', 'html_date', array($eventid)).' '.$this->pdh->get('calendar_events', 'html_time_start', array($eventid)).': '.$this->pdh->get('calendar_events', 'name', array($eventid)).'</a></p>';
			$this->tpl->assign_vars(array(
				'DD_STATUS'		=> (new hmultiselect('status', array('options' => $this->user->lang('raidevent_raid_status'), 'value' => $this->in->getArray('status', 'int'), 'width' => 400)))->output(),
			));

			$arrRaidgroups = $this->pdh->aget('raid_groups', 'name', false, array($this->pdh->get('raid_groups', 'id_list')));
			if(count($arrRaidgroups) > 1){
				$this->tpl->assign_vars(array(
					'S_RAIDGROUPS'	=> true,
					'DD_RAIDGROUPS'	=> (new hmultiselect('raidgroups', array('options' => $arrRaidgroups, 'value' => $this->in->getArray('raidgroups', 'int'), 'width' => 400)))->output(),
				));

			}
		}

		//Only load the template file, if submit button is not pressed
		if (!$blnIgnoreTemplate && $this->in->get('template') != "" && !$this->in->exists('submit') && !$this->in->exists('delete_template')){
			$file = preg_replace('/[^a-zA-Z0-9_-]/', '', $this->in->get('template'));
			$strLanguageFile = $this->root_path.'language/'.$this->user->data['user_lang'].'/email/massmail_'.$file.'.html';
			$strDataFile =  $this->pfh->FolderPath('massmails', 'eqdkp').'massmail_'.$file.'.html';
			if(is_file($strDataFile)){
				$strContent = file_get_contents($strDataFile);
			}elseif(is_file($strLanguageFile)){
				$strContent = file_get_contents($strLanguageFile);
			} else {
				$strContent = "";
			}

			$body = is_utf8($strContent) ? $strContent : utf8_encode($strContent);
			if (preg_match('#{SUBJECT}(.*?){/SUBJECT}#', $body, $matches)){
				$subject = $matches[1];
				$body = str_replace($matches[0], '', $body);
			}

		}

		$strTemplate = ($blnIgnoreTemplate) ? "" : $this->in->get('template');
		$blnHaveTemplate = ($strTemplate != "") ? true : false;

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
		$arrTemplates = sdir($this->pfh->FolderPath('massmails', 'eqdkp'), 'massmail_*.html');
		if (is_array($arrTemplates) && count($arrTemplates) > 0){
			foreach($arrTemplates as $file){
				$file = preg_replace('/[^a-zA-Z0-9 -]/', '', $file);
				$file = str_replace(array('massmail', 'html'), array('', ''), $file);
				$arrTempl[$file] = $file;
			}
		}

		$this->tpl->assign_vars(array(
			'DD_METHOD' 				=> (new hdropdown('method', array('options' => $this->messenger->getAvailableMessenger(), 'value' => $this->in->get('method', 'email'))))->output(),
			'DD_GROUPS'					=> (new hmultiselect('usergroups', array('options' => $arrUserGroups, 'value' => $this->in->getArray('usergroups', 'int'), 'width' => 400, 'filter' => true)))->output(),
			'DD_USERS'					=> (new hmultiselect('user', array('options' => $this->pdh->aget('user', 'name', 0, array($this->pdh->get('user', 'id_list'))), 'value' => $this->in->getArray('user', 'int'), 'width' => 400, 'filter' => true)))->output(),
			'SUBJECT'					=> ($this->in->exists('subject')) ? $this->in->get('subject', '') : $subject,
			'BODY'						=> ($this->in->exists('body')) ? $this->in->get('body', '', 'raw') : $body,
			'EVENT_ID'					=> ($bnlEventId) ? '&amp;event_id='.$eventid : '',
			'S_EVENT_ID'				=> $bnlEventId,
			'MM_TEMPLATE_NAME'			=> $strTemplate,
			'NUM_EVENT_ID' 				=> intval($eventid),
			'S_IS_TEMPLATE'				=> $blnHaveTemplate,
			'CSRF_ADDTEMPLATE_TOKEN'	=> $this->CSRFGetToken('add_template'),
			'CSRF_SAVETEMPLATE_TOKEN'	=> $this->CSRFGetToken('save_template'),
			'DD_TEMPLATE'				=> (new hdropdown('template', array('options' => $arrTempl, 'value' => $strTemplate, 'js' => 'onchange="load_template()"')))->output(),
		));

		$this->core->set_vars([
			'page_title'		=> $this->user->lang('massmail_send'),
			'template_file'		=> 'admin/manage_massmail.html',
			'page_path'			=> [
				['title'=>$this->user->lang('menu_admin_panel'), 'url'=>$this->root_path.'admin/'.$this->SID],
				['title'=>$this->user->lang('massmail'), 'url'=>' '],
			],
			'display'			=> true
		]);
	}
}
registry::register('Manage_massmail');
