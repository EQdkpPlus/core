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

if(!class_exists('pdh_w_rank')) {
	class pdh_w_rank extends pdh_w_generic {

		public function add_rank($id, $name, $hide=0, $prefix='', $suffix='', $sortid=0, $default=0, $icon='') {
			$arrSet = array(
				'rank_id'		=> $id,
				'rank_name'		=> $name,
				'rank_hide'		=> $hide,
				'rank_prefix'	=> $prefix,
				'rank_suffix'	=> $suffix,
				'rank_sortid'	=> $sortid,
				'rank_default'	=> ($default) ? 1 : 0,
				'rank_icon'		=> $icon,
			);
			
			$objQuery = $this->db->prepare("INSERT INTO __member_ranks :p")->set($arrSet)->execute();
			
			if(!$objQuery) {
				return false;
			}
			$this->pdh->enqueue_hook('rank_update', array($id));
			return $id;
		}

		public function update_rank($id, $name='', $hide='', $prefix='', $suffix='', $sortid=0,$default=0,$icon='') {
			$old['name'] = $this->pdh->get('rank', 'name', array($id));
			$old['hide'] = $this->pdh->get('rank', 'is_hidden', array($id));
			$old['prefix'] = $this->pdh->get('rank', 'prefix', array($id));
			$old['suffix'] = $this->pdh->get('rank', 'suffix', array($id));
			$old['sortid'] = $this->pdh->get('rank', 'sortid', array($id));
			$old['default'] = $this->pdh->get('rank', 'default_value', array($id));
			$old['icon'] = $this->pdh->get('rank', 'icon', array($id));
			
			
			$changes = false;
			foreach($old as $varname => $value) {
				if(${$varname} != $value) {
					$changes = true;
				}
			}
			if($changes) {
				$arrSet = array(
					'rank_name' => $name,
					'rank_hide' => $hide,
					'rank_prefix' => $prefix,
					'rank_suffix' => $suffix,
					'rank_sortid' => $sortid,
					'rank_default' => ($default) ? 1 : 0,
					'rank_icon'	=> $icon,
				);
				
				$objQuery = $this->db->prepare("UPDATE __member_ranks :p WHERE rank_id=?")->set($arrSet)->execute($id);
				
				
				if(!$objQuery) {
					return false;
				}
			}
			$this->pdh->enqueue_hook('rank_update', array($id));
			return true;
		}
		
		public function set_standardAndSort($intRankID, $blnDefault=false, $intSortID){
			$old['sortid'] = $this->pdh->get('rank', 'sortid', array($intRankID));
			$old['default'] = $this->pdh->get('rank', 'default_value', array($intRankID));
			if ($old['sortid'] != $intSortID || $old['default'] != $blnDefault){
				$arrSet = array(
					'rank_sortid'	=> $intSortID,
					'rank_default'	=> ($blnDefault) ? 1 : 0,
				);
				$objQuery = $this->db->prepare("UPDATE __member_ranks :p WHERE rank_id=?")->set($arrSet)->execute($intRankID);
				
				if(!$objQuery) {
					return false;
				}
				$this->pdh->enqueue_hook('rank_update', array($intRankID));
			}
			return true;
		}

		public function delete_rank($id) {
			$objQuery = $this->db->prepare("DELETE FROM __member_ranks WHERE rank_id = ?;")->execute($id);
			
			if($objQuery) {
				$this->pdh->enqueue_hook('rank_update', array());
				return true;
			}
			return false;
		}
		
		public function truncate(){
			if($this->db->query("TRUNCATE __member_ranks;")) {
				$this->pdh->enqueue_hook('rank_update');
				return true;
			}
			return false;
		}
	}
}
?>