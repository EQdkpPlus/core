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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

include_once(registry::get_const('root_path').'maintenance/includes/sql_update_task.class.php');

class update_2101 extends sql_update_task {
	public $author			= 'GodMod';
	public $version			= '2.1.0.1'; //new plus-version
	public $ext_version		= '2.1.0'; //new plus-version
	public $name			= '2.1.0 Update 2 Alpha 1';
	
	public function __construct(){
		parent::__construct();

		$this->langs = array(
			'english' => array(
				'update_2101'	=> 'EQdkp Plus 2.1.0 Update 2',
				'update_function'=> 'Perform some Update for Notifications',
				),
			'german' => array(
				'update_2101'	=> 'EQdkp Plus 2.1.0 Update 2',
				'update_function'=> 'Führe einige Updates für Benachrichtigungen aus',
			),
		);
		
		// init SQL querys
		$this->sqls = array();
	}
	
	public function update_function(){
		$this->ntfy->addNotificationType('calendarevent_new','notification_calendarevent_new', 'calendarevent', 0, 1, 0, '', 0, 'fa-calendar');
		
		$arrUsers = $this->pdh->get('user', 'id_list', array());
		foreach($arrUsers as $intUserID){
			$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($intUserID));
			if(!isset($arrNotificationSettings['ntfy_comment_new_article'])) continue;
			
			$arrNotificationSettings['ntfy_comment_new_article_categories'] = $arrNotificationSettings['ntfy_comment_new_article'];
			$arrNotificationSettings['ntfy_comment_new_article'] = 1;
			
			$this->pdh->put('user', 'update_user', array($intUserID, array('notifications' => serialize($arrNotificationSettings)), false, false));
		}

		// update the js date settings
		$this->config->set('default_jsdate_nrml', $this->user->lang('style_jsdate_nrml'));
		$this->config->set('default_jsdate_short', $this->user->lang('style_jsdate_short'));

		return true;
	}
}


?>