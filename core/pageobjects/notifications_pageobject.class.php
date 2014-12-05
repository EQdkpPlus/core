<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2009
 * Date:		$Date: 2014-07-20 19:43:25 +0200 (So, 20 Jul 2014) $
 * -----------------------------------------------------------------------
 * @author		$Author: wallenium $
 * @copyright	2006-2011 EQdkp-Plus Developer notifications
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev: 14491 $
 *
 * $Id: notifications_pageobject.class.php 14491 2014-07-20 17:43:25Z wallenium $
 */

class notifications_pageobject extends pageobject {
	public function __construct() {
		$handler = array(
			'markread'		=> array('process' => 'process_ajax_markread'),
			'markallread'	=> array('process' => 'process_ajax_markallread'),
			'redirect'		=> array('process' => 'process_redirect'),
			'load'			=> array('process' => 'process_load_notifications'),
		);
		
		
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
	
	public function process_load_notifications(){
		if(!$this->user->is_signedin()) exit;
		
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
		foreach($arrPersistent as $arrNotification){
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