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

if(!class_exists('pdh_w_calendar_raids_templates')){
	class pdh_w_calendar_raids_templates extends pdh_w_generic{

		public function reset() {
			$this->db->query("TRUNCATE TABLE __calendar_raid_templates;");
			$this->pdh->enqueue_hook('calendar_templates_update');
		}

		public function save_template($name, $tpldata){
			$templateid = $this->pdh->get('calendar_raids_templates', 'idbyname', array($name));
			if($templateid > 0){
				$this->delete_template($templateid);
			}
			$objQuery = $this->db->prepare("INSERT INTO __calendar_raid_templates :p")->set(array(
				'name'			=> $name,
				'tpldata'		=> json_encode($tpldata),
			))->execute();
			
			if ($objQuery){
				$id = $objQuery->insertId;
				$this->pdh->enqueue_hook('calendar_templates_update', array($id));
				return $id;
			}
			
			return false;		
		}

		public function delete_template($templateid){
			$objQuery = $this->db->prepare("DELETE FROM __calendar_raid_templates WHERE id=?;")->execute($templateid);
			
			if($objQuery){
				$this->pdh->enqueue_hook('calendar_templates_update', array($templateid));
				return true;
			}
			return false;
		}
	}
}
?>