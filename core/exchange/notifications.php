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

if (!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if (!class_exists('exchange_notifications')){
	class exchange_notifications extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');

		public function get_notifications($params, $arrBody){
			if($this->user->is_signedin()){
				$intUserID = $this->user->id;

				$arrNotifications = $this->ntfy->getAllUserNotifications();

				$intNumber = (intval($params['get']['number']) > 0) ?  intval($params['get']['number']) : 50;
				$intStart = (intval($params['get']['start']) > 0) ?  intval($params['get']['start']) : 0;

				$intTotalCount = count($arrNotifications);
				$arrNotifications = $this->pdh->limit($arrNotifications, $intStart, $intNumber);

				$arrOut = array();

				foreach($arrNotifications as $arrNotification){
					if (isset($arrNotification['persistent'])) {
						$arrOut[] = array(
								'persistent' => 1,
								'name'		=> $arrNotification['name'],
								'prio'		=> $arrNotification['prio'],
								'icon'		=> ($arrNotification['icon'] != "") ? $this->core->icon_font($arrNotification['icon']).' ' : '',
								'class' 	=> 'unread',
								'link'		=> $arrNotification['link'],
								'time'		=> 0,
								'read'		=> 0,
								'id'		=> 0,
						);
					} else {
						$arrOut[] = array(
								'persistent' => 0,
								'name'		=> $arrNotification['name'],
								'prio'		=> $arrNotification['prio'],
								'icon'		=> ($arrNotification['icon'] != "") ? $this->core->icon_font($arrNotification['icon']).' ' : '',
								'class' 	=> ($arrNotification['read']) ? 'read' : 'unread',
								'time'		=> $arrNotification['time'],
								'link'		=> (!$arrNotification['read']) ? $this->env->buildlink().$this->routing->build('Notifications', false, false, false, true).'&redirect='.$arrNotification['id'] : $this->env->link.$arrNotification['link'],
								'read'		=> ($arrNotification['read']) ? true : false,
								'id'		=> $arrNotification['id'],
						);
					}
				}

				return $arrOut;
			} else {
				return $this->pex->error('access denied');
			}

		}
	}
}
