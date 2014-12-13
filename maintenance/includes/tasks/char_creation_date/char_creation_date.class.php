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

if ( !defined('EQDKP_INC') ){
  header('HTTP/1.0 404 Not Found');exit;
}

class char_creation_date extends task {
	public $author = 'Hoofy';
	public $version = '1.0.0';
	public $form_method = 'post';
	public $name = 'Character creation date';
	public $type = 'fix';

	public function is_applicable() {
		$creation_dates = $this->pdh->aget('member', 'creation_date', 0, array($this->pdh->get('member', 'id_list')));
		if(in_array(0, $creation_dates)) return true;
		return false;
	}
	
	public function is_necessary() {
		return ($this->is_applicable() && $this->config->get('char_creation_date_update')) ? true : false;
	}
	
	public function get_form_content() {
		$creation_dates = $this->pdh->aget('member', 'creation_date', 0, array($this->pdh->get('member', 'id_list')));
		$members = array();
		foreach($creation_dates as $member_id => $date) {
			if(!$date) $members[] = $member_id;
		}
		$first_raids = $this->pdh->aget('member_dates', 'first_raid', 0, array($members, null, false));
		$first_items = $this->pdh->aget('member_dates', 'first_item_date', 0, array($members, null, false));
		$adjs4mem = $this->pdh->aget('adjustment', 'adjsofmember', 0, array($members));
		$adjs_date = $this->pdh->aget('adjustment', 'date', 0, array($this->pdh->get('adjustment', 'id_list')));
		$first_adjs = array();
		foreach($adjs4mem as $member_id => $adjs) {
			$first_adj = 99999999999;
			foreach($adjs as $id) {
				if($adjs_date[$id] < $first_adj) $first_adj = $adjs_date[$id];
			}
			$first_adjs[$member_id] = $first_adj;
		}
		foreach($members as $member_id) {
			if(!empty($first_raids[$member_id])) {
				if(!empty($first_items[$member_id])) {
					if(!empty($first_adjs[$member_id]))
						$date = min($first_raids[$member_id], $first_items[$member_id], $first_adjs[$member_id]);
					else $date = min($first_raids[$member_id], $first_items[$member_id]);
				} else $date = $first_raids[$member_id];
			} else $date = $this->time->time;
			
			$this->db->prepare("UPDATE __members SET member_creation_date = ? WHERE member_id = ?")->execute($date, $member_id);
		}
		$this->config->del('char_creation_date_update');
		return $this->lang['fix_creation_date_done'];
	}
}
?>