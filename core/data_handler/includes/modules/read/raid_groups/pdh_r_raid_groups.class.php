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

if ( !class_exists( "pdh_r_raid_groups" ) ){
	class pdh_r_raid_groups extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $raid_groups;
		public $raid_standard_group;

		public $hooks = array(
			'raid_groups_update',
		);

		public function reset(){
			$this->raid_groups = NULL;
			$this->raid_standard_group = NULL;
		}

		public function init(){
			$this->raid_groups = array();
			
			$objQuery = $this->db->query("SELECT * FROM __groups_raid ORDER BY groups_raid_sortid ASC, groups_raid_id ASC;");
			if($objQuery){
				while($row = $objQuery->fetchAssoc()){
					$this->raid_groups[$row['groups_raid_id']]['id']		= $row['groups_raid_id'];
					$this->raid_groups[$row['groups_raid_id']]['name']		= $row['groups_raid_name'];
					$this->raid_groups[$row['groups_raid_id']]['desc']		= $row['groups_raid_desc'];
					$this->raid_groups[$row['groups_raid_id']]['deletable']	= $row['groups_raid_deletable'];
					$this->raid_groups[$row['groups_raid_id']]['default']	= $row['groups_raid_default'];
					$this->raid_groups[$row['groups_raid_id']]['hide']		= $row['groups_raid_hide'];
					$this->raid_groups[$row['groups_raid_id']]['sortid']	= $row['groups_raid_sortid'];
					if ($row['groups_raid_default'] == 1){
						$this->raid_standard_group = $row['groups_raid_id'];
					}
				}
			}
		}

		public function get_id_list($hide = false){
			if (!$hide){
				return array_keys($this->raid_groups);
			} else {
				foreach ($this->raid_groups as $key=>$value){
					if ($value['hide'] != 1){
						$out[$key] = $key;
					}
				}
				return $out;
			}
		}

		public function get_data($groups_raid_id){
			return $this->raid_groups[$groups_raid_id];
		}

		public function get_name($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['name'];
		}

		public function get_desc($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['desc'];
		}

		public function get_deletable($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['deletable'];
		}

		public function get_standard($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['default'];
		}

		public function get_hide($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['hide'];
		}
		
		public function get_sortid($groups_raid_id){
			return $this->raid_groups[$groups_raid_id]['sortid'];
		}

		public function get_standard_group(){
			if ($this->raid_standard_group){
				return $this->raid_standard_group;
			} else {
				return 4;
			}
		}
	}//end class
}//end if
?>