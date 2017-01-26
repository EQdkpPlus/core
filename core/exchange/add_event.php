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
		public function post_add_event($params, $body){
			$isAPITokenRequest = $this->pex->getIsApiTokenRequest();
			
			if ($isAPITokenRequest ||  $this->user->check_auth('a_event_add', false)){
				$blnTest = (isset($params['get']['test']) && $params['get']['test']) ? true : false;
				
				$xml = simplexml_load_string($body);
				if ($xml){
					//Check required values
					if (!isset($xml->event_name) && !strlen($xml->event_name)) return $this->pex->error('required data missing');
					if (!isset($xml->event_value) && !strlen($xml->event_value)) return $this->pex->error('required data missing');
										
					//Event Value
					$fltEventValue = (float)$xml->event_value;
					
					//Item Name
					$strEventName = filter_var((string)$xml->event_name, FILTER_SANITIZE_STRING);	
					
					if($blnTest) return array('test' => 'success');
					
					$mixEventID = $this->pdh->put('event', 'add_event', array($strEventName, $fltEventValue, ''));
					if (!$mixEventID) return $this->pex->error('an error occured');
					$this->pdh->process_hook_queue();
					
					return array('event_id' => $mixEventID);
				}
				return $this->pex->error('malformed xml');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
?>