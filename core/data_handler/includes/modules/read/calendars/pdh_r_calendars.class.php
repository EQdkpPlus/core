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

if ( !class_exists( "pdh_r_calendars" ) ) {
	class pdh_r_calendars extends pdh_r_generic{

		public $default_lang = 'english';
		public $calendars;

		public $hooks = array(
			'calendar_update',
		);

		public function reset(){
			$this->pdc->del('pdh_calendars_table');
			$this->calendars = NULL;
		}

		public function init(){
			//cached data not outdated?
			$this->calendars	= $this->pdc->get('pdh_calendars_table');
			if($this->calendars !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __calendars");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->calendars[$row['id']] = array(
						'id'				=> $row['id'],
						'name'				=> $row['name'],
						'color'				=> $row['color'],
						'private'			=> $row['private'],
						'feed'				=> $row['feed'],
						'system'			=> $row['system'],
						'type'				=> $row['type'],
						'restricted'		=> $row['restricted'],
						'affiliation'		=> $row['affiliation'],
					);
				}
				$this->pdc->put('pdh_calendars_table', $this->calendars, null);
			}
		}

		//1 = raid, 2=event 3=feed
		public function get_idlist($filter=false, $idfilter=false){
			if($filter){
				$out = array();
				foreach($this->calendars as $id=>$cals){
					// continue if the idfilter is false or if the id is in the id filter
					if(!$idfilter || (is_array($idfilter) && in_array($id, $idfilter))){
						if($filter == 'feed'){
							if($cals['type'] == '3'){
								$out[] = $id;
							}
						#}elseif($filter == 'free2add'){
						#	if(!$cals['system'] && $cals['type'] != '3'){
						#		$out[] = $id;
						#	}
						}else{
							if($cals['type'] != '3'){
								$out[] = $id;
							}
						}
					}
				}
				return $out;
			}else{
				return (isset($this->calendars)) ? array_keys($this->calendars) : array();
			}
		}

		public function get_is_deletable($id){
			return 	($this->calendars[$id]['system'] > 0) ? false : true;
		}

		public function get_data($id=false){
			return ($id > 0) ? $this->calendars[$id] : $this->calendars;
		}

		public function get_name($id){
			return 	$this->calendars[$id]['name'];
		}

		public function get_color($id){
			return 	$this->calendars[$id]['color'];
		}

		public function get_private($id){
			return 	$this->calendars[$id]['private'];
		}

		public function get_feed($id){
			return 	$this->calendars[$id]['feed'];
		}

		public function get_restricted($id){
			return 	(isset($this->calendars[$id]['restricted'])) ? $this->calendars[$id]['restricted'] : 0;
		}

		public function get_affiliation($id){
			return 	(isset($this->calendars[$id]['affiliation'])) ? $this->calendars[$id]['affiliation'] : 'user';
		}

		public function get_type($id){
			return (isset($this->calendars[$id])) ? $this->calendars[$id]['type'] : '';
		}
	}//end class
}//end if
?>