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

if(!defined('EQDKP_INC')){
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendar_raids_guests')){
	class pdh_w_calendar_raids_guests extends pdh_w_generic{
	
		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendar_raid_guests;");
			$this->pdh->enqueue_hook('guests_update');
		}

		public function insert_guest($eventid, $name='', $classid='', $group='', $note=''){
			$objQuery = $this->db->prepare("INSERT INTO __calendar_raid_guests :p")->set(array(	
				'calendar_events_id'	=> $eventid,
				'name'					=> $name,
				'note'					=> $note,
				'timestamp_signup'		=> $this->time->time,
				'class'					=> $classid,
				'raidgroup'				=> $group
			))->execute();
			$this->pdh->enqueue_hook('guests_update', array($objQuery->insertId));
			if ($objQuery) return $objQuery->insertId;
			return false;
		}

		public function update_guest($guestid, $classid='', $group='', $note=''){
			$classname	= ($classname)	? $classname: $this->pdh->get('guests', 'class', array($guestid));
			$group		= ($group)		? $group	: $this->pdh->get('guests', 'group', array($guestid));
			$note		= ($note)		? $note 	: $this->pdh->get('guests', 'note', array($guestid));
			$objQuery = $this->db->prepare("UPDATE __calendar_raid_guests :p WHERE id=?")->set(array(
				'class'			=> $classid,
				'raidgroup'		=> $group,
				'note'			=> $note,
			))->execute($guestid);
			$this->pdh->enqueue_hook('guests_update', array($guestid));
		}

		public function delete_guest($guestid){
			$objQuery = $this->db->prepare("DELETE FROM __calendar_raid_guests WHERE id=?;")->execute($guestid);
			
			if($objQuery){
				$this->pdh->enqueue_hook('guests_update', array($guestid));
				return true;
			}
			return false;
		}
	}
}
?>