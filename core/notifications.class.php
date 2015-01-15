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

class notifications extends gen_class {

	public static $dependencies = array('pm');
	
	private $arrPersistentNotifications = array();
	
	
	public function add_persistent($strType, $strMessage, $strLink, $intPrio=0, $strIcon='', $intCount=1){
		if ($strType != ""){
			$blnHasAbo = $this->pdh->get('user', 'notification_abo', array($strType, $this->user->id));
		} else $blnHasAbo = true;

		if ($blnHasAbo){
			$this->arrPersistentNotifications[] = array(
				'id'			=> 0,
				'ids'			=> 0,
				'prio'			=> $intPrio,
				'type'			=> 'persistent',	
				'name'			=> $strMessage,
				'time'			=> false,
				'link'			=> $strLink,
				'count'			=> $intCount,
				'icon'			=> $strIcon,
				'persistent'	=> true,
			);
		}
	}
	
	public function add($strType, $intDatasetID, $strFromUsername, $strLink, $intUserID=false, $strAdditionalData="", $intCategoryID=false){
		
		if ($intUserID === false){
			if ($strType === 'comment_new_article'){
				if ($intCategoryID === false) return false;
				$arrUsers = $this->pdh->get('user', 'notification_articlecategory_abos', array($intCategoryID));
			} else {
				$arrUsers = $this->pdh->get('user', 'notification_abos', array($strType));
			}
			
			foreach($arrUsers as $intUserID){
				if ((int)$intUserID === $this->user->id) continue;
				
				$this->pdh->put('notifications', 'add', array($strType, $intUserID, $strFromUsername, $intDatasetID, $strLink, $strAdditionalData));
			}
			$this->pdh->process_hook_queue();
			
		} else {
			if ($strType === 'comment_new_article'){
				if ($intCategoryID === false) return false;
				
				if (is_array($intUserID)){
					foreach($intUserID as $intUID){
						if ((int)$intUID === $this->user->id) continue;
						$blnHasAbo = $this->pdh->get('user', 'notification_articlecategory_abo', array($intCategoryID, $intUID));
							
						if ($blnHasAbo){
							$this->pdh->put('notifications', 'add', array($strType, $intUID, $strFromUsername, $intDatasetID, $strLink, $strAdditionalData));
						}
					}
					$this->pdh->process_hook_queue();
					
				} else {
					$blnHasAbo = $this->pdh->get('user', 'notification_articlecategory_abo', array($intCategoryID, $intUserID));
					
					if ($blnHasAbo && intval($intUserID) !== $this->user->id){
						$this->pdh->put('notifications', 'add', array($strType, $intUserID, $strFromUsername, $intDatasetID, $strLink, $strAdditionalData));
						$this->pdh->process_hook_queue();
					}
					
				}
	
			} else {
				if (is_array($intUserID)){
					foreach($intUserID as $intUID){
						if ((int)$intUID === $this->user->id) continue;
						
						$blnHasAbo = $this->pdh->get('user', 'notification_abo', array($strType, $intUID));
						if ($blnHasAbo){
							$this->pdh->put('notifications', 'add', array($strType, $intUID, $strFromUsername, $intDatasetID, $strLink, $strAdditionalData));
						}
					}
					$this->pdh->process_hook_queue();
					
				} else {
				
					$blnHasAbo = $this->pdh->get('user', 'notification_abo', array($strType, $intUserID));
					if ($blnHasAbo && intval($intUserID) !== $this->user->id){
						$this->pdh->put('notifications', 'add', array($strType, $intUserID, $strFromUsername, $intDatasetID, $strLink, $strAdditionalData));
						$this->pdh->process_hook_queue();
					}
				}
			}
		}	
	}

	public function getPersistentNotifications(){
		return $this->arrPersistentNotifications;
	}
	
	public function getAllUserNotifications(){
		$arrNotifications = $arrTypes = array();
		$arrUserNotifications = $this->pdh->get('notifications', 'all_notifications_for_user', array($this->user->id));
		
		foreach($arrUserNotifications as $intNotificationID){
			$strType = $this->pdh->get('notifications', 'type', array($intNotificationID));
			$arrNotificationType = $this->pdh->get('notification_types', 'data', array($strType));
			if (!$arrNotificationType) $this->pdh->put('notifications', 'delete', array($intNotificationID));
				
			$arrNotifications[] = array(
					'id'			=> $intNotificationID,
					'ids'			=> $intNotificationID,
					'prio'			=> $arrNotificationType['prio'],
					'type'			=> $strType,
					'category'		=> $arrNotificationType['category'],
					'name'			=> $this->parseLang($arrNotificationType['name'], $this->pdh->get('notifications', 'username', array($intNotificationID)), $this->pdh->get('notifications', 'additional_data', array($intNotificationID))),
					'time'			=> $this->pdh->get('notifications', 'time', array($intNotificationID)),
					'link'			=> $this->pdh->get('notifications', 'link', array($intNotificationID)),
					'count'			=> 1,
					'icon'			=> $arrNotificationType['icon'],
					'read'			=> $this->pdh->get('notifications', 'read', array($intNotificationID))
			);
		}
		
		//Merge with persistent Notifications
		$arrNotifications = array_merge($this->arrPersistentNotifications, $arrNotifications);
		
		return $arrNotifications;
	}
	
	public function createNotifications(){
		$arrNotifications = $arrTypes = array();
		$arrUserNotifications = $this->pdh->get('notifications', 'notifications_for_user', array($this->user->id));
		foreach($arrUserNotifications as $intNotificationID){
			$strType = $this->pdh->get('notifications', 'type', array($intNotificationID));
			if (!isset($arrTypes[$strType])) $arrTypes[$strType] = array();
			$arrTypes[$strType][] = $intNotificationID;
		}
		
		$arrDone = array();
		foreach($arrUserNotifications as $intNotificationID){
			$strType = $this->pdh->get('notifications', 'type', array($intNotificationID));
			$arrNotificationType = $this->pdh->get('notification_types', 'data', array($strType));
			if (!$arrNotificationType) $this->pdh->put('notifications', 'delete', array($intNotificationID));
			if (in_array($intNotificationID, $arrDone)) continue;
			
			$blnGroup = ($arrNotificationType['group']) ? true : false;
			$intGroupAt = $arrNotificationType['group_at'];
			if ($blnGroup && count($arrTypes[$strType]) >= $intGroupAt && $intGroupAt > 0){
				$arrGroupedNotifications = $this->groupNotifications($arrTypes[$strType], $arrNotificationType);
				$arrDone = array_merge($arrDone, $arrTypes[$strType]);
				
				foreach($arrGroupedNotifications as $arrNotification){
					$arrNotifications[] = $arrNotification;
				}
			} else {
				$arrNotifications[] = array(
					'id'			=> $intNotificationID,
					'ids'			=> $intNotificationID,
					'prio'			=> $arrNotificationType['prio'],
					'type'			=> $strType,
					'category'		=> $arrNotificationType['category'],	
					'name'			=> $this->parseLang($arrNotificationType['name'], $this->pdh->get('notifications', 'username', array($intNotificationID)), $this->pdh->get('notifications', 'additional_data', array($intNotificationID))),
					'time'			=> $this->pdh->get('notifications', 'time', array($intNotificationID)),
					'link'			=> $this->pdh->get('notifications', 'link', array($intNotificationID)),
					'count'			=> 1,
					'icon'			=> $arrNotificationType['icon'],
				);
			}
		}
		
		//Merge with persistent Notifications
		$arrNotifications = array_merge($this->arrPersistentNotifications, $arrNotifications);
		
		return $this->createNotificationsHTML($arrNotifications);
	}
	
	private function createNotificationsHTML($arrNotifications){
		$strOut = "";
		$arrCount = array(0 => 0, 1 => 0, 2 => 0);
		
		if (count($arrNotifications) === 0){
			$strOut .= $this->user->lang('notification_none');
		} else {
		
			foreach($arrNotifications as $arrData){
				$arrCount[$arrData['prio']] += $arrData['count'];
				$strIcon = ($arrData['icon'] != "") ? $this->core->icon_font($arrData['icon']).' ' : '';
				$strLink = (isset($arrData['persistent'])) ? $arrData['link'] : $this->routing->build('Notifications').'&redirect='.$arrData['ids'];
				$strOut .= '<li class="prio_'.$arrData['prio'].'" data-ids="'.$arrData['ids'].'" data-count="'.$arrData['count'].'"><a href="'.$strLink.'">'.$strIcon.$arrData['name'].'</a>';
				if (!isset($arrData['persistent'])){
					$strOut .= '<div class="clear"></div><div class="floatLeft time">'.$this->time->nice_date($arrData['time']).'</div> <div class="floatRight"><i class="fa fa-check hand notification-markasread"></i></div><div class="clear"></div>';
				}
				$strOut .= '</li>';
			}
		}
		
		return array('count0' => $arrCount[0], 'count1' => $arrCount[1], 'count2' => $arrCount[2], 'count' => $arrCount[0]+$arrCount[1]+$arrCount[2], 'html' => $strOut);
	}
	
	public function groupNotifications($arrNotificationIDs, $arrNotificationType){
		$arrGrouped = array();
		$arrOut = array();
		foreach($arrNotificationIDs as $intNotificationID){
			$strLink = $this->pdh->get('notifications', 'link', array($intNotificationID));
			$strHash = md5($strLink);
			if (!isset($arrGrouped[$strHash])) $arrGrouped[$strHash] = array();
			$arrGrouped[$strHash][] = $intNotificationID;
		}
		
		foreach($arrGrouped as $strHash => $arrGroupedN){
			$arrUsers = array();
			foreach($arrGroupedN as $intNotificationID){
				$arrUsers[] = $this->pdh->get('notifications', 'username', array($intNotificationID));
			}

			$arrUsers = array_unique($arrUsers);
			$intFirstID = array_shift($arrGroupedN);

			if (count($arrUsers) > 2){
				$arrNewUser = array();
				$arrNewUser[] = array_shift($arrUsers);
				$arrNewUser[] = array_shift($arrUsers);
				
				$strAndMore = sprintf($this->user->lang('notification_and_more'), (count($arrUsers)));
				
				$strUsernames = implode(', ', $arrNewUser).$strAndMore;
				$strLang = $arrNotificationType['group_name'];
			} elseif (count($arrUsers) === 2){
				$strUsernames = implode(' & ', $arrUsers);
				$strLang = $arrNotificationType['group_name'];
			} elseif (count($arrUsers) === 1){
				$strUsernames = implode('', $arrUsers);
				$strLang = $arrNotificationType['name'];
			} else {
				$strUsernames = implode(', ', $arrUsers);
				$strLang = $arrNotificationType['group_name'];
			}
			
						
			$arrOut[] = array(
				'id'	  		=> $intFirstID,
				'grouped' 		=> true,
				'ids'	  		=> $intFirstID.','.implode(',', $arrGroupedN),
				'name'	  		=> $this->parseLang($strLang, $strUsernames, $this->pdh->get('notifications', 'additional_data', array($intFirstID)), (count($arrGroupedN)+1)),
				'prio'			=> $arrNotificationType['prio'],
				'type'			=> $arrNotificationType['id'],
				'category'		=> $arrNotificationType['category'],
				'link'			=> $this->pdh->get('notifications', 'link', array($intFirstID)),
				'time'			=> $this->pdh->get('notifications', 'time', array($intFirstID)),
				'count'			=> 1,
				'icon'			=> $arrNotificationType['icon'],
			);
		}
		
		return $arrOut;
	}
	
	private function parseLang($strName, $strUsername, $strAdditionalDatas, $intCount=1){
		$strLang = $this->user->lang($strName);
		$strLang = str_replace('{PRIMARY}', $strUsername, $strLang);
		$strLang = str_replace('{ADDITIONAL}', $strAdditionalDatas, $strLang);
		$strLang = str_replace('{COUNT}', $intCount, $strLang);
		return $strLang;
	}

	public function deleteNotification($strType, $intDatasetID){
		$this->pdh->put('notifications', 'delete_by_type_and_recordset', array($strType, $intDatasetID));
		$this->pdh->process_hook_queue();
	}
	
	public function addNotificationType($strID, $strLanguageVar, $strCategory, $intPrio=0, $blnDefault=0, $blnGroup=false, $strGroupLangVar="", $intGroupAt=3, $strIcon=""){
		if (!$this->pdh->get('notification_types', 'check_existing_type', array($strID))){
			$blnResult = $this->pdh->put('notifications', 'add_type', array($strID, $strLanguageVar, $strCategory, $intPrio, $blnDefault, $blnGroup, $strGroupLangVar, $intGroupAt, $strIcon));
			$this->pdh->process_hook_queue();
			return $blnResult;
		}
		return false;
	}
	
	public function deleteNotificationType($strID){
		$this->pdh->put('notifications', 'del_type', array($strID));
		$this->pdh->process_hook_queue();
	}
	
	public function cleanup($intDays){
		$intTime = $this->time->time;
		$intTime = $intTime - ($intDays * 3600*24);
		
		$this->pdh->put('notifications', 'cleanup', array($intTime));
		$this->pdh->process_hook_queue();
	}
}
?>