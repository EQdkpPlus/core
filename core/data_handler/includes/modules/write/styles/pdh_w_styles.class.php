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

if(!class_exists('pdh_w_styles')) {
	class pdh_w_styles extends pdh_w_generic {

		public function update_status($styleid, $status=1){
			$objQuery = $this->db->prepare("UPDATE __styles :p WHERE style_id=?")->set(array(
					'enabled'	=> $status
			))->execute($styleid);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('styles_update');
			return true;
		}

		public function update_version($version, $styleid){
			$objQuery = $this->db->prepare("UPDATE __styles :p WHERE style_id=?")->set(array(
					'style_version'	=> $version
			))->execute($style_id);
			if(!$objQuery) return false;
			$this->pdh->enqueue_hook('styles_update');
			return true;
		}

		public function delete_style($styleid){
			$this->db->prepare("DELETE FROM __styles WHERE style_id=?")->execute($styleid);

			$objQuery = $this->db->prepare("UPDATE __users :p WHERE user_style=?")->set(array(
					'user_style' => $this->config->get('default_style'),
			))->execute($styleid);

			$this->pdh->enqueue_hook('styles_update');
			return true;
		}
		
		public function insert_styleparams($style){
			$objQuery = $this->db->prepare("INSERT INTO __styles :p")->set(array(
				'style_name'	=> $style,
				'template_path'	=> $style,
				'enabled'		=> 1,
				'use_db_vars'	=> 1,	
			))->execute();

			if ($objQuery){
				$this->pdh->enqueue_hook('styles_update');
				return $objQuery->insertId;
			}
			
			return false;
		}
		
		public function add_style($data){
			if(!isset($data['background_type']) || $data['background_type'] == "") $data['background_type'] = 1;
			
			$objQuery = $this->db->prepare("INSERT INTO __styles :p")->set($data)->execute();
			if ($objQuery){
				$this->pdh->enqueue_hook('styles_update');
				return $objQuery->insertId;
			}
			
			return false;
		}
		
		public function update_style($styleid ,$data){
			$objQuery = $this->db->prepare("UPDATE __styles :p WHERE style_id=?")->set($data)->execute($styleid);
			$this->pdh->enqueue_hook('styles_update');
			return $styleid;
		}
	}
}
?>