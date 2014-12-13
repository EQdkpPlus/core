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

if(!class_exists('pdh_w_repository')) {
	class pdh_w_repository extends pdh_w_generic {

		public function insert($arrData){
			$objQuery = $this->db->prepare("INSERT INTO __repository :p")->set($arrData)->execute();
			$this->pdh->enqueue_hook('repository_update');
		}

		public function reset() {
			$this->db->query("TRUNCATE TABLE __repository;");
			$this->pdh->enqueue_hook('repository_update');
		}
		
		public function setUpdateTime($time){
			$objQuery = $this->db->prepare("UPDATE __repository :p")->set(array(
				'updated' => $time,
			))->execute();
			$this->pdh->enqueue_hook('repository_update');
		}
	}
}
?>