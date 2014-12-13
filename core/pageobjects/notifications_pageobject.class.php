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

class notifications_pageobject extends pageobject {
	public function __construct() {
		$handler = array(
			'markread'		=> array('process' => 'process_ajax_markread'),
			'markallread'	=> array('process' => 'process_ajax_markallread'),
			'delete'		=> array('process' => 'process_ajax_delete'),
			'redirect'		=> array('process' => 'process_redirect'),
			'load'			=> array('process' => 'process_load_notifications'),
		);
		if(!$this->user->is_signedin()) $this->user->check_auth('u_something');
		
		parent::__construct(false, $handler, array());
		$this->process();
	}
	
	public function process_ajax_markread(){
		if(!$this->user->is_signedin()) exit;
		
		$strIDs = $this->in->get('ids');
		$arrIDs = explode(',', $strIDs);
		
		foreach($arrIDs as $intID){
			$intID = intval($intID);
			$this->pdh->put('notifications', 'mark_as_read', array($intID));
		}
		$this->pdh->process_hook_queue();
		exit;
	}
	
	public function process_ajax_markallread(){
		if(!$this->user->is_signedin()) exit;
		$this->pdh->put('notifications', 'mark_all_as_read', array($this->user->id));
		$this->pdh->process_hook_queue();
		exit;
	}
	
	public function process_ajax_delete(){
		if(!$this->user->is_signedin()) exit;
		
		$strIDs = $this->in->get('ids');
		$arrIDs = explode(',', $strIDs);
		
		foreach($arrIDs as $intID){
			$intID = intval($intID);
			$intUserID = $this->pdh->get('notifications', 'user_id', array($intID));
			if ($intUserID === $this->user->id) $this->pdh->put('notifications', 'delete', array($intID));
		}
		$this->pdh->process_hook_queue();
		exit;
	}
	
	public function process_load_notifications(){
		if(!$this->user->is_signedin()) exit;
		
		$this->core->notifications();
		
		header('Content-type: text/html; charset=utf-8');
		$arrNotifications = $this->ntfy->createNotifications();
		echo $arrNotifications['html'];
		
		exit;
	}
	
	public function process_redirect(){
		$strIDs = $this->in->get('redirect');
		$arrIDs = explode(',', $strIDs);
		
		foreach($arrIDs as $intID){
			$intID = intval($intID);
			$this->pdh->put('notifications', 'mark_as_read', array($intID));
		}
		
		$this->pdh->process_hook_queue();
		
		$intFirst = intval(array_shift($arrIDs));
		$strLink = $this->pdh->get('notifications', 'link', array($intFirst));
		if (strlen($strLink)){
			redirect($strLink);
			return;
		}

		$this->display();
	}

	public function display(){
		//Cleanup
		$this->ntfy->cleanup(31);
		$this->pdh->process_hook_queue();
		
		$this->core->notifications();
		
		$arrNotifications = $this->ntfy->getAllUserNotifications();
		
		$intTotalCount = count($arrNotifications);
		$intStart = $this->in->get('start',0);
		$intPerPage = 50;
		$arrNotifications = $this->pdh->limit($arrNotifications, $intStart, $intPerPage);
		
		$arrDays = array();
		$arrPersistent = array();
		foreach($arrNotifications as $intKey => $arrNotification){
			if (isset($arrNotification['persistent'])) {
				$arrPersistent[] = $intKey;
			} else {
			
				$strDay = $this->time->user_date($arrNotification['time'], false, false, true, true, true);
				if (!isset($arrDays[$strDay])) $arrDays[$strDay] = array();
				$arrDays[$strDay][] = $intKey;
			}
		}
		
		//Bring Persistent to Template
		foreach($arrPersistent as $intKey){
			$arrNotification = $arrNotifications[$intKey];
			
			$this->tpl->assign_block_vars('persistent_row', array(
					'NAME'	=> $arrNotification['name'],
					'PRIO'	=> $arrNotification['prio'],
					'ICON'	=> ($arrNotification['icon'] != "") ? $this->core->icon_font($arrNotification['icon']).' ' : '',
					'CLASS' => 'unread',
					'LINK'	=> $arrNotification['link'],
			));
		}
		
		//Bring Notifications to Template
		
		foreach($arrDays as $strDay => $arrKeys){		
			$this->tpl->assign_block_vars('day_row', array(
				'DAY'	=> $strDay,
			));
			
			foreach($arrKeys as $intKey){
				$arrNotification = $arrNotifications[$intKey];
				$this->tpl->assign_block_vars('day_row.notification_row', array(
					'NAME'	=> $arrNotification['name'],
					'TIME'	=> $this->time->user_date($arrNotification['time'], true, true),
					'PRIO'	=> $arrNotification['prio'],
					'READ'	=> ($arrNotification['read']) ? true : false,
					'ID'	=> $arrNotification['id'],
					'ICON'	=> ($arrNotification['icon'] != "") ? $this->core->icon_font($arrNotification['icon']).' ' : '',
					'CLASS' => ($arrNotification['read']) ? 'read' : 'unread',
					'LINK'	=> (!$arrNotification['read']) ? $this->routing->build('Notifications').'&redirect='.$arrNotification['id'] : $this->server_path.$arrNotification['link'],
				));
			}	
		}
		
		$this->tpl->assign_vars(array(
			'PAGINATION' => generate_pagination($this->routing->build('Notifications'), $intTotalCount, $intPerPage, $intStart),
			'S_PERSISTENT' => (count($arrPersistent)) ? true : false,
		));

		$this->core->set_vars(array(
			'page_title'		=> $this->user->lang('notifications'),
			'template_file'		=> 'notifications.html',
			'display'			=> true)
		);
	}

}
?>