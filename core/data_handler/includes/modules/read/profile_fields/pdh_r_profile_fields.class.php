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

		public $default_lang = 'english';
		public $profile_fields;
		public $profile_categories;

		public $hooks = array(
			'game_update'
		);

		public function reset(){
			$this->pdc->del('pdh_profile_fields_table');
			$this->pdc->del('pdh_profile_categories_table');
			$this->profile_fields = NULL;
			$this->profile_categories = NULL;
		}

		public function init(){
			$this->profile_fields			= $this->pdc->get('pdh_profile_fields_table');
			$this->profile_categories		= $this->pdc->get('pdh_profile_categories_table');
			if($this->profile_fields !== NULL && $this->profile_categories !== NULL){
				return true;
			}
			$this->profile_fields = array();
			$objQuery = $this->db->query("SELECT * FROM __member_profilefields ORDER BY sort ASC,category,name");
			if($objQuery){
				while($drow = $objQuery->fetchAssoc()) {
					// build categories array
					if(!is_array($this->profile_categories) || !in_array($drow['category'], $this->profile_categories)){
						if($drow['category'] != 'character'){
							$this->profile_categories[] = $drow['category'];
						}
					}
	
					$this->profile_fields[$drow['name']] = array(
						'type'			=> $drow['type'],
						'category'		=> $drow['category'],
						'size'			=> $drow['size'],
						'sort'			=> $drow['sort'],
						'lang'			=> $drow['lang'],
						'options_language' => $drow['options_language'],
						'image'			=> $drow['image'],
						'enabled'		=> $drow['enabled'],
						'data'			=> unserialize($drow['data']),
						'undeletable'	=> $drow['undeletable'],
						'custom'		=> $drow['custom'],
					);
					foreach($this->profile_fields[$drow['name']]['data'] as $key => $dat) {
						$this->profile_fields[$drow['name']][$key] = $dat;
					}
				}
				
				// check if the character tab is in the categories list, if not add it
				if (!is_array($this->profile_categories)) $this->profile_categories = array();
				if(!in_array('character', $this->profile_categories)){
					$this->profile_categories[] = 'character';
				}
	
				// save all the stuff to the cache
				$this->profile_categories = array_unique($this->profile_categories);
				$this->pdc->put('pdh_profile_fields_table', $this->profile_fields, null);
				$this->pdc->put('pdh_profile_categories_table', $this->profile_categories, null);
			}
		}

		public function get_categories(){
			return $this->profile_categories;
		}

		public function get_fields($name=''){
			return ($name) ? (isset($this->profile_fields[$name]) ? $this->profile_fields[$name] : null) : $this->profile_fields;
		}

		public function get_fieldlist(){
			return array_keys($this->profile_fields);
		}

		public function get_lang($name) {
			return $this->profile_fields[$name]['lang'];
		}
		
		public function get_options_language($name) {
			return $this->profile_fields[$name]['options_language'];
		}

	}//end class
}//end if
?>