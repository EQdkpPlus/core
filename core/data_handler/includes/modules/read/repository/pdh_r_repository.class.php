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

if ( !class_exists( "pdh_r_repository" ) ) {
	class pdh_r_repository extends pdh_r_generic{

		public $default_lang = 'english';
		public $repository;

		public $hooks = array(
			'repository_update'
		);

		public function reset(){
			$this->pdc->del('pdh_repository_table');
			$this->repository = NULL;
		}

		public function init(){
			// disable for now until repository.php is fully converted
			$this->repository	= $this->pdc->get('pdh_repository_table');
			if($this->repository !== NULL){
				return true;
			}
			
			$objQuery = $this->db->query("SELECT * FROM __repository ORDER BY dep_coreversion DESC");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->repository[(int)$row['category']][$row['id']] = array(
						'name'			=> $row['name'],
						'plugin'		=> $row['plugin'],
						'date'			=> $row['date'],
						'author'		=> $row['author'],
						'version'		=> $row['version'],
						'version_ext'	=> $row['version_ext'],
						'changelog'		=> $row['changelog'],
						'lastupdate'	=> $row['updated'],
						'description'	=> $row['description'],
						'category'		=> $row['category'],
						'level'			=> $row['level'],
						'rating'		=> $row['rating'],
						'dep_coreversion'=> $row['dep_coreversion'],
						'dep_php'		=> $row['dep_php'],
						'plugin_id'		=> $row['plugin_id'],
						'bugtracker_url'=> $row['bugtracker_url'],
					);
				}
				
				$this->pdc->put('pdh_repository_table', $this->repository, null);
			}
		}

		public function get_repository(){
			return $this->repository;
		}
		
		public function get_row($id){
			foreach ($this->repository as $catid => $extensions){
				if (is_array($extensions)){
					foreach($extensions as $eid => $ext){
						if($eid == $id) return $ext;
					}
				}
			}
			return false;
		}

		public function get_lastupdate(){
			if ($this->repository == NULL) return 0;
			$categorys = array_keys($this->repository);
			if (isset($categorys[0])){
				$extensions = array_keys($this->repository[$categorys[0]]);
				if (isset($extensions[0])){
					return $this->repository[$categorys[0]][$extensions[0]]['lastupdate'];
				}
			}
			return 0;
		}
		
		public function get_bugtracker_url($cat, $id){
			if(isset($this->repository[$cat][$id])){
				return $this->repository[$cat][$id]['bugtracker_url'];
			}
		}
	}//end class
}//end if
?>