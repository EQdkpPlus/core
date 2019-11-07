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

if(!class_exists("calevents_awaymode_crontask")){
	class calevents_awaymode_crontask extends crontask{

		public function __construct(){
			$this->defaults['active']			= true;
			$this->defaults['repeat']			= true;
			$this->defaults['repeat_type']		= 'daily';
			$this->defaults['repeat_interval']	= 1;
			$this->defaults['ajax']				= true;
			$this->defaults['description']		= 'Sign-off away members in their away-time';
		}

		public function run(){
			// fetch the raid ids of the repeatable raids
			$future_events	= $this->pdh->get('calendar_events', 'id_list', array(true, $this->time->time));
			if(is_array($future_events) && count($future_events) > 0){
				foreach($future_events as $raidid){
					$away_attendees	= $this->pdh->get('calendar_raids_attendees', 'away_attendees', array($raidid));
					#d($away_attendees);die();
					// now, sign off the attendees
					$this->pdh->put('calendar_raids_attendees', 'automatic_signoff', array($raidid, $away_attendees));
				} // end of foreach
			}
			$this->pdh->process_hook_queue();
		}
	}
}
