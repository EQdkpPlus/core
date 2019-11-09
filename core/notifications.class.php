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

if ( !defined('EQDKP_INC') ){
	header('HTTP/1.0 404 Not Found');exit;
}

class notifications extends gen_class {

	public static $dependencies = array('pm');

	private $arrPersistentNotifications = array();

	public function __construct(){
		$this->cleanup_unread();
	}

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

	public function add($strType, $strDatasetID, $strFromUsername, $strLink, $intUserID=false, $strAdditionalData="", $intCategoryID=false, $mixPermission=false){

		if ($intUserID === false){
			if ($strType === 'comment_new_article'){
				if ($intCategoryID === false) return false;
				$arrUsers = $this->pdh->get('user', 'notification_articlecategory_abos', array($intCategoryID, $strDatasetID));
			} else {
				$arrUsers = $this->pdh->get('user', 'notification_abos', array($strType));
			}

			foreach($arrUsers as $intUserID){
				if ((int)$intUserID === $this->user->id) continue;
				if(!$this->check_permission($mixPermission, $intUserID)) continue;

				# Create EQdkp Plus Notification
				$intNotificationID = $this->pdh->put('notifications', 'add', array($strType, $intUserID, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData));
				# And now send by additional method
				$this->sendNofiticationByMethod($intUserID, $intNotificationID, $strType, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData);
			}
			$this->pdh->process_hook_queue();

		} else {
			if ($strType === 'comment_new_article'){
				if ($intCategoryID === false) return false;

				if (is_array($intUserID)){
					foreach($intUserID as $intUID){
						if ((int)$intUID === $this->user->id) continue;
						if(!$this->check_permission($mixPermission, (int)$intUID)) continue;
						$blnHasAbo = $this->pdh->get('user', 'notification_articlecategory_abo', array($intCategoryID, $strDatasetID, $intUID));

						if ($blnHasAbo){
							$intNotificationID = $this->pdh->put('notifications', 'add', array($strType, $intUID, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData));
							$this->sendNofiticationByMethod($intUID, $intNotificationID, $strType, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData);

						}
					}
					$this->pdh->process_hook_queue();

				} else {
					$blnHasAbo = $this->pdh->get('user', 'notification_articlecategory_abo', array($intCategoryID, $strDatasetID, $intUserID));
					if ($blnHasAbo && intval($intUserID) !== $this->user->id && $this->check_permission($mixPermission, (int)$intUserID)){
						$intNotificationID = $this->pdh->put('notifications', 'add', array($strType, $intUserID, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData));
						$this->sendNofiticationByMethod($intUserID, $intNotificationID, $strType, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData);
						$this->pdh->process_hook_queue();
					}

				}

			} else {
				if (is_array($intUserID)){
					foreach($intUserID as $intUID){
						if ((int)$intUID === $this->user->id) continue;
						if(!$this->check_permission($mixPermission, (int)$intUID)) continue;

						$blnHasAbo = $this->pdh->get('user', 'notification_abo', array($strType, $intUID));
						if ($blnHasAbo){
							$intNotificationID = $this->pdh->put('notifications', 'add', array($strType, $intUID, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData));
							$this->sendNofiticationByMethod($intUID, $intNotificationID, $strType, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData);
						}
					}
					$this->pdh->process_hook_queue();

				} else {

					$blnHasAbo = $this->pdh->get('user', 'notification_abo', array($strType, $intUserID));
					if ($blnHasAbo && intval($intUserID) !== $this->user->id && $this->check_permission($mixPermission, $intUserID)){
						$intNotificationID = $this->pdh->put('notifications', 'add', array($strType, $intUserID, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData));
						$this->sendNofiticationByMethod($intUserID, $intNotificationID, $strType, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData);
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

	private function parseLang($strName, $strUsername, $strAdditionalDatas, $intCount=1, $strLang=false){
		$strLang = $this->user->lang($strName, false, true, $strLang);
		$strLang = str_replace('{PRIMARY}', $strUsername, $strLang);
		$strLang = str_replace('{ADDITIONAL}', $strAdditionalDatas, $strLang);
		$strLang = str_replace('{COUNT}', $intCount, $strLang);
		return $strLang;
	}

	public function deleteNotification($strType, $strDatasetID, $intUserID=0){
		if($intUserID > 0){
			$this->pdh->put('notifications', 'delete_by_type_and_recordset_for_user', array($strType, $strDatasetID, $intUserID));
		}else{
			$this->pdh->put('notifications', 'delete_by_type_and_recordset', array($strType, $strDatasetID));
		}
		$this->pdh->process_hook_queue();
	}

	public function addNotificationType($strID, $strLanguageVar, $strCategory, $intPrio=0, $blnDefault=0, $blnGroup=false, $strGroupLangVar="", $intGroupAt=3, $strIcon=""){
		if (!$this->pdh->get('notification_types', 'check_existing_type', array($strID))){
			$blnResult = $this->pdh->put('notification_types', 'add', array($strID, $strLanguageVar, $strCategory, $intPrio, $blnDefault, $blnGroup, $strGroupLangVar, $intGroupAt, $strIcon));
			$this->pdh->process_hook_queue();
			return $blnResult;
		}
		return false;
	}

	public function deleteNotificationType($strID){
		$this->pdh->put('notifications', 'del_type', array($strID));
		$this->pdh->process_hook_queue();
	}

	public function cleanup_read($intDays){
		$intTime = $this->time->time;
		$intTime = $intTime - ($intDays * 3600*24);

		$this->pdh->put('notifications', 'cleanup_read', array($intTime));
		$this->pdh->process_hook_queue();
	}

	public function cleanup_unread(){
		$intDays = 90; //Delete all notifications older than 90 days, even if unread
		$intLastCleanup = $this->config->get('notifications_last_cleanup');
		//Once a day the complete cleanup
		if(!$intLastCleanup || $intLastCleanup < ($this->time->time - (3600*24))){
			$intTime = $this->time->time;
			$intTime = $intTime - ($intDays * 3600*24);
			$this->pdh->put('notifications', 'cleanup_unread', array($intTime));
			$this->pdh->process_hook_queue();
			$this->config->set('notifications_last_cleanup', $this->time->time);
		}
	}

	public function getAvailableNotificationMethods($blnAllMethods=false){
		include_once $this->root_path.'core/notifications/generic_notification.class.php';
		$types = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/notifications/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/notifications/' . $file)) && valid_folder($file)){
					if ($file == 'generic_notification.class.php') continue;

					include_once($this->root_path . 'core/notifications/' . $file);
					$name = substr($file, 0, strpos($file, '.'));
					$classname = $name.'_notification';
					$blnIsAvailable = register($classname)->isAvailable();
					if(!$blnIsAvailable && !$blnAllMethods) continue;
					$static_name = $this->user->lang('notification_type_'.$name);
					$types[$name] = (strlen($static_name)) ? $static_name : ucfirst($name);
				}
			}
		}
		return $types;
	}

	public function getNotificationMethodsAdminSettings(){
		include_once $this->root_path.'core/notifications/generic_notification.class.php';
		$types = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/notifications/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/notifications/' . $file)) && valid_folder($file)){
					if ($file == 'generic_notification.class.php') continue;

					include_once($this->root_path . 'core/notifications/' . $file);
					$name = substr($file, 0, strpos($file, '.'));
					$classname = $name.'_notification';
					$arrAdminSettings = register($classname)->getAdminSettings();
					if(count($arrAdminSettings)){
						$types = array_merge($types, $arrAdminSettings);
					}
				}
			}
		}
		return $types;
	}

	public function getNotificationMethodsUserSettings($blnAllMethods=false){
		include_once $this->root_path.'core/notifications/generic_notification.class.php';
		$types = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/notifications/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/notifications/' . $file)) && valid_folder($file)){
					if ($file == 'generic_notification.class.php') continue;

					include_once($this->root_path . 'core/notifications/' . $file);
					$name = substr($file, 0, strpos($file, '.'));
					$classname = $name.'_notification';
					$objNotificationMethod = register($classname);
					$blnIsAvailable = $objNotificationMethod->isAvailable();
					if(!$blnIsAvailable && !$blnAllMethods) continue;

					$arrUserSettings = $objNotificationMethod->getUserSettings();
					if(count($arrUserSettings)){
						$types = array_merge($types, $arrUserSettings);
					}
				}
			}
		}
		return $types;
	}

	public function sendNofiticationByMethod($intUserID, $intNotificationID, $strType, $strFromUsername, $strDatasetID, $strLink, $strAdditionalData){
		$arrNotificationSettings = $this->pdh->get('user', 'notification_settings', array($intUserID));
		$strNotificationMethod = $arrNotificationSettings['ntfy_'.$strType];
		if(!$strNotificationMethod || $strNotificationMethod == "") return true;

		$strUserLanguage = $this->pdh->get('user', 'lang', array($intUserID));

		$arrNotificationType = $this->pdh->get('notification_types', 'data', array($strType));

		if($this->server_path != "/" && $this->server_path != ""){
			$strLink = str_replace($this->server_path, '', $strLink);
		}
		$strLink = str_replace($this->root_path, '', $strLink);
		$strLink = str_replace('//', '/', $strLink);

		$arrNotification = array(
			'to_userid' 		=> $intUserID,
			'to_username'		=> $this->pdh->get('user', 'name', array($intUserID)),
			'from_username' 	=> $strFromUsername,
			'type' 				=> $strType,
			'link' 				=> $this->env->buildlink().$this->routing->build('Notifications', false, false, false, true).'?redirect='.$intNotificationID,
			'direct_link'		=> $this->env->buildlink().$strLink,
			'additional_data'	=> $strAdditionalData,
			'dataset_id'		=> $strDatasetID,
			'prio'				=> $arrNotificationType['prio'],
			'type_lang'			=> $this->user->lang('user_sett_f_ntfy_'.$strType, false, false, $strUserLanguage),
			'name'				=> $this->parseLang($arrNotificationType['name'], $strFromUsername, $strAdditionalData, 1, $strUserLanguage),
		);

		if(is_file($this->root_path.'core/notifications/'.$strNotificationMethod.'.notification.class.php')){
			include_once($this->root_path.'core/notifications/generic_notification.class.php');
			include_once($this->root_path.'core/notifications/'.$strNotificationMethod.'.notification.class.php');
			$objNotificationMethod = register($strNotificationMethod.'_notification');
			$objNotificationMethod->sendNotification($arrNotification);

			return true;
		}
		return false;
	}

	public function markAsRead($strType, $intUserId, $mixDatasetID){
		$this->pdh->put('notifications', 'mark_as_read_bytype', array($strType, $intUserId, $mixDatasetID));
		$this->pdh->process_hook_queue();
	}

	private function check_permission($mixPermission, $intUserID){
		if(!is_array($mixPermission)){
			if($mixPermission != "") return $this->user->check_auth($mixPermission, false, $intUserID);
		} else {
			$strFirstValue = strtolower($mixPermission[0]);
			if($strFirstValue == "and"){
				unset($mixPermission[0]);
				return $this->user->check_auths($mixPermission, "AND", false, $intUserID);
			}elseif($strFirstValue == "or"){
				unset($mixPermission[0]);
				return $this->user->check_auths($mixPermission, "OR", false, $intUserID);
			}else {
				$arrPermissions = $mixPermission;
				return $this->user->check_auths($arrPermissions, "AND", false, $intUserID);
			}
		}

		return true;
	}
}
