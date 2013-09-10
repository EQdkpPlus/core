<?php
/*
* Project:		EQdkp-Plus
* License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
* Link:			http://creativecommons.org/licenses/by-nc-sa/3.0/
* -----------------------------------------------------------------------
* Began:		2009
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

if ( !class_exists( "pdh_r_user_groups" ) ){
	class pdh_r_user_groups extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('db2'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $user_groups;
		public $user_standard_group;

		public $hooks = array(
			'user_groups_update',
		);

		public function reset(){
			$this->user_groups = NULL;
			$this->user_standard_group = NULL;
		}

		public function init(){
			$this->user_groups = array();
			
			$objQuery = $this->db2->query("SELECT * FROM __groups_user ORDER BY groups_user_sortid ASC, groups_user_id ASC;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->user_groups[$row['groups_user_id']]['id']		= $row['groups_user_id'];
					$this->user_groups[$row['groups_user_id']]['name']		= $row['groups_user_name'];
					$this->user_groups[$row['groups_user_id']]['desc']		= $row['groups_user_desc'];
					$this->user_groups[$row['groups_user_id']]['deletable']	= $row['groups_user_deletable'];
					$this->user_groups[$row['groups_user_id']]['default']	= $row['groups_user_default'];
					$this->user_groups[$row['groups_user_id']]['hide']		= $row['groups_user_hide'];
					$this->user_groups[$row['groups_user_id']]['sortid']	= $row['groups_user_sortid'];
					if ($row['groups_user_default'] == 1){
						$this->user_standard_group = $row['groups_user_id'];
					}
				}
			}
		}

		public function get_id_list($hide = false){
			if (!$hide){
				return array_keys($this->user_groups);
			} else {
				foreach ($this->user_groups as $key=>$value){
					if ($value['hide'] != 1){
						$out[$key] = $key;
					}
				}
				return $out;
			}
		}

		public function get_data($groups_user_id){
			return $this->user_groups[$groups_user_id];
		}

		public function get_name($groups_user_id){
			return $this->user_groups[$groups_user_id]['name'];
		}

		public function get_desc($groups_user_id){
			return $this->user_groups[$groups_user_id]['desc'];
		}

		public function get_deletable($groups_user_id){
			return $this->user_groups[$groups_user_id]['deletable'];
		}

		public function get_standard($groups_user_id){
			return $this->user_groups[$groups_user_id]['default'];
		}

		public function get_hide($groups_user_id){
			return $this->user_groups[$groups_user_id]['hide'];
		}
		
		public function get_sortid($groups_user_id){
			return $this->user_groups[$groups_user_id]['sortid'];
		}

		public function get_standard_group(){
			if ($this->user_standard_group){
				return $this->user_standard_group;
			} else {
				return 4;
			}
		}
	}//end class
}//end if
?>