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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_notification_types')) {
	class pdh_w_notification_types extends pdh_w_generic {

		public function add($strType, $strName, $strCategory, $intPrio=0, $strDefault=0, $blnGroup=0, $strGroupName='', $intGroupAt=3, $strIcon=""){

			$objQuery = $this->db->prepare("INSERT INTO __notification_types :p")->set(array(
					'id'			=> $strType,
					'name'			=> $strName,
					'category'		=> $strCategory,
					'prio'			=> $intPrio,
					'`default`'		=> $strDefault,
					'`group`'		=> ($blnGroup) ? 1 : 0,
					'group_name'	=> $strGroupName,
					'group_at'		=> $intGroupAt,
					'icon'			=> $strIcon,
			))->execute();

			if($objQuery) {
				$this->pdh->enqueue_hook('notification_types_update', array());
				return true;
			}
			return false;
		}

		public function update($strNotificationID, $intPrio, $strDefault=0, $blnGroup=0, $intGroupAt=3, $strIcon=""){
			$objQuery = $this->db->prepare("UPDATE __notification_types :p WHERE id=?")->set(array(
					'prio'			=> $intPrio,
					'`default`'		=> $strDefault,
					'`group`'		=> ($blnGroup) ? 1 : 0,
					'group_at'		=> $intGroupAt,
					'icon'			=> $strIcon,
			))->execute($strNotificationID);

			if($objQuery) {
				$this->pdh->enqueue_hook('notification_types_update', array($strNotificationID));
				return true;
			}
			return false;
		}

	}
}
