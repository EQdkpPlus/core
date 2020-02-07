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

if (!class_exists('exchange_event_signup')){
	class exchange_event_signup extends gen_class {
		public static $shortcuts = array('pex'=>'plus_exchange');
		public $options		= array();

		public function post_event_signup($params, $arrBody){
			if ($this->user->check_auth('po_calendarevent', false) && !$this->pex->isApiReadonlyTokenRequest()){

				if (count($arrBody) && intval($arrBody['eventid']) > 0){
					$eventid = intval($arrBody['eventid']);
					$eventdata = $this->pdh->get('calendar_events', 'data', array($eventid));
					
					if(!$this->pdh->get('calendar_events', 'private_userperm', array($eventid))){
						return $this->pex->error('access denied');
					}
					
					if ($eventdata && ((int)$this->pdh->get('calendar_events', 'calendartype', array($eventid)) != 1)){
						if(!$this->user->is_signedin()){
							return $this->pex->error('access denied');
						}
												
						$availableStatus = array('attend', 'maybe', 'decline');
						
						$status = $arrBody['status'];
						
						if(!in_array($status, $availableStatus)){
							return $this->pex->error('required data missing', 'status not found');
						}
						
						$this->pdh->put('calendar_events', 'handle_attendance', array($eventid, $this->user->id, $status));
												
						return array('status'	=> 1);
					} else {
						return $this->pex->error('required data missing', 'eventid not found');
					}
				}
				return $this->pex->error('required data missing', 'eventid');
			} else {
				return $this->pex->error('access denied');
			}
		}
	}
}
