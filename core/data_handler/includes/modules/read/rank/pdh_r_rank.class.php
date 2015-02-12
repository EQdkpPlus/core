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
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_rank" ) ) {
	class pdh_r_rank extends pdh_r_generic{

		public $default_lang = 'english';
		public $ranks;

		public $hooks = array(
			'adjustment_update',
			'event_update',
			'item_update',
			'member_update',
			'raid_update',
			'rank_update'
		);

		public function reset(){
			$this->pdc->del('pdh_member_ranks');
			$this->ranks = NULL;
		}

		public function init(){
			$this->ranks = $this->pdc->get('pdh_member_ranks');
			if($this->ranks !== NULL) return true;
			
			$objQuery = $this->db->query("SELECT * FROM __member_ranks ORDER BY rank_sortid ASC;");
			if($objQuery){
				while($r_row = $objQuery->fetchAssoc()){
					$this->ranks[$r_row['rank_id']]['rank_id']	= $r_row['rank_id'];
					$this->ranks[$r_row['rank_id']]['prefix']	= $r_row['rank_prefix'];
					$this->ranks[$r_row['rank_id']]['suffix']	= $r_row['rank_suffix'];
					$this->ranks[$r_row['rank_id']]['name']		= $r_row['rank_name'];
					$this->ranks[$r_row['rank_id']]['hide']		= (int)$r_row['rank_hide'];
					$this->ranks[$r_row['rank_id']]['sortid']	= (int)$r_row['rank_sortid'];
					$this->ranks[$r_row['rank_id']]['default']	= (int)$r_row['rank_default'];
					$this->ranks[$r_row['rank_id']]['icon']		= $r_row['rank_icon'];
				}
				if (!isset($this->ranks[0])) {
					$this->pdh->put('rank', 'add_rank', array(0, 'Default', 0, '', '', 0, 1));
					$this->ranks[0] = array('rank_id' => 0,	'prefix' => '',	'suffix' => '',	'name' => 'Default', 'hide' => 0, 'sortid' => 0, 'default' => 1);
				}
				$this->pdc->put('pdh_member_ranks', $this->ranks);
			}
		}

		public function get_id($name) {
			if(!empty($this->ranks)) {
				foreach($this->ranks as $id => $rank) {
					if($rank['name'] == $name) return $id;
				}
			}
			return false;
		}

		public function get_ranks(){
			return $this->ranks;
		}

		public function get_id_list(){
			return array_keys($this->ranks);
		}

		public function get_name($rank_id){
			return $this->ranks[$rank_id]['name'];
		}

		public function get_html_name($rank_id){
			return $this->game->decorate('ranks', $rank_id).$this->ranks[$rank_id]['name'];
		}

		public function get_rank_image($rank_id){
			$strGameFolder = 'games/'.$this->game->get_game().'/icons/ranks/';
			$strIcon = $this->get_icon($rank_id);
			
			$rankimage = (strlen($strIcon) && is_file($this->root_path.$strGameFolder.$strIcon)) ? $this->server_path.$strGameFolder.$strIcon : "";
			return ($rankimage != "") ? '<img src="'.$rankimage.'" alt="rank image" width="20"/>' : '';
		}

		public function get_prefix($rank_id){
			return $this->ranks[$rank_id]['prefix'];
		}

		public function get_suffix($rank_id){
			return $this->ranks[$rank_id]['suffix'];
		}

		public function get_is_hidden($rank_id){
			return $this->ranks[$rank_id]['hide'];
		}
		
		public function get_sortid($rank_id){
			return $this->ranks[$rank_id]['sortid'];
		}
		
		public function get_icon($rank_id){
			return $this->ranks[$rank_id]['icon'];
		}
		
		public function get_html_icon($rank_id){
			return $this->get_rank_image($rank_id);
		}
		
		public function get_default_value($rank_id){
			return $this->ranks[$rank_id]['default'];
		}
		
		public function get_default(){
			if(is_array($this->ranks)){
				foreach($this->ranks as $key => $val){
					if ($val['default'] == 1) return $key;
				}
			}
			
			$arrIDs = $this->get_id_list();
			return ((isset($arrIDs[0])) ? $arrIDs[0] : 0);
		}
		
	}//end class
}//end if
?>