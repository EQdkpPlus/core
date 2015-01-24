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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_calendars')) {
	class pdh_w_calendars extends pdh_w_generic {

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendars;");
			$this->add_calendar(0, 'Raids',		'#00628c', '', 0, 1, 1, 'core', 1);
			$this->add_calendar(0, 'Userraids',	'#0cb20f', '', 0, 1, 0, 'core', 0);
			$this->add_calendar(0, 'Raids',		'#ba1e1e', '', 0, 2, 0, 'core', 0);
			$this->pdh->enqueue_hook('calendar_update');
		}

		public function update_calendar($id, $name, $color, $feed, $private, $type, $restricted, $affiliation=false){
			$old['name']		= $this->pdh->get('calendars', 'name', array($id));
			$old['feed']		= $this->pdh->get('calendars', 'feed', array($id));
			$old['private']		= $this->pdh->get('calendars', 'private', array($id));
			$old['color']		= $this->pdh->get('calendars', 'color', array($id));
			$old['type']		= $this->pdh->get('calendars', 'type', array($id));
			$old['restricted']	= $this->pdh->get('calendars', 'restricted', array($id));
			$old['affiliation']	= $this->pdh->get('calendars', 'affiliation', array($id));
			$changes		= false;
			foreach($old as $varname => $value) {
				if(${$varname} != $value) {
					$changes = true;
				}
			}
			if($changes) {
				$objQuery = $this->db->prepare("UPDATE __calendars :p WHERE id=?")->set(array(
					'name'			=> $name,
					'feed'			=> $feed,
					'private'		=> ($private) ? 1 : 0,
					'color'			=> $color,
					'type'			=> $type,
					'restricted'	=> ($restricted) ? 1 : 0,
					'affiliation'	=> $affiliation
				))->execute($id);
				
				if(!$objQuery) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('calendar_update', array($id));
			return true;
		}

		public function add_calendar($id, $name, $color, $feed, $private, $type, $restricted, $affiliation = 'user', $system=0){
			$objQuery = $this->db->prepare('INSERT INTO __calendars :p')->set(array(
				'feed'			=> ($feed) ? $feed : '',
				'name'			=> $name,
				'system'		=> $system,
				'color'			=> $color,
				'private'		=> ($private) ? 1 : 0,
				'type'			=> $type,
				'restricted'	=> ($restricted) ? 1 : 0,
				'affiliation'	=> $affiliation,
			))->execute();
			
			if($objQuery){
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('calendar_update', array($id));
				return $id;
			}
			return false;
		}

		public function delete_calendar($id){
			if(!$this->pdh->get('calendars', 'system', array($id))){
				$objQuery = $this->db->prepare("DELETE FROM __calendars WHERE id=?")->execute($id);
				$this->pdh->enqueue_hook('calendar_update', array($id));
				return true;
			}
		}
		
		public function delete_calendar_byaffiliation($affiliation){
			if($affiliation != 'core' && $affiliation != ''){
				$objQuery = $this->db->prepare("DELETE FROM __calendars WHERE affiliation=?")->execute($affiliation);
				$this->pdh->enqueue_hook('calendar_update');
				return true;
			}
		}
	}
}
?>