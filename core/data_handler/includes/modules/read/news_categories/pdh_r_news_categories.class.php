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

if ( !class_exists( "pdh_r_news_categories" ) ) {
	class pdh_r_news_categories extends pdh_r_generic{
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $news_categories;

		public $hooks = array(
			'newscat_update'
		);

		public function reset(){
			$this->pdc->del('pdh_news_categories_table');
			$this->news_categories = NULL;
		}

		public function init(){
			$this->news_categories	= $this->pdc->get('pdh_news_categories_table');
			if($this->news_categories !== NULL){
				return true;
			}

			$pff_result = $this->db->query("SELECT * FROM __news_categories ORDER BY category_id");
			while($drow = $this->db->fetch_record($pff_result) ){
				$this->news_categories[$drow['category_id']] = array(
					'category_id'		=> $drow['category_id'],
					'category_name'		=> $drow['category_name'],
					'category_icon'		=> $drow['category_icon'],
					'category_color'	=> $drow['category_color']
				);
			}
				$this->db->free_result($pff_result);
				$this->pdc->put('pdh_news_categories_table', $this->news_categories, null);
		}

		public function get_id_list() {
			return array_keys($this->news_categories);
		}

		public function get_category($id=''){
			return ($id) ? $this->news_categories[$id] : $this->news_categories;
		}

		public function get_name($id){
			return $this->news_categories[$id]['category_name'];
		}

		public function get_icon($id){
			return $this->news_categories[$id]['category_icon'];
		}

		public function get_color($id){
			return $this->news_categories[$id]['category_color'];
		}

	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_news_categories', pdh_r_news_categories::__shortcuts());
?>