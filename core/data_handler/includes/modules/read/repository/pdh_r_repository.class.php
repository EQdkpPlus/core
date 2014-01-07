<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2010
* Date:			$Date$
* -----------------------------------------------------------------------
* @author		$Author$
* @copyright	2006-2011 EQdkp-Plus Developer Team
* @link			http://eqdkp-plus.com
* @package		eqdkpplus
* @version		$Rev$
*
* $Id$
*/

if ( !defined('EQDKP_INC') ){
	die('Do not access this file directly.');
}

if ( !class_exists( "pdh_r_repository" ) ) {
	class pdh_r_repository extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

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

			$pff_result = $this->db->query("SELECT * FROM __repository ORDER BY dep_coreversion DESC");
			while ( $row = $this->db->fetch_record($pff_result) ){

				$this->repository[(int)$row['category']][$row['id']] = array(
					'name'			=> $row['name'],
					'plugin'		=> $row['plugin'],
					'date'			=> $row['date'],
					'author'		=> $row['author'],
					'version'		=> $row['version'],
					'version_ext'	=> $row['version_ext'],
					'changelog'		=> $row['changelog'],
					'lastupdate'	=> $row['updated'],
					'shortdesc'		=> $row['shortdesc'],
					'category'		=> $row['category'],
					'level'			=> $row['level'],
					'rating'		=> $row['rating'],
					'dep_coreversion'=> $row['dep_coreversion'],
					'dep_php'		=> $row['dep_php'],
				);
			}
			
			$this->db->free_result($pff_result);
			if($pff_result) $this->pdc->put('pdh_repository_table', $this->repository, null);
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
	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_repository', pdh_r_repository::__shortcuts());
?>