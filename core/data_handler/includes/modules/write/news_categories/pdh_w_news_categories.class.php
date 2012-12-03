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

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists('pdh_w_news_categories')) {
	class pdh_w_news_categories extends pdh_w_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdh', 'db', 'pfh'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public function __construct() {
			parent::__construct();
		}

		public function add_category($new_cat_name='', $new_cat='', $new_color=''){
			$result = $this->db->query('INSERT INTO __news_categories :params', array(
				'category_name'		=> $new_cat_name,
				'category_icon'		=> $new_cat,
				'category_color'	=> $new_color
			));
			$this->pdh->enqueue_hook('newscat_update');
			return $this->db->insert_id();
		}

		public function update_category($id, $category_name='', $category_icon='', $category_color=''){
			$old['category_name']	= $this->pdh->get('news_categories', 'name', array($id));
			$old['category_icon']	= $this->pdh->get('news_categories', 'icon', array($id));
			$old['category_color']	= $this->pdh->get('news_categories', 'color', array($id));

			$changes = false;
			foreach($old as $varname => $value){
				if(${$varname} == ''){
					${$varname} = $value;
				}else{
					if(${$varname} != $value){
						$changes = true;
					}
				}
			}
			if($changes){
				$this->db->query("UPDATE __news_categories SET :params WHERE category_id=?", array(
					'category_name'		=> $category_name,
					'category_icon'		=> $category_icon,
					'category_color'	=> $category_color
				), $id);
			}
			$this->pdh->enqueue_hook('newscat_update');
		}

		public function delete_icon($id) {
			$icon = $this->pdh->get('news_categories', 'icon', array($id));
			$this->db->query("UPDATE __news_categories SET :params WHERE category_id=?", array('category_icon'=>''), $id);
			$this->pfh->Delete($this->pfh->FilePath('newscat_icons/'.$icon));
			$this->pdh->enqueue_hook('newscat_update');
		}

		public function delete_category($id) {
			$this->db->query("DELETE FROM __news_categories WHERE category_id = '".$this->db->escape($id)."'");
			$this->db->query("UPDATE __news SET news_category=1 WHERE news_category =".$this->db->escape($id));
			$this->pdh->enqueue_hook('newscat_update');
			$this->pdh->enqueue_hook('news_update');
		}
		
	}
}
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_w_news_categories', pdh_w_news_categories::__shortcuts());
?>