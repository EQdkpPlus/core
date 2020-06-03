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

if (!class_exists('exchange_add_event')){
	class exchange_add_event extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		/*
		* Required: Event Name (String)
		* Required: Event Value (float)
		*
		* Returns: Status 0 on error, Status 1 and inserted Event-ID on succes
		*/
		public function post_add_event($params, $arrBody){
			$isAPITokenRequest = $this->pex->isApiWriteTokenRequest();

			if ($isAPITokenRequest ||  $this->user->check_auth('a_event_add', false)){
				$blnTest = (isset($params['get']['test']) && $params['get']['test']) ? true : false;

				if (count($arrBody)){
					//Check required values
					if (!isset($arrBody['event_name']) || !strlen($arrBody['event_name'])) return $this->pex->error('required data missing', 'event_name');
					if (!isset($arrBody['event_value']) || !strlen($arrBody['event_value'])) return $this->pex->error('required data missing', 'event_value');
					#if (!isset($arrBody['multidkp_poolid']) || !strlen($arrBody['multidkp_poolid'])) return $this->pex->error('required data missing', 'multidkp_poolid');
					
					
					//Event Value
					$fltEventValue = (float)$arrBody['event_value'];

					//Item Name
					$strEventName = filter_var((string)$arrBody['event_name'], FILTER_SANITIZE_STRING);

					if($blnTest) return array('test' => 'success');

					$intDefaultItempool = (isset($arrBody['event_default_itempool'])) ? (int)$arrBody['event_default_itempool'] : 0;

					$mixEventID = $this->pdh->put('event', 'add_event', array($strEventName, $fltEventValue, '', $intDefaultItempool));
					if (!$mixEventID) return $this->pex->error('an error occured');
					
					//Add the event to an MultiDKP Pool
					$arrPools = $this->pdh->get('multidkp', 'id_list', array());
					if(isset($arrBody['multidkp_poolid'])){
						$intMultidkpPool = intval($arrBody['multidkp_poolid']);
						if(!in_array($intMultidkpPool, $arrPools)){
							$intMultidkpPool = intval($arrPools[0]);
						}
					} else {
						$intMultidkpPool = intval($arrPools[0]);
					}
					
					$blnNoAttendance = (isset($arrBody['event_no_attendance'])) ? (int)$arrBody['event_no_attendance'] : 0;
					
					
					$this->pdh->put('multidkp', 'add_event2multidkp', array($mixEventID, $intMultidkpPool, $blnNoAttendance));
					
					$this->pdh->process_hook_queue();

					return array('event_id' => $mixEventID);
				}
				return $this->pex->error('malformed input');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
