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

class messenger extends gen_class {

	public function sendMessage($strMethod, $intToUserID, $strSubject, $strMessage){

		if(is_file($this->root_path.'core/messenger/'.$strMethod.'.messenger.class.php')){
			include_once($this->root_path.'core/messenger/generic_messenger.class.php');
			include_once($this->root_path.'core/messenger/'.$strMethod.'.messenger.class.php');
			$objMessengerMethod = register($strMethod.'_messenger');
			$blnResult = $objMessengerMethod->sendMessage($intToUserID, $strSubject, $strMessage);

			return $blnResult;
		}
		return false;
	}

	public function getAvailableMessenger($blnAllMethods=false){
		include_once $this->root_path.'core/messenger/generic_messenger.class.php';
		$types = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/messenger/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/messenger/' . $file)) && valid_folder($file)){
					if ($file == 'generic_messenger.class.php') continue;

					include_once($this->root_path . 'core/messenger/' . $file);
					$name = substr($file, 0, strpos($file, '.'));
					$classname = $name.'_messenger';
					$blnIsAvailable = register($classname)->isAvailable();
					if(!$blnIsAvailable && !$blnAllMethods) continue;
					$static_name = $this->user->lang('messenger_type_'.$name);
					$types[$name] = (strlen($static_name)) ? $static_name : ucfirst($name);
				}
			}
		}
		return $types;
	}

	public function getMethodsAdminSettings(){
		include_once $this->root_path.'core/messenger/generic_messenger.class.php';
		$types = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/messenger/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/messenger/' . $file)) && valid_folder($file)){
					if ($file == 'generic_messenger.class.php') continue;
					$name = substr($file, 0, strpos($file, '.'));

					$settings = $this->getMethodAdminSettings($name);
					if(is_array($settings)){
						$types = array_merge($types, $settings);
					}
				}
			}
		}
		return $types;
	}

	public function getMethodAdminSettings($strMessenger){
		include_once $this->root_path.'core/messenger/generic_messenger.class.php';
		$settings = array();

		$file = $strMessenger.'.messenger.class.php';
		if(is_file($this->root_path . 'core/messenger/' . $file)){
			include_once($this->root_path . 'core/messenger/' . $file);
			$name = substr($file, 0, strpos($file, '.'));
			$classname = $name.'_messenger';
			$arrAdminSettings = register($classname)->getAdminSettings();
			if(count($arrAdminSettings)){
				$settings = $arrAdminSettings;
			}
		}

		return $settings;
	}

	public function getMethodsUserSettings($blnAllMethods=false){
		include_once $this->root_path.'core/messenger/generic_messenger.class.php';
		$types = array();
		// Build auth array
		if($dir = @opendir($this->root_path . 'core/messenger/')){
			while ( $file = @readdir($dir) ){
				if ((is_file($this->root_path . 'core/messenger/' . $file)) && valid_folder($file)){
					if ($file == 'generic_messenger.class.php') continue;

					$name = substr($file, 0, strpos($file, '.'));
					$settings = $this->getMethodUserSettings($name);
					if(is_array($settings)){
						$types = array_merge($types, $settings);
					}
				}
			}
		}
		return $types;
	}

	public function getMethodUserSettings($strMessenger){
		include_once $this->root_path.'core/messenger/generic_messenger.class.php';
		$settings = array();

		$file = $strMessenger.'.messenger.class.php';
		if(is_file($this->root_path . 'core/messenger/' . $file)){
			include_once($this->root_path . 'core/messenger/' . $file);
			$name = substr($file, 0, strpos($file, '.'));
			$classname = $name.'_messenger';
			$arrAdminSettings = register($classname)->getUserSettings();
			if(count($arrAdminSettings)){
				$settings = $arrAdminSettings;
			}
		}

		return $settings;
	}

	public function isAvailable($strMessenger){
		include_once $this->root_path.'core/messenger/generic_messenger.class.php';

		$file = $strMessenger.'.messenger.class.php';
		if(is_file($this->root_path . 'core/messenger/' . $file)){
			include_once($this->root_path . 'core/messenger/' . $file);
			$name = substr($file, 0, strpos($file, '.'));
			$classname = $name.'_messenger';

			$blnIsAvailable = register($classname)->isAvailable();
			return $blnIsAvailable;
		}

		return false;
	}
}
