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

if ( !class_exists( "pdh_r_profile_fields" ) ) {
	class pdh_r_profile_fields extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'game'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $profile_fields;
		public $profile_categories;
		public $field_list;

		public $hooks = array(
			'game_update'
		);

		public function reset(){
			$this->pdc->del('pdh_profile_fields_table');
			$this->pdc->del('pdh_profile_categories_table');
			$this->pdc->del('pdh_profile_fieldlist_table');
			$this->profile_fields = NULL;
			$this->profile_categories = NULL;
			$this->field_list = NULL;
		}

		public function init(){
			$this->profile_fields			= $this->pdc->get('pdh_profile_fields_table');
			$this->profile_categories		= $this->pdc->get('pdh_profile_categories_table');
			$this->field_list				= $this->pdc->get('pdh_profile_fieldlist_table');
			if($this->profile_fields !== NULL && $this->field_list !== NULL && $this->profile_categories !== NULL){
				return true;
			}

			$pff_sql = "SELECT * FROM __member_profilefields ORDER BY enabled DESC,category,name";
			$pff_result = $this->db->query($pff_sql);
			$this->profile_fields = array();
			while ( $drow = $this->db->fetch_record($pff_result) ){
				if(!is_array($this->profile_categories) || !in_array($drow['category'], $this->profile_categories)){
					if($drow['category'] != 'character'){
						$this->profile_categories[] = $drow['category'];
					}
				}
				$this->field_list[] = $drow['name'];

				$this->profile_fields[$drow['name']] = array(
					'fieldtype'		=> $drow['fieldtype'],
					'category'		=> $drow['category'],
					'size'			=> $drow['size'],
					'visible'		=> $drow['visible'],
					'language'		=> ($this->game->glang($drow['language'])) ? $this->game->glang($drow['language']) : $drow['language'],
					'image'			=> $drow['image'],
					'enabled'		=> $drow['enabled'],
					'undeletable'	=> $drow['undeletable'],
					'options'		=> unserialize($drow['options']),
					'custom'		=> $drow['custom'],
				);
			}

			// check if the character tab is in the categories list, if not add it
			if (!is_array($this->profile_categories)) $this->profile_categories = array();
			if(!in_array('character', $this->profile_categories)){
				$this->profile_categories[] = 'character';
			}

			// save all the stuff to the cache
			$this->db->free_result($pff_result);
			$this->pdc->put('pdh_profile_fields_table', $this->profile_fields, null);
			$this->pdc->put('pdh_profile_fieldlist_table', $this->field_list, null);
			$this->pdc->put('pdh_profile_categories_table', $this->profile_categories, null);
		}

		public function get_categories(){
			return array_unique($this->profile_categories);
		}

		public function get_fields($name=''){
			return ($name) ? $this->profile_fields[$name] : $this->profile_fields;
		}

		public function get_fieldlist(){
			return $this->field_list;
		}

		public function get_language($name) {
			return $this->profile_fields[$name]['language'];
		}

	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_profile_fields', pdh_r_profile_fields::__shortcuts());
?>