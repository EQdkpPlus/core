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

/**
 * Notification Generic. Is abstract class, all Notification have to extend this class.
 * @author GodMod
 */
abstract class generic_notification extends gen_class {

	/**
	 * Returns true, if this method is available for users (admin has set all options)
	 */
	abstract public function isAvailable();


	abstract public function sendNotification($arrNotificationData);

	/**
	 * Settings the Admin has to set, e.g. API Keys or Server Settings
	 *
	 * @return array
	 */
	public function getAdminSettings(){
		return array();
	}

	/**
	 * Settings the User has to set, e.g. Twitter Account for DMs
	 * All Settings have to start with ntfy_
	 *
	 * @return array
	 */
	public function getUserSettings(){
		return array();
	}
}
