<?php
 /*
 * Project:		EQdkp-Plus
 * License:		Creative Commons - Attribution-Noncommercial-Share Alike 3.0 Unported
 * Link:		http://creativecommons.org/licenses/by-nc-sa/3.0/
 * -----------------------------------------------------------------------
 * Began:		2007
 * Date:		$Date$
 * -----------------------------------------------------------------------
 * @author		$Author$
 * @copyright	2006-2011 EQdkp-Plus Developer Team
 * @link		http://eqdkp-plus.com
 * @package		eqdkp-plus
 * @version		$Rev$
 *
 * $Id$
 */

if(!defined('EQDKP_INC')) {
	die('Do not access this file directly.');
}

if(!class_exists( "pdh_r_pages")) {
	class pdh_r_pages extends pdh_r_generic {
		public static function __shortcuts() {
		$shortcuts = array('pdc', 'db', 'user'	);
		return array_merge(parent::$shortcuts, $shortcuts);
	}

		public $default_lang = 'english';
		public $pagelist;

		public $hooks = array(
			'pages',
		);

		public function reset() {
			$this->pdc->del('pdh_pages_table');
			$this->pagelist = NULL;
		}

		public function init() {
			// try to get from cache first
			$this->pagelist = $this->pdc->get('pdh_pages_table');
			if($this->pagelist !== NULL) {
				return true;
			}

			$this->pagelist = array();
			$sql = "SELECT *
					FROM __pages";
			$r_result = $this->db->query($sql);

			while($row = $this->db->fetch_record($r_result)) {
				$this->pagelist[$row['page_id']]['id'] = $row['page_id'];
				$this->pagelist[$row['page_id']]['title'] = $row['page_title'];
				$this->pagelist[$row['page_id']]['content'] = $row['page_content'];
				$this->pagelist[$row['page_id']]['visibility'] = unserialize($row['page_visibility']);
				$this->pagelist[$row['page_id']]['menu_link'] = $row['page_menu_link'];
				$this->pagelist[$row['page_id']]['edit_user'] = $row['page_edit_user'];
				$this->pagelist[$row['page_id']]['edit_date'] = $row['page_edit_date'];
				$this->pagelist[$row['page_id']]['alias'] = $row['page_alias'];
				$this->pagelist[$row['page_id']]['comments'] = $row['page_comments'];
				$this->pagelist[$row['page_id']]['voting'] = $row['page_voting'];
				$this->pagelist[$row['page_id']]['votes'] = $row['page_votes'];
				$this->pagelist[$row['page_id']]['voters'] = unserialize($row['page_voters']);
				$this->pagelist[$row['page_id']]['rating'] = $row['page_rating'];
			}

			$this->db->free_result($r_result);
			if($r_result) $this->pdc->put('pdh_pages_table', $this->pagelist, NULL);
			return true;
		}

		public function get_id_list() {
			return array_keys($this->pagelist);
		}

		public function get_tiny_dropdown(){
			foreach ($this->pagelist as $value){
				$output[$value['id']] = $value['title'].' ('.$value['id'].')';
			}
			return $output;
		}

		public function get_data ($id){
			return $this->pagelist[$id];
		}

		public function get_content ($id){
			return (isset($this->pagelist[$id]['content'])) ? $this->pagelist[$id]['content'] : '';
		}

		public function get_title ($id){
			return (isset($this->pagelist[$id]['title'])) ? $this->pagelist[$id]['title'] : '';
		}

		public function get_menu_link ($id){
			return (isset($this->pagelist[$id]['menu_link'])) ? $this->pagelist[$id]['menu_link'] : '';
		}

		public function get_visibility ($id){
			return (isset($this->pagelist[$id]['visibility'])) ? $this->pagelist[$id]['visibility'] : '';
		}

		public function get_check_visibility ($id){
			if ($this->handle_permission($this->pagelist[$id]['visibility'])){
				return true;
			}
			return false;
		}

		public function get_edit_user ($id){
			return $this->pagelist[$id]['edit_user'];
		}

		public function get_edit_date ($id){
			return $this->pagelist[$id]['edit_date'];
		}

		public function get_alias ($id){
			return (isset($this->pagelist[$id]['alias'])) ? $this->pagelist[$id]['alias'] : '';
		}

		public function get_comments ($id){
			return (isset($this->pagelist[$id]['comments'])) ? $this->pagelist[$id]['comments'] : '';
		}

		public function get_voting ($id){
			return (isset($this->pagelist[$id]['voting'])) ? $this->pagelist[$id]['voting'] : '';
		}

		public function get_rating ($id){
			return $this->pagelist[$id]['rating'];
		}

		public function get_voters ($id){
			return $this->pagelist[$id]['voters'];
		}

		public function get_alias_to_page($alias){
			foreach ($this->pagelist as $key=>$value){
				if (strtolower($value['alias']) == strtolower($alias)){
					return $key;
				}
			}
			return false;
		}

		public function get_page_exists($id){
			if (isset($this->pagelist[$id])){
				return true;
			} else {
				return false;
			}
		}

		public function get_url($id){
			$pid = ($this->pagelist[$id]['alias']) ? $this->pagelist[$id]['alias'] : $id;
			$url = $this->root_path.'pages.php'.$this->SID.'&amp;page='.$pid;
			return $url;
		}

		public function get_startpage_list(){
			$startpages = array();
			foreach ($this->pagelist as $key=>$value){
				$id = ($value['alias'] != "") ? $value['alias'] : $key;
				$startpages[] = array('link' => 'pages.php'.$this->SID.'&amp;page='.$id, 'text' => $value['title']);
			}
			return $startpages;
		}

		public function get_usermenu_pages(){
			$pages = array();
			foreach ($this->pagelist as $key=>$value){
				if ($value['menu_link'] == 2 && $this->handle_permission($value['visibility'])){
					$id = ($value['alias'] != "") ? $value['alias'] : $key;
					$pages[] = array('link' => 'pages.php'.$this->SID.'&amp;page='.$id, 'text' => $value['title'], 'check' => '');
				}
			}
			return $pages;
		}

		public function get_mainmenu_pages(){
			$pages = array();
			foreach ($this->pagelist as $key=>$value){
				if ($value['menu_link'] == 1 && $this->handle_permission($value['visibility'])){
					$id = ($value['alias'] != "") ? $value['alias'] : $key;
					$pages[] = array('link' => 'pages.php'.$this->SID.'&amp;page='.$id, 'text' => $value['title'], 'check' => '');
				}
			}
			return $pages;
		}

		public function get_tab_pages(){
			$pages = array();
			foreach ($this->pagelist as $key=>$value){
				if ($value['menu_link'] == 3 && $this->handle_permission($value['visibility'])){
					$id = ($value['alias'] != "") ? $value['alias'] : $key;
					$pages[] = array('link' => 'pages.php'.$this->SID.'&amp;page='.$id, 'text' => $value['title'], 'check' => '');
				}
			}
			return $pages;
		}

		public function get_portalmodule_pages(){
			$pages = array();
			foreach ($this->pagelist as $key=>$value){
				if ($value['menu_link'] == 99 && $this->handle_permission($value['visibility'])){
					$id = ($value['alias'] != "") ? $value['alias'] : $key;
					$pages[$id] = $value['title'];
				}
			}
			return $pages;
		}

		public function get_guildrule_page(){
			$pages = array();
			foreach ($this->pagelist as $key=>$value){
				if ($value['menu_link'] == 4){
					$pages[] = $key;
				}
			}
			return $pages;
		}

		private function handle_permission($groups){
			if (is_array($groups)){
				foreach ($groups as $key => $value){
					if ((int)$value == 0) return true;
					if ($this->user->check_group($value, false)){
						return true;
					}
				}
			}
			return false;
		}

	}//end class
}//end if
if(version_compare(PHP_VERSION, '5.3.0', '<')) registry::add_const('short_pdh_r_pages', pdh_r_pages::__shortcuts());
?>