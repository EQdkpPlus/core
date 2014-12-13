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

if(!class_exists('pdh_w_links')) {
	class pdh_w_links extends pdh_w_generic {
		
		public function add($name, $url, $window=0, $visibility='[&#34;0&#34;]', $height=4024){
			if (strlen($name)){
				$objQuery = $this->db->prepare("INSERT INTO __links :p")->set(array(
					'link_name'			=> $name,
					'link_url'			=> $url,
					'link_window'		=> $window,
					'link_visibility'	=> $visibility,
					'link_height'		=> $height,
				))->execute();
				
				if ($objQuery){
					$this->pdh->enqueue_hook('links');
					return $objQuery->insertId;
				}
			}
			return false;
		}
		
		public function update($id, $name, $url, $window, $visibility, $height, $force = false){
			$data = $this->pdh->get('links', 'data', array($id));
		
			if ($force OR $data['name'] != $name OR $data['url'] != $url OR (int)$data['window'] != (int)$window OR $data['visibility'] != $visibility OR (int)$data['height'] != (int)$height){
				$objQuery = $this->db->prepare("UPDATE __links :p WHERE link_id=?")->set(array(
					'link_name'			=> $name,
					'link_url'			=> $url,
					'link_window'		=> $window,
					'link_visibility'	=> $visibility,
					'link_height'		=> $height,
				))->execute($id);
				
				$this->pdh->enqueue_hook('links');
				if (!$objQuery) return false;
			}
			return true;
		}

		public function delete_link($id){
			$objQuery = $this->db->prepare("DELETE FROM __links WHERE link_id =?")->execute($id);
			$this->pdh->enqueue_hook('links', array($id));
		}
		
		public function deleteByName($strName){
			$objQuery = $this->db->prepare("DELETE FROM __links WHERE link_name =?")->execute($strName);
			$this->pdh->enqueue_hook('links');
		}
	}
}
?>